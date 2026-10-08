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
    } elseif ((str_contains($currentUrl, 'profile') || str_contains($currentUrl, 'edit-profile'))) {
        $section = 'Usuario';
        $pageTitle = 'Mi Perfil';
    }

    $name = $user?->name ?? 'Administrator';
    $role = 'Superusuario DataPlus';
    if ($user && $user->roles && $user->roles->count() > 0) {
        $roleName = $user->roles->first()->name;
        $role = ($roleName === 'Super Admin' || $roleName === 'Administrador') ? 'Superusuario DataPlus' : $roleName;
    }

    $initials = 'DP';
@endphp

<div {{ $attributes->class(['fi-topbar sticky top-0 z-20 overflow-x-clip']) }} style="background: #080e1a; border-bottom: 1px solid rgba(255, 255, 255, 0.08); box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.6);">
    <nav style="height: 70px; display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 0 24px; background: transparent; width: 100%;">
        
        {{-- ─── LADO IZQUIERDO: Breadcrumbs y Título ─── --}}
        <div style="display: flex; align-items: center; gap: 14px; min-width: 0;">
            @if (filament()->hasNavigation())
                <button
                    type="button"
                    x-data="{}"
                    x-on:click="$store.sidebar.isOpen ? $store.sidebar.close() : $store.sidebar.open()"
                    class="lg:hidden"
                    style="padding: 6px; border-radius: 8px; color: #94a3b8; background: transparent; border: 1px solid transparent; cursor: pointer;"
                    title="Menu"
                >
                    <svg style="width: 22px; height: 22px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
            @endif

            <div style="display: flex; flex-direction: column; justify-content: center; min-width: 0;">
                <div style="display: flex; align-items: center; gap: 7px; font-size: 11px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase;">
                    <span style="color: #64748b; font-family: monospace;">DATAPLUS</span>
                    <span style="color: #334155;">/</span>
                    <span style="color: #00e5a3; font-weight: 700;">{{ $section }}</span>
                </div>
                <h1 style="color: #ffffff; font-size: 18px; font-weight: 800; letter-spacing: -0.02em; line-height: 1.2; margin: 3px 0 0; text-shadow: 0 2px 4px rgba(0,0,0,0.4); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    {{ $pageTitle }}
                </h1>
            </div>
        </div>

        {{-- ─── CENTRO: Buscador Global (Buscar clientes, proyectos, ítems... ⌘K) ─── --}}
        <div class="hidden md:flex" style="flex: 1; max-width: 440px; margin: 0 20px; justify-content: center;">
            @if (filament()->isGlobalSearchEnabled())
                <div style="width: 100%;">
                    @livewire(Filament\Livewire\GlobalSearch::class)
                </div>
            @else
                <div style="width: 100%; position: relative; display: flex; align-items: center;">
                    <div style="position: absolute; left: 14px; display: flex; align-items: center; color: #64748b;">
                        <svg style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                    </div>
                    <input
                        type="text"
                        placeholder="Buscar clientes, proyectos, ítems..."
                        readonly
                        x-data="{}"
                        x-on:click="$dispatch('open-global-search')"
                        class="dp-global-search-input"
                        style="width: 100%; height: 38px; background: #0d1726; border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 10px; padding: 0 48px 0 40px; color: #e2e8f0; font-size: 13px; font-weight: 500; outline: none; cursor: pointer;"
                    />
                    <div style="position: absolute; right: 10px; display: flex; align-items: center;">
                        <kbd style="background: #1e293b; border: 1px solid rgba(255, 255, 255, 0.12); color: #64748b; border-radius: 6px; font-size: 11px; font-family: monospace; font-weight: 700; padding: 2px 6px;">⌘K</kbd>
                    </div>
                </div>
            @endif
        </div>

        {{-- ─── LADO DERECHO: Notificaciones y Perfil de Usuario ─── --}}
        <div style="display: flex; align-items: center; gap: 12px; flex-shrink: 0;">
            
            {{-- Botón de Notificaciones --}}
            <div class="relative" x-data="{ openNotifications: false }">
                <button
                    type="button"
                    @click="openNotifications = !openNotifications"
                    class="dp-bell-btn"
                    style="width: 38px; height: 38px; border-radius: 10px; background: #0d1726; border: 1px solid rgba(255, 255, 255, 0.1); display: flex; align-items: center; justify-content: center; color: #94a3b8; cursor: pointer; transition: all 0.2s ease; position: relative;"
                    title="Notificaciones"
                >
                    <svg style="width: 18px; height: 18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                    </svg>
                    <span style="position: absolute; top: 8px; right: 8px; width: 7px; height: 7px; border-radius: 9999px; background: #00e5a3; border: 2px solid #080e1a;"></span>
                </button>

                <div
                    x-show="openNotifications"
                    @click.outside="openNotifications = false"
                    x-cloak
                    style="position: absolute; right: 0; margin-top: 8px; width: 290px; border-radius: 14px; border: 1px solid rgba(255,255,255,0.1); background: #0b1325; padding: 14px; box-shadow: 0 20px 40px rgba(0,0,0,0.7); z-index: 50; display: none;"
                >
                    <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 10px;">
                        <span style="font-size: 13px; font-weight: 700; color: #ffffff;">Notificaciones</span>
                        <span style="font-size: 10px; font-weight: 700; color: #00e5a3; background: rgba(0,229,163,0.1); border: 1px solid rgba(0,229,163,0.3); padding: 2px 8px; border-radius: 9999px; text-transform: uppercase;">Al día</span>
                    </div>
                    <div style="padding: 16px 0; text-align: center; font-size: 12px; color: #64748b;">
                        No hay alertas pendientes en este momento.
                    </div>
                </div>
            </div>

            {{-- Separador vertical sutil --}}
            <div style="height: 26px; width: 1px; background: rgba(255, 255, 255, 0.08); margin: 0 2px;"></div>

            {{-- Perfil del Usuario --}}
            <div class="relative" x-data="{ openUserMenu: false }">
                <button
                    type="button"
                    @click="openUserMenu = !openUserMenu"
                    class="dp-user-btn"
                    style="display: flex; align-items: center; gap: 10px; padding: 4px 6px; border-radius: 10px; background: transparent; border: 1px solid transparent; cursor: pointer; text-align: left; transition: all 0.2s ease;"
                >
                    {{-- Badge Verde Esmeralda con 'DP' --}}
                    <div class="dp-avatar-box" style="width: 38px; height: 38px; border-radius: 9px; background: linear-gradient(135deg, #00e5a3 0%, #059669 100%); color: #022c22; font-weight: 900; font-size: 14px; display: flex; align-items: center; justify-content: center; box-shadow: 0 3px 12px rgba(0, 229, 163, 0.35); flex-shrink: 0; letter-spacing: -0.02em;">
                        {{ $initials }}
                    </div>

                    {{-- Nombre, Icono Verificado y Rol --}}
                    <div style="display: flex; flex-direction: column; justify-content: center; min-width: 0;">
                        <div style="display: flex; align-items: center; gap: 5px;">
                            <span style="font-size: 13px; font-weight: 700; color: #ffffff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 150px;">
                                {{ $name }}
                            </span>
                            {{-- Escudo verificado verde turquesa --}}
                            <svg style="width: 14px; height: 14px; color: #00e5a3; flex-shrink: 0;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                            </svg>
                            {{-- Chevron --}}
                            <svg style="width: 11px; height: 11px; color: #64748b; margin-left: 1px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </div>
                        <span style="font-size: 11px; font-weight: 500; color: #64748b; line-height: 1.1; margin-top: 2px;">
                            {{ $role }}
                        </span>
                    </div>
                </button>

                {{-- Menú Desplegable --}}
                <div
                    x-show="openUserMenu"
                    @click.outside="openUserMenu = false"
                    x-cloak
                    style="position: absolute; right: 0; margin-top: 8px; width: 220px; border-radius: 14px; border: 1px solid rgba(255,255,255,0.1); background: #0b1325; padding: 6px; box-shadow: 0 20px 40px rgba(0,0,0,0.7); z-index: 50; display: none;"
                >
                    <div style="padding: 8px 12px; border-bottom: 1px solid rgba(255,255,255,0.08); margin-bottom: 4px;">
                        <p style="font-size: 10px; color: #64748b; text-transform: uppercase; font-weight: 700; margin: 0;">Usuario Activo</p>
                        <p style="font-size: 12px; font-weight: 700; color: #ffffff; margin: 2px 0 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $user?->email ?? 'admin@dataplus.cu' }}</p>
                    </div>

                    <a href="{{ filament()->getProfileUrl() ?? url('/admin/profile') }}" class="dp-menu-item" style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; color: #94a3b8; text-decoration: none; transition: all 0.15s ease;">
                        <svg style="width: 15px; height: 15px; color: #64748b;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                        <span>Mi Perfil</span>
                    </a>

                    <a href="/admin/proyectos" class="dp-menu-item" style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; color: #94a3b8; text-decoration: none; transition: all 0.15s ease;">
                        <svg style="width: 15px; height: 15px; color: #64748b;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0" />
                        </svg>
                        <span>Proyectos Activos</span>
                    </a>

                    <div style="margin: 4px 0; border-top: 1px solid rgba(255,255,255,0.08);"></div>

                    <form method="POST" action="{{ filament()->getLogoutUrl() }}" style="margin: 0;">
                        @csrf
                        <button type="submit" class="dp-menu-item-logout" style="width: 100%; display: flex; align-items: center; gap: 8px; padding: 8px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; color: #f87171; background: transparent; border: none; cursor: pointer; text-align: left; transition: all 0.15s ease;">
                            <svg style="width: 15px; height: 15px; color: #f87171;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                            </svg>
                            <span>Cerrar Sesión</span>
                        </button>
                    </form>
                </div>
            </div>

        </div>

    </nav>
</div>
