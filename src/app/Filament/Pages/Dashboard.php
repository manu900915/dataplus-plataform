<?php

namespace App\Filament\Pages;

use App\Models\Brigada;
use App\Models\Cliente;
use App\Models\Incidencia;
use App\Models\Item;
use App\Models\LineaPresupuesto;
use App\Models\Proyecto;
use App\Models\SolicitudServicio;
use App\Models\User;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Cache;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?int $navigationSort = -2;
    protected static string $view = 'filament.pages.dashboard';

    /**
     * Datos del Dashboard calculados y cacheados 2 minutos para alta velocidad y fluidez.
     */
    public function getData(): array
    {
        return Cache::remember('dashboard:stats:v6', now()->addMinutes(2), function () {
            // ─── 1. Incidencias & Helpdesk Operativo ───
            $activas = Incidencia::whereIn('estado', ['Pendiente', 'Asignada', 'En_Progreso', 'En_Espera'])->count();
            
            $criticasOVencidas = Incidencia::whereIn('estado', ['Pendiente', 'Asignada', 'En_Progreso', 'En_Espera'])
                ->where(function ($q) {
                    $q->where('prioridad', 'Critica')
                      ->orWhere(function ($sub) {
                          $sub->whereNotNull('fecha_limite')->where('fecha_limite', '<', now());
                      });
                })->count();

            $porRevisarSupervisor = Incidencia::where('estado', 'Resuelta')->count();
            $porCerrarComercial = Incidencia::where('estado', 'Revisada_Supervisor')->count();

            // Cálculo real de cumplimiento SLA
            $totalConSlaResueltas = Incidencia::whereNotNull('fecha_resolucion')
                ->whereNotNull('fecha_limite')
                ->count();

            $dentroDeSla = Incidencia::whereNotNull('fecha_resolucion')
                ->whereNotNull('fecha_limite')
                ->whereColumn('fecha_resolucion', '<=', 'fecha_limite')
                ->count();

            $porcentajeSla = $totalConSlaResueltas > 0 
                ? round(($dentroDeSla / $totalConSlaResueltas) * 100, 1) 
                : 100;

            // Incidencias urgentes en vivo (para panel de acción rápida)
            $incidenciasUrgentes = Incidencia::with(['cliente', 'tecnico'])
                ->whereIn('estado', ['Pendiente', 'Asignada', 'En_Progreso', 'En_Espera'])
                ->orderByRaw("CASE WHEN prioridad = 'Critica' THEN 1 WHEN prioridad = 'Alta' THEN 2 ELSE 3 END")
                ->orderBy('fecha_limite')
                ->take(5)
                ->get()
                ->map(fn (Incidencia $inc) => [
                    'id'               => $inc->id,
                    'codigo'           => $inc->codigo,
                    'titulo'           => $inc->titulo,
                    'cliente'          => $inc->cliente?->nombre ?? 'Sin cliente',
                    'tecnico'          => $inc->tecnico?->name ?? 'Sin asignar',
                    'prioridad'        => $inc->prioridad,
                    'estado'           => $inc->estado,
                    'esta_vencida'     => $inc->esta_vencida,
                    'horas_restantes'  => $inc->horas_restantes,
                    'horas_vencida'    => $inc->horas_vencida,
                    'tiempo_abierta'   => $inc->tiempo_transcurrido_texto,
                ])->all();

            // ─── 2. Pipeline Comercial & Proyectos ───
            $solicitudesPendientes = SolicitudServicio::where('estado', 'Pendiente_Aprobacion')->count();
            $solicitudesConvertidas = SolicitudServicio::where('estado', 'Convertida_Proyecto')->count();

            $solicitudesRecientes = SolicitudServicio::with('cliente')
                ->latest()
                ->take(5)
                ->get()
                ->map(fn (SolicitudServicio $s) => [
                    'id'          => $s->id,
                    'codigo'      => $s->codigo,
                    'titulo'      => $s->titulo,
                    'cliente'     => $s->cliente?->nombre ?? 'Sin cliente',
                    'estado'      => $s->estado,
                    'presupuesto' => (float) $s->presupuesto_estimado,
                    'fecha'       => $s->created_at?->diffForHumans(),
                ])->all();

            $proyectosActivos = Proyecto::whereIn('estado', ['borrador', 'en_progreso'])->count();
            $proyectosMes = Proyecto::where('estado', 'completado')->whereMonth('created_at', now()->month)->count();
            $retrasados = Proyecto::where('estado', 'en_progreso')->where('fecha_fin', '<', now())->count();
            $presupuestoTotal = (float) Proyecto::sum('presupuesto_total');

            // ─── 3. Inventario & Almacenes ───
            $items = Item::count();
            $stockBajo = Item::whereColumn('stock_actual', '<=', 'stock_minimo')->count();
            $equipamientoStock = (int) Item::where('es_equipamiento', true)->sum('stock_actual');
            $materialesStock = (int) Item::where('es_equipamiento', false)->sum('stock_actual');

            $alertasStock = Item::whereColumn('stock_actual', '<=', 'stock_minimo')
                ->orderBy('stock_actual')
                ->take(8)
                ->get()
                ->map(fn (Item $item) => [
                    'nombre'        => $item->nombre,
                    'codigo'        => $item->codigo,
                    'stock_actual'  => $item->stock_actual,
                    'unidad_medida' => $item->unidad_medida,
                    'stock_minimo'  => $item->stock_minimo,
                ])->all();

            // ─── 4. Finanzas & Costos ───
            $finanzasMes = (float) Proyecto::whereMonth('created_at', now()->month)->sum('presupuesto_total');
            $presupuestoEquipamiento = (float) LineaPresupuesto::where('tipo_linea', 'equipamiento')->sum('subtotal');
            $presupuestoManoObra = (float) LineaPresupuesto::where('tipo_linea', 'mano_obra')->sum('subtotal');

            $topProyectos = Proyecto::orderByDesc('presupuesto_total')
                ->take(5)
                ->get()
                ->map(fn (Proyecto $p) => [
                    'nombre'            => $p->nombre,
                    'presupuesto_total' => (float) $p->presupuesto_total,
                ])->all();

            $maxPresupuesto = (float) (Proyecto::max('presupuesto_total') ?: 1);

            return [
                // Operaciones / Helpdesk
                'incidencias_activas'         => $activas,
                'incidencias_criticas'        => $criticasOVencidas,
                'por_revisar_supervisor'      => $porRevisarSupervisor,
                'por_cerrar_comercial'        => $porCerrarComercial,
                'porcentaje_sla'              => $porcentajeSla,
                'incidencias_urgentes'        => $incidenciasUrgentes,
                'brigadas'                    => Brigada::where('activa', true)->count(),

                // Pipeline Comercial & Proyectos
                'solicitudes_pendientes'      => $solicitudesPendientes,
                'solicitudes_convertidas'     => $solicitudesConvertidas,
                'solicitudes_recientes'       => $solicitudesRecientes,
                'proyectos_activos'           => $proyectosActivos,
                'proyectos_mes'               => $proyectosMes,
                'retrasados'                  => $retrasados,
                'presupuesto_total'           => $presupuestoTotal,
                'recientes'                   => Proyecto::latest()->take(5)->get()->map(fn ($p) => [
                    'nombre'     => $p->nombre,
                    'created_at' => $p->created_at?->diffForHumans(),
                    'estado'     => $p->estado,
                ])->all(),

                // Inventario
                'items'                       => $items,
                'stock_bajo'                  => $stockBajo,
                'equipamiento_stock'          => $equipamientoStock,
                'materiales_stock'            => $materialesStock,
                'alertas_stock'               => $alertasStock,

                // Finanzas
                'finanzas_mes'                => $finanzasMes,
                'presupuesto_equipamiento'    => $presupuestoEquipamiento,
                'presupuesto_mano_obra'       => $presupuestoManoObra,
                'top_proyectos'               => $topProyectos,
                'max_presupuesto'             => $maxPresupuesto,

                // Administración
                'usuarios'                    => User::where('activo', true)->count(),
                'clientes'                    => Cliente::count(),
                'clientes_activos'            => Cliente::where('activo', true)->count(),
            ];
        });
    }
}
