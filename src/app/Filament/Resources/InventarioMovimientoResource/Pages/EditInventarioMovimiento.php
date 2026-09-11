<?php

namespace App\Filament\Resources\InventarioMovimientoResource\Pages;

use App\Filament\Resources\InventarioMovimientoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInventarioMovimiento extends EditRecord
{
    protected static string $resource = InventarioMovimientoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
