<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\DB;

class Venta extends Model
{
    protected $table = 'ventas';

    public const METODOS_PAGO = [
        'CUP' => 'CUP (Efectivo)',
        'USD' => 'USD (Efectivo)',
        'Transferencia CUP' => 'Transferencia CUP',
        'Tarjeta Clasica' => 'Tarjeta Clásica',
    ];

    public const ESTADOS = [
        'completada' => 'Completada',
        'pendiente' => 'Pendiente',
        'anulada' => 'Anulada',
    ];

    protected $fillable = [
        'codigo',
        'cliente_id',
        'cliente_nombre',
        'almacen_id',
        'user_id',
        'fecha_venta',
        'metodo_pago',
        'moneda',
        'tasa_cambio',
        'margen_ganancia_global',
        'referencia_pago',
        'costo_total',
        'subtotal',
        'descuento',
        'monto_total',
        'ganancia_total',
        'estado',
        'inventario_descontado',
        'notas',
    ];

    protected $casts = [
        'fecha_venta' => 'datetime',
        'tasa_cambio' => 'decimal:4',
        'margen_ganancia_global' => 'decimal:2',
        'costo_total' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'descuento' => 'decimal:2',
        'monto_total' => 'decimal:2',
        'ganancia_total' => 'decimal:2',
        'inventario_descontado' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Venta $venta) {
            if (blank($venta->codigo)) {
                $venta->codigo = self::generarCodigo();
            }

            if (blank($venta->fecha_venta)) {
                $venta->fecha_venta = now();
            }

            if (blank($venta->user_id)) {
                $venta->user_id = auth()->id();
            }

            $venta->moneda = self::monedaDesdeMetodo((string) $venta->metodo_pago);
        });

        static::saving(function (Venta $venta) {
            $venta->moneda = self::monedaDesdeMetodo((string) $venta->metodo_pago);

            if ($venta->cliente_id && blank($venta->cliente_nombre)) {
                $venta->cliente_nombre = Cliente::find($venta->cliente_id)?->nombre;
            } elseif (blank($venta->cliente_nombre)) {
                $venta->cliente_nombre = 'Consumidor Final';
            }
        });

        static::deleting(function (Venta $venta) {
            if ($venta->inventario_descontado) {
                $venta->revertirInventario("Eliminación de Venta {$venta->codigo}");
            }
        });
    }

    public static function monedaDesdeMetodo(string $metodoPago): string
    {
        return match ($metodoPago) {
            'USD', 'Tarjeta Clasica' => 'USD',
            default => 'CUP',
        };
    }

    public static function generarCodigo(): string
    {
        $year = now()->year;
        $prefix = "VTA-{$year}-";
        $codigos = self::where('codigo', 'like', "{$prefix}%")->pluck('codigo');
        $max = 0;

        foreach ($codigos as $cod) {
            if (preg_match('/^VTA-\d{4}-(\d+)$/', (string) $cod, $m)) {
                $max = max($max, (int) $m[1]);
            }
        }

        return $prefix . str_pad((string) ($max + 1), 4, '0', STR_PAD_LEFT);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function almacen(): BelongsTo
    {
        return $this->belongsTo(Almacen::class, 'almacen_id');
    }

    public function vendedor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(VentaItem::class, 'venta_id');
    }

    public function movimientosInventario(): MorphMany
    {
        return $this->morphMany(InventarioMovimiento::class, 'documento');
    }

    public function recalcularTotales(): void
    {
        $lineas = $this->items()->get();

        $costoTotal = 0.0;
        $subtotal = 0.0;

        foreach ($lineas as $linea) {
            $costoTotal += (float) $linea->precio_costo_unitario * (float) $linea->cantidad;
            $subtotal += (float) $linea->subtotal;
        }

        $descuento = max(0, (float) $this->descuento);
        $montoTotal = max(0, round($subtotal - $descuento, 2));
        $gananciaTotal = round($montoTotal - $costoTotal, 2);

        $this->updateQuietly([
            'costo_total' => round($costoTotal, 2),
            'subtotal' => round($subtotal, 2),
            'monto_total' => $montoTotal,
            'ganancia_total' => $gananciaTotal,
        ]);
    }

    /**
     * Descuenta del almacén e inventario los productos vendidos.
     *
     * @throws \RuntimeException si no hay stock suficiente.
     */
    public function aplicarDescuentoInventario(): void
    {
        DB::transaction(function () {
            /** @var self $venta */
            $venta = self::whereKey($this->id)->lockForUpdate()->firstOrFail();

            if ($venta->inventario_descontado || $venta->estado !== 'completada') {
                return;
            }

            $lineas = $venta->items()->get();
            if ($lineas->isEmpty()) {
                throw new \RuntimeException('La venta debe contener al menos un producto para descontar del almacén.');
            }

            foreach ($lineas as $linea) {
                $almacenId = $linea->almacen_id ?: $venta->almacen_id;
                /** @var Item $item */
                $item = Item::whereKey($linea->item_id)->lockForUpdate()->firstOrFail();
                $cantidad = (float) $linea->cantidad;

                if ($cantidad <= 0) {
                    continue;
                }

                if ((float) $item->stock_actual < $cantidad) {
                    throw new \RuntimeException(
                        "Stock insuficiente para \"{$item->nombre}\": disponible {$item->stock_actual} {$item->unidad_medida}, solicitado {$cantidad}."
                    );
                }

                if ($almacenId && $item->movimientos()->whereNotNull('almacen_id')->exists()) {
                    $stockAlmacen = $item->stockEnAlmacen((int) $almacenId);
                    if ($stockAlmacen < $cantidad) {
                        $almacenNombre = Almacen::find($almacenId)?->nombre ?? "Almacén #{$almacenId}";
                        throw new \RuntimeException(
                            "Stock insuficiente de \"{$item->nombre}\" en {$almacenNombre}: disponible {$stockAlmacen} {$item->unidad_medida}, solicitado {$cantidad}."
                        );
                    }
                }

                $item->mover(
                    tipo: 'salida',
                    cantidad: $cantidad,
                    costo: (float) $linea->precio_costo_unitario,
                    extra: [
                        'almacen_id' => $almacenId,
                        'motivo' => "Venta {$venta->codigo}" . ($venta->cliente_nombre ? " — {$venta->cliente_nombre}" : ''),
                        'documento_type' => self::class,
                        'documento_id' => $venta->id,
                        'user_id' => $venta->user_id ?? auth()->id(),
                    ]
                );
            }

            $venta->updateQuietly(['inventario_descontado' => true]);
            $this->inventario_descontado = true;
        });
    }

    /**
     * Revierte al almacén los productos descontados por una venta (p. ej. al anular o editar).
     */
    public function revertirInventario(?string $motivo = null): void
    {
        DB::transaction(function () use ($motivo) {
            /** @var self $venta */
            $venta = self::whereKey($this->id)->lockForUpdate()->first();
            if (!$venta || !$venta->inventario_descontado) {
                return;
            }

            $lineas = $venta->items()->get();

            foreach ($lineas as $linea) {
                $almacenId = $linea->almacen_id ?: $venta->almacen_id;
                /** @var Item|null $item */
                $item = Item::whereKey($linea->item_id)->lockForUpdate()->first();
                if (!$item) {
                    continue;
                }

                $cantidad = (float) $linea->cantidad;
                if ($cantidad <= 0) {
                    continue;
                }

                $item->mover(
                    tipo: 'devolucion',
                    cantidad: $cantidad,
                    costo: (float) $linea->precio_costo_unitario,
                    extra: [
                        'almacen_id' => $almacenId,
                        'motivo' => $motivo ?? "Anulación de Venta {$venta->codigo}",
                        'documento_type' => self::class,
                        'documento_id' => $venta->id,
                        'user_id' => auth()->id() ?? $venta->user_id,
                    ]
                );
            }

            $venta->updateQuietly(['inventario_descontado' => false]);
            $this->inventario_descontado = false;
        });
    }
}
