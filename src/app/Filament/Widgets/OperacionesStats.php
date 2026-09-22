<?php

namespace App\Filament\Widgets;

use App\Models\Proyecto;
use App\Models\Brigada;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OperacionesStats extends BaseWidget
{
    protected static ?int $sort = 3;

    protected function getStats(): array
    {
        return [
            Stat::make('Brigadas Activas', Brigada::where('activa', true)->count())
                ->description('Brigadas operativas')
                ->descriptionIcon('heroicon-o-user-group')
                ->color('primary'),

            Stat::make('Proyectos I+D', Proyecto::where('tipo_seguimiento', 'investigacion')
                ->whereIn('estado', ['borrador', 'en_progreso'])
                ->count())
                ->description('Proyectos en seguimiento Kanban')
                ->descriptionIcon('heroicon-o-queue-list')
                ->color('info'),

            Stat::make('Instalaciones', Proyecto::where('tipo_seguimiento', 'instalacion')
                ->whereIn('estado', ['borrador', 'en_progreso'])
                ->count())
                ->description('Proyectos de instalación')
                ->descriptionIcon('heroicon-o-wrench-screwdriver')
                ->color('success'),
        ];
    }
}