<?php

namespace App\Filament\Widgets;

use App\Models\Proyecto;
use App\Models\LineaPresupuesto;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FinanzasStats extends BaseWidget
{
    protected static ?int $sort = 5;

    protected function getStats(): array
    {
        $presupuestoTotal = Proyecto::sum('presupuesto_total');
        $presupuestoMes = Proyecto::whereMonth('created_at', now()->month)->sum('presupuesto_total');
        $equipamientoTotal = LineaPresupuesto::where('tipo_linea', 'equipamiento')->sum('subtotal');
        $manoObraTotal = LineaPresupuesto::where('tipo_linea', 'mano_obra')->sum('subtotal');
        $materialesTotal = LineaPresupuesto::where('tipo_linea', 'material')->sum('subtotal');

        return [
            Stat::make('Presupuesto Total', '$' . number_format($presupuestoTotal, 2))
                ->description('Suma de todos los proyectos')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('primary'),

            Stat::make('Presupuesto este Mes', '$' . number_format($presupuestoMes, 2))
                ->description('Proyectos creados este mes')
                ->descriptionIcon('heroicon-o-calendar')
                ->color('success'),

            Stat::make('Equipamiento', '$' . number_format($equipamientoTotal, 2))
                ->description('Total en equipos')
                ->descriptionIcon('heroicon-o-cube')
                ->color('info'),

            Stat::make('Mano de Obra', '$' . number_format($manoObraTotal, 2))
                ->description('Total en servicios')
                ->descriptionIcon('heroicon-o-user-group')
                ->color('warning'),

            Stat::make('Materiales', '$' . number_format($materialesTotal, 2))
                ->description('Total en materiales')
                ->descriptionIcon('heroicon-o-archive-box')
                ->color('danger'),
        ];
    }
}