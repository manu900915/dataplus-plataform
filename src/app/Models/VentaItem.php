<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VentaItem extends Model
{
    protected $table = 'venta_items';

    protected $fillable = [
        'venta_id',
        'item_id',
        'almacen_id',
        'cantidad',
        'precio_costo_unitario',
        'margen_ganancia',
        'precio_unitario',
        'subtotal',
        'ganancia_linea',
    ];

    protected $casts = [
        'cantidad' => 'decimal:3',
        'precio_costo_unitario' => 'decimal:2',
        'margen_ganancia' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'ganancia_linea' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saving(function (VentaItem $linea) {
            $cantidad = max(0, (float) $linea->cantidad);
            $costo = max(0, (float) $linea->precio_costo_unitario);
            $margen = (float) ($linea->margen_ganancia ?? 60);

            if ((float) $linea->precio_unitario <= 0 && $costo > 0) {
                $linea->precio_unitario = round($costo * (1 + ($margen / 100)), 2);
            }

            $precio = max(0, (float) $linea->precio_unitario);

            if ($costo > 0 && $linea->isDirty('precio_unitario') && !$linea->isDirty('margen_ganancia')) {
                $linea->margen_ganancia = round((($precio / $costo) - 1) * 100, 2);
            }

            $linea->subtotal = round($cantidad * $precio, 2);
            $linea->ganancia_linea = round(($precio - $costo) * $cantidad, 2);
        });
    }

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function almacen(): BelongsTo
    {
        return $this->belongsTo(Almacen::class, 'almacen_id');
    }
}
