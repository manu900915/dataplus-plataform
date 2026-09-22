<?php

namespace App\Filament\Widgets;

use App\Models\Item;
use App\Models\Almacen;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InventarioStats extends BaseWidget
{
    protected static ?int $sort = 4;

    protected function getStats(): array
    {
        $stockBajo = Item::whereColumn('stock_actual', '<=', 'stock_minimo')->count();
        $totalEquipos = Item::where('es_equipamiento', true)->sum('stock_actual');
        $totalMateriales = Item::where('es_equipamiento', false)->sum('stock_actual');

        return [
            Stat::make('Total Items', Item::count())
                ->description('Items en inventario')
                ->descriptionIcon('heroicon-o-archive-box')
                ->color('primary'),

            Stat::make('Stock Bajo', $stockBajo)
                ->description('Items que necesitan reposición')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color('danger'),

            Stat::make('Equipamiento', $totalEquipos)
                ->description('Unidades de equipos')
                ->descriptionIcon('heroicon-o-cube')
                ->color('info'),

            Stat::make('Materiales', $totalMateriales)
                ->description('Unidades de materiales')
                ->descriptionIcon('heroicon-o-archive-box')
                ->color('success'),

            Stat::make('Almacenes', Almacen::count())
                ->description('Almacenes registrados')
                ->descriptionIcon('heroicon-o-building-storefront')
                ->color('warning'),
        ];
    }
}