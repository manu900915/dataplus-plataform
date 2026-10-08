@props(['collapsible' => true])

<div 
    x-data="{ sidebarOpen: ! $store.sidebar.isOpen }"
    x-init="() => {
        const stored = localStorage.getItem('sidebarOpen');
        if (stored !== null) {
            $store.sidebar.isOpen = stored === 'true';
        }
    }"
    class="fixed inset-y-0 left-0 z-50 flex flex-col bg-gray-900 transition-all duration-300 ease-in-out select-none"
    :class="sidebarOpen ? 'w-64' : 'w-20'"
>
    {{-- Header personalizado --}}
    @include('filament.components.sidebar.header')

    {{-- Navegación --}}
    <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4 select-none">
        @foreach ($this->getNavigation() as $navigationItem)
            @php
                $icon = $navigationItem->getIcon();
                $isActive = $navigationItem->isActive();
            @endphp
            
            <a
                href="{{ $navigationItem->getUrl() }}"
                target="{{ $navigationItem->shouldOpenUrlInNewTab() ? '_blank' : '' }}"
                class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all duration-200 hover:scale-[1.02] cursor-pointer select-none outline-none focus:outline-none"
                :class="sidebarOpen ? '' : 'justify-center'"
                :class="$isActive ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-gray-300 hover:bg-gray-800 hover:text-white'"
                style="caret-color: transparent;"
            >
                @if ($icon)
                    <x-dynamic-component 
                        :component="$icon" 
                        class="h-5 w-5 flex-shrink-0 pointer-events-none"
                        :class="$isActive ? 'text-white' : 'text-gray-400 group-hover:text-white'"
                    />
                @endif
                
                <span 
                    x-show="sidebarOpen"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 -translate-x-4"
                    x-transition:enter-end="opacity-100 translate-x-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-x-0"
                    x-transition:leave-end="opacity-0 -translate-x-4"
                    class="whitespace-nowrap select-none pointer-events-none"
                    style="caret-color: transparent;"
                >
                    {{ $navigationItem->getLabel() }}
                </span>
            </a>
        @endforeach
    </nav>
</div>
