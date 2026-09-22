<?php

namespace App\Filament\Widgets;

use App\Models\Proyecto;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProyectosStats extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $proyectosActivos = Proyecto::whereIn('estado', ['borrador', 'en_progreso'])->count();
        $proyectosCompletados = Proyecto::where('estado', 'completado')->whereMonth('created_at', now()->month)->count();
        $proyectosRetrasados = Proyecto::where('estado', 'en_progreso')
            ->where('fecha_fin', '<', now())
            ->count();

        return [
            Stat::make('Proyectos Activos', $proyectosActivos)
                ->description('En borrador o progreso')
                ->descriptionIcon('heroicon-o-briefcase')
                ->color('primary'),

            Stat::make('Completados este Mes', $proyectosCompletados)
                ->description('Proyectos finalizados')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Proyectos con Retraso', $proyectosRetrasados)
                ->description('Fecha límite vencida')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color('danger'),

            Stat::make('Presupuesto Total', '$' . number_format(Proyecto::sum('presupuesto_total'), 2))
                ->description('Suma de todos los presupuestos')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('warning'),
        ];
    }
}