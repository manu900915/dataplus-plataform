<?php

namespace App\Filament\Resources\SolicitudServicioResource\Pages;

use App\Filament\Resources\SolicitudServicioResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSolicitudServicio extends CreateRecord
{
    protected static string $resource = SolicitudServicioResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['comercial_id'] = auth()->id() ?? 1;
        return $data;
    }
}
