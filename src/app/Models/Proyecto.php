<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Proyecto extends Model
{
    protected $table = 'proyectos';

    protected $fillable = [
        'tipo_proyecto_id',
        'cliente_id',
        'cliente_ubicacion_id',
        'codigo',
        'nombre',
        'descripcion',
        'responsable_id',
        'estado',
        'fecha_inicio',
        'fecha_fin',
        'presupuesto_total',
        'notas',
        'tipo_seguimiento',
        'estado_kanban',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'presupuesto_total' => 'decimal:2',
    ];

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

    // Relaciones específicas por categoría para evitar duplicación entre pestañas en Filament
    public function lineasEquipamiento(): HasMany
    {
        return $this->hasMany(LineaPresupuesto::class, 'proyecto_id')->where('tipo_linea', 'equipamiento');
    }

    public function lineasManoObra(): HasMany
    {
        return $this->hasMany(LineaPresupuesto::class, 'proyecto_id')->where('tipo_linea', 'mano_obra');
    }

    public function lineasMateriales(): HasMany
    {
        return $this->hasMany(LineaPresupuesto::class, 'proyecto_id')->where('tipo_linea', 'material');
    }

    public function lineasTransporte(): HasMany
    {
        return $this->hasMany(LineaPresupuesto::class, 'proyecto_id')->where('tipo_linea', 'transporte');
    }

    public function lineasAlimentacion(): HasMany
    {
        return $this->hasMany(LineaPresupuesto::class, 'proyecto_id')->where('tipo_linea', 'alimentacion');
    }

    public function solicitud(): HasOne
    {
        return $this->hasOne(SolicitudServicio::class, 'proyecto_id');
    }

    public function calcularPresupuesto(): float
    {
        $total = (float) $this->lineasPresupuesto()->sum('subtotal');
        $this->update(['presupuesto_total' => $total]);
        return $total;
    }

    public function getSubtotalByTipo(string $tipo): float
    {
        return (float) $this->lineasPresupuesto()->where('tipo_linea', $tipo)->sum('subtotal');
    }
}
