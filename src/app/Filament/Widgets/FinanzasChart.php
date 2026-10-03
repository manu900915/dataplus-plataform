<?php

namespace App\Filament\Widgets;

use App\Filament\Concerns\AnimatedCharts;
use App\Models\LineaPresupuesto;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class FinanzasChart extends ChartWidget
{
    use AnimatedCharts;

    protected static ?string $heading = 'Distribución del Presupuesto por Tipo';
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 2;

    protected function getData(): array
    {
        $stats = LineaPresupuesto::select('tipo_linea', DB::raw('SUM(subtotal) as total'))
            ->groupBy('tipo_linea')
            ->pluck('total', 'tipo_linea');

        $labels = [
            'equipamiento' => 'Equipamiento',
            'mano_obra' => 'Mano de Obra',
            'material' => 'Materiales',
            'transporte' => 'Transporte',
            'alimentacion' => 'Alimentación',
            'otro' => 'Otros',
        ];

        $colors = [
            'equipamiento' => 'rgba(59, 130, 246, 0.8)',
            'mano_obra' => 'rgba(245, 158, 11, 0.8)',
            'material' => 'rgba(16, 185, 129, 0.8)',
            'transporte' => 'rgba(239, 68, 68, 0.8)',
            'alimentacion' => 'rgba(139, 92, 246, 0.8)',
            'otro' => 'rgba(107, 114, 128, 0.8)',
        ];

        $data = [];
        $bgColors = [];
        $displayLabels = [];

        foreach ($labels as $key => $label) {
            if (isset($stats[$key]) && $stats[$key] > 0) {
                $data[] = round($stats[$key], 2);
                $bgColors[] = $colors[$key];
                $displayLabels[] = $label;
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'USD',
                    'data' => $data,
                    'backgroundColor' => $bgColors,
                    'borderRadius' => 6,
                ],
            ],
            'labels' => $displayLabels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return $this->animatedOptions([
            'indexAxis' => 'y',
            'plugins' => ['legend' => ['display' => false]],
            'scales' => [
                'x' => ['beginAtZero' => true, 'grid' => ['display' => false], 'border' => ['display' => false]],
                'y' => ['grid' => ['display' => false], 'border' => ['display' => false]],
            ],
        ]);
    }
}