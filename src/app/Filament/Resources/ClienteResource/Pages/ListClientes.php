<?php

namespace App\Filament\Resources\ClienteResource\Pages;

use App\Filament\Resources\ClienteResource;
use App\Services\ClienteSyncService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListClientes extends ListRecords
{
    protected static string $resource = ClienteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('syncClientsCsv')
                ->label('Sincronizar Lista CSV')
                ->icon('heroicon-o-arrow-path')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('Sincronizar Códigos y Nombres Oficiales')
                ->modalDescription('¿Deseas sincronizar los códigos CLI-2026-ID y las denominaciones de negocio según el listado único oficial CSV? Esto también depurará los registros con nombres puramente numéricos.')
                ->modalSubmitActionLabel('Sincronizar Ahora')
                ->action(function (ClienteSyncService $syncService) {
                    try {
                        $result = $syncService->syncFromCsv();
                        Notification::make()
                            ->title('✅ Clientes Sincronizados')
                            ->body("Actualizados: {$result['updated']} | Nuevos: {$result['created']} | Depurados: {$result['cleaned']}")
                            ->success()
                            ->send();
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('❌ Error de Sincronización')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Actions\CreateAction::make()
                ->label('Nuevo Cliente'),
        ];
    }
}
