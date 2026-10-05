<x-filament-panels::page>
    @php
        /** @var array $data */
        $data = $this->getData();

        $tabs = [
            'operaciones'    => ['icon' => 'heroicon-o-wrench-screwdriver', 'label' => 'Operaciones & SLA'],
            'proyectos'      => ['icon' => 'heroicon-o-briefcase',          'label' => 'Proyectos & Solicitudes'],
            'inventario'     => ['icon' => 'heroicon-o-archive-box',        'label' => 'Inventario'],
            'finanzas'       => ['icon' => 'heroicon-o-banknotes',          'label' => 'Finanzas'],
            'administracion' => ['icon' => 'heroicon-o-shield-check',       'label' => 'Administración'],
        ];
    @endphp

    <style>
        [x-cloak] { display: none !important; }

        /* Estilo global y tipografía */
        .dash-root { font-family: 'Plus Jakarta Sans', 'Inter', ui-sans-serif, system-ui, sans-serif; }
        .dash-root .font-display { font-family: 'Plus Jakarta Sans', 'Inter', sans-serif; letter-spacing: -0.02em; }

        /* Hero Banner */
        .dash-hero {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 50%, #0f172a 100%);
            border-radius: 1.25rem;
            position: relative;
            overflow: hidden;
            isolation: isolate;
        }
        .dash-hero::after {
            content: '';
            position: absolute; inset: 0;
            background: radial-gradient(700px 240px at 80% -10%, rgba(255,255,255,.22), transparent 70%);
            pointer-events: none;
            z-index: -1;
        }

        /* Tabs de navegación */
        .dash-tab {
            position: relative;
            flex: 0 0 auto;
            min-height: 2.75rem;
            padding: .7rem 1.15rem;
            transition: all .2s ease;
        }
        .dash-tab[aria-selected="true"] {
            background: linear-gradient(135deg, #0284c7, #0369a1);
            color: #fff;
            box-shadow: 0 8px 20px -6px rgba(2, 132, 199, .5);
        }
        .dark .dash-tab[aria-selected="true"] { color: #fff; }

        /* Tarjetas de estadísticas */
        .dash-panel {
            background: #fff;
            border: 1px solid rgb(229 231 235);
            border-radius: 1rem;
        }
        .dark .dash-panel { background: rgb(17 24 39); border-color: rgb(55 65 81); }

        .dash-row { transition: transform .18s ease, box-shadow .18s ease; }
        .dash-row:hover { transform: translateX(3px); }
    </style>

    <div class="dash-root space-y-6"
         x-data="{
            activeTab: localStorage.getItem('dp-dash-tab') || 'operaciones',
            setTab(t) { this.activeTab = t; localStorage.setItem('dp-dash-tab', t); },
            greeting() {
                const h = new Date().getHours();
                if (h < 12) return 'Buenos días';
                if (h < 19) return 'Buenas tardes';
                return 'Buenas noches';
            }
         }"
         x-init="$watch('activeTab', v => localStorage.setItem('dp-dash-tab', v))">

        {{-- ================= HERO HEADER ================= --}}
        <div class="dash-hero p-6 text-white shadow-xl sm:p-8">
            <div class="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold uppercase tracking-wider backdrop-blur-md">
                        <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Centro de Control Operativo
                    </span>
                    <h1 class="font-display mt-2 text-2xl font-bold tracking-tight sm:text-3xl">
                        <span x-text="greeting()"></span>, {{ auth()->user()->name ?? 'Equipo' }}
                    </h1>
                    <p class="mt-1 max-w-xl text-sm text-sky-100/90">
                        Monitoreo en vivo de tickets técnicos, nivel de servicio (SLA), proyectos y recursos.
                    </p>
                </div>

                {{-- Badges de resumen rápido en Hero --}}
                <div class="flex flex-wrap items-center gap-2.5">
                    @if($data['incidencias_criticas'] > 0)
                        <div class="rounded-xl bg-red-500/25 px-4 py-2.5 ring-1 ring-red-400/40 backdrop-blur-md">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-red-200">¡SLA Crítico / Vencido!</p>
                            <p class="font-display text-xl font-extrabold text-white">{{ $data['incidencias_criticas'] }} averías</p>
                        </div>
                    @endif

                    <div class="rounded-xl bg-white/10 px-4 py-2.5 ring-1 ring-white/20 backdrop-blur-md">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-sky-200">Cumplimiento SLA</p>
                        <p class="font-display text-xl font-extrabold text-emerald-300">{{ $data['porcentaje_sla'] }}%</p>
                    </div>

                    <div class="rounded-xl bg-white/10 px-4 py-2.5 ring-1 ring-white/20 backdrop-blur-md">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-sky-200">Incidencias Activas</p>
                        <p class="font-display text-xl font-extrabold text-white">{{ $data['incidencias_activas'] }}</p>
                    </div>

                    <div class="rounded-xl bg-white/10 px-4 py-2.5 ring-1 ring-white/20 backdrop-blur-md">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-sky-200">Proyectos Activos</p>
                        <p class="font-display text-xl font-extrabold text-white">{{ $data['proyectos_activos'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= TABS NAVIGATION ================= --}}
        <div class="dash-panel min-w-0 p-1.5 shadow-sm">
            <nav class="flex snap-x gap-1.5 overflow-x-auto p-1" role="tablist">
                @foreach($tabs as $key => $tab)
                    <button type="button"
                            role="tab"
                            @click="setTab('{{ $key }}')"
                            :aria-selected="activeTab === '{{ $key }}'"
                            class="dash-tab flex items-center gap-2 whitespace-nowrap rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800">
                        <x-dynamic-component :component="$tab['icon']" class="h-5 w-5" />
                        {{ $tab['label'] }}
                    </button>
                @endforeach
            </nav>
        </div>

        {{-- ================= TAB 1: OPERACIONES & SLA (HELPDESK) ================= --}}
        <div x-show="activeTab === 'operaciones'" x-cloak class="space-y-6">
            {{-- KPIs Operativos --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
                <x-dashboard-stat label="Incidencias Activas" 
                                  :value="$data['incidencias_activas']" 
                                  icon="heroicon-o-wrench-screwdriver" 
                                  color="blue" 
                                  hint="En atención o espera" />

                <x-dashboard-stat label="Críticas / Fuera SLA" 
                                  :value="$data['incidencias_criticas']" 
                                  icon="heroicon-o-exclamation-triangle" 
                                  color="red" 
                                  hint="Atención inmediata requerida" />

                <x-dashboard-stat label="Cumplimiento SLA" 
                                  :value="$data['porcentaje_sla']" 
                                  icon="heroicon-o-check-badge" 
                                  color="green" 
                                  hint="% resuelto a tiempo" />

                <x-dashboard-stat label="Por Revisar (Supervisor)" 
                                  :value="$data['por_revisar_supervisor']" 
                                  icon="heroicon-o-shield-check" 
                                  color="amber" 
                                  hint="Técnicos culminaron" />

                <x-dashboard-stat label="Por Cerrar (Comercial)" 
                                  :value="$data['por_cerrar_comercial']" 
                                  icon="heroicon-o-phone-arrow-up-right" 
                                  color="purple" 
                                  hint="Validar con cliente" />
            </div>

            {{-- Gráficos y Panel de Incidencias Urgentes --}}
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="dash-panel p-5">
                    @livewire(\App\Filament\Widgets\IncidenciasTipoChart::class)
                </div>

                <div class="dash-panel p-5">
                    @livewire(\App\Filament\Widgets\IncidenciasEstadoChart::class)
                </div>

                {{-- Panel de Atención Inmediata --}}
                <div class="dash-panel p-5">
                    <div class="mb-4 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="flex h-2.5 w-2.5 rounded-full bg-red-500 animate-ping"></span>
                            <h3 class="font-display text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-white">
                                Urgencias en Campo
                            </h3>
                        </div>
                        <a href="/admin/incidencias" class="text-xs font-semibold text-sky-600 hover:underline dark:text-sky-400">Ver todas →</a>
                    </div>

                    <div class="space-y-2.5 max-h-[360px] overflow-y-auto pr-1">
                        @forelse($data['incidencias_urgentes'] as $inc)
                            <div class="dash-row rounded-xl border p-3 {{ $inc['esta_vencida'] ? 'border-red-200 bg-red-50/70 dark:border-red-900/50 dark:bg-red-950/20' : 'border-gray-100 bg-gray-50/80 dark:border-gray-800 dark:bg-gray-800/40' }}">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-1.5">
                                            <span class="font-mono text-xs font-bold text-gray-800 dark:text-gray-200">{{ $inc['codigo'] }}</span>
                                            <span class="rounded px-1.5 py-0.5 text-[10px] font-bold {{ $inc['prioridad'] === 'Critica' ? 'bg-red-100 text-red-700 dark:bg-red-900/60 dark:text-red-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/60 dark:text-amber-300' }}">
                                                {{ $inc['prioridad'] }}
                                            </span>
                                        </div>
                                        <p class="truncate text-xs font-semibold text-gray-900 dark:text-white mt-1">{{ $inc['titulo'] }}</p>
                                        <p class="text-[11px] text-gray-500 dark:text-gray-400">Cliente: {{ $inc['cliente'] }}</p>
                                    </div>
                                    <div class="text-right flex-shrink-0">
                                        @if($inc['esta_vencida'])
                                            <span class="rounded-full bg-red-600 px-2 py-0.5 text-[10px] font-bold text-white">
                                                Vencida {{ $inc['horas_vencida'] }}h
                                            </span>
                                        @elseif($inc['horas_restantes'] !== null)
                                            <span class="rounded-full bg-amber-500/20 px-2 py-0.5 text-[10px] font-bold text-amber-700 dark:text-amber-300">
                                                Quedan {{ $inc['horas_restantes'] }}h
                                            </span>
                                        @endif
                                        <p class="text-[10px] text-gray-400 mt-1">Téc: {{ $inc['tecnico'] }}</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="flex flex-col items-center justify-center py-10 text-center">
                                <x-heroicon-o-check-circle class="h-9 w-9 text-emerald-500" />
                                <p class="mt-2 text-xs font-medium text-gray-500 dark:text-gray-400">Sin incidencias urgentes en este momento.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= TAB 2: PROYECTOS & PIPELINE ================= --}}
        <div x-show="activeTab === 'proyectos'" x-cloak class="space-y-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <x-dashboard-stat label="Proyectos en Curso" 
                                  :value="$data['proyectos_activos']" 
                                  icon="heroicon-o-briefcase" 
                                  color="blue" 
                                  hint="Borrador o en ejecución" />

                <x-dashboard-stat label="Solicitudes Pendientes" 
                                  :value="$data['solicitudes_pendientes']" 
                                  icon="heroicon-o-document-plus" 
                                  color="amber" 
                                  hint="Esperando aprobación supervisor" />

                <x-dashboard-stat label="Entregados este Mes" 
                                  :value="$data['proyectos_mes']" 
                                  icon="heroicon-o-check-circle" 
                                  color="green" 
                                  hint="Completados con éxito" />

                <x-dashboard-stat label="Proyectos con Retraso" 
                                  :value="$data['retrasados']" 
                                  icon="heroicon-o-clock" 
                                  color="red" 
                                  hint="Excedieron fecha fin" />
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="dash-panel p-5">
                    @livewire(\App\Filament\Widgets\PipelineComercialChart::class)
                </div>

                {{-- Solicitudes Comerciales Recientes --}}
                <div class="dash-panel p-5">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="font-display text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-white">
                            Solicitudes Comerciales Recientes
                        </h3>
                        <a href="/admin/solicitud-servicios" class="text-xs font-semibold text-sky-600 hover:underline dark:text-sky-400">Ver todas →</a>
                    </div>
                    <div class="space-y-2.5">
                        @forelse($data['solicitudes_recientes'] as $sol)
                            <div class="dash-row flex items-center justify-between rounded-xl bg-gray-50 p-3 dark:bg-gray-800/60">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono text-xs font-bold text-gray-700 dark:text-gray-300">{{ $sol['codigo'] }}</span>
                                        <p class="truncate text-xs font-semibold text-gray-900 dark:text-white">{{ $sol['titulo'] }}</p>
                                    </div>
                                    <p class="text-[11px] text-gray-500 dark:text-gray-400">Cliente: {{ $sol['cliente'] }} • {{ $sol['fecha'] }}</p>
                                </div>
                                <div class="text-right">
                                    @if($sol['presupuesto'] > 0)
                                        <p class="text-xs font-bold text-gray-900 dark:text-white">${{ number_format($sol['presupuesto'], 2) }}</p>
                                    @endif
                                    <span class="rounded px-1.5 py-0.5 text-[10px] font-semibold {{ $sol['estado'] === 'Pendiente_Aprobacion' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300' }}">
                                        {{ $sol['estado'] === 'Pendiente_Aprobacion' ? 'Por Aprobar' : 'Aprobada' }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="py-6 text-center text-xs text-gray-400">No hay solicitudes registradas.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= TAB 3: INVENTARIO ================= --}}
        <div x-show="activeTab === 'inventario'" x-cloak class="space-y-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <x-dashboard-stat label="Total Items"   :value="$data['items']"              icon="heroicon-o-archive-box"          color="blue" />
                <x-dashboard-stat label="Stock Bajo"    :value="$data['stock_bajo']"         icon="heroicon-o-exclamation-triangle" color="red" hint="Requieren reposición" />
                <x-dashboard-stat label="Equipamiento"  :value="$data['equipamiento_stock']" icon="heroicon-o-cube"                 color="purple" hint="Unidades en almacén" />
                <x-dashboard-stat label="Materiales"    :value="$data['materiales_stock']"   icon="heroicon-o-square-3-stack-3d"    color="green" hint="Unidades en almacén" />
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="dash-panel p-5">
                    @livewire(\App\Filament\Widgets\InventarioChart::class)
                </div>

                <div class="dash-panel p-5 lg:col-span-2">
                    <div class="mb-4 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <x-heroicon-s-exclamation-triangle class="h-4 w-4 text-red-500" />
                            <h3 class="font-display text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-white">Alertas de Reposición</h3>
                        </div>
                        <span class="rounded-full bg-red-100 px-2 py-0.5 text-xs font-bold text-red-700 dark:bg-red-900/40 dark:text-red-300">
                            {{ $data['stock_bajo'] }} críticos
                        </span>
                    </div>

                    <div class="space-y-2 max-h-[340px] overflow-y-auto pr-1">
                        @forelse($data['alertas_stock'] as $item)
                            <div class="dash-row flex items-center justify-between rounded-xl border border-red-100 bg-red-50/60 p-3 dark:border-red-900/40 dark:bg-red-950/20">
                                <div class="min-w-0">
                                    <p class="truncate text-xs font-bold text-gray-900 dark:text-white">{{ $item['nombre'] }}</p>
                                    <p class="font-mono text-[11px] text-gray-500 dark:text-gray-400">{{ $item['codigo'] }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs font-extrabold text-red-600 dark:text-red-400">{{ $item['stock_actual'] }} {{ $item['unidad_medida'] }}</p>
                                    <p class="text-[10px] text-gray-400">mínimo: {{ $item['stock_minimo'] }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="py-8 text-center text-xs text-gray-400">
                                ¡Todo el inventario está sobre los niveles mínimos!
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= TAB 4: FINANZAS ================= --}}
        <div x-show="activeTab === 'finanzas'" x-cloak class="space-y-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <x-dashboard-stat label="Presupuesto Total" :value="$data['presupuesto_total']"        icon="heroicon-o-banknotes"     color="blue"   :currency="true" />
                <x-dashboard-stat label="Este Mes"          :value="$data['finanzas_mes']"             icon="heroicon-o-calendar-days" color="green"  :currency="true" hint="Nuevos proyectos" />
                <x-dashboard-stat label="Equipamiento"      :value="$data['presupuesto_equipamiento']" icon="heroicon-o-cube"          color="purple" :currency="true" />
                <x-dashboard-stat label="Mano de Obra"      :value="$data['presupuesto_mano_obra']"    icon="heroicon-o-user-group"    color="amber"  :currency="true" />
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="dash-panel p-5">
                    @livewire(\App\Filament\Widgets\FinanzasChart::class)
                </div>

                <div class="dash-panel p-5">
                    <h3 class="font-display mb-4 text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-white">Top Proyectos por Presupuesto</h3>
                    <div class="space-y-3.5">
                        @foreach($data['top_proyectos'] as $i => $proyecto)
                            @php $pct = round(($proyecto['presupuesto_total'] / $data['max_presupuesto']) * 100); @endphp
                            <div>
                                <div class="mb-1 flex items-baseline justify-between text-xs font-semibold">
                                    <span class="truncate text-gray-800 dark:text-gray-200">{{ $i + 1 }}. {{ $proyecto['nombre'] }}</span>
                                    <span class="text-sky-600 dark:text-sky-400 font-bold">${{ number_format($proyecto['presupuesto_total'], 2) }}</span>
                                </div>
                                <div class="h-2 w-full overflow-hidden rounded-full bg-gray-100 dark:bg-gray-800">
                                    <div class="h-full rounded-full bg-gradient-to-r from-sky-400 to-blue-600" style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= TAB 5: ADMINISTRACIÓN ================= --}}
        <div x-show="activeTab === 'administracion'" x-cloak class="space-y-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <x-dashboard-stat label="Usuarios Activos" :value="$data['usuarios']"         icon="heroicon-o-users"             color="blue"   hint="Con acceso al sistema" />
                <x-dashboard-stat label="Total Clientes"   :value="$data['clientes']"         icon="heroicon-o-building-office-2" color="green"  hint="Empresas registradas" />
                <x-dashboard-stat label="Clientes Activos" :value="$data['clientes_activos']" icon="heroicon-o-hand-thumb-up"    color="purple" hint="Con proyectos en curso" />
            </div>

            @livewire(\App\Filament\Widgets\AdministracionStats::class)
        </div>
    </div>
</x-filament-panels::page>
