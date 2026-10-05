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
            $this->getCreateFormAction()
                ->label('Guardar')
                ->color('primary'),
            $this->getCreateAnotherFormAction()
                ->label('Guardar y crear otro')
                ->color('gray'),
            Action::make('cancel')
                ->label('Cancelar')
                ->url(fn () => ListClientes::getUrl())
                ->color('danger')
                ->outlined(),
        ];
    }

    public function getFormActionsAlignment(): string
    {
        return 'start';
    }

    protected function getRedirectUrl(): string
    {
        return ListClientes::getUrl();
    }
}
