<?php

namespace App\Filament\Pages;

use App\Models\Brigada;
use App\Models\Cliente;
use App\Models\Item;
use App\Models\LineaPresupuesto;
use App\Models\Proyecto;
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
     * Datos globales calculados una sola vez y cacheados 5 minutos.
     * Evita decenas de queries repetidas en el blade (más rápido y eficiente).
     */
    public function getData(): array
    {
        return Cache::remember('dashboard:stats:v2', now()->addMinutes(5), function () {
            return [
                'proyectos_activos' => Proyecto::whereIn('estado', ['borrador', 'en_progreso'])->count(),
                'proyectos_mes' => Proyecto::where('estado', 'completado')->whereMonth('created_at', now()->month)->count(),
                'retrasados' => Proyecto::where('estado', 'en_progreso')->where('fecha_fin', '<', now())->count(),
                'presupuesto_total' => (float) Proyecto::sum('presupuesto_total'),
                'brigadas' => Brigada::where('activa', true)->count(),
                'investigacion' => Proyecto::where('tipo_seguimiento', 'investigacion')->whereIn('estado', ['borrador', 'en_progreso'])->count(),
                'instalaciones' => Proyecto::where('tipo_seguimiento', 'instalacion')->whereIn('estado', ['borrador', 'en_progreso'])->count(),
                'items' => Item::count(),
                'stock_bajo' => Item::whereColumn('stock_actual', '<=', 'stock_minimo')->count(),
                'equipamiento_stock' => (int) Item::where('es_equipamiento', true)->sum('stock_actual'),
                'materiales_stock' => (int) Item::where('es_equipamiento', false)->sum('stock_actual'),
                'finanzas_mes' => (float) Proyecto::whereMonth('created_at', now()->month)->sum('presupuesto_total'),
                'presupuesto_equipamiento' => (float) LineaPresupuesto::where('tipo_linea', 'equipamiento')->sum('subtotal'),
                'presupuesto_mano_obra' => (float) LineaPresupuesto::where('tipo_linea', 'mano_obra')->sum('subtotal'),
                'usuarios' => User::where('activo', true)->count(),
                'clientes' => Cliente::count(),
                'clientes_activos' => Cliente::where('activo', true)->count(),
                'recientes' => Proyecto::latest()->take(6)->get(),
                'alertas_stock' => Item::whereColumn('stock_actual', '<=', 'stock_minimo')->orderBy('stock_actual')->take(10)->get(),
                'top_proyectos' => Proyecto::orderByDesc('presupuesto_total')->take(5)->get(),
                'max_presupuesto' => (float) (Proyecto::max('presupuesto_total') ?: 1),
            ];
        });
    }
}