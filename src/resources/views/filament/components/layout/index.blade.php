<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ __('filament-panels::layout.direction') ?? 'ltr' }}">
<head>
    @include('filament::components.layout.head')
    <style>
        /* Prevenir selección de texto y cursor de escritura/caret en el menú lateral */
        .fi-sidebar,
        .fi-sidebar *,
        .fi-sidebar-nav,
        .fi-sidebar-nav *,
        .fi-sidebar-header,
        .fi-sidebar-header *,
        .fi-sidebar-item,
        .fi-sidebar-item *,
        .fi-sidebar-item-button,
        .fi-sidebar-item-button *,
        .fi-sidebar-item-label,
        .fi-sidebar-group-label,
        .fi-sidebar-group-button {
            -webkit-user-select: none !important;
            -moz-user-select: none !important;
            -ms-user-select: none !important;
            user-select: none !important;
            caret-color: transparent !important;
            -webkit-user-modify: read-only !important;
        }

        .fi-sidebar-item-button,
        .fi-sidebar-nav a,
        .fi-sidebar-nav button,
        .fi-sidebar-group-button {
            cursor: pointer !important;
            caret-color: transparent !important;
            outline: none !important;
            -webkit-tap-highlight-color: transparent !important;
        }

        .fi-sidebar-item-button *,
        .fi-sidebar-nav a *,
        .fi-sidebar-nav button * {
            cursor: pointer !important;
            caret-color: transparent !important;
        }

        .fi-sidebar-item-label,
        .fi-sidebar-group-label,
        .fi-sidebar-nav span {
            pointer-events: none !important;
            caret-color: transparent !important;
            user-select: none !important;
            -webkit-user-select: none !important;
        }

        .fi-sidebar-nav a:focus,
        .fi-sidebar-nav a:focus-visible,
        .fi-sidebar-nav button:focus,
        .fi-sidebar-nav button:focus-visible,
        .fi-sidebar-item-button:focus,
        .fi-sidebar-item-button:focus-visible {
            outline: none !important;
            box-shadow: none !important;
            caret-color: transparent !important;
        }
    </style>
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
    <script>
        document.addEventListener('mousedown', function (e) {
            const item = e.target.closest('.fi-sidebar-nav a, .fi-sidebar-nav button, .fi-sidebar-item-button, .fi-sidebar-group-button, .fi-sidebar a');
            if (item) {
                if (window.getSelection) {
                    window.getSelection().removeAllRanges();
                }
            }
        }, true);

        document.addEventListener('click', function (e) {
            const item = e.target.closest('.fi-sidebar-nav a, .fi-sidebar-nav button, .fi-sidebar-item-button, .fi-sidebar-group-button, .fi-sidebar a');
            if (item) {
                if (window.getSelection) {
                    window.getSelection().removeAllRanges();
                }
                item.blur();
            }
        }, true);
    </script>
</body>
</html>
