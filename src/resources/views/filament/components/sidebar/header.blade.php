@props(['collapsible' => true])

<div 
    x-data="{ 
        sidebarOpen: ! $store.sidebar.isOpen,
        toggleSidebar: () => {
            $store.sidebar.isOpen = ! $store.sidebar.isOpen;
            localStorage.setItem('sidebarOpen', $store.sidebar.isOpen);
        }
    }"
    x-init="() => {
        const stored = localStorage.getItem('sidebarOpen');
        if (stored !== null) {
            $store.sidebar.isOpen = stored === 'true';
        }
    }"
    class="flex h-16 items-center justify-between border-b border-gray-800 px-4 transition-all duration-300 ease-in-out"
>
    {{-- Logo cuando está EXPANDIDO --}}
    <div 
        x-show="sidebarOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-x-2"
        x-transition:enter-end="opacity-100 translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-x-0"
        x-transition:leave-end="opacity-0 -translate-x-2"
        class="flex items-center gap-2"
    >
        <img src="{{ asset('images/9.png') }}" alt="Dataplus" class="h-10 w-10 rounded-full flex-shrink-0">
        <img src="{{ asset('images/logo-dataplus.png') }}" alt="Dataplus Platform" class="h-8 w-auto object-contain flex-shrink-0">
    </div>

    {{-- Logo cuando está CONTRAÍDO --}}
    <div 
        x-show="!sidebarOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-90"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90"
        class="absolute inset-0 flex items-center justify-center"
    >
        <img src="{{ asset('images/9.png') }}" alt="Dataplus" class="h-10 w-10 rounded-full shadow-lg">
    </div>

    {{-- Botón de toggle --}}
    @if ($collapsible)
        <button
            x-on:click="toggleSidebar"
            class="relative z-10 flex h-8 w-8 items-center justify-center rounded-lg bg-gray-800 text-gray-400 transition-all duration-300 hover:bg-gray-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
            :class="sidebarOpen ? 'rotate-0' : 'rotate-180'"
        >
            <svg 
                class="h-5 w-5 transition-transform duration-300"
                :class="sidebarOpen ? 'rotate-0' : 'rotate-180'"
                fill="none" 
                stroke="currentColor" 
                viewBox="0 0 24 24"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>
    @endif
</div>