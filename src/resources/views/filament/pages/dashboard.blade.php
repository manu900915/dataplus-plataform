<x-filament-panels::page>
    @php
        /** @var array $data */
        $data = $this->getData();

        $tabs = [
            'operaciones'    => ['label' => 'Operaciones & Incidencias', 'icon' => 'heroicon-o-cpu-chip'],
            'proyectos'      => ['label' => 'Proyectos & Obras',         'icon' => 'heroicon-o-briefcase'],
            'inventario'     => ['label' => 'Inventario & Almacén',      'icon' => 'heroicon-o-archive-box'],
            'clientes'       => ['label' => 'Clientes & Sedes',          'icon' => 'heroicon-o-building-office-2'],
            'administracion' => ['label' => 'Administración',            'icon' => 'heroicon-o-shield-check'],
        ];
    @endphp

    {{-- ============================================================
         ESTILOS AUTO-CONTENIDOS CON SEPARACIÓN AMPLIA Y ACABADO EJECUTIVO
         ============================================================ --}}
    <style>
        [x-cloak] { display: none !important; }

        .dp-dash-wrap {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #f1f5f9;
            user-select: none;
            width: 100%;
        }

        /* ─── Hero Banner DataPlus ─── */
        .dp-hero {
            position: relative;
            overflow: hidden;
            border-radius: 1.25rem;
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 50%, #0c4a6e 100%);
            padding: 2rem 2.25rem;
            color: #ffffff;
            box-shadow: 0 20px 25px -5px rgba(8, 47, 73, 0.35);
            margin-bottom: 2rem !important;
        }
        .dp-hero-content {
            position: relative;
            z-index: 10;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
            justify-content: space-between;
        }
        @media (min-width: 900px) {
            .dp-hero-content {
                flex-direction: row;
                align-items: center;
            }
        }
        .dp-hero-glow {
            position: absolute;
            right: -3rem;
            bottom: -3rem;
            width: 18rem;
            height: 18rem;
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.14);
            filter: blur(48px);
            pointer-events: none;
        }
        .dp-hero-tag {
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: rgba(224, 242, 254, 0.9);
            margin: 0;
        }
        .dp-hero-title {
            margin-top: 0.35rem;
            font-size: 1.85rem;
            font-weight: 800;
            letter-spacing: -0.025em;
            color: #ffffff;
            line-height: 1.2;
        }
        .dp-hero-sub {
            margin-top: 0.5rem;
            font-size: 0.875rem;
            color: rgba(224, 242, 254, 0.88);
            font-weight: 500;
        }
        .dp-hero-pills {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            flex-shrink: 0;
            flex-wrap: wrap;
        }
        .dp-hero-pill {
            border-radius: 0.85rem;
            background: rgba(255, 255, 255, 0.12);
            padding: 0.75rem 1.15rem;
            border: 1px solid rgba(255, 255, 255, 0.22);
            backdrop-filter: blur(10px);
        }

        /* ─── Ribbon Financiero Consolidado (Dinero Generado por Cualquier Concepto) ─── */
        .dp-finance-bar {
            border-radius: 1rem;
            border: 1px solid rgba(56, 189, 248, 0.3);
            background: linear-gradient(90deg, rgba(15, 23, 42, 0.95) 0%, rgba(8, 47, 73, 0.6) 100%);
            padding: 1.25rem 1.5rem;
            margin-bottom: 2rem !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
        }
        .dp-finance-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1.25rem;
        }
        @media (min-width: 768px) {
            .dp-finance-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        }
        .dp-finance-item {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }
        .dp-finance-item-label {
            font-size: 0.725rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #94a3b8;
        }
        .dp-finance-item-val {
            font-size: 1.35rem;
            font-weight: 800;
            color: #ffffff;
            font-family: monospace;
            line-height: 1.1;
        }
        .dp-finance-item-hint {
            font-size: 0.675rem;
            color: #64748b;
        }

        /* ─── Pestañas de Navegación ─── */
        .dp-tabs-box {
            border-radius: 0.85rem;
            border: 1px solid #1e293b;
            background: rgba(15, 23, 42, 0.9);
            padding: 0.45rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2);
            margin-bottom: 2.25rem !important; /* Separación garantizada de 36px */
        }
        .dp-tabs-nav {
            display: flex;
            gap: 0.5rem;
            overflow-x: auto;
        }
        .dp-tab-item {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            white-space: nowrap;
            border-radius: 0.6rem;
            padding: 0.65rem 1.25rem;
            font-size: 0.8rem;
            font-weight: 700;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            border: none;
            background: transparent;
            color: #94a3b8;
        }
        .dp-tab-item:hover {
            background: rgba(30, 41, 59, 0.85);
            color: #f1f5f9;
        }
        .dp-tab-item.dp-tab-active {
            background: linear-gradient(90deg, #0ea5e9 0%, #0284c7 100%) !important;
            color: #ffffff !important;
            box-shadow: 0 4px 14px rgba(14, 165, 233, 0.4);
        }

        /* ─── Grids de Tarjetas KPI Superiores (Separación 36px) ─── */
        .dp-stat-grid-4 {
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: 1.25rem;
            margin-bottom: 2.25rem !important; /* 👈 SEPARA 36px DE LOS PANELES DE ABAJO */
        }
        @media (min-width: 640px) {
            .dp-stat-grid-4 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (min-width: 1024px) {
            .dp-stat-grid-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        }

        .dp-stat-grid-3 {
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: 1.25rem;
            margin-bottom: 2.25rem !important; /* 👈 SEPARA 36px DE LOS PANELES DE ABAJO */
        }
        @media (min-width: 640px) {
            .dp-stat-grid-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        }

        .dp-stat-card {
            border-radius: 1rem;
            border: 1px solid #1e293b;
            background: rgba(15, 23, 42, 0.85);
            padding: 1.35rem 1.5rem;
            transition: all 0.2s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.15);
        }
        .dp-stat-card:hover {
            border-color: rgba(56, 189, 248, 0.45);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.4);
        }
        .dp-stat-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .dp-stat-label {
            font-size: 0.775rem;
            font-weight: 600;
            color: #94a3b8;
        }
        .dp-stat-icon-box {
            border-radius: 0.6rem;
            padding: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .dp-stat-icon-box.sky {
            background: rgba(14, 165, 233, 0.12);
            color: #38bdf8;
            border: 1px solid rgba(56, 189, 248, 0.25);
        }
        .dp-stat-icon-box.emerald {
            background: rgba(16, 185, 129, 0.12);
            color: #34d399;
            border: 1px solid rgba(52, 211, 153, 0.25);
        }
        .dp-stat-icon-box.rose {
            background: rgba(244, 63, 94, 0.12);
            color: #fb7185;
            border: 1px solid rgba(251, 113, 133, 0.25);
        }
        .dp-stat-icon-box.amber {
            background: rgba(245, 158, 11, 0.12);
            color: #fbbf24;
            border: 1px solid rgba(251, 191, 36, 0.25);
        }
        .dp-stat-icon-box.purple {
            background: rgba(168, 85, 247, 0.12);
            color: #c084fc;
            border: 1px solid rgba(192, 132, 252, 0.25);
        }
        .dp-stat-num {
            font-size: 1.65rem;
            font-weight: 800;
            color: #ffffff;
            margin-top: 0.6rem;
            line-height: 1.15;
            letter-spacing: -0.02em;
        }
        .dp-stat-hint {
            font-size: 0.7rem;
            color: #94a3b8;
            margin-top: 0.35rem;
        }

        /* ─── Paneles Inferiores de 2 Columnas ─── */
        .dp-panel-grid-2 {
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: 1.75rem !important;
            margin-bottom: 2rem !important;
        }
        @media (min-width: 1024px) {
            .dp-panel-grid-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        .dp-panel {
            border-radius: 1rem;
            border: 1px solid #1e293b;
            background: rgba(15, 23, 42, 0.85);
            padding: 1.65rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.15);
        }
        .dp-panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.35rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid rgba(30, 41, 59, 0.8);
        }
        .dp-panel-title {
            font-size: 0.9rem;
            font-weight: 700;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0;
        }
        .dp-panel-link {
            font-size: 0.75rem;
            color: #38bdf8;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: color 0.15s ease;
        }
        .dp-panel-link:hover {
            color: #7dd3fc;
            text-decoration: underline;
        }

        /* ─── Filas de Listas ─── */
        .dp-list-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem 0.95rem;
            border-radius: 0.75rem;
            background: rgba(2, 6, 23, 0.65);
            border: 1px solid rgba(30, 41, 59, 0.85);
            transition: all 0.2s ease;
            margin-bottom: 0.75rem;
        }
        .dp-list-row:last-child {
            margin-bottom: 0;
        }
        .dp-list-row:hover {
            border-color: rgba(56, 189, 248, 0.4);
            transform: translateX(3px);
            background: rgba(2, 6, 23, 0.85);
        }

        /* ─── Barras de Progreso ─── */
        .dp-progress-bg {
            height: 0.55rem;
            width: 100%;
            border-radius: 9999px;
            background: #1e293b;
            overflow: hidden;
            margin-top: 0.45rem;
        }
        .dp-progress-bar {
            height: 100%;
            border-radius: 9999px;
            background: linear-gradient(90deg, #38bdf8 0%, #2563eb 100%);
            transition: width 0.7s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* ─── Alertas de Stock Bajo ─── */
        .dp-alert-card {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 0.85rem 1.15rem;
            border-radius: 0.75rem;
            border: 1px solid rgba(244, 63, 94, 0.35);
            background: rgba(136, 19, 55, 0.18);
            gap: 0.85rem;
            margin-bottom: 0.85rem;
        }
        .dp-alert-card:last-child {
            margin-bottom: 0;
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
            padding: 0.4rem 0.85rem;
            font-size: 0.75rem;
            font-weight: 700;
            text-decoration: none;
            transition: background-color 0.2s ease, transform 0.15s ease;
            display: inline-block;
        }
        .dp-btn-reponer:hover {
            background: #fb7185;
            color: #020617;
            transform: scale(1.03);
        }

        /* ─── Micro-boxes de Servicios Técnicos ─── */
        .dp-service-box {
            border-radius: 0.75rem;
            border: 1px solid #1e293b;
            background: #020617;
            padding: 0.85rem 0.5rem;
            text-align: center;
        }
        .dp-service-icon {
            margin: 0 auto 0.35rem;
            width: 1.35rem;
            height: 1.35rem;
        }

        /* ─── Enlaces Rápidos Administración ─── */
        .dp-quick-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.15rem;
            border-radius: 0.75rem;
            border: 1px solid #1e293b;
            background: rgba(2, 6, 23, 0.7);
            text-decoration: none;
            transition: all 0.2s ease;
            margin-bottom: 0.75rem;
        }
        .dp-quick-link:last-child {
            margin-bottom: 0;
        }
        .dp-quick-link:hover {
            border-color: rgba(56, 189, 248, 0.45);
            transform: translateX(3px);
            background: rgba(2, 6, 23, 0.9);
        }
    </style>

    <div class="dp-dash-wrap"
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

        {{-- ================= HERO BANNER EJECUTIVO CON DINERO TOTAL GENERADO ================= --}}
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
                        DataPlus Platform · {{ $data['servicios_total'] }} servicios en campo · 
                        {{ $data['incidencias_abiertas'] }} incidencias activas · 
                        {{ $data['proyectos_activos'] }} proyectos en curso · 
                        Gastos especialista: ${{ number_format($data['gastos_campo_total'], 2) }} CUP
                    </p>
                </div>
                <div class="dp-hero-pills">
                    {{-- Dinero Total Generado por cualquier concepto --}}
                    <div class="dp-hero-pill" style="border-color: rgba(56, 189, 248, 0.5); background: rgba(8, 47, 73, 0.45);">
                        <p style="font-size: 0.625rem; text-transform: uppercase; font-weight: 700; color: #7dd3fc;">Dinero Total Generado</p>
                        <p style="font-size: 1.35rem; font-weight: 800; color: #ffffff; line-height: 1;">
                            ${{ number_format($data['dinero_generado_total'], 2) }} <span style="font-size: 0.8rem; font-weight: 600; color: #7dd3fc;">CUP</span>
                        </p>
                    </div>

                    {{-- Gastos de campo del especialista (Transporte + Almuerzo) --}}
                    @if($data['gastos_campo_total'] > 0)
                        <div class="dp-hero-pill" style="border-color: rgba(251, 191, 36, 0.4); background: rgba(120, 53, 15, 0.25);">
                            <p style="font-size: 0.625rem; text-transform: uppercase; font-weight: 700; color: #fde68a;">Gastos de Campo</p>
                            <p style="font-size: 1.25rem; font-weight: 800; color: #fbbf24; line-height: 1;">
                                ${{ number_format($data['gastos_campo_total'], 2) }}
                            </p>
                        </div>
                    @endif

                    @if($data['incidencias_criticas'] > 0)
                        <div class="dp-hero-pill" style="border-color: rgba(244, 63, 94, 0.4); background: rgba(136, 19, 55, 0.35);">
                            <p style="font-size: 0.625rem; text-transform: uppercase; font-weight: 700; color: #fca5a5;">SLA Crítico</p>
                            <p style="font-size: 1.25rem; font-weight: 800; color: #ffffff; line-height: 1;">{{ $data['incidencias_criticas'] }}</p>
                        </div>
                    @endif
                </div>
            </div>
            <div class="dp-hero-glow"></div>
        </div>

        {{-- ================= DESGLOSE FINANCIERO: DINERO POR CUALQUIER CONCEPTO ================= --}}
        <div class="dp-finance-bar">
            <div class="dp-finance-grid">
                <div class="dp-finance-item">
                    <span class="dp-finance-item-label">Obras & Proyectos</span>
                    <span class="dp-finance-item-val" style="color: #38bdf8;">${{ number_format($data['presupuesto_total'], 2) }}</span>
                    <span class="dp-finance-item-hint">Presupuestos de proyectos contratados</span>
                </div>
                <div class="dp-finance-item">
                    <span class="dp-finance-item-label">Solicitudes Comerciales</span>
                    <span class="dp-finance-item-val" style="color: #34d399;">${{ number_format($data['solicitudes_monto'], 2) }}</span>
                    <span class="dp-finance-item-hint">Requerimientos aprobados en cartera</span>
                </div>
                <div class="dp-finance-item">
                    <span class="dp-finance-item-label">Servicios & Asistencias</span>
                    <span class="dp-finance-item-val" style="color: #c084fc;">${{ number_format($data['ingresos_servicios_total'], 2) }}</span>
                    <span class="dp-finance-item-hint">Facturado por incidencias y gestión 4G</span>
                </div>
                <div class="dp-finance-item">
                    <span class="dp-finance-item-label">Gastos Especialista</span>
                    <span class="dp-finance-item-val" style="color: #fbbf24;">-${{ number_format($data['gastos_campo_total'], 2) }}</span>
                    <span class="dp-finance-item-hint">Transporte: ${{ number_format($data['gastos_transporte_total'], 2) }} · Almuerzo: ${{ number_format($data['gastos_almuerzo_total'], 2) }}</span>
                </div>
            </div>
        </div>

        {{-- ================= BARRA DE PESTAÑAS (TABS) ================= --}}
        <div class="dp-tabs-box">
            <nav class="dp-tabs-nav" role="tablist">
                @foreach($tabs as $key => $tab)
                    <button type="button"
                            role="tab"
                            @click="setTab('{{ $key }}')"
                            :class="activeTab === '{{ $key }}' ? 'dp-tab-active' : ''"
                            class="dp-tab-item">
                        <x-dynamic-component :component="$tab['icon']" style="width: 1.05rem; height: 1.05rem;" />
                        <span>{{ $tab['label'] }}</span>
                    </button>
                @endforeach
            </nav>
        </div>

        {{-- ============================================================
             SECCIÓN 1: OPERACIONES (Servicios, Incidencias, Gastos de Campo)
             ============================================================ --}}
        <div x-show="activeTab === 'operaciones'" x-cloak style="display: block;">
            <div class="dp-stat-grid-4">
                <div class="dp-stat-card">
                    <div class="dp-stat-top">
                        <span class="dp-stat-label">Brigadas Activas</span>
                        <div class="dp-stat-icon-box purple">
                            <x-heroicon-o-user-group style="width: 1.1rem; height: 1.1rem;" />
                        </div>
                    </div>
                    <p class="dp-stat-num" style="color: #c084fc;">{{ $data['brigadas_count'] }}</p>
                    <p class="dp-stat-hint">Cuadrillas operativas en campo</p>
                </div>

                <div class="dp-stat-card">
                    <div class="dp-stat-top">
                        <span class="dp-stat-label">Servicios Instalados</span>
                        <div class="dp-stat-icon-box sky">
                            <x-heroicon-o-radio style="width: 1.1rem; height: 1.1rem;" />
                        </div>
                    </div>
                    <p class="dp-stat-num" style="color: #38bdf8;">{{ $data['servicios_total'] }}</p>
                    <p class="dp-stat-hint">CCTV, SACI y Gestión Remota 4G</p>
                </div>

                <div class="dp-stat-card">
                    <div class="dp-stat-top">
                        <span class="dp-stat-label">Incidencias Activas</span>
                        <div class="dp-stat-icon-box rose">
                            <x-heroicon-o-wrench-screwdriver style="width: 1.1rem; height: 1.1rem;" />
                        </div>
                    </div>
                    <p class="dp-stat-num" style="color: #fb7185;">{{ $data['incidencias_abiertas'] }}</p>
                    <p class="dp-stat-hint">En atención o espera técnica</p>
                </div>

                {{-- Gasto Especialistas (Transporte y Almuerzo) --}}
                <div class="dp-stat-card">
                    <div class="dp-stat-top">
                        <span class="dp-stat-label">Gastos Especialistas</span>
                        <div class="dp-stat-icon-box amber">
                            <x-heroicon-o-banknotes style="width: 1.1rem; height: 1.1rem;" />
                        </div>
                    </div>
                    <p class="dp-stat-num" style="color: #fbbf24;">${{ number_format($data['gastos_campo_total'], 2) }}</p>
                    <p class="dp-stat-hint">
                        Transporte: ${{ number_format($data['gastos_transporte_total'], 2) }} · Almuerzo: ${{ number_format($data['gastos_almuerzo_total'], 2) }}
                    </p>
                </div>
            </div>

            {{-- Paneles Inferiores (Servicios Técnicos e Incidencias Recientes con Gastos) --}}
            <div class="dp-panel-grid-2">
                {{-- Parque de Servicios Técnicos --}}
                <div class="dp-panel">
                    <div class="dp-panel-header">
                        <h3 class="dp-panel-title">
                            <x-heroicon-o-radio style="width: 1.1rem; height: 1.1rem; color: #38bdf8;" />
                            Parque de Servicios Técnicos
                        </h3>
                        <a href="/admin/servicios" class="dp-panel-link">Ver servicios →</a>
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 0.85rem; margin-bottom: 1.25rem;">
                        <div class="dp-service-box">
                            <x-heroicon-o-video-camera class="dp-service-icon" style="color: #60a5fa;" />
                            <p style="font-size: 1.35rem; font-weight: 800; color: #ffffff; margin: 0; line-height: 1;">{{ $data['servicios_cctv'] }}</p>
                            <p style="font-size: 0.65rem; color: #94a3b8; text-transform: uppercase; font-weight: 600; margin: 0.35rem 0 0;">CCTV</p>
                        </div>
                        <div class="dp-service-box">
                            <x-heroicon-o-shield-check class="dp-service-icon" style="color: #34d399;" />
                            <p style="font-size: 1.35rem; font-weight: 800; color: #ffffff; margin: 0; line-height: 1;">{{ $data['servicios_saci'] }}</p>
                            <p style="font-size: 0.65rem; color: #94a3b8; text-transform: uppercase; font-weight: 600; margin: 0.35rem 0 0;">Alarmas SACI</p>
                        </div>
                        <div class="dp-service-box">
                            <x-heroicon-o-signal class="dp-service-icon" style="color: #fbbf24;" />
                            <p style="font-size: 1.35rem; font-weight: 800; color: #ffffff; margin: 0; line-height: 1;">{{ $data['servicios_gr'] }}</p>
                            <p style="font-size: 0.65rem; color: #94a3b8; text-transform: uppercase; font-weight: 600; margin: 0.35rem 0 0;">Gestión 4G</p>
                        </div>
                    </div>
                    <div>
                        @forelse($data['servicios_recientes'] as $s)
                            <div class="dp-list-row">
                                <div style="min-width: 0; flex: 1; padding-right: 0.5rem;">
                                    <p style="font-size: 0.775rem; font-weight: 600; color: #ffffff; margin: 0;">{{ $s['cliente_nombre'] }}</p>
                                    <p style="font-size: 0.65rem; color: #94a3b8; margin: 0.25rem 0 0;">
                                        {{ $s['tipo'] }} · {{ $s['detalle'] }}
                                    </p>
                                </div>
                                <span style="font-family: monospace; font-size: 0.65rem; color: #38bdf8; background: rgba(8, 47, 73, 0.45); padding: 0.2rem 0.55rem; border-radius: 0.35rem; border: 1px solid rgba(56, 189, 248, 0.25);">
                                    {{ $s['codigo'] }}
                                </span>
                            </div>
                        @empty
                            <div style="padding: 2.5rem 0; text-align: center; font-size: 0.75rem; color: #64748b;">
                                No hay servicios técnicos registrados en campo.
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Incidencias Técnicas Recientes (Con reflejo explícito de gastos) --}}
                <div class="dp-panel">
                    <div class="dp-panel-header">
                        <h3 class="dp-panel-title">
                            <x-heroicon-o-exclamation-circle style="width: 1.1rem; height: 1.1rem; color: #fb7185;" />
                            Incidencias & Gastos de Especialistas
                        </h3>
                        <a href="/admin/incidencias" class="dp-panel-link">Ver tickets →</a>
                    </div>
                    <div>
                        @forelse($data['incidencias_recientes'] as $i)
                            <div class="dp-list-row" style="flex-direction: column; align-items: flex-start; gap: 0.35rem;">
                                <div style="display: flex; align-items: center; justify-content: space-between; width: 100%; font-size: 0.775rem;">
                                    <span style="font-weight: 600; color: #ffffff; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 70%;">
                                        {{ $i['titulo'] }}
                                    </span>
                                    <div style="display: flex; align-items: center; gap: 0.4rem;">
                                        @if($i['total_gastos'] > 0)
                                            <span style="font-size: 0.65rem; font-weight: 700; color: #fbbf24; background: rgba(245, 158, 11, 0.15); padding: 0.15rem 0.45rem; border-radius: 0.35rem; border: 1px solid rgba(245, 158, 11, 0.25);">
                                                Gastos: ${{ number_format($i['total_gastos'], 2) }}
                                            </span>
                                        @endif
                                        <span style="padding: 0.15rem 0.45rem; border-radius: 0.35rem; font-size: 0.65rem; font-weight: 700;
                                            {{ $i['prioridad'] === 'Critica' ? 'background: rgba(244, 63, 94, 0.22); color: #fca5a5; border: 1px solid rgba(244, 63, 94, 0.35);' :
                                               ($i['prioridad'] === 'Alta' ? 'background: rgba(245, 158, 11, 0.22); color: #fde68a; border: 1px solid rgba(245, 158, 11, 0.35);' :
                                               'background: #1e293b; color: #cbd5e1;') }}">
                                            {{ $i['prioridad'] }}
                                        </span>
                                    </div>
                                </div>
                                <div style="display: flex; align-items: center; justify-content: space-between; width: 100%; font-size: 0.7rem; color: #94a3b8; margin: 0;">
                                    <span>
                                        <span style="font-family: monospace; color: #38bdf8;">{{ $i['codigo'] }}</span> · 
                                        Cliente: <span style="color: #e2e8f0;">{{ $i['cliente_nombre'] }}</span>
                                    </span>
                                    @if($i['total_gastos'] > 0)
                                        <span style="color: #fde68a; font-size: 0.65rem;">
                                            Trans: ${{ number_format($i['gasto_transporte'], 2) }} · Alm: ${{ number_format($i['gasto_almuerzo'], 2) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div style="padding: 2.5rem 0; text-align: center; font-size: 0.75rem; color: #64748b; display: flex; flex-direction: column; align-items: center;">
                                <x-heroicon-o-check-circle style="width: 1.75rem; height: 1.75rem; color: #34d399; margin-bottom: 0.35rem;" />
                                <span>Sin incidencias abiertas. Todo el parque en orden.</span>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================
             SECCIÓN 2: PROYECTOS (Proyectos, Solicitudes Comerciales)
             ============================================================ --}}
        <div x-show="activeTab === 'proyectos'" x-cloak style="display: block;">
            <div class="dp-stat-grid-4">
                <div class="dp-stat-card">
                    <div class="dp-stat-top">
                        <span class="dp-stat-label">Proyectos en Curso</span>
                        <div class="dp-stat-icon-box sky">
                            <x-heroicon-o-briefcase style="width: 1.1rem; height: 1.1rem;" />
                        </div>
                    </div>
                    <p class="dp-stat-num">{{ $data['proyectos_activos'] }}</p>
                    <p class="dp-stat-hint">En borrador o ejecución activa</p>
                </div>

                <div class="dp-stat-card">
                    <div class="dp-stat-top">
                        <span class="dp-stat-label">Solicitudes Comerciales</span>
                        <div class="dp-stat-icon-box amber">
                            <x-heroicon-o-document-plus style="width: 1.1rem; height: 1.1rem;" />
                        </div>
                    </div>
                    <p class="dp-stat-num" style="color: #fbbf24;">{{ $data['solicitudes_pendientes'] }}</p>
                    <p class="dp-stat-hint">Esperando evaluación comercial</p>
                </div>

                <div class="dp-stat-card">
                    <div class="dp-stat-top">
                        <span class="dp-stat-label">Obras Completadas</span>
                        <div class="dp-stat-icon-box emerald">
                            <x-heroicon-o-check-circle style="width: 1.1rem; height: 1.1rem;" />
                        </div>
                    </div>
                    <p class="dp-stat-num" style="color: #34d399;">{{ $data['proyectos_completados'] }}</p>
                    <p class="dp-stat-hint">Entrega satisfactoria a cliente</p>
                </div>

                <div class="dp-stat-card">
                    <div class="dp-stat-top">
                        <span class="dp-stat-label">Cartera Presupuestaria</span>
                        <div class="dp-stat-icon-box purple">
                            <x-heroicon-o-banknotes style="width: 1.1rem; height: 1.1rem;" />
                        </div>
                    </div>
                    <p class="dp-stat-num" style="color: #c084fc;">${{ number_format($data['presupuesto_total'], 2) }} CUP</p>
                    <p class="dp-stat-hint">Total acumulado de obras</p>
                </div>
            </div>

            <div class="dp-panel-grid-2">
                {{-- Top Proyectos por Presupuesto --}}
                <div class="dp-panel">
                    <div class="dp-panel-header">
                        <h3 class="dp-panel-title">
                            <x-heroicon-o-briefcase style="width: 1.1rem; height: 1.1rem; color: #38bdf8;" />
                            Top Proyectos por Presupuesto
                        </h3>
                        <a href="/admin/proyectos" class="dp-panel-link">Ver proyectos →</a>
                    </div>
                    <div>
                        @forelse($data['top_proyectos'] as $idx => $p)
                            @php
                                $pct = round(($p['presupuesto_total'] / $data['max_presupuesto']) * 100);
                            @endphp
                            <div style="margin-bottom: 1.15rem;">
                                <div style="display: flex; align-items: center; justify-content: space-between; font-size: 0.775rem;">
                                    <span style="font-weight: 600; color: #ffffff; display: flex; align-items: center; gap: 0.5rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 70%;">
                                        <span style="display: flex; width: 1.35rem; height: 1.35rem; align-items: center; justify-content: center; border-radius: 0.35rem; background: rgba(14, 165, 233, 0.2); font-size: 0.65rem; font-weight: 700; color: #7dd3fc; flex-shrink: 0;">
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
                            <div style="padding: 2.5rem 0; text-align: center; font-size: 0.75rem; color: #64748b;">
                                No hay proyectos registrados aún.
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Solicitudes Comerciales Recientes --}}
                <div class="dp-panel">
                    <div class="dp-panel-header">
                        <h3 class="dp-panel-title">
                            <x-heroicon-o-document-plus style="width: 1.1rem; height: 1.1rem; color: #fbbf24;" />
                            Solicitudes Comerciales Recientes
                        </h3>
                        <a href="/admin/solicitud-servicios" class="dp-panel-link">Ver solicitudes →</a>
                    </div>
                    <div>
                        @forelse($data['solicitudes_recientes'] as $s)
                            <div class="dp-list-row">
                                <div style="min-width: 0; flex: 1; padding-right: 0.5rem;">
                                    <p style="font-size: 0.775rem; font-weight: 600; color: #ffffff; margin: 0;">{{ $s['titulo'] }}</p>
                                    <p style="font-size: 0.65rem; color: #94a3b8; margin: 0.25rem 0 0;">
                                        <span style="font-family: monospace; color: #fbbf24;">{{ $s['codigo'] }}</span> · Cliente: {{ $s['cliente'] }}
                                    </p>
                                </div>
                                <div style="text-align: right; flex-shrink: 0;">
                                    @if($s['presupuesto'] > 0)
                                        <p style="font-size: 0.75rem; font-weight: 700; color: #38bdf8; margin: 0;">${{ number_format($s['presupuesto'], 2) }}</p>
                                    @endif
                                    <span style="padding: 0.15rem 0.45rem; border-radius: 0.35rem; font-size: 0.625rem; font-weight: 700; text-transform: uppercase;
                                        {{ $s['estado'] === 'Pendiente_Aprobacion' ? 'background: rgba(245, 158, 11, 0.2); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3);' :
                                           'background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid rgba(52, 211, 153, 0.3);' }}">
                                        {{ $s['estado'] === 'Pendiente_Aprobacion' ? 'Por Aprobar' : 'Aprobada' }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div style="padding: 2.5rem 0; text-align: center; font-size: 0.75rem; color: #64748b;">
                                No hay solicitudes comerciales registradas.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================
             SECCIÓN 3: INVENTARIO (Items, Stock Bajo, Almacenes)
             ============================================================ --}}
        <div x-show="activeTab === 'inventario'" x-cloak style="display: block;">
            <div class="dp-stat-grid-4">
                <div class="dp-stat-card">
                    <div class="dp-stat-top">
                        <span class="dp-stat-label">Total Artículos</span>
                        <div class="dp-stat-icon-box sky">
                            <x-heroicon-o-archive-box style="width: 1.1rem; height: 1.1rem;" />
                        </div>
                    </div>
                    <p class="dp-stat-num">{{ $data['items_total'] }}</p>
                    <p class="dp-stat-hint">En catálogo de inventario</p>
                </div>

                <div class="dp-stat-card">
                    <div class="dp-stat-top">
                        <span class="dp-stat-label">Stock Bajo</span>
                        <div class="dp-stat-icon-box rose">
                            <x-heroicon-o-exclamation-triangle style="width: 1.1rem; height: 1.1rem;" />
                        </div>
                    </div>
                    <p class="dp-stat-num" style="color: #fb7185;">{{ $data['stock_bajo_count'] }}</p>
                    <p class="dp-stat-hint">Requieren reposición inmediata</p>
                </div>

                <div class="dp-stat-card">
                    <div class="dp-stat-top">
                        <span class="dp-stat-label">Valor en Almacén</span>
                        <div class="dp-stat-icon-box emerald">
                            <x-heroicon-o-currency-dollar style="width: 1.1rem; height: 1.1rem;" />
                        </div>
                    </div>
                    <p class="dp-stat-num" style="color: #34d399;">${{ number_format($data['valor_inventario_total'], 2) }}</p>
                    <p class="dp-stat-hint">Capital en existencias físicas</p>
                </div>

                <div class="dp-stat-card">
                    <div class="dp-stat-top">
                        <span class="dp-stat-label">Almacenes Físicos</span>
                        <div class="dp-stat-icon-box purple">
                            <x-heroicon-o-building-storefront style="width: 1.1rem; height: 1.1rem;" />
                        </div>
                    </div>
                    <p class="dp-stat-num" style="color: #c084fc;">{{ $data['almacenes_total'] }}</p>
                    <p class="dp-stat-hint">Puntos de almacenamiento</p>
                </div>
            </div>

            <div class="dp-panel-grid-2">
                {{-- Alertas de Stock Bajo --}}
                <div class="dp-panel">
                    <div class="dp-panel-header">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <x-heroicon-s-exclamation-triangle style="width: 1.25rem; height: 1.25rem; color: #f43f5e;" />
                            <h3 class="dp-panel-title" style="margin: 0;">Alertas de Stock Crítico</h3>
                        </div>
                        <span style="border-radius: 9999px; background: rgba(244, 63, 94, 0.15); padding: 0.2rem 0.65rem; font-size: 0.725rem; font-weight: 700; color: #fb7185; border: 1px solid rgba(244, 63, 94, 0.25);">
                            {{ $data['stock_bajo_count'] }} ítems
                        </span>
                    </div>
                    <div>
                        @forelse($data['alertas_stock'] as $item)
                            <div class="dp-alert-card">
                                <div>
                                    <p style="font-weight: 600; font-size: 0.775rem; color: #ffffff; margin: 0;">{{ $item['nombre'] }}</p>
                                    <p style="font-size: 0.675rem; color: #94a3b8; font-family: monospace; margin: 0.25rem 0 0;">
                                        Código: {{ $item['codigo'] }} · Categoría: {{ $item['categoria_nombre'] }}
                                    </p>
                                </div>
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div style="text-align: right;">
                                        <p style="font-size: 0.775rem; font-weight: 700; color: #fb7185; margin: 0;">
                                            {{ $item['stock_actual'] }} {{ $item['unidad_medida'] }}
                                        </p>
                                        <p style="font-size: 0.625rem; color: #94a3b8; margin: 0.1rem 0 0;">Mínimo: {{ $item['stock_minimo'] }}</p>
                                    </div>
                                    <a href="/admin/inventario-movimientos/create" class="dp-btn-reponer">
                                        Reponer
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div style="padding: 2.5rem 0; text-align: center; font-size: 0.75rem; color: #64748b;">
                                ¡Todo el inventario está sobre los niveles mínimos requeridos!
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Almacenes Registrados --}}
                <div class="dp-panel">
                    <div class="dp-panel-header">
                        <h3 class="dp-panel-title">
                            <x-heroicon-o-building-storefront style="width: 1.1rem; height: 1.1rem; color: #34d399;" />
                            Almacenes de la Empresa
                        </h3>
                        <a href="/admin/almacenes" class="dp-panel-link">Ver almacenes →</a>
                    </div>
                    <div>
                        @forelse($data['almacenes_list'] as $alm)
                            <div class="dp-list-row">
                                <div style="min-width: 0; flex: 1;">
                                    <p style="font-size: 0.775rem; font-weight: 600; color: #ffffff; margin: 0;">{{ $alm['nombre'] }}</p>
                                    <p style="font-size: 0.65rem; color: #94a3b8; margin: 0.25rem 0 0;">
                                        {{ $alm['municipio'] }}, {{ $alm['provincia'] }} · Responsable: {{ $alm['responsable'] }}
                                    </p>
                                </div>
                                <span style="font-size: 0.65rem; font-weight: 700; color: #34d399; background: rgba(16, 185, 129, 0.15); padding: 0.2rem 0.55rem; border-radius: 0.35rem; border: 1px solid rgba(52, 211, 153, 0.25);">
                                    Activo
                                </span>
                            </div>
                        @empty
                            <div style="padding: 2.5rem 0; text-align: center; font-size: 0.75rem; color: #64748b;">
                                No hay almacenes registrados.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================
             SECCIÓN 4: CLIENTES (Empresas, Sedes/Ubicaciones, Contactos)
             ============================================================ --}}
        <div x-show="activeTab === 'clientes'" x-cloak style="display: block;">
            <div class="dp-stat-grid-4">
                <div class="dp-stat-card">
                    <div class="dp-stat-top">
                        <span class="dp-stat-label">Empresas Clientes</span>
                        <div class="dp-stat-icon-box sky">
                            <x-heroicon-o-building-office-2 style="width: 1.1rem; height: 1.1rem;" />
                        </div>
                    </div>
                    <p class="dp-stat-num">{{ $data['clientes_total'] }}</p>
                    <p class="dp-stat-hint">Cartera de clientes registrados</p>
                </div>

                <div class="dp-stat-card">
                    <div class="dp-stat-top">
                        <span class="dp-stat-label">Clientes Activos</span>
                        <div class="dp-stat-icon-box emerald">
                            <x-heroicon-o-check-badge style="width: 1.1rem; height: 1.1rem;" />
                        </div>
                    </div>
                    <p class="dp-stat-num" style="color: #34d399;">{{ $data['clientes_activos'] }}</p>
                    <p class="dp-stat-hint">Con servicios u obras en marcha</p>
                </div>

                <div class="dp-stat-card">
                    <div class="dp-stat-top">
                        <span class="dp-stat-label">Sedes Monitoreadas</span>
                        <div class="dp-stat-icon-box purple">
                            <x-heroicon-o-map-pin style="width: 1.1rem; height: 1.1rem;" />
                        </div>
                    </div>
                    <p class="dp-stat-num" style="color: #c084fc;">{{ $data['ubicaciones_total'] }}</p>
                    <p class="dp-stat-hint">Puntos físicos de clientes</p>
                </div>

                <div class="dp-stat-card">
                    <div class="dp-stat-top">
                        <span class="dp-stat-label">Contactos Registrados</span>
                        <div class="dp-stat-icon-box amber">
                            <x-heroicon-o-user-group style="width: 1.1rem; height: 1.1rem;" />
                        </div>
                    </div>
                    <p class="dp-stat-num" style="color: #fbbf24;">{{ $data['contactos_total'] }}</p>
                    <p class="dp-stat-hint">Interlocutores empresariales</p>
                </div>
            </div>

            <div class="dp-panel-grid-2">
                {{-- Directorio de Clientes --}}
                <div class="dp-panel">
                    <div class="dp-panel-header">
                        <h3 class="dp-panel-title">
                            <x-heroicon-o-building-office-2 style="width: 1.1rem; height: 1.1rem; color: #38bdf8;" />
                            Clientes de la Cartera
                        </h3>
                        <a href="/admin/clientes" class="dp-panel-link">Ver clientes →</a>
                    </div>
                    <div>
                        @forelse($data['clientes_recientes'] as $c)
                            <div class="dp-list-row">
                                <div style="min-width: 0; flex: 1;">
                                    <p style="font-size: 0.775rem; font-weight: 600; color: #ffffff; margin: 0;">{{ $c['nombre'] }}</p>
                                    <p style="font-size: 0.65rem; color: #94a3b8; margin: 0.25rem 0 0;">
                                        CIF/NIT: {{ $c['cif'] }} · Tel: {{ $c['telefono'] }}
                                    </p>
                                </div>
                                <div style="text-align: right; flex-shrink: 0;">
                                    <span style="font-size: 0.65rem; font-weight: 700; color: #38bdf8; background: rgba(14, 165, 233, 0.15); padding: 0.2rem 0.55rem; border-radius: 0.35rem; border: 1px solid rgba(56, 189, 248, 0.25);">
                                        {{ $c['servicios'] }} servicios
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div style="padding: 2.5rem 0; text-align: center; font-size: 0.75rem; color: #64748b;">
                                No hay clientes registrados.
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Sedes y Puntos de Servicio --}}
                <div class="dp-panel">
                    <div class="dp-panel-header">
                        <h3 class="dp-panel-title">
                            <x-heroicon-o-map-pin style="width: 1.1rem; height: 1.1rem; color: #c084fc;" />
                            Sedes y Puntos de Atención
                        </h3>
                        <a href="/admin/clientes" class="dp-panel-link">Ver sedes →</a>
                    </div>
                    <div>
                        @forelse($data['ubicaciones_recientes'] as $u)
                            <div class="dp-list-row">
                                <div style="min-width: 0; flex: 1;">
                                    <p style="font-size: 0.775rem; font-weight: 600; color: #ffffff; margin: 0;">{{ $u['nombre'] }}</p>
                                    <p style="font-size: 0.65rem; color: #94a3b8; margin: 0.25rem 0 0;">
                                        Cliente: {{ $u['cliente'] }} · {{ $u['provincia'] }}
                                    </p>
                                </div>
                                <span style="font-size: 0.625rem; font-weight: 700; color: #c084fc; background: rgba(168, 85, 247, 0.15); padding: 0.2rem 0.55rem; border-radius: 0.35rem; border: 1px solid rgba(192, 132, 252, 0.25);">
                                    {{ $u['tipo_negocio'] }}
                                </span>
                            </div>
                        @empty
                            <div style="padding: 2.5rem 0; text-align: center; font-size: 0.75rem; color: #64748b;">
                                No hay sedes registradas.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================
             SECCIÓN 5: ADMINISTRACIÓN (Usuarios, Roles, LDAP)
             ============================================================ --}}
        <div x-show="activeTab === 'administracion'" x-cloak style="display: block;">
            <div class="dp-stat-grid-3">
                <div class="dp-stat-card">
                    <span class="dp-stat-label">Usuarios con Acceso</span>
                    <p class="dp-stat-num" style="color: #38bdf8;">{{ $data['usuarios_total'] }}</p>
                    <p class="dp-stat-hint">Cuentas activas en la plataforma</p>
                </div>
                <div class="dp-stat-card">
                    <span class="dp-stat-label">Personal Operativo</span>
                    <p class="dp-stat-num" style="color: #34d399;">{{ $data['tecnicos_total'] }}</p>
                    <p class="dp-stat-hint">Técnicos, especialistas y brigadistas</p>
                </div>
                <div class="dp-stat-card">
                    <span class="dp-stat-label">Directorio LDAP / LLDAP</span>
                    <p class="dp-stat-num" style="color: #c084fc; font-size: 1.25rem;">dc=dataplus,dc=cu</p>
                    <p class="dp-stat-hint">Autenticación centralizada en línea</p>
                </div>
            </div>

            <div class="dp-panel">
                <div class="dp-panel-header" style="margin-bottom: 1.35rem;">
                    <h3 class="dp-panel-title">
                        <x-heroicon-o-shield-check style="width: 1.1rem; height: 1.1rem; color: #38bdf8;" />
                        Accesos Rápidos de Administración y Seguridad
                    </h3>
                </div>
                <div style="display: grid; grid-template-columns: repeat(1, minmax(0, 1fr)); gap: 0.85rem;">
                    <a href="/admin/clientes" class="dp-quick-link">
                        <div>
                            <p style="font-size: 0.8rem; font-weight: 700; color: #ffffff; margin: 0;">Directorio de Clientes y Sedes</p>
                            <p style="font-size: 0.65rem; color: #94a3b8; margin: 0.2rem 0 0;">{{ $data['clientes_total'] }} empresas y {{ $data['ubicaciones_total'] }} sedes registradas</p>
                        </div>
                        <x-heroicon-o-chevron-right style="width: 1.1rem; height: 1.1rem; color: #94a3b8;" />
                    </a>
                    <a href="/admin/users" class="dp-quick-link">
                        <div>
                            <p style="font-size: 0.8rem; font-weight: 700; color: #ffffff; margin: 0;">Usuarios, Roles y Permisos RBAC</p>
                            <p style="font-size: 0.65rem; color: #94a3b8; margin: 0.2rem 0 0;">Control de acceso para supervisores, especialistas y técnicos</p>
                        </div>
                        <x-heroicon-o-chevron-right style="width: 1.1rem; height: 1.1rem; color: #94a3b8;" />
                    </a>
                    <a href="/admin/ldap-configurations" class="dp-quick-link">
                        <div>
                            <p style="font-size: 0.8rem; font-weight: 700; color: #ffffff; margin: 0;">Servidor de Directorio LDAP / LLDAP</p>
                            <p style="font-size: 0.65rem; color: #94a3b8; margin: 0.2rem 0 0;">Parámetros de conexión, certificados y sincronización</p>
                        </div>
                        <x-heroicon-o-chevron-right style="width: 1.1rem; height: 1.1rem; color: #94a3b8;" />
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
