@php
    $debounce = filament()->getGlobalSearchDebounce();
    $keyBindings = filament()->getGlobalSearchKeyBindings();
@endphp

<div x-id="['input']" class="fi-global-search-field" style="width: 100%; position: relative;">
    <label x-bind:for="$id('input')" class="sr-only">
        Buscar clientes, proyectos, ítems...
    </label>

    <div style="position: relative; display: flex; align-items: center; width: 100%;">
        {{-- Icono Lupa --}}
        <div style="position: absolute; left: 14px; display: flex; align-items: center; pointer-events: none; color: #64748b; z-index: 1;">
            <svg style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
        </div>

        {{-- Input Real con Livewire --}}
        <input
            autocomplete="off"
            maxlength="1000"
            placeholder="Buscar clientes, proyectos, ítems..."
            type="search"
            wire:key="global-search.field.input"
            wire:model.live.debounce.{{ $debounce }}="search"
            x-bind:id="$id('input')"
            x-on:keydown.down.prevent.stop="$dispatch('focus-first-global-search-result')"
            x-data="{}"
            @if ($keyBindings)
                x-mousetrap.global.{{ collect($keyBindings)->map(fn (string $keyBinding): string => str_replace('+', '-', $keyBinding))->implode('.') }}="document.getElementById($id('input')).focus()"
            @endif
            class="dp-global-search-input"
            style="width: 100%; height: 38px; background: #0d1726; border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 10px; padding: 0 48px 0 40px; color: #e2e8f0; font-size: 13px; font-weight: 500; outline: none; transition: all 0.2s ease; box-shadow: inset 0 2px 4px rgba(0,0,0,0.3);"
        />

        {{-- Badge de atajo ⌘K --}}
        <div style="position: absolute; right: 10px; display: flex; align-items: center; pointer-events: none; z-index: 1;">
            <kbd style="background: #1e293b; border: 1px solid rgba(255, 255, 255, 0.12); color: #64748b; border-radius: 6px; font-size: 11px; font-family: monospace; font-weight: 700; padding: 2px 6px; letter-spacing: -0.02em;">⌘K</kbd>
        </div>
    </div>
</div>
