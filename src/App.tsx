import React, { useState, useEffect } from 'react';
import { 
  Cliente, 
  Proyecto, 
  Incidencia, 
  ItemInventario, 
  Almacen, 
  Brigada, 
  InventarioMovimiento,
  Servicio,
  TipoProyecto,
  TipoNegocio,
  Contrato,
  Venta,
  Gasto,
  CategoriaItem,
  UserAccount
} from './types';
import { 
  INITIAL_CLIENTES, 
  INITIAL_ALMACENES, 
  INITIAL_ITEMS, 
  INITIAL_BRIGADAS, 
  INITIAL_PROYECTOS, 
  INITIAL_INCIDENCIAS, 
  INITIAL_MOVIMIENTOS,
  INITIAL_SERVICIOS,
  INITIAL_TIPOS_PROYECTO,
  INITIAL_TIPOS_NEGOCIO,
  INITIAL_CONTRATOS,
  INITIAL_VENTAS,
  INITIAL_GASTOS,
  INITIAL_CATEGORIAS_ITEM,
  INITIAL_USUARIOS,
  LDAP_DIRECTORY_USERS,
  loadFromStorage, 
  saveToStorage 
} from './data/mockData';

import { Sidebar } from './components/Sidebar';
import { Header } from './components/Header';
import { DashboardView } from './components/DashboardView';
import { ProyectosView } from './components/ProyectosView';
import { KanbanView } from './components/KanbanView';
import { IncidenciasView } from './components/IncidenciasView';
import { InventarioView } from './components/InventarioView';
import { AlmacenesView } from './components/AlmacenesView';
import { ClientesView } from './components/ClientesView';
import { BuscarNegociosView } from './components/BuscarNegociosView';
import { BrigadasView } from './components/BrigadasView';
import { ReportesView } from './components/ReportesView';
import { SearchModal } from './components/SearchModal';
import { PresupuestoModal } from './components/PresupuestoModal';
import { LdapSettingsView } from './components/LdapSettingsView';
import { ServiciosView } from './components/ServiciosView';
import { TiposProyectoView } from './components/TiposProyectoView';
import { TiposNegocioView } from './components/TiposNegocioView';
import { ContratosView } from './components/ContratosView';
import { FinanzasView } from './components/FinanzasView';
import { CategoriasItemView } from './components/CategoriasItemView';
import { UsuariosView } from './components/UsuariosView';

export const App: React.FC = () => {
  const [currentModule, setCurrentModule] = useState<string>('dashboard');
  const [isSearchOpen, setIsSearchOpen] = useState<boolean>(false);
  const [editingBudgetFromKanban, setEditingBudgetFromKanban] = useState<Proyecto | null>(null);
  const [quickRestockItem, setQuickRestockItem] = useState<ItemInventario | null>(null);

  // Persistent States
  const [clientes, setClientes] = useState<Cliente[]>(() => 
    loadFromStorage('clientes', INITIAL_CLIENTES)
  );
  const [almacenes, setAlmacenes] = useState<Almacen[]>(() => 
    loadFromStorage('almacenes', INITIAL_ALMACENES)
  );
  const [items, setItems] = useState<ItemInventario[]>(() => 
    loadFromStorage('items', INITIAL_ITEMS)
  );
  const [brigadas, setBrigadas] = useState<Brigada[]>(() => 
    loadFromStorage('brigadas', INITIAL_BRIGADAS)
  );
  const [proyectos, setProyectos] = useState<Proyecto[]>(() => 
    loadFromStorage('proyectos', INITIAL_PROYECTOS)
  );
  const [incidencias, setIncidencias] = useState<Incidencia[]>(() => 
    loadFromStorage('incidencias', INITIAL_INCIDENCIAS)
  );
  const [movimientos, setMovimientos] = useState<InventarioMovimiento[]>(() => 
    loadFromStorage('movimientos', INITIAL_MOVIMIENTOS)
  );
  const [servicios, setServicios] = useState<Servicio[]>(() => 
    loadFromStorage('servicios', INITIAL_SERVICIOS)
  );
  const [tiposProyecto, setTiposProyecto] = useState<TipoProyecto[]>(() => 
    loadFromStorage('tipos_proyecto', INITIAL_TIPOS_PROYECTO)
  );
  const [tiposNegocio, setTiposNegocio] = useState<TipoNegocio[]>(() => 
    loadFromStorage('tipos_negocio', INITIAL_TIPOS_NEGOCIO)
  );
  const [contratos, setContratos] = useState<Contrato[]>(() => 
    loadFromStorage('contratos', INITIAL_CONTRATOS)
  );
  const [ventas, setVentas] = useState<Venta[]>(() => 
    loadFromStorage('ventas', INITIAL_VENTAS)
  );
  const [gastos, setGastos] = useState<Gasto[]>(() => 
    loadFromStorage('gastos', INITIAL_GASTOS)
  );
  const [categoriasItem, setCategoriasItem] = useState<CategoriaItem[]>(() => 
    loadFromStorage('categorias_item', INITIAL_CATEGORIAS_ITEM)
  );
  const [usuarios, setUsuarios] = useState<UserAccount[]>(() => 
    loadFromStorage('usuarios', INITIAL_USUARIOS)
  );

  // Sync to storage
  useEffect(() => saveToStorage('clientes', clientes), [clientes]);
  useEffect(() => saveToStorage('almacenes', almacenes), [almacenes]);
  useEffect(() => saveToStorage('items', items), [items]);
  useEffect(() => saveToStorage('brigadas', brigadas), [brigadas]);
  useEffect(() => saveToStorage('proyectos', proyectos), [proyectos]);
  useEffect(() => saveToStorage('incidencias', incidencias), [incidencias]);
  useEffect(() => saveToStorage('movimientos', movimientos), [movimientos]);
  useEffect(() => saveToStorage('servicios', servicios), [servicios]);
  useEffect(() => saveToStorage('tipos_proyecto', tiposProyecto), [tiposProyecto]);
  useEffect(() => saveToStorage('tipos_negocio', tiposNegocio), [tiposNegocio]);
  useEffect(() => saveToStorage('contratos', contratos), [contratos]);
  useEffect(() => saveToStorage('ventas', ventas), [ventas]);
  useEffect(() => saveToStorage('gastos', gastos), [gastos]);
  useEffect(() => saveToStorage('categorias_item', categoriasItem), [categoriasItem]);
  useEffect(() => saveToStorage('usuarios', usuarios), [usuarios]);

  // Derived Alerts
  const lowStockItems = items.filter(i => i.stock_actual <= i.stock_minimo);
  const openIncidencias = incidencias.filter(
    i => i.estado !== 'Resuelta' && i.estado !== 'Cerrada' && i.estado !== 'Cancelada'
  );

  // Handlers for Proyectos
  const handleAddProyecto = (nuevo: Proyecto) => {
    setProyectos([nuevo, ...proyectos]);
  };

  const handleUpdateProyecto = (actualizado: Proyecto) => {
    setProyectos(proyectos.map(p => p.id === actualizado.id ? actualizado : p));
  };

  const handleDeleteProyecto = (id: string) => {
    setProyectos(proyectos.filter(p => p.id !== id));
  };

  const handleUpdateKanbanStatus = (proyectoId: string, newKanbanStatus: any) => {
    setProyectos(proyectos.map(p => {
      if (p.id === proyectoId) {
        return {
          ...p,
          estado_kanban: newKanbanStatus,
          estado: newKanbanStatus === 'completado' ? 'completado' : 'en_progreso',
          progreso: newKanbanStatus === 'completado' ? 100 : newKanbanStatus === 'en_revision' ? 85 : newKanbanStatus === 'en_progreso' ? 50 : 10,
          updated_at: new Date().toISOString()
        };
      }
      return p;
    }));
  };

  // Handlers for Incidencias
  const handleAddIncidencia = (nueva: Omit<Incidencia, 'id' | 'codigo' | 'fecha_reporte'>) => {
    const codeNumber = incidencias.length + 1;
    const code = `INC-${String(codeNumber).padStart(4, '0')}`;
    const newInc: Incidencia = {
      ...nueva,
      id: `inc-${Date.now()}`,
      codigo: code,
      fecha_reporte: new Date().toISOString()
    };
    setIncidencias([newInc, ...incidencias]);
  };

  const handleUpdateIncidencia = (actualizada: Incidencia) => {
    setIncidencias(incidencias.map(i => i.id === actualizada.id ? actualizada : i));
  };

  // Handlers for Inventario
  const handleAddItem = (nuevo: Omit<ItemInventario, 'id' | 'codigo'>) => {
    const codeNumber = items.length + 1;
    const code = `INV-${String(codeNumber).padStart(6, '0')}`;
    const newItem: ItemInventario = {
      ...nuevo,
      id: `itm-${Date.now()}`,
      codigo: code
    };
    setItems([newItem, ...items]);
  };

  const handleUpdateItem = (actualizado: ItemInventario) => {
    setItems(items.map(i => i.id === actualizado.id ? actualizado : i));
  };

  const handleDeleteItem = (id: string) => {
    setItems(items.filter(i => i.id !== id));
  };

  const handleRegisterMovement = (nuevoMov: Omit<InventarioMovimiento, 'id' | 'fecha'>) => {
    const fullMov: InventarioMovimiento = {
      ...nuevoMov,
      id: `mov-${Date.now()}`,
      fecha: new Date().toISOString()
    };
    setMovimientos([fullMov, ...movimientos]);

    setItems(items.map(it => {
      if (it.id === nuevoMov.item_id) {
        let newStock = it.stock_actual;
        if (nuevoMov.tipo === 'entrada' || nuevoMov.tipo === 'devolucion') {
          newStock += nuevoMov.cantidad;
        } else if (nuevoMov.tipo === 'salida') {
          newStock = Math.max(0, newStock - nuevoMov.cantidad);
        } else if (nuevoMov.tipo === 'ajuste') {
          newStock = nuevoMov.cantidad;
        }
        return { ...it, stock_actual: newStock };
      }
      return it;
    }));
  };

  // Handlers for Clientes
  const handleAddCliente = (nuevo: Omit<Cliente, 'id' | 'codigo' | 'created_at'>) => {
    const codeNumber = clientes.length + 1;
    const code = `CLI-${String(codeNumber).padStart(4, '0')}`;
    const newCli: Cliente = {
      ...nuevo,
      id: `cli-${Date.now()}`,
      codigo: code,
      created_at: new Date().toISOString()
    };
    setClientes([newCli, ...clientes]);
  };

  const handleUpdateCliente = (actualizado: Cliente) => {
    setClientes(clientes.map(c => c.id === actualizado.id ? actualizado : c));
  };

  const handleDeleteCliente = (id: string) => {
    setClientes(clientes.filter(c => c.id !== id));
  };

  // Handlers for Brigadas
  const handleAddBrigada = (nueva: Omit<Brigada, 'id' | 'servicios_activos'>) => {
    const newBrig: Brigada = {
      ...nueva,
      id: `brg-${Date.now()}`,
      servicios_activos: 0
    };
    setBrigadas([...brigadas, newBrig]);
  };

  const handleUpdateBrigada = (actualizada: Brigada) => {
    setBrigadas(brigadas.map(b => b.id === actualizada.id ? actualizada : b));
  };

  // Handlers for Servicios
  const handleAddServicio = (nuevo: Omit<Servicio, 'id' | 'codigo'>) => {
    const codeNumber = servicios.length + 1;
    const code = `SRV-${String(codeNumber).padStart(4, '0')}`;
    const newSrv: Servicio = {
      ...nuevo,
      id: `srv-${Date.now()}`,
      codigo: code
    };
    setServicios([newSrv, ...servicios]);
  };

  const handleUpdateServicio = (actualizado: Servicio) => {
    setServicios(servicios.map(s => s.id === actualizado.id ? actualizado : s));
  };

  const handleDeleteServicio = (id: string) => {
    setServicios(servicios.filter(s => s.id !== id));
  };

  const [isSyncingLdap, setIsSyncingLdap] = useState(false);
  const handleSyncLdap = () => {
    setIsSyncingLdap(true);
    setTimeout(() => {
      const existingEmails = new Set(usuarios.map(u => u.email.toLowerCase()));
      const newLdapUsers = LDAP_DIRECTORY_USERS.filter(u => !existingEmails.has(u.email.toLowerCase()));
      setUsuarios([...usuarios, ...newLdapUsers]);
      setIsSyncingLdap(false);
    }, 600);
  };

  return (
    <div className="flex h-screen bg-slate-950 text-slate-100 antialiased overflow-hidden font-sans">
      {/* Sidebar Navigation */}
      <Sidebar
        currentModule={currentModule}
        onSelectModule={setCurrentModule}
        openIncidenciasCount={openIncidencias.length}
        lowStockCount={lowStockItems.length}
      />

      {/* Main Content Area */}
      <div className="flex flex-1 flex-col overflow-hidden">
        {/* Global Application Header */}
        <Header
          currentModule={currentModule}
          lowStockItems={lowStockItems}
          pendingIncidencias={openIncidencias}
          onOpenSearch={() => setIsSearchOpen(true)}
          onNavigate={setCurrentModule}
        />

        {/* Scrollable Viewport */}
        <main className="flex-1 overflow-y-auto p-6 bg-slate-950">
          <div className="mx-auto max-w-7xl">
            {currentModule === 'dashboard' && (
              <DashboardView
                proyectos={proyectos}
                incidencias={incidencias}
                items={items}
                almacenes={almacenes}
                brigadas={brigadas}
                clientes={clientes}
                servicios={servicios}
                contratos={contratos}
                ventas={ventas}
                gastos={gastos}
                usuarios={usuarios}
                onNavigate={setCurrentModule}
                onQuickRestock={(item) => {
                  setQuickRestockItem(item);
                  setCurrentModule('inventario');
                }}
              />
            )}

            {currentModule === 'proyectos' && (
              <ProyectosView
                proyectos={proyectos}
                clientes={clientes}
                items={items}
                onAddProyecto={handleAddProyecto}
                onUpdateProyecto={handleUpdateProyecto}
                onDeleteProyecto={handleDeleteProyecto}
              />
            )}

            {currentModule === 'kanban' && (
              <KanbanView
                proyectos={proyectos}
                onUpdateStatus={handleUpdateKanbanStatus}
                onOpenBudget={(p) => setEditingBudgetFromKanban(p)}
              />
            )}

            {currentModule === 'tipos_proyecto' && (
              <TiposProyectoView
                tipos={tiposProyecto}
                onAddTipo={(t) => setTiposProyecto([...tiposProyecto, { ...t, id: `tp-${Date.now()}`, proyectos_count: 0 }])}
                onUpdateTipo={(t) => setTiposProyecto(tiposProyecto.map(item => item.id === t.id ? t : item))}
                onDeleteTipo={(id) => setTiposProyecto(tiposProyecto.filter(item => item.id !== id))}
              />
            )}

            {currentModule === 'servicios' && (
              <ServiciosView
                servicios={servicios}
                clientes={clientes}
                brigadas={brigadas}
                onAddServicio={handleAddServicio}
                onUpdateServicio={handleUpdateServicio}
                onDeleteServicio={handleDeleteServicio}
              />
            )}

            {currentModule === 'incidencias' && (
              <IncidenciasView
                incidencias={incidencias}
                clientes={clientes}
                onAddIncidencia={handleAddIncidencia}
                onUpdateIncidencia={handleUpdateIncidencia}
              />
            )}

            {currentModule === 'buscar_negocios' && (
              <BuscarNegociosView
                clientes={clientes}
                onSelectCliente={() => setCurrentModule('clientes')}
              />
            )}

            {currentModule === 'contratos' && (
              <ContratosView
                contratos={contratos}
                clientes={clientes}
                onAddContrato={(c) => setContratos([...contratos, { ...c, id: `ctr-${Date.now()}`, codigo: `CTR-2026-${String(contratos.length + 1).padStart(3, '0')}` }])}
                onUpdateContrato={(c) => setContratos(contratos.map(item => item.id === c.id ? c : item))}
                onDeleteContrato={(id) => setContratos(contratos.filter(item => item.id !== id))}
              />
            )}

            {currentModule === 'inventario' && (
              <InventarioView
                items={items}
                almacenes={almacenes}
                movimientos={movimientos}
                onAddItem={handleAddItem}
                onUpdateItem={handleUpdateItem}
                onDeleteItem={handleDeleteItem}
                onRegisterMovement={handleRegisterMovement}
                highlightItem={quickRestockItem}
                onClearHighlight={() => setQuickRestockItem(null)}
              />
            )}

            {currentModule === 'categorias_item' && (
              <CategoriasItemView
                categorias={categoriasItem}
                onAddCategoria={(c) => setCategoriasItem([...categoriasItem, { ...c, id: `cat-${Date.now()}`, items_count: 0 }])}
                onUpdateCategoria={(c) => setCategoriasItem(categoriasItem.map(item => item.id === c.id ? c : item))}
                onDeleteCategoria={(id) => setCategoriasItem(categoriasItem.filter(item => item.id !== id))}
              />
            )}

            {currentModule === 'almacenes' && (
              <AlmacenesView
                almacenes={almacenes}
                items={items}
                onAddAlmacen={(a) => setAlmacenes([...almacenes, { ...a, id: `alm-${Date.now()}`, total_items: 0, valor_estimado: 0 }])}
                onUpdateAlmacen={(a) => setAlmacenes(almacenes.map(item => item.id === a.id ? a : item))}
              />
            )}

            {currentModule === 'finanzas' && (
              <FinanzasView
                ventas={ventas}
                gastos={gastos}
                clientes={clientes}
                onAddVenta={(v) => setVentas([...ventas, { ...v, id: `vta-${Date.now()}`, codigo: `VTA-${String(ventas.length + 1).padStart(3, '0')}` }])}
                onAddGasto={(g) => setGastos([...gastos, { ...g, id: `gst-${Date.now()}`, codigo: `GST-${String(gastos.length + 1).padStart(3, '0')}` }])}
              />
            )}

            {currentModule === 'reportes' && (
              <ReportesView
                proyectos={proyectos}
                incidencias={incidencias}
                items={items}
                almacenes={almacenes}
              />
            )}

            {currentModule === 'clientes' && (
              <ClientesView
                clientes={clientes}
                onAddCliente={handleAddCliente}
                onUpdateCliente={handleUpdateCliente}
                onDeleteCliente={handleDeleteCliente}
              />
            )}

            {currentModule === 'tipos_negocio' && (
              <TiposNegocioView
                tipos={tiposNegocio}
                onAddTipo={(t) => setTiposNegocio([...tiposNegocio, { ...t, id: `tn-${Date.now()}`, negocios_count: 0 }])}
                onUpdateTipo={(t) => setTiposNegocio(tiposNegocio.map(item => item.id === t.id ? t : item))}
                onDeleteTipo={(id) => setTiposNegocio(tiposNegocio.filter(item => item.id !== id))}
              />
            )}

            {currentModule === 'brigadas' && (
              <BrigadasView
                brigadas={brigadas}
                onAddBrigada={handleAddBrigada}
                onUpdateBrigada={handleUpdateBrigada}
              />
            )}

            {currentModule === 'usuarios' && (
              <UsuariosView
                usuarios={usuarios}
                onAddUsuario={(u) => setUsuarios([...usuarios, { ...u, id: `usr-${Date.now()}`, created_at: new Date().toISOString().split('T')[0] }])}
                onUpdateUsuario={(u) => setUsuarios(usuarios.map(item => item.id === u.id ? u : item))}
                onDeleteUsuario={(id) => setUsuarios(usuarios.filter(item => item.id !== id))}
                onSyncLdap={handleSyncLdap}
                isSyncingLdap={isSyncingLdap}
              />
            )}

            {currentModule === 'ldap_settings' && (
              <LdapSettingsView />
            )}
          </div>
        </main>
      </div>

      {/* Global Spotlight Search Modal */}
      <SearchModal
        isOpen={isSearchOpen}
        onClose={() => setIsSearchOpen(false)}
        proyectos={proyectos}
        incidencias={incidencias}
        items={items}
        clientes={clientes}
        onSelectProyecto={(p) => {
          setIsSearchOpen(false);
          setCurrentModule('proyectos');
        }}
        onSelectIncidencia={(inc) => {
          setIsSearchOpen(false);
          setCurrentModule('incidencias');
        }}
        onSelectItem={(item) => {
          setIsSearchOpen(false);
          setCurrentModule('inventario');
        }}
        onSelectCliente={(cli) => {
          setIsSearchOpen(false);
          setCurrentModule('clientes');
        }}
      />

      {/* Presupuesto Modal */}
      {editingBudgetFromKanban && (
        <PresupuestoModal
          isOpen={true}
          onClose={() => setEditingBudgetFromKanban(null)}
          proyecto={editingBudgetFromKanban}
          items={items}
          onSavePresupuesto={(lineas) => {
            const nuevoTotal = lineas.reduce((acc, l) => acc + (l.subtotal || 0), 0);
            handleUpdateProyecto({
              ...editingBudgetFromKanban,
              lineas_presupuesto: lineas,
              presupuesto_total: nuevoTotal
            });
            setEditingBudgetFromKanban(null);
          }}
        />
      )}
    </div>
  );
};

export default App;
