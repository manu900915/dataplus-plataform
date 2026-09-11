<?php

namespace App\Filament\Resources\InventarioMovimientoResource\Pages;

use App\Filament\Resources\InventarioMovimientoResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewInventarioMovimiento extends ViewRecord
{
    protected static string $resource = InventarioMovimientoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Editar'),
        ];
    }
}