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
        /* Estilos consistentes con la plataforma DataPlus para todos los inputs flotantes */
        .dp-modal-input {
            background-color: #020617 !important;
            color: #f8fafc !important;
            border: 1px solid #334155 !important;
            border-radius: 0.75rem !important;
            padding: 0.6rem 0.85rem !important;
            font-size: 0.8125rem !important;
            width: 100% !important;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.3) !important;
        }
        .dp-modal-input:focus {
            border-color: #0284c7 !important;
            outline: none !important;
            box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.3) !important;
        }
        .dp-modal-input option {
            background-color: #020617 !important;
            color: #f8fafc !important;
        }
        .dp-card {
            background-color: rgba(15, 23, 42, 0.75) !important;
            border: 1px solid #1e293b !important;
            border-radius: 1rem !important;
        }
    </style>

    <div class="dp-fact-wrap space-y-6">
        {{-- ─── 1. BANNER PRINCIPAL DE PLAZO (ESTILO DATAPLUS) ─── --}}
        <div class="rounded-2xl border border-sky-500/30 bg-gradient-to-r from-sky-950/70 via-slate-900 to-indigo-950/70 p-5 shadow-xl relative overflow-hidden">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 relative z-10">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 rounded-xl bg-sky-500/20 border border-sky-500/40 flex items-center justify-center text-sky-400 shrink-0">
                        <x-heroicon-o-calendar-days class="h-6 w-6" />
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-lg font-bold text-white tracking-tight">Periodo de Cobro: Octubre 2026</h2>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-sky-500/20 text-sky-300 border border-sky-500/30 font-mono">
                                Mes Adelantado
                            </span>
                        </div>
                        <p class="text-xs text-slate-300 mt-1">
                            Norma operativa: El servicio de Gestión Remota se liquida en los <strong class="text-white">primeros 15 días</strong> del mes en curso.
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <div class="bg-slate-950/90 border border-slate-800 rounded-xl px-4 py-2 text-center min-w-[120px]">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Día del mes actual</span>
                        <div class="flex items-center justify-center gap-1 mt-0.5">
                            <span class="text-lg font-black text-white font-mono">{{ $metricas['dia_del_mes'] }}</span>
                            <span class="text-xs text-slate-500">/ 15</span>
                        </div>
                    </div>

                    @if($metricas['periodo_vencido'])
                        <div class="bg-rose-500/10 border border-rose-500/30 rounded-xl px-4 py-2 text-center min-w-[130px]">
                            <span class="text-[10px] uppercase font-bold block text-rose-400">Plazo Concluido</span>
                            <span class="text-xs font-semibold text-rose-300">Cuentas en mora</span>
                        </div>
                    @else
                        <div class="bg-amber-500/10 border border-amber-500/30 rounded-xl px-4 py-2 text-center min-w-[130px]">
                            <span class="text-[10px] uppercase font-bold block text-amber-400">Plazo en Curso</span>
                            <span class="text-xs font-bold font-mono text-amber-300">{{ $metricas['dias_restantes_15'] }} días restantes</span>
                        </div>
                    @endif

                    <button 
                        wire:click="$set('modalNuevaSubOpen', true)"
                        type="button" 
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-bold text-xs transition-all shadow-lg shadow-sky-600/30 cursor-pointer">
                        <x-heroicon-m-plus class="h-4 w-4 stroke-[2.5]" />
                        <span>Nueva Suscripción</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- ─── 2. TARJETAS DE MÉTRICAS KPI (ESTILO DASHBOARD DATAPLUS) ─── --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Recaudado CUP --}}
            <div class="dp-card p-5 border-l-4 border-l-emerald-500 shadow-md">
                <div class="flex items-center justify-between text-slate-400 text-xs font-semibold mb-1">
                    <span>Recaudado Octubre (CUP)</span>
                    <span class="px-2 py-0.5 rounded bg-emerald-500/15 text-emerald-400 text-[10px] font-mono font-bold border border-emerald-500/30">CUP</span>
                </div>
                <div class="text-2xl font-black text-white font-mono mt-1">
                    ${{ number_format($metricas['recaudado_cup'], 2) }}
                </div>
                <div class="text-[11px] text-slate-400 mt-2 flex items-center justify-between border-t border-slate-800/80 pt-2">
                    <span>Pendiente:</span>
                    <span class="text-amber-400 font-mono font-bold">${{ number_format($metricas['pendiente_cup'], 2) }}</span>
                </div>
            </div>

            {{-- Recaudado USD --}}
            <div class="dp-card p-5 border-l-4 border-l-sky-500 shadow-md">
                <div class="flex items-center justify-between text-slate-400 text-xs font-semibold mb-1">
                    <span>Recaudado Octubre (USD)</span>
                    <span class="px-2 py-0.5 rounded bg-sky-500/15 text-sky-400 text-[10px] font-mono font-bold border border-sky-500/30">USD</span>
                </div>
                <div class="text-2xl font-black text-white font-mono mt-1">
                    ${{ number_format($metricas['recaudado_usd'], 2) }}
                </div>
                <div class="text-[11px] text-slate-400 mt-2 flex items-center justify-between border-t border-slate-800/80 pt-2">
                    <span>Pendiente:</span>
                    <span class="text-amber-400 font-mono font-bold">${{ number_format($metricas['pendiente_usd'], 2) }}</span>
                </div>
            </div>

            {{-- Deuda Acumulada --}}
            <div class="dp-card p-5 border-l-4 border-l-rose-500 shadow-md">
                <div class="flex items-center justify-between text-slate-400 text-xs font-semibold mb-1">
                    <span>Deuda Acumulada</span>
                    <span class="px-2 py-0.5 rounded bg-rose-500/15 text-rose-400 text-[10px] font-mono font-bold border border-rose-500/30">En Mora</span>
                </div>
                <div class="flex items-baseline gap-2 mt-1">
                    <span class="text-xl font-black text-rose-400 font-mono">${{ number_format($metricas['deuda_cup'], 0) }} CUP</span>
                    <span class="text-slate-500">/</span>
                    <span class="text-base font-bold text-rose-300 font-mono">${{ number_format($metricas['deuda_usd'], 0) }} USD</span>
                </div>
                <div class="text-[11px] text-slate-400 mt-2 flex items-center justify-between border-t border-slate-800/80 pt-2">
                    <span>Clientes con Deuda:</span>
                    <span class="text-rose-400 font-bold">{{ $metricas['total_deudores'] }} de {{ $metricas['total_clientes'] }}</span>
                </div>
            </div>

            {{-- Parque Activo --}}
            <div class="dp-card p-5 border-l-4 border-l-blue-500 shadow-md">
                <div class="flex items-center justify-between text-slate-400 text-xs font-semibold mb-1">
                    <span>Parque Gestión Remota</span>
                    <span class="px-2 py-0.5 rounded bg-blue-500/15 text-blue-400 text-[10px] font-mono font-bold border border-blue-500/30">4G LTE</span>
                </div>
                <div class="text-2xl font-black text-white font-mono mt-1">
                    {{ $metricas['total_clientes'] }} <span class="text-xs font-normal text-slate-400">Routers</span>
                </div>
                <div class="text-[11px] text-slate-400 mt-2 flex items-center justify-between border-t border-slate-800/80 pt-2">
                    <span>Cobros al día:</span>
                    <span class="text-emerald-400 font-semibold">{{ $metricas['total_clientes'] - $metricas['total_deudores'] }} cuentas (50%)</span>
                </div>
            </div>
        </div>

        {{-- ─── 3. SELECTOR DE PESTAÑAS (TABS REACTIVOS) ─── --}}
        <div class="flex items-center justify-between border-b border-slate-800 pb-2">
            <div class="flex gap-2 overflow-x-auto">
                <button 
                    wire:click="$set('activeTab', 'cobros')"
                    type="button"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center gap-2 {{ $activeTab === 'cobros' ? 'bg-sky-500/20 text-sky-400 border border-sky-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-900 border border-transparent' }}">
                    <x-heroicon-o-table-cells class="h-4 w-4" />
                    <span>Control de Cobros Mensuales</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-slate-800 text-slate-300 font-mono font-bold">{{ count($suscripciones) }}</span>
                </button>

                <button 
                    wire:click="$set('activeTab', 'semana')"
                    type="button"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center gap-2 {{ $activeTab === 'semana' ? 'bg-sky-500/20 text-sky-400 border border-sky-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-900 border border-transparent' }}">
                    <x-heroicon-o-check-circle class="h-4 w-4" />
                    <span>Pagaron esta Semana</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-emerald-500/20 text-emerald-300 font-mono font-bold">{{ count($pagosSemana) }}</span>
                </button>

                <button 
                    wire:click="$set('activeTab', 'deudores')"
                    type="button"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center gap-2 {{ $activeTab === 'deudores' ? 'bg-rose-500/20 text-rose-400 border border-rose-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-900 border border-transparent' }}">
                    <x-heroicon-o-exclamation-triangle class="h-4 w-4" />
                    <span>Mayores Deudores</span>
                    @if(count($mayoresDeudores) > 0)
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-rose-500 text-white font-mono font-bold">{{ count($mayoresDeudores) }}</span>
                    @endif
                </button>

                <button 
                    wire:click="$set('activeTab', 'reporte')"
                    type="button"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center gap-2 {{ $activeTab === 'reporte' ? 'bg-sky-500/20 text-sky-400 border border-sky-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-900 border border-transparent' }}">
                    <x-heroicon-o-chart-bar class="h-4 w-4" />
                    <span>Reporte Temporal (Año/Sem/Trim/Mes)</span>
                </button>
            </div>
        </div>

        {{-- ─── TAB 1: CONTROL DE COBROS MENSUALES ─── --}}
        @if($activeTab === 'cobros')
            <div class="space-y-4">
                {{-- Barra de Filtros --}}
                <div class="flex flex-col sm:flex-row gap-3 items-center justify-between bg-slate-900/80 p-3 rounded-2xl border border-slate-800 shadow-sm">
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <div class="relative w-full sm:w-80">
                            <input 
                                wire:model.live.debounce.300ms="busqueda"
                                type="text" 
                                placeholder="Buscar por cliente, código, SIM..."
                                class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500" />
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto justify-end">
                        <div class="flex items-center gap-1 bg-slate-950 p-1 rounded-xl border border-slate-800 text-xs">
                            <span class="text-[11px] font-semibold text-slate-400 px-2">Moneda:</span>
                            <button 
                                wire:click="$set('filtroMoneda', 'todas')"
                                class="px-2.5 py-1 rounded-lg font-bold text-xs cursor-pointer {{ $filtroMoneda === 'todas' ? 'bg-slate-800 text-white' : 'text-slate-400 hover:text-white' }}">
                                Todas
                            </button>
                            <button 
                                wire:click="$set('filtroMoneda', 'CUP')"
                                class="px-2.5 py-1 rounded-lg font-bold text-xs cursor-pointer {{ $filtroMoneda === 'CUP' ? 'bg-slate-800 text-white' : 'text-slate-400 hover:text-white' }}">
                                CUP
                            </button>
                            <button 
                                wire:click="$set('filtroMoneda', 'USD')"
                                class="px-2.5 py-1 rounded-lg font-bold text-xs cursor-pointer {{ $filtroMoneda === 'USD' ? 'bg-slate-800 text-white' : 'text-slate-400 hover:text-white' }}">
                                USD
                            </button>
                        </div>

                        <div class="flex items-center gap-1 bg-slate-950 p-1 rounded-xl border border-slate-800 text-xs">
                            <span class="text-[11px] font-semibold text-slate-400 px-2">Estado:</span>
                            <button 
                                wire:click="$set('filtroEstado', 'todos')"
                                class="px-2.5 py-1 rounded-lg font-bold text-xs cursor-pointer {{ $filtroEstado === 'todos' ? 'bg-slate-800 text-white' : 'text-slate-400 hover:text-white' }}">
                                Todos
                            </button>
                            <button 
                                wire:click="$set('filtroEstado', 'cobrados')"
                                class="px-2.5 py-1 rounded-lg font-bold text-xs cursor-pointer {{ $filtroEstado === 'cobrados' ? 'bg-slate-800 text-white' : 'text-slate-400 hover:text-white' }}">
                                Cobrados
                            </button>
                            <button 
                                wire:click="$set('filtroEstado', 'pendientes')"
                                class="px-2.5 py-1 rounded-lg font-bold text-xs cursor-pointer {{ $filtroEstado === 'pendientes' ? 'bg-slate-800 text-white' : 'text-slate-400 hover:text-white' }}">
                                Pendientes
                            </button>
                            <button 
                                wire:click="$set('filtroEstado', 'mora')"
                                class="px-2.5 py-1 rounded-lg font-bold text-xs cursor-pointer {{ $filtroEstado === 'mora' ? 'bg-slate-800 text-white' : 'text-slate-400 hover:text-white' }}">
                                En Mora
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Tabla de Cobros --}}
                <div class="overflow-x-auto rounded-2xl border border-slate-800 bg-slate-900/80 shadow-md">
                    <table class="w-full text-left text-xs text-slate-200">
                        <thead class="bg-slate-950/80 text-[11px] font-semibold uppercase tracking-wider text-slate-400 border-b border-slate-800">
                            <tr>
                                <th class="px-4 py-3">Código</th>
                                <th class="px-4 py-3">Cliente / Teléfono</th>
                                <th class="px-4 py-3">Solución / SIM</th>
                                <th class="px-4 py-3">Mensualidad</th>
                                <th class="px-4 py-3">Deuda Anterior</th>
                                <th class="px-4 py-3 font-bold text-white">Total a Cobrar</th>
                                <th class="px-4 py-3 text-center">Estado</th>
                                <th class="px-4 py-3 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/80">
                            @forelse($suscripciones as $sub)
                                @php
                                    $esMora = $sub->estado_cobro_mes_actual === 'En_Mora' || $sub->tiene_deuda;
                                    $esCobrado = $sub->estado_cobro_mes_actual === 'Al_Dia';
                                    $esPendiente = $sub->estado_cobro_mes_actual === 'Pendiente';
                                @endphp
                                <tr class="hover:bg-slate-800/40 transition-colors">
                                    <td class="px-4 py-3.5 font-mono font-bold text-white whitespace-nowrap">
                                        {{ $sub->codigo }}
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <div class="font-bold text-white">{{ $sub->cliente_nombre }}</div>
                                        @if($sub->cliente_telefono)
                                            <div class="text-[11px] text-slate-400 font-mono mt-0.5 flex items-center gap-1">
                                                <x-heroicon-m-phone class="h-3 w-3 inline text-slate-500" />
                                                <span>{{ $sub->cliente_telefono }}</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <div class="text-slate-200">{{ $sub->tipo_solucion ?: $sub->nombre_servicio }}</div>
                                        <div class="text-[11px] text-slate-400 font-mono mt-0.5">
                                            SIM: {{ $sub->sim_numero ?: 'Sin asignar' }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3.5 font-mono font-semibold text-slate-200 whitespace-nowrap">
                                        ${{ number_format($sub->tarifa_mensual, 2) }} {{ $sub->moneda }}
                                    </td>
                                    <td class="px-4 py-3.5 whitespace-nowrap">
                                        @if($sub->tiene_deuda && $sub->deuda_acumulada > 0)
                                            <div class="text-rose-400 font-mono font-bold">
                                                +${{ number_format($sub->deuda_acumulada, 2) }}
                                            </div>
                                            <div class="text-[10px] text-slate-500">
                                                ({{ $sub->meses_deuda_count }} {{ $sub->meses_deuda_count === 1 ? 'mes' : 'meses' }})
                                            </div>
                                        @else
                                            <span class="text-slate-500 font-mono">$0.00</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 font-mono font-black text-white text-sm whitespace-nowrap">
                                        ${{ number_format($sub->monto_a_cobrar, 2) }} {{ $sub->moneda }}
                                    </td>
                                    <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                        @if($esMora)
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-500/10 text-rose-400 border border-rose-500/30">
                                                <x-heroicon-m-exclamation-triangle class="h-3.5 w-3.5" />
                                                <span>En Mora</span>
                                            </span>
                                        @elseif($esCobrado)
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                                                <x-heroicon-m-check class="h-3.5 w-3.5 stroke-[2.5]" />
                                                <span>Cobrado</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-500/10 text-amber-400 border border-amber-500/30">
                                                <x-heroicon-m-clock class="h-3.5 w-3.5" />
                                                <span>Pendiente (Día 1-15)</span>
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                        <div class="flex items-center justify-center gap-2">
                                            @if($esCobrado)
                                                <button 
                                                    wire:click="abrirModalRecibo({{ $sub->id }})"
                                                    type="button" 
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-sky-400 font-bold text-xs border border-slate-700 transition-all cursor-pointer">
                                                    <x-heroicon-m-receipt-percent class="h-3.5 w-3.5" />
                                                    <span>Recibo</span>
                                                </button>
                                            @else
                                                <button 
                                                    wire:click="abrirModalPago({{ $sub->id }})"
                                                    type="button" 
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs shadow-md shadow-emerald-500/20 transition-all cursor-pointer">
                                                    <x-heroicon-m-credit-card class="h-3.5 w-3.5" />
                                                    <span>Cobrar</span>
                                                </button>
                                            @endif

                                            @if($sub->cliente_telefono)
                                                @php
                                                    $tel = preg_replace('/[^0-9]/', '', $sub->cliente_telefono);
                                                    $msg = rawurlencode("Hola {$sub->cliente_nombre}, le contactamos de DataPlus para recordarle el pago de su servicio mensual de Gestión Remota por un total de $" . number_format($sub->monto_a_cobrar, 2) . " {$sub->moneda}. Recuerde enviar el comprobante de su transferencia a este chat. ¡Muchas gracias!");
                                                @endphp
                                                <a 
                                                    href="https://wa.me/{{ $tel }}?text={{ $msg }}"
                                                    target="_blank"
                                                    title="Cobrar por WhatsApp"
                                                    class="p-1.5 rounded-xl bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 transition-all">
                                                    <x-heroicon-m-chat-bubble-left-ellipsis class="h-4 w-4" />
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-8 text-slate-500">
                                        No se encontraron registros con los filtros seleccionados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- ─── TAB 2: PAGARON ESTA SEMANA ─── --}}
        @if($activeTab === 'semana')
            <div class="space-y-4">
                <div class="rounded-2xl border border-slate-800 bg-slate-900/80 p-5 shadow-md">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2 mb-3">
                        <x-heroicon-o-check-circle class="h-5 w-5 text-emerald-400" />
                        <span>Clientes con Pago Registrado esta Semana</span>
                    </h3>
                    <div class="divide-y divide-slate-800">
                        @forelse($pagosSemana as $pago)
                            <div class="py-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                <div>
                                    <div class="font-bold text-white text-xs">{{ $pago->cliente_nombre }}</div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">
                                        Código: <span class="font-mono text-sky-400">{{ $pago->codigo_pago }}</span> · Fecha: {{ $pago->fecha_pago }} · Método: {{ $pago->metodo_pago }}
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="font-mono font-black text-emerald-400 text-sm">
                                        +${{ number_format($pago->monto_pagado, 2) }} {{ $pago->moneda }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6 text-slate-500 text-xs">
                                No hay cobros registrados en los últimos 7 días.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif

        {{-- ─── TAB 3: MAYORES DEUDORES ─── --}}
        @if($activeTab === 'deudores')
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($mayoresDeudores as $d)
                        <div class="rounded-2xl border border-rose-900/40 bg-slate-900/80 p-5 shadow-lg space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="font-mono text-xs font-bold text-rose-400 bg-rose-500/10 px-2.5 py-0.5 rounded-full border border-rose-500/20">
                                    {{ $d->codigo }}
                                </span>
                                <span class="text-xs font-bold text-rose-400 font-mono">
                                    {{ $d->meses_deuda_count }} meses atrasados
                                </span>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-white">{{ $d->cliente_nombre }}</h4>
                                <p class="text-xs text-slate-400 mt-0.5">{{ $d->tipo_solucion ?: $d->nombre_servicio }}</p>
                            </div>
                            <div class="flex items-center justify-between pt-2 border-t border-slate-800 text-xs">
                                <div>
                                    <span class="text-[11px] text-slate-400">Total con mora:</span>
                                    <p class="text-base font-black font-mono text-rose-400">
                                        ${{ number_format($d->monto_a_cobrar, 2) }} {{ $d->moneda }}
                                    </p>
                                </div>
                                <div class="flex items-center gap-2">
                                    @if($d->cliente_telefono)
                                        @php
                                            $telD = preg_replace('/[^0-9]/', '', $d->cliente_telefono);
                                            $msgD = rawurlencode("Hola {$d->cliente_nombre}, le contactamos de DataPlus para recordarle que presenta {$d->meses_deuda_count} meses pendientes de pago por su servicio de Gestión Remota. El monto total es de $" . number_format($d->monto_a_cobrar, 2) . " {$d->moneda}. Le solicitamos realizar la liquidación a la brevedad.");
                                        @endphp
                                        <a href="https://wa.me/{{ $telD }}?text={{ $msgD }}" target="_blank" class="p-2 rounded-xl bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                            <x-heroicon-m-chat-bubble-left-ellipsis class="h-4 w-4" />
                                        </a>
                                    @endif
                                    <button 
                                        wire:click="abrirModalPago({{ $d->id }})"
                                        type="button" 
                                        class="px-3 py-1.5 rounded-xl bg-rose-500 hover:bg-rose-400 text-slate-950 font-bold text-xs shadow-md shadow-rose-500/20 cursor-pointer">
                                        Cobrar
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-2 text-center py-8 text-slate-500 text-xs">
                            Excelente: No existen clientes con mora registrada.
                        </div>
                    @endforelse
                </div>
            </div>
        @endif

        {{-- ─── TAB 4: REPORTE TEMPORAL CON GRÁFICA ESTILIZADA (LINE & EVOLUTION) ─── --}}
        @if($activeTab === 'reporte')
            <div class="rounded-2xl border border-slate-800 bg-slate-900/80 p-6 shadow-xl space-y-6">
                {{-- Encabezado del Reporte --}}
                <div class="flex flex-col sm:flex-row items-center justify-between pb-4 border-b border-slate-800 gap-3">
                    <div>
                        <h3 class="text-base font-bold text-white flex items-center gap-2">
                            <x-heroicon-o-presentation-chart-line class="h-5 w-5 text-sky-400" />
                            <span>Evolución Temporal & Comparativa de Recaudación (2026)</span>
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">
                            Comportamiento histórico y cumplimiento mensual del servicio de Gestión Remota.
                        </p>
                    </div>

                    {{-- Selector de Periodicidad --}}
                    <div class="flex items-center p-1 bg-slate-950 rounded-xl border border-slate-800 text-xs shadow-inner">
                        <button 
                            wire:click="$set('periodoReporte', 'mensual')"
                            class="px-3.5 py-1.5 rounded-lg font-bold cursor-pointer transition-all {{ $periodoReporte === 'mensual' ? 'bg-sky-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                            Mensual
                        </button>
                        <button 
                            wire:click="$set('periodoReporte', 'trimestral')"
                            class="px-3.5 py-1.5 rounded-lg font-bold cursor-pointer transition-all {{ $periodoReporte === 'trimestral' ? 'bg-sky-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                            Trimestral
                        </button>
                        <button 
                            wire:click="$set('periodoReporte', 'semestral')"
                            class="px-3.5 py-1.5 rounded-lg font-bold cursor-pointer transition-all {{ $periodoReporte === 'semestral' ? 'bg-sky-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                            Semestral
                        </button>
                        <button 
                            wire:click="$set('periodoReporte', 'anual')"
                            class="px-3.5 py-1.5 rounded-lg font-bold cursor-pointer transition-all {{ $periodoReporte === 'anual' ? 'bg-sky-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                            Anual
                        </button>
                    </div>
                </div>

                {{-- CANVAS SVG DE LA GRÁFICA ESTILIZADA DE LA PLATAFORMA --}}
                <div class="rounded-xl border border-slate-800 bg-slate-950/70 p-5 space-y-4">
                    {{-- Leyenda --}}
                    <div class="flex flex-wrap items-center justify-between gap-3 text-xs border-b border-slate-800/80 pb-3">
                        <div class="flex items-center gap-6">
                            <div class="flex items-center gap-2">
                                <span class="w-4 h-[3px] bg-emerald-400 rounded-full shadow-sm shadow-emerald-400/50"></span>
                                <span class="text-slate-200 font-semibold">Recaudación CUP</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-4 h-[2px] border-b-2 border-dashed border-sky-400"></span>
                                <span class="text-sky-300">Recaudación USD</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-4 h-[1.5px] border-b border-dotted border-amber-400/70"></span>
                                <span class="text-slate-400">Meta Mensual (~$24,500 CUP)</span>
                            </div>
                        </div>
                        <div class="text-[11px] font-mono text-slate-400">
                            Año Fiscal 2026 · Mes en curso: <strong class="text-emerald-400">Octubre</strong>
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
                                'periodo' => $f['periodo'] ?? '',
                                'cup' => $monto,
                                'usd' => $f['usd_cobrado'] ?? 0,
                                'cumplimiento' => $f['cumplimiento'] ?? '0%'
                            ];
                        }

                        $pathLine = '';
                        if (count($puntos) > 0) {
                            $pathLine = 'M ' . $puntos[0]['x'] . ' ' . $puntos[0]['y'];
                            for ($i = 0; $i < count($puntos) - 1; $i++) {
                                $p0 = $puntos[$i];
                                $p1 = $puntos[$i + 1];
                                $cx = $p0['x'] + ($p1['x'] - $p0['x']) / 2;
                                $pathLine .= " C {$cx} {$p0['y']}, {$cx} {$p1['y']}, {$p1['x']} {$p1['y']}";
                            }
                        }
                        $areaLine = count($puntos) > 0 
                            ? $pathLine . ' L ' . $puntos[count($puntos)-1]['x'] . ' ' . ($svgH - $padY) . ' L ' . $puntos[0]['x'] . ' ' . ($svgH - $padY) . ' Z'
                            : '';
                    @endphp

                    <div class="w-full overflow-x-auto">
                        <svg viewBox="0 0 {{ $svgW }} {{ $svgH }}" class="w-full min-w-[650px] h-60 overflow-visible">
                            <defs>
                                <linearGradient id="dpLineGradient" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="#10b981" stop-opacity="0.25" />
                                    <stop offset="100%" stop-color="#10b981" stop-opacity="0.0" />
                                </linearGradient>
                            </defs>

                            {{-- Guías Horizontales --}}
                            @foreach([0, 7000, 14000, 21000, 28000] as $gridVal)
                                @php $gy = $svgH - $padY - (($gridVal / $maxCUP) * $innerH); @endphp
                                <line x1="{{ $padX }}" y1="{{ $gy }}" x2="{{ $svgW - $padX }}" y2="{{ $gy }}" stroke="rgba(255, 255, 255, 0.06)" stroke-dasharray="3 3" />
                                <text x="{{ $padX - 8 }}" y="{{ $gy + 3 }}" fill="#64748b" font-size="10" font-family="monospace" text-anchor="end">${{ $gridVal / 1000 }}k</text>
                            @endforeach

                            {{-- Meta Mensual --}}
                            <line x1="{{ $padX }}" y1="{{ $metaY }}" x2="{{ $svgW - $padX }}" y2="{{ $metaY }}" stroke="rgba(245, 158, 11, 0.4)" stroke-dasharray="4 4" stroke-width="1.5" />
                            <text x="{{ $svgW - $padX + 5 }}" y="{{ $metaY + 3 }}" fill="#f59e0b" font-size="9" font-family="monospace">Meta</text>

                            {{-- Área sombreada --}}
                            @if($areaLine)
                                <path d="{{ $areaLine }}" fill="url(#dpLineGradient)" />
                            @endif

                            {{-- Línea continua de recaudación CUP --}}
                            @if($pathLine)
                                <path d="{{ $pathLine }}" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                            @endif

                            {{-- Nodos y etiquetas --}}
                            @foreach($puntos as $pt)
                                @php $isActual = str_contains($pt['periodo'], 'Octubre') || str_contains($pt['periodo'], 'Q4') || str_contains($pt['periodo'], '2do Semestre'); @endphp
                                <g class="cursor-pointer group">
                                    <line x1="{{ $pt['x'] }}" y1="{{ $pt['y'] }}" x2="{{ $pt['x'] }}" y2="{{ $svgH - $padY }}" stroke="{{ $isActual ? 'rgba(56, 189, 248, 0.4)' : 'rgba(255, 255, 255, 0.05)' }}" stroke-dasharray="2 2" />
                                    <circle cx="{{ $pt['x'] }}" cy="{{ $pt['y'] }}" r="{{ $isActual ? '6' : '4.5' }}" fill="{{ $pt['cup'] > 0 ? '#10b981' : '#0f172a' }}" stroke="{{ $isActual ? '#38bdf8' : '#059669' }}" stroke-width="{{ $isActual ? '2.5' : '1.5' }}" />
                                    <text x="{{ $pt['x'] }}" y="{{ $svgH - $padY + 16 }}" fill="{{ $isActual ? '#38bdf8' : '#94a3b8' }}" font-size="10" font-weight="{{ $isActual ? 'bold' : 'normal' }}" font-family="monospace" text-anchor="middle">
                                        {{ mb_substr(explode(' ', $pt['periodo'])[0], 0, 3) }}
                                    </text>
                                </g>
                            @endforeach
                        </svg>
                    </div>

                    <div class="flex justify-between items-center text-[11px] text-slate-400 px-2 pt-2 border-t border-slate-800">
                        <span>Ene 2026</span>
                        <span>Plataforma DataPlus · Sistema de Análisis Contable</span>
                        <span>Dic 2026</span>
                    </div>
                </div>

                {{-- Tabla complementaria --}}
                <div class="overflow-x-auto rounded-2xl border border-slate-800 bg-slate-950/60 shadow-md">
                    <table class="w-full text-left text-xs text-slate-200">
                        <thead class="bg-slate-950/90 text-[10px] font-semibold uppercase tracking-wider text-slate-400 border-b border-slate-800">
                            <tr>
                                <th class="px-4 py-3">Periodo</th>
                                <th class="px-4 py-3 text-right">Recaudado (CUP)</th>
                                <th class="px-4 py-3 text-right">Recaudado (USD)</th>
                                <th class="px-4 py-3 text-right">Deuda Pendiente</th>
                                <th class="px-4 py-3 text-center">Cumplimiento</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/80">
                            @foreach($reporteTemporal['filas'] as $fila)
                                <tr class="hover:bg-slate-800/40 transition-colors">
                                    <td class="px-4 py-3 font-bold text-white whitespace-nowrap">
                                        {{ $fila['periodo'] }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-mono font-bold text-emerald-400 whitespace-nowrap">
                                        ${{ number_format($fila['cup_cobrado'], 2) }} CUP
                                    </td>
                                    <td class="px-4 py-3 text-right font-mono font-bold text-sky-400 whitespace-nowrap">
                                        ${{ number_format($fila['usd_cobrado'], 2) }} USD
                                    </td>
                                    <td class="px-4 py-3 text-right font-mono text-rose-400 whitespace-nowrap">
                                        ${{ number_format($fila['cup_deuda'], 0) }} CUP
                                    </td>
                                    <td class="px-4 py-3 text-center whitespace-nowrap">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold font-mono bg-sky-500/15 text-sky-300 border border-sky-500/30">
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

        {{-- ─── MODAL 1: REGISTRAR PAGO (DISMISS EN FONDO) ─── --}}
        @if($modalPagoOpen)
            <div 
                x-data 
                x-on:click.self="$wire.set('modalPagoOpen', false)" 
                x-on:keydown.escape.window="$wire.set('modalPagoOpen', false)" 
                class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4 overflow-y-auto cursor-pointer">
                <div 
                    x-on:click.stop 
                    class="bg-slate-900 border border-slate-700 rounded-2xl w-full max-w-lg p-6 shadow-2xl relative text-left my-8 cursor-default text-white">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-credit-card class="h-6 w-6 text-emerald-400" />
                            <h3 class="text-base font-bold text-white">Registrar Cobranza de Gestión Remota</h3>
                        </div>
                        <button 
                            wire:click="$set('modalPagoOpen', false)" 
                            type="button" 
                            class="text-slate-400 hover:text-white cursor-pointer p-1 rounded-lg">
                            <x-heroicon-m-x-mark class="h-5 w-5" />
                        </button>
                    </div>

                    <div class="mt-4 space-y-4 text-xs">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-slate-300 font-semibold mb-1">Monto Pagado</label>
                                <input 
                                    wire:model="formMontoPagado" 
                                    type="number" 
                                    step="0.01" 
                                    class="dp-modal-input font-mono font-bold" />
                            </div>
                            <div>
                                <label class="block text-slate-300 font-semibold mb-1">Día que Pagó</label>
                                <input 
                                    wire:model="formFechaPago" 
                                    type="date" 
                                    class="dp-modal-input font-mono" />
                            </div>
                        </div>

                        <div>
                            <label class="block text-slate-300 font-semibold mb-1">Método de Pago</label>
                            <div class="grid grid-cols-2 gap-2">
                                <button 
                                    wire:click="$set('formMetodoPago', 'transferencia')" 
                                    type="button" 
                                    class="px-3 py-2 rounded-xl font-bold border cursor-pointer text-center text-xs transition-all {{ $formMetodoPago === 'transferencia' ? 'bg-sky-500/20 text-sky-300 border-sky-500' : 'bg-slate-950 text-slate-400 border-slate-800' }}">
                                    Transferencia (Transfermóvil / EnZona)
                                </button>
                                <button 
                                    wire:click="$set('formMetodoPago', 'efectivo')" 
                                    type="button" 
                                    class="px-3 py-2 rounded-xl font-bold border cursor-pointer text-center text-xs transition-all {{ $formMetodoPago === 'efectivo' ? 'bg-sky-500/20 text-sky-300 border-sky-500' : 'bg-slate-950 text-slate-400 border-slate-800' }}">
                                    Pago en Efectivo (Cobro en Sitio)
                                </button>
                            </div>
                        </div>

                        @if($formMetodoPago === 'transferencia')
                            <div>
                                <label class="block text-slate-300 font-semibold mb-1">Número de Transacción / Operación</label>
                                <input 
                                    wire:model="formNumeroTransaccion" 
                                    type="text" 
                                    placeholder="TRF-9018231 o ID EnZona" 
                                    class="dp-modal-input font-mono font-bold" />
                            </div>
                        @else
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-slate-300 font-semibold mb-1">¿Quién Recibe?</label>
                                    <input 
                                        wire:model="formQuienRecibe" 
                                        type="text" 
                                        class="dp-modal-input" />
                                </div>
                                <div>
                                    <label class="block text-slate-300 font-semibold mb-1">¿Quién Entrega?</label>
                                    <input 
                                        wire:model="formQuienEntrega" 
                                        type="text" 
                                        placeholder="Nombre del cliente" 
                                        class="dp-modal-input" />
                                </div>
                            </div>
                        @endif

                        <div>
                            <label class="block text-slate-300 font-semibold mb-1">Notas / Observaciones</label>
                            <textarea 
                                wire:model="formNotasPago" 
                                rows="2" 
                                placeholder="Notas opcionales del pago..." 
                                class="dp-modal-input"></textarea>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
                        <button 
                            wire:click="$set('modalPagoOpen', false)" 
                            type="button" 
                            class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs border border-slate-700 cursor-pointer">
                            Cancelar
                        </button>
                        <button 
                            wire:click="guardarPago" 
                            type="button" 
                            class="px-5 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs shadow-lg shadow-emerald-500/25 cursor-pointer">
                            Confirmar Cobranza
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- ─── MODAL 2: VISOR DE COMPROBANTE DE WHATSAPP ─── --}}
        @if($modalVisorOpen && $comprobanteActual)
            <div 
                x-data 
                x-on:click.self="$wire.set('modalVisorOpen', false)" 
                x-on:keydown.escape.window="$wire.set('modalVisorOpen', false)" 
                class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4 overflow-y-auto cursor-pointer">
                <div 
                    x-on:click.stop 
                    class="bg-slate-900 border border-slate-700 rounded-2xl w-full max-w-md p-6 shadow-2xl relative text-left my-8 cursor-default text-white">
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
                        <div class="p-2 rounded-xl bg-slate-950 border border-slate-800 flex justify-center">
                            @if(!empty($comprobanteActual['url']))
                                <img src="{{ $comprobanteActual['url'] }}" alt="Comprobante" class="max-h-72 object-contain rounded-lg" />
                            @else
                                <div class="py-12 text-center text-slate-500">Sin archivo adjunto</div>
                            @endif
                        </div>
                        <p class="text-[11px] text-slate-400 font-mono text-center">
                            Ref: {{ $comprobanteActual['referencia'] ?? 'N/A' }} · Cliente: {{ $comprobanteActual['cliente'] ?? 'N/A' }}
                        </p>
                    </div>

                    <div class="mt-5 flex justify-end">
                        <button 
                            wire:click="$set('modalVisorOpen', false)" 
                            type="button" 
                            class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs border border-slate-700 cursor-pointer">
                            Cerrar Visor
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- ─── MODAL 3: RECIBO OFICIAL DATAPLUS ─── --}}
        @if($modalReciboOpen && $reciboActual)
            <div 
                x-data 
                x-on:click.self="$wire.set('modalReciboOpen', false)" 
                x-on:keydown.escape.window="$wire.set('modalReciboOpen', false)" 
                class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4 overflow-y-auto cursor-pointer">
                <div 
                    x-on:click.stop 
                    class="bg-slate-900 border border-slate-700 rounded-2xl w-full max-w-lg p-6 shadow-2xl relative text-left my-8 cursor-default text-white">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-receipt-percent class="h-6 w-6 text-sky-400" />
                            <h3 class="text-base font-bold text-white">Comprobante Oficial DataPlus S.R.L.</h3>
                        </div>
                        <button wire:click="$set('modalReciboOpen', false)" class="text-slate-400 hover:text-white cursor-pointer">
                            <x-heroicon-m-x-mark class="h-5 w-5" />
                        </button>
                    </div>

                    <div class="mt-4 p-4 rounded-xl border border-slate-700 bg-slate-950 space-y-3 text-xs font-mono">
                        <div class="text-center border-b border-slate-800 pb-2">
                            <h4 class="font-extrabold text-sm text-white">DATAPLUS PLATAFORMA</h4>
                            <p class="text-[10px] text-slate-400">Servicio de Gestión Remota & Enlaces 4G</p>
                            <p class="text-[10px] text-emerald-400 font-bold mt-1">RECIBO DE COBRO VERIFICADO</p>
                        </div>

                        <div class="space-y-1.5 text-slate-300 text-[11px]">
                            <div class="flex justify-between">
                                <span class="text-slate-400">No. Recibo:</span>
                                <span class="text-white font-bold">{{ $reciboActual['codigo_pago'] ?? '' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Cliente:</span>
                                <span class="text-white font-bold">{{ $reciboActual['cliente_nombre'] ?? '' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Servicio:</span>
                                <span class="text-white">{{ $reciboActual['servicio_nombre'] ?? 'Gestión Remota' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Mes Liquidado:</span>
                                <span class="text-white">{{ $reciboActual['mes_nombre'] ?? 'Octubre 2026' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Fecha de Pago:</span>
                                <span class="text-white">{{ $reciboActual['fecha_pago'] ?? '' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Método:</span>
                                <span class="text-white">{{ $reciboActual['metodo_pago'] ?? '' }}</span>
                            </div>
                            <div class="flex justify-between border-t border-slate-800 pt-2 text-xs font-bold">
                                <span class="text-white">Monto Abonado:</span>
                                <span class="text-emerald-400 font-black text-sm">
                                    ${{ number_format($reciboActual['monto_pagado'] ?? 0, 2) }} {{ $reciboActual['moneda'] ?? 'CUP' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-between pt-3 border-t border-slate-800">
                        @php
                            $telR = preg_replace('/[^0-9]/', '', $reciboActual['cliente_telefono'] ?? '');
                            $msgR = rawurlencode("*DATAPLUS - RECIBO DE PAGO*
Recibo: {$reciboActual['codigo_pago']}
Cliente: {$reciboActual['cliente_nombre']}
Mes: {$reciboActual['mes_nombre']}
Monto: $" . number_format($reciboActual['monto_pagado'] ?? 0, 2) . " {$reciboActual['moneda']}
¡Pago verificado y registrado exitosamente!");
                            $linkWpRecibo = "https://wa.me/{$telR}?text={$msgR}";
                        @endphp
                        <a 
                            href="{{ $linkWpRecibo }}" 
                            target="_blank"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md">
                            <x-heroicon-m-share class="h-4 w-4" />
                            <span>Enviar por WhatsApp</span>
                        </a>

                        <button 
                            wire:click="$set('modalReciboOpen', false)" 
                            type="button" 
                            class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold border border-slate-700 cursor-pointer">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- ─── MODAL 4: NUEVA SUSCRIPCIÓN (DISMISS EN FONDO Y ESTILO DATAPLUS) ─── --}}
        @if($modalNuevaSubOpen)
            <div 
                x-data 
                x-on:click.self="$wire.set('modalNuevaSubOpen', false)" 
                x-on:keydown.escape.window="$wire.set('modalNuevaSubOpen', false)" 
                class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4 overflow-y-auto cursor-pointer">
                <div 
                    x-on:click.stop
                    class="bg-slate-900 border border-slate-700 rounded-2xl w-full max-w-lg p-6 shadow-2xl relative text-left my-8 cursor-default text-white">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-plus-circle class="h-6 w-6 text-sky-400" />
                            <h3 class="text-base font-bold text-white">Nueva Suscripción de Gestión Remota</h3>
                        </div>
                        <button 
                            wire:click="$set('modalNuevaSubOpen', false)" 
                            type="button"
                            class="text-slate-400 hover:text-white cursor-pointer p-1 rounded-lg">
                            <x-heroicon-m-x-mark class="h-5 w-5" />
                        </button>
                    </div>

                    <div class="mt-4 space-y-3.5 text-xs">
                        <div>
                            <label class="block text-slate-300 font-semibold mb-1">Nombre del Cliente / Negocio *</label>
                            <input 
                                wire:model="newSubClienteNombre" 
                                type="text" 
                                placeholder="Ej: Hostal Miramar o Juan Pérez"
                                class="dp-modal-input" />
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-slate-300 font-semibold mb-1">Teléfono Móvil (WhatsApp)</label>
                                <input 
                                    wire:model="newSubClienteTelefono" 
                                    type="text" 
                                    placeholder="+53 5200 0000"
                                    class="dp-modal-input font-mono" />
                            </div>
                            <div>
                                <label class="block text-slate-300 font-semibold mb-1">Número SIM Card</label>
                                <input 
                                    wire:model="newSubSim" 
                                    type="text" 
                                    placeholder="535XXXXXXX"
                                    class="dp-modal-input font-mono" />
                            </div>
                        </div>

                        <div>
                            <label class="block text-slate-300 font-semibold mb-1">Solución de Gestión Remota</label>
                            <input 
                                wire:model="newSubSolucion" 
                                type="text" 
                                placeholder="Ej: Router 4G LTE Huawei B310 o MikroTik"
                                class="dp-modal-input" />
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-slate-300 font-semibold mb-1">Moneda de Cobro</label>
                                <select 
                                    wire:model="newSubMoneda" 
                                    class="dp-modal-input font-bold">
                                    <option value="CUP">CUP (Pesos Cubanos)</option>
                                    <option value="USD">USD (Dólares)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-slate-300 font-semibold mb-1">Tarifa Mensual</label>
                                <input 
                                    wire:model="newSubMonto" 
                                    type="number" 
                                    step="0.01" 
                                    class="dp-modal-input font-mono font-bold" />
                            </div>
                        </div>

                        <div>
                            <label class="block text-slate-300 font-semibold mb-1">Notas / Observaciones</label>
                            <textarea 
                                wire:model="newSubNotas" 
                                rows="2" 
                                placeholder="Condiciones especiales o notas..."
                                class="dp-modal-input"></textarea>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3 pt-3 border-t border-slate-800">
                        <button 
                            wire:click="$set('modalNuevaSubOpen', false)" 
                            type="button" 
                            class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs border border-slate-700 cursor-pointer">
                            Cancelar
                        </button>
                        <button 
                            wire:click="crearSuscripcion" 
                            type="button" 
                            class="px-5 py-2 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-bold text-xs shadow-lg shadow-sky-600/30 cursor-pointer">
                            Guardar Suscripción
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
