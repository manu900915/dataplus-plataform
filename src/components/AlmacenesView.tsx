import React, { useState } from 'react';
import { 
  Warehouse, 
  Plus, 
  MapPin, 
  User, 
  Boxes, 
  DollarSign, 
  Package, 
  Check, 
  X,
  ExternalLink
} from 'lucide-react';
import { Almacen, ItemInventario } from '../types';
import { PROVINCIAS_CUBA } from '../data/mockData';

interface AlmacenesViewProps {
  almacenes: Almacen[];
  items: ItemInventario[];
  onAddAlmacen: (almacen: Almacen) => void;
  onNavigateToItems: () => void;
}

export const AlmacenesView: React.FC<AlmacenesViewProps> = ({
  almacenes,
  items,
  onAddAlmacen,
  onNavigateToItems,
}) => {
  const [showModal, setShowModal] = useState(false);
  const [nombre, setNombre] = useState('');
  const [provincia, setProvincia] = useState('La Habana');
  const [municipio, setMunicipio] = useState('Plaza de la Revolución');
  const [responsable, setResponsable] = useState('');

  const provinciasList = Object.keys(PROVINCIAS_CUBA);
  const municipiosList = PROVINCIAS_CUBA[provincia] || [];

  const handleProvinciaChange = (p: string) => {
    setProvincia(p);
    setMunicipio(PROVINCIAS_CUBA[p]?.[0] || '');
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!nombre) return;

    const newAlm: Almacen = {
      id: `alm-${Date.now()}`,
      nombre,
      provincia,
      municipio,
      responsable,
      total_items: 0,
      valor_estimado: 0
    };

    onAddAlmacen(newAlm);
    setShowModal(false);
    setNombre('');
    setResponsable('');
  };

  return (
    <div className="space-y-6">
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h2 className="text-xl font-bold text-white tracking-tight flex items-center gap-2">
            <Warehouse className="h-5 w-5 text-teal-400" />
            Almacenes & Puntos de Suministro
          </h2>
          <p className="text-xs text-slate-400">
            Control físico de depósitos territoriales y custodia de equipamiento.
          </p>
        </div>

        <button
          onClick={() => setShowModal(true)}
          className="flex items-center gap-1.5 rounded-lg bg-teal-500 hover:bg-teal-400 text-slate-950 font-bold px-4 py-2 text-xs transition-all shadow-md shadow-teal-500/20 cursor-pointer"
        >
          <Plus className="h-4 w-4" />
          Nuevo Almacén
        </button>
      </div>

      {/* Grid of Warehouses */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-5">
        {almacenes.map((alm) => {
          const almItems = items.filter(i => i.almacen_id === alm.id);
          const totalStockCount = almItems.reduce((acc, i) => acc + i.stock_actual, 0);
          const totalValuation = almItems.reduce((acc, i) => acc + (i.stock_actual * i.precio_unitario), 0);
          const lowStockCount = almItems.filter(i => i.stock_actual <= i.stock_minimo).length;

          return (
            <div
              key={alm.id}
              className="rounded-2xl border border-slate-800 bg-slate-900/70 p-5 shadow-sm hover:border-slate-700 transition-all flex flex-col justify-between"
            >
              <div>
                <div className="flex items-center justify-between pb-3 border-b border-slate-800">
                  <div className="flex items-center gap-2">
                    <div className="rounded-lg bg-teal-500/10 p-2 text-teal-400 border border-teal-500/20">
                      <Warehouse className="h-4 w-4" />
                    </div>
                    <span className="text-xs font-semibold uppercase tracking-wider text-teal-400">
                      Sede Activa
                    </span>
                  </div>
                  {lowStockCount > 0 && (
                    <span className="rounded bg-amber-500/20 px-2 py-0.5 text-[10px] font-bold text-amber-300 border border-amber-500/30">
                      {lowStockCount} alertas
                    </span>
                  )}
                </div>

                <h3 className="text-base font-bold text-white mt-3">{alm.nombre}</h3>

                <div className="mt-2 space-y-1.5 text-xs text-slate-300">
                  <div className="flex items-center gap-1.5">
                    <MapPin className="h-3.5 w-3.5 text-slate-400 flex-shrink-0" />
                    <span>{alm.municipio ? `${alm.municipio}, ` : ''}{alm.provincia}</span>
                  </div>
                  <div className="flex items-center gap-1.5">
                    <User className="h-3.5 w-3.5 text-slate-400 flex-shrink-0" />
                    <span>Responsable: <strong className="text-slate-200">{alm.responsable || 'No asignado'}</strong></span>
                  </div>
                </div>

                <div className="mt-4 grid grid-cols-2 gap-2 rounded-xl bg-slate-950/70 p-3 border border-slate-800/80">
                  <div>
                    <div className="text-[10px] text-slate-400 uppercase">Artículos en Custodia</div>
                    <div className="text-base font-mono font-bold text-white mt-0.5">
                      {totalStockCount} <span className="text-[11px] font-normal text-slate-400">({almItems.length} líneas)</span>
                    </div>
                  </div>
                  <div>
                    <div className="text-[10px] text-slate-400 uppercase">Valor Estimado</div>
                    <div className="text-base font-mono font-bold text-teal-400 mt-0.5">
                      ${totalValuation.toLocaleString('en-US', { minimumFractionDigits: 0 })}
                    </div>
                  </div>
                </div>
              </div>

              <div className="mt-5 pt-3 border-t border-slate-800 flex justify-end">
                <button
                  onClick={onNavigateToItems}
                  className="text-xs text-teal-400 hover:text-teal-300 font-medium flex items-center gap-1 cursor-pointer"
                >
                  Ver ítems en este almacén <ExternalLink className="h-3 w-3" />
                </button>
              </div>
            </div>
          );
        })}
      </div>

      {/* Modal */}
      {showModal && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 p-4 backdrop-blur-sm">
          <div className="w-full max-w-md rounded-2xl border border-slate-800 bg-slate-900 shadow-2xl overflow-hidden">
            <div className="flex items-center justify-between border-b border-slate-800 px-6 py-4 bg-slate-950/60">
              <h3 className="text-base font-bold text-white flex items-center gap-2">
                <Warehouse className="h-4 w-4 text-teal-400" />
                Registrar Nuevo Almacén
              </h3>
              <button
                onClick={() => setShowModal(false)}
                className="text-slate-400 hover:text-white"
              >
                <X className="h-5 w-5" />
              </button>
            </div>

            <form onSubmit={handleSubmit} className="p-6 space-y-4">
              <div>
                <label className="block text-[11px] font-medium text-slate-400 mb-1">
                  Nombre del Almacén
                </label>
                <input
                  type="text"
                  required
                  placeholder="Ej: Depósito Regional Cienfuegos"
                  value={nombre}
                  onChange={(e) => setNombre(e.target.value)}
                  className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-teal-500 focus:outline-none"
                />
              </div>

              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">
                    Provincia
                  </label>
                  <select
                    value={provincia}
                    onChange={(e) => handleProvinciaChange(e.target.value)}
                    className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-teal-500 focus:outline-none"
                  >
                    {provinciasList.map((p) => (
                      <option key={p} value={p}>{p}</option>
                    ))}
                  </select>
                </div>

                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">
                    Municipio
                  </label>
                  <select
                    value={municipio}
                    onChange={(e) => setMunicipio(e.target.value)}
                    className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-teal-500 focus:outline-none"
                  >
                    {municipiosList.map((m) => (
                      <option key={m} value={m}>{m}</option>
                    ))}
                  </select>
                </div>
              </div>

              <div>
                <label className="block text-[11px] font-medium text-slate-400 mb-1">
                  Responsable del Almacén
                </label>
                <input
                  type="text"
                  required
                  placeholder="Ej: Miguel Ángel Rivas"
                  value={responsable}
                  onChange={(e) => setResponsable(e.target.value)}
                  className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-teal-500 focus:outline-none"
                />
              </div>

              <div className="flex justify-end gap-3 pt-3 border-t border-slate-800">
                <button
                  type="button"
                  onClick={() => setShowModal(false)}
                  className="rounded-lg border border-slate-700 bg-slate-800 px-4 py-2 text-xs font-medium text-slate-300 hover:bg-slate-700 cursor-pointer"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  className="rounded-lg bg-teal-500 hover:bg-teal-400 text-slate-950 px-5 py-2 text-xs font-bold transition-all shadow-md shadow-teal-500/20 cursor-pointer"
                >
                  Guardar Almacén
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
};
