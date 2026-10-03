<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LineaPresupuesto extends Model
{
    use HasFactory;

    protected $table = 'lineas_presupuesto';

    protected $fillable = [
        'proyecto_id', 'item_id', 'tipo_linea', 'descripcion',
        'cantidad', 'costo_unitario', 'subtotal', 'descontar_inventario',
    ];

    protected $casts = [
        'cantidad' => 'decimal:3',
        'costo_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'descontar_inventario' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (LineaPresupuesto $linea) {
            $linea->subtotal = $linea->cantidad * $linea->costo_unitario;
        });

        static::saved(function (LineaPresupuesto $linea) {
            $linea->proyecto->calcularPresupuesto();
        });

        static::deleted(function (LineaPresupuesto $linea) {
            $linea->proyecto->calcularPresupuesto();
        });
    }

    public function proyecto(): BelongsTo
    {
        return $this->belongsTo(Proyecto::class, 'proyecto_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function getTipoLineaLabelAttribute(): string
    {
        return match ($this->tipo_linea) {
            'equipamiento' => 'Equipo',
            'material' => 'Material',
            'mano_obra' => 'Mano de Obra',
            'transporte' => 'Transporte',
            'alimentacion' => 'Alimentación',
            'otro' => 'Otro',
            default => $this->tipo_linea,
        };
    }
}