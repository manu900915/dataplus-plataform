<?php

namespace App\Filament\Widgets;

use App\Models\Proyecto;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ProyectosChart extends ChartWidget
{
    protected static ?string $heading = 'Proyectos por Estado';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $stats = Proyecto::select('estado', DB::raw('count(*) as total'))
            ->groupBy('estado')
            ->pluck('total', 'estado');

        return [
            'datasets' => [
                [
                    'label' => 'Cantidad',
                    'data' => [
                        $stats['borrador'] ?? 0,
                        $stats['en_progreso'] ?? 0,
                        $stats['completado'] ?? 0,
                        $stats['cancelado'] ?? 0,
                    ],
                    'backgroundColor' => [
                        'rgba(107, 114, 128, 0.7)',
                        'rgba(59, 130, 246, 0.7)',
                        'rgba(16, 185, 129, 0.7)',
                        'rgba(239, 68, 68, 0.7)',
                    ],
                    'borderColor' => [
                        'rgb(107, 114, 128)',
                        'rgb(59, 130, 246)',
                        'rgb(16, 185, 129)',
                        'rgb(239, 68, 68)',
                    ],
                    'borderWidth' => 1,
                ],
            ],
            'labels' => ['Borrador', 'En Progreso', 'Completado', 'Cancelado'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'plugins' => [
                'legend' => ['display' => false],
            ],
            'scales' => [
                'y' => ['beginAtZero' => true, 'ticks' => ['stepSize' => 1]],
            ],
        ];
    }
}