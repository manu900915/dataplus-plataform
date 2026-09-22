<?php

namespace App\Filament\Widgets;

use App\Models\Proyecto;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class OperacionesChart extends ChartWidget
{
    protected static ?string $heading = 'Proyectos I+D por Estado Kanban';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        $stats = Proyecto::where('tipo_seguimiento', 'investigacion')
            ->select('estado_kanban', DB::raw('count(*) as total'))
            ->groupBy('estado_kanban')
            ->pluck('total', 'estado_kanban');

        return [
            'datasets' => [
                [
                    'label' => 'Proyectos',
                    'data' => [
                        $stats['por_hacer'] ?? 0,
                        $stats['en_progreso'] ?? 0,
                        $stats['en_revision'] ?? 0,
                        $stats['completado'] ?? 0,
                    ],
                    'backgroundColor' => [
                        'rgba(107, 114, 128, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                    ],
                    'borderRadius' => 8,
                ],
            ],
            'labels' => ['Por Hacer', 'En Progreso', 'En Revisión', 'Completado'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}