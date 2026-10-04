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
        'costo_estimado',
        'creado_por'
    ];

    protected $casts = [
        'fecha_reporte' => 'datetime',
        'fecha_asignacion' => 'datetime',
        'fecha_inicio_trabajo' => 'datetime',
        'fecha_limite' => 'datetime',
        'fecha_resolucion' => 'datetime',
        'fecha_cierre' => 'datetime',
        'requiere_repuestos' => 'boolean',
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

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    /**
     * Helpers de estado y SLA
     */
    public function getEstaVencidaAttribute(): bool
    {
        return $this->fecha_limite 
            && $this->fecha_limite->isPast() 
            && !in_array($this->estado, ['Resuelta', 'Cerrada', 'Cancelada']);
    }

    public function getHorasRestantesAttribute(): ?int
    {
        if (!$this->fecha_limite || in_array($this->estado, ['Resuelta', 'Cerrada', 'Cancelada'])) {
            return null;
        }

        return (int) now()->diffInHours($this->fecha_limite, false);
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
            $incidencia->creado_por = auth()->id() ?? 1;

            // Auto-cálculo de SLA si no se especificó fecha límite
            if (empty($incidencia->fecha_limite)) {
                $incidencia->fecha_limite = match ($incidencia->prioridad) {
                    'Critica' => now()->addHours(8),
                    'Alta'    => now()->addHours(24),
                    'Media'   => now()->addHours(48),
                    'Baja'    => now()->addDays(7),
                    default   => now()->addHours(48),
                };
            }
        });

        static::updating(function ($incidencia) {
            if ($incidencia->isDirty('tecnico_id') && $incidencia->tecnico_id && !$incidencia->fecha_asignacion) {
                $incidencia->fecha_asignacion = now();
            }

            if ($incidencia->isDirty('estado') && $incidencia->estado === 'En_Progreso' && !$incidencia->fecha_inicio_trabajo) {
                $incidencia->fecha_inicio_trabajo = now();
            }

            if ($incidencia->isDirty('estado') && $incidencia->estado === 'Resuelta' && !$incidencia->fecha_resolucion) {
                $incidencia->fecha_resolucion = now();
            }

            if ($incidencia->isDirty('estado') && $incidencia->estado === 'Cerrada' && !$incidencia->fecha_cierre) {
                $incidencia->fecha_cierre = now();
            }
        });
    }
}
