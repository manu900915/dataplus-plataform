<?php

namespace App\Filament\Widgets;

use App\Filament\Concerns\AnimatedCharts;
use App\Models\Incidencia;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class IncidenciasEstadoChart extends ChartWidget
{
    use AnimatedCharts;

    protected static ?string $heading = 'Incidencias por Etapa de Atención';
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        $stats = Incidencia::select('estado', DB::raw('count(*) as total'))
            ->groupBy('estado')
            ->pluck('total', 'estado')
            ->all();

        $enCampo = ($stats['Asignada'] ?? 0) + ($stats['En_Progreso'] ?? 0);
        $resueltasOCerradas = ($stats['Resuelta'] ?? 0) + ($stats['Revisada_Supervisor'] ?? 0) + ($stats['Cerrada'] ?? 0);

        return [
            'datasets' => [
                [
                    'label' => 'Incidencias',
                    'data' => [
                        $stats['Pendiente'] ?? 0,
                        $enCampo,
                        $stats['En_Espera'] ?? 0,
                        $stats['Resuelta'] ?? 0,
                        $stats['Revisada_Supervisor'] ?? 0,
                        $stats['Cerrada'] ?? 0,
                    ],
                    'backgroundColor' => [
                        'rgba(148, 163, 184, 0.85)', // Gris/Slate - Pendiente
                        'rgba(14, 165, 233, 0.85)',  // Sky - En campo
                        'rgba(168, 85, 247, 0.85)',  // Púrpura - En espera
                        'rgba(245, 158, 11, 0.85)',  // Ámbar - Culminada por técnico
                        'rgba(99, 102, 241, 0.85)',  // Índigo - Visto bueno supervisor
                        'rgba(16, 185, 129, 0.85)',  // Esmeralda - Cerrada
                    ],
                    'borderRadius' => 6,
                ],
            ],
            'labels' => [
                '1. Pendiente',
                '2. En Campo',
                '3. En Espera',
                '4. Por Revisar',
                '5. Por Cerrar',
                '6. Cerrada',
            ],
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
            'plugins' => [
                'legend' => ['display' => false],
            ],
            'scales' => [
                'x' => [
                    'beginAtZero' => true,
                    'ticks' => ['stepSize' => 1],
                    'grid' => ['display' => false],
                    'border' => ['display' => false],
                ],
                'y' => [
                    'grid' => ['display' => false],
                    'border' => ['display' => false],
                ],
            ],
        ]);
    }
}
