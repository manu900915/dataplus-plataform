<?php

namespace App\Filament\Resources\ClienteResource\Pages;

use App\Filament\Resources\ClienteResource;
use App\Filament\Resources\ClienteResource\Pages\ListClientes;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateCliente extends CreateRecord
{
    protected static string $resource = ClienteResource::class;

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Guardar')
                ->action('save')
                ->after(fn () => $this->redirect(ListClientes::getUrl()))
                ->color('primary'),

            Action::make('saveAndCreateAnother')
                ->label('Guardar y crear otro')
                ->action('saveAndCreateAnother')
                ->color('gray'),

            Action::make('cancel')
                ->label('Cancelar')
                ->action(fn () => $this->redirect(ListClientes::getUrl()))
                ->color('danger')
                ->outlined(),
        ];
    }

    public function getFormActionsAlignment(): string
    {
        return 'start';
    }
}