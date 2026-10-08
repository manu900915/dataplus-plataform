import React from 'react';
import { 
  LayoutDashboard, 
  FolderKanban, 
  Columns3, 
  Tag,
  Radio,
  AlertCircle, 
  Search,
  FileText,
  Boxes, 
  Layers,
  Warehouse, 
  ArrowLeftRight,
  ShoppingCart,
  CreditCard,
  BarChart3,
  Users2, 
  Store,
  Truck, 
  Users,
  Server,
  Activity
} from 'lucide-react';

interface SidebarProps {
  currentModule: string;
  onSelectModule: (module: string) => void;
  openIncidenciasCount: number;
  lowStockCount: number;
}

export const Sidebar: React.FC<SidebarProps> = ({
  currentModule,
  onSelectModule,
  openIncidenciasCount,
  lowStockCount,
}) => {
  const navSections = [
    {
      title: 'Principal',
      items: [
        { id: 'dashboard', label: 'Dashboard', icon: LayoutDashboard }
      ]
    },
    {
      title: 'Proyectos',
      items: [
        { id: 'proyectos', label: 'Proyectos & Presup.', icon: FolderKanban },
        { id: 'kanban', label: 'Seguimiento (Kanban)', icon: Columns3 },
        { id: 'tipos_proyecto', label: 'Tipos de Proyecto', icon: Tag }
      ]
    },
    {
      title: 'Operaciones',
      items: [
        { id: 'servicios', label: 'Servicios Técnicos', icon: Radio },
        { 
          id: 'incidencias', 
          label: 'Incidencias Técnicas', 
          icon: AlertCircle, 
          badge: openIncidenciasCount > 0 ? openIncidenciasCount : undefined,
          badgeColor: 'bg-rose-500/20 text-rose-300 border border-rose-500/30'
        },
        { id: 'buscar_negocios', label: 'Buscar Negocios', icon: Search },
        { id: 'contratos', label: 'Contratos', icon: FileText }
      ]
    },
    {
      title: 'Inventario',
      items: [
        { 
          id: 'inventario', 
          label: 'Items', 
          icon: Boxes,
          badge: lowStockCount > 0 ? lowStockCount : undefined,
          badgeColor: 'bg-amber-500/20 text-amber-300 border border-amber-500/30'
        },
        { id: 'categorias_item', label: 'Categorías de Items', icon: Layers },
        { id: 'almacenes', label: 'Almacenes', icon: Warehouse }
      ]
    },
    {
      title: 'Finanzas',
      items: [
        { id: 'finanzas', label: 'Ventas y Gastos', icon: ShoppingCart }
      ]
    },
    {
      title: 'Reportes',
      items: [
        { id: 'reportes', label: 'Reportes & Métricas', icon: BarChart3 }
      ]
    },
    {
      title: 'Administración',
      items: [
        { id: 'clientes', label: 'Clientes', icon: Users2 },
        { id: 'tipos_negocio', label: 'Tipos de Negocio', icon: Store },
        { id: 'brigadas', label: 'Brigadas', icon: Truck },
        { id: 'usuarios', label: 'Usuarios & Roles', icon: Users },
        { id: 'ldap_settings', label: 'Configuración LDAP', icon: Server }
      ]
    }
  ];

  return (
    <aside className="w-64 flex-shrink-0 border-r border-slate-800 bg-slate-950 flex flex-col justify-between select-none">
      {/* Brand Header matching Filament AdminPanelProvider */}
      <div>
        <div className="flex h-16 items-center px-5 border-b border-slate-800/80 gap-3">
          <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-tr from-sky-500 to-blue-600 p-2 shadow-lg shadow-sky-500/20">
            <Radio className="h-6 w-6 text-white stroke-[2.5]" />
          </div>
          <div>
            <div className="flex items-center gap-1.5 font-bold tracking-tight text-white text-base">
              <span>data</span>
              <span className="text-sky-400">plus</span>
              <span className="rounded bg-sky-500/10 px-1.5 py-0.5 text-[9px] font-mono font-medium text-sky-400 border border-sky-500/20">
                PROD
              </span>
            </div>
            <p className="text-[10px] text-slate-400 font-medium leading-none">
              dataempress.dataplus.cu
            </p>
          </div>
        </div>

        {/* Navigation Items */}
        <div className="px-3 py-3 space-y-4 overflow-y-auto max-h-[calc(100vh-130px)]">
          {navSections.map((section) => (
            <div key={section.title}>
              <h3 className="px-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">
                {section.title}
              </h3>
              <div className="space-y-0.5">
                {section.items.map((item) => {
                  const Icon = item.icon;
                  const isActive = currentModule === item.id;
                  return (
                                        <button
                      key={item.id}
                      onClick={() => onSelectModule(item.id)}
                      onMouseDown={(e) => {
                        e.preventDefault();
                        if (window.getSelection) {
                          window.getSelection().removeAllRanges();
                        }
                      }}
                      className={`group flex w-full items-center justify-between rounded-lg px-3 py-1.5 text-xs font-medium transition-all cursor-pointer select-none outline-none focus:outline-none focus:ring-0 ${
                        isActive
                          ? "bg-sky-500/15 text-sky-300 font-semibold shadow-sm shadow-sky-500/5"
                          : "text-slate-400 hover:bg-slate-900 hover:text-slate-200"
                      }`}
                      style={{ caretColor: "transparent" }}
                    >
                      <div className="flex items-center gap-2.5 pointer-events-none select-none">
                        <Icon
                          className={`h-4 w-4 transition-colors pointer-events-none ${
                            isActive ? "text-sky-400" : "text-slate-400 group-hover:text-slate-300"
                          }`}
                        />
                        <span className="select-none pointer-events-none" style={{ caretColor: "transparent" }}>
                          {item.label}
                        </span>
                      </div>
                      {item.badge !== undefined && (
                        <span
                          className={`flex h-4 min-w-4 items-center justify-center rounded-full px-1.5 text-[10px] font-mono font-semibold pointer-events-none select-none ${item.badgeColor}`}
                        >
                          {item.badge}
                        </span>
                      )}
                    </button>
                  );
                })}
              </div>
            </div>
          ))}
        </div>
      </div>

      {/* Footer System Status */}
      <div className="p-3 border-t border-slate-800/80 bg-slate-900/40">
        <div className="flex items-center justify-between text-[11px] text-slate-400">
          <div className="flex items-center gap-1.5">
            <span className="relative flex h-2 w-2">
              <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
              <span className="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
            </span>
            <span className="text-slate-300 font-medium">VPS srv3.dataplus.cu</span>
          </div>
          <span className="font-mono text-[10px] text-slate-400">v2.6.4</span>
        </div>
      </div>
    </aside>
  );
};
