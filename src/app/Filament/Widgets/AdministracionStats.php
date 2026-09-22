<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Cliente;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdministracionStats extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Usuarios Activos', User::where('activo', true)->count())
                ->description('Usuarios del sistema')
                ->descriptionIcon('heroicon-o-users')
                ->color('primary'),

            Stat::make('Total Clientes', Cliente::count())
                ->description('Clientes registrados')
                ->descriptionIcon('heroicon-o-building-office-2')
                ->color('info'),

            Stat::make('Clientes Activos', Cliente::where('activo', true)->count())
                ->description('Clientes con proyectos activos')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),
        ];
    }
}