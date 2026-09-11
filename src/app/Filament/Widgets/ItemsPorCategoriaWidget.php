<?php

namespace App\Filament\Widgets;

use App\Models\CategoriaItem;
use Filament\Widgets\Widget;

class ItemsPorCategoriaWidget extends Widget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';
    protected static string $view = 'filament.widgets.items-por-categoria-widget';

    public function getCategoriasConItems()
    {
        return CategoriaItem::withCount('items')
            ->withSum('items', 'stock_actual')
            ->where('activo', true)
            ->orderBy('items_count', 'desc')
            ->get()
            ->map(function ($categoria) {
                return [
                    'id' => $categoria->id,
                    'nombre' => $categoria->nombre,
                    'tipo' => $categoria->tipo,
                    'items_count' => $categoria->items_count,
                    'total_stock' => $categoria->items_sum_stock_actual ?? 0,
                    'url' => \App\Filament\Resources\ItemResource::getUrl('index', [
                        'tableFilters' => [
                            'categoria_id' => ['values' => [$categoria->id]],
                        ],
                    ]),
                ];
            });
    }
}