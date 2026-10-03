@props(['label', 'value', 'icon' => 'heroicon-o-circle-stack', 'color' => 'blue', 'hint' => null])

{{--
    Tarjeta de estadística con:
    - degradado coherente (paleta única)
    - icono en contenedor translúcido (jerarquía visual correcta)
    - número animado con Alpine (count-up)
--}}
<div {{ $attributes->merge(['class' => 'dash-card group relative min-w-0 overflow-hidden rounded-2xl p-6 text-white shadow-lg transition-all duration-300 hover:-translate-y-1 hover:shadow-xl', 'style' => 'background: ' . $gradientStyle . ';']) }}>
    <div class="relative flex min-h-28 items-start justify-between gap-6">
        <div class="min-w-0">
            <p class="text-[11px] font-semibold uppercase leading-snug tracking-wider text-white/75">{{ $label }}</p>
            <p class="mt-3 font-display text-3xl font-bold tabular-nums"
               x-data="{ shown: 0, target: {{ is_numeric($value ?? 0) ? (float) $value : 0 }}, fmt: {{ isset($currency) && $currency ? 'true' : 'false' }} }"
               x-init="
                   if (target > 0) {
                       const dur = 900, t0 = performance.now();
                       const step = (t) => {
                           const p = Math.min((t - t0) / dur, 1);
                           shown = target * (1 - Math.pow(1 - p, 3));
                           if (p < 1) requestAnimationFrame(step);
                       };
                       requestAnimationFrame(step);
                   } else { shown = target; }
               "
               x-text="fmt ? '$' + shown.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : Math.round(shown).toLocaleString('en-US')">
            </p>
            @if($hint)
                <p class="mt-2 text-xs text-white/70">{{ $hint }}</p>
            @endif
        </div>

        <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center transition-transform duration-300 group-hover:rotate-3 group-hover:scale-110">
            <x-dynamic-component :component="$icon" class="h-7 w-7 text-white/95" />
        </div>
    </div>
</div>
