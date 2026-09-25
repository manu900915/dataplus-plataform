@props(['label', 'value', 'icon' => 'heroicon-o-circle-stack', 'color' => 'blue', 'hint' => null])

{{--
    Tarjeta de estadística con:
    - degradado coherente (paleta única)
    - icono en contenedor translúcido (jerarquía visual correcta)
    - número animado con Alpine (count-up)
--}}
<div {{ $attributes->merge(['class' => 'dash-card group relative overflow-hidden rounded-2xl bg-gradient-to-br ' . ($gradientClasses ?? 'from-sky-500 to-blue-700') . ' p-5 text-white shadow-lg transition-all duration-300 hover:-translate-y-1 hover:shadow-xl']) }}>
    {{-- brillo decorativo --}}
    <div class="pointer-events-none absolute -right-6 -top-10 h-32 w-32 rounded-full bg-white/10 blur-2xl transition-transform duration-500 group-hover:scale-150"></div>

    <div class="relative flex items-start justify-between gap-4">
        <div class="min-w-0">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-white/75 leading-snug">{{ $label }}</p>
            <p class="mt-2 font-display text-3xl font-bold tabular-nums"
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
                <p class="mt-1 text-xs text-white/60">{{ $hint }}</p>
            @endif
        </div>

        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-white/15 ring-1 ring-white/25 backdrop-blur-sm transition-transform duration-300 group-hover:rotate-3 group-hover:scale-110">
            <x-dynamic-component :component="$icon" class="h-6 w-6 text-white" />
        </div>
    </div>
</div>
