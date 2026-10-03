<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Services\LdapSyncService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('syncLdap')
                ->label('Sincronizar desde LDAP')
                ->icon('heroicon-o-arrow-path')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('Sincronizar Directorio LDAP')
                ->modalDescription('¿Deseas conectar al servidor LDAP para importar los nuevos usuarios y actualizar los existentes?')
                ->modalSubmitActionLabel('Sincronizar ahora')
                ->action(function (LdapSyncService $syncService) {
                    $result = $syncService->syncAllUsers();
                    Notification::make()
                        ->title('✅ Sincronización LDAP Completada')
                        ->body("Total en LDAP: {$result['total']} | Nuevos importados: {$result['imported']} | Actualizados: {$result['updated']}")
                        ->success()
                        ->send();
                }),

            Actions\CreateAction::make()
                ->label('Nuevo usuario local'),
        ];
    }
}
