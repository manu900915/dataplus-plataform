<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Servicio extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_ubicacion_id', 'tipo', 'fecha_instalacion', 'brigada_id', 'tecnico_id',
        'estado', 'notas', 'gr_tipo_solucion', 'gr_sim_numero', 'gr_sim_tipo',
        'gr_marca_modelo', 'gr_tipo_internet', 'gr_recarga_por',
        'gr_recarga_monto', 'gr_ultima_recarga'
    ];

    protected $casts = [
        'fecha_instalacion' => 'date',
        'gr_recarga_monto' => 'decimal:2',
        'gr_ultima_recarga' => 'date',
    ];

    public function ubicacion(): BelongsTo
    {
        return $this->belongsTo(ClienteUbicacion::class, 'cliente_ubicacion_id');
    }

    public function cliente(): BelongsTo
    {
        return $this->hasOneThrough(
            Cliente::class,
            ClienteUbicacion::class,
            'id',
            'id',
            'cliente_ubicacion_id',
            'cliente_id',
        );
    }

    public function brigada(): BelongsTo
    {
        return $this->belongsTo(Brigada::class);
    }

    public function tecnico(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tecnico_id');
    }
}