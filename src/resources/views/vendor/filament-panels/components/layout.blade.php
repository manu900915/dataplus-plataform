<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ __('filament-panels::layout.direction') ?? 'ltr' }}">
<head>
    @include('filament::components.layout.head')
</head>
<body class="min-h-screen bg-gray-100 dark:bg-gray-950">
    {{ $slot }}

    {{-- Script para manejar el logo dinámicamente --}}
    <script>
        document.addEventListener('alpine:init', () => {
            setTimeout(() => {
                const sidebarHeader = document.querySelector('.fi-sidebar-header');
                if (!sidebarHeader) return;

                // Agregar el logo circular para cuando está contraído
                const logoContraido = document.createElement('div');
                logoContraido.setAttribute('x-show', '! $store.sidebar.isOpen');
                logoContraido.setAttribute('x-transition:enter', 'transition ease-out duration-300');
                logoContraido.setAttribute('x-transition:enter-start', 'opacity-0 scale-90');
                logoContraido.setAttribute('x-transition:enter-end', 'opacity-100 scale-100');
                logoContraido.className = 'flex justify-center';
                logoContraido.innerHTML = `
                    <img alt="Dataplus" src="/images/9.png" class="h-10 w-10 rounded-full shadow-lg">
                `;

                sidebarHeader.insertBefore(logoContraido, sidebarHeader.children[1]);
            }, 100);
        });
    </script>
</body>
</html>