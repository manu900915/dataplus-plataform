<?php

namespace App\Filament\Resources\VentaResource\Pages;

use App\Filament\Resources\VentaResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditVenta extends EditRecord
{
    protected static string $resource = VentaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function beforeSave(): void
    {
        if ($this->record->inventario_descontado) {
            $this->record->revertirInventario("Reajuste por edición de Venta {$this->record->codigo}");
        }
    }

    protected function afterSave(): void
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
