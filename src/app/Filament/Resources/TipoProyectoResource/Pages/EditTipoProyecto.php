<?php

namespace App\Filament\Resources\TipoProyectoResource\Pages;

use App\Filament\Resources\TipoProyectoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTipoProyecto extends EditRecord
{
    protected static string $resource = TipoProyectoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}