<?php

namespace App\Filament\Concerns;

/**
 * Animaciones y estilo Chart.js compartidas por todos los gráficos
 * del dashboard (transiciones suaves, tooltips modernos, fuentes).
 */
trait AnimatedCharts
{
    protected function animatedOptions(array $extra = []): array
    {
        $base = [
            'responsive' => true,
            'animation' => [
                'duration' => 1000,
                'easing' => 'easeOutQuart',
            ],
            'transitions' => [
                'active' => ['animation' => ['duration' => 350]],
                'resize' => ['animation' => ['duration' => 200]],
            ],
            'interaction' => ['mode' => 'index', 'intersect' => false],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                    'labels' => [
                        'usePointStyle' => true,
                        'pointStyle' => 'circle',
                        'boxWidth' => 8,
                        'padding' => 16,
                        'font' => ['family' => "'Plus Jakarta Sans','Inter',sans-serif", 'size' => 12, 'weight' => '500'],
                    ],
                ],
                'tooltip' => [
                    'backgroundColor' => 'rgba(17, 24, 39, 0.92)',
                    'titleFont' => ['family' => "'Plus Jakarta Sans','Inter',sans-serif", 'size' => 13, 'weight' => '700'],
                    'bodyFont' => ['family' => "'Inter',sans-serif", 'size' => 12],
                    'padding' => 12,
                    'cornerRadius' => 10,
                    'displayColors' => true,
                    'boxPadding' => 6,
                ],
            ],
        ];

        return array_replace_recursive($base, $extra);
    }
}
