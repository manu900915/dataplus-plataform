import React, { useState } from 'react';
import { Tag, Plus, Check, Trash2, Edit2, FolderKanban, X } from 'lucide-react';
import { TipoProyecto } from '../types';

interface TiposProyectoViewProps {
  tipos: TipoProyecto[];
  onAddTipo: (tipo: Omit<TipoProyecto, 'id' | 'proyectos_count'>) => void;
  onUpdateTipo: (tipo: TipoProyecto) => void;
  onDeleteTipo: (id: string) => void;
}

export const TiposProyectoView: React.FC<TiposProyectoViewProps> = ({
  tipos,
  onAddTipo,
  onUpdateTipo,
  onDeleteTipo,
}) => {
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [editingTipo, setEditingTipo] = useState<TipoProyecto | null>(null);
  const [nombre, setNombre] = useState('');
  const [descripcion, setDescripcion] = useState('');
  const [activo, setActivo] = useState(true);

  const handleOpenAdd = () => {
    setEditingTipo(null);
    setNombre('');
    setDescripcion('');
    setActivo(true);
    setIsModalOpen(true);
  };

  const handleOpenEdit = (t: TipoProyecto) => {
    setEditingTipo(t);
    setNombre(t.nombre);
    setDescripcion(t.descripcion || '');
    setActivo(t.activo);
    setIsModalOpen(true);
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (editingTipo) {
      onUpdateTipo({
        ...editingTipo,
        nombre,
        descripcion,
        activo
      });
    } else {
      onAddTipo({
        nombre,
        descripcion,
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
            <Tag className="h-5 w-5 text-sky-400" />
            Tipos de Proyecto
          </h2>
          <p className="text-xs text-slate-400">
            Categorización formal de obras e investigaciones técnicas (Filament: TipoProyectoResource).
          </p>
        </div>

        <button
          onClick={handleOpenAdd}
          className="flex items-center gap-2 rounded-lg bg-sky-500 hover:bg-sky-400 text-slate-950 font-bold px-4 py-2 text-xs transition-all shadow-md shadow-sky-500/20 cursor-pointer"
        >
          <Plus className="h-4 w-4 stroke-[2.5]" />
          Nuevo Tipo
        </button>
      </div>

      <div className="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/50 shadow-sm">
        <table className="w-full text-left text-xs text-slate-300">
          <thead className="bg-slate-950/80 text-[11px] font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-800">
            <tr>
              <th className="py-3.5 px-4">Nombre</th>
              <th className="py-3.5 px-4">Descripción</th>
              <th className="py-3.5 px-4">Estado</th>
              <th className="py-3.5 px-4">Proyectos Asociados</th>
              <th className="py-3.5 px-4 text-right">Acciones</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-slate-800/80">
            {tipos.map((t) => (
              <tr key={t.id} className="hover:bg-slate-800/40 transition-colors">
                <td className="py-3 px-4 font-bold text-white flex items-center gap-2">
                  <FolderKanban className="h-4 w-4 text-sky-400" />
                  {t.nombre}
                </td>
                <td className="py-3 px-4 text-slate-400">{t.descripcion || 'Sin descripción'}</td>
                <td className="py-3 px-4">
                  <span className={`inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold ${
                    t.activo ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-slate-800 text-slate-400'
                  }`}>
                    {t.activo ? 'Activo' : 'Inactivo'}
                  </span>
                </td>
                <td className="py-3 px-4">
                  <span className="rounded bg-sky-500/10 text-sky-400 border border-sky-500/20 px-2 py-0.5 font-mono text-[11px] font-bold">
                    {t.proyectos_count} proyectos
                  </span>
                </td>
                <td className="py-3 px-4 text-right">
                  <div className="flex items-center justify-end gap-1.5">
                    <button
                      onClick={() => handleOpenEdit(t)}
                      className="rounded p-1 text-slate-400 hover:bg-slate-800 hover:text-sky-400 transition-colors"
                    >
                      <Edit2 className="h-4 w-4" />
                    </button>
                    <button
                      onClick={() => onDeleteTipo(t.id)}
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
                {editingTipo ? 'Editar Tipo de Proyecto' : 'Crear Tipo de Proyecto'}
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
                  placeholder="Ej: Instalación de CCTV, SACI, Automatización..."
                  className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-sky-500 focus:outline-none"
                />
              </div>
              <div>
                <label className="block text-[11px] font-medium text-slate-400 mb-1">Descripción</label>
                <textarea
                  rows={2}
                  value={descripcion}
                  onChange={(e) => setDescripcion(e.target.value)}
                  className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-sky-500 focus:outline-none"
                />
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
