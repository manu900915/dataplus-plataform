<?php

namespace App\Filament\Widgets;

use App\Filament\Concerns\AnimatedCharts;
use App\Models\Proyecto;
use App\Models\SolicitudServicio;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class PipelineComercialChart extends ChartWidget
{
    use AnimatedCharts;

    protected static ?string $heading = 'Embudo: Solicitudes Comerciales a Proyectos';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        $solicitudesPendientes = SolicitudServicio::where('estado', 'Pendiente_Aprobacion')->count();
        $solicitudesConvertidas = SolicitudServicio::where('estado', 'Convertida_Proyecto')->count();
        $proyectosBorrador = Proyecto::where('estado', 'borrador')->count();
        $proyectosEnProgreso = Proyecto::where('estado', 'en_progreso')->count();
        $proyectosCompletados = Proyecto::where('estado', 'completado')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Total Registros',
                    'data' => [
                        $solicitudesPendientes,
                        $solicitudesConvertidas,
                        $proyectosBorrador,
                        $proyectosEnProgreso,
                        $proyectosCompletados,
                    ],
                    'backgroundColor' => [
                        'rgba(245, 158, 11, 0.85)', // Ámbar - Solicitudes pendientes
                        'rgba(14, 165, 233, 0.85)',  // Sky - Aprobadas
                        'rgba(148, 163, 184, 0.85)', // Slate - Proyecto Borrador
                        'rgba(59, 130, 246, 0.85)',  // Azul - En Ejecución
                        'rgba(16, 185, 129, 0.85)',  // Esmeralda - Entregado
                    ],
                    'borderRadius' => 6,
                ],
            ],
            'labels' => [
                'Sol. Pendientes',
                'Sol. Aprobadas',
                'Prj. Planificación',
                'Prj. En Ejecución',
                'Prj. Completados',
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
            'plugins' => ['legend' => ['display' => false]],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => ['stepSize' => 1],
                    'grid' => ['display' => false],
                    'border' => ['display' => false],
                ],
                'x' => ['grid' => ['display' => false], 'border' => ['display' => false]],
            ],
        ]);
    }
}
