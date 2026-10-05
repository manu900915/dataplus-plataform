<?php

namespace App\Filament\Pages;

use App\Models\Brigada;
use App\Models\Cliente;
use App\Models\Incidencia;
use App\Models\Item;
use App\Models\LineaPresupuesto;
use App\Models\Proyecto;
use App\Models\Servicio;
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
     * Datos del Dashboard calculados y cacheados 1 minuto para máxima reactividad y velocidad.
     */
    public function getData(): array
    {
        return Cache::remember('dashboard:preview_match:v3', now()->addMinutes(1), function () {
            // Proyectos
            $proyectosActivos = Proyecto::whereIn('estado', ['borrador', 'en_progreso'])->count();
            $proyectosCompletados = Proyecto::where('estado', 'completado')->count();
            $retrasados = Proyecto::where('estado', 'en_progreso')
                ->whereNotNull('fecha_fin')
                ->where('fecha_fin', '<', now())
                ->count();
            $presupuestoTotal = (float) Proyecto::sum('presupuesto_total');
            $maxPresupuesto = (float) (Proyecto::max('presupuesto_total') ?: 1);

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

            // Operaciones / Servicios / Incidencias
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

            $incidenciasAbiertas = Incidencia::whereNotIn('estado', ['Resuelta', 'Cerrada', 'Cancelada'])->count();
            $incidenciasRecientes = Incidencia::with('cliente')
                ->whereNotIn('estado', ['Cerrada', 'Cancelada'])
                ->latest()
                ->take(4)
                ->get()
                ->map(fn (Incidencia $i) => [
                    'id'             => $i->id,
                    'codigo'         => $i->codigo,
                    'titulo'         => $i->titulo,
                    'cliente_nombre' => $i->cliente?->nombre ?? 'Sin cliente',
                    'prioridad'      => $i->prioridad,
                    'tipo'           => $i->tipo,
                    'estado'         => $i->estado,
                ])->all();

            // Inventario (usando relación 'categoria', ya que Item pertenece a CategoriaItem)
            $itemsTotal = Item::count();
            $stockBajoCount = Item::whereColumn('stock_actual', '<=', 'stock_minimo')->count();
            $equipamientoStock = (int) Item::where('es_equipamiento', true)->sum('stock_actual');
            $materialesStock = (int) Item::where('es_equipamiento', false)->sum('stock_actual');

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

            // Finanzas
            $presupuestoEquipamiento = (float) LineaPresupuesto::where('tipo_linea', 'equipamiento')->sum('subtotal');
            $presupuestoManoObra = (float) LineaPresupuesto::where('tipo_linea', 'mano_obra')->sum('subtotal');
            $presupuestoMateriales = (float) LineaPresupuesto::where('tipo_linea', 'material')->sum('subtotal');
            $presupuestoTransporte = (float) LineaPresupuesto::where('tipo_linea', 'transporte')->sum('subtotal');
            $finanzasMes = (float) Proyecto::whereMonth('created_at', now()->month)->sum('presupuesto_total');

            // Administración
            $clientesTotal = Cliente::count();
            $clientesActivos = Cliente::where('activo', true)->count();
            $usuariosTotal = User::where('activo', true)->count();

            return [
                // Proyectos
                'proyectos_activos'        => $proyectosActivos,
                'proyectos_completados'    => $proyectosCompletados,
                'retrasados'               => $retrasados,
                'presupuesto_total'        => $presupuestoTotal,
                'max_presupuesto'          => $maxPresupuesto,
                'top_proyectos'            => $topProyectos,
                'proyectos_recientes'      => $proyectosRecientes,

                // Operaciones
                'brigadas_count'           => $brigadasCount,
                'servicios_total'          => $serviciosTotal,
                'servicios_cctv'           => $serviciosCctv,
                'servicios_saci'           => $serviciosSaci,
                'servicios_gr'             => $serviciosGr,
                'servicios_recientes'      => $serviciosRecientes,
                'incidencias_abiertas'     => $incidenciasAbiertas,
                'incidencias_recientes'    => $incidenciasRecientes,

                // Inventario
                'items_total'              => $itemsTotal,
                'stock_bajo_count'         => $stockBajoCount,
                'equipamiento_stock'       => $equipamientoStock,
                'materiales_stock'         => $materialesStock,
                'alertas_stock'            => $alertasStock,

                // Finanzas
                'presupuesto_equipamiento' => $presupuestoEquipamiento,
                'presupuesto_mano_obra'    => $presupuestoManoObra,
                'presupuesto_materiales'   => $presupuestoMateriales,
                'presupuesto_transporte'   => $presupuestoTransporte,
                'finanzas_mes'             => $finanzasMes,

                // Administración
                'clientes_total'           => $clientesTotal,
                'clientes_activos'         => $clientesActivos,
                'usuarios_total'           => $usuariosTotal,
            ];
        });
    }
}
