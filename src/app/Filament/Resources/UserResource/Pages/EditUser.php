<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make(),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Salvar')
                ->action('save')
                ->after(fn () => $this->redirect(ListUsers::getUrl()))
                ->color('primary'),

            Action::make('cancel')
                ->label('Cancelar')
                ->action(fn () => $this->redirect(ListUsers::getUrl()))
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
        return ListUsers::getUrl();
    }
}