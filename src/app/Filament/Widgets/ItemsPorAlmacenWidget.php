<?php

namespace App\Filament\Widgets;

use App\Models\Almacen;
use App\Models\InventarioMovimiento;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class ItemsPorAlmacenWidget extends Widget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';
    protected static string $view = 'filament.widgets.items-por-almacen-widget';

    public function getAlmacenesConItems()
    {
        return Almacen::withCount(['movimientos as items_count' => function ($query) {
                $query->select(DB::raw('count(distinct item_id)'));
            }])
            ->withSum('movimientos', 'cantidad')
            ->get()
            ->map(function ($almacen) {
                return [
                    'id' => $almacen->id,
                    'nombre' => $almacen->nombre,
                    'ubicacion' => ($almacen->municipio ?? '') . ', ' . ($almacen->provincia ?? ''),
                    'items_count' => $almacen->items_count ?? 0,
                    'total_stock' => $almacen->movimientos_sum_cantidad ?? 0,
                    'url' => \App\Filament\Resources\InventarioMovimientoResource::getUrl('index', [
                        'tableFilters' => [
                            'almacen_id' => ['values' => [$almacen->id]],
                        ],
                    ]),
                ];
            });
    }
}