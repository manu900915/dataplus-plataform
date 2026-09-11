<?php

namespace App\Filament\Resources\InventarioMovimientoResource\Pages;

use App\Filament\Resources\InventarioMovimientoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInventarioMovimientos extends ListRecords
{
    protected static string $resource = InventarioMovimientoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
