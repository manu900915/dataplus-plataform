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

            'pendientes' => Tab::make('Pendientes')
                ->badge(Incidencia::where('estado', 'Pendiente')->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('estado', 'Pendiente')),

            'en_curso' => Tab::make('En Curso')
                ->badge(Incidencia::whereIn('estado', ['Asignada', 'En_Progreso', 'En_Espera'])->count())
                ->badgeColor('info')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('estado', ['Asignada', 'En_Progreso', 'En_Espera'])),

            'sla_vencido' => Tab::make('SLA Vencido')
                ->badge(
                    Incidencia::where('fecha_limite', '<', now())
                        ->whereNotIn('estado', ['Resuelta', 'Cerrada', 'Cancelada'])
                        ->count()
                )
                ->badgeColor('danger')
                ->modifyQueryUsing(
                    fn (Builder $query) => $query->where('fecha_limite', '<', now())
                        ->whereNotIn('estado', ['Resuelta', 'Cerrada', 'Cancelada'])
                ),

            'resueltas' => Tab::make('Resueltas')
                ->badge(Incidencia::whereIn('estado', ['Resuelta', 'Cerrada'])->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('estado', ['Resuelta', 'Cerrada'])),
        ];
    }
}
