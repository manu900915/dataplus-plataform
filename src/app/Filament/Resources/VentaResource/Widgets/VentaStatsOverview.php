<?php

namespace App\Filament\Resources\VentaResource\Widgets;

use App\Models\Venta;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class VentaStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $completadas = Venta::where('estado', 'completada');

        $cupEfectivo = (float) (clone $completadas)->where('metodo_pago', 'CUP')->sum('monto_total');
        $cupTransferencia = (float) (clone $completadas)->where('metodo_pago', 'Transferencia CUP')->sum('monto_total');
        $usdEfectivo = (float) (clone $completadas)->where('metodo_pago', 'USD')->sum('monto_total');
        $tarjetaClasica = (float) (clone $completadas)->where('metodo_pago', 'Tarjeta Clasica')->sum('monto_total');

        $gananciaCup = (float) (clone $completadas)->where('moneda', 'CUP')->sum('ganancia_total');
        $gananciaUsd = (float) (clone $completadas)->where('moneda', 'USD')->sum('ganancia_total');

        return [
            Stat::make('Ventas en CUP', '$' . number_format($cupEfectivo + $cupTransferencia, 2) . ' CUP')
                ->description('Efectivo: $' . number_format($cupEfectivo, 2) . ' | Transf: $' . number_format($cupTransferencia, 2))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Ventas en USD / Clásica', '$' . number_format($usdEfectivo + $tarjetaClasica, 2) . ' USD')
                ->description('Efectivo USD: $' . number_format($usdEfectivo, 2) . ' | Clásica: $' . number_format($tarjetaClasica, 2))
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('warning'),

            Stat::make('Ganancia Neta Acumulada', '$' . number_format($gananciaCup, 2) . ' CUP / $' . number_format($gananciaUsd, 2) . ' USD')
                ->description((clone $completadas)->count() . ' ventas completadas con descuento en almacén')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('info'),
        ];
    }
}
