<x-filament-panels::page>
    @php
        /** @var array $data */
        $data = $this->getReporteData();
    @endphp

    {{-- ============================================================
         ESTILOS AUTO-CONTENIDOS PARA PANTALLA, VISTA PREVIA Y MODO IMPRESIÓN
         ============================================================ --}}
    <style>
        [x-cloak] { display: none !important; }

        .dp-rep-wrap {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #f1f5f9;
            width: 100%;
            position: relative;
        }

        /* ─── Contenedor de Filtros del Reporte (Pantalla) ─── */
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
            .dp-inputs-row { grid-template-columns: repeat(4, minmax(0, 1fr)); }
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
        }

        .dp-input-field:focus {
            border-color: #38bdf8;
        }

        /* ─── Reglas estrictas para <select> (elimina flechas repetidas) ─── */
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

        select.dp-input-field::-ms-expand,
        select.dp-select-field::-ms-expand {
            display: none !important;
        }

        select.dp-input-field option,
        select.dp-select-field option {
            background-color: #0f172a !important;
            color: #ffffff !important;
        }

        /* ─── Botones de Acción: Vista Previa e Impresión ─── */
        .dp-btn-group-actions {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            flex-wrap: wrap;
        }

        .dp-btn-preview {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            border-radius: 0.5rem;
            background: rgba(30, 41, 59, 0.9);
            color: #38bdf8;
            border: 1px solid rgba(56, 189, 248, 0.4);
            padding: 0.6rem 1.1rem;
            font-size: 0.825rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            height: 38px;
        }

        .dp-btn-preview:hover {
            background: rgba(56, 189, 248, 0.15);
            border-color: #38bdf8;
            color: #ffffff;
            transform: translateY(-1px);
        }

        .dp-btn-print {
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
        }

        .dp-btn-print:hover {
            transform: translateY(-1px);
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        }

        /* ─── Hero / Encabezado de Reporte (Pantalla) ─── */
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

        /* Membrete y firmas ocultos en vista regular de pantalla */
        .dp-print-header { display: none; }
        .dp-print-signatures { display: none; }

        /* ─── Estilos de la Hoja Simulada en el Modal de Vista Previa ─── */
        .dp-sheet {
            background: #ffffff !important;
            color: #0f172a !important;
            width: 100%;
            max-width: 860px;
            margin: 0 auto;
            padding: 3rem;
            border-radius: 4px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.75), 0 0 0 1px rgba(255, 255, 255, 0.1);
            font-size: 0.75rem;
            line-height: 1.4;
        }

        .dp-sheet * {
            box-sizing: border-box;
        }

        .dp-sheet .dp-print-header {
            display: block !important;
            margin-bottom: 1.5rem !important;
            padding-bottom: 0.75rem !important;
            border-bottom: 2px solid #0f172a !important;
        }

        .dp-sheet .dp-rep-hero {
            background: #f8fafc !important;
            border: 2px solid #0f172a !important;
            padding: 1.25rem 1.5rem !important;
            margin-bottom: 1.5rem !important;
            box-shadow: none !important;
            border-radius: 0.5rem !important;
            color: #0f172a !important;
        }

        .dp-sheet .dp-rep-hero-tag {
            color: #475569 !important;
        }

        .dp-sheet .dp-rep-hero-title {
            color: #0f172a !important;
            font-size: 1.45rem !important;
        }

        .dp-sheet .dp-rep-hero-sub {
            color: #334155 !important;
        }

        .dp-sheet .dp-rep-hero-badge {
            background: #f1f5f9 !important;
            border: 1px solid #cbd5e1 !important;
            color: #0f172a !important;
        }

        .dp-sheet .dp-stat-grid-4 {
            display: grid !important;
            grid-template-columns: repeat(4, 1fr) !important;
            gap: 0.75rem !important;
            margin-bottom: 1.5rem !important;
        }

        .dp-sheet .dp-stat-card {
            background: #ffffff !important;
            border: 1px solid #94a3b8 !important;
            padding: 0.85rem !important;
            box-shadow: none !important;
            border-radius: 0.35rem !important;
        }

        .dp-sheet .dp-stat-label {
            color: #475569 !important;
            font-size: 0.7rem !important;
        }

        .dp-sheet .dp-stat-num {
            color: #0f172a !important;
            font-size: 1.25rem !important;
        }

        .dp-sheet .dp-stat-hint {
            color: #64748b !important;
            font-size: 0.65rem !important;
        }

        .dp-sheet .dp-panel {
            background: #ffffff !important;
            border: 1px solid #94a3b8 !important;
            padding: 1rem !important;
            margin-bottom: 1.5rem !important;
            box-shadow: none !important;
            border-radius: 0.35rem !important;
        }

        .dp-sheet .dp-panel-header {
            border-bottom: 1px solid #cbd5e1 !important;
            margin-bottom: 0.75rem !important;
            padding-bottom: 0.5rem !important;
        }

        .dp-sheet .dp-panel-title {
            color: #0f172a !important;
            font-size: 0.9rem !important;
        }

        .dp-sheet .dp-table-wrap {
            background: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            box-shadow: none !important;
        }

        .dp-sheet .dp-table {
            width: 100% !important;
            border-collapse: collapse !important;
        }

        .dp-sheet .dp-table th {
            background: #f1f5f9 !important;
            color: #0f172a !important;
            border-bottom: 2px solid #0f172a !important;
            padding: 0.5rem 0.65rem !important;
            font-size: 0.7rem !important;
        }

        .dp-sheet .dp-table td {
            color: #0f172a !important;
            border-bottom: 1px solid #e2e8f0 !important;
            padding: 0.5rem 0.65rem !important;
            font-size: 0.7rem !important;
        }

        .dp-sheet .dp-print-signatures {
            display: grid !important;
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 4rem !important;
            margin-top: 3rem !important;
            padding-top: 2rem !important;
        }

        .dp-sheet .dp-sig-line {
            border-top: 1px solid #0f172a !important;
            text-align: center !important;
            padding-top: 0.5rem !important;
            font-size: 0.775rem !important;
            font-weight: 700 !important;
            color: #0f172a !important;
        }

        /* ============================================================
           MODO IMPRESIÓN OFICIAL (@media print) - RESISTENTE A COLAPSOS
           ============================================================ */
        @media print {
            @page {
                size: letter portrait;
                margin: 1.25cm;
            }

            /* 1. Ocultar absolutamente todos los elementos de navegación y de control */
            nav,
            aside,
            header,
            [x-data*="sidebarOpen"],
            [class*="sidebar"],
            [class*="topbar"],
            .fi-topbar,
            .fi-sidebar,
            .fi-breadcrumbs,
            .dp-filter-box,
            .dp-btn-group-actions,
            .dp-preview-modal-bar,
            .dp-preview-modal-overlay {
                display: none !important;
                visibility: hidden !important;
                height: 0 !important;
                width: 0 !important;
                overflow: hidden !important;
            }

            /* 2. Neutralizar flexbox, fixed y márgenes del layout en TODA la jerarquía */
            html,
            body,
            body > div,
            div.flex,
            div.min-h-screen,
            div[class*="ml-"],
            .fi-layout,
            .fi-main-ctn,
            .fi-main,
            .fi-page,
            .fi-page-content,
            .dp-rep-wrap {
                display: block !important;
                position: static !important;
                float: none !important;
                margin: 0 !important;
                margin-left: 0 !important;
                padding: 0 !important;
                padding-left: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                min-height: 0 !important;
                height: auto !important;
                overflow: visible !important;
                background: #ffffff !important;
                color: #0f172a !important;
                box-shadow: none !important;
            }

            /* 3. Forzar contenedor de reporte a ocupar la página completa con alto contraste */
            #dp-print-content {
                display: block !important;
                visibility: visible !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #ffffff !important;
                color: #0f172a !important;
                overflow: visible !important;
            }

            #dp-print-content,
            #dp-print-content * {
                color: #0f172a !important;
                text-shadow: none !important;
                box-shadow: none !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            /* 4. Membrete oficial visible */
            .dp-print-header {
                display: block !important;
                margin-bottom: 1.5rem !important;
                padding-bottom: 0.75rem !important;
                border-bottom: 2px solid #0f172a !important;
            }

            /* 5. Hero banner adaptado a escala de grises limpia */
            .dp-rep-hero {
                background: #f8fafc !important;
                border: 2px solid #0f172a !important;
                padding: 1.25rem 1.5rem !important;
                margin-bottom: 1.5rem !important;
                box-shadow: none !important;
                border-radius: 0.5rem !important;
                color: #0f172a !important;
            }

            .dp-rep-hero-tag {
                color: #475569 !important;
            }

            .dp-rep-hero-title {
                color: #0f172a !important;
                font-size: 1.45rem !important;
            }

            .dp-rep-hero-sub {
                color: #334155 !important;
            }

            /* 6. Tarjetas KPI de impresión */
            .dp-stat-grid-4 {
                display: grid !important;
                grid-template-columns: repeat(4, 1fr) !important;
                gap: 0.75rem !important;
                margin-bottom: 1.5rem !important;
            }

            .dp-stat-card {
                background: #ffffff !important;
                border: 1px solid #94a3b8 !important;
                padding: 0.85rem !important;
                box-shadow: none !important;
                border-radius: 0.35rem !important;
            }

            .dp-stat-label {
                color: #475569 !important;
            }

            .dp-stat-num {
                color: #0f172a !important;
                font-size: 1.25rem !important;
            }

            .dp-stat-hint {
                color: #64748b !important;
            }

            /* 7. Paneles y Tablas de impresión */
            .dp-panel {
                background: #ffffff !important;
                border: 1px solid #94a3b8 !important;
                padding: 1rem !important;
                margin-bottom: 1.5rem !important;
                box-shadow: none !important;
                border-radius: 0.35rem !important;
                page-break-inside: avoid;
            }

            .dp-panel-header {
                border-bottom: 1px solid #94a3b8 !important;
                margin-bottom: 0.85rem !important;
            }

            .dp-panel-title {
                color: #0f172a !important;
                font-size: 0.95rem !important;
            }

            .dp-table-wrap {
                background: #ffffff !important;
                border: 1px solid #94a3b8 !important;
                box-shadow: none !important;
            }

            .dp-table {
                width: 100% !important;
                border-collapse: collapse !important;
            }

            .dp-table th {
                background: #f1f5f9 !important;
                color: #0f172a !important;
                border-bottom: 2px solid #0f172a !important;
                padding: 0.5rem 0.65rem !important;
                font-size: 0.725rem !important;
            }

            .dp-table td {
                color: #0f172a !important;
                border-bottom: 1px solid #cbd5e1 !important;
                padding: 0.5rem 0.65rem !important;
                font-size: 0.725rem !important;
            }

            /* 8. Firmas de validación oficial */
            .dp-print-signatures {
                display: grid !important;
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 4rem !important;
                margin-top: 3rem !important;
                padding-top: 2rem !important;
                page-break-inside: avoid;
            }

            .dp-sig-line {
                border-top: 1px solid #0f172a !important;
                text-align: center !important;
                padding-top: 0.5rem !important;
                font-size: 0.8rem !important;
                font-weight: 700 !important;
                color: #0f172a !important;
            }
        }
    </style>

    <div class="dp-rep-wrap" x-data="{ showPreviewModal: false }">

        {{-- ================= PANEL DE FILTROS INTERACTIVOS (OCULTO AL IMPRIMIR) ================= --}}
        <div class="dp-filter-box">
            {{-- Barra de Selección de Período --}}
            <div class="dp-filter-nav">
                <button type="button"
                        wire:click="$set('periodo', 'diario')"
                        class="dp-period-btn {{ $periodo === 'diario' ? 'active' : '' }}">
                    <x-heroicon-o-clock style="width: 1rem; height: 1rem;" />
                    <span>Reporte Diario</span>
                </button>
                <button type="button"
                        wire:click="$set('periodo', 'semanal')"
                        class="dp-period-btn {{ $periodo === 'semanal' ? 'active' : '' }}">
                    <x-heroicon-o-calendar-days style="width: 1rem; height: 1rem;" />
                    <span>Reporte Semanal</span>
                </button>
                <button type="button"
                        wire:click="$set('periodo', 'mensual')"
                        class="dp-period-btn {{ $periodo === 'mensual' ? 'active' : '' }}">
                    <x-heroicon-o-calendar style="width: 1rem; height: 1rem;" />
                    <span>Reporte Mensual</span>
                </button>
                <button type="button"
                        wire:click="$set('periodo', 'anual')"
                        class="dp-period-btn {{ $periodo === 'anual' ? 'active' : '' }}">
                    <x-heroicon-o-chart-pie style="width: 1rem; height: 1rem;" />
                    <span>Balance Anual (12 Meses)</span>
                </button>
                <button type="button"
                        wire:click="$set('periodo', 'personalizado')"
                        class="dp-period-btn {{ $periodo === 'personalizado' ? 'active' : '' }}">
                    <x-heroicon-o-adjustments-horizontal style="width: 1rem; height: 1rem;" />
                    <span>Rango Personalizado</span>
                </button>
            </div>

            {{-- Fila de Parámetros Específicos según Período --}}
            <div class="dp-inputs-row">
                @if($periodo === 'diario')
                    <div class="dp-input-group">
                        <label class="dp-input-label">Día del Reporte</label>
                        <input type="date" wire:model.live="fecha" class="dp-input-field" />
                    </div>
                @elseif($periodo === 'semanal')
                    <div class="dp-input-group">
                        <label class="dp-input-label">Día de Referencia (Semana)</label>
                        <input type="date" wire:model.live="fecha" class="dp-input-field" />
                    </div>
                @elseif($periodo === 'mensual')
                    <div class="dp-input-group">
                        <label class="dp-input-label">Mes a Evaluar</label>
                        <input type="month" wire:model.live="mes" class="dp-input-field" />
                    </div>
                @elseif($periodo === 'anual')
                    <div class="dp-input-group">
                        <label class="dp-input-label">Año Fiscal</label>
                        <select wire:model.live="ano" class="dp-input-field dp-select-field">
                            @foreach(range(2024, 2030) as $y)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                @elseif($periodo === 'personalizado')
                    <div class="dp-input-group">
                        <label class="dp-input-label">Desde</label>
                        <input type="date" wire:model.live="fechaInicio" class="dp-input-field" />
                    </div>
                    <div class="dp-input-group">
                        <label class="dp-input-label">Hasta</label>
                        <input type="date" wire:model.live="fechaFin" class="dp-input-field" />
                    </div>
                @endif

                {{-- Filtro por Especialista / Técnico --}}
                <div class="dp-input-group">
                    <label class="dp-input-label">Especialista / Técnico</label>
                    <select wire:model.live="tecnicoId" class="dp-input-field dp-select-field">
                        <option value="">Todos los especialistas</option>
                        @foreach($this->tecnicos as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Filtro por Cliente --}}
                <div class="dp-input-group">
                    <label class="dp-input-label">Cliente</label>
                    <select wire:model.live="clienteId" class="dp-input-field dp-select-field">
                        <option value="">Todos los clientes</option>
                        @foreach($this->clientes as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Botones de Acción: Vista Previa e Impresión --}}
                <div class="dp-input-group">
                    <label class="dp-input-label" style="opacity: 0;">Acciones</label>
                    <div class="dp-btn-group-actions">
                        <button type="button" @click="showPreviewModal = true" class="dp-btn-preview" title="Ver cómo saldrá el documento impreso antes de enviarlo">
                            <x-heroicon-o-eye style="width: 1.1rem; height: 1.1rem;" />
                            <span>Vista Previa</span>
                        </button>
                        <button type="button" onclick="window.print()" class="dp-btn-print" title="Imprimir directamente o guardar en PDF">
                            <x-heroicon-o-printer style="width: 1.1rem; height: 1.1rem;" />
                            <span>Imprimir / PDF</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================
             CONTENIDO FORMAL DEL REPORTE (ENVUELTO EN #dp-print-content)
             ============================================================ --}}
        <div id="dp-print-content">

            {{-- ─── Membrete Corporativo Oficial (Visible al Imprimir) ─── --}}
            <div class="dp-print-header">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h1 style="font-size: 1.45rem; font-weight: 800; margin: 0; color: #0f172a;">DATAPLUS S.R.L.</h1>
                        <p style="font-size: 0.8rem; color: #475569; margin: 0.2rem 0 0;">Plataforma Oficial de Gestión Técnica, Operativa y Financiera</p>
                    </div>
                    <div style="text-align: right; font-size: 0.775rem; color: #475569;">
                        <p style="margin: 0;"><strong>Fecha de Emisión:</strong> {{ now()->format('d/m/Y H:i') }}</p>
                        <p style="margin: 0.15rem 0 0;"><strong>Emitido por:</strong> {{ auth()->user()->name ?? 'Administración' }}</p>
                    </div>
                </div>
            </div>

            {{-- ─── Hero / Encabezado Principal del Reporte ─── --}}
            <div class="dp-rep-hero">
                <div class="dp-rep-hero-content">
                    <div>
                        <p class="dp-rep-hero-tag">Documento de Rendición & Control Operativo</p>
                        <h1 class="dp-rep-hero-title">{{ $data['etiqueta_periodo'] }}</h1>
                        <p class="dp-rep-hero-sub">
                            DataPlus S.R.L. · {{ $data['incidencias_total'] }} asistencias técnicas · 
                            {{ $data['proyectos_count'] }} proyectos vinculados · 
                            Gastos especialista: ${{ number_format($data['gastos_campo_total'], 2) }} CUP
                        </p>
                    </div>
                    <div class="dp-rep-hero-badge" style="text-align: right; background: rgba(255, 255, 255, 0.12); border: 1px solid rgba(255, 255, 255, 0.25); border-radius: 0.85rem; padding: 0.85rem 1.35rem; backdrop-filter: blur(10px);">
                        <p style="font-size: 0.65rem; text-transform: uppercase; font-weight: 700; color: #e0f2fe; margin: 0;">Dinero Generado en Período</p>
                        <p style="font-size: 1.65rem; font-weight: 800; color: #ffffff; margin: 0.25rem 0 0; line-height: 1;">
                            ${{ number_format($data['dinero_generado_total'], 2) }} <span style="font-size: 0.85rem; color: #7dd3fc;">CUP</span>
                        </p>
                    </div>
                </div>
            </div>

            {{-- ─── 4 Tarjetas KPI del Período ─── --}}
            <div class="dp-stat-grid-4">
                {{-- 1. Dinero Generado Total --}}
                <div class="dp-stat-card">
                    <div class="dp-stat-top">
                        <span class="dp-stat-label">Dinero Generado</span>
                        <div style="border-radius: 0.5rem; padding: 0.45rem; background: rgba(14, 165, 233, 0.15); color: #38bdf8;">
                            <x-heroicon-o-banknotes style="width: 1.1rem; height: 1.1rem;" />
                        </div>
                    </div>
                    <p class="dp-stat-num" style="color: #38bdf8;">${{ number_format($data['dinero_generado_total'], 2) }}</p>
                    <p class="dp-stat-hint">Obras, proyectos y facturación</p>
                </div>

                {{-- 2. Gastos de Campo (Transporte + Almuerzo) --}}
                <div class="dp-stat-card">
                    <div class="dp-stat-top">
                        <span class="dp-stat-label">Gastos Especialistas</span>
                        <div style="border-radius: 0.5rem; padding: 0.45rem; background: rgba(245, 158, 11, 0.15); color: #fbbf24;">
                            <x-heroicon-o-truck style="width: 1.1rem; height: 1.1rem;" />
                        </div>
                    </div>
                    <p class="dp-stat-num" style="color: #fbbf24;">${{ number_format($data['gastos_campo_total'], 2) }}</p>
                    <p class="dp-stat-hint">Transporte ${{ number_format(($data['gastos_transporte'] ?? $data['gasto_transporte_total'] ?? 0), 2) }} · Almuerzo ${{ number_format(($data['gastos_almuerzo'] ?? $data['gasto_almuerzo_total'] ?? 0), 2) }}</p>
                </div>

                {{-- 3. Balance Neto --}}
                <div class="dp-stat-card">
                    <div class="dp-stat-top">
                        <span class="dp-stat-label">Balance Neto Operativo</span>
                        <div style="border-radius: 0.5rem; padding: 0.45rem; background: rgba(16, 185, 129, 0.15); color: #34d399;">
                            <x-heroicon-o-scale style="width: 1.1rem; height: 1.1rem;" />
                        </div>
                    </div>
                    <p class="dp-stat-num" style="color: #34d399;">${{ number_format(($data['margen_operativo_neto'] ?? $data['margen_neto_total'] ?? 0), 2) }}</p>
                    <p class="dp-stat-hint">Ingresos menos gastos en terreno</p>
                </div>

                {{-- 4. Cumplimiento de SLA --}}
                <div class="dp-stat-card">
                    <div class="dp-stat-top">
                        <span class="dp-stat-label">Cumplimiento de SLA</span>
                        <div style="border-radius: 0.5rem; padding: 0.45rem; background: rgba(168, 85, 247, 0.15); color: #c084fc;">
                            <x-heroicon-o-check-badge style="width: 1.1rem; height: 1.1rem;" />
                        </div>
                    </div>
                    <p class="dp-stat-num" style="color: #c084fc;">{{ $data['tasa_sla'] }}%</p>
                    <p class="dp-stat-hint">{{ $data['cumplieron_sla'] }} dentro de SLA / {{ $data['excedieron_sla'] }} fuera</p>
                </div>
            </div>

            {{-- ─── SECCIÓN A: GASTOS DE ESPECIALISTAS (TRANSPORTE Y ALMUERZO) ─── --}}
            <div class="dp-panel">
                <div class="dp-panel-header">
                    <h3 class="dp-panel-title">
                        <x-heroicon-o-user-group style="width: 1.1rem; height: 1.1rem; color: #fbbf24;" />
                        Rendición de Gastos de Especialistas (Transporte & Almuerzo)
                    </h3>
                    <span style="font-size: 0.75rem; color: #fbbf24; font-weight: 700; background: rgba(245, 158, 11, 0.15); padding: 0.2rem 0.6rem; border-radius: 0.35rem; border: 1px solid rgba(245, 158, 11, 0.25);">
                        Total Campo: ${{ number_format($data['gastos_campo_total'], 2) }} CUP
                    </span>
                </div>
                <div class="dp-table-wrap">
                    <table class="dp-table">
                        <thead>
                            <tr>
                                <th>Especialista / Técnico</th>
                                <th style="text-align: center;">Incidencias Atendidas</th>
                                <th style="text-align: right;">Gasto Transporte</th>
                                <th style="text-align: right;">Gasto Almuerzo</th>
                                <th style="text-align: right;">Total Gastos en Terreno</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data['gastos_por_especialista'] as $esp)
                                <tr>
                                    <td>
                                        <div style="font-weight: 700; color: #ffffff;">{{ $esp->tecnico?->name ?? 'Técnico Sin Asignar' }}</div>
                                        <div style="font-size: 0.675rem; color: #94a3b8;">{{ $esp->tecnico?->email ?? '' }}</div>
                                    </td>
                                    <td style="text-align: center; font-weight: 700;">{{ $esp->total_incidencias }}</td>
                                    <td style="text-align: right; font-family: monospace; color: #38bdf8;">
                                        ${{ number_format($esp->total_transporte, 2) }}
                                    </td>
                                    <td style="text-align: right; font-family: monospace; color: #fbbf24;">
                                        ${{ number_format($esp->total_almuerzo, 2) }}
                                    </td>
                                    <td style="text-align: right; font-family: monospace; font-weight: 800; color: #ffffff;">
                                        ${{ number_format($esp->total_campo, 2) }} CUP
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 2rem; color: #64748b;">
                                        No hay gastos de campo reportados por especialistas en este período.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ─── SECCIÓN B: BALANCE ANUAL MES A MES (SI PERIODO ES 'ANUAL') ─── --}}
            @if($periodo === 'anual')
                <div class="dp-panel">
                    <div class="dp-panel-header">
                        <h3 class="dp-panel-title">
                            <x-heroicon-o-scale style="width: 1.1rem; height: 1.1rem; color: #34d399;" />
                            Balance Financiero Mes a Mes del Año {{ $ano }}
                        </h3>
                        <span style="font-size: 0.75rem; color: #34d399; font-weight: 700; background: rgba(16, 185, 129, 0.15); padding: 0.2rem 0.6rem; border-radius: 0.35rem; border: 1px solid rgba(52, 211, 153, 0.25);">
                            12 Meses Consolidados
                        </span>
                    </div>
                    <div class="dp-table-wrap">
                        <table class="dp-table">
                            <thead>
                                <tr>
                                    <th>Mes</th>
                                    <th style="text-align: right;">Obras & Proyectos</th>
                                    <th style="text-align: right;">Servicios & Asistencias</th>
                                    <th style="text-align: right;">Total Ingresos</th>
                                    <th style="text-align: right;">Transporte Especialistas</th>
                                    <th style="text-align: right;">Almuerzos Especialistas</th>
                                    <th style="text-align: right;">Total Gastos Campo</th>
                                    <th style="text-align: right;">Balance Neto Operativo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totProy = 0; $totServ = 0; $totIng = 0;
                                    $totTrans = 0; $totAlm = 0; $totGastos = 0; $totNeto = 0;
                                @endphp
                                @foreach($data['balance_meses'] as $bm)
                                    @php
                                        $totProy += $bm['proyectos'];
                                        $totServ += $bm['servicios'];
                                        $totIng += $bm['ingresos'];
                                        $totTrans += $bm['transporte'];
                                        $totAlm += $bm['almuerzo'];
                                        $totGastos += $bm['gastos_campo'];
                                        $totNeto += $bm['margen_neto'];
                                    @endphp
                                    <tr>
                                        <td style="font-weight: 700; color: #ffffff; text-transform: capitalize;">
                                            {{ $bm['mes_nombre'] }}
                                        </td>
                                        <td style="text-align: right; font-family: monospace; color: #38bdf8;">
                                            ${{ number_format($bm['proyectos'], 2) }}
                                        </td>
                                        <td style="text-align: right; font-family: monospace; color: #c084fc;">
                                            ${{ number_format($bm['servicios'], 2) }}
                                        </td>
                                        <td style="text-align: right; font-family: monospace; font-weight: 700; color: #ffffff;">
                                            ${{ number_format($bm['ingresos'], 2) }}
                                        </td>
                                        <td style="text-align: right; font-family: monospace; color: #94a3b8;">
                                            ${{ number_format($bm['transporte'], 2) }}
                                        </td>
                                        <td style="text-align: right; font-family: monospace; color: #94a3b8;">
                                            ${{ number_format($bm['almuerzo'], 2) }}
                                        </td>
                                        <td style="text-align: right; font-family: monospace; color: #fbbf24;">
                                            ${{ number_format($bm['gastos_campo'], 2) }}
                                        </td>
                                        <td style="text-align: right; font-family: monospace; font-weight: 800; color: {{ $bm['margen_neto'] >= 0 ? '#34d399' : '#fb7185' }};">
                                            ${{ number_format($bm['margen_neto'], 2) }} CUP
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr style="background: rgba(15, 23, 42, 0.95); font-weight: 800; border-top: 2px solid #38bdf8;">
                                    <td style="color: #ffffff;">TOTAL AÑO {{ $ano }}</td>
                                    <td style="text-align: right; font-family: monospace; color: #38bdf8;">${{ number_format($totProy, 2) }}</td>
                                    <td style="text-align: right; font-family: monospace; color: #c084fc;">${{ number_format($totServ, 2) }}</td>
                                    <td style="text-align: right; font-family: monospace; color: #ffffff;">${{ number_format($totIng, 2) }}</td>
                                    <td style="text-align: right; font-family: monospace; color: #94a3b8;">${{ number_format($totTrans, 2) }}</td>
                                    <td style="text-align: right; font-family: monospace; color: #94a3b8;">${{ number_format($totAlm, 2) }}</td>
                                    <td style="text-align: right; font-family: monospace; color: #fbbf24;">${{ number_format($totGastos, 2) }}</td>
                                    <td style="text-align: right; font-family: monospace; color: #34d399; font-size: 0.85rem;">
                                        ${{ number_format($totNeto, 2) }} CUP
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @endif

            {{-- ─── SECCIÓN C: INCIDENCIAS Y ASISTENCIAS DETALLADAS DEL PERÍODO ─── --}}
            <div class="dp-panel">
                <div class="dp-panel-header">
                    <h3 class="dp-panel-title">
                        <x-heroicon-o-wrench-screwdriver style="width: 1.1rem; height: 1.1rem; color: #38bdf8;" />
                        Registro de Incidencias Técnicas & Cumplimiento de SLA
                    </h3>
                    <span style="font-size: 0.75rem; color: #94a3b8;">
                        {{ count($data['incidencias_detalladas']) }} asistencias mostradas
                    </span>
                </div>
                <div class="dp-table-wrap">
                    <table class="dp-table">
                        <thead>
                            <tr>
                                <th>Código / Fecha</th>
                                <th>Cliente / Sede</th>
                                <th>Servicio</th>
                                <th>Especialista</th>
                                <th>Estado / SLA</th>
                                <th style="text-align: right;">Gasto Transp.</th>
                                <th style="text-align: right;">Gasto Almuerzo</th>
                                <th style="text-align: right;">Total Gastos</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data['incidencias_detalladas'] as $inc)
                                <tr>
                                    <td>
                                        <div style="font-family: monospace; font-weight: 700; color: #38bdf8;">{{ $inc->codigo }}</div>
                                        <div style="font-size: 0.675rem; color: #94a3b8;">{{ $inc->fecha_reporte?->format('d/m/Y H:i') ?? $inc->created_at->format('d/m/Y') }}</div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600; color: #ffffff;">{{ $inc->cliente?->nombre ?? 'Sin cliente' }}</div>
                                        <div style="font-size: 0.675rem; color: #94a3b8;">{{ $inc->titulo }}</div>
                                    </td>
                                    <td>
                                        <span style="padding: 0.15rem 0.45rem; border-radius: 0.25rem; font-size: 0.65rem; font-weight: 700; background: rgba(14, 165, 233, 0.15); color: #38bdf8;">
                                            {{ $inc->tipo }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $inc->tecnico?->name ?? 'Sin asignar' }}
                                    </td>
                                    <td>
                                        @if($inc->fecha_resolucion)
                                            @if($inc->cumplio_sla === true)
                                                <span style="color: #34d399; font-weight: 700;">✓ Dentro de SLA</span>
                                            @elseif($inc->cumplio_sla === false)
                                                <span style="color: #fb7185; font-weight: 700;">⚠ Excedió SLA</span>
                                            @else
                                                <span style="color: #94a3b8;">Resuelta</span>
                                            @endif
                                        @else
                                            <span style="color: #fbbf24;">En curso</span>
                                        @endif
                                    </td>
                                    <td style="text-align: right; font-family: monospace;">
                                        ${{ number_format($inc->gasto_transporte ?? 0, 2) }}
                                    </td>
                                    <td style="text-align: right; font-family: monospace;">
                                        ${{ number_format($inc->gasto_almuerzo ?? 0, 2) }}
                                    </td>
                                    <td style="text-align: right; font-family: monospace; font-weight: 700; color: #fbbf24;">
                                        ${{ number_format($inc->total_gastos_operativos, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" style="text-align: center; padding: 2rem; color: #64748b;">
                                        No hay incidencias técnicas registradas en este rango de fechas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ─── Sección de Firmas visible solo en impresión / PDF ─── --}}
            <div class="dp-print-signatures">
                <div class="dp-sig-line">
                    <p style="margin: 0;">Firma del Responsable de Operaciones</p>
                    <p style="margin: 0.25rem 0 0; font-size: 0.7rem; font-weight: normal; color: #475569;">DataPlus S.R.L.</p>
                </div>
                <div class="dp-sig-line">
                    <p style="margin: 0;">VºBº Dirección General / Auditoría</p>
                    <p style="margin: 0.25rem 0 0; font-size: 0.7rem; font-weight: normal; color: #475569;">DataPlus S.R.L.</p>
                </div>
            </div>

        </div> {{-- Fin de #dp-print-content --}}

        {{-- ============================================================
             MODAL INTERACTIVO DE VISTA PREVIA DEL DOCUMENTO (SIMULACIÓN A4 / CARTA)
             ============================================================ --}}
        <div x-show="showPreviewModal"
             x-cloak
             @keydown.escape.window="showPreviewModal = false"
             class="dp-preview-modal-overlay"
             style="position: fixed; inset: 0; z-index: 99999; background: rgba(2, 6, 23, 0.92); backdrop-filter: blur(8px); display: flex; flex-direction: column; overflow-y: auto;">

            {{-- Barra Superior de Control del Modal --}}
            <div class="dp-preview-modal-bar" style="position: sticky; top: 0; z-index: 30; background: #0f172a; border-bottom: 1px solid #334155; padding: 12px 24px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 4px 14px rgba(0,0,0,0.6);">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 36px; height: 36px; border-radius: 9px; background: rgba(14, 165, 233, 0.15); display: flex; align-items: center; justify-content: center; color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.3);">
                        <x-heroicon-o-document-text style="width: 1.3rem; height: 1.3rem;" />
                    </div>
                    <div>
                        <h4 style="margin: 0; font-size: 0.95rem; font-weight: 800; color: #ffffff;">Vista Previa del Documento a Imprimir</h4>
                        <p style="margin: 2px 0 0; font-size: 0.725rem; color: #94a3b8;">Hoja Oficial DataPlus S.R.L. · Verifique los datos antes de imprimir o descargar en PDF</p>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <button type="button"
                            onclick="window.print()"
                            style="display: inline-flex; align-items: center; gap: 8px; background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); color: #ffffff; border: none; border-radius: 8px; padding: 8px 18px; font-size: 0.825rem; font-weight: 700; cursor: pointer; box-shadow: 0 4px 12px rgba(2, 132, 199, 0.4); transition: transform 0.15s ease;"
                            onmouseover="this.style.transform='translateY(-1px)'"
                            onmouseout="this.style.transform='translateY(0)'">
                        <x-heroicon-o-printer style="width: 1.15rem; height: 1.15rem;" />
                        <span>Imprimir / Descargar PDF</span>
                    </button>
                    <button type="button"
                            @click="showPreviewModal = false"
                            style="display: inline-flex; align-items: center; gap: 6px; background: #1e293b; color: #cbd5e1; border: 1px solid #475569; border-radius: 8px; padding: 8px 16px; font-size: 0.825rem; font-weight: 600; cursor: pointer;">
                        <x-heroicon-o-x-mark style="width: 1.15rem; height: 1.15rem;" />
                        <span>Cerrar</span>
                    </button>
                </div>
            </div>

            {{-- Hoja de Papel Blanco Simulada --}}
            <div style="flex: 1; padding: 32px 16px 60px; display: flex; justify-content: center; background: rgba(15, 23, 42, 0.65);">
                <div class="dp-sheet" id="dp-sheet-preview-render">

                    {{-- Membrete Oficial --}}
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; padding-bottom: 12px; border-bottom: 2px solid #0f172a;">
                        <div>
                            <h2 style="font-size: 1.4rem; font-weight: 900; margin: 0; color: #0f172a; letter-spacing: -0.01em;">DATAPLUS S.R.L.</h2>
                            <p style="font-size: 0.775rem; color: #475569; margin: 3px 0 0; font-weight: 500;">Plataforma Oficial de Gestión Técnica, Operativa y Financiera</p>
                        </div>
                        <div style="text-align: right; font-size: 0.75rem; color: #475569;">
                            <p style="margin: 0;"><strong>Fecha de Emisión:</strong> {{ now()->format('d/m/Y H:i') }}</p>
                            <p style="margin: 2px 0 0;"><strong>Emitido por:</strong> {{ auth()->user()->name ?? 'Administración' }}</p>
                        </div>
                    </div>

                    {{-- Encabezado del Período --}}
                    <div style="background: #f8fafc; border: 1.5px solid #0f172a; border-radius: 8px; padding: 16px 20px; margin-bottom: 20px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap;">
                            <div>
                                <p style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #475569; margin: 0; letter-spacing: 0.05em;">Rendición & Control Operativo</p>
                                <h3 style="font-size: 1.35rem; font-weight: 800; color: #0f172a; margin: 4px 0;">{{ $data['etiqueta_periodo'] }}</h3>
                                <p style="font-size: 0.775rem; color: #334155; margin: 4px 0 0;">
                                    DataPlus S.R.L. · {{ $data['incidencias_total'] }} asistencias técnicas · 
                                    {{ $data['proyectos_count'] }} proyectos vinculados · 
                                    Gastos especialista: ${{ number_format($data['gastos_campo_total'], 2) }} CUP
                                </p>
                            </div>
                            <div style="text-align: right; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px 16px;">
                                <p style="font-size: 0.65rem; text-transform: uppercase; font-weight: 700; color: #475569; margin: 0;">Dinero Generado Total</p>
                                <p style="font-size: 1.45rem; font-weight: 900; color: #0284c7; margin: 2px 0 0;">
                                    ${{ number_format($data['dinero_generado_total'], 2) }} <span style="font-size: 0.75rem; color: #64748b;">CUP</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Cuadrícula de 4 KPIs --}}
                    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-bottom: 22px;">
                        <div style="background: #ffffff; border: 1px solid #94a3b8; border-radius: 6px; padding: 10px 12px;">
                            <span style="font-size: 0.675rem; font-weight: 700; color: #475569; text-transform: uppercase;">Dinero Generado</span>
                            <div style="font-size: 1.2rem; font-weight: 800; color: #0284c7; margin-top: 4px;">${{ number_format($data['dinero_generado_total'], 2) }}</div>
                            <span style="font-size: 0.65rem; color: #64748b;">Obras y contratos</span>
                        </div>
                        <div style="background: #ffffff; border: 1px solid #94a3b8; border-radius: 6px; padding: 10px 12px;">
                            <span style="font-size: 0.675rem; font-weight: 700; color: #475569; text-transform: uppercase;">Gastos Terreno</span>
                            <div style="font-size: 1.2rem; font-weight: 800; color: #d97706; margin-top: 4px;">${{ number_format($data['gastos_campo_total'], 2) }}</div>
                            <span style="font-size: 0.65rem; color: #64748b;">Transp. ${{ number_format(($data['gastos_transporte'] ?? $data['gasto_transporte_total'] ?? 0), 2) }} + Alm.</span>
                        </div>
                        <div style="background: #ffffff; border: 1px solid #94a3b8; border-radius: 6px; padding: 10px 12px;">
                            <span style="font-size: 0.675rem; font-weight: 700; color: #475569; text-transform: uppercase;">Balance Neto</span>
                            <div style="font-size: 1.2rem; font-weight: 800; color: #059669; margin-top: 4px;">${{ number_format(($data['margen_operativo_neto'] ?? $data['margen_neto_total'] ?? 0), 2) }}</div>
                            <span style="font-size: 0.65rem; color: #64748b;">Margen operativo</span>
                        </div>
                        <div style="background: #ffffff; border: 1px solid #94a3b8; border-radius: 6px; padding: 10px 12px;">
                            <span style="font-size: 0.675rem; font-weight: 700; color: #475569; text-transform: uppercase;">Cumplimiento SLA</span>
                            <div style="font-size: 1.2rem; font-weight: 800; color: #7c3aed; margin-top: 4px;">{{ $data['tasa_sla'] }}%</div>
                            <span style="font-size: 0.65rem; color: #64748b;">{{ $data['cumplieron_sla'] }} dentro / {{ $data['excedieron_sla'] }} fuera</span>
                        </div>
                    </div>

                    {{-- Tabla Gastos de Especialistas --}}
                    <div style="margin-bottom: 22px; border: 1px solid #cbd5e1; border-radius: 6px; overflow: hidden;">
                        <div style="background: #f1f5f9; padding: 8px 12px; border-bottom: 1px solid #cbd5e1; display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-weight: 800; color: #0f172a; font-size: 0.775rem;">1. Rendición de Gastos de Especialistas (Transporte y Almuerzo)</span>
                            <span style="font-weight: 700; color: #d97706; font-size: 0.725rem;">Total: ${{ number_format($data['gastos_campo_total'], 2) }} CUP</span>
                        </div>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.725rem;">
                            <thead>
                                <tr style="background: #f8fafc; border-bottom: 1px solid #cbd5e1;">
                                    <th style="padding: 6px 10px; text-align: left; font-size: 0.675rem; text-transform: uppercase; color: #475569;">Especialista / Técnico</th>
                                    <th style="padding: 6px 10px; text-align: center; font-size: 0.675rem; text-transform: uppercase; color: #475569;">Asistencias</th>
                                    <th style="padding: 6px 10px; text-align: right; font-size: 0.675rem; text-transform: uppercase; color: #475569;">Gasto Transporte</th>
                                    <th style="padding: 6px 10px; text-align: right; font-size: 0.675rem; text-transform: uppercase; color: #475569;">Gasto Almuerzo</th>
                                    <th style="padding: 6px 10px; text-align: right; font-size: 0.675rem; text-transform: uppercase; color: #475569;">Total Gastos Campo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data['gastos_por_especialista'] as $esp)
                                    <tr style="border-bottom: 1px solid #e2e8f0;">
                                        <td style="padding: 6px 10px; font-weight: 700; color: #0f172a;">{{ $esp->tecnico?->name ?? 'Técnico Sin Asignar' }}</td>
                                        <td style="padding: 6px 10px; text-align: center; font-weight: 700;">{{ $esp->total_incidencias }}</td>
                                        <td style="padding: 6px 10px; text-align: right; font-family: monospace;">${{ number_format($esp->total_transporte, 2) }}</td>
                                        <td style="padding: 6px 10px; text-align: right; font-family: monospace;">${{ number_format($esp->total_almuerzo, 2) }}</td>
                                        <td style="padding: 6px 10px; text-align: right; font-family: monospace; font-weight: 800; color: #0f172a;">${{ number_format($esp->total_campo, 2) }} CUP</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" style="padding: 14px; text-align: center; color: #64748b;">No hay gastos de especialistas registrados en este período.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Tabla Balance Anual (si período es anual) --}}
                    @if($periodo === 'anual')
                        <div style="margin-bottom: 22px; border: 1px solid #cbd5e1; border-radius: 6px; overflow: hidden;">
                            <div style="background: #f1f5f9; padding: 8px 12px; border-bottom: 1px solid #cbd5e1; display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-weight: 800; color: #0f172a; font-size: 0.775rem;">2. Balance Financiero Consolidado Mes a Mes — Año {{ $ano }}</span>
                                <span style="font-weight: 700; color: #059669; font-size: 0.725rem;">12 Meses</span>
                            </div>
                            <table style="width: 100%; border-collapse: collapse; font-size: 0.725rem;">
                                <thead>
                                    <tr style="background: #f8fafc; border-bottom: 1px solid #cbd5e1;">
                                        <th style="padding: 5px 8px; text-align: left; font-size: 0.65rem; color: #475569;">Mes</th>
                                        <th style="padding: 5px 8px; text-align: right; font-size: 0.65rem; color: #475569;">Proyectos</th>
                                        <th style="padding: 5px 8px; text-align: right; font-size: 0.65rem; color: #475569;">Servicios</th>
                                        <th style="padding: 5px 8px; text-align: right; font-size: 0.65rem; color: #475569;">Total Ingresos</th>
                                        <th style="padding: 5px 8px; text-align: right; font-size: 0.65rem; color: #475569;">Gastos Campo</th>
                                        <th style="padding: 5px 8px; text-align: right; font-size: 0.65rem; color: #475569;">Balance Neto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $totIngAño = 0; $totGastoAño = 0; $totNetoAño = 0; @endphp
                                    @foreach($data['balance_meses'] as $bm)
                                        @php
                                            $totIngAño += $bm['ingresos'];
                                            $totGastoAño += $bm['gastos_campo'];
                                            $totNetoAño += $bm['margen_neto'];
                                        @endphp
                                        <tr style="border-bottom: 1px solid #e2e8f0;">
                                            <td style="padding: 5px 8px; font-weight: 700; text-transform: capitalize;">{{ $bm['mes_nombre'] }}</td>
                                            <td style="padding: 5px 8px; text-align: right; font-family: monospace;">${{ number_format($bm['proyectos'], 2) }}</td>
                                            <td style="padding: 5px 8px; text-align: right; font-family: monospace;">${{ number_format($bm['servicios'], 2) }}</td>
                                            <td style="padding: 5px 8px; text-align: right; font-family: monospace; font-weight: 700;">${{ number_format($bm['ingresos'], 2) }}</td>
                                            <td style="padding: 5px 8px; text-align: right; font-family: monospace; color: #d97706;">${{ number_format($bm['gastos_campo'], 2) }}</td>
                                            <td style="padding: 5px 8px; text-align: right; font-family: monospace; font-weight: 800; color: {{ $bm['margen_neto'] >= 0 ? '#059669' : '#e11d48' }};">
                                                ${{ number_format($bm['margen_neto'], 2) }} CUP
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr style="background: #f1f5f9; font-weight: 800; border-top: 1.5px solid #0f172a;">
                                        <td style="padding: 6px 8px;">TOTAL {{ $ano }}</td>
                                        <td colspan="2"></td>
                                        <td style="padding: 6px 8px; text-align: right; font-family: monospace;">${{ number_format($totIngAño, 2) }}</td>
                                        <td style="padding: 6px 8px; text-align: right; font-family: monospace; color: #d97706;">${{ number_format($totGastoAño, 2) }}</td>
                                        <td style="padding: 6px 8px; text-align: right; font-family: monospace; color: #059669;">${{ number_format($totNetoAño, 2) }} CUP</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @endif

                    {{-- Tabla de Asistencias e Incidencias Técnicas --}}
                    <div style="margin-bottom: 26px; border: 1px solid #cbd5e1; border-radius: 6px; overflow: hidden;">
                        <div style="background: #f1f5f9; padding: 8px 12px; border-bottom: 1px solid #cbd5e1; display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-weight: 800; color: #0f172a; font-size: 0.775rem;">{{ $periodo === 'anual' ? '3.' : '2.' }} Registro de Incidencias Técnicas & Cumplimiento SLA</span>
                            <span style="font-size: 0.725rem; color: #64748b;">{{ count($data['incidencias_detalladas']) }} asistencias</span>
                        </div>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.725rem;">
                            <thead>
                                <tr style="background: #f8fafc; border-bottom: 1px solid #cbd5e1;">
                                    <th style="padding: 5px 8px; text-align: left; font-size: 0.65rem; color: #475569;">Código / Fecha</th>
                                    <th style="padding: 5px 8px; text-align: left; font-size: 0.65rem; color: #475569;">Cliente</th>
                                    <th style="padding: 5px 8px; text-align: left; font-size: 0.65rem; color: #475569;">Servicio</th>
                                    <th style="padding: 5px 8px; text-align: left; font-size: 0.65rem; color: #475569;">Especialista</th>
                                    <th style="padding: 5px 8px; text-align: left; font-size: 0.65rem; color: #475569;">Estado / SLA</th>
                                    <th style="padding: 5px 8px; text-align: right; font-size: 0.65rem; color: #475569;">Gasto Transp.</th>
                                    <th style="padding: 5px 8px; text-align: right; font-size: 0.65rem; color: #475569;">Gasto Alm.</th>
                                    <th style="padding: 5px 8px; text-align: right; font-size: 0.65rem; color: #475569;">Total Gastos</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data['incidencias_detalladas'] as $inc)
                                    <tr style="border-bottom: 1px solid #e2e8f0;">
                                        <td style="padding: 5px 8px;">
                                            <div style="font-family: monospace; font-weight: 700; color: #0284c7;">{{ $inc->codigo }}</div>
                                            <div style="font-size: 0.625rem; color: #64748b;">{{ $inc->fecha_reporte?->format('d/m/Y') ?? $inc->created_at->format('d/m/Y') }}</div>
                                        </td>
                                        <td style="padding: 5px 8px; font-weight: 600; color: #0f172a;">{{ $inc->cliente?->nombre ?? 'Sin cliente' }}</td>
                                        <td style="padding: 5px 8px; color: #475569;">{{ $inc->tipo }}</td>
                                        <td style="padding: 5px 8px; color: #334155;">{{ $inc->tecnico?->name ?? 'Sin asignar' }}</td>
                                        <td style="padding: 5px 8px;">
                                            @if($inc->fecha_resolucion)
                                                @if($inc->cumplio_sla === true)
                                                    <span style="color: #059669; font-weight: 700;">✓ SLA Cumplido</span>
                                                @elseif($inc->cumplio_sla === false)
                                                    <span style="color: #e11d48; font-weight: 700;">⚠ Fuera SLA</span>
                                                @else
                                                    <span style="color: #475569;">Resuelta</span>
                                                @endif
                                            @else
                                                <span style="color: #d97706;">En curso</span>
                                            @endif
                                        </td>
                                        <td style="padding: 5px 8px; text-align: right; font-family: monospace;">${{ number_format($inc->gasto_transporte ?? 0, 2) }}</td>
                                        <td style="padding: 5px 8px; text-align: right; font-family: monospace;">${{ number_format($inc->gasto_almuerzo ?? 0, 2) }}</td>
                                        <td style="padding: 5px 8px; text-align: right; font-family: monospace; font-weight: 700; color: #d97706;">${{ number_format($inc->total_gastos_operativos, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" style="padding: 14px; text-align: center; color: #64748b;">No hay incidencias registradas en este período.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Firmas Institucionales Oficiales --}}
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 48px; margin-top: 36px; padding-top: 16px;">
                        <div style="border-top: 1.5px solid #0f172a; text-align: center; padding-top: 6px;">
                            <p style="margin: 0; font-size: 0.775rem; font-weight: 700; color: #0f172a;">Firma del Responsable de Operaciones</p>
                            <p style="margin: 2px 0 0; font-size: 0.675rem; color: #64748b;">DataPlus S.R.L.</p>
                        </div>
                        <div style="border-top: 1.5px solid #0f172a; text-align: center; padding-top: 6px;">
                            <p style="margin: 0; font-size: 0.775rem; font-weight: 700; color: #0f172a;">VºBº Dirección General / Auditoría</p>
                            <p style="margin: 2px 0 0; font-size: 0.675rem; color: #64748b;">DataPlus S.R.L.</p>
                        </div>
                    </div>

                </div>
            </div>

        </div> {{-- Fin de Modal de Vista Previa --}}

    </div>

    {{-- ============================================================
         SCRIPT JS: MOTOR DE IMPRESIÓN AISLADO Y ROBUSTO (SIN PANTALLA EN BLANCO)
         ============================================================ --}}
    <script>
        function imprimirDocumentoReporte() {
            window.print();
        }
    </script>
</x-filament-panels::page>
