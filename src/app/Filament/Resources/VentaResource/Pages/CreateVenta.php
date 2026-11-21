<?php

namespace App\Filament\Resources\VentaResource\Pages;

use App\Filament\Resources\VentaResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateVenta extends CreateRecord
{
    protected static string $resource = VentaResource::class;

    protected function afterCreate(): void
    {
        $this->record->recalcularTotales();

        if ($this->record->estado === 'completada') {
            try {
                $this->record->aplicarDescuentoInventario();
            } catch (\Throwable $e) {
                Notification::make()
                    ->title('Error de stock en almacén')
                    ->body($e->getMessage())
                    ->danger()
                    ->send();

                $this->halt();
            }
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
