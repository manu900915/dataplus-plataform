<?php
namespace App\Filament\Resources\InventarioMovimientoResource\Pages;

use App\Filament\Resources\InventarioMovimientoResource;
use App\Models\Item;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateInventarioMovimiento extends CreateRecord
{
    protected static string $resource = InventarioMovimientoResource::class;

    protected function getFormActions(): array
    {
        // Precargar el item si viene del botón "ajustar" del ItemResource
        if ($itemId = request()->query('item_id')) {
            $this->form->fill(['item_id' => $itemId, 'tipo' => 'salida']);
        }

        return parent::getFormActions();
    }

    protected function handleRecordCreation(array $data): Model
    {
        $item = Item::findOrFail($data['item_id']);

        return $item->mover(
            tipo: $data['tipo'],
            cantidad: (float) $data['cantidad'],
            costo: isset($data['costo_unitario']) ? (float) $data['costo_unitario'] : null,
            extra: [
                'almacen_id' => $data['almacen_id'] ?? null,
                'motivo' => $data['motivo'] ?? null,
                'user_id' => auth()->id(),
            ],
        );
    }
}