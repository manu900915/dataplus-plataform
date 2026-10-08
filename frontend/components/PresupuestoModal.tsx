import React, { useState } from 'react';
import { 
  X, 
  Plus, 
  Trash2, 
  DollarSign, 
  Package, 
  Check, 
  Boxes, 
  Calculator,
  Download
} from 'lucide-react';
import { Proyecto, LineaPresupuesto, ItemInventario, BudgetLineType } from '../types';

interface PresupuestoModalProps {
  proyecto: Proyecto;
  items: ItemInventario[];
  onClose: () => void;
  onSave: (updatedProyecto: Proyecto) => void;
}

export const PresupuestoModal: React.FC<PresupuestoModalProps> = ({
  proyecto,
  items,
  onClose,
  onSave,
}) => {
  const [lineas, setLineas] = useState<LineaPresupuesto[]>(proyecto.lineas_presupuesto || []);
  const [activeTab, setActiveTab] = useState<'todos' | BudgetLineType>('todos');
  const [selectedItemId, setSelectedItemId] = useState<string>('');
  const [tipoLinea, setTipoLinea] = useState<BudgetLineType>('equipamiento');
  const [descripcion, setDescripcion] = useState<string>('');
  const [cantidad, setCantidad] = useState<number>(1);
  const [costoUnitario, setCostoUnitario] = useState<number>(0);
  const [descontarInventario, setDescontarInventario] = useState<boolean>(true);

  // If item is selected from inventory dropdown, autofill details
  const handleItemSelect = (itemId: string) => {
    setSelectedItemId(itemId);
    const found = items.find(i => i.id === itemId);
    if (found) {
      setDescripcion(found.nombre);
      setCostoUnitario(found.precio_unitario);
      const tipo = found.es_equipamiento ? 'equipamiento' : 'material';
      setTipoLinea(tipo);
      setDescontarInventario(true);
    }
  };

  const handleTabChange = (tab: 'todos' | BudgetLineType) => {
    setActiveTab(tab);
    if (tab !== 'todos') {
      setTipoLinea(tab);
    }
  };

  const handleAddLinea = (e: React.FormEvent) => {
    e.preventDefault();
    if (!descripcion || cantidad <= 0 || costoUnitario < 0) return;

    const subtotal = Number((cantidad * costoUnitario).toFixed(2));
    const newLine: LineaPresupuesto = {
      id: `lp-${Date.now()}`,
      proyecto_id: proyecto.id,
      tipo_linea: tipoLinea,
      item_id: selectedItemId || undefined,
      descripcion,
      cantidad,
      costo_unitario: costoUnitario,
      subtotal,
      descontar_inventario: descontarInventario
    };

    setLineas([...lineas, newLine]);
    // Reset inputs
    setSelectedItemId('');
    setDescripcion('');
    setCantidad(1);
    setCostoUnitario(0);
  };

  const handleRemoveLinea = (id: string) => {
    setLineas(lineas.filter(l => l.id !== id));
  };

  const displayedLineas = activeTab === 'todos'
    ? lineas
    : lineas.filter(l => l.tipo_linea === activeTab);

  const getCountByTipo = (tipo: BudgetLineType) => lineas.filter(l => l.tipo_linea === tipo).length;

  const totalCalculado = lineas.reduce((acc, l) => acc + l.subtotal, 0);

  const handleSaveAll = () => {
    const updated: Proyecto = {
      ...proyecto,
      lineas_presupuesto: lineas,
      presupuesto_total: Number(totalCalculado.toFixed(2)),
      updated_at: new Date().toISOString()
    };
    onSave(updated);
    onClose();
  };

  const getTipoBadgeColor = (tipo: BudgetLineType) => {
    switch (tipo) {
      case 'equipamiento': return 'bg-teal-500/10 text-teal-400 border-teal-500/20';
      case 'material': return 'bg-sky-500/10 text-sky-400 border-sky-500/20';
      case 'mano_obra': return 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20';
      case 'transporte': return 'bg-amber-500/10 text-amber-400 border-amber-500/20';
      default: return 'bg-slate-500/10 text-slate-400 border-slate-500/20';
    }
  };

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 p-4 backdrop-blur-sm">
      <div className="flex flex-col w-full max-w-4xl max-h-[90vh] rounded-2xl border border-slate-800 bg-slate-900 shadow-2xl overflow-hidden">
        {/* Header */}
        <div className="flex items-center justify-between border-b border-slate-800 px-6 py-4 bg-slate-950/60">
          <div>
            <div className="flex items-center gap-2">
              <span className="font-mono text-xs font-semibold text-teal-400">{proyecto.codigo}</span>
              <span className="text-slate-400">•</span>
              <span className="text-xs text-slate-300">{proyecto.cliente_nombre || 'Sin cliente asignado'}</span>
            </div>
            <h2 className="text-lg font-bold text-white mt-0.5">
              Presupuesto & Desglose de Costos — {proyecto.nombre}
            </h2>
          </div>
          <button
            onClick={onClose}
            className="rounded-lg p-1.5 text-slate-400 hover:bg-slate-800 hover:text-white transition-colors cursor-pointer"
          >
            <X className="h-5 w-5" />
          </button>
        </div>

        {/* Content Body */}
        <div className="flex-1 overflow-y-auto p-6 space-y-6">
          {/* Add Line Form */}
          <form onSubmit={handleAddLinea} className="rounded-xl border border-slate-800 bg-slate-950/40 p-4 space-y-3">
            <div className="text-xs font-semibold uppercase tracking-wider text-slate-300 flex items-center gap-1.5">
              <Plus className="h-3.5 w-3.5 text-teal-400" />
              Agregar Línea de Presupuesto
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-12 gap-3">
              {/* Pick from existing inventory */}
              <div className="sm:col-span-4">
                <label className="block text-[11px] font-medium text-slate-400 mb-1">
                  Vincular Ítem de Inventario (Opcional)
                </label>
                <select
                  value={selectedItemId}
                  onChange={(e) => handleItemSelect(e.target.value)}
                  className="w-full rounded-lg border border-slate-800 bg-slate-900 px-3 py-1.5 text-xs text-slate-200 focus:border-teal-500 focus:outline-none"
                >
                  <option value="">-- Personalizado o mano de obra --</option>
                  {items.map((item) => (
                    <option key={item.id} value={item.id}>
                      [{item.codigo}] {item.nombre} (Stock: {item.stock_actual})
                    </option>
                  ))}
                </select>
              </div>

              {/* Type */}
              <div className="sm:col-span-3">
                <label className="block text-[11px] font-medium text-slate-400 mb-1">
                  Tipo de Línea
                </label>
                <select
                  value={tipoLinea}
                  onChange={(e) => setTipoLinea(e.target.value as BudgetLineType)}
                  className="w-full rounded-lg border border-slate-800 bg-slate-900 px-3 py-1.5 text-xs text-slate-200 focus:border-teal-500 focus:outline-none capitalize"
                >
                  <option value="equipamiento">Equipamiento</option>
                  <option value="material">Material</option>
                  <option value="mano_obra">Mano de Obra</option>
                  <option value="transporte">Transporte</option>
                  <option value="alimentacion">Alimentación</option>
                  <option value="otro">Otro</option>
                </select>
              </div>

              {/* Description */}
              <div className="sm:col-span-5">
                <label className="block text-[11px] font-medium text-slate-400 mb-1">
                  Descripción del Concepto
                </label>
                <input
                  type="text"
                  required
                  placeholder="Ej: Cámara Domo 4K Hikvision / Tendido Cat6"
                  value={descripcion}
                  onChange={(e) => setDescripcion(e.target.value)}
                  className="w-full rounded-lg border border-slate-800 bg-slate-900 px-3 py-1.5 text-xs text-slate-200 focus:border-teal-500 focus:outline-none"
                />
              </div>

              {/* Cantidad */}
              <div className="sm:col-span-3">
                <label className="block text-[11px] font-medium text-slate-400 mb-1">
                  Cantidad
                </label>
                <input
                  type="number"
                  min="0.1"
                  step="any"
                  required
                  value={cantidad}
                  onChange={(e) => setCantidad(parseFloat(e.target.value) || 0)}
                  className="w-full rounded-lg border border-slate-800 bg-slate-900 px-3 py-1.5 text-xs text-slate-200 font-mono focus:border-teal-500 focus:outline-none"
                />
              </div>

              {/* Costo Unitario */}
              <div className="sm:col-span-3">
                <label className="block text-[11px] font-medium text-slate-400 mb-1">
                  Costo Unitario ($)
                </label>
                <input
                  type="number"
                  min="0"
                  step="0.01"
                  required
                  value={costoUnitario}
                  onChange={(e) => setCostoUnitario(parseFloat(e.target.value) || 0)}
                  className="w-full rounded-lg border border-slate-800 bg-slate-900 px-3 py-1.5 text-xs text-slate-200 font-mono focus:border-teal-500 focus:outline-none"
                />
              </div>

              {/* Subtotal Preview */}
              <div className="sm:col-span-3 flex flex-col justify-end">
                <div className="text-[11px] text-slate-400 mb-1">Subtotal estimado</div>
                <div className="rounded-lg bg-slate-900 border border-slate-800 px-3 py-1.5 text-xs font-mono font-bold text-teal-400">
                  ${(cantidad * costoUnitario).toFixed(2)}
                </div>
              </div>

              {/* Descontar Inventario & Button */}
              <div className="sm:col-span-3 flex items-center justify-between gap-2 pt-5">
                <label className="flex items-center gap-1.5 text-[11px] text-slate-300 cursor-pointer">
                  <input
                    type="checkbox"
                    checked={descontarInventario}
                    onChange={(e) => setDescontarInventario(e.target.checked)}
                    className="rounded border-slate-700 bg-slate-900 text-teal-500 focus:ring-teal-500"
                  />
                  <span>Descontar stock</span>
                </label>

                <button
                  type="submit"
                  className="flex items-center gap-1 rounded-lg bg-teal-500 hover:bg-teal-400 text-slate-950 px-3 py-1.5 text-xs font-semibold transition-colors cursor-pointer"
                >
                  <Plus className="h-3.5 w-3.5" />
                  Agregar
                </button>
              </div>
            </div>
          </form>

          {/* Category Tabs & Table */}
          <div className="space-y-3">
            {/* Tabs Filter */}
            <div className="flex items-center gap-1.5 overflow-x-auto border-b border-slate-800 pb-2 text-xs">
              <button
                type="button"
                onClick={() => handleTabChange('todos')}
                className={`px-3 py-1.5 rounded-lg font-medium transition-colors cursor-pointer flex items-center gap-1.5 ${
                  activeTab === 'todos'
                    ? 'bg-teal-500 text-slate-950 font-bold'
                    : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60'
                }`}
              >
                <span>Todas</span>
                <span className={`text-[10px] px-1.5 py-0.2 rounded-full ${
                  activeTab === 'todos' ? 'bg-teal-700/30 text-slate-950 font-mono font-bold' : 'bg-slate-800 text-slate-400'
                }`}>
                  {lineas.length}
                </span>
              </button>

              <button
                type="button"
                onClick={() => handleTabChange('equipamiento')}
                className={`px-3 py-1.5 rounded-lg font-medium transition-colors cursor-pointer flex items-center gap-1.5 ${
                  activeTab === 'equipamiento'
                    ? 'bg-teal-500 text-slate-950 font-bold'
                    : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60'
                }`}
              >
                <span>Equipamiento</span>
                <span className={`text-[10px] px-1.5 py-0.2 rounded-full ${
                  activeTab === 'equipamiento' ? 'bg-teal-700/30 text-slate-950 font-mono font-bold' : 'bg-slate-800 text-slate-400'
                }`}>
                  {getCountByTipo('equipamiento')}
                </span>
              </button>

              <button
                type="button"
                onClick={() => handleTabChange('mano_obra')}
                className={`px-3 py-1.5 rounded-lg font-medium transition-colors cursor-pointer flex items-center gap-1.5 ${
                  activeTab === 'mano_obra'
                    ? 'bg-teal-500 text-slate-950 font-bold'
                    : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60'
                }`}
              >
                <span>Mano de Obra</span>
                <span className={`text-[10px] px-1.5 py-0.2 rounded-full ${
                  activeTab === 'mano_obra' ? 'bg-teal-700/30 text-slate-950 font-mono font-bold' : 'bg-slate-800 text-slate-400'
                }`}>
                  {getCountByTipo('mano_obra')}
                </span>
              </button>

              <button
                type="button"
                onClick={() => handleTabChange('material')}
                className={`px-3 py-1.5 rounded-lg font-medium transition-colors cursor-pointer flex items-center gap-1.5 ${
                  activeTab === 'material'
                    ? 'bg-teal-500 text-slate-950 font-bold'
                    : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60'
                }`}
              >
                <span>Materiales</span>
                <span className={`text-[10px] px-1.5 py-0.2 rounded-full ${
                  activeTab === 'material' ? 'bg-teal-700/30 text-slate-950 font-mono font-bold' : 'bg-slate-800 text-slate-400'
                }`}>
                  {getCountByTipo('material')}
                </span>
              </button>

              <button
                type="button"
                onClick={() => handleTabChange('transporte')}
                className={`px-3 py-1.5 rounded-lg font-medium transition-colors cursor-pointer flex items-center gap-1.5 ${
                  activeTab === 'transporte'
                    ? 'bg-teal-500 text-slate-950 font-bold'
                    : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60'
                }`}
              >
                <span>Transporte</span>
                <span className={`text-[10px] px-1.5 py-0.2 rounded-full ${
                  activeTab === 'transporte' ? 'bg-teal-700/30 text-slate-950 font-mono font-bold' : 'bg-slate-800 text-slate-400'
                }`}>
                  {getCountByTipo('transporte')}
                </span>
              </button>

              <button
                type="button"
                onClick={() => handleTabChange('alimentacion')}
                className={`px-3 py-1.5 rounded-lg font-medium transition-colors cursor-pointer flex items-center gap-1.5 ${
                  activeTab === 'alimentacion'
                    ? 'bg-teal-500 text-slate-950 font-bold'
                    : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60'
                }`}
              >
                <span>Alimentación</span>
                <span className={`text-[10px] px-1.5 py-0.2 rounded-full ${
                  activeTab === 'alimentacion' ? 'bg-teal-700/30 text-slate-950 font-mono font-bold' : 'bg-slate-800 text-slate-400'
                }`}>
                  {getCountByTipo('alimentacion')}
                </span>
              </button>
            </div>

            {/* Table of Lines */}
            <div className="rounded-xl border border-slate-800 bg-slate-950/60 overflow-hidden">
              <table className="w-full text-left text-xs">
                <thead className="border-b border-slate-800 bg-slate-900/80 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
                  <tr>
                    <th className="px-4 py-3">Tipo</th>
                    <th className="px-4 py-3">Descripción</th>
                    <th className="px-4 py-3 text-right">Cant.</th>
                    <th className="px-4 py-3 text-right">Costo Unit.</th>
                    <th className="px-4 py-3 text-right">Subtotal</th>
                    <th className="px-4 py-3 text-center">Inv.</th>
                    <th className="px-4 py-3 text-center">Acción</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-slate-800/80 text-slate-200">
                  {displayedLineas.length > 0 ? (
                    displayedLineas.map((linea) => (
                      <tr key={linea.id} className="hover:bg-slate-900/40">
                        <td className="px-4 py-3">
                          <span className={`inline-block px-2 py-0.5 rounded text-[10px] font-semibold uppercase border ${getTipoBadgeColor(linea.tipo_linea)}`}>
                            {linea.tipo_linea.replace('_', ' ')}
                          </span>
                        </td>
                        <td className="px-4 py-3 font-medium">
                          {linea.descripcion}
                        </td>
                        <td className="px-4 py-3 text-right font-mono">
                          {linea.cantidad}
                        </td>
                        <td className="px-4 py-3 text-right font-mono">
                          ${linea.costo_unitario.toFixed(2)}
                        </td>
                        <td className="px-4 py-3 text-right font-mono font-bold text-teal-400">
                          ${linea.subtotal.toFixed(2)}
                        </td>
                        <td className="px-4 py-3 text-center">
                          {linea.descontar_inventario ? (
                            <span className="inline-flex items-center text-[10px] text-teal-400 bg-teal-500/10 px-1.5 py-0.5 rounded">
                              Sí
                            </span>
                          ) : (
                            <span className="text-slate-500 text-[10px]">-</span>
                          )}
                        </td>
                        <td className="px-4 py-3 text-center">
                          <button
                            onClick={() => handleRemoveLinea(linea.id)}
                            className="text-slate-500 hover:text-rose-400 p-1 rounded transition-colors cursor-pointer"
                            title="Eliminar línea"
                          >
                            <Trash2 className="h-3.5 w-3.5" />
                          </button>
                        </td>
                      </tr>
                    ))
                  ) : (
                    <tr>
                      <td colSpan={7} className="px-4 py-8 text-center text-slate-400">
                        No hay conceptos en la categoría {activeTab === 'todos' ? 'seleccionada' : activeTab} aún.
                      </td>
                    </tr>
                  )}
                </tbody>
              </table>
            </div>
          </div>
        </div>

        {/* Footer with Totals and Action Buttons */}
        <div className="flex items-center justify-between border-t border-slate-800 px-6 py-4 bg-slate-950/80">
          <div className="flex items-center gap-3">
            <span className="text-xs text-slate-400">Presupuesto Calculado:</span>
            <span className="text-xl font-bold font-mono text-teal-400">
              ${totalCalculado.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
            </span>
            <span className="text-xs text-slate-400">({lineas.length} conceptos)</span>
          </div>

          <div className="flex items-center gap-3">
            <button
              type="button"
              onClick={onClose}
              className="rounded-lg border border-slate-700 bg-slate-800 px-4 py-2 text-xs font-medium text-slate-300 hover:bg-slate-700 transition-colors cursor-pointer"
            >
              Cancelar
            </button>
            <button
              type="button"
              onClick={handleSaveAll}
              className="flex items-center gap-1.5 rounded-lg bg-teal-500 hover:bg-teal-400 text-slate-950 px-5 py-2 text-xs font-bold transition-all shadow-md shadow-teal-500/20 cursor-pointer"
            >
              <Check className="h-4 w-4" />
              Guardar Presupuesto
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};
