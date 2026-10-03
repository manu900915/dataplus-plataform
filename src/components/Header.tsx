import React, { useState } from 'react';
import { 
  Search, 
  Bell, 
  ShieldCheck, 
  AlertTriangle, 
  CheckCircle2, 
  User, 
  ExternalLink,
  Layers
} from 'lucide-react';
import { ItemInventario, Incidencia } from '../types';

interface HeaderProps {
  currentModule: string;
  lowStockItems: ItemInventario[];
  pendingIncidencias: Incidencia[];
  onOpenSearch: () => void;
  onNavigate: (module: string) => void;
}

export const Header: React.FC<HeaderProps> = ({
  currentModule,
  lowStockItems,
  pendingIncidencias,
  onOpenSearch,
  onNavigate,
}) => {
  const [showNotifications, setShowNotifications] = useState(false);
  const totalAlerts = lowStockItems.length + pendingIncidencias.length;

  const moduleTitles: Record<string, string> = {
    dashboard: 'Panel de Control Principal',
    proyectos: 'Proyectos & Presupuestos',
    kanban: 'Seguimiento de Proyectos (Kanban)',
    tipos_proyecto: 'Tipos de Proyecto',
    servicios: 'Servicios Técnicos (CCTV, SACI, Gestión Remota)',
    incidencias: 'Incidencias Técnicas',
    buscar_negocios: 'Búsqueda Rápida de Negocios',
    contratos: 'Contratos & Pólizas de Mantenimiento',
    inventario: 'Items & Control de Stock',
    categorias_item: 'Categorías de Items',
    almacenes: 'Almacenes & Sedes',
    finanzas: 'Finanzas (Ventas y Gastos)',
    reportes: 'Reportes & Estadísticas',
    clientes: 'Directorio de Clientes & Sedes',
    tipos_negocio: 'Tipos de Negocio',
    brigadas: 'Brigadas de Servicio & Cuadrillas',
    usuarios: 'Usuarios del Sistema & Permisos',
    ldap_settings: 'Configuración de Directorio LDAP / LLDAP'
  };

  return (
    <header className="sticky top-0 z-30 flex h-16 w-full items-center justify-between border-b border-slate-800 bg-slate-900/90 px-6 backdrop-blur-md">
      <div className="flex items-center gap-4">
        <div>
          <div className="flex items-center gap-2 text-xs font-medium text-slate-400">
            <span>DATAPLUS</span>
            <span>/</span>
            <span className="text-teal-400 capitalize">{currentModule.replace('_', ' ')}</span>
          </div>
          <h1 className="text-lg font-semibold text-white tracking-tight">
            {moduleTitles[currentModule] || 'DataPlus Plataforma'}
          </h1>
        </div>
      </div>

      <div className="flex items-center gap-3">
        {/* Global Quick Search Button */}
        <button
          onClick={onOpenSearch}
          className="flex items-center gap-2 rounded-lg border border-slate-800 bg-slate-800/60 px-3 py-1.5 text-xs text-slate-300 hover:border-slate-700 hover:bg-slate-800 transition-colors cursor-pointer"
        >
          <Search className="h-3.5 w-3.5 text-slate-400" />
          <span className="hidden sm:inline">Buscar clientes, proyectos, ítems...</span>
          <kbd className="hidden rounded bg-slate-700/80 px-1.5 py-0.5 text-[10px] text-slate-300 sm:inline-block font-mono">
            ⌘K
          </kbd>
        </button>

        {/* Notifications Dropdown */}
        <div className="relative">
          <button
            onClick={() => setShowNotifications(!showNotifications)}
            className="relative flex h-9 w-9 items-center justify-center rounded-lg border border-slate-800 bg-slate-800/60 text-slate-300 hover:bg-slate-800 transition-colors cursor-pointer"
            title="Notificaciones y alertas operativas"
          >
            <Bell className="h-4 w-4" />
            {totalAlerts > 0 && (
              <span className="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-amber-500 text-[10px] font-bold text-slate-950 animate-pulse">
                {totalAlerts}
              </span>
            )}
          </button>

          {showNotifications && (
            <div className="absolute right-0 mt-2 w-80 rounded-xl border border-slate-700 bg-slate-900 p-3 shadow-2xl z-50">
              <div className="flex items-center justify-between border-b border-slate-800 pb-2 mb-2">
                <span className="text-xs font-semibold text-white uppercase tracking-wider">
                  Alertas del Sistema ({totalAlerts})
                </span>
                <span className="text-[11px] text-teal-400">En tiempo real</span>
              </div>

              <div className="max-h-64 overflow-y-auto space-y-2">
                {lowStockItems.length > 0 && (
                  <div className="rounded-lg bg-amber-950/30 border border-amber-800/40 p-2.5">
                    <div className="flex items-center gap-1.5 text-xs font-medium text-amber-400 mb-1">
                      <AlertTriangle className="h-3.5 w-3.5" />
                      <span>{lowStockItems.length} Ítems con Stock Bajo</span>
                    </div>
                    <ul className="text-[11px] text-slate-300 space-y-1">
                      {lowStockItems.slice(0, 3).map((item) => (
                        <li key={item.id} className="flex justify-between items-center">
                          <span className="truncate max-w-[170px]">{item.nombre}</span>
                          <span className="text-amber-300 font-mono font-semibold">
                            {item.stock_actual} / {item.stock_minimo} {item.unidad_medida}
                          </span>
                        </li>
                      ))}
                    </ul>
                    <button
                      onClick={() => {
                        setShowNotifications(false);
                        onNavigate('inventario');
                      }}
                      className="mt-2 text-[11px] text-amber-400 hover:underline flex items-center gap-1"
                    >
                      Ver inventario <ExternalLink className="h-2.5 w-2.5" />
                    </button>
                  </div>
                )}

                {pendingIncidencias.length > 0 && (
                  <div className="rounded-lg bg-sky-950/30 border border-sky-800/40 p-2.5">
                    <div className="flex items-center gap-1.5 text-xs font-medium text-sky-400 mb-1">
                      <CheckCircle2 className="h-3.5 w-3.5" />
                      <span>{pendingIncidencias.length} Incidencias Pendientes</span>
                    </div>
                    <ul className="text-[11px] text-slate-300 space-y-1">
                      {pendingIncidencias.slice(0, 2).map((inc) => (
                        <li key={inc.id} className="truncate">
                          <span className="font-semibold text-slate-200">[{inc.codigo}]</span> {inc.titulo}
                        </li>
                      ))}
                    </ul>
                    <button
                      onClick={() => {
                        setShowNotifications(false);
                        onNavigate('incidencias');
                      }}
                      className="mt-2 text-[11px] text-sky-400 hover:underline flex items-center gap-1"
                    >
                      Ver incidencias <ExternalLink className="h-2.5 w-2.5" />
                    </button>
                  </div>
                )}

                {totalAlerts === 0 && (
                  <div className="py-4 text-center text-xs text-slate-400">
                    No hay alertas activas en este momento.
                  </div>
                )}
              </div>
            </div>
          )}
        </div>

        {/* User Badge */}
        <div className="flex items-center gap-2 pl-2 border-l border-slate-800">
          <div className="flex h-9 w-9 items-center justify-center rounded-lg bg-gradient-to-br from-teal-500 to-emerald-600 text-slate-950 font-bold text-xs shadow-md shadow-teal-500/20">
            DP
          </div>
          <div className="hidden lg:block text-left">
            <div className="text-xs font-medium text-white flex items-center gap-1">
              Admin Operaciones
              <ShieldCheck className="h-3 w-3 text-teal-400" />
            </div>
            <div className="text-[10px] text-slate-400">Superusuario DataPlus</div>
          </div>
        </div>
      </div>
    </header>
  );
};
