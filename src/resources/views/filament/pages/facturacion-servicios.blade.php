<x-filament-panels::page>
    @php
        /** @var array $metricas */
        $metricas = $this->getMetricas();
        $suscripciones = $this->getSuscripciones();
        $pagosSemana = $this->getPagosSemana();
        $mayoresDeudores = $this->getMayoresDeudores();
        $reporteTemporal = $this->getDatosReporteTemporal();
    @endphp

    {{-- ============================================================
         ESTILOS AUTO-CONTENIDOS Y HOMOGÉNEOS CON EL DISEÑO DE LA PLATAFORMA (IDENTICO A REPORTES)
         ============================================================ --}}
    <style>
        [x-cloak] { display: none !important; }

        .dp-fact-wrap {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #f1f5f9;
            width: 100%;
            position: relative;
        }

        /* ─── Contenedor de Filtros del Módulo (Pantalla) ─── */
        .dp-filter-box {
            border-radius: 1rem;
            border: 1px solid #1e293b;
            background: rgba(15, 23, 42, 0.95);
            padding: 1.5rem;
            margin-bottom: 2rem !important;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.25);
        }

        .dp-filter-nav {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            overflow-x: auto;
            margin-bottom: 1.25rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #1e293b;
        }

        .dp-period-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border-radius: 0.6rem;
            padding: 0.6rem 1.25rem;
            font-size: 0.8rem;
            font-weight: 700;
            border: none;
            background: transparent;
            color: #94a3b8;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .dp-period-btn:hover {
            background: rgba(30, 41, 59, 0.8);
            color: #f1f5f9;
        }

        .dp-period-btn.active {
            background: linear-gradient(90deg, #0ea5e9 0%, #0284c7 100%) !important;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(14, 165, 233, 0.35);
        }

        .dp-period-btn.active-rose {
            background: linear-gradient(90deg, #f43f5e 0%, #e11d48 100%) !important;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(244, 63, 94, 0.35);
        }

        .dp-inputs-row {
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: 1rem;
            align-items: flex-end;
        }

        @media (min-width: 640px) {
            .dp-inputs-row { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (min-width: 1024px) {
            .dp-inputs-row { grid-template-columns: 2fr 1.2fr 1.2fr 1.6fr; }
        }

        .dp-input-group {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }

        .dp-input-label {
            font-size: 0.725rem;
            font-weight: 600;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .dp-input-field {
            background-color: #020617;
            border: 1px solid #334155;
            border-radius: 0.5rem;
            padding: 0.55rem 0.85rem;
            color: #ffffff;
            font-size: 0.825rem;
            outline: none;
            transition: border-color 0.15s ease;
            width: 100%;
            box-sizing: border-box;
        }

        .dp-input-field:focus {
            border-color: #38bdf8;
        }

        select.dp-input-field,
        select.dp-select-field {
            appearance: none !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            background-color: #020617 !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%2394a3b8' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e") !important;
            background-position: right 0.75rem center !important;
            background-repeat: no-repeat !important;
            background-size: 1.15rem 1.15rem !important;
            padding-right: 2.5rem !important;
            cursor: pointer;
        }

        select.dp-input-field option,
        select.dp-select-field option {
            background-color: #0f172a !important;
            color: #ffffff !important;
        }

        /* ─── Botones de Acción ─── */
        .dp-btn-group-actions {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            flex-wrap: wrap;
        }

        .dp-btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            border-radius: 0.5rem;
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            color: #ffffff;
            padding: 0.6rem 1.25rem;
            font-size: 0.825rem;
            font-weight: 700;
            cursor: pointer;
            border: none;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.3);
            transition: all 0.2s ease;
            height: 38px;
            text-decoration: none;
            white-space: nowrap;
        }

        .dp-btn-primary:hover {
            transform: translateY(-1px);
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        }

        .dp-btn-emerald {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            border-radius: 0.5rem;
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: #ffffff;
            padding: 0.5rem 1rem;
            font-size: 0.775rem;
            font-weight: 700;
            cursor: pointer;
            border: none;
            box-shadow: 0 4px 10px rgba(5, 150, 105, 0.25);
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .dp-btn-emerald:hover {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            transform: translateY(-1px);
        }

        .dp-btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            border-radius: 0.5rem;
            background: rgba(30, 41, 59, 0.9);
            color: #38bdf8;
            border: 1px solid rgba(56, 189, 248, 0.4);
            padding: 0.5rem 0.9rem;
            font-size: 0.775rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .dp-btn-secondary:hover {
            background: rgba(56, 189, 248, 0.15);
            border-color: #38bdf8;
            color: #ffffff;
        }

        /* ─── Hero / Encabezado Principal (Idéntico a Reportes) ─── */
        .dp-rep-hero {
            position: relative;
            overflow: hidden;
            border-radius: 1.25rem;
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 55%, #075985 100%);
            padding: 2rem 2.25rem;
            color: #ffffff;
            box-shadow: 0 20px 25px -5px rgba(8, 47, 73, 0.35);
            margin-bottom: 2rem !important;
        }

        .dp-rep-hero-content {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
            justify-content: space-between;
        }

        @media (min-width: 768px) {
            .dp-rep-hero-content {
                flex-direction: row;
                align-items: center;
            }
        }

        .dp-rep-hero-tag {
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: rgba(224, 242, 254, 0.9);
            margin: 0;
        }

        .dp-rep-hero-title {
            margin-top: 0.35rem;
            font-size: 1.85rem;
            font-weight: 800;
            letter-spacing: -0.025em;
            color: #ffffff;
            line-height: 1.2;
        }

        .dp-rep-hero-sub {
            margin-top: 0.5rem;
            font-size: 0.875rem;
            color: rgba(224, 242, 254, 0.9);
            font-weight: 500;
        }

        .dp-rep-hero-badge {
            text-align: right;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 0.85rem;
            padding: 0.85rem 1.35rem;
            backdrop-filter: blur(10px);
            min-width: 220px;
        }

        /* ─── Grids de Tarjetas KPI Superiores (Pantalla) ─── */
        .dp-stat-grid-4 {
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: 1.25rem;
            margin-bottom: 2.25rem !important;
        }

        @media (min-width: 640px) {
            .dp-stat-grid-4 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (min-width: 1024px) {
            .dp-stat-grid-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        }

        .dp-stat-card {
            border-radius: 1rem;
            border: 1px solid #1e293b;
            background: rgba(15, 23, 42, 0.85);
            padding: 1.35rem 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.15);
            transition: all 0.2s ease;
        }

        .dp-stat-card:hover {
            border-color: rgba(56, 189, 248, 0.45);
            transform: translateY(-2px);
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
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .dp-stat-num {
            font-size: 1.65rem;
            font-weight: 800;
            color: #ffffff;
            margin-top: 0.6rem;
            line-height: 1.15;
            letter-spacing: -0.02em;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        }

        .dp-stat-hint {
            font-size: 0.725rem;
            color: #94a3b8;
            margin-top: 0.35rem;
        }

        /* ─── Paneles de Contenido (Pantalla) ─── */
        .dp-panel {
            border-radius: 1rem;
            border: 1px solid #1e293b;
            background: rgba(15, 23, 42, 0.85);
            padding: 1.75rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.15);
            margin-bottom: 2rem !important;
        }

        .dp-panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.35rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid rgba(30, 41, 59, 0.8);
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .dp-panel-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0;
        }

        /* ─── Tablas de Datos del Reporte ─── */
        .dp-table-wrap {
            overflow-x: auto;
            border-radius: 0.75rem;
            border: 1px solid #1e293b;
            background: rgba(2, 6, 23, 0.65);
        }

        .dp-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.775rem;
            text-align: left;
        }

        .dp-table th {
            background: rgba(15, 23, 42, 0.9);
            color: #94a3b8;
            padding: 0.75rem 1rem;
            font-weight: 600;
            border-bottom: 1px solid #1e293b;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 0.05em;
        }

        .dp-table td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid rgba(30, 41, 59, 0.6);
            color: #f1f5f9;
        }

        .dp-table tr:last-child td {
            border-bottom: none;
        }

        .dp-table tr:hover td {
            background: rgba(30, 41, 59, 0.4);
        }

        /* ─── Modales Flotantes DataPlus ─── */
        .dp-modal-overlay {
            position: fixed;
            inset: 0;
            z-index: 99999;
            background: rgba(2, 6, 23, 0.88);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            overflow-y: auto;
        }

        .dp-modal-card {
            background: #0f172a;
            border: 1px solid #1e293b;
            border-radius: 1rem;
            width: 100%;
            max-width: 560px;
            padding: 1.75rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.8);
            color: #f1f5f9;
            position: relative;
            margin: auto;
        }

        .dp-modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 1rem;
            border-bottom: 1px solid #1e293b;
            margin-bottom: 1.25rem;
        }

        .dp-modal-title {
            font-size: 1.05rem;
            font-weight: 800;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            margin: 0;
        }

        .dp-modal-close-btn {
            background: transparent;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 0.35rem;
            border-radius: 0.4rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.15s ease;
        }

        .dp-modal-close-btn:hover {
            color: #ffffff;
            background: rgba(30, 41, 59, 0.6);
        }

        .dp-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.25rem 0.65rem;
            border-radius: 9999px;
            font-size: 0.725rem;
            font-weight: 700;
        }

        .dp-badge-emerald {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .dp-badge-amber {
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .dp-badge-rose {
            background: rgba(244, 63, 94, 0.15);
            color: #fb7185;
            border: 1px solid rgba(244, 63, 94, 0.3);
        }
    </style>

    <div class="dp-fact-wrap">
        {{-- ================= PANEL DE FILTROS & NAVEGACIÓN SUPERIOR ================= --}}
        <div class="dp-filter-box">
            {{-- Pestañas principales con efecto glowing idéntico a Reportes --}}
            <div class="dp-filter-nav">
                <button type="button"
                        wire:click="$set('activeTab', 'cobros')"
                        class="dp-period-btn {{ $activeTab === 'cobros' ? 'active' : '' }}">
                    <x-heroicon-o-table-cells style="width: 1rem; height: 1rem;" />
                    <span>Control de Cobros Mensuales ({{ count($suscripciones) }})</span>
                </button>
                <button type="button"
                        wire:click="$set('activeTab', 'semana')"
                        class="dp-period-btn {{ $activeTab === 'semana' ? 'active' : '' }}">
                    <x-heroicon-o-check-circle style="width: 1rem; height: 1rem;" />
                    <span>Pagaron esta Semana ({{ count($pagosSemana) }})</span>
                </button>
                <button type="button"
                        wire:click="$set('activeTab', 'deudores')"
                        class="dp-period-btn {{ $activeTab === 'deudores' ? 'active-rose' : '' }}">
                    <x-heroicon-o-exclamation-triangle style="width: 1rem; height: 1rem;" />
                    <span>Mayores Deudores ({{ count($mayoresDeudores) }})</span>
                </button>
                <button type="button"
                        wire:click="$set('activeTab', 'reporte')"
                        class="dp-period-btn {{ $activeTab === 'reporte' ? 'active' : '' }}">
                    <x-heroicon-o-chart-bar style="width: 1rem; height: 1rem;" />
                    <span>Evolución & Comparativa Anual</span>
                </button>
            </div>

            {{-- Fila de Parámetros y Acciones --}}
            <div class="dp-inputs-row">
                <div class="dp-input-group">
                    <label class="dp-input-label">Buscar Cliente / Código / SIM</label>
                    <input type="text"
                           wire:model.live.debounce.300ms="busqueda"
                           placeholder="Ej. Juan Pérez, SUB-001, 535284..."
                           class="dp-input-field" />
                </div>

                <div class="dp-input-group">
                    <label class="dp-input-label">Moneda</label>
                    <select wire:model.live="filtroMoneda" class="dp-input-field dp-select-field">
                        <option value="todas">Todas las Monedas</option>
                        <option value="CUP">Pesos Cubanos (CUP)</option>
                        <option value="USD">Dólares (USD)</option>
                    </select>
                </div>

                <div class="dp-input-group">
                    <label class="dp-input-label">Estado de Cobro</label>
                    <select wire:model.live="filtroEstado" class="dp-input-field dp-select-field">
                        <option value="todos">Todos los Estados</option>
                        <option value="cobrados">Cobrados (Al Día)</option>
                        <option value="pendientes">Pendientes (Días 1-15)</option>
                        <option value="mora">En Mora (Vencidos)</option>
                    </select>
                </div>

                <div class="dp-input-group">
                    <label class="dp-input-label" style="opacity: 0;">Acción</label>
                    <div class="dp-btn-group-actions">
                        <button type="button"
                                wire:click="$set('modalNuevaSubOpen', true)"
                                class="dp-btn-primary"
                                title="Crear nueva suscripción de servicio de Gestión Remota">
                            <x-heroicon-m-plus style="width: 1.1rem; height: 1.1rem; stroke-width: 2.5;" />
                            <span>Nueva Suscripción</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= HERO BANNER PRINCIPAL (ESTILO REPORTE MENSUAL) ================= --}}
        <div class="dp-rep-hero">
            <div class="dp-rep-hero-content">
                <div>
                    <p class="dp-rep-hero-tag">
                        Periodo de Cobro: Octubre 2026 · Mes Adelantado
                    </p>
                    <h1 class="dp-rep-hero-title">Facturación de Gestión Remota & Servicios Recurrentes</h1>
                    <p class="dp-rep-hero-sub">
                        DataPlus S.R.L. · 
                        @if($metricas['periodo_vencido'])
                            <strong style="color: #fecdd3;">Plazo Vencido: Cuentas no liquidadas en mora</strong>
                        @else
                            Plazo en curso: <strong style="color: #bae6fd;">{{ $metricas['dias_restantes_15'] }} días restantes</strong> (Día {{ $metricas['dia_del_mes'] }} de 15)
                        @endif
                        · Norma operativa: Liquidación en los primeros 15 días del mes.
                    </p>
                </div>
                <div class="dp-rep-hero-badge">
                    <p style="font-size: 0.65rem; text-transform: uppercase; font-weight: 700; color: #e0f2fe; margin: 0;">Recaudado Mes en Curso</p>
                    <p style="font-size: 1.65rem; font-weight: 800; color: #ffffff; margin: 0.25rem 0 0; line-height: 1; font-family: ui-monospace, monospace;">
                        ${{ number_format($metricas['recaudado_cup'], 2) }} <span style="font-size: 0.85rem; color: #7dd3fc;">CUP</span>
                    </p>
                    @if($metricas['recaudado_usd'] > 0)
                        <p style="font-size: 0.8rem; font-weight: 700; color: #38bdf8; margin: 0.35rem 0 0; font-family: ui-monospace, monospace;">
                            +${{ number_format($metricas['recaudado_usd'], 2) }} USD
                        </p>
                    @endif
                </div>
            </div>
        </div>

        {{-- ================= 4 TARJETAS KPI (ESTILO IDÉNTICO A REPORTES) ================= --}}
        <div class="dp-stat-grid-4">
            {{-- 1. Recaudado CUP --}}
            <div class="dp-stat-card">
                <div class="dp-stat-top">
                    <span class="dp-stat-label">Recaudado Octubre (CUP)</span>
                    <div style="border-radius: 0.5rem; padding: 0.45rem; background: rgba(16, 185, 129, 0.15); color: #34d399;">
                        <x-heroicon-o-banknotes style="width: 1.1rem; height: 1.1rem;" />
                    </div>
                </div>
                <p class="dp-stat-num" style="color: #34d399;">${{ number_format($metricas['recaudado_cup'], 2) }}</p>
                <p class="dp-stat-hint">
                    Pendiente por recaudar: <strong style="color: #fbbf24;">${{ number_format($metricas['pendiente_cup'], 2) }} CUP</strong>
                </p>
            </div>

            {{-- 2. Recaudado USD --}}
            <div class="dp-stat-card">
                <div class="dp-stat-top">
                    <span class="dp-stat-label">Recaudado Octubre (USD)</span>
                    <div style="border-radius: 0.5rem; padding: 0.45rem; background: rgba(14, 165, 233, 0.15); color: #38bdf8;">
                        <x-heroicon-o-currency-dollar style="width: 1.1rem; height: 1.1rem;" />
                    </div>
                </div>
                <p class="dp-stat-num" style="color: #38bdf8;">${{ number_format($metricas['recaudado_usd'], 2) }}</p>
                <p class="dp-stat-hint">
                    Pendiente: <strong style="color: #38bdf8;">${{ number_format($metricas['pendiente_usd'], 2) }} USD</strong>
                </p>
            </div>

            {{-- 3. Deuda Acumulada --}}
            <div class="dp-stat-card">
                <div class="dp-stat-top">
                    <span class="dp-stat-label">Deuda Acumulada</span>
                    <div style="border-radius: 0.5rem; padding: 0.45rem; background: rgba(244, 63, 94, 0.15); color: #fb7185;">
                        <x-heroicon-o-exclamation-triangle style="width: 1.1rem; height: 1.1rem;" />
                    </div>
                </div>
                <p class="dp-stat-num" style="color: #fb7185;">${{ number_format($metricas['deuda_cup'], 2) }} <span style="font-size: 0.75rem; color: #94a3b8;">CUP</span></p>
                <p class="dp-stat-hint">
                    <strong style="color: #fb7185;">{{ $metricas['total_deudores'] }} clientes en mora</strong>
                    @if($metricas['deuda_usd'] > 0)
                        <span>(+ ${{ number_format($metricas['deuda_usd'], 2) }} USD)</span>
                    @endif
                </p>
            </div>

            {{-- 4. Parque Gestión Remota --}}
            <div class="dp-stat-card">
                <div class="dp-stat-top">
                    <span class="dp-stat-label">Parque Gestión Remota</span>
                    <div style="border-radius: 0.5rem; padding: 0.45rem; background: rgba(168, 85, 247, 0.15); color: #c084fc;">
                        <x-heroicon-o-signal style="width: 1.1rem; height: 1.1rem;" />
                    </div>
                </div>
                <p class="dp-stat-num" style="color: #c084fc;">{{ $metricas['total_routers'] ?? count($suscripciones) }} <span style="font-size: 0.75rem; color: #94a3b8;">Routers</span></p>
                <p class="dp-stat-hint">
                    {{ $metricas['al_dia_count'] ?? 0 }} cobros al día ({{ $metricas['porcentaje_al_dia'] ?? 50 }}% cumplimiento)
                </p>
            </div>
        </div>

        {{-- ================= TAB 1: CONTROL DE COBROS MENSUALES ================= --}}
        @if($activeTab === 'cobros')
            <div class="dp-panel">
                <div class="dp-panel-header">
                    <h3 class="dp-panel-title">
                        <x-heroicon-o-receipt-percent style="width: 1.1rem; height: 1.1rem; color: #38bdf8;" />
                        Planilla General de Cobros de Gestión Remota & Servicios Recurrentes
                    </h3>
                    <span style="font-size: 0.75rem; color: #38bdf8; font-weight: 700; background: rgba(14, 165, 233, 0.15); padding: 0.25rem 0.75rem; border-radius: 0.35rem; border: 1px solid rgba(14, 165, 233, 0.3);">
                        {{ count($suscripciones) }} Servicios Registrados
                    </span>
                </div>

                <div class="dp-table-wrap">
                    <table class="dp-table">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Cliente / Teléfono</th>
                                <th>Solución / SIM</th>
                                <th style="text-align: right;">Mensualidad</th>
                                <th style="text-align: right;">Deuda Previa</th>
                                <th style="text-align: right; color: #ffffff;">Total Exigible</th>
                                <th style="text-align: center;">Estado</th>
                                <th style="text-align: center;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($suscripciones as $sub)
                                @php
                                    $esMora = $sub->estado_cobro_mes_actual === 'En_Mora' || $sub->tiene_deuda;
                                    $esCobrado = $sub->estado_cobro_mes_actual === 'Al_Dia';
                                    $esPendiente = $sub->estado_cobro_mes_actual === 'Pendiente';
                                @endphp
                                <tr>
                                    <td style="font-family: ui-monospace, monospace; font-weight: 700; color: #38bdf8;">
                                        {{ $sub->codigo }}
                                    </td>
                                    <td>
                                        <div style="font-weight: 700; color: #ffffff;">{{ $sub->cliente_nombre }}</div>
                                        @if($sub->cliente_telefono)
                                            <div style="font-size: 0.7rem; color: #94a3b8; font-family: ui-monospace, monospace; margin-top: 2px;">
                                                ☎ {{ $sub->cliente_telefono }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div style="color: #f1f5f9; font-weight: 500;">{{ $sub->tipo_solucion ?: $sub->nombre_servicio }}</div>
                                        <div style="font-size: 0.7rem; color: #64748b; font-family: ui-monospace, monospace; margin-top: 2px;">
                                            SIM: {{ $sub->sim_numero ?: 'Sin asignar' }}
                                        </div>
                                    </td>
                                    <td style="text-align: right; font-family: ui-monospace, monospace; font-weight: 600; color: #e2e8f0;">
                                        ${{ number_format($sub->tarifa_mensual, 2) }} {{ $sub->moneda }}
                                    </td>
                                    <td style="text-align: right; font-family: ui-monospace, monospace;">
                                        @if($sub->tiene_deuda && $sub->deuda_acumulada > 0)
                                            <span style="color: #fb7185; font-weight: 700;">+${{ number_format($sub->deuda_acumulada, 2) }}</span>
                                            <div style="font-size: 0.65rem; color: #94a3b8;">({{ $sub->meses_deuda_count }}m)</div>
                                        @else
                                            <span style="color: #64748b;">$0.00</span>
                                        @endif
                                    </td>
                                    <td style="text-align: right; font-family: ui-monospace, monospace; font-weight: 800; color: #ffffff; font-size: 0.85rem;">
                                        ${{ number_format($sub->monto_a_cobrar, 2) }} {{ $sub->moneda }}
                                    </td>
                                    <td style="text-align: center;">
                                        @if($esMora)
                                            <span class="dp-badge dp-badge-rose">
                                                <x-heroicon-m-exclamation-triangle style="width: 0.9rem; height: 0.9rem;" />
                                                <span>En Mora</span>
                                            </span>
                                        @elseif($esCobrado)
                                            <span class="dp-badge dp-badge-emerald">
                                                <x-heroicon-m-check style="width: 0.9rem; height: 0.9rem; stroke-width: 2.5;" />
                                                <span>Cobrado</span>
                                            </span>
                                        @else
                                            <span class="dp-badge dp-badge-amber">
                                                <x-heroicon-m-clock style="width: 0.9rem; height: 0.9rem;" />
                                                <span>Pendiente (1-15)</span>
                                            </span>
                                        @endif
                                    </td>
                                    <td style="text-align: center;">
                                        <div style="display: inline-flex; align-items: center; gap: 0.4rem; justify-content: center;">
                                            @if($esCobrado)
                                                <button type="button"
                                                        wire:click="abrirModalRecibo({{ $sub->id }})"
                                                        class="dp-btn-secondary"
                                                        title="Ver Recibo Oficial de Cobro">
                                                    <x-heroicon-m-receipt-percent style="width: 0.9rem; height: 0.9rem;" />
                                                    <span>Recibo</span>
                                                </button>
                                            @else
                                                <button type="button"
                                                        wire:click="abrirModalPago({{ $sub->id }})"
                                                        class="dp-btn-emerald"
                                                        title="Registrar cobranza efectuada">
                                                    <x-heroicon-m-credit-card style="width: 0.9rem; height: 0.9rem;" />
                                                    <span>Cobrar</span>
                                                </button>
                                            @endif

                                            @if($sub->cliente_telefono)
                                                @php
                                                    $tel = preg_replace('/[^0-9]/', '', $sub->cliente_telefono);
                                                    $msg = rawurlencode("Hola {$sub->cliente_nombre}, le contactamos de DataPlus para recordarle el pago de su servicio mensual de Gestión Remota por un total de $" . number_format($sub->monto_a_cobrar, 2) . " {$sub->moneda}. Recuerde enviar el comprobante de su transferencia a este chat. ¡Muchas gracias!");
                                                @endphp
                                                <a href="https://wa.me/{{ $tel }}?text={{ $msg }}"
                                                   target="_blank"
                                                   title="Contactar y Cobrar por WhatsApp"
                                                   style="display: inline-flex; align-items: center; justify-content: center; padding: 0.45rem; border-radius: 0.5rem; background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); text-decoration: none;">
                                                    <x-heroicon-m-chat-bubble-left-ellipsis style="width: 1rem; height: 1rem;" />
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" style="text-align: center; padding: 2.5rem 1rem; color: #64748b;">
                                        No se encontraron suscripciones con los filtros seleccionados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- ================= TAB 2: PAGARON ESTA SEMANA ================= --}}
        @if($activeTab === 'semana')
            <div class="dp-panel">
                <div class="dp-panel-header">
                    <h3 class="dp-panel-title">
                        <x-heroicon-o-check-circle style="width: 1.1rem; height: 1.1rem; color: #34d399;" />
                        Clientes con Pago Registrado esta Semana
                    </h3>
                    <span style="font-size: 0.75rem; color: #34d399; font-weight: 700; background: rgba(16, 185, 129, 0.15); padding: 0.25rem 0.75rem; border-radius: 0.35rem; border: 1px solid rgba(16, 185, 129, 0.3);">
                        {{ count($pagosSemana) }} Cobros Liquidados
                    </span>
                </div>

                <div class="dp-table-wrap">
                    <table class="dp-table">
                        <thead>
                            <tr>
                                <th>Código Pago</th>
                                <th>Cliente</th>
                                <th>Fecha de Pago</th>
                                <th>Método / Vía</th>
                                <th style="text-align: right;">Monto Liquidado</th>
                                <th style="text-align: center;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pagosSemana as $pago)
                                <tr>
                                    <td style="font-family: ui-monospace, monospace; font-weight: 700; color: #38bdf8;">
                                        {{ $pago->codigo_pago }}
                                    </td>
                                    <td style="font-weight: 700; color: #ffffff;">
                                        {{ $pago->cliente_nombre }}
                                    </td>
                                    <td style="font-family: ui-monospace, monospace; color: #94a3b8;">
                                        {{ $pago->fecha_pago }}
                                    </td>
                                    <td>
                                        <span class="dp-badge dp-badge-emerald">
                                            {{ $pago->metodo_pago }}
                                        </span>
                                    </td>
                                    <td style="text-align: right; font-family: ui-monospace, monospace; font-weight: 800; color: #34d399; font-size: 0.85rem;">
                                        +${{ number_format($pago->monto_pagado, 2) }} {{ $pago->moneda }}
                                    </td>
                                    <td style="text-align: center;">
                                        <button type="button"
                                                wire:click="abrirModalRecibo({{ $pago->suscripcion_id ?? 1 }})"
                                                class="dp-btn-secondary">
                                            <x-heroicon-m-receipt-percent style="width: 0.9rem; height: 0.9rem;" />
                                            <span>Recibo</span>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 2.5rem 1rem; color: #64748b;">
                                        No hay cobros registrados en los últimos 7 días.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- ================= TAB 3: MAYORES DEUDORES ================= --}}
        @if($activeTab === 'deudores')
            <div class="dp-panel">
                <div class="dp-panel-header">
                    <h3 class="dp-panel-title">
                        <x-heroicon-o-exclamation-triangle style="width: 1.1rem; height: 1.1rem; color: #fb7185;" />
                        Clientes con Deuda Acumulada y Cuentas en Mora
                    </h3>
                    <span style="font-size: 0.75rem; color: #fb7185; font-weight: 700; background: rgba(244, 63, 94, 0.15); padding: 0.25rem 0.75rem; border-radius: 0.35rem; border: 1px solid rgba(244, 63, 94, 0.3);">
                        {{ count($mayoresDeudores) }} Clientes en Mora
                    </span>
                </div>

                <div class="dp-stat-grid-4" style="grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));">
                    @forelse($mayoresDeudores as $d)
                        <div class="dp-stat-card" style="border-color: rgba(244, 63, 94, 0.35); background: rgba(15, 23, 42, 0.9);">
                            <div class="dp-stat-top">
                                <span class="dp-badge dp-badge-rose">
                                    {{ $d->codigo }}
                                </span>
                                <span style="font-size: 0.75rem; font-weight: 700; color: #fb7185; font-family: ui-monospace, monospace;">
                                    {{ $d->meses_deuda_count }} meses atrasados
                                </span>
                            </div>
                            <h4 style="margin: 0.75rem 0 0.2rem; font-size: 0.95rem; font-weight: 800; color: #ffffff;">
                                {{ $d->cliente_nombre }}
                            </h4>
                            <p style="font-size: 0.75rem; color: #94a3b8; margin: 0 0 1rem;">
                                {{ $d->tipo_solucion ?: $d->nombre_servicio }}
                            </p>
                            <div style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid #1e293b; padding-top: 0.75rem;">
                                <div>
                                    <span style="font-size: 0.65rem; text-transform: uppercase; color: #94a3b8; font-weight: 600; display: block;">Total con mora:</span>
                                    <span style="font-size: 1.15rem; font-weight: 900; color: #fb7185; font-family: ui-monospace, monospace;">
                                        ${{ number_format($d->monto_a_cobrar, 2) }} {{ $d->moneda }}
                                    </span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    @if($d->cliente_telefono)
                                        @php
                                            $telD = preg_replace('/[^0-9]/', '', $d->cliente_telefono);
                                            $msgD = rawurlencode("Hola {$d->cliente_nombre}, le contactamos de DataPlus para recordarle que presenta {$d->meses_deuda_count} meses pendientes de pago por su servicio de Gestión Remota. El monto total es de $" . number_format($d->monto_a_cobrar, 2) . " {$d->moneda}. Le solicitamos realizar la liquidación a la brevedad.");
                                        @endphp
                                        <a href="https://wa.me/{{ $telD }}?text={{ $msgD }}"
                                           target="_blank"
                                           title="Contactar vía WhatsApp"
                                           style="display: inline-flex; align-items: center; justify-content: center; padding: 0.5rem; border-radius: 0.5rem; background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); text-decoration: none;">
                                            <x-heroicon-m-chat-bubble-left-ellipsis style="width: 1rem; height: 1rem;" />
                                        </a>
                                    @endif
                                    <button type="button"
                                            wire:click="abrirModalPago({{ $d->id }})"
                                            class="dp-btn-primary"
                                            style="height: 32px; padding: 0.4rem 0.85rem; font-size: 0.75rem; background: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%); box-shadow: 0 4px 10px rgba(244, 63, 94, 0.3);">
                                        Cobrar
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div style="grid-column: 1 / -1; text-align: center; padding: 3rem 1rem; color: #64748b;">
                            Excelente: No existen clientes con mora registrada.
                        </div>
                    @endforelse
                </div>
            </div>
        @endif

        {{-- ================= TAB 4: REPORTE TEMPORAL CON GRÁFICA ================= --}}
        @if($activeTab === 'reporte')
            <div class="dp-panel">
                <div class="dp-panel-header">
                    <div>
                        <h3 class="dp-panel-title">
                            <x-heroicon-o-presentation-chart-line style="width: 1.1rem; height: 1.1rem; color: #38bdf8;" />
                            Evolución Temporal & Comparativa de Recaudación (2026)
                        </h3>
                        <p style="font-size: 0.75rem; color: #94a3b8; margin: 0.25rem 0 0;">
                            Comportamiento histórico y cumplimiento mensual del servicio de Gestión Remota.
                        </p>
                    </div>

                    {{-- Selector de Periodicidad --}}
                    <div style="display: flex; gap: 0.35rem; background: #020617; padding: 0.25rem; border-radius: 0.5rem; border: 1px solid #1e293b;">
                        <button type="button"
                                wire:click="$set('periodoReporte', 'mensual')"
                                class="dp-period-btn {{ $periodoReporte === 'mensual' ? 'active' : '' }}"
                                style="padding: 0.35rem 0.75rem; font-size: 0.75rem;">
                            Mensual
                        </button>
                        <button type="button"
                                wire:click="$set('periodoReporte', 'trimestral')"
                                class="dp-period-btn {{ $periodoReporte === 'trimestral' ? 'active' : '' }}"
                                style="padding: 0.35rem 0.75rem; font-size: 0.75rem;">
                            Trimestral
                        </button>
                        <button type="button"
                                wire:click="$set('periodoReporte', 'semestral')"
                                class="dp-period-btn {{ $periodoReporte === 'semestral' ? 'active' : '' }}"
                                style="padding: 0.35rem 0.75rem; font-size: 0.75rem;">
                            Semestral
                        </button>
                        <button type="button"
                                wire:click="$set('periodoReporte', 'anual')"
                                class="dp-period-btn {{ $periodoReporte === 'anual' ? 'active' : '' }}"
                                style="padding: 0.35rem 0.75rem; font-size: 0.75rem;">
                            Anual
                        </button>
                    </div>
                </div>

                {{-- Contenedor de la Gráfica --}}
                <div style="background: rgba(2, 6, 23, 0.75); border: 1px solid #1e293b; border-radius: 0.75rem; padding: 1.5rem; margin-bottom: 1.5rem;">
                    {{-- Leyenda --}}
                    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #1e293b; padding-bottom: 0.75rem; margin-bottom: 1rem; font-size: 0.75rem;">
                        <div style="display: flex; gap: 1.5rem; align-items: center;">
                            <div style="display: flex; align-items: center; gap: 0.4rem;">
                                <span style="width: 14px; height: 3px; background: #34d399; border-radius: 2px;"></span>
                                <span style="color: #e2e8f0; font-weight: 600;">Recaudación CUP</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.4rem;">
                                <span style="width: 14px; height: 2px; border-bottom: 2px dashed #38bdf8;"></span>
                                <span style="color: #7dd3fc;">Recaudación USD</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.4rem;">
                                <span style="width: 14px; height: 1.5px; border-bottom: 1.5px dotted #fbbf24;"></span>
                                <span style="color: #94a3b8;">Meta Mensual (~$24,500 CUP)</span>
                            </div>
                        </div>
                        <div style="color: #94a3b8; font-family: ui-monospace, monospace;">
                            Año Fiscal 2026 · Mes actual: <strong style="color: #34d399;">Octubre</strong>
                        </div>
                    </div>

                    @php
                        $filas = $reporteTemporal['filas'] ?? [];
                        $cantPuntos = max(count($filas), 1);
                        $svgW = 860;
                        $svgH = 220;
                        $padX = 50;
                        $padY = 30;
                        $innerW = $svgW - ($padX * 2);
                        $innerH = $svgH - ($padY * 2);
                        $maxCUP = 28000;
                        $metaY = $svgH - $padY - ((24500 / $maxCUP) * $innerH);

                        $puntos = [];
                        foreach ($filas as $idx => $f) {
                            $x = $padX + ($idx / max($cantPuntos - 1, 1)) * $innerW;
                            $monto = floatval($f['cup_cobrado'] ?? 0);
                            $y = $svgH - $padY - (($monto / $maxCUP) * $innerH);
                            $puntos[] = [
                                'x' => $x,
                                'y' => $y,
                                'monto' => $monto,
                                'usd' => floatval($f['usd_cobrado'] ?? 0),
                                'label' => $f['etiqueta'] ?? "P{$idx}",
                                'pct' => $f['porcentaje_meta'] ?? 0
                            ];
                        }
                        $polylinePoints = implode(' ', array_map(fn($p) => "{$p['x']},{$p['y']}", $puntos));
                        $areaPoints = "{$padX}," . ($svgH - $padY) . " " . $polylinePoints . " " . ($svgW - $padX) . "," . ($svgH - $padY);
                    @endphp

                    <div style="width: 100%; overflow-x: auto;">
                        <svg viewBox="0 0 {{ $svgW }} {{ $svgH }}" style="width: 100%; min-width: 650px; height: auto;" preserveAspectRatio="xMidYMid meet">
                            <defs>
                                <linearGradient id="chartGradient" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="#10b981" stop-opacity="0.35" />
                                    <stop offset="100%" stop-color="#10b981" stop-opacity="0.0" />
                                </linearGradient>
                            </defs>

                            {{-- Guías Horizontales --}}
                            @for($g = 0; $g <= 4; $g++)
                                @php
                                    $gy = $padY + ($g * ($innerH / 4));
                                    $valG = $maxCUP - ($g * ($maxCUP / 4));
                                @endphp
                                <line x1="{{ $padX }}" y1="{{ $gy }}" x2="{{ $svgW - $padX }}" y2="{{ $gy }}" stroke="#1e293b" stroke-width="1" stroke-dasharray="3 3" />
                                <text x="{{ $padX - 8 }}" y="{{ $gy + 4 }}" fill="#64748b" font-size="9" text-anchor="end" font-family="monospace">${{ number_format($valG / 1000) }}k</text>
                            @endfor

                            {{-- Línea de Meta --}}
                            <line x1="{{ $padX }}" y1="{{ $metaY }}" x2="{{ $svgW - $padX }}" y2="{{ $metaY }}" stroke="#fbbf24" stroke-width="1.5" stroke-dasharray="4 4" opacity="0.8" />
                            <text x="{{ $svgW - $padX + 8 }}" y="{{ $metaY + 3 }}" fill="#fbbf24" font-size="9" font-family="monospace">Meta</text>

                            {{-- Área y Línea continua --}}
                            <polygon points="{{ $areaPoints }}" fill="url(#chartGradient)" />
                            <polyline points="{{ $polylinePoints }}" fill="none" stroke="#10b981" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />

                            {{-- Nodos interactivos --}}
                            @foreach($puntos as $pt)
                                <circle cx="{{ $pt['x'] }}" cy="{{ $pt['y'] }}" r="4.5" fill="#020617" stroke="#10b981" stroke-width="2.5" />
                                <text x="{{ $pt['x'] }}" y="{{ $svgH - 8 }}" fill="#94a3b8" font-size="10" text-anchor="middle" font-family="sans-serif">{{ $pt['label'] }}</text>
                                <text x="{{ $pt['x'] }}" y="{{ $pt['y'] - 8 }}" fill="#ffffff" font-size="9" text-anchor="middle" font-weight="bold" font-family="monospace">${{ number_format($pt['monto']) }}</text>
                            @endforeach
                        </svg>
                    </div>
                </div>

                {{-- Tabla complementaria del reporte --}}
                <div class="dp-table-wrap">
                    <table class="dp-table">
                        <thead>
                            <tr>
                                <th>Período</th>
                                <th style="text-align: right;">Recaudado CUP</th>
                                <th style="text-align: right;">Recaudado USD</th>
                                <th style="text-align: right;">Cobros Realizados</th>
                                <th style="text-align: center;">% Cumplimiento</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($filas as $f)
                                <tr>
                                    <td style="font-weight: 700; color: #ffffff;">{{ $f['etiqueta'] }}</td>
                                    <td style="text-align: right; font-family: ui-monospace, monospace; color: #34d399; font-weight: 700;">
                                        ${{ number_format($f['cup_cobrado'], 2) }} CUP
                                    </td>
                                    <td style="text-align: right; font-family: ui-monospace, monospace; color: #38bdf8;">
                                        ${{ number_format($f['usd_cobrado'], 2) }} USD
                                    </td>
                                    <td style="text-align: right; font-family: ui-monospace, monospace; color: #e2e8f0;">
                                        {{ $f['total_cobros'] }}
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="dp-badge {{ $f['porcentaje_meta'] >= 80 ? 'dp-badge-emerald' : 'dp-badge-amber' }}">
                                            {{ $f['porcentaje_meta'] }}%
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- ============================================================
             MODALES FLOTANTES (ESTILO DATAPLUS, CIERRE CON FONDO Y ESCAPE)
             ============================================================ --}}

        {{-- ─── MODAL 1: REGISTRAR COBRANZA ─── --}}
        @if($modalPagoOpen)
            <div x-data
                 x-on:click.self="$wire.set('modalPagoOpen', false)"
                 x-on:keydown.escape.window="$wire.set('modalPagoOpen', false)"
                 class="dp-modal-overlay">
                <div x-on:click.stop class="dp-modal-card">
                    <div class="dp-modal-header">
                        <h3 class="dp-modal-title">
                            <x-heroicon-o-credit-card style="width: 1.35rem; height: 1.35rem; color: #34d399;" />
                            <span>Registrar Cobranza de Gestión Remota</span>
                        </h3>
                        <button type="button"
                                wire:click="$set('modalPagoOpen', false)"
                                class="dp-modal-close-btn">
                            <x-heroicon-m-x-mark style="width: 1.25rem; height: 1.25rem;" />
                        </button>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 1rem; font-size: 0.8rem;">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                            <div class="dp-input-group">
                                <label class="dp-input-label">Monto Pagado</label>
                                <input wire:model="formMontoPagado" type="number" step="0.01" class="dp-input-field" style="font-family: monospace; font-weight: 700; font-size: 0.95rem; color: #34d399;" />
                            </div>
                            <div class="dp-input-group">
                                <label class="dp-input-label">Fecha del Pago</label>
                                <input wire:model="formFechaPago" type="date" class="dp-input-field" />
                            </div>
                        </div>

                        <div class="dp-input-group">
                            <label class="dp-input-label">Vía / Método de Pago</label>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
                                <button type="button"
                                        wire:click="$set('formMetodoPago', 'transferencia')"
                                        style="padding: 0.6rem; border-radius: 0.5rem; font-weight: 700; font-size: 0.775rem; cursor: pointer; border: 1px solid {{ $formMetodoPago === 'transferencia' ? '#0ea5e9' : '#334155' }}; background: {{ $formMetodoPago === 'transferencia' ? 'rgba(14, 165, 233, 0.2)' : '#020617' }}; color: {{ $formMetodoPago === 'transferencia' ? '#38bdf8' : '#94a3b8' }};">
                                    Transferencia (Transfermóvil / EnZona)
                                </button>
                                <button type="button"
                                        wire:click="$set('formMetodoPago', 'efectivo')"
                                        style="padding: 0.6rem; border-radius: 0.5rem; font-weight: 700; font-size: 0.775rem; cursor: pointer; border: 1px solid {{ $formMetodoPago === 'efectivo' ? '#0ea5e9' : '#334155' }}; background: {{ $formMetodoPago === 'efectivo' ? 'rgba(14, 165, 233, 0.2)' : '#020617' }}; color: {{ $formMetodoPago === 'efectivo' ? '#38bdf8' : '#94a3b8' }};">
                                    Pago en Efectivo (Cobro en Sitio)
                                </button>
                            </div>
                        </div>

                        @if($formMetodoPago === 'transferencia')
                            <div class="dp-input-group">
                                <label class="dp-input-label">Número de Transacción / Operación</label>
                                <input wire:model="formNumeroTransaccion" type="text" placeholder="Ej. TRF-5918234" class="dp-input-field" style="font-family: monospace;" />
                            </div>
                        @else
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                                <div class="dp-input-group">
                                    <label class="dp-input-label">Quién Recibe (DataPlus)</label>
                                    <input wire:model="formQuienRecibe" type="text" class="dp-input-field" />
                                </div>
                                <div class="dp-input-group">
                                    <label class="dp-input-label">Quién Entrega</label>
                                    <input wire:model="formQuienEntrega" type="text" placeholder="Nombre de quien entrega" class="dp-input-field" />
                                </div>
                            </div>
                        @endif

                        <div class="dp-input-group">
                            <label class="dp-input-label">Notas / Observaciones</label>
                            <input wire:model="formNotasPago" type="text" placeholder="Ej. Pago verificado por WhatsApp" class="dp-input-field" />
                        </div>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 0.6rem; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #1e293b;">
                        <button type="button"
                                wire:click="$set('modalPagoOpen', false)"
                                class="dp-btn-secondary">
                            Cancelar
                        </button>
                        <button type="button"
                                wire:click="guardarPago"
                                class="dp-btn-primary"
                                style="background: linear-gradient(135deg, #059669 0%, #047857 100%);">
                            <x-heroicon-m-check style="width: 1.1rem; height: 1.1rem; stroke-width: 2.5;" />
                            <span>Confirmar Cobro</span>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- ─── MODAL 2: VISOR DE COMPROBANTE ─── --}}
        @if($modalVisorOpen && $comprobanteActual)
            <div x-data
                 x-on:click.self="$wire.set('modalVisorOpen', false)"
                 x-on:keydown.escape.window="$wire.set('modalVisorOpen', false)"
                 class="dp-modal-overlay">
                <div x-on:click.stop class="dp-modal-card">
                    <div class="dp-modal-header">
                        <h3 class="dp-modal-title">
                            <x-heroicon-o-photo style="width: 1.35rem; height: 1.35rem; color: #38bdf8;" />
                            <span>Comprobante de WhatsApp</span>
                        </h3>
                        <button type="button" wire:click="$set('modalVisorOpen', false)" class="dp-modal-close-btn">
                            <x-heroicon-m-x-mark style="width: 1.25rem; height: 1.25rem;" />
                        </button>
                    </div>
                    <div style="text-align: center;">
                        @if(!empty($comprobanteActual['url']))
                            <img src="{{ $comprobanteActual['url'] }}" alt="Comprobante" style="max-height: 380px; width: auto; margin: 0 auto; border-radius: 0.5rem; border: 1px solid #334155;" />
                        @else
                            <div style="padding: 3rem 1rem; color: #64748b;">No hay imagen adjunta.</div>
                        @endif
                        <p style="font-size: 0.75rem; color: #94a3b8; font-family: monospace; margin-top: 0.75rem;">
                            Ref: {{ $comprobanteActual['referencia'] ?? 'N/A' }} · Cliente: {{ $comprobanteActual['cliente'] ?? 'N/A' }}
                        </p>
                    </div>
                    <div style="display: flex; justify-content: flex-end; margin-top: 1.25rem; border-top: 1px solid #1e293b; padding-top: 0.75rem;">
                        <button type="button" wire:click="$set('modalVisorOpen', false)" class="dp-btn-secondary">
                            Cerrar Visor
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- ─── MODAL 3: RECIBO OFICIAL DATAPLUS ─── --}}
        @if($modalReciboOpen && $reciboActual)
            <div x-data
                 x-on:click.self="$wire.set('modalReciboOpen', false)"
                 x-on:keydown.escape.window="$wire.set('modalReciboOpen', false)"
                 class="dp-modal-overlay">
                <div x-on:click.stop class="dp-modal-card" style="max-width: 500px;">
                    <div class="dp-modal-header">
                        <h3 class="dp-modal-title">
                            <x-heroicon-o-receipt-percent style="width: 1.35rem; height: 1.35rem; color: #38bdf8;" />
                            <span>Recibo Oficial de Pago</span>
                        </h3>
                        <button type="button" wire:click="$set('modalReciboOpen', false)" class="dp-modal-close-btn">
                            <x-heroicon-m-x-mark style="width: 1.25rem; height: 1.25rem;" />
                        </button>
                    </div>

                    <div style="background: #020617; border: 1px solid #334155; border-radius: 0.75rem; padding: 1.25rem; font-family: monospace; font-size: 0.75rem;">
                        <div style="text-align: center; border-bottom: 1px solid #1e293b; padding-bottom: 0.75rem; margin-bottom: 0.75rem;">
                            <h4 style="font-size: 1rem; font-weight: 900; color: #ffffff; margin: 0;">DATAPLUS S.R.L.</h4>
                            <p style="font-size: 0.7rem; color: #94a3b8; margin: 2px 0 0;">Gestión Remota & Soluciones de Conectividad</p>
                            <span class="dp-badge dp-badge-emerald" style="margin-top: 6px;">PAGO VERIFICADO Y REGISTRADO</span>
                        </div>

                        <div style="display: flex; flex-direction: column; gap: 0.4rem; color: #cbd5e1;">
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: #64748b;">No. Recibo:</span>
                                <span style="color: #ffffff; font-weight: 700;">{{ $reciboActual['codigo_pago'] ?? '' }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: #64748b;">Cliente:</span>
                                <span style="color: #ffffff; font-weight: 700;">{{ $reciboActual['cliente_nombre'] ?? '' }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: #64748b;">Servicio:</span>
                                <span style="color: #ffffff;">{{ $reciboActual['servicio_nombre'] ?? 'Gestión Remota' }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: #64748b;">Mes Liquidado:</span>
                                <span style="color: #ffffff;">{{ $reciboActual['mes_nombre'] ?? 'Octubre 2026' }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: #64748b;">Fecha de Pago:</span>
                                <span style="color: #ffffff;">{{ $reciboActual['fecha_pago'] ?? '' }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: #64748b;">Método:</span>
                                <span style="color: #ffffff;">{{ $reciboActual['metodo_pago'] ?? '' }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; border-top: 1px solid #1e293b; padding-top: 0.6rem; margin-top: 0.2rem; font-size: 0.85rem;">
                                <span style="color: #ffffff; font-weight: 700;">Monto Abonado:</span>
                                <span style="color: #34d399; font-weight: 900; font-size: 1rem;">
                                    ${{ number_format($reciboActual['monto_pagado'] ?? 0, 2) }} {{ $reciboActual['moneda'] ?? 'CUP' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 1.25rem; border-top: 1px solid #1e293b; padding-top: 0.75rem;">
                        @php
                            $telR = preg_replace('/[^0-9]/', '', $reciboActual['cliente_telefono'] ?? '');
                            $msgR = rawurlencode("*DATAPLUS - RECIBO DE PAGO*\nRecibo: {$reciboActual['codigo_pago']}\nCliente: {$reciboActual['cliente_nombre']}\nMes: {$reciboActual['mes_nombre']}\nMonto: $" . number_format($reciboActual['monto_pagado'] ?? 0, 2) . " {$reciboActual['moneda']}\n¡Pago verificado y registrado exitosamente!");
                            $linkWpRecibo = "https://wa.me/{$telR}?text={$msgR}";
                        @endphp
                        <a href="{{ $linkWpRecibo }}"
                           target="_blank"
                           class="dp-btn-emerald"
                           style="text-decoration: none;">
                            <x-heroicon-m-share style="width: 1rem; height: 1rem;" />
                            <span>Enviar por WhatsApp</span>
                        </a>

                        <button type="button" wire:click="$set('modalReciboOpen', false)" class="dp-btn-secondary">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- ─── MODAL 4: NUEVA SUSCRIPCIÓN ─── --}}
        @if($modalNuevaSubOpen)
            <div x-data
                 x-on:click.self="$wire.set('modalNuevaSubOpen', false)"
                 x-on:keydown.escape.window="$wire.set('modalNuevaSubOpen', false)"
                 class="dp-modal-overlay">
                <div x-on:click.stop class="dp-modal-card">
                    <div class="dp-modal-header">
                        <h3 class="dp-modal-title">
                            <x-heroicon-o-plus-circle style="width: 1.35rem; height: 1.35rem; color: #38bdf8;" />
                            <span>Nueva Suscripción de Gestión Remota</span>
                        </h3>
                        <button type="button" wire:click="$set('modalNuevaSubOpen', false)" class="dp-modal-close-btn">
                            <x-heroicon-m-x-mark style="width: 1.25rem; height: 1.25rem;" />
                        </button>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.85rem; font-size: 0.8rem;">
                        <div class="dp-input-group">
                            <label class="dp-input-label">Nombre del Cliente / Empresa</label>
                            <input wire:model="newSubClienteNombre" type="text" placeholder="Ej. Hostal La Habana" class="dp-input-field" />
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                            <div class="dp-input-group">
                                <label class="dp-input-label">Teléfono de Contacto</label>
                                <input wire:model="newSubClienteTelefono" type="text" placeholder="+53 5284 9102" class="dp-input-field" />
                            </div>
                            <div class="dp-input-group">
                                <label class="dp-input-label">Número de SIM / Línea</label>
                                <input wire:model="newSubSim" type="text" placeholder="5352849102" class="dp-input-field" />
                            </div>
                        </div>

                        <div class="dp-input-group">
                            <label class="dp-input-label">Solución / Equipo Instalado</label>
                            <select wire:model="newSubSolucion" class="dp-input-field dp-select-field">
                                <option value="Router 4G LTE Huawei B310">Router 4G LTE Huawei B310</option>
                                <option value="Router 4G LTE Huawei B311">Router 4G LTE Huawei B311</option>
                                <option value="Router 4G LTE TP-Link MR6400">Router 4G LTE TP-Link MR6400</option>
                                <option value="Enlace Ubiquiti AirMax">Enlace Ubiquiti AirMax</option>
                                <option value="Sistema DVR / NVR CCTV IP">Sistema DVR / NVR CCTV IP</option>
                            </select>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                            <div class="dp-input-group">
                                <label class="dp-input-label">Moneda</label>
                                <select wire:model="newSubMoneda" class="dp-input-field dp-select-field">
                                    <option value="CUP">Pesos Cubanos (CUP)</option>
                                    <option value="USD">Dólares (USD)</option>
                                </select>
                            </div>
                            <div class="dp-input-group">
                                <label class="dp-input-label">Tarifa Mensual</label>
                                <input wire:model="newSubMonto" type="number" step="0.01" class="dp-input-field" style="font-family: monospace; font-weight: 700; color: #38bdf8;" />
                            </div>
                        </div>

                        <div class="dp-input-group">
                            <label class="dp-input-label">Notas Adicionales</label>
                            <input wire:model="newSubNotas" type="text" placeholder="Ubicación física o detalles de la antena" class="dp-input-field" />
                        </div>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 0.6rem; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #1e293b;">
                        <button type="button" wire:click="$set('modalNuevaSubOpen', false)" class="dp-btn-secondary">
                            Cancelar
                        </button>
                        <button type="button" wire:click="guardarNuevaSub" class="dp-btn-primary">
                            <x-heroicon-m-check style="width: 1.1rem; height: 1.1rem; stroke-width: 2.5;" />
                            <span>Crear Suscripción</span>
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
