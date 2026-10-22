import React, { useState } from 'react';
import { 
  Briefcase, 
  Cpu, 
  Archive, 
  BanknotesIcon, 
  ShieldCheck, 
  TrendingUp, 
  TrendingDown, 
  AlertTriangle, 
  AlertCircle, 
  CheckCircle2, 
  Clock, 
  Radio, 
  Camera, 
  Signal, 
  Boxes, 
  Users, 
  Building2, 
  ArrowUpRight, 
  DollarSign, 
  Wrench, 
  Lightbulb, 
  ChevronRight 
} from 'lucide-react';
import { 
  Proyecto, 
  Incidencia, 
  ItemInventario, 
  Almacen, 
  Brigada, 
  Cliente, 
  Servicio, 
  Contrato, 
  Venta, 
  Gasto, 
  UserAccount,
  SuscripcionServicio,
  RegistroPagoServicio
} from '../types';

interface DashboardViewProps {
  proyectos: Proyecto[];
  incidencias: Incidencia[];
  items: ItemInventario[];
  almacenes: Almacen[];
  brigadas: Brigada[];
  clientes: Cliente[];
  servicios?: Servicio[];
  contratos?: Contrato[];
  ventas?: Venta[];
  gastos?: Gasto[];
  usuarios?: UserAccount[];
  suscripciones?: SuscripcionServicio[];
  pagosServicio?: RegistroPagoServicio[];
  onNavigate: (module: string) => void;
  onQuickRestock: (item: ItemInventario) => void;
}

export const DashboardView: React.FC<DashboardViewProps> = ({
  proyectos,
  incidencias,
  items,
  almacenes,
  brigadas,
  clientes,
  servicios = [],
  contratos = [],
  ventas = [],
  gastos = [],
  usuarios = [],
  suscripciones = [],
  pagosServicio = [],
  onNavigate,
  onQuickRestock,
}) => {
  const [activeTab, setActiveTab] = useState<'proyectos' | 'operaciones' | 'inventario' | 'finanzas' | 'administracion'>('proyectos');

  // Greeting logic matching Filament Dashboard.php
  const hour = new Date().getHours();
  const greeting = hour < 12 ? 'Buenos días' : hour < 19 ? 'Buenas tardes' : 'Buenas noches';

  // Calculations matching Filament Dashboard.php
  const proyectosActivos = proyectos.filter(p => p.estado === 'borrador' || p.estado === 'en_progreso');
  const proyectosCompletados = proyectos.filter(p => p.estado === 'completado');
  const retrasados = proyectos.filter(p => p.estado === 'en_progreso' && p.fecha_fin && new Date(p.fecha_fin) < new Date());
  const presupuestoTotal = proyectos.reduce((acc, p) => acc + (p.presupuesto_total || 0), 0);

  const stockBajoItems = items.filter(i => i.stock_actual <= i.stock_minimo);
  const equipamientoStockTotal = items.filter(i => i.es_equipamiento).reduce((acc, i) => acc + i.stock_actual, 0);
  const materialesStockTotal = items.filter(i => !i.es_equipamiento).reduce((acc, i) => acc + i.stock_actual, 0);

  const totalVentas = ventas.reduce((acc, v) => acc + (v.estado === 'cobrado' ? v.monto_total : 0), 0);
  const totalGastos = gastos.reduce((acc, g) => acc + g.monto, 0);

  const serviciosCCTV = servicios.filter(s => s.tipo === 'CCTV').length;
  const serviciosSACI = servicios.filter(s => s.tipo === 'SACI').length;
  const serviciosGR = servicios.filter(s => s.tipo === 'Gestion_Remota').length;

  const maxPresupuesto = Math.max(...proyectos.map(p => p.presupuesto_total), 1);
  const topProyectos = [...proyectos].sort((a, b) => b.presupuesto_total - a.presupuesto_total).slice(0, 5);

  const tabs = [
    { key: 'proyectos', label: 'Proyectos', icon: Briefcase },
    { key: 'operaciones', label: 'Operaciones', icon: Cpu },
    { key: 'inventario', label: 'Inventario', icon: Archive },
    { key: 'finanzas', label: 'Finanzas', icon: DollarSign },
    { key: 'administracion', label: 'Administración', icon: ShieldCheck },
  ] as const;

  return (
    <div className="space-y-6 select-none font-sans text-slate-100">
      {/* ================= HERO / ENCABEZADO OFICIAL FILAMENT ================= */}
      <div className="relative overflow-hidden rounded-2xl bg-gradient-to-r from-sky-500 via-sky-600 to-sky-800 p-6 sm:p-8 text-white shadow-xl shadow-sky-950/20">
        <div className="relative z-10 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <p className="text-xs font-semibold uppercase tracking-wider text-sky-100/80">
              {greeting},
            </p>
            <h1 className="mt-1 text-2xl sm:text-3xl font-extrabold tracking-tight text-white">
              Enmanuel Caraballo
            </h1>
            <p className="mt-2 text-xs sm:text-sm text-sky-100/80 font-medium">
              DataPlus Platform · {proyectosActivos.length} proyectos activos · {stockBajoItems.length > 0 ? `${stockBajoItems.length} alertas de stock bajo` : 'sin alertas de stock'} · {servicios.length} servicios en campo
            </p>
          </div>

          <div className="flex items-center gap-3">
            {retrasados.length > 0 && (
              <div className="rounded-xl bg-white/10 px-4 py-2.5 ring-1 ring-white/20 backdrop-blur-sm">
                <p className="text-[10px] uppercase font-bold tracking-wider text-amber-200">Con retraso</p>
                <p className="text-xl font-extrabold text-amber-300">{retrasados.length}</p>
              </div>
            )}
            <div className="rounded-xl bg-white/10 px-4 py-2.5 ring-1 ring-white/20 backdrop-blur-sm">
              <p className="text-[10px] uppercase font-bold tracking-wider text-sky-100">Presupuesto total</p>
              <p className="text-xl font-extrabold text-white">${presupuestoTotal.toLocaleString()} CUP</p>
            </div>
          </div>
        </div>

        {/* Ambient glow decoration */}
        <div className="absolute -right-12 -bottom-12 h-48 w-48 rounded-full bg-white/10 blur-2xl pointer-events-none" />
      </div>

      {/* ================= TABS DE SECCIONES DEL DASHBOARD ================= */}
      <div className="rounded-xl border border-slate-800 bg-slate-900/80 p-1.5 shadow-sm">
        <nav className="flex snap-x gap-1.5 overflow-x-auto" role="tablist">
          {tabs.map((tab) => {
            const Icon = tab.icon;
            const isSelected = activeTab === tab.key;
            return (
              <button
                key={tab.key}
                type="button"
                onClick={() => setActiveTab(tab.key)}
                className={`flex items-center gap-2 whitespace-nowrap rounded-lg px-4 py-2 text-xs font-bold transition-all cursor-pointer ${
                  isSelected
                    ? 'bg-gradient-to-r from-sky-500 to-sky-600 text-white shadow-md shadow-sky-500/30'
                    : 'text-slate-400 hover:bg-slate-800/80 hover:text-slate-200'
                }`}
              >
                <Icon className="h-4 w-4" />
                <span>{tab.label}</span>
              </button>
            );
          })}
        </nav>
      </div>

      {/* ================= CONTENIDO DE CADA TAB ================= */}

      {/* ---------- TAB: PROYECTOS ---------- */}
      {activeTab === 'proyectos' && (
        <div className="space-y-6">
          <div className="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <div className="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
              <div className="flex items-center justify-between">
                <span className="text-xs font-semibold text-slate-400">Proyectos Activos</span>
                <div className="rounded-lg bg-sky-500/10 p-2 text-sky-400 border border-sky-500/20">
                  <Briefcase className="h-4 w-4" />
                </div>
              </div>
              <p className="text-2xl font-bold text-white mt-2">{proyectosActivos.length}</p>
              <p className="text-[11px] text-slate-400 mt-1">En borrador o ejecución activa</p>
            </div>

            <div className="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
              <div className="flex items-center justify-between">
                <span className="text-xs font-semibold text-slate-400">Completados este Mes</span>
                <div className="rounded-lg bg-emerald-500/10 p-2 text-emerald-400 border border-emerald-500/20">
                  <CheckCircle2 className="h-4 w-4" />
                </div>
              </div>
              <p className="text-2xl font-bold text-emerald-400 mt-2">{proyectosCompletados.length}</p>
              <p className="text-[11px] text-slate-400 mt-1">Cierre satisfactorio</p>
            </div>

            <div className="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
              <div className="flex items-center justify-between">
                <span className="text-xs font-semibold text-slate-400">Con Retraso</span>
                <div className="rounded-lg bg-rose-500/10 p-2 text-rose-400 border border-rose-500/20">
                  <AlertTriangle className="h-4 w-4" />
                </div>
              </div>
              <p className="text-2xl font-bold text-rose-400 mt-2">{retrasados.length}</p>
              <p className="text-[11px] text-slate-400 mt-1">Pasaron su fecha comprometida</p>
            </div>

            <div className="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
              <div className="flex items-center justify-between">
                <span className="text-xs font-semibold text-slate-400">Presupuesto Total</span>
                <div className="rounded-lg bg-amber-500/10 p-2 text-amber-400 border border-amber-500/20">
                  <DollarSign className="h-4 w-4" />
                </div>
              </div>
              <p className="text-2xl font-bold text-amber-400 mt-2">${presupuestoTotal.toLocaleString()} CUP</p>
              <p className="text-[11px] text-slate-400 mt-1">Suma de todas las obras y proyectos</p>
            </div>
          </div>

          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {/* Top 5 Proyectos por Presupuesto */}
            <div className="rounded-2xl border border-slate-800 bg-slate-900/70 p-6 shadow-sm">
              <div className="flex items-center justify-between mb-4">
                <h3 className="text-sm font-bold text-white flex items-center gap-2">
                  <Briefcase className="h-4 w-4 text-sky-400" />
                  Top 5 Proyectos por Presupuesto
                </h3>
                <button
                  onClick={() => onNavigate('proyectos')}
                  className="text-xs text-sky-400 hover:text-sky-300 font-semibold cursor-pointer"
                >
                  Ver todos →
                </button>
              </div>

              <div className="space-y-4">
                {topProyectos.length === 0 ? (
                  <div className="py-8 text-center text-xs text-slate-500">
                    No hay proyectos registrados aún en la plataforma.
                  </div>
                ) : (
                  topProyectos.map((p, idx) => {
                    const pct = Math.round((p.presupuesto_total / maxPresupuesto) * 100);
                    return (
                      <div key={p.id} className="space-y-1.5">
                        <div className="flex items-center justify-between text-xs">
                          <span className="font-semibold text-white flex items-center gap-2 truncate max-w-xs">
                            <span className="flex h-5 w-5 items-center justify-center rounded bg-sky-500/20 text-[10px] font-bold text-sky-300">
                              {idx + 1}
                            </span>
                            <span className="truncate">{p.nombre}</span>
                          </span>
                          <span className="font-mono font-bold text-sky-400">
                            ${p.presupuesto_total.toLocaleString()} CUP
                          </span>
                        </div>
                        <div className="h-2 w-full rounded-full bg-slate-800 overflow-hidden">
                          <div
                            className="h-full rounded-full bg-gradient-to-r from-sky-400 to-blue-600 transition-all duration-700"
                            style={{ width: `${pct}%` }}
                          />
                        </div>
                      </div>
                    );
                  })
                )}
              </div>
            </div>

            {/* Proyectos Recientes */}
            <div className="rounded-2xl border border-slate-800 bg-slate-900/70 p-6 shadow-sm">
              <div className="flex items-center justify-between mb-4">
                <h3 className="text-sm font-bold text-white flex items-center gap-2">
                  <Clock className="h-4 w-4 text-sky-400" />
                  Proyectos Recientes
                </h3>
                <span className="rounded-full bg-sky-500/10 px-2 py-0.5 text-[10px] font-bold text-sky-400 border border-sky-500/20">
                  en seguimiento
                </span>
              </div>

              <div className="space-y-2.5">
                {proyectos.length === 0 ? (
                  <div className="py-8 text-center text-xs text-slate-500">
                    No hay proyectos en curso.
                  </div>
                ) : (
                  proyectos.slice(0, 5).map((p) => (
                    <div key={p.id} className="flex items-center justify-between p-2.5 rounded-xl bg-slate-950/60 border border-slate-800/80 hover:border-sky-500/30 transition-colors">
                      <div className="min-w-0 flex-1 pr-3">
                        <p className="font-semibold text-xs text-white truncate">{p.nombre}</p>
                        <p className="text-[10px] text-slate-400 flex items-center gap-2 mt-0.5">
                          <span className="font-mono">{p.codigo}</span>
                          <span>·</span>
                          <span>{p.cliente_nombre || 'Sin cliente'}</span>
                        </p>
                      </div>
                      <span className={`px-2 py-0.5 rounded text-[10px] font-bold uppercase ${
                        p.estado === 'completado' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' :
                        p.estado === 'en_progreso' ? 'bg-sky-500/10 text-sky-400 border border-sky-500/20' :
                        'bg-slate-800 text-slate-400'
                      }`}>
                        {p.estado.replace('_', ' ')}
                      </span>
                    </div>
                  ))
                )}
              </div>
            </div>
          </div>
        </div>
      )}

      {/* ---------- TAB: OPERACIONES ---------- */}
      {activeTab === 'operaciones' && (
        <div className="space-y-6">
          <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div className="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
              <div className="flex items-center justify-between">
                <span className="text-xs font-semibold text-slate-400">Brigadas Activas</span>
                <div className="rounded-lg bg-purple-500/10 p-2 text-purple-400 border border-purple-500/20">
                  <Users className="h-4 w-4" />
                </div>
              </div>
              <p className="text-2xl font-bold text-purple-400 mt-2">{brigadas.length}</p>
              <p className="text-[11px] text-slate-400 mt-1">Cuadrillas disponibles en terreno</p>
            </div>

            <div className="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
              <div className="flex items-center justify-between">
                <span className="text-xs font-semibold text-slate-400">Servicios Instalados</span>
                <div className="rounded-lg bg-sky-500/10 p-2 text-sky-400 border border-sky-500/20">
                  <Radio className="h-4 w-4" />
                </div>
              </div>
              <p className="text-2xl font-bold text-sky-400 mt-2">{servicios.length}</p>
              <p className="text-[11px] text-slate-400 mt-1">CCTV, SACI y Gestión Remota 4G</p>
            </div>

            <div className="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
              <div className="flex items-center justify-between">
                <span className="text-xs font-semibold text-slate-400">Incidencias Abiertas</span>
                <div className="rounded-lg bg-rose-500/10 p-2 text-rose-400 border border-rose-500/20">
                  <AlertCircle className="h-4 w-4" />
                </div>
              </div>
              <p className="text-2xl font-bold text-rose-400 mt-2">
                {incidencias.filter(i => i.estado !== 'Resuelta' && i.estado !== 'Cerrada').length}
              </p>
              <p className="text-[11px] text-slate-400 mt-1">Tickets técnicos pendientes</p>
            </div>
          </div>

          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {/* Desglose de Servicios Técnicos */}
            <div className="rounded-2xl border border-slate-800 bg-slate-900/70 p-6 shadow-sm">
              <h3 className="text-sm font-bold text-white mb-4 flex items-center gap-2">
                <Radio className="h-4 w-4 text-sky-400" />
                Parque de Servicios Técnicos
              </h3>

              <div className="grid grid-cols-3 gap-3 mb-4">
                <div className="rounded-xl border border-slate-800 bg-slate-950 p-3 text-center">
                  <Camera className="h-4 w-4 text-blue-400 mx-auto mb-1" />
                  <p className="text-xl font-extrabold text-white">{serviciosCCTV}</p>
                  <p className="text-[10px] text-slate-400 uppercase font-semibold">CCTV</p>
                </div>
                <div className="rounded-xl border border-slate-800 bg-slate-950 p-3 text-center">
                  <ShieldCheck className="h-4 w-4 text-emerald-400 mx-auto mb-1" />
                  <p className="text-xl font-extrabold text-white">{serviciosSACI}</p>
                  <p className="text-[10px] text-slate-400 uppercase font-semibold">Alarmas SACI</p>
                </div>
                <div className="rounded-xl border border-slate-800 bg-slate-950 p-3 text-center">
                  <Signal className="h-4 w-4 text-amber-400 mx-auto mb-1" />
                  <p className="text-xl font-extrabold text-white">{serviciosGR}</p>
                  <p className="text-[10px] text-slate-400 uppercase font-semibold">Gestión 4G</p>
                </div>
              </div>

              <div className="space-y-2">
                {servicios.length === 0 ? (
                  <div className="py-8 text-center text-xs text-slate-500">
                    No hay servicios técnicos instalados en campo.
                  </div>
                ) : (
                  servicios.slice(0, 4).map((s) => (
                    <div key={s.id} className="flex items-center justify-between p-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-xs">
                      <div>
                        <p className="font-semibold text-white">{s.cliente_nombre}</p>
                        <p className="text-[10px] text-slate-400">
                          {s.tipo} · {s.gr_marca_modelo || s.notas || 'En servicio'}
                        </p>
                      </div>
                      <span className="font-mono text-[10px] text-sky-400 bg-sky-950/40 px-2 py-0.5 rounded border border-sky-500/20">
                        {s.codigo}
                      </span>
                    </div>
                  ))
                )}
              </div>
            </div>

            {/* Incidencias Prioritarias */}
            <div className="rounded-2xl border border-slate-800 bg-slate-900/70 p-6 shadow-sm">
              <div className="flex items-center justify-between mb-4">
                <h3 className="text-sm font-bold text-white flex items-center gap-2">
                  <AlertCircle className="h-4 w-4 text-rose-400" />
                  Incidencias Técnicas Recientes
                </h3>
                <button
                  onClick={() => onNavigate('incidencias')}
                  className="text-xs text-sky-400 hover:text-sky-300 font-semibold cursor-pointer"
                >
                  Ver tickets →
                </button>
              </div>

              <div className="space-y-2.5">
                {incidencias.length === 0 ? (
                  <div className="py-8 text-center text-xs text-slate-500 flex flex-col items-center justify-center">
                    <CheckCircle2 className="h-6 w-6 text-emerald-400 mb-1" />
                    <span>Sin incidencias reportadas. Todo en orden.</span>
                  </div>
                ) : (
                  incidencias.slice(0, 4).map((i) => (
                    <div key={i.id} className="p-3 rounded-xl bg-slate-950/60 border border-slate-800 space-y-1">
                      <div className="flex items-center justify-between text-xs">
                        <span className="font-semibold text-white truncate max-w-[200px]">{i.titulo}</span>
                        <span className={`px-1.5 py-0.5 rounded text-[10px] font-bold ${
                          i.prioridad === 'Critica' ? 'bg-rose-500/20 text-rose-300' :
                          i.prioridad === 'Alta' ? 'bg-amber-500/20 text-amber-300' :
                          'bg-slate-800 text-slate-300'
                        }`}>
                          {i.prioridad}
                        </span>
                      </div>
                      <p className="text-[11px] text-slate-400">
                        Cliente: <span className="text-slate-200">{i.cliente_nombre}</span> · Tipo: {i.tipo}
                      </p>
                    </div>
                  ))
                )}
              </div>
            </div>
          </div>
        </div>
      )}

      {/* ---------- TAB: INVENTARIO ---------- */}
      {activeTab === 'inventario' && (
        <div className="space-y-6">
          <div className="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <div className="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
              <div className="flex items-center justify-between">
                <span className="text-xs font-semibold text-slate-400">Total Items</span>
                <div className="rounded-lg bg-sky-500/10 p-2 text-sky-400 border border-sky-500/20">
                  <Boxes className="h-4 w-4" />
                </div>
              </div>
              <p className="text-2xl font-bold text-white mt-2">{items.length}</p>
              <p className="text-[11px] text-slate-400 mt-1">En catálogo de inventario</p>
            </div>

            <div className="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
              <div className="flex items-center justify-between">
                <span className="text-xs font-semibold text-slate-400">Stock Bajo</span>
                <div className="rounded-lg bg-rose-500/10 p-2 text-rose-400 border border-rose-500/20">
                  <AlertTriangle className="h-4 w-4" />
                </div>
              </div>
              <p className="text-2xl font-bold text-rose-400 mt-2">{stockBajoItems.length}</p>
              <p className="text-[11px] text-slate-400 mt-1">Requieren reposición inmediata</p>
            </div>

            <div className="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
              <div className="flex items-center justify-between">
                <span className="text-xs font-semibold text-slate-400">Equipamiento</span>
                <div className="rounded-lg bg-purple-500/10 p-2 text-purple-400 border border-purple-500/20">
                  <Boxes className="h-4 w-4" />
                </div>
              </div>
              <p className="text-2xl font-bold text-purple-400 mt-2">{equipamientoStockTotal}</p>
              <p className="text-[11px] text-slate-400 mt-1">Unidades en almacén</p>
            </div>

            <div className="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
              <div className="flex items-center justify-between">
                <span className="text-xs font-semibold text-slate-400">Materiales & Consumibles</span>
                <div className="rounded-lg bg-emerald-500/10 p-2 text-emerald-400 border border-emerald-500/20">
                  <Boxes className="h-4 w-4" />
                </div>
              </div>
              <p className="text-2xl font-bold text-emerald-400 mt-2">{materialesStockTotal}</p>
              <p className="text-[11px] text-slate-400 mt-1">Unidades en stock</p>
            </div>
          </div>

          {/* Panel Alertas de Stock Bajo */}
          <div className="rounded-2xl border border-slate-800 bg-slate-900/70 p-6 shadow-sm">
            <div className="flex items-center justify-between mb-4">
              <div className="flex items-center gap-2">
                <AlertTriangle className="h-5 w-5 text-rose-500" />
                <h3 className="text-sm font-bold text-white">Alertas de Stock Bajo</h3>
              </div>
              <span className="rounded-full bg-rose-500/10 px-2.5 py-0.5 text-xs font-bold text-rose-400 border border-rose-500/20">
                {stockBajoItems.length} ítems en umbral crítico
              </span>
            </div>

            <div className="space-y-3">
              {stockBajoItems.map((item) => (
                <div key={item.id} className="flex flex-col sm:flex-row sm:items-center justify-between p-3 rounded-xl border border-rose-500/30 bg-rose-950/20 gap-3">
                  <div>
                    <p className="font-semibold text-xs text-white">{item.nombre}</p>
                    <p className="text-[11px] text-slate-400 font-mono mt-0.5">
                      Código: {item.codigo} · Almacén: {item.almacen_nombre}
                    </p>
                  </div>
                  <div className="flex items-center gap-3">
                    <div className="text-right">
                      <p className="text-xs font-bold text-rose-400">
                        {item.stock_actual} {item.unidad_medida} en stock
                      </p>
                      <p className="text-[10px] text-slate-400">Mínimo: {item.stock_minimo}</p>
                    </div>
                    <button
                      onClick={() => onQuickRestock(item)}
                      className="rounded-lg bg-rose-500 hover:bg-rose-400 px-3 py-1.5 text-xs font-bold text-slate-950 transition-colors cursor-pointer"
                    >
                      Reponer
                    </button>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>
      )}

      {/* ---------- TAB: FINANZAS ---------- */}
      {activeTab === 'finanzas' && (
        <div className="space-y-6">
          <div className="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <div className="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
              <span className="text-xs font-semibold text-slate-400">Presupuesto en Obras</span>
              <p className="text-2xl font-bold text-white mt-2">${presupuestoTotal.toLocaleString()} CUP</p>
              <p className="text-[11px] text-slate-400 mt-1">Cartera de proyectos</p>
            </div>

            <div className="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
              <span className="text-xs font-semibold text-slate-400">Ventas Cobradas</span>
              <p className="text-2xl font-bold text-emerald-400 mt-2">${totalVentas.toLocaleString()} CUP</p>
              <p className="text-[11px] text-slate-400 mt-1">Ingresos de obras ejecutadas</p>
            </div>

            <div className="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
              <span className="text-xs font-semibold text-slate-400">Gastos Operativos</span>
              <p className="text-2xl font-bold text-rose-400 mt-2">${totalGastos.toLocaleString()} CUP</p>
              <p className="text-[11px] text-slate-400 mt-1">Egresos y logística</p>
            </div>

            <div className="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
              <span className="text-xs font-semibold text-slate-400">Margen Operativo</span>
              <p className="text-2xl font-bold text-sky-400 mt-2">${(totalVentas - totalGastos).toLocaleString()} CUP</p>
              <p className="text-[11px] text-slate-400 mt-1">Balance neto</p>
            </div>
          </div>

          {/* ================= SECCIÓN GESTIÓN REMOTA EN DASHBOARD ================= */}
          <div className="rounded-2xl border border-slate-800 bg-gradient-to-br from-slate-900/90 to-slate-950 p-6 shadow-sm space-y-5">
            <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 border-b border-slate-800/80 pb-4">
              <div className="flex items-center gap-2.5">
                <div className="p-2 rounded-xl bg-gradient-to-tr from-sky-500 to-indigo-600 text-white shadow-md shadow-sky-500/20">
                  <Radio className="h-5 w-5" />
                </div>
                <div>
                  <h3 className="text-sm font-bold text-white flex items-center gap-2">
                    Facturación de Gestión Remota · Mes Adelantado
                    <span className="text-[10px] font-mono font-bold px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                      Plazo: Días 1 al 15
                    </span>
                  </h3>
                  <p className="text-xs text-slate-400">
                    Control de mensualidades recurrentes, comprobantes por transferencia y cobranza activa.
                  </p>
                </div>
              </div>

              <button
                onClick={() => onNavigate('finanzas')}
                className="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-sky-500 hover:bg-sky-400 text-slate-950 font-bold text-xs transition-colors cursor-pointer"
              >
                Abrir Facturación de Servicios →
              </button>
            </div>

            {/* Micro KPIs de Gestión Remota */}
            <div className="grid grid-cols-1 sm:grid-cols-3 gap-3">
              <div className="p-3.5 rounded-xl bg-slate-950/70 border border-slate-800">
                <span className="text-[11px] font-semibold text-slate-400">Recaudado este Mes</span>
                <div className="flex items-baseline gap-2 mt-1">
                  <p className="text-lg font-bold font-mono text-emerald-400">
                    ${pagosServicio.filter(p => p.fecha_pago.startsWith('2026-10') && p.moneda === 'CUP').reduce((a, b) => a + b.monto_pagado, 0).toLocaleString()} CUP
                  </p>
                  <span className="text-slate-600">|</span>
                  <p className="text-lg font-bold font-mono text-sky-400">
                    ${pagosServicio.filter(p => p.fecha_pago.startsWith('2026-10') && p.moneda === 'USD').reduce((a, b) => a + b.monto_pagado, 0)} USD
                  </p>
                </div>
                <p className="text-[10px] text-emerald-400 mt-1 flex items-center gap-1">
                  <CheckCircle2 className="h-3 w-3" /> Cobros verificados
                </p>
              </div>

              <div className="p-3.5 rounded-xl bg-slate-950/70 border border-slate-800">
                <span className="text-[11px] font-semibold text-slate-400">Plazo Primeros 15 Días</span>
                <p className="text-lg font-bold text-white mt-1">
                  Día 10 del mes
                </p>
                <p className="text-[10px] text-amber-400 mt-1 flex items-center gap-1">
                  <Clock className="h-3 w-3" /> Quedan 5 días para pagar sin mora
                </p>
              </div>

              <div className="p-3.5 rounded-xl bg-slate-950/70 border border-rose-500/20 bg-rose-950/10">
                <span className="text-[11px] font-semibold text-rose-300">Clientes con Deuda / Mora</span>
                <p className="text-lg font-bold font-mono text-rose-400 mt-1">
                  {suscripciones.filter(s => s.tiene_deuda).length} clientes en mora
                </p>
                <p className="text-[10px] text-rose-400 mt-1">
                  Requieren gestión de cobranza
                </p>
              </div>
            </div>

            {/* Dos columnas: Mayores deudores & Pagos de la semana */}
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-4 pt-2">
              {/* Columna 1: Mayores deudores */}
              <div className="p-4 rounded-xl border border-slate-800 bg-slate-950/60 space-y-3">
                <div className="flex justify-between items-center">
                  <h4 className="text-xs font-bold text-white flex items-center gap-1.5">
                    <AlertTriangle className="h-3.5 w-3.5 text-rose-400" />
                    Mayores Deudores de Gestión Remota
                  </h4>
                  <button
                    onClick={() => onNavigate('finanzas')}
                    className="text-[11px] text-rose-400 hover:text-rose-300 font-semibold"
                  >
                    Ver todos →
                  </button>
                </div>

                <div className="space-y-2">
                  {suscripciones.filter(s => s.tiene_deuda).slice(0, 3).map((sub) => (
                    <div key={sub.id} className="p-2.5 rounded-lg border border-rose-500/20 bg-rose-950/20 flex items-center justify-between text-xs">
                      <div>
                        <p className="font-semibold text-white">{sub.cliente_nombre}</p>
                        <p className="text-[10px] text-slate-400 font-mono">
                          {sub.meses_deuda_count} meses ({sub.meses_deuda_detalle.join(', ') || 'Octubre'})
                        </p>
                      </div>
                      <span className="font-mono font-bold text-rose-400 text-xs">
                        {sub.moneda === 'USD' ? `$${sub.monto_a_cobrar} USD` : `$${sub.monto_a_cobrar.toLocaleString()} CUP`}
                      </span>
                    </div>
                  ))}
                </div>
              </div>

              {/* Columna 2: Pagos registrados en la semana */}
              <div className="p-4 rounded-xl border border-slate-800 bg-slate-950/60 space-y-3">
                <div className="flex justify-between items-center">
                  <h4 className="text-xs font-bold text-white flex items-center gap-1.5">
                    <Calendar className="h-3.5 w-3.5 text-emerald-400" />
                    Pagos Recibidos en la Semana
                  </h4>
                  <button
                    onClick={() => onNavigate('finanzas')}
                    className="text-[11px] text-emerald-400 hover:text-emerald-300 font-semibold"
                  >
                    Ver historial →
                  </button>
                </div>

                <div className="space-y-2">
                  {pagosServicio.slice(0, 3).map((p) => (
                    <div key={p.id} className="p-2.5 rounded-lg border border-slate-800 bg-slate-900/50 flex items-center justify-between text-xs">
                      <div>
                        <p className="font-semibold text-white">{p.cliente_nombre}</p>
                        <p className="text-[10px] text-slate-400">
                          {p.fecha_pago} · {p.metodo_pago}{p.numero_transaccion ? ` (${p.numero_transaccion})` : ''}
                        </p>
                      </div>
                      <span className="font-mono font-bold text-emerald-400 text-xs">
                        +{p.moneda === 'USD' ? `$${p.monto_pagado} USD` : `$${p.monto_pagado.toLocaleString()} CUP`}
                      </span>
                    </div>
                  ))}
                </div>
              </div>
            </div>
          </div>

          <div className="rounded-2xl border border-slate-800 bg-slate-900/70 p-6 shadow-sm">
            <div className="flex items-center justify-between mb-4">
              <h3 className="text-sm font-bold text-white flex items-center gap-2">
                <DollarSign className="h-4 w-4 text-emerald-400" />
                Ventas y Gastos Operativos de Obras
              </h3>
              <button
                onClick={() => onNavigate('finanzas')}
                className="text-xs text-sky-400 hover:text-sky-300 font-semibold cursor-pointer"
              >
                Abrir Ventas & Gastos →
              </button>
            </div>

            <div className="space-y-2">
              {ventas.length === 0 && gastos.length === 0 ? (
                <div className="py-8 text-center text-xs text-slate-500">
                  No hay movimientos de ventas o gastos registrados.
                </div>
              ) : (
                <>
                  {ventas.slice(0, 3).map((v) => (
                    <div key={v.id} className="flex items-center justify-between p-3 rounded-xl bg-slate-950/60 border border-slate-800 text-xs">
                      <div>
                        <p className="font-semibold text-white">{v.concepto}</p>
                        <p className="text-[10px] text-slate-400">{v.cliente_nombre} · {v.fecha}</p>
                      </div>
                      <span className="font-mono font-bold text-emerald-400">
                        +${v.monto_total.toLocaleString()} CUP
                      </span>
                    </div>
                  ))}
                  {gastos.slice(0, 2).map((g) => (
                    <div key={g.id} className="flex items-center justify-between p-3 rounded-xl bg-slate-950/60 border border-slate-800 text-xs">
                      <div>
                        <p className="font-semibold text-white">{g.concepto}</p>
                        <p className="text-[10px] text-slate-400">{g.categoria} · {g.responsable}</p>
                      </div>
                      <span className="font-mono font-bold text-rose-400">
                        -${g.monto.toLocaleString()} CUP
                      </span>
                    </div>
                  ))}
                </>
              )}
            </div>
          </div>
        </div>
      )}

      {/* ---------- TAB: ADMINISTRACIÓN ---------- */}
      {activeTab === 'administracion' && (
        <div className="space-y-6">
          <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div className="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
              <span className="text-xs font-semibold text-slate-400">Total Clientes en Sistema</span>
              <p className="text-2xl font-bold text-sky-400 mt-2">{clientes.length}</p>
              <p className="text-[11px] text-slate-400 mt-1">Portafolio oficial DataPlus</p>
            </div>

            <div className="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
              <span className="text-xs font-semibold text-slate-400">Usuarios con Acceso</span>
              <p className="text-2xl font-bold text-emerald-400 mt-2">{usuarios.length || 6}</p>
              <p className="text-[11px] text-slate-400 mt-1">Autenticación híbrida LDAP + Local</p>
            </div>

            <div className="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
              <span className="text-xs font-semibold text-slate-400">Directorio LDAP / LLDAP</span>
              <p className="text-2xl font-bold text-purple-400 mt-2">dc=dataplus,dc=cu</p>
              <p className="text-[11px] text-slate-400 mt-1">Servicio online y enlazado</p>
            </div>
          </div>

          <div className="rounded-2xl border border-slate-800 bg-slate-900/70 p-6 shadow-sm space-y-4">
            <h3 className="text-sm font-bold text-white flex items-center gap-2">
              <ShieldCheck className="h-4 w-4 text-sky-400" />
              Accesos Rápidos de Administración
            </h3>
            <div className="grid grid-cols-1 sm:grid-cols-3 gap-3">
              <button
                onClick={() => onNavigate('clientes')}
                className="flex items-center justify-between p-3.5 rounded-xl border border-slate-800 bg-slate-950/70 hover:border-sky-500/40 text-left transition-colors cursor-pointer"
              >
                <div>
                  <p className="text-xs font-bold text-white">Directorio de Clientes</p>
                  <p className="text-[10px] text-slate-400 mt-0.5">{clientes.length} empresas registradas</p>
                </div>
                <ChevronRight className="h-4 w-4 text-slate-400" />
              </button>

              <button
                onClick={() => onNavigate('usuarios')}
                className="flex items-center justify-between p-3.5 rounded-xl border border-slate-800 bg-slate-950/70 hover:border-sky-500/40 text-left transition-colors cursor-pointer"
              >
                <div>
                  <p className="text-xs font-bold text-white">Usuarios & Permisos</p>
                  <p className="text-[10px] text-slate-400 mt-0.5">Roles RBAC y cuentas</p>
                </div>
                <ChevronRight className="h-4 w-4 text-slate-400" />
              </button>

              <button
                onClick={() => onNavigate('ldap_settings')}
                className="flex items-center justify-between p-3.5 rounded-xl border border-slate-800 bg-slate-950/70 hover:border-sky-500/40 text-left transition-colors cursor-pointer"
              >
                <div>
                  <p className="text-xs font-bold text-white">Configuración LDAP</p>
                  <p className="text-[10px] text-slate-400 mt-0.5">Parámetros de conexión LLDAP</p>
                </div>
                <ChevronRight className="h-4 w-4 text-slate-400" />
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};
