<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Salvar')
                ->submit('create') // 👈 Ejecuta el guardado nativo de Filament
                ->color('primary'),

            Action::make('saveAndCreateAnother')
                ->label('Salvar y Crear otro')
                ->action('createAnother') // 👈 Guarda y limpia el formulario para otro
                ->color('gray'),

            Action::make('cancel')
                ->label('Cancelar')
                ->url(fn () => ListUsers::getUrl()) // 👈 Redirección limpia a la tabla
                ->color('danger')
                ->outlined(),
        ];
    }

    // 👇 Cambiado de 'protected' a 'public' para coincidir con la clase padre de Filament
    public function getFormActionsAlignment(): string
    {
        return 'start'; // Alinea los botones a la izquierda
    }
}