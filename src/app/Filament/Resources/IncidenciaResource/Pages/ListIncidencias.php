<?php

namespace App\Filament\Resources\IncidenciaResource\Pages;

use App\Filament\Resources\IncidenciaResource;
use App\Models\Incidencia;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListIncidencias extends ListRecords
{
    protected static string $resource = IncidenciaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Nueva Incidencia'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'todas' => Tab::make('Todas')
                ->badge(Incidencia::count()),

            'pendientes' => Tab::make('1. Pendientes')
                ->badge(Incidencia::where('estado', 'Pendiente')->count())
                ->badgeColor('gray')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('estado', 'Pendiente')),

            'en_curso' => Tab::make('2. En Campo')
                ->badge(Incidencia::whereIn('estado', ['Asignada', 'En_Progreso', 'En_Espera'])->count())
                ->badgeColor('info')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('estado', ['Asignada', 'En_Progreso', 'En_Espera'])),

            'revisar_supervisor' => Tab::make('3. Por Revisar (Supervisor)')
                ->badge(Incidencia::where('estado', 'Resuelta')->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('estado', 'Resuelta')),

            'cerrar_comercial' => Tab::make('4. Por Cerrar (Comercial)')
                ->badge(Incidencia::where('estado', 'Revisada_Supervisor')->count())
                ->badgeColor('primary')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('estado', 'Revisada_Supervisor')),

            'cerradas' => Tab::make('5. Cerradas')
                ->badge(Incidencia::where('estado', 'Cerrada')->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('estado', 'Cerrada')),
        ];
    }
}
