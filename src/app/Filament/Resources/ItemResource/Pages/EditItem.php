<?php

namespace App\Filament\Resources\ItemResource\Pages;

use App\Filament\Resources\InventarioMovimientoResource;
use App\Filament\Resources\ItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditItem extends EditRecord
{
    protected static string $resource = ItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('ajustarStock')
                ->label('Ajustar Stock')
                ->icon('heroicon-o-adjustments-horizontal')
                ->color('warning')
                ->url(fn () => InventarioMovimientoResource::getUrl('create', ['item_id' => $this->record->id])),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()->label('Guardar Cambios'),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $almacenId = $data['almacen_inicial_id'] ?? null;
        $stockInicial = $data['stock_inicial'] ?? 0;
        
        unset($data['almacen_inicial_id'], $data['stock_inicial']);
        
        $record->update($data);
        
        if ($almacenId && $stockInicial > 0 && $record->stock_actual == 0) {
            $record->mover(
                tipo: 'entrada',
                cantidad: (float) $stockInicial,
                costo: $data['precio_costo'] ?? $data['precio_unitario'] ?? 0,
                extra: [
                    'almacen_id' => $almacenId,
                    'motivo' => 'Stock inicial - Actualización del item',
                    'user_id' => auth()->id(),
                ]
            );
        }
        
        return $record;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}