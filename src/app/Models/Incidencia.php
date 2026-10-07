<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Incidencia extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'tipo',
        'cliente_id',
        'cliente_ubicacion_id',
        'servicio_id',
        'direccion_incidencia',
        'contacto_local',
        'telefono_local',
        'titulo',
        'descripcion',
        'diagnostico',
        'tecnico_id',
        'brigada_id',
        'revisado_por',
        'fecha_revision_supervisor',
        'notas_supervisor',
        'estado',
        'prioridad',
        'fecha_reporte',
        'fecha_asignacion',
        'fecha_inicio_trabajo',
        'fecha_limite',
        'fecha_resolucion',
        'fecha_cierre',
        'solucion',
        'notas_internas',
        'requiere_repuestos',
        'conformidad_cliente',
        'observaciones_cierre_comercial',
        'costo_estimado',
        'creado_por',
        'cerrado_por',
    ];

    protected $casts = [
        'fecha_reporte' => 'datetime',
        'fecha_asignacion' => 'datetime',
        'fecha_inicio_trabajo' => 'datetime',
        'fecha_limite' => 'datetime',
        'fecha_resolucion' => 'datetime',
        'fecha_revision_supervisor' => 'datetime',
        'fecha_cierre' => 'datetime',
        'requiere_repuestos' => 'boolean',
        'conformidad_cliente' => 'boolean',
        'costo_estimado' => 'decimal:2',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function ubicacion(): BelongsTo
    {
        return $this->belongsTo(ClienteUbicacion::class, 'cliente_ubicacion_id');
    }

    public function servicio(): BelongsTo
    {
        return $this->belongsTo(Servicio::class, 'servicio_id');
    }

    public function tecnico(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tecnico_id');
    }

    public function brigada(): BelongsTo
    {
        return $this->belongsTo(Brigada::class, 'brigada_id');
    }

    public function supervisorRevisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revisado_por');
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function cerrador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cerrado_por');
    }

    /**
     * Helpers de estado y SLA
     */
    public function getEstaVencidaAttribute(): bool
    {
        // Solo puede estar vencida si NO está resuelta ni cerrada
        if (in_array($this->estado, ['Resuelta', 'Revisada_Supervisor', 'Cerrada', 'Cancelada'])) {
            return false;
        }

        return $this->fecha_limite && now()->gt($this->fecha_limite);
    }

    public function getHorasRestantesAttribute(): ?int
    {
        if (!$this->fecha_limite || in_array($this->estado, ['Resuelta', 'Revisada_Supervisor', 'Cerrada', 'Cancelada'])) {
            return null;
        }

        if (now()->gt($this->fecha_limite)) {
            return 0;
        }

        return (int) now()->diffInHours($this->fecha_limite, false);
    }

    public function getHorasVencidaAttribute(): ?int
    {
        if (!$this->esta_vencida || !$this->fecha_limite) {
            return null;
        }

        return max(1, (int) $this->fecha_limite->diffInHours(now()));
    }

    /**
     * Tiempo total de resolución técnica (desde reporte/creación hasta que se marcó resuelta)
     */
    public function getTiempoResolucionTextoAttribute(): ?string
    {
        if (!$this->fecha_resolucion) {
            return null;
        }

        $inicio = $this->fecha_reporte ?? $this->created_at;
        if (!$inicio) {
            return null;
        }

        $minutos = max(1, $inicio->diffInMinutes($this->fecha_resolucion));
        $dias = intdiv($minutos, 1440);
        $horas = intdiv($minutos % 1440, 60);
        $restoMinutos = $minutos % 60;

        $partes = [];
        if ($dias > 0) $partes[] = "{$dias}d";
        if ($horas > 0) $partes[] = "{$horas}h";
        if ($restoMinutos > 0 || empty($partes)) $partes[] = "{$restoMinutos}m";

        return implode(' ', $partes);
    }

    /**
     * Tiempo de intervención en sitio
     */
    public function getTiempoIntervencionTextoAttribute(): ?string
    {
        if (!$this->fecha_inicio_trabajo || !$this->fecha_resolucion) {
            return null;
        }

        $minutos = max(1, $this->fecha_inicio_trabajo->diffInMinutes($this->fecha_resolucion));
        $dias = intdiv($minutos, 1440);
        $horas = intdiv($minutos % 1440, 60);
        $restoMinutos = $minutos % 60;

        $partes = [];
        if ($dias > 0) $partes[] = "{$dias}d";
        if ($horas > 0) $partes[] = "{$horas}h";
        if ($restoMinutos > 0 || empty($partes)) $partes[] = "{$restoMinutos}m";

        return implode(' ', $partes);
    }

    /**
     * Tiempo transcurrido para incidencias activas
     */
    public function getTiempoTranscurridoTextoAttribute(): string
    {
        $inicio = $this->fecha_reporte ?? $this->created_at;
        if (!$inicio) {
            return '-';
        }

        $minutos = max(1, $inicio->diffInMinutes(now()));
        $dias = intdiv($minutos, 1440);
        $horas = intdiv($minutos % 1440, 60);
        $restoMinutos = $minutos % 60;

        $partes = [];
        if ($dias > 0) $partes[] = "{$dias}d";
        if ($horas > 0) $partes[] = "{$horas}h";
        if ($restoMinutos > 0 || empty($partes)) $partes[] = "{$restoMinutos}m";

        return implode(' ', $partes);
    }

    /**
     * Verifica si se cumplió con el SLA
     * Retorna:
     *  true  -> Resuelto dentro del plazo (fecha_resolucion <= fecha_limite)
     *  false -> Excedió el plazo (fecha_resolucion > fecha_limite)
     *  null  -> No tiene fecha_resolucion o fecha_limite
     */
    public function getCumplioSlaAttribute(): ?bool
    {
        if (!$this->fecha_resolucion || !$this->fecha_limite) {
            return null;
        }

        return $this->fecha_resolucion->lte($this->fecha_limite);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($incidencia) {
            if (empty($incidencia->codigo)) {
                $year = now()->year;
                $count = static::whereYear('created_at', $year)->count() + 1;
                $incidencia->codigo = "INC-{$year}-" . str_pad($count, 4, '0', STR_PAD_LEFT);
            }

            if (empty($incidencia->creado_por)) {
                $incidencia->creado_por = auth()->id() ?? 1;
            }

            if (empty($incidencia->fecha_reporte)) {
                $incidencia->fecha_reporte = now();
            }

            // Si al crear ya se especificó un responsable, pasa directamente a Asignada
            if (!empty($incidencia->tecnico_id)) {
                if (empty($incidencia->estado) || $incidencia->estado === 'Pendiente') {
                    $incidencia->estado = 'Asignada';
                }
                if (empty($incidencia->fecha_asignacion)) {
                    $incidencia->fecha_asignacion = now();
                }
            }

            // SLA según prioridad
            if (empty($incidencia->fecha_limite)) {
                $horas = match ($incidencia->prioridad) {
                    'Critica' => 8,
                    'Alta'    => 24,
                    'Media'   => 48,
                    'Baja'    => 168,
                    default   => 48,
                };
                $incidencia->fecha_limite = now()->addHours($horas);
            }
        });

        static::updating(function ($incidencia) {
            // Si se asigna responsable y estaba en Pendiente, pasa a Asignada
            if ($incidencia->isDirty('tecnico_id') && $incidencia->tecnico_id) {
                if (!$incidencia->fecha_asignacion) {
                    $incidencia->fecha_asignacion = now();
                }
                if ($incidencia->estado === 'Pendiente') {
                    $incidencia->estado = 'Asignada';
                }
            }

            if ($incidencia->isDirty('estado') && $incidencia->estado === 'En_Progreso' && !$incidencia->fecha_inicio_trabajo) {
                $incidencia->fecha_inicio_trabajo = now();
            }

            if ($incidencia->isDirty('estado') && $incidencia->estado === 'Resuelta' && !$incidencia->fecha_resolucion) {
                $incidencia->fecha_resolucion = now();
            }

            if ($incidencia->isDirty('estado') && $incidencia->estado === 'Revisada_Supervisor' && !$incidencia->fecha_revision_supervisor) {
                $incidencia->fecha_revision_supervisor = now();
                $incidencia->revisado_por = auth()->id();
            }

            if ($incidencia->isDirty('estado') && $incidencia->estado === 'Cerrada' && !$incidencia->fecha_cierre) {
                $incidencia->fecha_cierre = now();
                if (!$incidencia->cerrado_por) {
                    $incidencia->cerrado_por = auth()->id();
                }
            }
        });
    }
}
