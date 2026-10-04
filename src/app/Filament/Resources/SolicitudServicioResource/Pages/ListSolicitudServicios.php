<?php

namespace App\Filament\Resources\SolicitudServicioResource\Pages;

use App\Filament\Resources\SolicitudServicioResource;
use App\Models\SolicitudServicio;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListSolicitudServicios extends ListRecords
{
    protected static string $resource = SolicitudServicioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Nueva Solicitud Comercial'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'todas' => Tab::make('Todas')
                ->badge(SolicitudServicio::count()),

            'pendientes' => Tab::make('Por Aprobar (Supervisor)')
                ->badge(SolicitudServicio::where('estado', 'Pendiente_Aprobacion')->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('estado', 'Pendiente_Aprobacion')),

            'convertidas' => Tab::make('Proyectos Creados')
                ->badge(SolicitudServicio::where('estado', 'Convertida_Proyecto')->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('estado', 'Convertida_Proyecto')),
        ];
    }
}
