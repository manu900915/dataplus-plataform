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

    {{-- ============================================================
         ESTILOS AUTO-CONTENIDOS EXACTOS DEL PREVIEW REACT
         (Garantiza renderizado 100% idéntico sin depender de build de Tailwind)
         ============================================================ --}}
    <style>
        [x-cloak] { display: none !important; }

        .dp-dash-wrap {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #f1f5f9;
            user-select: none;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        /* Hero oficial con gradiente idéntico al preview */
        .dp-hero {
            position: relative;
            overflow: hidden;
            border-radius: 1rem;
            background: linear-gradient(90deg, #0284c7 0%, #0369a1 50%, #075985 100%);
            padding: 1.75rem 2rem;
            color: #ffffff;
            box-shadow: 0 20px 25px -5px rgba(8, 47, 73, 0.3), 0 8px 10px -6px rgba(8, 47, 73, 0.3);
        }
        .dp-hero-content {
            position: relative;
            z-index: 10;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            justify-content: space-between;
        }
        @media (min-width: 640px) {
            .dp-hero-content {
                flex-direction: row;
                align-items: center;
            }
        }
        .dp-hero-glow {
            position: absolute;
            right: -3rem;
            bottom: -3rem;
            width: 14rem;
            height: 14rem;
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.12);
            filter: blur(40px);
            pointer-events: none;
        }
        .dp-hero-tag {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: rgba(224, 242, 254, 0.85);
        }
        .dp-hero-title {
            margin-top: 0.25rem;
            font-size: 1.75rem;
            font-weight: 800;
            letter-spacing: -0.025em;
            color: #ffffff;
            line-height: 1.2;
        }
        .dp-hero-sub {
            margin-top: 0.5rem;
            font-size: 0.875rem;
            color: rgba(224, 242, 254, 0.85);
            font-weight: 500;
        }
        .dp-hero-pills {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-shrink: 0;
        }
        .dp-hero-pill {
            border-radius: 0.75rem;
            background: rgba(255, 255, 255, 0.12);
            padding: 0.65rem 1rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(8px);
        }

        /* Tabs bar */
        .dp-tabs-box {
            border-radius: 0.75rem;
            border: 1px solid #1e293b;
            background: rgba(15, 23, 42, 0.85);
            padding: 0.375rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
        .dp-tabs-nav {
            display: flex;
            gap: 0.375rem;
            overflow-x: auto;
        }
        .dp-tab-item {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            white-space: nowrap;
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            font-size: 0.75rem;
            font-weight: 700;
            transition: all 0.2s ease;
            cursor: pointer;
            border: none;
            background: transparent;
            color: #94a3b8;
        }
        .dp-tab-item:hover {
            background: rgba(30, 41, 59, 0.8);
            color: #e2e8f0;
        }
        .dp-tab-item.dp-tab-active {
            background: linear-gradient(90deg, #0ea5e9 0%, #0284c7 100%) !important;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(14, 165, 233, 0.35);
        }

        /* Tarjetas de estadísticas */
        .dp-stat-grid-4 {
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: 1rem;
        }
        @media (min-width: 640px) {
            .dp-stat-grid-4 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (min-width: 1280px) {
            .dp-stat-grid-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        }

        .dp-stat-grid-3 {
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: 1rem;
        }
        @media (min-width: 640px) {
            .dp-stat-grid-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        }

        .dp-stat-card {
            border-radius: 1rem;
            border: 1px solid #1e293b;
            background: rgba(15, 23, 42, 0.75);
            padding: 1.25rem;
            transition: border-color 0.2s ease, transform 0.2s ease;
        }
        .dp-stat-card:hover {
            border-color: rgba(56, 189, 248, 0.4);
            transform: translateY(-2px);
        }
        .dp-stat-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .dp-stat-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: #94a3b8;
        }
        .dp-stat-icon-box {
            border-radius: 0.5rem;
            padding: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .dp-stat-icon-box.sky {
            background: rgba(14, 165, 233, 0.12);
            color: #38bdf8;
            border: 1px solid rgba(56, 189, 248, 0.2);
        }
        .dp-stat-icon-box.emerald {
            background: rgba(16, 185, 129, 0.12);
            color: #34d399;
            border: 1px solid rgba(52, 211, 153, 0.2);
        }
        .dp-stat-icon-box.rose {
            background: rgba(244, 63, 94, 0.12);
            color: #fb7185;
            border: 1px solid rgba(251, 113, 133, 0.2);
        }
        .dp-stat-icon-box.amber {
            background: rgba(245, 158, 11, 0.12);
            color: #fbbf24;
            border: 1px solid rgba(251, 191, 36, 0.2);
        }
        .dp-stat-icon-box.purple {
            background: rgba(168, 85, 247, 0.12);
            color: #c084fc;
            border: 1px solid rgba(192, 132, 252, 0.2);
        }
        .dp-stat-num {
            font-size: 1.5rem;
            font-weight: 700;
            color: #ffffff;
            margin-top: 0.5rem;
            line-height: 1.2;
        }
        .dp-stat-hint {
            font-size: 0.6875rem;
            color: #94a3b8;
            margin-top: 0.25rem;
        }

        /* Paneles de dos columnas */
        .dp-panel-grid-2 {
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: 1.5rem;
        }
        @media (min-width: 1024px) {
            .dp-panel-grid-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        .dp-panel {
            border-radius: 1rem;
            border: 1px solid #1e293b;
            background: rgba(15, 23, 42, 0.75);
            padding: 1.5rem;
        }
        .dp-panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }
        .dp-panel-title {
            font-size: 0.875rem;
            font-weight: 700;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .dp-panel-link {
            font-size: 0.75rem;
            color: #38bdf8;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
        }
        .dp-panel-link:hover {
            color: #7dd3fc;
            text-decoration: underline;
        }

        /* Filas de listas */
        .dp-list-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.65rem 0.85rem;
            border-radius: 0.75rem;
            background: rgba(2, 6, 23, 0.6);
            border: 1px solid rgba(30, 41, 59, 0.8);
            transition: all 0.2s ease;
            margin-bottom: 0.625rem;
        }
        .dp-list-row:hover {
            border-color: rgba(56, 189, 248, 0.35);
            transform: translateX(2px);
        }

        /* Barras de progreso */
        .dp-progress-bg {
            height: 0.5rem;
            width: 100%;
            border-radius: 9999px;
            background: #1e293b;
            overflow: hidden;
            margin-top: 0.375rem;
        }
        .dp-progress-bar {
            height: 100%;
            border-radius: 9999px;
            background: linear-gradient(90deg, #38bdf8 0%, #2563eb 100%);
            transition: width 0.7s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Alertas stock */
        .dp-alert-card {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            border: 1px solid rgba(244, 63, 94, 0.3);
            background: rgba(136, 19, 55, 0.15);
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }
        @media (min-width: 640px) {
            .dp-alert-card {
                flex-direction: row;
                align-items: center;
            }
        }
        .dp-btn-reponer {
            border-radius: 0.5rem;
            background: #f43f5e;
            color: #020617;
            padding: 0.375rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 700;
            text-decoration: none;
            transition: background-color 0.2s ease;
            display: inline-block;
        }
        .dp-btn-reponer:hover {
            background: #fb7185;
            color: #020617;
        }

        /* Micro-boxes de servicios */
        .dp-service-box {
            border-radius: 0.75rem;
            border: 1px solid #1e293b;
            background: #020617;
            padding: 0.75rem;
            text-align: center;
        }
        .dp-service-icon {
            margin: 0 auto 0.25rem;
            width: 1.25rem;
            height: 1.25rem;
        }

        /* Quick link cards administración */
        .dp-quick-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.875rem 1rem;
            border-radius: 0.75rem;
            border: 1px solid #1e293b;
            background: rgba(2, 6, 23, 0.7);
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .dp-quick-link:hover {
            border-color: rgba(56, 189, 248, 0.4);
            transform: translateX(3px);
        }
    </style>

    <div class="dp-dash-wrap"
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

        {{-- ================= HERO / ENCABEZADO OFICIAL PREVIEW ================= --}}
        <div class="dp-hero">
            <div class="dp-hero-content">
                <div>
                    <p class="dp-hero-tag">
                        <span x-text="greeting()"></span>,
                    </p>
                    <h1 class="dp-hero-title">
                        {{ auth()->user()->name ?? 'Enmanuel Caraballo' }}
                    </h1>
                    <p class="dp-hero-sub">
                        DataPlus Platform · {{ $data['proyectos_activos'] }} proyectos activos · 
                        {{ $data['stock_bajo_count'] > 0 ? $data['stock_bajo_count'] . ' alertas de stock bajo' : 'sin alertas de stock' }} · 
                        {{ $data['servicios_total'] }} servicios en campo
                    </p>
                </div>
                <div class="dp-hero-pills">
                    @if($data['retrasados'] > 0)
                        <div class="dp-hero-pill">
                            <p style="font-size: 0.625rem; text-transform: uppercase; font-weight: 700; color: #fde68a;">Con retraso</p>
                            <p style="font-size: 1.25rem; font-weight: 800; color: #fcd34d; line-height: 1;">{{ $data['retrasados'] }}</p>
                        </div>
                    @endif
                    <div class="dp-hero-pill">
                        <p style="font-size: 0.625rem; text-transform: uppercase; font-weight: 700; color: #e0f2fe;">Presupuesto total</p>
                        <p style="font-size: 1.25rem; font-weight: 800; color: #ffffff; line-height: 1;">${{ number_format($data['presupuesto_total'], 2) }} CUP</p>
                    </div>
                </div>
            </div>
            <div class="dp-hero-glow"></div>
        </div>

        {{-- ================= TABS DE SECCIONES (IDÉNTICAS AL PREVIEW) ================= --}}
        <div class="dp-tabs-box">
            <nav class="dp-tabs-nav" role="tablist">
                @foreach($tabs as $key => $tab)
                    <button type="button"
                            role="tab"
                            @click="setTab('{{ $key }}')"
                            :class="activeTab === '{{ $key }}' ? 'dp-tab-active' : ''"
                            class="dp-tab-item">
                        <x-dynamic-component :component="$tab['icon']" style="width: 1rem; height: 1rem;" />
                        <span>{{ $tab['label'] }}</span>
                    </button>
                @endforeach
            </nav>
        </div>

        {{-- ================= CONTENIDO DE CADA TAB ================= --}}

        {{-- ---------- TAB 1: PROYECTOS ---------- --}}
        <div x-show="activeTab === 'proyectos'" x-cloak style="display: flex; flex-direction: column; gap: 1.5rem;">
            <div class="dp-stat-grid-4">
                <div class="dp-stat-card">
                    <div class="dp-stat-top">
                        <span class="dp-stat-label">Proyectos Activos</span>
                        <div class="dp-stat-icon-box sky">
                            <x-heroicon-o-briefcase style="width: 1rem; height: 1rem;" />
                        </div>
                    </div>
                    <p class="dp-stat-num">{{ $data['proyectos_activos'] }}</p>
                    <p class="dp-stat-hint">En borrador o ejecución activa</p>
                </div>

                <div class="dp-stat-card">
                    <div class="dp-stat-top">
                        <span class="dp-stat-label">Completados este Mes</span>
                        <div class="dp-stat-icon-box emerald">
                            <x-heroicon-o-check-circle style="width: 1rem; height: 1rem;" />
                        </div>
                    </div>
                    <p class="dp-stat-num" style="color: #34d399;">{{ $data['proyectos_completados'] }}</p>
                    <p class="dp-stat-hint">Cierre satisfactorio</p>
                </div>

                <div class="dp-stat-card">
                    <div class="dp-stat-top">
                        <span class="dp-stat-label">Con Retraso</span>
                        <div class="dp-stat-icon-box rose">
                            <x-heroicon-o-exclamation-triangle style="width: 1rem; height: 1rem;" />
                        </div>
                    </div>
                    <p class="dp-stat-num" style="color: #fb7185;">{{ $data['retrasados'] }}</p>
                    <p class="dp-stat-hint">Pasaron su fecha comprometida</p>
                </div>

                <div class="dp-stat-card">
                    <div class="dp-stat-top">
                        <span class="dp-stat-label">Presupuesto Total</span>
                        <div class="dp-stat-icon-box amber">
                            <x-heroicon-o-currency-dollar style="width: 1rem; height: 1rem;" />
                        </div>
                    </div>
                    <p class="dp-stat-num" style="color: #fbbf24;">${{ number_format($data['presupuesto_total'], 2) }} CUP</p>
                    <p class="dp-stat-hint">Suma de todas las obras y proyectos</p>
                </div>
            </div>

            <div class="dp-panel-grid-2">
                {{-- Top 5 Proyectos por Presupuesto --}}
                <div class="dp-panel">
                    <div class="dp-panel-header">
                        <h3 class="dp-panel-title">
                            <x-heroicon-o-briefcase style="width: 1rem; height: 1rem; color: #38bdf8;" />
                            Top 5 Proyectos por Presupuesto
                        </h3>
                        <a href="/admin/proyectos" class="dp-panel-link">Ver todos →</a>
                    </div>
                    <div>
                        @forelse($data['top_proyectos'] as $idx => $p)
                            @php
                                $pct = round(($p['presupuesto_total'] / $data['max_presupuesto']) * 100);
                            @endphp
                            <div style="margin-bottom: 1rem;">
                                <div style="display: flex; align-items: center; justify-content: space-between; font-size: 0.75rem;">
                                    <span style="font-weight: 600; color: #ffffff; display: flex; align-items: center; gap: 0.5rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 70%;">
                                        <span style="display: flex; width: 1.25rem; height: 1.25rem; align-items: center; justify-content: center; border-radius: 0.25rem; background: rgba(14, 165, 233, 0.2); font-size: 0.625rem; font-weight: 700; color: #7dd3fc; flex-shrink: 0;">
                                            {{ $idx + 1 }}
                                        </span>
                                        <span style="overflow: hidden; text-overflow: ellipsis;">{{ $p['nombre'] }}</span>
                                    </span>
                                    <span style="font-family: monospace; font-weight: 700; color: #38bdf8;">
                                        ${{ number_format($p['presupuesto_total'], 2) }} CUP
                                    </span>
                                </div>
                                <div class="dp-progress-bg">
                                    <div class="dp-progress-bar" style="width: {{ $pct }}%;"></div>
                                </div>
                            </div>
                        @empty
                            <div style="padding: 2rem 0; text-align: center; font-size: 0.75rem; color: #64748b;">
                                No hay proyectos registrados aún en la plataforma.
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Proyectos Recientes --}}
                <div class="dp-panel">
                    <div class="dp-panel-header">
                        <h3 class="dp-panel-title">
                            <x-heroicon-o-clock style="width: 1rem; height: 1rem; color: #38bdf8;" />
                            Proyectos Recientes
                        </h3>
                        <span style="border-radius: 9999px; background: rgba(14, 165, 233, 0.1); padding: 0.15rem 0.5rem; font-size: 0.625rem; font-weight: 700; color: #38bdf8; border: 1px solid rgba(14, 165, 233, 0.2);">
                            en seguimiento
                        </span>
                    </div>
                    <div>
                        @forelse($data['proyectos_recientes'] as $p)
                            <div class="dp-list-row">
                                <div style="min-width: 0; flex: 1; padding-right: 0.75rem;">
                                    <p style="font-size: 0.75rem; font-weight: 600; color: #ffffff; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; margin: 0;">{{ $p['nombre'] }}</p>
                                    <p style="font-size: 0.625rem; color: #94a3b8; display: flex; align-items: center; gap: 0.5rem; margin: 0.2rem 0 0;">
                                        <span style="font-family: monospace;">{{ $p['codigo'] }}</span>
                                        <span>·</span>
                                        <span>{{ $p['cliente_nombre'] }}</span>
                                    </p>
                                </div>
                                <span style="padding: 0.15rem 0.5rem; border-radius: 0.25rem; font-size: 0.625rem; font-weight: 700; text-transform: uppercase;
                                    {{ $p['estado'] === 'completado' ? 'background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(52, 211, 153, 0.25);' :
                                       ($p['estado'] === 'en_progreso' ? 'background: rgba(14, 165, 233, 0.15); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.25);' :
                                       'background: #1e293b; color: #94a3b8;') }}">
                                    {{ str_replace('_', ' ', $p['estado']) }}
                                </span>
                            </div>
                        @empty
                            <div style="padding: 2rem 0; text-align: center; font-size: 0.75rem; color: #64748b;">
                                No hay proyectos en curso.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- ---------- TAB 2: OPERACIONES ---------- --}}
        <div x-show="activeTab === 'operaciones'" x-cloak style="display: flex; flex-direction: column; gap: 1.5rem;">
            <div class="dp-stat-grid-3">
                <div class="dp-stat-card">
                    <div class="dp-stat-top">
                        <span class="dp-stat-label">Brigadas Activas</span>
                        <div class="dp-stat-icon-box purple">
                            <x-heroicon-o-user-group style="width: 1rem; height: 1rem;" />
                        </div>
                    </div>
                    <p class="dp-stat-num" style="color: #c084fc;">{{ $data['brigadas_count'] }}</p>
                    <p class="dp-stat-hint">Cuadrillas disponibles en terreno</p>
                </div>

                <div class="dp-stat-card">
                    <div class="dp-stat-top">
                        <span class="dp-stat-label">Servicios Instalados</span>
                        <div class="dp-stat-icon-box sky">
                            <x-heroicon-o-radio style="width: 1rem; height: 1rem;" />
                        </div>
                    </div>
                    <p class="dp-stat-num" style="color: #38bdf8;">{{ $data['servicios_total'] }}</p>
                    <p class="dp-stat-hint">CCTV, SACI y Gestión Remota 4G</p>
                </div>

                <div class="dp-stat-card">
                    <div class="dp-stat-top">
                        <span class="dp-stat-label">Incidencias Abiertas</span>
                        <div class="dp-stat-icon-box rose">
                            <x-heroicon-o-exclamation-circle style="width: 1rem; height: 1rem;" />
                        </div>
                    </div>
                    <p class="dp-stat-num" style="color: #fb7185;">{{ $data['incidencias_abiertas'] }}</p>
                    <p class="dp-stat-hint">Tickets técnicos pendientes</p>
                </div>
            </div>

            <div class="dp-panel-grid-2">
                {{-- Parque de Servicios Técnicos --}}
                <div class="dp-panel">
                    <div class="dp-panel-header">
                        <h3 class="dp-panel-title">
                            <x-heroicon-o-radio style="width: 1rem; height: 1rem; color: #38bdf8;" />
                            Parque de Servicios Técnicos
                        </h3>
                        <a href="/admin/servicios" class="dp-panel-link">Ver servicios →</a>
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 0.75rem; margin-bottom: 1rem;">
                        <div class="dp-service-box">
                            <x-heroicon-o-video-camera class="dp-service-icon" style="color: #60a5fa;" />
                            <p style="font-size: 1.25rem; font-weight: 800; color: #ffffff; margin: 0; line-height: 1;">{{ $data['servicios_cctv'] }}</p>
                            <p style="font-size: 0.625rem; color: #94a3b8; text-transform: uppercase; font-weight: 600; margin: 0.25rem 0 0;">CCTV</p>
                        </div>
                        <div class="dp-service-box">
                            <x-heroicon-o-shield-check class="dp-service-icon" style="color: #34d399;" />
                            <p style="font-size: 1.25rem; font-weight: 800; color: #ffffff; margin: 0; line-height: 1;">{{ $data['servicios_saci'] }}</p>
                            <p style="font-size: 0.625rem; color: #94a3b8; text-transform: uppercase; font-weight: 600; margin: 0.25rem 0 0;">Alarmas SACI</p>
                        </div>
                        <div class="dp-service-box">
                            <x-heroicon-o-signal class="dp-service-icon" style="color: #fbbf24;" />
                            <p style="font-size: 1.25rem; font-weight: 800; color: #ffffff; margin: 0; line-height: 1;">{{ $data['servicios_gr'] }}</p>
                            <p style="font-size: 0.625rem; color: #94a3b8; text-transform: uppercase; font-weight: 600; margin: 0.25rem 0 0;">Gestión 4G</p>
                        </div>
                    </div>
                    <div>
                        @forelse($data['servicios_recientes'] as $s)
                            <div class="dp-list-row">
                                <div style="min-width: 0; flex: 1;">
                                    <p style="font-size: 0.75rem; font-weight: 600; color: #ffffff; margin: 0;">{{ $s['cliente_nombre'] }}</p>
                                    <p style="font-size: 0.625rem; color: #94a3b8; margin: 0.2rem 0 0;">
                                        {{ $s['tipo'] }} · {{ $s['detalle'] }}
                                    </p>
                                </div>
                                <span style="font-family: monospace; font-size: 0.625rem; color: #38bdf8; background: rgba(8, 47, 73, 0.4); padding: 0.15rem 0.5rem; border-radius: 0.25rem; border: 1px solid rgba(56, 189, 248, 0.2);">
                                    {{ $s['codigo'] }}
                                </span>
                            </div>
                        @empty
                            <div style="padding: 2rem 0; text-align: center; font-size: 0.75rem; color: #64748b;">
                                No hay servicios técnicos instalados en campo.
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Incidencias Prioritarias --}}
                <div class="dp-panel">
                    <div class="dp-panel-header">
                        <h3 class="dp-panel-title">
                            <x-heroicon-o-exclamation-circle style="width: 1rem; height: 1rem; color: #fb7185;" />
                            Incidencias Técnicas Recientes
                        </h3>
                        <a href="/admin/incidencias" class="dp-panel-link">Ver tickets →</a>
                    </div>
                    <div>
                        @forelse($data['incidencias_recientes'] as $i)
                            <div class="dp-list-row" style="flex-direction: column; align-items: flex-start; gap: 0.25rem;">
                                <div style="display: flex; align-items: center; justify-content: space-between; width: 100%; font-size: 0.75rem;">
                                    <span style="font-weight: 600; color: #ffffff; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 75%;">
                                        {{ $i['titulo'] }}
                                    </span>
                                    <span style="padding: 0.12rem 0.4rem; border-radius: 0.25rem; font-size: 0.625rem; font-weight: 700;
                                        {{ $i['prioridad'] === 'Critica' ? 'background: rgba(244, 63, 94, 0.2); color: #fca5a5;' :
                                           ($i['prioridad'] === 'Alta' ? 'background: rgba(245, 158, 11, 0.2); color: #fde68a;' :
                                           'background: #1e293b; color: #cbd5e1;') }}">
                                        {{ $i['prioridad'] }}
                                    </span>
                                </div>
                                <p style="font-size: 0.6875rem; color: #94a3b8; margin: 0;">
                                    Cliente: <span style="color: #e2e8f0;">{{ $i['cliente_nombre'] }}</span> · Tipo: {{ $i['tipo'] }}
                                </p>
                            </div>
                        @empty
                            <div style="padding: 2rem 0; text-align: center; font-size: 0.75rem; color: #64748b; display: flex; flex-direction: column; align-items: center;">
                                <x-heroicon-o-check-circle style="width: 1.5rem; height: 1.5rem; color: #34d399; margin-bottom: 0.25rem;" />
                                <span>Sin incidencias reportadas. Todo en orden.</span>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- ---------- TAB 3: INVENTARIO ---------- --}}
        <div x-show="activeTab === 'inventario'" x-cloak style="display: flex; flex-direction: column; gap: 1.5rem;">
            <div class="dp-stat-grid-4">
                <div class="dp-stat-card">
                    <div class="dp-stat-top">
                        <span class="dp-stat-label">Total Items</span>
                        <div class="dp-stat-icon-box sky">
                            <x-heroicon-o-archive-box style="width: 1rem; height: 1rem;" />
                        </div>
                    </div>
                    <p class="dp-stat-num">{{ $data['items_total'] }}</p>
                    <p class="dp-stat-hint">En catálogo de inventario</p>
                </div>

                <div class="dp-stat-card">
                    <div class="dp-stat-top">
                        <span class="dp-stat-label">Stock Bajo</span>
                        <div class="dp-stat-icon-box rose">
                            <x-heroicon-o-exclamation-triangle style="width: 1rem; height: 1rem;" />
                        </div>
                    </div>
                    <p class="dp-stat-num" style="color: #fb7185;">{{ $data['stock_bajo_count'] }}</p>
                    <p class="dp-stat-hint">Requieren reposición inmediata</p>
                </div>

                <div class="dp-stat-card">
                    <div class="dp-stat-top">
                        <span class="dp-stat-label">Equipamiento</span>
                        <div class="dp-stat-icon-box purple">
                            <x-heroicon-o-cube style="width: 1rem; height: 1rem;" />
                        </div>
                    </div>
                    <p class="dp-stat-num" style="color: #c084fc;">{{ $data['equipamiento_stock'] }}</p>
                    <p class="dp-stat-hint">Unidades en almacén</p>
                </div>

                <div class="dp-stat-card">
                    <div class="dp-stat-top">
                        <span class="dp-stat-label">Materiales & Consumibles</span>
                        <div class="dp-stat-icon-box emerald">
                            <x-heroicon-o-squares-plus style="width: 1rem; height: 1rem;" />
                        </div>
                    </div>
                    <p class="dp-stat-num" style="color: #34d399;">{{ $data['materiales_stock'] }}</p>
                    <p class="dp-stat-hint">Unidades en stock</p>
                </div>
            </div>

            {{-- Panel Alertas de Stock Bajo --}}
            <div class="dp-panel">
                <div class="dp-panel-header">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <x-heroicon-s-exclamation-triangle style="width: 1.25rem; height: 1.25rem; color: #f43f5e;" />
                        <h3 class="dp-panel-title" style="margin: 0;">Alertas de Stock Bajo</h3>
                    </div>
                    <span style="border-radius: 9999px; background: rgba(244, 63, 94, 0.15); padding: 0.2rem 0.65rem; font-size: 0.75rem; font-weight: 700; color: #fb7185; border: 1px solid rgba(244, 63, 94, 0.25);">
                        {{ $data['stock_bajo_count'] }} ítems en umbral crítico
                    </span>
                </div>
                <div>
                    @forelse($data['alertas_stock'] as $item)
                        <div class="dp-alert-card">
                            <div>
                                <p style="font-weight: 600; font-size: 0.75rem; color: #ffffff; margin: 0;">{{ $item['nombre'] }}</p>
                                <p style="font-size: 0.6875rem; color: #94a3b8; font-family: monospace; margin: 0.2rem 0 0;">
                                    Código: {{ $item['codigo'] }} · Categoría: {{ $item['categoria_nombre'] }}
                                </p>
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div style="text-align: right;">
                                    <p style="font-size: 0.75rem; font-weight: 700; color: #fb7185; margin: 0;">
                                        {{ $item['stock_actual'] }} {{ $item['unidad_medida'] }} en stock
                                    </p>
                                    <p style="font-size: 0.625rem; color: #94a3b8; margin: 0.1rem 0 0;">Mínimo: {{ $item['stock_minimo'] }}</p>
                                </div>
                                <a href="/admin/inventario-movimientos/create" class="dp-btn-reponer">
                                    Reponer
                                </a>
                            </div>
                        </div>
                    @empty
                        <div style="padding: 2rem 0; text-align: center; font-size: 0.75rem; color: #64748b;">
                            ¡Todo el inventario está sobre los niveles mínimos requeridos!
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- ---------- TAB 4: FINANZAS ---------- --}}
        <div x-show="activeTab === 'finanzas'" x-cloak style="display: flex; flex-direction: column; gap: 1.5rem;">
            <div class="dp-stat-grid-4">
                <div class="dp-stat-card">
                    <span class="dp-stat-label">Presupuesto en Obras</span>
                    <p class="dp-stat-num">${{ number_format($data['presupuesto_total'], 2) }} CUP</p>
                    <p class="dp-stat-hint">Cartera de proyectos</p>
                </div>
                <div class="dp-stat-card">
                    <span class="dp-stat-label">Costo Equipamiento</span>
                    <p class="dp-stat-num" style="color: #34d399;">${{ number_format($data['presupuesto_equipamiento'], 2) }} CUP</p>
                    <p class="dp-stat-hint">Líneas de presupuesto</p>
                </div>
                <div class="dp-stat-card">
                    <span class="dp-stat-label">Costo Mano de Obra</span>
                    <p class="dp-stat-num" style="color: #fbbf24;">${{ number_format($data['presupuesto_mano_obra'], 2) }} CUP</p>
                    <p class="dp-stat-hint">Brigadas y técnicos</p>
                </div>
                <div class="dp-stat-card">
                    <span class="dp-stat-label">Inversión este Mes</span>
                    <p class="dp-stat-num" style="color: #38bdf8;">${{ number_format($data['finanzas_mes'], 2) }} CUP</p>
                    <p class="dp-stat-hint">Nuevas obras iniciadas</p>
                </div>
            </div>

            <div class="dp-panel">
                <div class="dp-panel-header">
                    <h3 class="dp-panel-title">
                        <x-heroicon-o-banknotes style="width: 1rem; height: 1rem; color: #34d399;" />
                        Desglose Presupuestario por Categoría
                    </h3>
                    <a href="/admin/proyectos" class="dp-panel-link">Ver presupuestos →</a>
                </div>
                @php
                    $totalLineas = ($data['presupuesto_equipamiento'] + $data['presupuesto_mano_obra'] + $data['presupuesto_materiales'] + $data['presupuesto_transporte']) ?: 1;
                    $catEquip = round(($data['presupuesto_equipamiento'] / $totalLineas) * 100);
                    $catMano = round(($data['presupuesto_mano_obra'] / $totalLineas) * 100);
                    $catMat = round(($data['presupuesto_materiales'] / $totalLineas) * 100);
                    $catTrans = round(($data['presupuesto_transporte'] / $totalLineas) * 100);
                @endphp
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div>
                        <div style="display: flex; align-items: center; justify-content: space-between; font-size: 0.75rem;">
                            <span style="font-weight: 600; color: #ffffff;">Equipamiento (Cámaras, Sensores, Enlaces)</span>
                            <span style="font-family: monospace; font-weight: 700; color: #34d399;">${{ number_format($data['presupuesto_equipamiento'], 2) }} CUP ({{ $catEquip }}%)</span>
                        </div>
                        <div class="dp-progress-bg">
                            <div class="dp-progress-bar" style="background: #10b981; width: {{ $catEquip }}%;"></div>
                        </div>
                    </div>

                    <div>
                        <div style="display: flex; align-items: center; justify-content: space-between; font-size: 0.75rem;">
                            <span style="font-weight: 600; color: #ffffff;">Mano de Obra & Instalaciones</span>
                            <span style="font-family: monospace; font-weight: 700; color: #fbbf24;">${{ number_format($data['presupuesto_mano_obra'], 2) }} CUP ({{ $catMano }}%)</span>
                        </div>
                        <div class="dp-progress-bg">
                            <div class="dp-progress-bar" style="background: #f59e0b; width: {{ $catMano }}%;"></div>
                        </div>
                    </div>

                    <div>
                        <div style="display: flex; align-items: center; justify-content: space-between; font-size: 0.75rem;">
                            <span style="font-weight: 600; color: #ffffff;">Materiales, Cables y Canalizaciones</span>
                            <span style="font-family: monospace; font-weight: 700; color: #38bdf8;">${{ number_format($data['presupuesto_materiales'], 2) }} CUP ({{ $catMat }}%)</span>
                        </div>
                        <div class="dp-progress-bg">
                            <div class="dp-progress-bar" style="background: #0ea5e9; width: {{ $catMat }}%;"></div>
                        </div>
                    </div>

                    <div>
                        <div style="display: flex; align-items: center; justify-content: space-between; font-size: 0.75rem;">
                            <span style="font-weight: 600; color: #ffffff;">Logística & Transporte</span>
                            <span style="font-family: monospace; font-weight: 700; color: #c084fc;">${{ number_format($data['presupuesto_transporte'], 2) }} CUP ({{ $catTrans }}%)</span>
                        </div>
                        <div class="dp-progress-bg">
                            <div class="dp-progress-bar" style="background: #8b5cf6; width: {{ $catTrans }}%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ---------- TAB 5: ADMINISTRACIÓN ---------- --}}
        <div x-show="activeTab === 'administracion'" x-cloak style="display: flex; flex-direction: column; gap: 1.5rem;">
            <div class="dp-stat-grid-3">
                <div class="dp-stat-card">
                    <span class="dp-stat-label">Total Clientes en Sistema</span>
                    <p class="dp-stat-num" style="color: #38bdf8;">{{ $data['clientes_total'] }}</p>
                    <p class="dp-stat-hint">Portafolio oficial DataPlus</p>
                </div>
                <div class="dp-stat-card">
                    <span class="dp-stat-label">Usuarios con Acceso</span>
                    <p class="dp-stat-num" style="color: #34d399;">{{ $data['usuarios_total'] }}</p>
                    <p class="dp-stat-hint">Autenticación híbrida LDAP + Local</p>
                </div>
                <div class="dp-stat-card">
                    <span class="dp-stat-label">Directorio LDAP / LLDAP</span>
                    <p class="dp-stat-num" style="color: #c084fc; font-size: 1.15rem;">dc=dataplus,dc=cu</p>
                    <p class="dp-stat-hint">Servicio online y enlazado</p>
                </div>
            </div>

            <div class="dp-panel">
                <div class="dp-panel-header" style="margin-bottom: 1.25rem;">
                    <h3 class="dp-panel-title">
                        <x-heroicon-o-shield-check style="width: 1rem; height: 1rem; color: #38bdf8;" />
                        Accesos Rápidos de Administración
                    </h3>
                </div>
                <div style="display: grid; grid-template-columns: repeat(1, minmax(0, 1fr)); gap: 0.75rem;">
                    <a href="/admin/clientes" class="dp-quick-link">
                        <div>
                            <p style="font-size: 0.75rem; font-weight: 700; color: #ffffff; margin: 0;">Directorio de Clientes</p>
                            <p style="font-size: 0.625rem; color: #94a3b8; margin: 0.15rem 0 0;">{{ $data['clientes_total'] }} empresas registradas</p>
                        </div>
                        <x-heroicon-o-chevron-right style="width: 1rem; height: 1rem; color: #94a3b8;" />
                    </a>
                    <a href="/admin/users" class="dp-quick-link">
                        <div>
                            <p style="font-size: 0.75rem; font-weight: 700; color: #ffffff; margin: 0;">Usuarios & Permisos</p>
                            <p style="font-size: 0.625rem; color: #94a3b8; margin: 0.15rem 0 0;">Roles RBAC y cuentas del sistema</p>
                        </div>
                        <x-heroicon-o-chevron-right style="width: 1rem; height: 1rem; color: #94a3b8;" />
                    </a>
                    <a href="/admin/ldap-configurations" class="dp-quick-link">
                        <div>
                            <p style="font-size: 0.75rem; font-weight: 700; color: #ffffff; margin: 0;">Configuración LDAP</p>
                            <p style="font-size: 0.625rem; color: #94a3b8; margin: 0.15rem 0 0;">Parámetros de conexión LLDAP</p>
                        </div>
                        <x-heroicon-o-chevron-right style="width: 1rem; height: 1rem; color: #94a3b8;" />
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
