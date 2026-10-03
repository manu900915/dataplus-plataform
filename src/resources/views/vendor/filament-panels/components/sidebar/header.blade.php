<div class="fi-sidebar-header flex h-16 items-center bg-white px-6 ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 lg:shadow-sm">
    {{-- Logo cuando está EXPANDIDO --}}
    <div 
        x-show="$store.sidebar.isOpen" 
        x-transition:enter="lg:transition lg:delay-100"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
    >
        <a href="{{ filament()->getUrl() }}">
            <img 
                alt="{{ filament()->getBrandName() }} logo" 
                src="{{ asset('images/9.png') }}"
                class="fi-logo flex h-10 w-10 rounded-full"
            >
            <span class="fi-custom-brand-name">dataplus</span>
        </a>
    </div>

    {{-- Logo cuando está CONTRAÍDO --}}
    <div 
        x-show="! $store.sidebar.isOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-90"
        x-transition:enter-end="opacity-100 scale-100"
        class="flex justify-center w-full"
    >
        <img 
            alt="Dataplus" 
            src="/images/9.png" 
            class="h-10 w-10 rounded-full shadow-lg"
        >
    </div>

    {{-- Botones de toggle --}}
    <button 
        x-show="! $store.sidebar.isOpen"
        x-data="{}"
        x-on:click="$store.sidebar.open()"
        class="fi-icon-btn relative flex items-center justify-center rounded-lg outline-none transition duration-75 focus-visible:ring-2 -m-1.5 h-9 w-9 text-gray-400 hover:text-gray-500 focus-visible:ring-primary-600 dark:text-gray-500 dark:hover:text-gray-400 dark:focus-visible:ring-primary-500 fi-color-gray mx-auto"
        title="Expandir barra lateral"
        type="button"
    >
        <span class="sr-only">Expandir barra lateral</span>
        <svg class="fi-icon-btn-icon h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"></path>
        </svg>
    </button>
    
    <button 
        x-show="$store.sidebar.isOpen"
        x-data="{}"
        x-on:click="$store.sidebar.close()"
        class="fi-icon-btn relative flex items-center justify-center rounded-lg outline-none transition duration-75 focus-visible:ring-2 -m-1.5 h-9 w-9 text-gray-400 hover:text-gray-500 focus-visible:ring-primary-600 dark:text-gray-500 dark:hover:text-gray-400 dark:focus-visible:ring-primary-500 fi-color-gray ms-auto hidden lg:flex"
        title="Contraer barra lateral"
        type="button"
    >
        <span class="sr-only">Contraer barra lateral</span>
        <svg class="fi-icon-btn-icon h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"></path>
        </svg>
    </button>
</div>
