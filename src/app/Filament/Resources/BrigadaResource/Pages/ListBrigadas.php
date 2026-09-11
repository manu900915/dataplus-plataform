<?php

namespace App\Filament\Resources\BrigadaResource\Pages;

use App\Filament\Resources\BrigadaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBrigadas extends ListRecords
{
    protected static string $resource = BrigadaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Nueva brigada'),
        ];
    }
}