<?php

namespace App\Filament\Widgets;

use App\Filament\Concerns\AnimatedCharts;
use App\Models\Proyecto;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class OperacionesChart extends ChartWidget
{
    use AnimatedCharts;

    protected static ?string $heading = 'Proyectos por Fase de Seguimiento';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        $stats = Proyecto::select('estado_kanban', DB::raw('count(*) as total'))
            ->groupBy('estado_kanban')
            ->pluck('total', 'estado_kanban');

        // Contabilizar también completados por estado general si estado_kanban estuviese desincronizado
        $completados = Proyecto::where('estado_kanban', 'completado')
            ->orWhere('estado', 'completado')
            ->count();

        $enRevision = Proyecto::where('estado_kanban', 'en_revision')
            ->where('estado', '!=', 'completado')
            ->count();

        $enProgreso = Proyecto::where(function ($q) {
                $q->where('estado_kanban', 'en_progreso')
                  ->orWhere('estado', 'en_progreso');
            })
            ->where('estado', '!=', 'completado')
            ->where('estado_kanban', '!=', 'completado')
            ->where('estado_kanban', '!=', 'en_revision')
            ->count();

        $porHacer = Proyecto::where(function ($q) {
                $q->where('estado_kanban', 'por_hacer')
                  ->orWhereNull('estado_kanban')
                  ->orWhere('estado', 'borrador');
            })
            ->where('estado', '!=', 'completado')
            ->where('estado_kanban', '!=', 'completado')
            ->where('estado', '!=', 'en_progreso')
            ->where('estado_kanban', '!=', 'en_progreso')
            ->where('estado_kanban', '!=', 'en_revision')
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Proyectos',
                    'data' => [
                        $porHacer,
                        $enProgreso,
                        $enRevision,
                        $completados,
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

    protected function getOptions(): array
    {
        return $this->animatedOptions([
            'plugins' => ['legend' => ['display' => false]],
            'scales' => [
                'y' => ['beginAtZero' => true, 'ticks' => ['stepSize' => 1], 'grid' => ['display' => false], 'border' => ['display' => false]],
                'x' => ['grid' => ['display' => false], 'border' => ['display' => false]],
            ],
        ]);
    }
}
