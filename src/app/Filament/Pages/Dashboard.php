<?php

namespace App\Filament\Pages;

use App\Models\Almacen;
use App\Models\Brigada;
use App\Models\Cliente;
use App\Models\ClienteUbicacion;
use App\Models\ContactoCliente;
use App\Models\Incidencia;
use App\Models\Item;
use App\Models\Proyecto;
use App\Models\Servicio;
use App\Models\SolicitudServicio;
use App\Models\User;
use App\Models\SuscripcionGestionRemota;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?int $navigationSort = -2;
    protected static string $view = 'filament.pages.dashboard';

    /**
     * Datos del Dashboard con consolidación financiera por todos los conceptos y módulos reales.
     */
    public function getData(): array
    {
        return Cache::remember('dashboard:real_modules:v3', now()->addMinutes(1), function () {
            // ─── CONSOLIDACIÓN FINANCIERA (DINERO GENERADO POR CUALQUIER CONCEPTO) ───
            $presupuestoObras = (float) Proyecto::sum('presupuesto_total');
            $solicitudesAprobadasMonto = (float) SolicitudServicio::whereIn('estado', ['Aprobada', 'Convertida_Proyecto'])
                ->sum('presupuesto_estimado');
            $solicitudesTodasMonto = (float) SolicitudServicio::sum('presupuesto_estimado');
            $facturacionIncidencias = (float) Incidencia::sum('monto_facturado');
            $costoIntervenciones = (float) Incidencia::sum('costo_estimado');
            $recargasGestionRemota = (float) Servicio::sum('gr_recarga_monto');

            // Gastos de campo reportados por especialistas (transporte y almuerzo)
            $gastosTransporteTotal = (float) Incidencia::sum('gasto_transporte');
            $gastosAlmuerzoTotal = (float) Incidencia::sum('gasto_almuerzo');
            $gastosCampoTotal = $gastosTransporteTotal + $gastosAlmuerzoTotal;

            // Valoración de inventario en almacenes
            $valorInventario = (float) Item::select(DB::raw('SUM(stock_actual * COALESCE(precio_venta, precio_unitario, precio_costo, 0)) as total'))
                ->value('total');

            // Dinero total generado por cualquier concepto (obras + solicitudes en cartera + asistencias facturadas + servicios 4G)
            $ingresosServiciosTotal = $facturacionIncidencias > 0 ? $facturacionIncidencias : $costoIntervenciones;
            $dineroGeneradoTotal = $presupuestoObras + $solicitudesAprobadasMonto + $ingresosServiciosTotal + $recargasGestionRemota;

            // ─── 1. OPERACIONES (Servicios, Incidencias, Brigadas) ───
            $brigadasCount = Brigada::where('activa', true)->count();
            $serviciosTotal = Servicio::count();
            $serviciosCctv = Servicio::where('tipo', 'CCTV')->count();
            $serviciosSaci = Servicio::where('tipo', 'SACI')->count();
            $serviciosGr = Servicio::whereIn('tipo', ['Gestion_Remota', 'Redes'])->count();

            $serviciosRecientes = Servicio::with('ubicacion.cliente')
                ->latest()
                ->take(4)
                ->get()
                ->map(fn (Servicio $s) => [
                    'id'             => $s->id,
                    'codigo'         => 'SRV-' . str_pad($s->id, 4, '0', STR_PAD_LEFT),
                    'tipo'           => $s->tipo,
                    'cliente_nombre' => $s->ubicacion?->cliente?->nombre ?? 'Sede Central',
                    'detalle'        => $s->cctv_modelo ?: ($s->saci_modelo ?: ($s->notas ?: 'Operativo en campo')),
                ])->all();

            $incidenciasAbiertas = Incidencia::whereNotIn('estado', ['Resuelta', 'Revisada_Supervisor', 'Cerrada', 'Cancelada'])->count();
            $incidenciasCriticas = Incidencia::whereIn('estado', ['Pendiente', 'Asignada', 'En_Progreso', 'En_Espera'])
                ->where(function ($q) {
                    $q->where('prioridad', 'Critica')
                      ->orWhere(function ($sub) {
                          $sub->whereNotNull('fecha_limite')->where('fecha_limite', '<', now());
                      });
                })->count();

            $incidenciasRecientes = Incidencia::with('cliente')
                ->whereNotIn('estado', ['Cerrada', 'Cancelada'])
                ->latest()
                ->take(4)
                ->get()
                ->map(fn (Incidencia $i) => [
                    'id'               => $i->id,
                    'codigo'           => $i->codigo,
                    'titulo'           => $i->titulo,
                    'cliente_nombre'   => $i->cliente?->nombre ?? 'Sin cliente',
                    'prioridad'        => $i->prioridad,
                    'tipo'             => $i->tipo,
                    'estado'           => $i->estado,
                    'gasto_transporte' => (float) ($i->gasto_transporte ?? 0),
                    'gasto_almuerzo'   => (float) ($i->gasto_almuerzo ?? 0),
                    'total_gastos'     => (float) ($i->total_gastos_operativos ?? 0),
                ])->all();

            // ─── 2. PROYECTOS & COMERCIAL (Proyectos, Solicitudes) ───
            $proyectosActivos = Proyecto::whereIn('estado', ['borrador', 'en_progreso'])->count();
            $proyectosCompletados = Proyecto::where('estado', 'completado')->count();
            $retrasados = Proyecto::where('estado', 'en_progreso')
                ->whereNotNull('fecha_fin')
                ->where('fecha_fin', '<', now())
                ->count();
            $maxPresupuesto = (float) (Proyecto::max('presupuesto_total') ?: 1);

            $solicitudesPendientes = SolicitudServicio::where('estado', 'Pendiente_Aprobacion')->count();
            $solicitudesRecientes = SolicitudServicio::with('cliente')
                ->latest()
                ->take(4)
                ->get()
                ->map(fn (SolicitudServicio $s) => [
                    'id'          => $s->id,
                    'codigo'      => $s->codigo,
                    'titulo'      => $s->titulo,
                    'cliente'     => $s->cliente?->nombre ?? 'Sin cliente',
                    'estado'      => $s->estado,
                    'presupuesto' => (float) $s->presupuesto_estimado,
                    'prioridad'   => $s->prioridad,
                ])->all();

            $topProyectos = Proyecto::with('cliente')
                ->orderByDesc('presupuesto_total')
                ->take(5)
                ->get()
                ->map(fn (Proyecto $p) => [
                    'id'                => $p->id,
                    'nombre'            => $p->nombre,
                    'codigo'            => $p->codigo,
                    'presupuesto_total' => (float) $p->presupuesto_total,
                    'cliente_nombre'    => $p->cliente?->nombre ?? 'Sin cliente',
                ])->all();

            $proyectosRecientes = Proyecto::with('cliente')
                ->latest()
                ->take(5)
                ->get()
                ->map(fn (Proyecto $p) => [
                    'id'             => $p->id,
                    'nombre'         => $p->nombre,
                    'codigo'         => $p->codigo,
                    'estado'         => $p->estado,
                    'cliente_nombre' => $p->cliente?->nombre ?? 'Sin cliente',
                ])->all();

            // ─── 3. INVENTARIO & ALMACENES ───
            $itemsTotal = Item::count();
            $stockBajoCount = Item::whereColumn('stock_actual', '<=', 'stock_minimo')->count();
            $equipamientoStock = (int) Item::where('es_equipamiento', true)->sum('stock_actual');
            $almacenesTotal = Almacen::count();

            $alertasStock = Item::with('categoria')
                ->whereColumn('stock_actual', '<=', 'stock_minimo')
                ->orderBy('stock_actual')
                ->take(6)
                ->get()
                ->map(fn (Item $item) => [
                    'id'               => $item->id,
                    'nombre'           => $item->nombre,
                    'codigo'           => $item->codigo,
                    'categoria_nombre' => $item->categoria?->nombre ?? 'General',
                    'stock_actual'     => $item->stock_actual,
                    'unidad_medida'    => $item->unidad_medida,
                    'stock_minimo'     => $item->stock_minimo,
                ])->all();

            $almacenesList = Almacen::with('responsable')
                ->latest()
                ->take(4)
                ->get()
                ->map(fn (Almacen $a) => [
                    'id'          => $a->id,
                    'nombre'      => $a->nombre,
                    'provincia'   => $a->provincia ?? 'La Habana',
                    'municipio'   => $a->municipio ?? 'Plaza',
                    'responsable' => $a->responsable?->name ?? 'Sin asignar',
                ])->all();

            // ─── 4. CLIENTES & SEDES ───
            $clientesTotal = Cliente::count();
            $clientesActivos = Cliente::where('activo', true)->count();
            $ubicacionesTotal = ClienteUbicacion::count();
            $contactosTotal = ContactoCliente::count();

            $clientesRecientes = Cliente::withCount(['ubicaciones', 'servicios'])
                ->latest()
                ->take(5)
                ->get()
                ->map(fn (Cliente $c) => [
                    'id'          => $c->id,
                    'nombre'      => $c->nombre,
                    'cif'         => $c->cif ?? 'S/N',
                    'telefono'    => $c->telefono ?? 'Sin teléfono',
                    'ubicaciones' => $c->ubicaciones_count ?? 0,
                    'servicios'   => $c->servicios_count ?? 0,
                    'activo'      => $c->activo,
                ])->all();

            $ubicacionesRecientes = ClienteUbicacion::with(['cliente', 'tipoNegocio'])
                ->latest()
                ->take(4)
                ->get()
                ->map(fn (ClienteUbicacion $u) => [
                    'id'           => $u->id,
                    'nombre'       => $u->nombre,
                    'cliente'      => $u->cliente?->nombre ?? 'Cliente',
                    'provincia'    => $u->provincia ?? 'La Habana',
                    'tipo_negocio' => $u->tipoNegocio?->nombre ?? 'Comercial',
                ])->all();

            // ─── 5. ADMINISTRACIÓN & SISTEMA ───
            $usuariosTotal = User::where('activo', true)->count();
            $tecnicosTotal = User::where('activo', true)
                ->whereHas('roles', fn ($q) => $q->where('name', 'like', '%tecnico%')->orWhere('name', 'like', '%brigada%')->orWhere('name', 'like', '%especialista%'))
                ->count();
            if ($tecnicosTotal === 0) {
                $tecnicosTotal = $usuariosTotal;
            }

            // ─── 6. GESTIÓN REMOTA FACTURACIÓN & COBRANZAS ───
            $grSubsTotal = 0;
            $grRecaudadoCup = 0.0;
            $grRecaudadoUsd = 0.0;
            $grPendienteCup = 0.0;
            $grPendienteUsd = 0.0;
            $grDeudaCup = 0.0;
            $grDeudaUsd = 0.0;
            $grDeudoresCount = 0;
            $grDeudoresList = [];

            if (Schema::hasTable('suscripciones_gestion_remota')) {
                $grSubs = SuscripcionGestionRemota::where('activo', true)->get();
                $grSubsTotal = $grSubs->count();
                foreach ($grSubs as $sub) {
                    $tot = (float) $sub->monto_mensual + (float) $sub->deuda_acumulada;
                    if ($sub->tiene_deuda || $sub->deuda_acumulada > 0) {
                        $grDeudoresCount++;
                    }
                    if ($sub->moneda === 'USD') {
                        $grDeudaUsd += (float) $sub->deuda_acumulada;
                        if ($sub->estado_cobro === 'cobrado') {
                            $grRecaudadoUsd += (float) $sub->monto_mensual;
                        } else {
                            $grPendienteUsd += $tot;
                        }
                    } else {
                        $grDeudaCup += (float) $sub->deuda_acumulada;
                        if ($sub->estado_cobro === 'cobrado') {
                            $grRecaudadoCup += (float) $sub->monto_mensual;
                        } else {
                            $grPendienteCup += $tot;
                        }
                    }
                }

                $grDeudoresList = SuscripcionGestionRemota::where('activo', true)
                    ->where(function ($q) {
                        $q->where('tiene_deuda', true)->orWhere('deuda_acumulada', '>', 0);
                    })
                    ->orderBy('deuda_acumulada', 'desc')
                    ->take(5)
                    ->get()
                    ->map(fn ($s) => [
                        'codigo' => $s->codigo,
                        'cliente' => $s->cliente_nombre,
                        'moneda' => $s->moneda,
                        'deuda' => (float) $s->deuda_acumulada,
                        'meses' => $s->meses_deuda_count,
                        'telefono' => $s->cliente_telefono,
                    ])->all();
            }

            return [
                // Gestión Remota Facturación
                'gr_subs_total'            => $grSubsTotal,
                'gr_recaudado_cup'         => $grRecaudadoCup,
                'gr_recaudado_usd'         => $grRecaudadoUsd,
                'gr_pendiente_cup'         => $grPendienteCup,
                'gr_pendiente_usd'         => $grPendienteUsd,
                'gr_deuda_cup'             => $grDeudaCup,
                'gr_deuda_usd'             => $grDeudaUsd,
                'gr_deudores_count'        => $grDeudoresCount,
                'gr_deudores_list'         => $grDeudoresList,

                // Consolidación Financiera (Dinero por cualquier concepto)
                'dinero_generado_total'    => $dineroGeneradoTotal,
                'presupuesto_total'        => $presupuestoObras,
                'solicitudes_monto'        => $solicitudesAprobadasMonto,
                'solicitudes_todas_monto'  => $solicitudesTodasMonto,
                'ingresos_servicios_total' => $ingresosServiciosTotal,
                'recargas_gr_total'        => $recargasGestionRemota,
                'gastos_transporte_total'  => $gastosTransporteTotal,
                'gastos_almuerzo_total'    => $gastosAlmuerzoTotal,
                'gastos_campo_total'       => $gastosCampoTotal,
                'valor_inventario_total'   => $valorInventario,

                // Operaciones
                'brigadas_count'           => $brigadasCount,
                'servicios_total'          => $serviciosTotal,
                'servicios_cctv'           => $serviciosCctv,
                'servicios_saci'           => $serviciosSaci,
                'servicios_gr'             => $serviciosGr,
                'servicios_recientes'      => $serviciosRecientes,
                'incidencias_abiertas'     => $incidenciasAbiertas,
                'incidencias_criticas'     => $incidenciasCriticas,
                'incidencias_recientes'    => $incidenciasRecientes,

                // Proyectos & Comercial
                'proyectos_activos'        => $proyectosActivos,
                'proyectos_completados'    => $proyectosCompletados,
                'retrasados'               => $retrasados,
                'max_presupuesto'          => $maxPresupuesto,
                'solicitudes_pendientes'   => $solicitudesPendientes,
                'solicitudes_recientes'    => $solicitudesRecientes,
                'top_proyectos'            => $topProyectos,
                'proyectos_recientes'      => $proyectosRecientes,

                // Inventario & Almacenes
                'items_total'              => $itemsTotal,
                'stock_bajo_count'         => $stockBajoCount,
                'equipamiento_stock'       => $equipamientoStock,
                'almacenes_total'          => $almacenesTotal,
                'alertas_stock'            => $alertasStock,
                'almacenes_list'           => $almacenesList,

                // Clientes & Sedes
                'clientes_total'           => $clientesTotal,
                'clientes_activos'         => $clientesActivos,
                'ubicaciones_total'        => $ubicacionesTotal,
                'contactos_total'          => $contactosTotal,
                'clientes_recientes'       => $clientesRecientes,
                'ubicaciones_recientes'    => $ubicacionesRecientes,

                // Administración
                'usuarios_total'           => $usuariosTotal,
                'tecnicos_total'           => $tecnicosTotal,
            ];
        });
    }
}
