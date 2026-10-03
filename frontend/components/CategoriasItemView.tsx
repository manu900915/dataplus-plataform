import React, { useState } from 'react';
import { Layers, Plus, Check, Trash2, Edit2, Boxes, X } from 'lucide-react';
import { CategoriaItem } from '../types';

interface CategoriasItemViewProps {
  categorias: CategoriaItem[];
  onAddCategoria: (cat: Omit<CategoriaItem, 'id' | 'items_count'>) => void;
  onUpdateCategoria: (cat: CategoriaItem) => void;
  onDeleteCategoria: (id: string) => void;
}

export const CategoriasItemView: React.FC<CategoriasItemViewProps> = ({
  categorias,
  onAddCategoria,
  onUpdateCategoria,
  onDeleteCategoria,
}) => {
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [editingCat, setEditingCat] = useState<CategoriaItem | null>(null);
  const [nombre, setNombre] = useState('');
  const [tipo, setTipo] = useState<CategoriaItem['tipo']>('material');
  const [activo, setActivo] = useState(true);

  const handleOpenAdd = () => {
    setEditingCat(null);
    setNombre('');
    setTipo('material');
    setActivo(true);
    setIsModalOpen(true);
  };

  const handleOpenEdit = (c: CategoriaItem) => {
    setEditingCat(c);
    setNombre(c.nombre);
    setTipo(c.tipo);
    setActivo(c.activo);
    setIsModalOpen(true);
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (editingCat) {
      onUpdateCategoria({
        ...editingCat,
        nombre,
        tipo,
        activo
      });
    } else {
      onAddCategoria({
        nombre,
        tipo,
        activo
      });
    }
    setIsModalOpen(false);
  };

  return (
    <div className="space-y-6 max-w-5xl">
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h2 className="text-xl font-bold text-white tracking-tight flex items-center gap-2">
            <Layers className="h-5 w-5 text-sky-400" />
            Categorías de Ítems e Inventario
          </h2>
          <p className="text-xs text-slate-400">
            Clasificación oficial de materiales y equipamiento (Filament: CategoriaItemResource).
          </p>
        </div>

        <button
          onClick={handleOpenAdd}
          className="flex items-center gap-2 rounded-lg bg-sky-500 hover:bg-sky-400 text-slate-950 font-bold px-4 py-2 text-xs transition-all shadow-md shadow-sky-500/20 cursor-pointer"
        >
          <Plus className="h-4 w-4 stroke-[2.5]" />
          Nueva Categoría
        </button>
      </div>

      <div className="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/50 shadow-sm">
        <table className="w-full text-left text-xs text-slate-300">
          <thead className="bg-slate-950/80 text-[11px] font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-800">
            <tr>
              <th className="py-3.5 px-4">Nombre de Categoría</th>
              <th className="py-3.5 px-4">Tipo</th>
              <th className="py-3.5 px-4">Estado</th>
              <th className="py-3.5 px-4">Ítems Registrados</th>
              <th className="py-3.5 px-4 text-right">Acciones</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-slate-800/80">
            {categorias.map((c) => (
              <tr key={c.id} className="hover:bg-slate-800/40 transition-colors">
                <td className="py-3 px-4 font-bold text-white flex items-center gap-2">
                  <Boxes className="h-4 w-4 text-sky-400" />
                  {c.nombre}
                </td>
                <td className="py-3 px-4">
                  <span className={`inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold uppercase ${
                    c.tipo === 'equipamiento'
                      ? 'bg-purple-500/10 text-purple-400 border border-purple-500/20'
                      : c.tipo === 'material'
                      ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20'
                      : 'bg-amber-500/10 text-amber-400 border border-amber-500/20'
                  }`}>
                    {c.tipo}
                  </span>
                </td>
                <td className="py-3 px-4">
                  <span className={`inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold ${
                    c.activo ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-slate-800 text-slate-400'
                  }`}>
                    {c.activo ? 'Activo' : 'Inactivo'}
                  </span>
                </td>
                <td className="py-3 px-4 font-mono font-bold text-sky-400">
                  {c.items_count} ítems
                </td>
                <td className="py-3 px-4 text-right">
                  <div className="flex items-center justify-end gap-1.5">
                    <button
                      onClick={() => handleOpenEdit(c)}
                      className="rounded p-1 text-slate-400 hover:bg-slate-800 hover:text-sky-400 transition-colors"
                    >
                      <Edit2 className="h-4 w-4" />
                    </button>
                    <button
                      onClick={() => onDeleteCategoria(c.id)}
                      className="rounded p-1 text-slate-400 hover:bg-slate-800 hover:text-rose-400 transition-colors"
                    >
                      <Trash2 className="h-4 w-4" />
                    </button>
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {isModalOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4">
          <div className="relative w-full max-w-md rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-2xl">
            <div className="flex items-center justify-between border-b border-slate-800 pb-3 mb-4">
              <h3 className="text-sm font-bold text-white">
                {editingCat ? 'Editar Categoría' : 'Nueva Categoría'}
              </h3>
              <button onClick={() => setIsModalOpen(false)} className="text-slate-400 hover:text-white">
                <X className="h-4 w-4" />
              </button>
            </div>
            <form onSubmit={handleSubmit} className="space-y-4">
              <div>
                <label className="block text-[11px] font-medium text-slate-400 mb-1">Nombre *</label>
                <input
                  type="text"
                  required
                  value={nombre}
                  onChange={(e) => setNombre(e.target.value)}
                  placeholder="Ej: Insecticidas, Equipos de aspersión, Sensores..."
                  className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-sky-500 focus:outline-none"
                />
              </div>
              <div>
                <label className="block text-[11px] font-medium text-slate-400 mb-1">Tipo de Ítem</label>
                <select
                  value={tipo}
                  onChange={(e) => setTipo(e.target.value as any)}
                  className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-sky-500 focus:outline-none"
                >
                  <option value="material">Material (Consumibles, insecticidas, cables)</option>
                  <option value="equipamiento">Equipamiento (Cámaras, NVRs, motomochilas, routers)</option>
                  <option value="ambos">Ambos / Protección (EPI)</option>
                </select>
              </div>
              <label className="flex items-center gap-2 cursor-pointer">
                <input
                  type="checkbox"
                  checked={activo}
                  onChange={(e) => setActivo(e.target.checked)}
                  className="rounded border-slate-700 bg-slate-950 text-sky-500 focus:ring-sky-500"
                />
                <span className="text-xs text-slate-300">Activo</span>
              </label>
              <div className="flex justify-end gap-2 pt-2 border-t border-slate-800">
                <button
                  type="button"
                  onClick={() => setIsModalOpen(false)}
                  className="rounded-lg border border-slate-700 bg-slate-800 px-3 py-1.5 text-xs text-slate-300"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  className="rounded-lg bg-sky-500 hover:bg-sky-400 px-4 py-1.5 text-xs font-bold text-slate-950"
                >
                  Guardar
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
};
