<?php

namespace App\Filament\Resources\TipoProyectoResource\Pages;

use App\Filament\Resources\TipoProyectoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTipoProyectos extends ListRecords
{
    protected static string $resource = TipoProyectoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Crear Tipo de Proyecto'),
        ];
    }
}