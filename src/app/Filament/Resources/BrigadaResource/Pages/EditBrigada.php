<?php

namespace App\Filament\Resources\BrigadaResource\Pages;

use App\Filament\Resources\BrigadaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBrigada extends EditRecord
{
    protected static string $resource = BrigadaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}