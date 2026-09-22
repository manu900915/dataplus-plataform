<?php

namespace App\Filament\Resources\ItemResource\Pages;

use App\Filament\Resources\ItemResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateItem extends CreateRecord
{
    protected static string $resource = ItemResource::class;

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()->label('Guardar y Crear Movimiento de Entrada'),
            $this->getCreateAnotherFormAction()->label('Guardar'),
            Actions\Action::make('cancel')
                ->label('Cancelar')
                ->color('gray')
                ->icon('heroicon-o-x-mark')
                ->url(fn () => ItemResource::getUrl('index')),
        ];
    }

    protected function handleRecordCreation(array $data): Model
    {
        $almacenId = $data['almacen_inicial_id'] ?? null;
        $stockInicial = $data['stock_inicial'] ?? 0;
        unset($data['almacen_inicial_id'], $data['stock_inicial']);

        $item = static::getModel()::create($data);

        if ($almacenId && $stockInicial > 0) {
            $item->mover(
                tipo: 'entrada',
                cantidad: (float) $stockInicial,
                costo: $data['precio_costo'] ?? $data['precio_unitario'] ?? 0,
                extra: [
                    'almacen_id' => $almacenId,
                    'motivo' => 'Stock inicial - Creación del item',
                    'user_id' => auth()->id(),
                ]
            );
        }

        return $item;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}