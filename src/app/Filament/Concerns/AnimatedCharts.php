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
            'layout' => ['padding' => ['top' => 8, 'right' => 12, 'bottom' => 8, 'left' => 12]],
            'elements' => [
                'line' => ['borderWidth' => 2, 'tension' => 0.35],
                'point' => ['radius' => 3, 'hoverRadius' => 5],
                'bar' => ['borderWidth' => 0, 'borderRadius' => 8, 'borderSkipped' => false],
                'arc' => ['borderWidth' => 0, 'hoverBorderWidth' => 0],
            ],
            'scales' => [
                'x' => [
                    'ticks' => [
                        'color' => '#64748b',
                        'font' => ['family' => "'Inter',sans-serif", 'size' => 11, 'weight' => '500'],
                    ],
                    'grid' => ['color' => 'rgba(148, 163, 184, 0.14)', 'drawTicks' => false],
                    'border' => ['display' => false],
                ],
                'y' => [
                    'ticks' => [
                        'color' => '#64748b',
                        'font' => ['family' => "'Inter',sans-serif", 'size' => 11, 'weight' => '500'],
                    ],
                    'grid' => ['color' => 'rgba(148, 163, 184, 0.14)', 'drawTicks' => false],
                    'border' => ['display' => false],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                    'labels' => [
                        'usePointStyle' => true,
                        'pointStyle' => 'circle',
                        'boxWidth' => 8,
                        'padding' => 14,
                        'color' => '#64748b',
                        'font' => ['family' => "'Plus Jakarta Sans','Inter',sans-serif", 'size' => 12, 'weight' => '500'],
                    ],
                ],
                'tooltip' => [
                    'backgroundColor' => 'rgba(15, 23, 42, 0.94)',
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
