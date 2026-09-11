<?php

namespace App\Filament\Resources\CategoriaItemResource\Pages;

use App\Filament\Resources\CategoriaItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCategoriaItems extends ListRecords
{
    protected static string $resource = CategoriaItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
