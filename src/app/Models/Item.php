<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Item extends Model
{
    protected $fillable = [
        'categoria_id',
        'codigo',
        'numero_serie',
        'nombre',
        'descripcion',
        'imagen',
        'unidad_medida',
        'precio_unitario',
        'stock_actual',
        'stock_minimo',
        'es_equipamiento',
        'precio_costo',
        'precio_venta',
        'margen_ganancia',
    ];

    protected static function booted(): void
    {
        static::saving(function (Item $item) {
            if ($item->precio_costo > 0 && (!$item->precio_venta || $item->isDirty(['precio_costo', 'margen_ganancia']))) {
                $margen = (float) ($item->margen_ganancia ?? 60);
                $item->precio_venta = round((float) $item->precio_costo * (1 + ($margen / 100)), 2);
            }
        });
    }

    public function calcularPrecioVenta(float $margen = 60): float
    {
        return round((float) $this->precio_costo * (1 + ($margen / 100)), 2);
    }

    protected $casts = [
        'es_equipamiento' => 'boolean',
        'precio_unitario' => 'decimal:2',
        'precio_costo' => 'decimal:2',
        'precio_venta' => 'decimal:2',
        'margen_ganancia' => 'float',
        'stock_actual' => 'decimal:3',
        'stock_minimo' => 'decimal:3',
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriaItem::class, 'categoria_id');
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(InventarioMovimiento::class);
    }

    public function ventaItems(): HasMany
    {
        return $this->hasMany(VentaItem::class);
    }

    public function getImagenUrlAttribute(): ?string
    {
        if (blank($this->imagen)) {
            return null;
        }

        if (str_starts_with($this->imagen, 'http://') || str_starts_with($this->imagen, 'https://') || str_starts_with($this->imagen, 'data:')) {
            return $this->imagen;
        }

        return Storage::disk('public')->url($this->imagen);
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
     * Stock disponible en un almacén específico (o stock global si no existen movimientos por almacén).
     */
    public function stockEnAlmacen(?int $almacenId): float
    {
        if (!$almacenId) {
            return (float) $this->stock_actual;
        }

        $tieneMovimientos = $this->movimientos()->whereNotNull('almacen_id')->exists();
        if (!$tieneMovimientos) {
            return (float) $this->stock_actual;
        }

        $entradas = (float) $this->movimientos()
            ->where('almacen_id', $almacenId)
            ->whereIn('tipo', ['entrada', 'devolucion'])
            ->sum('cantidad');

        $salidas = (float) $this->movimientos()
            ->where('almacen_id', $almacenId)
            ->where('tipo', 'salida')
            ->sum('cantidad');

        return $entradas - $salidas;
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
        return (float) $this->stock_actual * (float) ($this->precio_venta ?: $this->precio_unitario);
    }
}