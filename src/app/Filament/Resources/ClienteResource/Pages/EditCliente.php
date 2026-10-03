<?php

namespace App\Filament\Resources\ClienteResource\Pages;

use App\Filament\Resources\ClienteResource;
use App\Filament\Resources\ClienteResource\Pages\ListClientes;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCliente extends EditRecord
{
    protected static string $resource = ClienteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Eliminar'),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Guardar')
                ->action('save')
                ->after(fn () => $this->redirect(ListClientes::getUrl()))
                ->color('primary'),

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

    protected function getRedirectUrl(): string
    {
        return ListClientes::getUrl();
    }
}