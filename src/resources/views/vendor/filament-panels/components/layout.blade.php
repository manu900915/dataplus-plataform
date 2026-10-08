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
    {{ $slot }}

    {{-- Script para manejar el logo dinámicamente y prevenir cursor de texto en menú lateral --}}
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

        // Prevenir que quede cursor de texto o foco parpadeante al hacer clic en los incisos del menú lateral
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
