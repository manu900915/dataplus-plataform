<x-filament-panels::page>
    @php
        /** @var array $data */
        $data = $this->getData();

        $tabs = [
            'proyectos'      => ['icon' => 'heroicon-o-briefcase',       'label' => 'Proyectos'],
            'operaciones'    => ['icon' => 'heroicon-o-cpu-chip',        'label' => 'Operaciones'],
            'inventario'     => ['icon' => 'heroicon-o-archive-box',     'label' => 'Inventario'],
            'finanzas'       => ['icon' => 'heroicon-o-banknotes',       'label' => 'Finanzas'],
            'administracion' => ['icon' => 'heroicon-o-shield-check',    'label' => 'Administración'],
        ];
    @endphp

    <style>
        [x-cloak] { display: none !important; }

        /* ===== Tipografía y estilo global del dashboard ===== */
        .dash-root { font-family: 'Plus Jakarta Sans', 'Inter', ui-sans-serif, system-ui, sans-serif; }
        .dash-root .font-display { font-family: 'Plus Jakarta Sans', 'Inter', sans-serif; letter-spacing: -0.02em; }

        /* Encabezado con saludo */
        .dash-hero {
            background: linear-gradient(135deg, #0ea5e9 0%, #0369a1 55%, #0c4a6e 100%);
            border-radius: 1.25rem;
            position: relative;
            overflow: hidden;
        }
        .dash-hero::after {
            content: '';
            position: absolute; inset: 0;
            background: radial-gradient(600px 200px at 85% -20%, rgba(255,255,255,.25), transparent 60%);
            pointer-events: none;
        }

        /* Tabs */
        .dash-tab {
            position: relative;
            transition: color .25s ease, background-color .25s ease, transform .2s ease;
        }
        .dash-tab[aria-selected="true"] {
            background: linear-gradient(135deg, #0ea5e9, #0369a1);
            color: #fff;
            box-shadow: 0 8px 20px -8px rgba(14, 165, 233, .55);
        }
        .dark .dash-tab[aria-selected="true"] { color: #fff; }

        /* Paneles de contenido */
        .dash-panel {
            background: #fff;
            border: 1px solid rgb(229 231 235);
            border-radius: 1rem;
        }
        .dark .dash-panel { background: rgb(17 24 39); border-color: rgb(55 65 81); }

        /* Transición entre pestañas */
        .tab-leave-active { transition: opacity .15s ease, transform .15s ease; }
        .tab-enter-active  { transition: opacity .35s ease .05s, transform .35s ease .05s; }
        .tab-enter-from, .tab-leave-to { opacity: 0; transform: translateY(8px); }

        /* Barras de progreso animadas */
        .dash-bar { transition: width 1s cubic-bezier(.22,1,.36,1); }

        /* Tarjetas de lista interactivas */
        .dash-row { transition: transform .2s ease, box-shadow .2s ease; }
        .dash-row:hover { transform: translateX(4px); }
    </style>

    <div class="dash-root space-y-6"
         x-data="{
            activeTab: localStorage.getItem('dp-dash-tab') || 'proyectos',
            setTab(t) { this.activeTab = t; localStorage.setItem('dp-dash-tab', t); },
            greeting() {
                const h = new Date().getHours();
                if (h < 12) return 'Buenos días';
                if (h < 19) return 'Buenas tardes';
                return 'Buenas noches';
            }
         }"
         x-init="$watch('activeTab', v => localStorage.setItem('dp-dash-tab', v))">

        {{-- ================= HERO / ENCABEZADO ================= --}}
        <div class="dash-hero p-6 sm:p-8 text-white">
            <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-medium text-sky-100/80" x-text="greeting() + ','"></p>
                    <h1 class="font-display mt-1 text-2xl sm:text-3xl font-extrabold tracking-tight">
                        {{ auth()->user()->name ?? 'Usuario' }}
                    </h1>
                    <p class="mt-2 text-sm text-sky-100/70">
                        {{ now()->isoFormat('dddd, D [de] MMMM [de] YYYY') }} ·
                        {{ $data['proyectos_activos'] }} proyectos activos ·
                        {{ $data['stock_bajo'] > 0 ? $data['stock_bajo'] . ' alertas de stock' : 'sin alertas de stock' }}
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    @if($data['retrasados'] > 0)
                        <div class="rounded-xl bg-white/10 px-4 py-3 ring-1 ring-white/20 backdrop-blur-sm">
                            <p class="text-[11px] uppercase tracking-wider text-white/70">Con retraso</p>
                            <p class="font-display text-2xl font-bold text-amber-300">{{ $data['retrasados'] }}</p>
                        </div>
                    @endif
                    <div class="rounded-xl bg-white/10 px-4 py-3 ring-1 ring-white/20 backdrop-blur-sm">
                        <p class="text-[11px] uppercase tracking-wider text-white/70">Presupuesto total</p>
                        <p class="font-display text-2xl font-bold">${{ number_format($data['presupuesto_total'], 0) }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= TABS ================= --}}
        <div class="dash-panel p-2">
            <nav class="flex gap-1 overflow-x-auto" role="tablist" aria-label="Secciones del dashboard">
                @foreach($tabs as $key => $tab)
                    <button type="button"
                            role="tab"
                            @click="setTab('{{ $key }}')"
                            :aria-selected="activeTab === '{{ $key }}'"
                            class="dash-tab flex items-center gap-2 whitespace-nowrap rounded-xl px-5 py-2.5 text-sm font-semibold text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800">
                        <x-dynamic-component :component="$tab['icon']" class="h-5 w-5" />
                        {{ $tab['label'] }}
                    </button>
                @endforeach
            </nav>
        </div>

        {{-- ================= CONTENIDO ================= --}}

        {{-- ---------- PROYECTOS ---------- --}}
        <div x-show="activeTab === 'proyectos'" x-cloak
             x-transition:enter="tab-enter-active" x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:leave="tab-leave-active" x-transition:leave-end="opacity-0"
             class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            <x-dashboard-stat label="Proyectos Activos"   :value="$data['proyectos_activos']" icon="heroicon-o-briefcase"           color="blue"    hint="En borrador o ejecución" />
            <x-dashboard-stat label="Completados este Mes" :value="$data['proyectos_mes']"     icon="heroicon-o-check-circle"        color="green"   hint="Cierre satisfactorio" />
            <x-dashboard-stat label="Con Retraso"          :value="$data['retrasados']"        icon="heroicon-o-exclamation-triangle" color="red"    hint="Pasaron su fecha fin" />
            <x-dashboard-stat label="Presupuesto Total"    :value="$data['presupuesto_total']" icon="heroicon-o-banknotes"           color="amber"   :currency="true" hint="Suma de todos los proyectos" />
        </div>
        <div x-show="activeTab === 'proyectos'" x-cloak class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @livewire(\App\Filament\Widgets\ProyectosChart::class)
            @livewire(\App\Filament\Widgets\FinanzasChart::class)
        </div>

        {{-- ---------- OPERACIONES ---------- --}}
        <div x-show="activeTab === 'operaciones'" x-cloak
             x-transition:enter="tab-enter-active" x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:leave="tab-leave-active" x-transition:leave-end="opacity-0"
             class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <x-dashboard-stat label="Brigadas Activas" :value="$data['brigadas']"      icon="heroicon-o-user-group"        color="purple" hint="Disponibles para asignación" :columns="3" />
            <x-dashboard-stat label="Proyectos I+D"    :value="$data['investigacion']" icon="heroicon-o-light-bulb"        color="indigo" hint="En curso" :columns="3" />
            <x-dashboard-stat label="Instalaciones"    :value="$data['instalaciones']" icon="heroicon-o-wrench-screwdriver" color="emerald" hint="En curso" :columns="3" />
        </div>
        <div x-show="activeTab === 'operaciones'" x-cloak class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @livewire(\App\Filament\Widgets\OperacionesChart::class)
            <div class="dash-panel p-6">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="font-display text-base font-bold text-gray-900 dark:text-white">Actividad Reciente</h3>
                    <span class="rounded-full bg-sky-100 px-2.5 py-0.5 text-xs font-semibold text-sky-700 dark:bg-sky-900/40 dark:text-sky-300">últimos proyectos</span>
                </div>
                <div class="space-y-2.5">
                    @foreach($data['recientes'] as $proyecto)
                        <div class="dash-row flex items-center gap-3 rounded-xl bg-gray-50 p-3 dark:bg-gray-800/70">
                            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-gradient-to-br from-sky-500 to-blue-700 text-white shadow-sm">
                                <x-heroicon-m-briefcase class="h-5 w-5" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-gray-900 dark:text-white">{{ $proyecto->nombre }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $proyecto->created_at->diffForHumans() }}</p>
                            </div>
                            <x-filament::badge size="xs" :color="match($proyecto->estado) { 'completado' => 'success', 'en_progreso' => 'info', 'cancelado' => 'danger', default => 'gray' }">
                                {{ ucfirst(str_replace('_', ' ', $proyecto->estado)) }}
                            </x-filament::badge>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ---------- INVENTARIO ---------- --}}
        <div x-show="activeTab === 'inventario'" x-cloak
             x-transition:enter="tab-enter-active" x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:leave="tab-leave-active" x-transition:leave-end="opacity-0"
             class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            <x-dashboard-stat label="Total Items"   :value="$data['items']"              icon="heroicon-o-archive-box"          color="blue" />
            <x-dashboard-stat label="Stock Bajo"    :value="$data['stock_bajo']"         icon="heroicon-o-exclamation-triangle" color="red" hint="Requieren reposición" />
            <x-dashboard-stat label="Equipamiento"  :value="$data['equipamiento_stock']" icon="heroicon-o-cube"                 color="purple" hint="Unidades en almacén" />
            <x-dashboard-stat label="Materiales"    :value="$data['materiales_stock']"   icon="heroicon-o-square-3-stack-3d"    color="green" hint="Unidades en almacén" />
        </div>
        <div x-show="activeTab === 'inventario'" x-cloak class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            @livewire(\App\Filament\Widgets\InventarioChart::class)
            <div class="dash-panel p-6 lg:col-span-2">
                <div class="mb-4 flex items-center gap-2">
                    <x-heroicon-s-exclamation-triangle class="h-5 w-5 text-red-500" />
                    <h3 class="font-display text-base font-bold text-gray-900 dark:text-white">Alertas de Stock Bajo</h3>
                    <span class="ml-auto rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-bold text-red-700 dark:bg-red-900/40 dark:text-red-300">{{ $data['stock_bajo'] }}</span>
                </div>
                <div class="max-h-96 space-y-2 overflow-y-auto pr-1">
                    @forelse($data['alertas_stock'] as $item)
                        <div class="dash-row flex items-center justify-between rounded-xl border border-red-100 bg-red-50/60 p-3 dark:border-red-900/40 dark:bg-red-950/30">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-gray-900 dark:text-white">{{ $item->nombre }}</p>
                                <p class="font-mono text-xs text-gray-500 dark:text-gray-400">{{ $item->codigo }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-red-600 dark:text-red-400">{{ $item->stock_actual }} {{ $item->unidad_medida }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">mín: {{ $item->stock_minimo }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-10 text-center">
                            <x-heroicon-o-check-circle class="h-10 w-10 text-emerald-400" />
                            <p class="mt-2 text-sm font-medium text-gray-500 dark:text-gray-400">No hay alertas de stock. ¡Todo en orden!</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- ---------- FINANZAS ---------- --}}
        <div x-show="activeTab === 'finanzas'" x-cloak
             x-transition:enter="tab-enter-active" x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:leave="tab-leave-active" x-transition:leave-end="opacity-0"
             class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            <x-dashboard-stat label="Presupuesto Total" :value="$data['presupuesto_total']"          icon="heroicon-o-banknotes"     color="blue"  :currency="true" />
            <x-dashboard-stat label="Este Mes"          :value="$data['finanzas_mes']"               icon="heroicon-o-calendar-days"  color="green" :currency="true" hint="Nuevos proyectos del mes" />
            <x-dashboard-stat label="Equipamiento"      :value="$data['presupuesto_equipamiento']"   icon="heroicon-o-cube"           color="purple" :currency="true" />
            <x-dashboard-stat label="Mano de Obra"      :value="$data['presupuesto_mano_obra']"      icon="heroicon-o-user-group"     color="amber"  :currency="true" />
        </div>
        <div x-show="activeTab === 'finanzas'" x-cloak class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @livewire(\App\Filament\Widgets\FinanzasChart::class)
            <div class="dash-panel p-6">
                <h3 class="font-display mb-5 text-base font-bold text-gray-900 dark:text-white">Top 5 Proyectos por Presupuesto</h3>
                <div class="space-y-4">
                    @foreach($data['top_proyectos'] as $i => $proyecto)
                        @php $pct = round(($proyecto->presupuesto_total / $data['max_presupuesto']) * 100); @endphp
                        <div>
                            <div class="mb-1.5 flex items-baseline justify-between gap-3">
                                <span class="flex min-w-0 items-center gap-2 text-sm font-semibold text-gray-900 dark:text-white">
                                    <span class="flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-md bg-sky-100 text-[10px] font-bold text-sky-700 dark:bg-sky-900/50 dark:text-sky-300">{{ $i + 1 }}</span>
                                    <span class="truncate">{{ $proyecto->nombre }}</span>
                                </span>
                                <span class="font-display flex-shrink-0 text-sm font-bold text-sky-600 dark:text-sky-400">${{ number_format($proyecto->presupuesto_total, 2) }}</span>
                            </div>
                            <div class="h-2 w-full overflow-hidden rounded-full bg-gray-100 dark:bg-gray-800">
                                <div class="dash-bar h-full rounded-full bg-gradient-to-r from-sky-400 via-blue-500 to-blue-700"
                                     style="width: 0%"
                                     x-init="requestAnimationFrame(() => setTimeout(() => $el.style.width = '{{ $pct }}%', 100))"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ---------- ADMINISTRACIÓN ---------- --}}
        <div x-show="activeTab === 'administracion'" x-cloak
             x-transition:enter="tab-enter-active" x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:leave="tab-leave-active" x-transition:leave-end="opacity-0"
             class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <x-dashboard-stat label="Usuarios Activos" :value="$data['usuarios']"        icon="heroicon-o-users"               color="blue"   hint="Con acceso al sistema" :columns="3" />
            <x-dashboard-stat label="Total Clientes"   :value="$data['clientes']"        icon="heroicon-o-building-office-2"   color="green"  hint="Registrados en la plataforma" :columns="3" />
            <x-dashboard-stat label="Clientes Activos" :value="$data['clientes_activos']" icon="heroicon-o-hand-thumb-up"      color="purple" hint="Con proyectos vigentes" :columns="3" />
        </div>
        <div x-show="activeTab === 'administracion'" x-cloak>
            @livewire(\App\Filament\Widgets\AdministracionStats::class)
        </div>
    </div>
</x-filament-panels::page>
