import React, { useState } from 'react';
import { CategoriaItem } from '../types';
import { Layers, Plus, Edit2, Trash2, X, Boxes, ShieldCheck, Cpu, Wrench } from 'lucide-react';

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
  const [descripcion, setDescripcion] = useState('');
  const [tipo, setTipo] = useState<CategoriaItem['tipo']>('material');
  const [activo, setActivo] = useState(true);
  const [searchTerm, setSearchTerm] = useState('');

  const handleOpenAdd = () => {
    setEditingCat(null);
    setNombre('');
    setDescripcion('');
    setTipo('equipamiento');
    setActivo(true);
    setIsModalOpen(true);
  };

  const handleOpenEdit = (c: CategoriaItem) => {
    setEditingCat(c);
    setNombre(c.nombre);
    setDescripcion(c.descripcion || '');
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
        descripcion,
        tipo,
        activo,
      });
    } else {
      onAddCategoria({
        nombre,
        descripcion,
        tipo,
        activo,
      });
    }
    setIsModalOpen(false);
  };

  const filteredCategorias = categorias.filter((c) => {
    const matchesSearch =
      c.nombre.toLowerCase().includes(searchTerm.toLowerCase()) ||
      (c.descripcion && c.descripcion.toLowerCase().includes(searchTerm.toLowerCase()));
    return matchesSearch;
  });

  return (
    <div className="space-y-6 max-w-6xl">
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h2 className="text-xl font-bold text-white tracking-tight flex items-center gap-2">
            <Layers className="h-5 w-5 text-sky-400" />
            Categorías de Ítems e Inventario DataPlus
          </h2>
          <p className="text-xs text-slate-400">
            Clasificación profesional de equipamiento y materiales según el objeto social (CCTV, SACI, Redes 4G, Control de Acceso y Cableado).
          </p>
        </div>

        <div className="flex items-center gap-3 w-full sm:w-auto">
          <input
            type="text"
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
            placeholder="Buscar categoría..."
            className="rounded-lg border border-slate-800 bg-slate-900 px-3 py-1.5 text-xs text-slate-200 placeholder-slate-500 focus:border-sky-500 focus:outline-none w-full sm:w-48"
          />
          <button
            onClick={handleOpenAdd}
            className="flex items-center gap-2 rounded-lg bg-sky-500 hover:bg-sky-400 text-slate-950 font-bold px-4 py-2 text-xs transition-all shadow-md shadow-sky-500/20 cursor-pointer whitespace-nowrap"
          >
            <Plus className="h-4 w-4 stroke-[2.5]" />
            Nueva Categoría
          </button>
        </div>
      </div>

      <div className="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/50 shadow-sm">
        <table className="w-full text-left text-xs text-slate-300">
          <thead className="bg-slate-950/80 text-[11px] font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-800">
            <tr>
              <th className="py-3.5 px-4">Categoría & Descripción</th>
              <th className="py-3.5 px-4">Tipo</th>
              <th className="py-3.5 px-4">Estado</th>
              <th className="py-3.5 px-4">Ítems Registrados</th>
              <th className="py-3.5 px-4 text-right">Acciones</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-slate-800/80">
            {filteredCategorias.map((c) => (
              <tr key={c.id} className="hover:bg-slate-800/40 transition-colors">
                <td className="py-3 px-4">
                  <div className="flex items-start gap-2.5">
                    <div className="p-1.5 rounded-lg bg-sky-500/10 border border-sky-500/20 text-sky-400 mt-0.5">
                      {c.tipo === 'equipamiento' ? (
                        <Cpu className="h-4 w-4" />
                      ) : c.tipo === 'material' ? (
                        <Boxes className="h-4 w-4" />
                      ) : (
                        <ShieldCheck className="h-4 w-4" />
                      )}
                    </div>
                    <div>
                      <span className="font-bold text-white text-xs block">{c.nombre}</span>
                      {c.descripcion && (
                        <span className="text-[11px] text-slate-400 block mt-0.5 line-clamp-1">
                          {c.descripcion}
                        </span>
                      )}
                    </div>
                  </div>
                </td>
                <td className="py-3 px-4">
                  <span
                    className={`inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold uppercase tracking-wider ${
                      c.tipo === 'equipamiento'
                        ? 'bg-purple-500/10 text-purple-400 border border-purple-500/20'
                        : c.tipo === 'material'
                        ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20'
                        : 'bg-amber-500/10 text-amber-400 border border-amber-500/20'
                    }`}
                  >
                    {c.tipo}
                  </span>
                </td>
                <td className="py-3 px-4">
                  <span
                    className={`inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold ${
                      c.activo
                        ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20'
                        : 'bg-slate-800 text-slate-400'
                    }`}
                  >
                    {c.activo ? 'Activo' : 'Inactivo'}
                  </span>
                </td>
                <td className="py-3 px-4 font-mono font-bold text-sky-400">
                  {c.items_count || 0} ítems
                </td>
                <td className="py-3 px-4 text-right">
                  <div className="flex items-center justify-end gap-1.5">
                    <button
                      onClick={() => handleOpenEdit(c)}
                      title="Editar"
                      className="rounded p-1 text-slate-400 hover:bg-slate-800 hover:text-sky-400 transition-colors"
                    >
                      <Edit2 className="h-4 w-4" />
                    </button>
                    <button
                      onClick={() => onDeleteCategoria(c.id)}
                      title="Eliminar"
                      className="rounded p-1 text-slate-400 hover:bg-slate-800 hover:text-rose-400 transition-colors"
                    >
                      <Trash2 className="h-4 w-4" />
                    </button>
                  </div>
                </td>
              </tr>
            ))}
            {filteredCategorias.length === 0 && (
              <tr>
                <td colSpan={5} className="py-8 text-center text-slate-500">
                  No se encontraron categorías que coincidan con la búsqueda.
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </div>

      {isModalOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4">
          <div className="relative w-full max-w-lg rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-2xl">
            <div className="flex items-center justify-between border-b border-slate-800 pb-3 mb-4">
              <h3 className="text-sm font-bold text-white flex items-center gap-2">
                <Layers className="h-4 w-4 text-sky-400" />
                {editingCat ? 'Editar Categoría de Inventario' : 'Nueva Categoría de Inventario'}
              </h3>
              <button onClick={() => setIsModalOpen(false)} className="text-slate-400 hover:text-white">
                <X className="h-4 w-4" />
              </button>
            </div>

            <form onSubmit={handleSubmit} className="space-y-4">
              <div>
                <label className="block text-[11px] font-medium text-slate-400 mb-1">
                  Nombre de Categoría *
                </label>
                <input
                  type="text"
                  required
                  value={nombre}
                  onChange={(e) => setNombre(e.target.value)}
                  placeholder="Ej: CCTV - Cámaras IP, SACI - Sensores PIR, Cableado UTP Cat 6..."
                  className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-sky-500 focus:outline-none"
                />
              </div>

              <div>
                <label className="block text-[11px] font-medium text-slate-400 mb-1">
                  Descripción / Dispositivos Incluidos
                </label>
                <textarea
                  rows={3}
                  value={descripcion}
                  onChange={(e) => setDescripcion(e.target.value)}
                  placeholder="Ej: Cámaras IP, domos, tubulares bullet, PTZ, grabadores NVR, cables, accesorios..."
                  className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-sky-500 focus:outline-none resize-none"
                />
              </div>

              <div>
                <label className="block text-[11px] font-medium text-slate-400 mb-1">
                  Tipo de Ítem
                </label>
                <select
                  value={tipo}
                  onChange={(e) => setTipo(e.target.value as any)}
                  className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-sky-500 focus:outline-none"
                >
                  <option value="equipamiento">
                    Equipamiento (Cámaras IP, NVRs, Centrales SACI, Routers 4G, Switches)
                  </option>
                  <option value="material">
                    Material (Cables UTP, conectores RJ45, tubería conduit, cajas de pase, tornillería)
                  </option>
                  <option value="ambos">
                    Ambos / Especial (Sistemas de energía UPS, EPI y protección técnica)
                  </option>
                </select>
              </div>

              <label className="flex items-center gap-2 cursor-pointer">
                <input
                  type="checkbox"
                  checked={activo}
                  onChange={(e) => setActivo(e.target.checked)}
                  className="rounded border-slate-700 bg-slate-950 text-sky-500 focus:ring-sky-500"
                />
                <span className="text-xs text-slate-300">Categoría Activa en Inventario</span>
              </label>

              <div className="flex justify-end gap-2 pt-3 border-t border-slate-800">
                <button
                  type="button"
                  onClick={() => setIsModalOpen(false)}
                  className="rounded-lg border border-slate-700 bg-slate-800 px-3 py-1.5 text-xs text-slate-300 hover:bg-slate-700 transition-colors"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  className="rounded-lg bg-sky-500 hover:bg-sky-400 px-4 py-1.5 text-xs font-bold text-slate-950 transition-colors"
                >
                  Guardar Categoría
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
};
