import React, { useState } from 'react';
import { 
  Boxes, 
  Plus, 
  Search, 
  Filter, 
  AlertTriangle, 
  ArrowDownLeft, 
  ArrowUpRight, 
  RotateCcw, 
  CheckCircle2, 
  Warehouse, 
  DollarSign, 
  History, 
  ListFilter,
  X
} from 'lucide-react';
import { ItemInventario, Almacen, InventarioMovimiento, MovementType } from '../types';

interface InventarioViewProps {
  items: ItemInventario[];
  almacenes: Almacen[];
  movimientos: InventarioMovimiento[];
  onAddItem: (item: ItemInventario) => void;
  onAddMovimiento: (mov: InventarioMovimiento) => void;
  quickRestockItem: ItemInventario | null;
  onClearQuickRestock: () => void;
}

export const InventarioView: React.FC<InventarioViewProps> = ({
  items,
  almacenes,
  movimientos,
  onAddItem,
  onAddMovimiento,
  quickRestockItem,
  onClearQuickRestock,
}) => {
  const [activeTab, setActiveTab] = useState<'items' | 'movimientos'>('items');
  const [searchTerm, setSearchTerm] = useState('');
  const [almacenFilter, setAlmacenFilter] = useState('todos');
  const [categoryFilter, setCategoryFilter] = useState('todos');
  const [onlyLowStock, setOnlyLowStock] = useState(false);

  // New Item Modal
  const [showItemModal, setShowItemModal] = useState(false);
  const [newCodigo, setNewCodigo] = useState(`ITM-${Date.now().toString().slice(-4)}`);
  const [newNombre, setNewNombre] = useState('');
  const [newEsEquip, setNewEsEquip] = useState(true);
  const [newPrecio, setNewPrecio] = useState(100);
  const [newStock, setNewStock] = useState(10);
  const [newStockMin, setNewStockMin] = useState(5);
  const [newUnidad, setNewUnidad] = useState('u');
  const [newSerie, setNewSerie] = useState('');
  const [newAlmacenId, setNewAlmacenId] = useState(almacenes[0]?.id || '');
  const [newDesc, setNewDesc] = useState('');

  // Movement Modal
  const [showMovementModal, setShowMovementModal] = useState(false);
  const [movItemId, setMovItemId] = useState(quickRestockItem ? quickRestockItem.id : items[0]?.id || '');
  const [movTipo, setMovTipo] = useState<MovementType>('entrada');
  const [movCantidad, setMovCantidad] = useState(5);
  const [movMotivo, setMovMotivo] = useState('Reabastecimiento de stock');

  React.useEffect(() => {
    if (quickRestockItem) {
      setMovItemId(quickRestockItem.id);
      setMovTipo('entrada');
      setMovMotivo('Reposición por alerta de stock bajo');
      setShowMovementModal(true);
    }
  }, [quickRestockItem]);

  const filteredItems = items.filter((item) => {
    const matchesSearch =
      item.nombre.toLowerCase().includes(searchTerm.toLowerCase()) ||
      item.codigo.toLowerCase().includes(searchTerm.toLowerCase()) ||
      (item.numero_serie && item.numero_serie.toLowerCase().includes(searchTerm.toLowerCase()));

    const matchesAlmacen = almacenFilter === 'todos' || item.almacen_id === almacenFilter;
    const matchesCategory = categoryFilter === 'todos' || 
      (categoryFilter === 'equipamiento' && item.es_equipamiento) ||
      (categoryFilter === 'material' && !item.es_equipamiento);

    const matchesStock = !onlyLowStock || item.stock_actual <= item.stock_minimo;

    return matchesSearch && matchesAlmacen && matchesCategory && matchesStock;
  });

  const handleCreateItem = (e: React.FormEvent) => {
    e.preventDefault();
    if (!newNombre || !newCodigo) return;

    const alm = almacenes.find(a => a.id === newAlmacenId) || almacenes[0];

    const newItem: ItemInventario = {
      id: `itm-${Date.now()}`,
      codigo: newCodigo,
      nombre: newNombre,
      categoria_id: newEsEquip ? 'cat-eq' : 'cat-mat',
      categoria_nombre: newEsEquip ? 'Equipamiento' : 'Material',
      descripcion: newDesc,
      unidad_medida: newUnidad,
      precio_unitario: newPrecio,
      stock_actual: newStock,
      stock_minimo: newStockMin,
      es_equipamiento: newEsEquip,
      numero_serie: newSerie || undefined,
      almacen_id: alm.id,
      almacen_nombre: alm.nombre
    };

    onAddItem(newItem);
    setShowItemModal(false);
    setNewNombre('');
    setNewDesc('');
    setNewSerie('');
  };

  const handleCreateMovimiento = (e: React.FormEvent) => {
    e.preventDefault();
    const item = items.find(i => i.id === movItemId);
    if (!item) return;

    const newMov: InventarioMovimiento = {
      id: `mov-${Date.now()}`,
      fecha: new Date().toISOString().replace('T', ' ').slice(0, 16),
      item_id: item.id,
      item_nombre: item.nombre,
      almacen_id: item.almacen_id,
      almacen_nombre: item.almacen_nombre,
      tipo: movTipo,
      cantidad: movCantidad,
      costo_unitario: item.precio_unitario,
      motivo: movMotivo,
      usuario: 'Administrador'
    };

    onAddMovimiento(newMov);
    setShowMovementModal(false);
    onClearQuickRestock();
  };

  return (
    <div className="space-y-6">
      {/* Header Bar */}
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h2 className="text-xl font-bold text-white tracking-tight flex items-center gap-2">
            <Boxes className="h-5 w-5 text-teal-400" />
            Control de Inventario & Materiales
          </h2>
          <p className="text-xs text-slate-400">
            Catálogo multialmacén, control de números de serie y kardex de movimientos.
          </p>
        </div>

        <div className="flex items-center gap-2">
          <button
            onClick={() => {
              onClearQuickRestock();
              setShowMovementModal(true);
            }}
            className="flex items-center gap-1.5 rounded-lg border border-slate-700 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold px-3 py-2 text-xs transition-colors cursor-pointer"
          >
            <History className="h-4 w-4 text-teal-400" />
            Registrar Movimiento
          </button>
          <button
            onClick={() => setShowItemModal(true)}
            className="flex items-center gap-1.5 rounded-lg bg-teal-500 hover:bg-teal-400 text-slate-950 font-bold px-4 py-2 text-xs transition-all shadow-md shadow-teal-500/20 cursor-pointer"
          >
            <Plus className="h-4 w-4" />
            Nuevo Ítem
          </button>
        </div>
      </div>

      {/* Tabs */}
      <div className="flex items-center gap-2 border-b border-slate-800 pb-2">
        <button
          onClick={() => setActiveTab('items')}
          className={`flex items-center gap-2 px-4 py-1.5 rounded-lg text-xs font-semibold transition-all cursor-pointer ${
            activeTab === 'items'
              ? 'bg-teal-500/15 text-teal-300 border border-teal-500/30'
              : 'text-slate-400 hover:text-white'
          }`}
        >
          <Boxes className="h-3.5 w-3.5" />
          <span>Catálogo de Artículos ({items.length})</span>
        </button>
        <button
          onClick={() => setActiveTab('movimientos')}
          className={`flex items-center gap-2 px-4 py-1.5 rounded-lg text-xs font-semibold transition-all cursor-pointer ${
            activeTab === 'movimientos'
              ? 'bg-teal-500/15 text-teal-300 border border-teal-500/30'
              : 'text-slate-400 hover:text-white'
          }`}
        >
          <History className="h-3.5 w-3.5" />
          <span>Historial de Movimientos ({movimientos.length})</span>
        </button>
      </div>

      {activeTab === 'items' ? (
        <>
          {/* Filters Bar */}
          <div className="grid grid-cols-1 sm:grid-cols-4 gap-3 rounded-xl border border-slate-800 bg-slate-900/60 p-3">
            <div className="relative">
              <Search className="absolute left-3 top-2.5 h-3.5 w-3.5 text-slate-400" />
              <input
                type="text"
                placeholder="Buscar por código, serie, nombre..."
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
                className="w-full rounded-lg border border-slate-800 bg-slate-950 pl-9 pr-3 py-1.5 text-xs text-slate-200 placeholder-slate-500 focus:border-teal-500 focus:outline-none"
              />
            </div>

            <div>
              <select
                value={almacenFilter}
                onChange={(e) => setAlmacenFilter(e.target.value)}
                className="w-full rounded-lg border border-slate-800 bg-slate-950 px-2.5 py-1.5 text-xs text-slate-300 focus:border-teal-500 focus:outline-none"
              >
                <option value="todos">Todos los almacenes</option>
                {almacenes.map(a => (
                  <option key={a.id} value={a.id}>{a.nombre}</option>
                ))}
              </select>
            </div>

            <div>
              <select
                value={categoryFilter}
                onChange={(e) => setCategoryFilter(e.target.value)}
                className="w-full rounded-lg border border-slate-800 bg-slate-950 px-2.5 py-1.5 text-xs text-slate-300 focus:border-teal-500 focus:outline-none"
              >
                <option value="todos">Todas las categorías</option>
                <option value="equipamiento">Equipamiento</option>
                <option value="material">Materiales / Consumibles</option>
              </select>
            </div>

            <div className="flex items-center">
              <label className="flex items-center gap-2 text-xs text-slate-300 cursor-pointer select-none">
                <input
                  type="checkbox"
                  checked={onlyLowStock}
                  onChange={(e) => setOnlyLowStock(e.target.checked)}
                  className="rounded border-slate-700 bg-slate-950 text-amber-500 focus:ring-amber-500"
                />
                <span className="flex items-center gap-1 text-amber-400 font-medium">
                  <AlertTriangle className="h-3.5 w-3.5" />
                  Solo con stock bajo
                </span>
              </label>
            </div>
          </div>

          {/* Items Table */}
          <div className="rounded-xl border border-slate-800 bg-slate-900/70 overflow-hidden shadow-sm">
            <div className="overflow-x-auto">
              <table className="w-full text-left text-xs">
                <thead className="border-b border-slate-800 bg-slate-950/80 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
                  <tr>
                    <th className="px-4 py-3">Código</th>
                    <th className="px-4 py-3">Nombre & Descripción</th>
                    <th className="px-4 py-3">Categoría</th>
                    <th className="px-4 py-3">Almacén</th>
                    <th className="px-4 py-3 text-right">Precio Unit.</th>
                    <th className="px-4 py-3 text-center">Stock Actual</th>
                    <th className="px-4 py-3 text-center">Stock Mín.</th>
                    <th className="px-4 py-3 text-center">Acciones</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-slate-800 text-slate-200">
                  {filteredItems.length > 0 ? (
                    filteredItems.map((item) => {
                      const isLow = item.stock_actual <= item.stock_minimo;
                      return (
                        <tr key={item.id} className="hover:bg-slate-800/40 transition-colors">
                          <td className="px-4 py-3 font-mono font-bold text-teal-400">
                            {item.codigo}
                            {item.numero_serie && (
                              <div className="text-[10px] text-slate-400 font-normal">S/N: {item.numero_serie}</div>
                            )}
                          </td>
                          <td className="px-4 py-3 max-w-xs">
                            <div className="font-semibold text-white">{item.nombre}</div>
                            {item.descripcion && (
                              <div className="text-[11px] text-slate-400 line-clamp-1">{item.descripcion}</div>
                            )}
                          </td>
                          <td className="px-4 py-3">
                            <span className={`inline-block px-2 py-0.5 rounded text-[10px] font-semibold uppercase border ${
                              item.es_equipamiento
                                ? 'bg-teal-500/10 text-teal-400 border-teal-500/20'
                                : 'bg-purple-500/10 text-purple-400 border-purple-500/20'
                            }`}>
                              {item.categoria_nombre}
                            </span>
                          </td>
                          <td className="px-4 py-3 text-slate-300">
                            {item.almacen_nombre}
                          </td>
                          <td className="px-4 py-3 text-right font-mono font-medium text-white">
                            ${item.precio_unitario.toFixed(2)}
                          </td>
                          <td className="px-4 py-3 text-center">
                            <span className={`inline-flex items-center gap-1 font-mono font-bold px-2 py-0.5 rounded text-xs ${
                              isLow
                                ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30'
                                : 'text-slate-100'
                            }`}>
                              {isLow && <AlertTriangle className="h-3 w-3" />}
                              {item.stock_actual} {item.unidad_medida}
                            </span>
                          </td>
                          <td className="px-4 py-3 text-center font-mono text-slate-400">
                            {item.stock_minimo} {item.unidad_medida}
                          </td>
                          <td className="px-4 py-3 text-center">
                            <button
                              onClick={() => {
                                setMovItemId(item.id);
                                setMovTipo('entrada');
                                setShowMovementModal(true);
                              }}
                              className="rounded bg-teal-500/10 hover:bg-teal-500/20 text-teal-400 border border-teal-500/20 px-2 py-1 text-[11px] font-semibold transition-colors cursor-pointer"
                            >
                              + Ajuste
                            </button>
                          </td>
                        </tr>
                      );
                    })
                  ) : (
                    <tr>
                      <td colSpan={8} className="px-4 py-8 text-center text-slate-400">
                        No se encontraron ítems con los filtros aplicados.
                      </td>
                    </tr>
                  )}
                </tbody>
              </table>
            </div>
          </div>
        </>
      ) : (
        /* Movimientos Table */
        <div className="rounded-xl border border-slate-800 bg-slate-900/70 overflow-hidden shadow-sm">
          <div className="overflow-x-auto">
            <table className="w-full text-left text-xs">
              <thead className="border-b border-slate-800 bg-slate-950/80 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
                <tr>
                  <th className="px-4 py-3">Fecha</th>
                  <th className="px-4 py-3">Tipo</th>
                  <th className="px-4 py-3">Artículo</th>
                  <th className="px-4 py-3">Almacén</th>
                  <th className="px-4 py-3 text-right">Cantidad</th>
                  <th className="px-4 py-3">Motivo / Documento</th>
                  <th className="px-4 py-3">Responsable</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-800 text-slate-200">
                {movimientos.map((mov) => {
                  const isEntrada = mov.tipo === 'entrada' || mov.tipo === 'devolucion';
                  return (
                    <tr key={mov.id} className="hover:bg-slate-800/40 transition-colors">
                      <td className="px-4 py-3 font-mono text-slate-400">
                        {mov.fecha}
                      </td>
                      <td className="px-4 py-3">
                        <span className={`inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold uppercase ${
                          isEntrada
                            ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30'
                            : 'bg-rose-500/20 text-rose-400 border border-rose-500/30'
                        }`}>
                          {isEntrada ? <ArrowDownLeft className="h-3 w-3" /> : <ArrowUpRight className="h-3 w-3" />}
                          {mov.tipo}
                        </span>
                      </td>
                      <td className="px-4 py-3 font-medium text-white">
                        {mov.item_nombre}
                      </td>
                      <td className="px-4 py-3 text-slate-300">
                        {mov.almacen_nombre}
                      </td>
                      <td className={`px-4 py-3 text-right font-mono font-bold ${isEntrada ? 'text-emerald-400' : 'text-rose-400'}`}>
                        {isEntrada ? `+${mov.cantidad}` : `-${mov.cantidad}`}
                      </td>
                      <td className="px-4 py-3 text-slate-400">
                        {mov.motivo || 'Operación ordinaria'}
                      </td>
                      <td className="px-4 py-3 text-slate-400">
                        {mov.usuario}
                      </td>
                    </tr>
                  );
                })}
              </tbody>
            </table>
          </div>
        </div>
      )}

      {/* Movement Modal */}
      {showMovementModal && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 p-4 backdrop-blur-sm">
          <div className="w-full max-w-lg rounded-2xl border border-slate-800 bg-slate-900 shadow-2xl overflow-hidden">
            <div className="flex items-center justify-between border-b border-slate-800 px-6 py-4 bg-slate-950/60">
              <h3 className="text-base font-bold text-white flex items-center gap-2">
                <History className="h-4 w-4 text-teal-400" />
                Registrar Movimiento de Inventario
              </h3>
              <button
                onClick={() => {
                  setShowMovementModal(false);
                  onClearQuickRestock();
                }}
                className="text-slate-400 hover:text-white"
              >
                <X className="h-5 w-5" />
              </button>
            </div>

            <form onSubmit={handleCreateMovimiento} className="p-6 space-y-4">
              <div>
                <label className="block text-[11px] font-medium text-slate-400 mb-1">
                  Artículo de Inventario
                </label>
                <select
                  value={movItemId}
                  onChange={(e) => setMovItemId(e.target.value)}
                  className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-teal-500 focus:outline-none"
                >
                  {items.map((i) => (
                    <option key={i.id} value={i.id}>
                      [{i.codigo}] {i.nombre} (Stock actual: {i.stock_actual} {i.unidad_medida})
                    </option>
                  ))}
                </select>
              </div>

              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">
                    Tipo de Operación
                  </label>
                  <select
                    value={movTipo}
                    onChange={(e) => setMovTipo(e.target.value as MovementType)}
                    className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-teal-500 focus:outline-none capitalize"
                  >
                    <option value="entrada">Entrada (Compra/Recepción)</option>
                    <option value="salida">Salida (Instalación/Consumo)</option>
                    <option value="ajuste">Ajuste de Conteo</option>
                    <option value="devolucion">Devolución</option>
                  </select>
                </div>

                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">
                    Cantidad
                  </label>
                  <input
                    type="number"
                    min="1"
                    required
                    value={movCantidad}
                    onChange={(e) => setMovCantidad(parseFloat(e.target.value) || 1)}
                    className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs font-mono text-slate-200 focus:border-teal-500 focus:outline-none"
                  />
                </div>
              </div>

              <div>
                <label className="block text-[11px] font-medium text-slate-400 mb-1">
                  Motivo o Justificación
                </label>
                <input
                  type="text"
                  required
                  placeholder="Ej: Reabastecimiento lote mensual / Asignación a cuadrilla"
                  value={movMotivo}
                  onChange={(e) => setMovMotivo(e.target.value)}
                  className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-teal-500 focus:outline-none"
                />
              </div>

              <div className="flex justify-end gap-3 pt-3 border-t border-slate-800">
                <button
                  type="button"
                  onClick={() => {
                    setShowMovementModal(false);
                    onClearQuickRestock();
                  }}
                  className="rounded-lg border border-slate-700 bg-slate-800 px-4 py-2 text-xs font-medium text-slate-300 hover:bg-slate-700 cursor-pointer"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  className="rounded-lg bg-teal-500 hover:bg-teal-400 text-slate-950 px-5 py-2 text-xs font-bold transition-all shadow-md shadow-teal-500/20 cursor-pointer"
                >
                  Confirmar y Actualizar Stock
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      {/* New Item Modal */}
      {showItemModal && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 p-4 backdrop-blur-sm">
          <div className="w-full max-w-xl rounded-2xl border border-slate-800 bg-slate-900 shadow-2xl overflow-hidden">
            <div className="flex items-center justify-between border-b border-slate-800 px-6 py-4 bg-slate-950/60">
              <h3 className="text-base font-bold text-white flex items-center gap-2">
                <Boxes className="h-4 w-4 text-teal-400" />
                Registrar Nuevo Ítem en Catálogo
              </h3>
              <button
                onClick={() => setShowItemModal(false)}
                className="text-slate-400 hover:text-white"
              >
                <X className="h-5 w-5" />
              </button>
            </div>

            <form onSubmit={handleCreateItem} className="p-6 space-y-4 max-h-[80vh] overflow-y-auto">
              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">
                    Código Interno
                  </label>
                  <input
                    type="text"
                    required
                    value={newCodigo}
                    onChange={(e) => setNewCodigo(e.target.value)}
                    className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs font-mono text-slate-200 focus:border-teal-500 focus:outline-none"
                  />
                </div>
                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">
                    Tipo de Categoría
                  </label>
                  <select
                    value={newEsEquip ? 'eq' : 'mat'}
                    onChange={(e) => setNewEsEquip(e.target.value === 'eq')}
                    className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-teal-500 focus:outline-none"
                  >
                    <option value="eq">Equipamiento (Dispositivos / NVR / Cámaras)</option>
                    <option value="mat">Material (Cables / Conectores / Insumos)</option>
                  </select>
                </div>
              </div>

              <div>
                <label className="block text-[11px] font-medium text-slate-400 mb-1">
                  Nombre del Artículo
                </label>
                <input
                  type="text"
                  required
                  placeholder="Ej: Switch PoE+ 8 Puertos Gigabit"
                  value={newNombre}
                  onChange={(e) => setNewNombre(e.target.value)}
                  className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-teal-500 focus:outline-none"
                />
              </div>

              <div className="grid grid-cols-3 gap-3">
                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">
                    Precio Unitario ($)
                  </label>
                  <input
                    type="number"
                    min="0"
                    step="0.01"
                    required
                    value={newPrecio}
                    onChange={(e) => setNewPrecio(parseFloat(e.target.value) || 0)}
                    className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs font-mono text-slate-200 focus:border-teal-500 focus:outline-none"
                  />
                </div>
                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">
                    Stock Inicial
                  </label>
                  <input
                    type="number"
                    min="0"
                    required
                    value={newStock}
                    onChange={(e) => setNewStock(parseFloat(e.target.value) || 0)}
                    className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs font-mono text-slate-200 focus:border-teal-500 focus:outline-none"
                  />
                </div>
                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">
                    Stock Mínimo (Alerta)
                  </label>
                  <input
                    type="number"
                    min="0"
                    required
                    value={newStockMin}
                    onChange={(e) => setNewStockMin(parseFloat(e.target.value) || 0)}
                    className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs font-mono text-slate-200 focus:border-teal-500 focus:outline-none"
                  />
                </div>
              </div>

              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">
                    Almacén Asignado
                  </label>
                  <select
                    value={newAlmacenId}
                    onChange={(e) => setNewAlmacenId(e.target.value)}
                    className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-teal-500 focus:outline-none"
                  >
                    {almacenes.map(a => (
                      <option key={a.id} value={a.id}>{a.nombre}</option>
                    ))}
                  </select>
                </div>
                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">
                    Número de Serie (Opcional)
                  </label>
                  <input
                    type="text"
                    placeholder="Ej: HK-9821-SN"
                    value={newSerie}
                    onChange={(e) => setNewSerie(e.target.value)}
                    className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs font-mono text-slate-200 focus:border-teal-500 focus:outline-none"
                  />
                </div>
              </div>

              <div>
                <label className="block text-[11px] font-medium text-slate-400 mb-1">
                  Descripción Técnica
                </label>
                <textarea
                  rows={2}
                  placeholder="Especificaciones técnicas relevantes..."
                  value={newDesc}
                  onChange={(e) => setNewDesc(e.target.value)}
                  className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-teal-500 focus:outline-none"
                />
              </div>

              <div className="flex justify-end gap-3 pt-3 border-t border-slate-800">
                <button
                  type="button"
                  onClick={() => setShowItemModal(false)}
                  className="rounded-lg border border-slate-700 bg-slate-800 px-4 py-2 text-xs font-medium text-slate-300 hover:bg-slate-700 cursor-pointer"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  className="rounded-lg bg-teal-500 hover:bg-teal-400 text-slate-950 px-5 py-2 text-xs font-bold transition-all shadow-md shadow-teal-500/20 cursor-pointer"
                >
                  Guardar Ítem
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
};
