<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Item extends Model
{
    protected $fillable = [
        'categoria_id', 'codigo', 'nombre', 'descripcion',
        'unidad_medida', 'precio_unitario', 'stock_actual',
        'stock_minimo', 'es_equipamiento','precio_costo',
        'precio_venta','margen_ganancia',
    ];

    protected static function booted(): void
    {
        static::saving(function (Item $item) {
            // Calcular automáticamente precio de venta si no se establece
            if (!$item->precio_venta && $item->precio_costo > 0) {
                $margen = $item->margen_ganancia ?? 60;
                $item->precio_venta = $item->precio_costo * (1 + ($margen / 100));
            }
        });
    }

    // Método helper
    public function calcularPrecioVenta(float $margen = 60): float
    {
        return $this->precio_costo * (1 + ($margen / 100));
    }

    protected $casts = [
        'es_equipamiento' => 'boolean',
        'precio_unitario' => 'decimal:2',
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriaItem::class, 'categoria_id');
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(InventarioMovimiento::class);
    }

    /**
     * Stock calculado a partir de movimientos (fuente de verdad).
     */
    public function getStockRealAttribute(): float
    {
        $entradas = $this->movimientos()->whereIn('tipo', ['entrada', 'devolucion'])->sum('cantidad');
        $salidas  = $this->movimientos()->where('tipo', 'salida')->sum('cantidad');

        return (float) $entradas - (float) $salidas;
    }

    /**
     * Aplica un movimiento y sincroniza el stock caché.
     */
    public function mover(string $tipo, float $cantidad, ?float $costo = null, array $extra = []): InventarioMovimiento
    {
        $signo = in_array($tipo, ['entrada', 'devolucion']) ? 1 : -1;

        return DB::transaction(function () use ($tipo, $cantidad, $costo, $signo, $extra) {
            $movimiento = $this->movimientos()->create([
                'tipo' => $tipo,
                'cantidad' => $cantidad,
                'costo_unitario' => $costo,
                ...$extra,
            ]);

            $this->increment('stock_actual', $signo * $cantidad);

            return $movimiento;
        });
    }
    public function getStockEstadoAttribute(): string
{
    return match (true) {
        $this->stock_actual < 0 => 'negativo',
        $this->stock_actual <= $this->stock_minimo => 'bajo',
        default => 'ok',
    };
}

public function getValorInventarioAttribute(): float
{
    return (float) $this->stock_actual * (float) $this->precio_unitario;
}
}