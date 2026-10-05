<x-filament-panels::page>
    @php
        /** @var array $data */
        $data = $this->getData();

        $tabs = [
            'proyectos'      => ['label' => 'Proyectos',      'icon' => 'heroicon-o-briefcase'],
            'operaciones'    => ['label' => 'Operaciones',    'icon' => 'heroicon-o-cpu-chip'],
            'inventario'     => ['label' => 'Inventario',     'icon' => 'heroicon-o-archive-box'],
            'finanzas'       => ['label' => 'Finanzas',       'icon' => 'heroicon-o-banknotes'],
            'administracion' => ['label' => 'Administración', 'icon' => 'heroicon-o-shield-check'],
        ];
    @endphp

    <style>
        [x-cloak] { display: none !important; }
    </style>

    <div class="space-y-6 select-none font-sans text-slate-100"
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

        {{-- ================= HERO / ENCABEZADO OFICIAL (ESTILO PREVIEW) ================= --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-sky-500 via-sky-600 to-sky-800 p-6 sm:p-8 text-white shadow-xl shadow-sky-950/20">
            <div class="relative z-10 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-sky-100/80">
                        <span x-text="greeting()"></span>,
                    </p>
                    <h1 class="mt-1 text-2xl sm:text-3xl font-extrabold tracking-tight text-white">
                        {{ auth()->user()->name ?? 'Enmanuel Caraballo' }}
                    </h1>
                    <p class="mt-2 text-xs sm:text-sm text-sky-100/80 font-medium">
                        DataPlus Platform · {{ $data['proyectos_activos'] }} proyectos activos · 
                        {{ $data['stock_bajo_count'] > 0 ? $data['stock_bajo_count'] . ' alertas de stock bajo' : 'sin alertas de stock' }} · 
                        {{ $data['servicios_total'] }} servicios en campo
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    @if($data['retrasados'] > 0)
                        <div class="rounded-xl bg-white/10 px-4 py-2.5 ring-1 ring-white/20 backdrop-blur-sm">
                            <p class="text-[10px] uppercase font-bold tracking-wider text-amber-200">Con retraso</p>
                            <p class="text-xl font-extrabold text-amber-300">{{ $data['retrasados'] }}</p>
                        </div>
                    @endif
                    <div class="rounded-xl bg-white/10 px-4 py-2.5 ring-1 ring-white/20 backdrop-blur-sm">
                        <p class="text-[10px] uppercase font-bold tracking-wider text-sky-100">Presupuesto total</p>
                        <p class="text-xl font-extrabold text-white">${{ number_format($data['presupuesto_total'], 2) }} CUP</p>
                    </div>
                </div>
            </div>
            {{-- Ambient glow decoration --}}
            <div class="absolute -right-12 -bottom-12 h-48 w-48 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
        </div>

        {{-- ================= TABS DE SECCIONES (ESTILO PREVIEW) ================= --}}
        <div class="rounded-xl border border-slate-800 bg-slate-900/80 p-1.5 shadow-sm">
            <nav class="flex snap-x gap-1.5 overflow-x-auto" role="tablist">
                @foreach($tabs as $key => $tab)
                    <button type="button"
                            role="tab"
                            @click="setTab('{{ $key }}')"
                            :class="activeTab === '{{ $key }}' ? 'bg-gradient-to-r from-sky-500 to-sky-600 text-white shadow-md shadow-sky-500/30' : 'text-slate-400 hover:bg-slate-800/80 hover:text-slate-200'"
                            class="flex items-center gap-2 whitespace-nowrap rounded-lg px-4 py-2 text-xs font-bold transition-all cursor-pointer">
                        <x-dynamic-component :component="$tab['icon']" class="h-4 w-4" />
                        <span>{{ $tab['label'] }}</span>
                    </button>
                @endforeach
            </nav>
        </div>

        {{-- ================= CONTENIDO DE CADA TAB ================= --}}

        {{-- ---------- TAB: PROYECTOS ---------- --}}
        <div x-show="activeTab === 'proyectos'" x-cloak class="space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-400">Proyectos Activos</span>
                        <div class="rounded-lg bg-sky-500/10 p-2 text-sky-400 border border-sky-500/20">
                            <x-heroicon-o-briefcase class="h-4 w-4" />
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-white mt-2">{{ $data['proyectos_activos'] }}</p>
                    <p class="text-[11px] text-slate-400 mt-1">En borrador o ejecución activa</p>
                </div>

                <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-400">Completados este Mes</span>
                        <div class="rounded-lg bg-emerald-500/10 p-2 text-emerald-400 border border-emerald-500/20">
                            <x-heroicon-o-check-circle class="h-4 w-4" />
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-emerald-400 mt-2">{{ $data['proyectos_completados'] }}</p>
                    <p class="text-[11px] text-slate-400 mt-1">Cierre satisfactorio</p>
                </div>

                <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-400">Con Retraso</span>
                        <div class="rounded-lg bg-rose-500/10 p-2 text-rose-400 border border-rose-500/20">
                            <x-heroicon-o-exclamation-triangle class="h-4 w-4" />
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-rose-400 mt-2">{{ $data['retrasados'] }}</p>
                    <p class="text-[11px] text-slate-400 mt-1">Pasaron su fecha comprometida</p>
                </div>

                <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-400">Presupuesto Total</span>
                        <div class="rounded-lg bg-amber-500/10 p-2 text-amber-400 border border-amber-500/20">
                            <x-heroicon-o-currency-dollar class="h-4 w-4" />
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-amber-400 mt-2">${{ number_format($data['presupuesto_total'], 2) }} CUP</p>
                    <p class="text-[11px] text-slate-400 mt-1">Suma de todas las obras y proyectos</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Top 5 Proyectos por Presupuesto --}}
                <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-white flex items-center gap-2">
                            <x-heroicon-o-briefcase class="h-4 w-4 text-sky-400" />
                            Top 5 Proyectos por Presupuesto
                        </h3>
                        <a href="/admin/proyectos" class="text-xs text-sky-400 hover:text-sky-300 font-semibold cursor-pointer">
                            Ver todos →
                        </a>
                    </div>
                    <div class="space-y-4">
                        @forelse($data['top_proyectos'] as $idx => $p)
                            @php
                                $pct = round(($p['presupuesto_total'] / $data['max_presupuesto']) * 100);
                            @endphp
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-semibold text-white flex items-center gap-2 truncate max-w-xs">
                                        <span class="flex h-5 w-5 items-center justify-center rounded bg-sky-500/20 text-[10px] font-bold text-sky-300">
                                            {{ $idx + 1 }}
                                        </span>
                                        <span class="truncate">{{ $p['nombre'] }}</span>
                                    </span>
                                    <span class="font-mono font-bold text-sky-400">
                                        ${{ number_format($p['presupuesto_total'], 2) }} CUP
                                    </span>
                                </div>
                                <div class="h-2 w-full rounded-full bg-slate-800 overflow-hidden">
                                    <div class="h-full rounded-full bg-gradient-to-r from-sky-400 to-blue-600 transition-all duration-700"
                                         style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                        @empty
                            <div class="py-8 text-center text-xs text-slate-500">
                                No hay proyectos registrados aún en la plataforma.
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Proyectos Recientes --}}
                <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-white flex items-center gap-2">
                            <x-heroicon-o-clock class="h-4 w-4 text-sky-400" />
                            Proyectos Recientes
                        </h3>
                        <span class="rounded-full bg-sky-500/10 px-2 py-0.5 text-[10px] font-bold text-sky-400 border border-sky-500/20">
                            en seguimiento
                        </span>
                    </div>
                    <div class="space-y-2.5">
                        @forelse($data['proyectos_recientes'] as $p)
                            <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-950/60 border border-slate-800/80 hover:border-sky-500/30 transition-colors">
                                <div class="min-w-0 flex-1 pr-3">
                                    <p class="font-semibold text-xs text-white truncate">{{ $p['nombre'] }}</p>
                                    <p class="text-[10px] text-slate-400 flex items-center gap-2 mt-0.5">
                                        <span class="font-mono">{{ $p['codigo'] }}</span>
                                        <span>·</span>
                                        <span>{{ $p['cliente_nombre'] }}</span>
                                    </p>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{
                                    $p['estado'] === 'completado' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' :
                                    ($p['estado'] === 'en_progreso' ? 'bg-sky-500/10 text-sky-400 border border-sky-500/20' :
                                    'bg-slate-800 text-slate-400')
                                }}">
                                    {{ str_replace('_', ' ', $p['estado']) }}
                                </span>
                            </div>
                        @empty
                            <div class="py-8 text-center text-xs text-slate-500">
                                No hay proyectos en curso.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- ---------- TAB: OPERACIONES ---------- --}}
        <div x-show="activeTab === 'operaciones'" x-cloak class="space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-400">Brigadas Activas</span>
                        <div class="rounded-lg bg-purple-500/10 p-2 text-purple-400 border border-purple-500/20">
                            <x-heroicon-o-user-group class="h-4 w-4" />
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-purple-400 mt-2">{{ $data['brigadas_count'] }}</p>
                    <p class="text-[11px] text-slate-400 mt-1">Cuadrillas disponibles en terreno</p>
                </div>

                <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-400">Servicios Instalados</span>
                        <div class="rounded-lg bg-sky-500/10 p-2 text-sky-400 border border-sky-500/20">
                            <x-heroicon-o-radio class="h-4 w-4" />
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-sky-400 mt-2">{{ $data['servicios_total'] }}</p>
                    <p class="text-[11px] text-slate-400 mt-1">CCTV, SACI y Gestión Remota 4G</p>
                </div>

                <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-400">Incidencias Abiertas</span>
                        <div class="rounded-lg bg-rose-500/10 p-2 text-rose-400 border border-rose-500/20">
                            <x-heroicon-o-exclamation-circle class="h-4 w-4" />
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-rose-400 mt-2">{{ $data['incidencias_abiertas'] }}</p>
                    <p class="text-[11px] text-slate-400 mt-1">Tickets técnicos pendientes</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Desglose de Servicios Técnicos --}}
                <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6 shadow-sm">
                    <h3 class="text-sm font-bold text-white mb-4 flex items-center gap-2">
                        <x-heroicon-o-radio class="h-4 w-4 text-sky-400" />
                        Parque de Servicios Técnicos
                    </h3>
                    <div class="grid grid-cols-3 gap-3 mb-4">
                        <div class="rounded-xl border border-slate-800 bg-slate-950 p-3 text-center">
                            <x-heroicon-o-video-camera class="h-4 w-4 text-blue-400 mx-auto mb-1" />
                            <p class="text-xl font-extrabold text-white">{{ $data['servicios_cctv'] }}</p>
                            <p class="text-[10px] text-slate-400 uppercase font-semibold">CCTV</p>
                        </div>
                        <div class="rounded-xl border border-slate-800 bg-slate-950 p-3 text-center">
                            <x-heroicon-o-shield-check class="h-4 w-4 text-emerald-400 mx-auto mb-1" />
                            <p class="text-xl font-extrabold text-white">{{ $data['servicios_saci'] }}</p>
                            <p class="text-[10px] text-slate-400 uppercase font-semibold">Alarmas SACI</p>
                        </div>
                        <div class="rounded-xl border border-slate-800 bg-slate-950 p-3 text-center">
                            <x-heroicon-o-signal class="h-4 w-4 text-amber-400 mx-auto mb-1" />
                            <p class="text-xl font-extrabold text-white">{{ $data['servicios_gr'] }}</p>
                            <p class="text-[10px] text-slate-400 uppercase font-semibold">Gestión 4G</p>
                        </div>
                    </div>
                    <div class="space-y-2">
                        @forelse($data['servicios_recientes'] as $s)
                            <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-xs">
                                <div>
                                    <p class="font-semibold text-white">{{ $s['cliente_nombre'] }}</p>
                                    <p class="text-[10px] text-slate-400">
                                        {{ $s['tipo'] }} · {{ $s['detalle'] }}
                                    </p>
                                </div>
                                <span class="font-mono text-[10px] text-sky-400 bg-sky-950/40 px-2 py-0.5 rounded border border-sky-500/20">
                                    {{ $s['codigo'] }}
                                </span>
                            </div>
                        @empty
                            <div class="py-8 text-center text-xs text-slate-500">
                                No hay servicios técnicos instalados en campo.
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Incidencias Prioritarias --}}
                <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-white flex items-center gap-2">
                            <x-heroicon-o-exclamation-circle class="h-4 w-4 text-rose-400" />
                            Incidencias Técnicas Recientes
                        </h3>
                        <a href="/admin/incidencias" class="text-xs text-sky-400 hover:text-sky-300 font-semibold cursor-pointer">
                            Ver tickets →
                        </a>
                    </div>
                    <div class="space-y-2.5">
                        @forelse($data['incidencias_recientes'] as $i)
                            <div class="p-3 rounded-xl bg-slate-950/60 border border-slate-800 space-y-1">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-semibold text-white truncate max-w-[200px]">{{ $i['titulo'] }}</span>
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold {{
                                        $i['prioridad'] === 'Critica' ? 'bg-rose-500/20 text-rose-300' :
                                        ($i['prioridad'] === 'Alta' ? 'bg-amber-500/20 text-amber-300' :
                                        'bg-slate-800 text-slate-300')
                                    }}">
                                        {{ $i['prioridad'] }}
                                    </span>
                                </div>
                                <p class="text-[11px] text-slate-400">
                                    Cliente: <span class="text-slate-200">{{ $i['cliente_nombre'] }}</span> · Tipo: {{ $i['tipo'] }}
                                </p>
                            </div>
                        @empty
                            <div class="py-8 text-center text-xs text-slate-500 flex flex-col items-center justify-center">
                                <x-heroicon-o-check-circle class="h-6 w-6 text-emerald-400 mb-1" />
                                <span>Sin incidencias reportadas. Todo en orden.</span>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- ---------- TAB: INVENTARIO ---------- --}}
        <div x-show="activeTab === 'inventario'" x-cloak class="space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-400">Total Items</span>
                        <div class="rounded-lg bg-sky-500/10 p-2 text-sky-400 border border-sky-500/20">
                            <x-heroicon-o-archive-box class="h-4 w-4" />
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-white mt-2">{{ $data['items_total'] }}</p>
                    <p class="text-[11px] text-slate-400 mt-1">En catálogo de inventario</p>
                </div>

                <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-400">Stock Bajo</span>
                        <div class="rounded-lg bg-rose-500/10 p-2 text-rose-400 border border-rose-500/20">
                            <x-heroicon-o-exclamation-triangle class="h-4 w-4" />
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-rose-400 mt-2">{{ $data['stock_bajo_count'] }}</p>
                    <p class="text-[11px] text-slate-400 mt-1">Requieren reposición inmediata</p>
                </div>

                <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-400">Equipamiento</span>
                        <div class="rounded-lg bg-purple-500/10 p-2 text-purple-400 border border-purple-500/20">
                            <x-heroicon-o-cube class="h-4 w-4" />
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-purple-400 mt-2">{{ $data['equipamiento_stock'] }}</p>
                    <p class="text-[11px] text-slate-400 mt-1">Unidades en almacén</p>
                </div>

                <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-400">Materiales & Consumibles</span>
                        <div class="rounded-lg bg-emerald-500/10 p-2 text-emerald-400 border border-emerald-500/20">
                            <x-heroicon-o-squares-plus class="h-4 w-4" />
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-emerald-400 mt-2">{{ $data['materiales_stock'] }}</p>
                    <p class="text-[11px] text-slate-400 mt-1">Unidades en stock</p>
                </div>
            </div>

            {{-- Panel Alertas de Stock Bajo --}}
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <x-heroicon-s-exclamation-triangle class="h-5 w-5 text-rose-500" />
                        <h3 class="text-sm font-bold text-white">Alertas de Stock Bajo</h3>
                    </div>
                    <span class="rounded-full bg-rose-500/10 px-2.5 py-0.5 text-xs font-bold text-rose-400 border border-rose-500/20">
                        {{ $data['stock_bajo_count'] }} ítems en umbral crítico
                    </span>
                </div>
                <div class="space-y-3">
                    @forelse($data['alertas_stock'] as $item)
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between p-3 rounded-xl border border-rose-500/30 bg-rose-950/20 gap-3">
                            <div>
                                <p class="font-semibold text-xs text-white">{{ $item['nombre'] }}</p>
                                <p class="text-[11px] text-slate-400 font-mono mt-0.5">
                                    Código: {{ $item['codigo'] }} · Categoría: {{ $item['categoria_nombre'] }}
                                </p>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="text-right">
                                    <p class="text-xs font-bold text-rose-400">
                                        {{ $item['stock_actual'] }} {{ $item['unidad_medida'] }} en stock
                                    </p>
                                    <p class="text-[10px] text-slate-400">Mínimo: {{ $item['stock_minimo'] }}</p>
                                </div>
                                <a href="/admin/inventario-movimientos/create"
                                   class="rounded-lg bg-rose-500 hover:bg-rose-400 px-3 py-1.5 text-xs font-bold text-slate-950 transition-colors cursor-pointer">
                                    Reponer
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-xs text-slate-500">
                            ¡Todo el inventario está sobre los niveles mínimos requeridos!
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- ---------- TAB: FINANZAS ---------- --}}
        <div x-show="activeTab === 'finanzas'" x-cloak class="space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5 shadow-sm">
                    <span class="text-xs font-semibold text-slate-400">Presupuesto en Obras</span>
                    <p class="text-2xl font-bold text-white mt-2">${{ number_format($data['presupuesto_total'], 2) }} CUP</p>
                    <p class="text-[11px] text-slate-400 mt-1">Cartera de proyectos</p>
                </div>
                <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5 shadow-sm">
                    <span class="text-xs font-semibold text-slate-400">Costo Equipamiento</span>
                    <p class="text-2xl font-bold text-emerald-400 mt-2">${{ number_format($data['presupuesto_equipamiento'], 2) }} CUP</p>
                    <p class="text-[11px] text-slate-400 mt-1">Líneas de presupuesto</p>
                </div>
                <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5 shadow-sm">
                    <span class="text-xs font-semibold text-slate-400">Costo Mano de Obra</span>
                    <p class="text-2xl font-bold text-amber-400 mt-2">${{ number_format($data['presupuesto_mano_obra'], 2) }} CUP</p>
                    <p class="text-[11px] text-slate-400 mt-1">Brigadas y técnicos</p>
                </div>
                <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5 shadow-sm">
                    <span class="text-xs font-semibold text-slate-400">Inversión este Mes</span>
                    <p class="text-2xl font-bold text-sky-400 mt-2">${{ number_format($data['finanzas_mes'], 2) }} CUP</p>
                    <p class="text-[11px] text-slate-400 mt-1">Nuevas obras iniciadas</p>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <x-heroicon-o-banknotes class="h-4 w-4 text-emerald-400" />
                        Desglose Presupuestario por Categoría
                    </h3>
                    <a href="/admin/proyectos" class="text-xs text-sky-400 hover:text-sky-300 font-semibold cursor-pointer">
                        Ver presupuestos →
                    </a>
                </div>
                @php
                    $totalLineas = ($data['presupuesto_equipamiento'] + $data['presupuesto_mano_obra'] + $data['presupuesto_materiales'] + $data['presupuesto_transporte']) ?: 1;
                    $catEquip = round(($data['presupuesto_equipamiento'] / $totalLineas) * 100);
                    $catMano = round(($data['presupuesto_mano_obra'] / $totalLineas) * 100);
                    $catMat = round(($data['presupuesto_materiales'] / $totalLineas) * 100);
                    $catTrans = round(($data['presupuesto_transporte'] / $totalLineas) * 100);
                @endphp
                <div class="space-y-4">
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-semibold text-white">Equipamiento (Cámaras, Sensores, Enlaces)</span>
                            <span class="font-mono font-bold text-emerald-400">${{ number_format($data['presupuesto_equipamiento'], 2) }} CUP ({{ $catEquip }}%)</span>
                        </div>
                        <div class="h-2 w-full rounded-full bg-slate-800 overflow-hidden">
                            <div class="h-full rounded-full bg-emerald-500" style="width: {{ $catEquip }}%"></div>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-semibold text-white">Mano de Obra & Instalaciones</span>
                            <span class="font-mono font-bold text-amber-400">${{ number_format($data['presupuesto_mano_obra'], 2) }} CUP ({{ $catMano }}%)</span>
                        </div>
                        <div class="h-2 w-full rounded-full bg-slate-800 overflow-hidden">
                            <div class="h-full rounded-full bg-amber-500" style="width: {{ $catMano }}%"></div>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-semibold text-white">Materiales, Cables y Canalizaciones</span>
                            <span class="font-mono font-bold text-sky-400">${{ number_format($data['presupuesto_materiales'], 2) }} CUP ({{ $catMat }}%)</span>
                        </div>
                        <div class="h-2 w-full rounded-full bg-slate-800 overflow-hidden">
                            <div class="h-full rounded-full bg-sky-500" style="width: {{ $catMat }}%"></div>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-semibold text-white">Logística & Transporte</span>
                            <span class="font-mono font-bold text-purple-400">${{ number_format($data['presupuesto_transporte'], 2) }} CUP ({{ $catTrans }}%)</span>
                        </div>
                        <div class="h-2 w-full rounded-full bg-slate-800 overflow-hidden">
                            <div class="h-full rounded-full bg-purple-500" style="width: {{ $catTrans }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ---------- TAB: ADMINISTRACIÓN ---------- --}}
        <div x-show="activeTab === 'administracion'" x-cloak class="space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5 shadow-sm">
                    <span class="text-xs font-semibold text-slate-400">Total Clientes en Sistema</span>
                    <p class="text-2xl font-bold text-sky-400 mt-2">{{ $data['clientes_total'] }}</p>
                    <p class="text-[11px] text-slate-400 mt-1">Portafolio oficial DataPlus</p>
                </div>
                <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5 shadow-sm">
                    <span class="text-xs font-semibold text-slate-400">Usuarios con Acceso</span>
                    <p class="text-2xl font-bold text-emerald-400 mt-2">{{ $data['usuarios_total'] }}</p>
                    <p class="text-[11px] text-slate-400 mt-1">Autenticación híbrida LDAP + Local</p>
                </div>
                <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5 shadow-sm">
                    <span class="text-xs font-semibold text-slate-400">Directorio LDAP / LLDAP</span>
                    <p class="text-2xl font-bold text-purple-400 mt-2">dc=dataplus,dc=cu</p>
                    <p class="text-[11px] text-slate-400 mt-1">Servicio online y enlazado</p>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <x-heroicon-o-shield-check class="h-4 w-4 text-sky-400" />
                    Accesos Rápidos de Administración
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <a href="/admin/clientes"
                       class="flex items-center justify-between p-3.5 rounded-xl border border-slate-800 bg-slate-950/70 hover:border-sky-500/40 text-left transition-colors cursor-pointer">
                        <div>
                            <p class="text-xs font-bold text-white">Directorio de Clientes</p>
                            <p class="text-[10px] text-slate-400 mt-0.5">{{ $data['clientes_total'] }} empresas registradas</p>
                        </div>
                        <x-heroicon-o-chevron-right class="h-4 w-4 text-slate-400" />
                    </a>
                    <a href="/admin/users"
                       class="flex items-center justify-between p-3.5 rounded-xl border border-slate-800 bg-slate-950/70 hover:border-sky-500/40 text-left transition-colors cursor-pointer">
                        <div>
                            <p class="text-xs font-bold text-white">Usuarios & Permisos</p>
                            <p class="text-[10px] text-slate-400 mt-0.5">Roles RBAC y cuentas</p>
                        </div>
                        <x-heroicon-o-chevron-right class="h-4 w-4 text-slate-400" />
                    </a>
                    <a href="/admin/ldap-configurations"
                       class="flex items-center justify-between p-3.5 rounded-xl border border-slate-800 bg-slate-950/70 hover:border-sky-500/40 text-left transition-colors cursor-pointer">
                        <div>
                            <p class="text-xs font-bold text-white">Configuración LDAP</p>
                            <p class="text-[10px] text-slate-400 mt-0.5">Parámetros de conexión LLDAP</p>
                        </div>
                        <x-heroicon-o-chevron-right class="h-4 w-4 text-slate-400" />
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
