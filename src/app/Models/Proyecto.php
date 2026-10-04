<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proyecto extends Model
{
    protected $table = 'proyectos';

    protected $fillable = [
        'tipo_proyecto_id', 'cliente_id', 'cliente_ubicacion_id',
        'codigo', 'nombre', 'descripcion', 'responsable_id',
        'estado', 'fecha_inicio', 'fecha_fin', 'presupuesto_total', 'notas',
        'tipo_seguimiento', 'estado_kanban', // <-- Asegúrate de que estén aquí
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'presupuesto_total' => 'decimal:2',
    ];

    // ✅ Esta relación DEBE existir
    public function tipoProyecto(): BelongsTo
    {
        return $this->belongsTo(TipoProyecto::class, 'tipo_proyecto_id');
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function ubicacion(): BelongsTo
    {
        return $this->belongsTo(ClienteUbicacion::class, 'cliente_ubicacion_id');
    }

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function lineasPresupuesto(): HasMany
    {
        return $this->hasMany(LineaPresupuesto::class, 'proyecto_id');
    }

    public function solicitud(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(SolicitudServicio::class, 'proyecto_id');
    }

    public function calcularPresupuesto(): float
    {
        $total = $this->lineasPresupuesto()->sum('subtotal');
        $this->update(['presupuesto_total' => $total]);
        return $total;
    }

    public function getSubtotalByTipo(string $tipo): float
    {
        return $this->lineasPresupuesto()->where('tipo_linea', $tipo)->sum('subtotal');
    }
}