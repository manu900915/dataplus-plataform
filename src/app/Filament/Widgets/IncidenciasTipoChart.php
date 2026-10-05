<?php

namespace App\Filament\Widgets;

use App\Filament\Concerns\AnimatedCharts;
use App\Models\Incidencia;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class IncidenciasTipoChart extends ChartWidget
{
    use AnimatedCharts;

    protected static ?string $heading = 'Averías por Especialidad / Tecnología';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        $stats = Incidencia::select('tipo', DB::raw('count(*) as total'))
            ->groupBy('tipo')
            ->pluck('total', 'tipo')
            ->all();

        $cctv = $stats['CCTV'] ?? 0;
        $saci = $stats['SACI'] ?? 0;
        $redes = $stats['Redes'] ?? 0;
        $gr = $stats['Gestion_Remota'] ?? 0;

        return [
            'datasets' => [
                [
                    'label' => 'Total',
                    'data' => [$cctv, $saci, $redes, $gr],
                    'backgroundColor' => [
                        '#3b82f6', // CCTV - Azul
                        '#10b981', // SACI - Esmeralda
                        '#f59e0b', // Redes - Ámbar
                        '#f43f5e', // Gestión Remota - Rosa
                    ],
                    'borderWidth' => 0,
                    'hoverOffset' => 6,
                ],
            ],
            'labels' => [
                "CCTV ({$cctv})",
                "SACI Alarma ({$saci})",
                "Redes ({$redes})",
                "Gestión Remota ({$gr})",
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return $this->animatedOptions([
            'cutout' => '70%',
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
        ]);
    }
}
