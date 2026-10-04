<?php

namespace App\Filament\Resources\SolicitudServicioResource\Pages;

use App\Filament\Resources\SolicitudServicioResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSolicitudServicio extends EditRecord
{
    protected static string $resource = SolicitudServicioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
