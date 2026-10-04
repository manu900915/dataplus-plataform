@props([
    'navigation' => [],
])

@php
    $user = filament()->auth()->user();
    $currentUrl = request()->path();

    // Determinar sección y título dinámico según la página activa
    $section = 'Dashboard';
    $pageTitle = 'Panel de Control Principal';

    if (str_contains($currentUrl, 'clientes')) {
        $section = 'Administración';
        $pageTitle = 'Gestión de Clientes';
    } elseif (str_contains($currentUrl, 'proyectos') || str_contains($currentUrl, 'seguimiento-proyectos')) {
        $section = 'Operaciones';
        $pageTitle = 'Control de Proyectos';
    } elseif (str_contains($currentUrl, 'servicios')) {
        $section = 'Operaciones';
        $pageTitle = 'Servicios Técnicos';
    } elseif (str_contains($currentUrl, 'items') || str_contains($currentUrl, 'inventario') || str_contains($currentUrl, 'almacenes')) {
        $section = 'Inventario';
        $pageTitle = 'Gestión de Almacenes e Ítems';
    } elseif (str_contains($currentUrl, 'users')) {
        $section = 'Administración';
        $pageTitle = 'Gestión de Usuarios y Roles';
    } elseif (str_contains($currentUrl, 'brigadas')) {
        $section = 'Operaciones';
        $pageTitle = 'Brigadas Técnicas';
    } elseif (str_contains($currentUrl, 'edit-profile')) {
        $section = 'Usuario';
        $pageTitle = 'Mi Perfil';
    }

    // Datos del usuario logueado o valores ejecutivos de DataPlus
    $name = $user?->name ?? 'Admin Operaciones';
    $role = 'Superusuario DataPlus';
    if ($user && $user->roles && $user->roles->count() > 0) {
        $roleName = $user->roles->first()->name;
        $role = ($roleName === 'Super Admin' || $roleName === 'Administrador') ? 'Superusuario DataPlus' : $roleName;
    }
    
    // Iniciales en el badge verde
    $initials = 'DP';
@endphp

<div {{ $attributes->class(['fi-topbar sticky top-0 z-20 overflow-x-clip']) }}>
    <nav class="flex h-20 items-center justify-between gap-x-4 bg-[#080e1a] px-4 border-b border-white/10 md:px-6 lg:px-8 shadow-lg shadow-black/50">
        
        {{-- ─── LADO IZQUIERDO: Breadcrumbs y Título de Página ─── --}}
        <div class="flex items-center gap-x-4 min-w-0">
            @if (filament()->hasNavigation())
                <button
                    type="button"
                    x-data="{}"
                    x-on:click="$store.sidebar.isOpen ? $store.sidebar.close() : $store.sidebar.open()"
                    class="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-white/5 border border-transparent hover:border-slate-800 transition lg:hidden"
                    title="Menu"
                >
                    <x-heroicon-o-bars-3 class="w-6 h-6" />
                </button>
            @endif

            <div class="flex flex-col justify-center min-w-0">
                <div class="flex items-center gap-2 text-[11px] font-bold tracking-wider">
                    <span class="text-slate-400 uppercase font-mono">DATAPLUS</span>
                    <span class="text-slate-600 font-bold">/</span>
                    <span class="text-[#00e5a3] font-semibold">{{ $section }}</span>
                </div>
                <h1 class="text-lg sm:text-xl font-bold tracking-tight text-white font-display truncate drop-shadow-sm leading-tight mt-0.5">
                    {{ $pageTitle }}
                </h1>
            </div>
        </div>

        {{-- ─── CENTRO: Buscador Global (Buscar clientes, proyectos, ítems... ⌘K) ─── --}}
        <div class="hidden md:flex flex-1 max-w-lg mx-6 justify-center">
            @if (filament()->isGlobalSearchEnabled())
                <div class="w-full relative">
                    @livewire(Filament\Livewire\GlobalSearch::class)
                </div>
            @else
                <button
                    type="button"
                    x-data="{}"
                    x-on:click="$dispatch('open-global-search')"
                    class="w-full flex items-center justify-between gap-3 px-4 py-2.5 rounded-xl border border-slate-700/60 bg-[#0d1726]/90 text-sm text-slate-400 hover:border-slate-600 hover:bg-[#111e33] hover:text-slate-200 transition shadow-inner"
                >
                    <div class="flex items-center gap-2.5">
                        <x-heroicon-o-magnifying-glass class="w-4 h-4 text-slate-400" />
                        <span class="truncate">Buscar clientes, proyectos, ítems...</span>
                    </div>
                    <kbd class="px-2 py-0.5 text-[11px] font-mono font-bold text-slate-400 bg-slate-800/90 border border-slate-700/70 rounded-md">⌘K</kbd>
                </button>
            @endif
        </div>

        {{-- ─── LADO DERECHO: Notificaciones y Perfil de Usuario ─── --}}
        <div class="flex items-center gap-x-3 sm:gap-x-4 flex-shrink-0">
            
            {{-- Botón de Notificaciones --}}
            <div class="relative" x-data="{ openNotifications: false }">
                <button
                    type="button"
                    @click="openNotifications = !openNotifications"
                    class="relative p-2.5 rounded-xl border border-slate-700/60 bg-[#0d1726]/80 hover:bg-[#111e33] hover:border-slate-600 text-slate-300 hover:text-white transition shadow-sm"
                    title="Notificaciones"
                >
                    <x-heroicon-o-bell class="w-5 h-5" />
                    <span class="absolute top-2 right-2 w-2 h-2 rounded-full bg-[#00e5a3] ring-2 ring-[#080e1a]"></span>
                </button>

                <div
                    x-show="openNotifications"
                    @click.outside="openNotifications = false"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    class="absolute right-0 mt-3 w-80 rounded-2xl border border-slate-700/80 bg-[#0b1325] p-4 shadow-2xl z-50 text-slate-200"
                    style="display: none;"
                >
                    <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                        <span class="text-sm font-bold text-white font-display">Notificaciones</span>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-[#00e5a3] bg-emerald-950/60 border border-emerald-800/40 px-2 py-0.5 rounded-full">Al día</span>
                    </div>
                    <div class="py-4 text-xs text-slate-400 text-center">
                        <x-heroicon-o-check-circle class="w-8 h-8 text-[#00e5a3] mx-auto mb-2 opacity-80" />
                        No tienes alertas críticas pendientes.
                    </div>
                </div>
            </div>

            {{-- Separador vertical sutil --}}
            <div class="h-8 w-px bg-slate-800 hidden sm:block"></div>

            {{-- Perfil del Usuario --}}
            <div class="relative" x-data="{ openUserMenu: false }">
                <button
                    type="button"
                    @click="openUserMenu = !openUserMenu"
                    class="flex items-center gap-3 p-1.5 pr-2 rounded-xl hover:bg-white/5 transition border border-transparent hover:border-slate-800 text-left"
                >
                    {{-- Badge Verde Esmeralda con 'DP' --}}
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#00e5a3] to-[#059669] text-emerald-950 font-black flex items-center justify-center text-sm shadow-md shadow-emerald-500/20 flex-shrink-0 tracking-tight">
                        {{ $initials }}
                    </div>

                    {{-- Nombre, Icono Verificado y Rol --}}
                    <div class="hidden sm:block min-w-0">
                        <div class="flex items-center gap-1.5">
                            <span class="text-sm font-bold text-white truncate max-w-[140px] lg:max-w-[180px]">{{ $name }}</span>
                            {{-- Escudo / Check verificado en verde turquesa --}}
                            <svg class="w-4 h-4 text-[#00e5a3] flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                            </svg>
                        </div>
                        <p class="text-xs text-slate-400 font-medium truncate leading-tight">{{ $role }}</p>
                    </div>

                    <x-heroicon-m-chevron-down class="w-4 h-4 text-slate-400 hidden sm:block ml-0.5" />
                </button>

                {{-- Menú Desplegable --}}
                <div
                    x-show="openUserMenu"
                    @click.outside="openUserMenu = false"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    class="absolute right-0 mt-3 w-56 rounded-2xl border border-slate-700/80 bg-[#0b1325] p-2 shadow-2xl z-50 text-slate-200"
                    style="display: none;"
                >
                    <div class="px-3 py-2 border-b border-slate-800 mb-1">
                        <p class="text-[11px] text-slate-400">Usuario activo</p>
                        <p class="text-xs font-bold text-white truncate">{{ $user?->email ?? 'admin@dataplus.cu' }}</p>
                    </div>

                    <a href="/admin/edit-profile" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-slate-300 hover:text-white hover:bg-white/5 transition">
                        <x-heroicon-o-user class="w-4 h-4 text-slate-400" />
                        <span>Mi Perfil</span>
                    </a>

                    <a href="/admin/proyectos" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-slate-300 hover:text-white hover:bg-white/5 transition">
                        <x-heroicon-o-briefcase class="w-4 h-4 text-slate-400" />
                        <span>Proyectos Activos</span>
                    </a>

                    <div class="my-1 border-t border-slate-800"></div>

                    <form method="POST" action="{{ filament()->getLogoutUrl() }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-red-400 hover:text-red-300 hover:bg-red-500/10 transition text-left">
                            <x-heroicon-o-arrow-left-on-rectangle class="w-4 h-4 text-red-400" />
                            <span>Cerrar Sesión</span>
                        </button>
                    </form>
                </div>
            </div>

        </div>

    </nav>
</div>
