<?php

namespace App\Filament\Pages;

use App\Models\Cliente;
use App\Models\Incidencia;
use App\Models\InventarioMovimiento;
use App\Models\Proyecto;
use App\Models\Servicio;
use App\Models\SolicitudServicio;
use App\Models\User;
use Carbon\Carbon;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportesPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'Reportes';
    protected static ?string $navigationLabel = 'Reportes & Balances';
    protected static ?string $slug = 'reportes';
    protected static ?string $title = 'Centro de Reportes & Balances';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.pages.reportes';

    // ─── Propiedades reactivas de filtro ───
    public string $periodo = 'mensual'; // 'diario', 'semanal', 'mensual', 'anual', 'personalizado'
    public string $fecha = '';
    public string $mes = '';
    public int $ano = 2026;
    public string $fechaInicio = '';
    public string $fechaFin = '';

    // Filtros opcionales
    public ?int $tecnicoId = null;
    public ?int $clienteId = null;
    public ?string $tipoServicio = null;

    public function mount(): void
    {
        $now = now();
        $this->fecha = $now->format('Y-m-d');
        $this->mes = $now->format('Y-m');
        $this->ano = (int) $now->year;
        $this->fechaInicio = $now->startOfMonth()->format('Y-m-d');
        $this->fechaFin = $now->format('Y-m-d');
    }

    public static function canAccess(): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        if ($user->hasAnyRole(['Administrador', 'Supervisor', 'Contable', 'admin', 'supervisor', 'contable'])) {
            return true;
        }

        return !$user->roles()->exists();
    }

    /**
     * Calcula los límites de fecha (inicio y fin) según el período seleccionado.
     */
    public function getRangoFechas(): array
    {
        return match ($this->periodo) {
            'diario' => [
                Carbon::parse($this->fecha ?: now())->startOfDay(),
                Carbon::parse($this->fecha ?: now())->endOfDay(),
                'Reporte Diario: ' . Carbon::parse($this->fecha ?: now())->translatedFormat('d \d\e F \d\e Y'),
            ],
            'semanal' => [
                Carbon::parse($this->fecha ?: now())->startOfWeek(),
                Carbon::parse($this->fecha ?: now())->endOfWeek(),
                'Reporte Semanal: Semana del ' . Carbon::parse($this->fecha ?: now())->startOfWeek()->format('d/m/Y') . ' al ' . Carbon::parse($this->fecha ?: now())->endOfWeek()->format('d/m/Y'),
            ],
            'mensual' => [
                Carbon::parse(($this->mes ?: now()->format('Y-m')) . '-01')->startOfMonth(),
                Carbon::parse(($this->mes ?: now()->format('Y-m')) . '-01')->endOfMonth(),
                'Reporte Mensual: ' . Carbon::parse(($this->mes ?: now()->format('Y-m')) . '-01')->translatedFormat('F \d\e Y'),
            ],
            'anual' => [
                Carbon::createFromDate($this->ano ?: now()->year, 1, 1)->startOfYear(),
                Carbon::createFromDate($this->ano ?: now()->year, 12, 31)->endOfYear(),
                'Balance del Año ' . ($this->ano ?: now()->year),
            ],
            'personalizado' => [
                Carbon::parse($this->fechaInicio ?: now()->startOfMonth())->startOfDay(),
                Carbon::parse($this->fechaFin ?: now())->endOfDay(),
                'Reporte Personalizado: ' . Carbon::parse($this->fechaInicio)->format('d/m/Y') . ' al ' . Carbon::parse($this->fechaFin)->format('d/m/Y'),
            ],
            default => [
                now()->startOfMonth(),
                now()->endOfMonth(),
                'Reporte Mensual: ' . now()->translatedFormat('F \d\e Y'),
            ],
        };
    }

    /**
     * Genera los datos consolidados del reporte para el rango actual.
     */
    public function getReporteData(): array
    {
        [$inicio, $fin, $etiquetaPeriodo] = $this->getRangoFechas();

        // ─── 1. Obras y Proyectos en el período ───
        $proyectosQuery = Proyecto::query()->whereBetween('created_at', [$inicio, $fin]);
        if ($this->clienteId) {
            $proyectosQuery->where('cliente_id', $this->clienteId);
        }
        $proyectosTotal = (float) $proyectosQuery->sum('presupuesto_total');
        $proyectosCount = $proyectosQuery->count();
        $proyectosList = $proyectosQuery->with('cliente')->latest()->take(8)->get();

        // ─── 2. Solicitudes Comerciales Aprobadas ───
        $solicitudesQuery = SolicitudServicio::query()
            ->whereBetween('created_at', [$inicio, $fin]);
        if ($this->clienteId) {
            $solicitudesQuery->where('cliente_id', $this->clienteId);
        }
        $solicitudesMonto = (float) (clone $solicitudesQuery)
            ->whereIn('estado', ['Aprobada', 'Convertida_Proyecto'])
            ->sum('presupuesto_estimado');
        $solicitudesCount = $solicitudesQuery->count();

        // ─── 3. Incidencias & Asistencias Técnicas ───
        $incidenciasQuery = Incidencia::query()
            ->where(function ($q) use ($inicio, $fin) {
                $q->whereBetween('fecha_resolucion', [$inicio, $fin])
                  ->orWhereBetween('created_at', [$inicio, $fin]);
            });

        if ($this->tecnicoId) {
            $incidenciasQuery->where('tecnico_id', $this->tecnicoId);
        }
        if ($this->clienteId) {
            $incidenciasQuery->where('cliente_id', $this->clienteId);
        }
        if ($this->tipoServicio) {
            $incidenciasQuery->where('tipo', $this->tipoServicio);
        }

        $incidenciasTotalCount = (clone $incidenciasQuery)->count();
        $incidenciasResueltas = (clone $incidenciasQuery)->whereNotNull('fecha_resolucion')->count();
        
        // Cumplimiento SLA
        $resueltasConSla = (clone $incidenciasQuery)
            ->whereNotNull('fecha_resolucion')
            ->whereNotNull('fecha_limite')
            ->get();
        $cumplieronSla = $resueltasConSla->filter(fn ($i) => $i->fecha_resolucion->lte($i->fecha_limite))->count();
        $excedieronSla = $resueltasConSla->count() - $cumplieronSla;
        $tasaSla = $resueltasConSla->count() > 0 ? round(($cumplieronSla / $resueltasConSla->count()) * 100) : 100;

        // Facturación de incidencias / asistencias
        $facturacionIncidencias = (float) (clone $incidenciasQuery)->sum('monto_facturado');
        if ($facturacionIncidencias <= 0) {
            $facturacionIncidencias = (float) (clone $incidenciasQuery)->sum('costo_estimado');
        }

        // ─── 4. Gastos Operativos de Especialistas (Transporte y Almuerzo) ───
        $gastosTransporte = (float) (clone $incidenciasQuery)->sum('gasto_transporte');
        $gastosAlmuerzo = (float) (clone $incidenciasQuery)->sum('gasto_almuerzo');
        $gastosRepuestos = (float) (clone $incidenciasQuery)->sum('costo_estimado');
        $gastosCampoTotal = $gastosTransporte + $gastosAlmuerzo;

        // Desglose de gastos por especialista/técnico
        $gastosPorEspecialista = (clone $incidenciasQuery)
            ->whereNotNull('tecnico_id')
            ->with('tecnico')
            ->select('tecnico_id',
                DB::raw('SUM(gasto_transporte) as total_transporte'),
                DB::raw('SUM(gasto_almuerzo) as total_almuerzo'),
                DB::raw('SUM(COALESCE(gasto_transporte, 0) + COALESCE(gasto_almuerzo, 0)) as total_campo'),
                DB::raw('COUNT(id) as total_incidencias')
            )
            ->groupBy('tecnico_id')
            ->orderByDesc('total_campo')
            ->get();

        // ─── 5. Movimientos de Almacén en el período ───
        $movimientosQuery = InventarioMovimiento::query()->whereBetween('created_at', [$inicio, $fin]);
        $entradasCount = (clone $movimientosQuery)->whereIn('tipo', ['entrada', 'devolucion'])->count();
        $salidasCount = (clone $movimientosQuery)->where('tipo', 'salida')->count();
        $costoSalidas = (float) (clone $movimientosQuery)
            ->where('tipo', 'salida')
            ->select(DB::raw('SUM(cantidad * COALESCE(costo_unitario, 0)) as total'))
            ->value('total');

        // ─── 6. Recargas Gestión Remota 4G en el período ───
        $recargas4g = (float) Servicio::query()
            ->whereBetween('gr_ultima_recarga', [$inicio, $fin])
            ->sum('gr_recarga_monto');

        // ─── 7. Consolidación Financiera Total del Período ───
        $dineroGeneradoTotal = $proyectosTotal + $solicitudesMonto + $facturacionIncidencias + $recargas4g;
        $margenOperativoNeto = $dineroGeneradoTotal - $gastosCampoTotal;

        // ─── 8. Lista de Incidencias detalladas del período ───
        $incidenciasDetalladas = (clone $incidenciasQuery)
            ->with(['cliente', 'tecnico', 'brigada'])
            ->latest('fecha_reporte')
            ->take(15)
            ->get();

        // ─── 9. Datos específicos para Balance Anual (12 Meses) ───
        $balanceMeses = [];
        if ($this->periodo === 'anual') {
            for ($m = 1; $m <= 12; $m++) {
                $mInicio = Carbon::createFromDate($this->ano, $m, 1)->startOfMonth();
                $mFin = Carbon::createFromDate($this->ano, $m, 1)->endOfMonth();

                $mProy = (float) Proyecto::whereBetween('created_at', [$mInicio, $mFin])->sum('presupuesto_total');
                $mIncFact = (float) Incidencia::whereBetween('fecha_resolucion', [$mInicio, $mFin])->sum('monto_facturado');
                if ($mIncFact <= 0) {
                    $mIncFact = (float) Incidencia::whereBetween('fecha_resolucion', [$mInicio, $mFin])->sum('costo_estimado');
                }
                $mTrans = (float) Incidencia::whereBetween('fecha_resolucion', [$mInicio, $mFin])->sum('gasto_transporte');
                $mAlm = (float) Incidencia::whereBetween('fecha_resolucion', [$mInicio, $mFin])->sum('gasto_almuerzo');
                $mGastos = $mTrans + $mAlm;
                $mTotalIngreso = $mProy + $mIncFact;
                $mNeto = $mTotalIngreso - $mGastos;
                $mIncCount = Incidencia::whereBetween('created_at', [$mInicio, $mFin])->count();

                $balanceMeses[] = [
                    'mes_numero'   => $m,
                    'mes_nombre'   => $mInicio->translatedFormat('F'),
                    'proyectos'    => $mProy,
                    'servicios'    => $mIncFact,
                    'ingresos'     => $mTotalIngreso,
                    'transporte'   => $mTrans,
                    'almuerzo'     => $mAlm,
                    'gastos_campo' => $mGastos,
                    'margen_neto'  => $mNeto,
                    'incidencias'  => $mIncCount,
                ];
            }
        }

        return [
            'inicio'                  => $inicio,
            'fin'                     => $fin,
            'etiqueta_periodo'        => $etiquetaPeriodo,
            'dinero_generado_total'   => $dineroGeneradoTotal,
            'margen_operativo_neto'   => $margenOperativoNeto,
            'proyectos_total'         => $proyectosTotal,
            'proyectos_count'         => $proyectosCount,
            'proyectos_list'          => $proyectosList,
            'solicitudes_monto'       => $solicitudesMonto,
            'solicitudes_count'       => $solicitudesCount,
            'facturacion_incidencias' => $facturacionIncidencias,
            'gastos_transporte'       => $gastosTransporte,
            'gasto_transporte_total'  => $gastosTransporte,
            'gastos_almuerzo'         => $gastosAlmuerzo,
            'gasto_almuerzo_total'    => $gastosAlmuerzo,
            'gastos_campo_total'      => $gastosCampoTotal,
            'margen_neto_total'       => $margenOperativoNeto,
            'gastos_repuestos'        => $gastosRepuestos,
            'recargas_4g'             => $recargas4g,
            'incidencias_total'       => $incidenciasTotalCount,
            'incidencias_resueltas'   => $incidenciasResueltas,
            'cumplieron_sla'          => $cumplieronSla,
            'excedieron_sla'          => $excedieronSla,
            'tasa_sla'                => $tasaSla,
            'gastos_por_especialista' => $gastosPorEspecialista,
            'entradas_almacen'        => $entradasCount,
            'salidas_almacen'         => $salidasCount,
            'costo_salidas'           => $costoSalidas,
            'incidencias_detalladas'  => $incidenciasDetalladas,
            'balance_meses'           => $balanceMeses,
        ];
    }

    /**
     * Lista de técnicos para el select de filtro.
     */
    public function getTecnicosProperty()
    {
        return User::where('activo', true)
            ->orderBy('name')
            ->pluck('name', 'id');
    }

    /**
     * Lista de clientes para el select de filtro.
     */
    public function getClientesProperty()
    {
        return Cliente::where('activo', true)
            ->orderBy('nombre')
            ->pluck('nombre', 'id');
    }
}
