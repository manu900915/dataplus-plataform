<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ __('filament-panels::layout.direction') ?? 'ltr' }}">
<head>
    @include('filament::components.layout.head')
</head>
<body class="min-h-screen bg-gray-100 dark:bg-gray-950">
    <div class="flex min-h-screen">
        {{-- Sidebar personalizado --}}
        @livewire(\Filament\Livewire\Sidebar::class)
        
        {{-- Main content --}}
        <div 
            x-data="{ sidebarOpen: ! $store.sidebar.isOpen }"
            x-init="() => {
                const stored = localStorage.getItem('sidebarOpen');
                if (stored !== null) {
                    $store.sidebar.isOpen = stored === 'true';
                }
            }"
            class="flex-1 transition-all duration-300 ease-in-out"
            :class="sidebarOpen ? 'ml-64' : 'ml-20'"
        >
            {{ $slot }}
        </div>
    </div>
</body>
</html>