<?php

namespace App\View\Components;

use Illuminate\View\Component;

/**
 * Tarjeta de estadística del Dashboard.
 *
 * Mantiene UNA sola definición visual (degradado, icono, tipografía,
 * animación de número) para todas las pestañas del dashboard.
 */
class DashboardStat extends Component
{
    /** Paleta de degradados permitida (coherente en toda la plataforma). */
    protected const GRADIENTS = [
        'blue'      => 'from-sky-500 to-blue-700',
        'green'     => 'from-emerald-500 to-teal-700',
        'red'       => 'from-rose-500 to-red-700',
        'amber'     => 'from-amber-500 to-orange-700',
        'purple'    => 'from-violet-500 to-purple-700',
        'indigo'    => 'from-indigo-500 to-blue-800',
        'emerald'   => 'from-emerald-500 to-green-700',
        'slate'     => 'from-slate-500 to-slate-700',
        'cyan'      => 'from-cyan-500 to-sky-700',
        'fuchsia'   => 'from-fuchsia-500 to-pink-700',
    ];

    protected const GRADIENT_STYLES = [
        'blue' => 'linear-gradient(135deg, #0ea5e9, #1d4ed8)',
        'green' => 'linear-gradient(135deg, #10b981, #0f766e)',
        'red' => 'linear-gradient(135deg, #f43f5e, #b91c1c)',
        'amber' => 'linear-gradient(135deg, #f59e0b, #c2410c)',
        'purple' => 'linear-gradient(135deg, #8b5cf6, #6b21a8)',
        'indigo' => 'linear-gradient(135deg, #6366f1, #1e40af)',
        'emerald' => 'linear-gradient(135deg, #10b981, #15803d)',
        'slate' => 'linear-gradient(135deg, #64748b, #334155)',
        'cyan' => 'linear-gradient(135deg, #06b6d4, #0369a1)',
        'fuchsia' => 'linear-gradient(135deg, #d946ef, #be185d)',
    ];

    public function __construct(
        public string $label,
        public string|int|float $value,
        public string $icon = 'heroicon-o-circle-stack',
        public string $color = 'blue',
        public ?string $hint = null,
        public bool $currency = false,
        public int $columns = 4,
    ) {
    }

    /** Valor formateado según el tipo (moneda, miles o entero). */
    public function displayValue(): string
    {
        $numeric = is_numeric($this->value);

        if ($numeric && $this->currency) {
            return '$' . number_format((float) $this->value, 2);
        }

        return $numeric
            ? number_format((float) $this->value, ($this->value == (int) $this->value) ? 0 : 2)
            : (string) $this->value;
    }

    /** Clases tailwind del degradado (fallback seguro a azul). */
    public function gradientClasses(): string
    {
        return self::GRADIENTS[$this->color] ?? self::GRADIENTS['blue'];
    }

    public function gradientStyle(): string
    {
        return self::GRADIENT_STYLES[$this->color] ?? self::GRADIENT_STYLES['blue'];
    }

    /** Columnas responsivas según cantidad de tarjetas del grupo. */
    public function columnClasses(): string
    {
        return match ($this->columns) {
            3 => 'sm:col-span-1',
            2 => 'sm:col-span-1',
            default => 'sm:col-span-2 lg:col-span-1',
        };
    }

    public function render()
    {
        return view('components.dashboard-stat', [
            'gradientClasses' => $this->gradientClasses(),
            'gradientStyle' => $this->gradientStyle(),
        ]);
    }
}
