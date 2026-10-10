<x-filament-panels::page>
    @php
        /** @var array $metricas */
        $metricas = $this->getMetricas();
        $suscripciones = $this->getSuscripciones();
        $pagosSemana = $this->getPagosSemana();
        $mayoresDeudores = $this->getMayoresDeudores();
        $reporteTemporal = $this->getDatosReporteTemporal();
    @endphp

    <style>
        [x-cloak] { display: none !important; }
        .dp-fact-wrap {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #f1f5f9;
            width: 100%;
        }
        .dp-stat-card {
            background: linear-gradient(145deg, rgba(15, 23, 42, 0.95), rgba(30, 41, 59, 0.7));
            border: 1px solid #334155;
            border-radius: 1rem;
            padding: 1.25rem 1.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            transition: all 0.2s ease;
        }
        .dp-stat-card:hover {
            border-color: #0284c7;
            transform: translateY(-2px);
        }
    </style>

    <div class="dp-fact-wrap space-y-6">

        {{-- ─── 1. BANNER DE REGLA: MES ADELANTADO (DÍAS 1 AL 15) ─── --}}
        <div class="rounded-2xl border border-sky-500/30 bg-gradient-to-r from-sky-950/80 via-slate-900 to-indigo-950/80 p-5 shadow-xl relative overflow-hidden">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 relative z-10">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 rounded-xl bg-sky-500/20 border border-sky-500/40 flex items-center justify-center text-sky-400">
                        <x-heroicon-o-calendar-days class="h-6 w-6" />
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-lg font-bold text-white tracking-tight">Periodo de Cobro: Octubre 2026</h2>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-sky-500/20 text-sky-300 border border-sky-500/30">
                                Mes Adelantado
                            </span>
                        </div>
                        <p class="text-xs text-slate-300 mt-1">
                            Norma operativa: El servicio de Gestión Remota se liquida en los <strong class="text-white">primeros 15 días</strong> del mes en curso.
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="bg-slate-900/90 border border-slate-700 rounded-xl px-4 py-2.5 text-right">
                        <span class="text-[10px] uppercase font-semibold text-slate-400 block">Día del mes actual</span>
                        <div class="flex items-center justify-end gap-1.5 mt-0.5">
                            <span class="text-xl font-black text-white font-mono">{{ $metricas['dia_del_mes'] }}</span>
                            <span class="text-xs text-slate-400">/ 15</span>
                        </div>
                    </div>

                    @if($metricas['periodo_vencido'])
                        <div class="bg-rose-500/10 border border-rose-500/40 rounded-xl px-4 py-2.5 text-rose-300">
                            <span class="text-[10px] uppercase font-bold block text-rose-400">Plazo Concluido</span>
                            <span class="text-xs font-semibold">Cuentas pendientes en mora</span>
                        </div>
                    @else
                        <div class="bg-amber-500/10 border border-amber-500/40 rounded-xl px-4 py-2.5 text-amber-300">
                            <span class="text-[10px] uppercase font-bold block text-amber-400">Plazo en Curso</span>
                            <span class="text-sm font-bold font-mono">{{ $metricas['dias_restantes_15'] }} días restantes</span>
                        </div>
                    @endif

                    <button 
                        wire:click="$set('modalNuevaSubOpen', true)"
                        type="button" 
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-semibold text-xs transition-all shadow-lg shadow-sky-600/30 cursor-pointer">
                        <x-heroicon-m-plus class="h-4 w-4" />
                        <span>Nueva Suscripción</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- ─── 2. TARJETAS DE MÉTRICAS GLOBALES (CUP Y USD) ─── --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="dp-stat-card border-l-4 border-l-emerald-500">
                <div class="flex items-center justify-between text-slate-400 text-xs font-semibold mb-2">
                    <span>Recaudado Octubre (CUP)</span>
                    <span class="px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-400 font-mono">CUP</span>
                </div>
                <div class="text-2xl font-black text-white font-mono">
                    ${{ number_format($metricas['recaudado_cup'], 2) }}
                </div>
                <div class="text-[11px] text-slate-400 mt-2 flex items-center justify-between">
                    <span>Pendiente:</span>
                    <span class="text-amber-400 font-mono font-semibold">${{ number_format($metricas['pendiente_cup'], 2) }}</span>
                </div>
            </div>

            <div class="dp-stat-card border-l-4 border-l-blue-500">
                <div class="flex items-center justify-between text-slate-400 text-xs font-semibold mb-2">
                    <span>Recaudado Octubre (USD)</span>
                    <span class="px-2 py-0.5 rounded bg-blue-500/10 text-blue-400 font-mono">USD</span>
                </div>
                <div class="text-2xl font-black text-white font-mono">
                    ${{ number_format($metricas['recaudado_usd'], 2) }}
                </div>
                <div class="text-[11px] text-slate-400 mt-2 flex items-center justify-between">
                    <span>Pendiente:</span>
                    <span class="text-amber-400 font-mono font-semibold">${{ number_format($metricas['pendiente_usd'], 2) }}</span>
                </div>
            </div>

            <div class="dp-stat-card border-l-4 border-l-rose-500">
                <div class="flex items-center justify-between text-slate-400 text-xs font-semibold mb-2">
                    <span>Deuda Acumulada</span>
                    <span class="px-2 py-0.5 rounded bg-rose-500/10 text-rose-400 font-mono">En Mora</span>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-xl font-bold text-rose-400 font-mono">${{ number_format($metricas['deuda_cup'], 0) }} CUP</span>
                    <span class="text-slate-500">/</span>
                    <span class="text-lg font-bold text-rose-300 font-mono">${{ number_format($metricas['deuda_usd'], 0) }} USD</span>
                </div>
                <div class="text-[11px] text-slate-400 mt-2 flex items-center justify-between">
                    <span>Clientes con Deuda:</span>
                    <span class="text-rose-400 font-bold">{{ $metricas['total_deudores'] }} de {{ $metricas['total_clientes'] }}</span>
                </div>
            </div>

            <div class="dp-stat-card border-l-4 border-l-sky-500">
                <div class="flex items-center justify-between text-slate-400 text-xs font-semibold mb-2">
                    <span>Parque Gestión Remota</span>
                    <span class="px-2 py-0.5 rounded bg-sky-500/10 text-sky-400 font-mono">4G LTE</span>
                </div>
                <div class="text-2xl font-black text-white font-mono">
                    {{ $metricas['total_clientes'] }} <span class="text-xs font-normal text-slate-400">Suscripciones</span>
                </div>
                <div class="text-[11px] text-slate-400 mt-2 flex items-center justify-between">
                    <span>Cobros al día:</span>
                    <span class="text-emerald-400 font-semibold">{{ $metricas['total_clientes'] - $metricas['total_deudores'] }} cuentas</span>
                </div>
            </div>
        </div>

        {{-- ─── 3. SELECTOR DE PESTAÑAS (TABS REACTIVOS) ─── --}}
        <div class="flex items-center justify-between border-b border-slate-800 pb-2">
            <div class="flex gap-2 overflow-x-auto">
                <button 
                    wire:click="$set('activeTab', 'cobros')"
                    type="button"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center gap-2 {{ $activeTab === 'cobros' ? 'bg-sky-500/20 text-sky-400 border border-sky-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                    <x-heroicon-o-table-cells class="h-4 w-4" />
                    <span>Control de Cobros Mensuales</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-slate-800 text-slate-300 font-mono">{{ count($suscripciones) }}</span>
                </button>

                <button 
                    wire:click="$set('activeTab', 'semana')"
                    type="button"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center gap-2 {{ $activeTab === 'semana' ? 'bg-sky-500/20 text-sky-400 border border-sky-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                    <x-heroicon-o-check-circle class="h-4 w-4" />
                    <span>Pagaron esta Semana</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-emerald-500/20 text-emerald-300 font-mono">{{ count($pagosSemana) }}</span>
                </button>

                <button 
                    wire:click="$set('activeTab', 'deudores')"
                    type="button"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center gap-2 {{ $activeTab === 'deudores' ? 'bg-rose-500/20 text-rose-400 border border-rose-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                    <x-heroicon-o-exclamation-triangle class="h-4 w-4" />
                    <span>Mayores Deudores</span>
                    @if(count($mayoresDeudores) > 0)
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-rose-500 text-white font-mono font-bold">{{ count($mayoresDeudores) }}</span>
                    @endif
                </button>

                <button 
                    wire:click="$set('activeTab', 'reporte')"
                    type="button"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center gap-2 {{ $activeTab === 'reporte' ? 'bg-sky-500/20 text-sky-400 border border-sky-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                    <x-heroicon-o-chart-bar class="h-4 w-4" />
                    <span>Reporte Temporal (Año/Sem/Trim/Mes)</span>
                </button>
            </div>
        </div>

        {{-- ─── TAB 1: CONTROL DE COBROS MENSUALES ─── --}}
        @if($activeTab === 'cobros')
            <div class="space-y-4">
                <div class="flex flex-col sm:flex-row gap-3 items-center justify-between bg-slate-900/80 p-3 rounded-xl border border-slate-800">
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <div class="relative w-full sm:w-72">
                            <input 
                                wire:model.live.debounce.300ms="busqueda"
                                type="text" 
                                placeholder="Buscar por cliente, código, SIM..."
                                class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3 py-1.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-sky-500" />
                        </div>
                    </div>

                    <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                        <div class="flex items-center gap-1 bg-slate-950 p-1 rounded-lg border border-slate-800 text-xs">
                            <span class="text-[10px] uppercase font-semibold text-slate-400 px-2">Moneda:</span>
                            <button 
                                wire:click="$set('filtroMoneda', 'todas')"
                                class="px-2.5 py-1 rounded text-xs font-semibold cursor-pointer {{ $filtroMoneda === 'todas' ? 'bg-sky-500 text-slate-950 font-bold' : 'text-slate-400 hover:text-white' }}">
                                Todas
                            </button>
                            <button 
                                wire:click="$set('filtroMoneda', 'CUP')"
                                class="px-2.5 py-1 rounded text-xs font-semibold cursor-pointer {{ $filtroMoneda === 'CUP' ? 'bg-emerald-500 text-slate-950 font-bold' : 'text-slate-400 hover:text-white' }}">
                                CUP
                            </button>
                            <button 
                                wire:click="$set('filtroMoneda', 'USD')"
                                class="px-2.5 py-1 rounded text-xs font-semibold cursor-pointer {{ $filtroMoneda === 'USD' ? 'bg-blue-500 text-slate-950 font-bold' : 'text-slate-400 hover:text-white' }}">
                                USD
                            </button>
                        </div>

                        <div class="flex items-center gap-1 bg-slate-950 p-1 rounded-lg border border-slate-800 text-xs">
                            <span class="text-[10px] uppercase font-semibold text-slate-400 px-2">Estado:</span>
                            <button 
                                wire:click="$set('filtroEstado', 'todos')"
                                class="px-2.5 py-1 rounded text-xs font-semibold cursor-pointer {{ $filtroEstado === 'todos' ? 'bg-slate-700 text-white' : 'text-slate-400 hover:text-white' }}">
                                Todos
                            </button>
                            <button 
                                wire:click="$set('filtroEstado', 'cobrado')"
                                class="px-2.5 py-1 rounded text-xs font-semibold cursor-pointer {{ $filtroEstado === 'cobrado' ? 'bg-emerald-600 text-white' : 'text-slate-400 hover:text-white' }}">
                                Cobrados
                            </button>
                            <button 
                                wire:click="$set('filtroEstado', 'pendiente')"
                                class="px-2.5 py-1 rounded text-xs font-semibold cursor-pointer {{ $filtroEstado === 'pendiente' ? 'bg-amber-600 text-white' : 'text-slate-400 hover:text-white' }}">
                                Pendientes
                            </button>
                            <button 
                                wire:click="$set('filtroEstado', 'vencido')"
                                class="px-2.5 py-1 rounded text-xs font-semibold cursor-pointer {{ $filtroEstado === 'vencido' ? 'bg-rose-600 text-white' : 'text-slate-400 hover:text-white' }}">
                                En Mora
                            </button>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-800 bg-slate-900/70 shadow-lg">
                    <table class="w-full text-left text-xs text-slate-300">
                        <thead class="bg-slate-950/80 text-[11px] font-semibold uppercase tracking-wider text-slate-400 border-b border-slate-800">
                            <tr>
                                <th class="px-4 py-3">Código</th>
                                <th class="px-4 py-3">Cliente / Teléfono</th>
                                <th class="px-4 py-3">Solución / SIM</th>
                                <th class="px-4 py-3 text-right">Mensualidad</th>
                                <th class="px-4 py-3 text-right">Deuda Anterior</th>
                                <th class="px-4 py-3 text-right">Total a Cobrar</th>
                                <th class="px-4 py-3 text-center">Estado</th>
                                <th class="px-4 py-3 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            @forelse($suscripciones as $sub)
                                @php
                                    $totalCobrar = (float) $sub->monto_mensual + (float) $sub->deuda_acumulada;
                                @endphp
                                <tr class="hover:bg-slate-800/40 transition-colors {{ $sub->tiene_deuda ? 'bg-rose-950/10' : '' }}">
                                    <td class="px-4 py-3 font-mono font-bold text-sky-400 whitespace-nowrap">
                                        {{ $sub->codigo }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-bold text-white">{{ $sub->cliente_nombre }}</div>
                                        @if($sub->cliente_telefono)
                                            <div class="text-[11px] text-slate-400 flex items-center gap-1 mt-0.5 font-mono">
                                                <x-heroicon-m-phone class="h-3 w-3 text-slate-500" />
                                                {{ $sub->cliente_telefono }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-slate-200">{{ $sub->tipo_solucion }}</div>
                                        @if($sub->sim_numero)
                                            <div class="text-[10px] text-slate-400 font-mono mt-0.5">SIM: {{ $sub->sim_numero }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right font-mono font-semibold text-slate-200 whitespace-nowrap">
                                        ${{ number_format($sub->monto_mensual, 2) }}
                                        <span class="text-[10px] px-1 py-0.5 rounded ml-1 {{ $sub->moneda === 'USD' ? 'bg-blue-500/20 text-blue-300' : 'bg-emerald-500/20 text-emerald-300' }}">
                                            {{ $sub->moneda }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        @if($sub->tiene_deuda && $sub->deuda_acumulada > 0)
                                            <div class="font-mono font-bold text-rose-400">
                                                +${{ number_format($sub->deuda_acumulada, 2) }}
                                            </div>
                                            <div class="text-[10px] text-rose-300/80 mt-0.5">
                                                ({{ $sub->meses_deuda_count }} {{ $sub->meses_deuda_count == 1 ? 'mes' : 'meses' }})
                                            </div>
                                        @else
                                            <span class="text-slate-500 font-mono">$0.00</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        <div class="text-sm font-black font-mono {{ $sub->tiene_deuda ? 'text-amber-400' : 'text-white' }}">
                                            ${{ number_format($totalCobrar, 2) }} {{ $sub->moneda }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center whitespace-nowrap">
                                        @if($sub->estado_cobro === 'cobrado')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                                <x-heroicon-s-check-circle class="h-3 w-3" />
                                                Cobrado
                                            </span>
                                        @elseif($sub->estado_cobro === 'vencido')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30">
                                                <x-heroicon-s-exclamation-circle class="h-3 w-3" />
                                                En Mora
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                                <x-heroicon-s-clock class="h-3 w-3" />
                                                Pendiente (Día 1-15)
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1.5">
                                            @if($sub->estado_cobro !== 'cobrado')
                                                <button 
                                                    wire:click="abrirModalPago({{ $sub->id }})"
                                                    type="button"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-[11px] shadow-sm transition-all cursor-pointer">
                                                    <x-heroicon-m-credit-card class="h-3.5 w-3.5" />
                                                    <span>Cobrar</span>
                                                </button>
                                            @else
                                                <button 
                                                    wire:click="verRecibo({{ $sub->id }})"
                                                    type="button"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-sky-400 font-semibold text-[11px] border border-slate-700 transition-all cursor-pointer">
                                                    <x-heroicon-m-receipt-percent class="h-3.5 w-3.5" />
                                                    <span>Recibo</span>
                                                </button>
                                            @endif

                                            @if($sub->cliente_telefono && $sub->tiene_deuda)
                                                @php
                                                    $mensajeWp = urlencode("Estimado cliente {$sub->cliente_nombre}, le contactamos de DataPlus respecto al servicio de Gestión Remota. Tiene un pendiente de cobro de {$sub->moneda} {$totalCobrar}. Por favor remitir comprobante de pago por esta vía.");
                                                    $linkWp = "https://wa.me/" . preg_replace('/[^0-9]/', '', $sub->cliente_telefono) . "?text=" . $mensajeWp;
                                                @endphp
                                                <a 
                                                    href="{{ $linkWp }}" 
                                                    target="_blank"
                                                    title="Enviar recordatorio por WhatsApp"
                                                    class="inline-flex items-center p-1 rounded-lg bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 border border-emerald-500/20 transition-all">
                                                    <x-heroicon-m-chat-bubble-left-ellipsis class="h-3.5 w-3.5" />
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-8 text-slate-500">
                                        No se encontraron registros de suscripción.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- ─── TAB 2: PAGOS REALIZADOS EN LA SEMANA ─── --}}
        @if($activeTab === 'semana')
            <div class="space-y-4">
                <div class="flex items-center justify-between bg-slate-900/80 p-4 rounded-xl border border-slate-800">
                    <div>
                        <h3 class="text-sm font-bold text-white flex items-center gap-2">
                            <x-heroicon-o-calendar-days class="h-5 w-5 text-emerald-400" />
                            Pagos Recibidos en los Últimos 7 Días
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">
                            Cobranzas acreditadas con comprobante o recibo de entrega física.
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="px-3 py-1.5 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-right">
                            <span class="text-[10px] text-slate-400 block font-semibold uppercase">Total Semana (CUP)</span>
                            <span class="text-sm font-mono font-bold text-emerald-400">
                                ${{ number_format($pagosSemana->where('moneda', 'CUP')->sum('monto_pagado'), 2) }}
                            </span>
                        </div>
                        <div class="px-3 py-1.5 rounded-lg bg-blue-500/10 border border-blue-500/20 text-right">
                            <span class="text-[10px] text-slate-400 block font-semibold uppercase">Total Semana (USD)</span>
                            <span class="text-sm font-mono font-bold text-blue-400">
                                ${{ number_format($pagosSemana->where('moneda', 'USD')->sum('monto_pagado'), 2) }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-800 bg-slate-900/70 shadow-lg">
                    <table class="w-full text-left text-xs text-slate-300">
                        <thead class="bg-slate-950/80 text-[11px] font-semibold uppercase tracking-wider text-slate-400 border-b border-slate-800">
                            <tr>
                                <th class="px-4 py-3">Código Recibo</th>
                                <th class="px-4 py-3">Fecha de Pago</th>
                                <th class="px-4 py-3">Cliente</th>
                                <th class="px-4 py-3">Método / Vía</th>
                                <th class="px-4 py-3">Detalle (Transacción o Entrega)</th>
                                <th class="px-4 py-3 text-right">Monto Pagado</th>
                                <th class="px-4 py-3 text-center">Comprobante</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            @forelse($pagosSemana as $p)
                                <tr class="hover:bg-slate-800/40 transition-colors">
                                    <td class="px-4 py-3 font-mono font-bold text-emerald-400 whitespace-nowrap">
                                        {{ $p->codigo_pago }}
                                    </td>
                                    <td class="px-4 py-3 font-mono text-slate-300 whitespace-nowrap">
                                        {{ $p->fecha_pago ? $p->fecha_pago->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="px-4 py-3 font-bold text-white">
                                        {{ $p->cliente_nombre }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @if($p->metodo_pago === 'transferencia')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-sky-500/20 text-sky-300 border border-sky-500/30">
                                                <x-heroicon-m-arrows-right-left class="h-3 w-3" />
                                                Transferencia
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                                <x-heroicon-m-banknotes class="h-3 w-3" />
                                                Efectivo
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($p->metodo_pago === 'transferencia')
                                            <span class="font-mono text-slate-300 text-[11px]">
                                                ID: {{ $p->numero_transaccion ?: 'Transfermóvil/EnZona' }}
                                            </span>
                                        @else
                                            <div class="text-[11px] text-slate-300">
                                                <span>Entrega: <strong class="text-white">{{ $p->efectivo_quien_entrega ?: 'Cliente' }}</strong></span>
                                                <span class="mx-1">→</span>
                                                <span>Recibe: <strong class="text-white">{{ $p->efectivo_quien_recibe ?: 'Admin' }}</strong></span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right font-mono font-black text-white whitespace-nowrap">
                                        ${{ number_format($p->monto_pagado, 2) }} {{ $p->moneda }}
                                    </td>
                                    <td class="px-4 py-3 text-center whitespace-nowrap">
                                        <button 
                                            wire:click="verComprobante({{ $p->id }})"
                                            type="button"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-sky-400 font-semibold text-[11px] border border-slate-700 transition-all cursor-pointer">
                                            <x-heroicon-m-eye class="h-3.5 w-3.5" />
                                            <span>Ver Comprobante</span>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-8 text-slate-500">
                                        No se han registrado cobranzas en los últimos 7 días.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- ─── TAB 3: MAYORES DEUDORES ─── --}}
        @if($activeTab === 'deudores')
            <div class="space-y-4">
                <div class="flex items-center justify-between bg-rose-950/40 p-4 rounded-xl border border-rose-900/50">
                    <div>
                        <h3 class="text-sm font-bold text-rose-300 flex items-center gap-2">
                            <x-heroicon-o-shield-exclamation class="h-5 w-5 text-rose-400" />
                            Ranking de Mayores Deudores de Gestión Remota
                        </h3>
                        <p class="text-xs text-rose-200/80 mt-0.5">
                            Clientes con mensualidades atrasadas ordenados por mayor importe y tiempo adeudado.
                        </p>
                    </div>

                    <div class="text-right font-mono">
                        <span class="text-xs text-rose-300/80 block uppercase font-semibold">Total en Mora</span>
                        <span class="text-base font-black text-rose-400">
                            ${{ number_format($metricas['deuda_cup'], 2) }} CUP + ${{ number_format($metricas['deuda_usd'], 2) }} USD
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($mayoresDeudores as $d)
                        @php
                            $totalCobrar = (float) $d->monto_mensual + (float) $d->deuda_acumulada;
                            $mesesDetalle = json_decode($d->meses_deuda_detalle, true) ?: [];
                        @endphp
                        <div class="rounded-xl border border-rose-900/40 bg-slate-900/90 p-4 shadow-md flex flex-col justify-between space-y-3">
                            <div class="flex items-start justify-between">
                                <div>
                                    <span class="text-[10px] font-mono font-bold text-rose-400 px-2 py-0.5 rounded bg-rose-500/10 border border-rose-500/20">
                                        {{ $d->codigo }}
                                    </span>
                                    <h4 class="text-sm font-bold text-white mt-1">{{ $d->cliente_nombre }}</h4>
                                    <p class="text-xs text-slate-400 mt-0.5">{{ $d->tipo_solucion }} (SIM: {{ $d->sim_numero ?: 'N/A' }})</p>
                                </div>

                                <div class="text-right">
                                    <span class="text-[10px] uppercase font-semibold text-rose-400 block">Deuda Total</span>
                                    <span class="text-lg font-black text-rose-300 font-mono">
                                        ${{ number_format($d->deuda_acumulada, 2) }} {{ $d->moneda }}
                                    </span>
                                </div>
                            </div>

                            <div class="bg-slate-950/70 p-2.5 rounded-lg border border-slate-800 text-xs">
                                <div class="flex items-center justify-between text-slate-400 text-[11px] mb-1">
                                    <span>Meses acumulados en mora:</span>
                                    <span class="font-bold text-rose-400 font-mono">{{ $d->meses_deuda_count }} meses</span>
                                </div>
                                <div class="flex flex-wrap gap-1 mt-1">
                                    @forelse($mesesDetalle as $mes)
                                        <span class="px-2 py-0.5 rounded text-[10px] bg-rose-950 text-rose-300 border border-rose-800">
                                            {{ $mes }}
                                        </span>
                                    @empty
                                        <span class="text-[11px] text-slate-500">Mora acumulada del período anterior.</span>
                                    @endforelse
                                </div>
                                <div class="mt-2 pt-2 border-t border-slate-800/80 flex items-center justify-between text-xs font-semibold">
                                    <span class="text-slate-300">Total a liquidar (con mes actual):</span>
                                    <span class="text-white font-mono font-bold">${{ number_format($totalCobrar, 2) }} {{ $d->moneda }}</span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-1">
                                @if($d->cliente_telefono)
                                    @php
                                        $mensaje = urlencode("Estimado {$d->cliente_nombre}, le contactamos de DataPlus para recordarle que el servicio de Gestión Remota tiene una deuda acumulada de {$d->deuda_acumulada} {$d->moneda} (Total con octubre: {$totalCobrar} {$d->moneda}). Por favor remitir comprobante de pago a la brevedad.");
                                        $link = "https://wa.me/" . preg_replace('/[^0-9]/', '', $d->cliente_telefono) . "?text=" . $mensaje;
                                    @endphp
                                    <a 
                                        href="{{ $link }}" 
                                        target="_blank"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-300 text-xs font-semibold border border-emerald-600/30 transition-all">
                                        <x-heroicon-m-chat-bubble-left-right class="h-3.5 w-3.5" />
                                        <span>Cobrar por WhatsApp</span>
                                    </a>
                                @else
                                    <span class="text-slate-500 text-[11px]">Sin teléfono registrado</span>
                                @endif

                                <button 
                                    wire:click="abrirModalPago({{ $d->id }})"
                                    type="button"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-sky-600 hover:bg-sky-500 text-white text-xs font-semibold shadow-sm transition-all cursor-pointer">
                                    <x-heroicon-m-credit-card class="h-3.5 w-3.5" />
                                    <span>Registrar Pago</span>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-2 text-center py-8 text-slate-500">
                            Excelente: Ningún cliente presenta mora en el servicio de Gestión Remota.
                        </div>
                    @endforelse
                </div>
            </div>
        @endif

        {{-- ─── TAB 4: REPORTE TEMPORAL ─── --}}
        @if($activeTab === 'reporte')
            <div class="space-y-4">
                <div class="flex flex-col sm:flex-row items-center justify-between bg-slate-900/80 p-4 rounded-xl border border-slate-800 gap-3">
                    <div>
                        <h3 class="text-sm font-bold text-white flex items-center gap-2">
                            <x-heroicon-o-presentation-chart-line class="h-5 w-5 text-sky-400" />
                            Estado y Evolución Temporal de Pagos (Gestión Remota)
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">
                            Comparativa de facturación cobrada vs. saldos pendientes según periodicidad.
                        </p>
                    </div>

                    <div class="flex items-center gap-1 bg-slate-950 p-1 rounded-lg border border-slate-800 text-xs">
                        <button 
                            wire:click="$set('periodoReporte', 'mensual')"
                            class="px-3 py-1.5 rounded-lg font-semibold cursor-pointer {{ $periodoReporte === 'mensual' ? 'bg-sky-600 text-white' : 'text-slate-400 hover:text-white' }}">
                            Mensual
                        </button>
                        <button 
                            wire:click="$set('periodoReporte', 'trimestral')"
                            class="px-3 py-1.5 rounded-lg font-semibold cursor-pointer {{ $periodoReporte === 'trimestral' ? 'bg-sky-600 text-white' : 'text-slate-400 hover:text-white' }}">
                            Trimestral
                        </button>
                        <button 
                            wire:click="$set('periodoReporte', 'semestral')"
                            class="px-3 py-1.5 rounded-lg font-semibold cursor-pointer {{ $periodoReporte === 'semestral' ? 'bg-sky-600 text-white' : 'text-slate-400 hover:text-white' }}">
                            Semestral
                        </button>
                        <button 
                            wire:click="$set('periodoReporte', 'anual')"
                            class="px-3 py-1.5 rounded-lg font-semibold cursor-pointer {{ $periodoReporte === 'anual' ? 'bg-sky-600 text-white' : 'text-slate-400 hover:text-white' }}">
                            Anual
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-800 bg-slate-900/70 shadow-lg">
                    <table class="w-full text-left text-xs text-slate-300">
                        <thead class="bg-slate-950/80 text-[11px] font-semibold uppercase tracking-wider text-slate-400 border-b border-slate-800">
                            <tr>
                                <th class="px-4 py-3">Periodo</th>
                                <th class="px-4 py-3 text-right">Recaudado (CUP)</th>
                                <th class="px-4 py-3 text-right">Recaudado (USD)</th>
                                <th class="px-4 py-3 text-right">Deuda Pendiente (CUP)</th>
                                <th class="px-4 py-3 text-right">Deuda Pendiente (USD)</th>
                                <th class="px-4 py-3 text-center">Cumplimiento</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            @foreach($reporteTemporal['filas'] as $fila)
                                <tr class="hover:bg-slate-800/40 transition-colors">
                                    <td class="px-4 py-3 font-bold text-white whitespace-nowrap">
                                        {{ $fila['periodo'] }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-mono font-semibold text-emerald-400 whitespace-nowrap">
                                        ${{ number_format($fila['cup_cobrado'], 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-mono font-semibold text-blue-400 whitespace-nowrap">
                                        ${{ number_format($fila['usd_cobrado'], 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-mono text-rose-400 whitespace-nowrap">
                                        ${{ number_format($fila['cup_deuda'], 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-mono text-rose-400 whitespace-nowrap">
                                        ${{ number_format($fila['usd_deuda'], 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-center whitespace-nowrap">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold font-mono bg-sky-500/10 text-sky-300 border border-sky-500/20">
                                            {{ $fila['cumplimiento'] }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- ─── MODAL 1: REGISTRAR PAGO ─── --}}
        @if($modalPagoOpen)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4 overflow-y-auto">
                <div class="bg-slate-900 border border-slate-700 rounded-2xl w-full max-w-lg p-6 shadow-2xl relative text-left">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-credit-card class="h-6 w-6 text-emerald-400" />
                            <h3 class="text-base font-bold text-white">Registrar Cobranza de Gestión Remota</h3>
                        </div>
                        <button 
                            wire:click="$set('modalPagoOpen', false)"
                            type="button" 
                            class="text-slate-400 hover:text-white cursor-pointer">
                            <x-heroicon-m-x-mark class="h-5 w-5" />
                        </button>
                    </div>

                    <div class="mt-4 space-y-4 text-xs">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-slate-400 font-semibold mb-1">Monto Pagado</label>
                                <input 
                                    wire:model="formMontoPagado" 
                                    type="number" 
                                    step="0.01" 
                                    class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3 py-2 text-white font-mono font-bold focus:border-sky-500" />
                            </div>
                            <div>
                                <label class="block text-slate-400 font-semibold mb-1">Día que Pagó</label>
                                <input 
                                    wire:model="formFechaPago" 
                                    type="date" 
                                    class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3 py-2 text-white font-mono focus:border-sky-500" />
                            </div>
                        </div>

                        <div>
                            <label class="block text-slate-400 font-semibold mb-1">Método de Pago</label>
                            <div class="grid grid-cols-2 gap-2">
                                <button 
                                    wire:click="$set('formMetodoPago', 'transferencia')"
                                    type="button" 
                                    class="px-3 py-2 rounded-lg font-semibold border cursor-pointer text-center {{ $formMetodoPago === 'transferencia' ? 'bg-sky-500/20 text-sky-300 border-sky-500' : 'bg-slate-950 text-slate-400 border-slate-800' }}">
                                    Transferencia (Transfermóvil / EnZona)
                                </button>
                                <button 
                                    wire:click="$set('formMetodoPago', 'efectivo')"
                                    type="button" 
                                    class="px-3 py-2 rounded-lg font-semibold border cursor-pointer text-center {{ $formMetodoPago === 'efectivo' ? 'bg-amber-500/20 text-amber-300 border-amber-500' : 'bg-slate-950 text-slate-400 border-slate-800' }}">
                                    Efectivo Físico
                                </button>
                            </div>
                        </div>

                        @if($formMetodoPago === 'transferencia')
                            <div class="bg-slate-950/80 p-3 rounded-xl border border-slate-800 space-y-3">
                                <div>
                                    <label class="block text-slate-400 font-semibold mb-1">Número de Transacción / Referencia</label>
                                    <input 
                                        wire:model="formNumeroTransaccion" 
                                        type="text" 
                                        placeholder="Ej: TRF-839218392"
                                        class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white font-mono focus:border-sky-500" />
                                </div>
                                <div>
                                    <label class="block text-slate-400 font-semibold mb-1">Comprobante de WhatsApp (Imagen / Referencia)</label>
                                    <input 
                                        wire:model="formComprobanteNombre" 
                                        type="text" 
                                        placeholder="Nombre archivo o foto WhatsApp (ej: WhatsApp_Image_1010.jpg)"
                                        class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white focus:border-sky-500" />
                                </div>
                            </div>
                        @else
                            <div class="bg-slate-950/80 p-3 rounded-xl border border-slate-800 grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-slate-400 font-semibold mb-1">Quién Entrega el Dinero</label>
                                    <input 
                                        wire:model="formQuienEntrega" 
                                        type="text" 
                                        placeholder="Nombre de quien entrega"
                                        class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white focus:border-sky-500" />
                                </div>
                                <div>
                                    <label class="block text-slate-400 font-semibold mb-1">Quién Recibe el Dinero</label>
                                    <input 
                                        wire:model="formQuienRecibe" 
                                        type="text" 
                                        placeholder="Especialista DataPlus que recibe"
                                        class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white focus:border-sky-500" />
                                </div>
                            </div>
                        @endif

                        <div>
                            <label class="block text-slate-400 font-semibold mb-1">Notas de Cobranza</label>
                            <textarea 
                                wire:model="formNotasPago" 
                                rows="2" 
                                placeholder="Detalles de conciliación..."
                                class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3 py-2 text-white focus:border-sky-500"></textarea>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
                        <button 
                            wire:click="$set('modalPagoOpen', false)"
                            type="button" 
                            class="px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs cursor-pointer">
                            Cancelar
                        </button>
                        <button 
                            wire:click="guardarPago"
                            type="button" 
                            class="px-5 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg shadow-emerald-600/30 cursor-pointer">
                            Confirmar y Acreditar Pago
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- ─── MODAL 2: VISOR DE COMPROBANTE DE WHATSAPP ─── --}}
        @if($modalVisorOpen && $comprobanteActual)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4">
                <div class="bg-slate-900 border border-slate-700 rounded-2xl w-full max-w-md p-6 shadow-2xl relative text-left">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-document-magnifying-glass class="h-6 w-6 text-sky-400" />
                            <h3 class="text-base font-bold text-white">Comprobante de Pago (WhatsApp)</h3>
                        </div>
                        <button wire:click="$set('modalVisorOpen', false)" class="text-slate-400 hover:text-white cursor-pointer">
                            <x-heroicon-m-x-mark class="h-5 w-5" />
                        </button>
                    </div>

                    <div class="mt-4 space-y-3 text-xs">
                        <div class="rounded-xl border border-slate-700 bg-slate-950 p-4 text-center font-mono">
                            <div class="text-[11px] text-emerald-400 font-bold tracking-widest uppercase">
                                ✓ TRANSFERENCIA CONFIRMADA
                            </div>
                            <div class="text-2xl font-black text-white mt-1">{{ $comprobanteActual['monto'] }}</div>
                            <div class="text-[10px] text-slate-400 mt-0.5">{{ $comprobanteActual['fecha'] }}</div>

                            <div class="mt-3 pt-3 border-t border-slate-800 text-left text-[11px] space-y-1.5 text-slate-300">
                                <div><span class="text-slate-500">Recibo:</span> <strong class="text-white">{{ $comprobanteActual['codigo'] }}</strong></div>
                                <div><span class="text-slate-500">Cliente:</span> <strong class="text-white">{{ $comprobanteActual['cliente'] }}</strong></div>
                                <div><span class="text-slate-500">Método:</span> <strong class="text-white">{{ ucfirst($comprobanteActual['metodo']) }}</strong></div>
                                <div><span class="text-slate-500">Transacción:</span> <strong class="text-sky-400">{{ $comprobanteActual['transaccion'] }}</strong></div>
                                @if($comprobanteActual['quien_recibe'])
                                    <div><span class="text-slate-500">Receptor:</span> <strong class="text-white">{{ $comprobanteActual['quien_recibe'] }}</strong></div>
                                @endif
                            </div>
                        </div>

                        <p class="text-[11px] text-slate-400 text-center">
                            Archivo registrado: <span class="text-slate-300 font-mono">{{ $comprobanteActual['nombre_archivo'] }}</span>
                        </p>
                    </div>

                    <div class="mt-5 flex justify-end">
                        <button 
                            wire:click="$set('modalVisorOpen', false)"
                            type="button" 
                            class="px-4 py-2 rounded-lg bg-sky-600 hover:bg-sky-500 text-white font-semibold text-xs cursor-pointer">
                            Cerrar Visor
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- ─── MODAL 3: RECIBO OFICIAL DATAPLUS ─── --}}
        @if($modalReciboOpen && $reciboActual)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4">
                <div class="bg-slate-900 border border-slate-700 rounded-2xl w-full max-w-lg p-6 shadow-2xl relative text-left">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-receipt-percent class="h-6 w-6 text-sky-400" />
                            <h3 class="text-base font-bold text-white">Comprobante Oficial DataPlus S.R.L.</h3>
                        </div>
                        <button wire:click="$set('modalReciboOpen', false)" class="text-slate-400 hover:text-white cursor-pointer">
                            <x-heroicon-m-x-mark class="h-5 w-5" />
                        </button>
                    </div>

                    <div class="mt-4 p-4 rounded-xl border border-slate-700 bg-slate-950 space-y-3 text-xs">
                        <div class="flex justify-between items-center border-b border-slate-800 pb-2">
                            <div>
                                <span class="font-bold text-white text-sm">DataPlus S.R.L.</span>
                                <span class="text-[10px] text-sky-400 block font-mono">Gestión Remota & Seguridad</span>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] text-slate-400 font-mono">{{ $reciboActual['folio'] }}</span>
                                <span class="text-[11px] text-white block font-mono font-bold">{{ $reciboActual['fecha'] }}</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-[11px]">
                            <div><span class="text-slate-400">Cliente:</span> <strong class="text-white block">{{ $reciboActual['cliente'] }}</strong></div>
                            <div><span class="text-slate-400">Teléfono:</span> <strong class="text-white block">{{ $reciboActual['telefono'] }}</strong></div>
                            <div><span class="text-slate-400">Solución:</span> <span class="text-slate-200 block">{{ $reciboActual['solucion'] }}</span></div>
                            <div><span class="text-slate-400">SIM Card:</span> <span class="text-slate-200 block font-mono">{{ $reciboActual['sim'] }}</span></div>
                        </div>

                        <div class="border-t border-slate-800 pt-2 space-y-1">
                            <div class="flex justify-between text-slate-300">
                                <span>Mensualidad {{ $reciboActual['mes'] }}:</span>
                                <span class="font-mono font-bold">{{ $reciboActual['mensualidad'] }}</span>
                            </div>
                            <div class="flex justify-between text-rose-400">
                                <span>Deuda acumulada anterior:</span>
                                <span class="font-mono font-bold">{{ $reciboActual['deuda'] }}</span>
                            </div>
                            <div class="flex justify-between text-white font-bold text-sm pt-1 border-t border-slate-800">
                                <span>Total Liquidado:</span>
                                <span class="text-emerald-400 font-mono font-black">{{ $reciboActual['total'] }}</span>
                            </div>
                        </div>

                        <div class="bg-emerald-500/10 p-2 rounded text-[11px] text-emerald-300 border border-emerald-500/20 text-center font-medium">
                            ✓ Estado de Cobro: {{ strtoupper($reciboActual['estado']) }}
                        </div>
                    </div>

                    <div class="mt-4 flex justify-between items-center">
                        @php
                            $textoWp = urlencode("COMPROBANTE OFICIAL DATAPLUS S.R.L.\nFolio: {$reciboActual['folio']}\nCliente: {$reciboActual['cliente']}\nServicio: {$reciboActual['servicio']}\nMes: {$reciboActual['mes']}\nTotal: {$reciboActual['total']}\nEstado: {$reciboActual['estado']}\nGracias por su preferencia.");
                            $linkWpRecibo = "https://wa.me/" . preg_replace('/[^0-9]/', '', $reciboActual['telefono']) . "?text=" . $textoWp;
                        @endphp
                        <a 
                            href="{{ $linkWpRecibo }}" 
                            target="_blank"
                            class="inline-flex items-center gap-1 px-3 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs">
                            <x-heroicon-m-share class="h-4 w-4" />
                            <span>Enviar por WhatsApp</span>
                        </a>

                        <button 
                            wire:click="$set('modalReciboOpen', false)"
                            type="button" 
                            class="px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold cursor-pointer">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- ─── MODAL 4: NUEVA SUSCRIPCIÓN ─── --}}
        @if($modalNuevaSubOpen)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4">
                <div class="bg-slate-900 border border-slate-700 rounded-2xl w-full max-w-lg p-6 shadow-2xl relative text-left">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-plus-circle class="h-6 w-6 text-sky-400" />
                            <h3 class="text-base font-bold text-white">Nueva Suscripción de Gestión Remota</h3>
                        </div>
                        <button wire:click="$set('modalNuevaSubOpen', false)" class="text-slate-400 hover:text-white cursor-pointer">
                            <x-heroicon-m-x-mark class="h-5 w-5" />
                        </button>
                    </div>

                    <div class="mt-4 space-y-3 text-xs">
                        <div>
                            <label class="block text-slate-400 font-semibold mb-1">Nombre del Cliente / Negocio *</label>
                            <input 
                                wire:model="newSubClienteNombre" 
                                type="text" 
                                placeholder="Ej: Hostal Miramar o Juan Pérez"
                                class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3 py-2 text-white focus:border-sky-500" />
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-slate-400 font-semibold mb-1">Teléfono Móvil (WhatsApp)</label>
                                <input 
                                    wire:model="newSubClienteTelefono" 
                                    type="text" 
                                    placeholder="+53 5200 0000"
                                    class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3 py-2 text-white font-mono focus:border-sky-500" />
                            </div>
                            <div>
                                <label class="block text-slate-400 font-semibold mb-1">Número SIM Card</label>
                                <input 
                                    wire:model="newSubSim" 
                                    type="text" 
                                    placeholder="535XXXXXXX"
                                    class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3 py-2 text-white font-mono focus:border-sky-500" />
                            </div>
                        </div>

                        <div>
                            <label class="block text-slate-400 font-semibold mb-1">Solución de Gestión Remota</label>
                            <input 
                                wire:model="newSubSolucion" 
                                type="text" 
                                placeholder="Ej: Router 4G LTE Huawei B310 o MikroTik"
                                class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3 py-2 text-white focus:border-sky-500" />
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-slate-400 font-semibold mb-1">Moneda de Cobro</label>
                                <select 
                                    wire:model="newSubMoneda" 
                                    class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3 py-2 text-white font-bold focus:border-sky-500">
                                    <option value="CUP">CUP (Pesos Cubanos)</option>
                                    <option value="USD">USD (Dólares)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-slate-400 font-semibold mb-1">Tarifa Mensual</label>
                                <input 
                                    wire:model="newSubMonto" 
                                    type="number" 
                                    step="0.01" 
                                    class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3 py-2 text-white font-mono font-bold focus:border-sky-500" />
                            </div>
                        </div>

                        <div>
                            <label class="block text-slate-400 font-semibold mb-1">Notas / Observaciones</label>
                            <textarea 
                                wire:model="newSubNotas" 
                                rows="2" 
                                placeholder="Condiciones especiales o notas..."
                                class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3 py-2 text-white focus:border-sky-500"></textarea>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3 pt-3 border-t border-slate-800">
                        <button 
                            wire:click="$set('modalNuevaSubOpen', false)"
                            type="button" 
                            class="px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs cursor-pointer">
                            Cancelar
                        </button>
                        <button 
                            wire:click="crearSuscripcion"
                            type="button" 
                            class="px-5 py-2 rounded-lg bg-sky-600 hover:bg-sky-500 text-white font-bold text-xs shadow-lg shadow-sky-600/30 cursor-pointer">
                            Guardar Suscripción
                        </button>
                    </div>
                </div>
            </div>
        @endif

    </div>
</x-filament-panels::page>
