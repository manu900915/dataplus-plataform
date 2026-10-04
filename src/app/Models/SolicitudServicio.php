<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SolicitudServicio extends Model
{
    use HasFactory;

    protected $table = 'solicitudes_servicio';

    protected $fillable = [
        'codigo',
        'cliente_id',
        'cliente_ubicacion_id',
        'comercial_id',
        'titulo',
        'descripcion',
        'tipo_proyecto_id',
        'prioridad',
        'presupuesto_estimado',
        'estado',
        'supervisor_id',
        'fecha_aprobacion',
        'notas_supervisor',
        'proyecto_id',
    ];

    protected $casts = [
        'presupuesto_estimado' => 'decimal:2',
        'fecha_aprobacion' => 'datetime',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function ubicacion(): BelongsTo
    {
        return $this->belongsTo(ClienteUbicacion::class, 'cliente_ubicacion_id');
    }

    public function comercial(): BelongsTo
    {
        return $this->belongsTo(User::class, 'comercial_id');
    }

    public function tipoProyecto(): BelongsTo
    {
        return $this->belongsTo(TipoProyecto::class, 'tipo_proyecto_id');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function proyecto(): BelongsTo
    {
        return $this->belongsTo(Proyecto::class, 'proyecto_id');
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($solicitud) {
            if (empty($solicitud->codigo)) {
                $year = now()->year;
                $count = static::whereYear('created_at', $year)->count() + 1;
                $solicitud->codigo = "SOL-{$year}-" . str_pad($count, 4, '0', STR_PAD_LEFT);
            }

            if (empty($solicitud->comercial_id)) {
                $solicitud->comercial_id = auth()->id() ?? 1;
            }
        });
    }
}
