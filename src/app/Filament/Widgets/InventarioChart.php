<?php

namespace App\Filament\Widgets;

use App\Filament\Concerns\AnimatedCharts;
use App\Models\Item;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class InventarioChart extends ChartWidget
{
    use AnimatedCharts;

    protected static ?string $heading = 'Inventario por Categoría';
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 2;

    protected function getData(): array
    {
        $stats = Item::select('categoria_id', DB::raw('count(*) as total'))
            ->with('categoria')
            ->groupBy('categoria_id')
            ->get()
            ->map(fn ($item) => [
                'nombre' => $item->categoria->nombre ?? 'Sin categoría',
                'total' => $item->total,
            ]);

        $colors = [
            'rgba(59, 130, 246, 0.7)',
            'rgba(16, 185, 129, 0.7)',
            'rgba(245, 158, 11, 0.7)',
            'rgba(239, 68, 68, 0.7)',
            'rgba(139, 92, 246, 0.7)',
            'rgba(236, 72, 153, 0.7)',
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Items',
                    'data' => $stats->pluck('total')->toArray(),
                    'backgroundColor' => array_slice($colors, 0, $stats->count()),
                    'borderWidth' => 2,
                    'borderColor' => '#1f2937',
                ],
            ],
            'labels' => $stats->pluck('nombre')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return $this->animatedOptions([
            'cutout' => '62%',
            'plugins' => [
                'legend' => ['position' => 'right'],
            ],
        ]);
    }
}