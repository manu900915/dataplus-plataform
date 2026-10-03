import React, { useState, useEffect } from 'react';
import { 
  Search, 
  X, 
  FolderKanban, 
  AlertCircle, 
  Boxes, 
  Users2, 
  Warehouse, 
  Truck,
  ArrowRight
} from 'lucide-react';
import { Proyecto, Incidencia, ItemInventario, Cliente } from '../types';

interface SearchModalProps {
  isOpen: boolean;
  onClose: () => void;
  proyectos: Proyecto[];
  incidencias: Incidencia[];
  items: ItemInventario[];
  clientes: Cliente[];
  onSelectAction: (module: string) => void;
}

export const SearchModal: React.FC<SearchModalProps> = ({
  isOpen,
  onClose,
  proyectos,
  incidencias,
  items,
  clientes,
  onSelectAction,
}) => {
  const [query, setQuery] = useState('');

  useEffect(() => {
    const handleKeyDown = (e: KeyboardEvent) => {
      if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
        e.preventDefault();
        if (isOpen) onClose();
        else setQuery('');
      } else if (e.key === 'Escape' && isOpen) {
        onClose();
      }
    };
    window.addEventListener('keydown', handleKeyDown);
    return () => window.removeEventListener('keydown', handleKeyDown);
  }, [isOpen, onClose]);

  if (!isOpen) return null;

  const q = query.toLowerCase().trim();

  const matchingProyectos = q ? proyectos.filter(p => p.nombre.toLowerCase().includes(q) || p.codigo.toLowerCase().includes(q)) : [];
  const matchingIncidencias = q ? incidencias.filter(i => i.titulo.toLowerCase().includes(q) || i.codigo.toLowerCase().includes(q)) : [];
  const matchingItems = q ? items.filter(it => it.nombre.toLowerCase().includes(q) || it.codigo.toLowerCase().includes(q)) : [];
  const matchingClientes = q ? clientes.filter(c => c.nombre.toLowerCase().includes(q) || c.codigo.toLowerCase().includes(q)) : [];

  const hasResults = matchingProyectos.length > 0 || matchingIncidencias.length > 0 || matchingItems.length > 0 || matchingClientes.length > 0;

  return (
    <div className="fixed inset-0 z-50 flex items-start justify-center pt-20 bg-slate-950/80 p-4 backdrop-blur-sm">
      <div className="w-full max-w-2xl rounded-2xl border border-slate-800 bg-slate-900 shadow-2xl overflow-hidden">
        {/* Search Input Box */}
        <div className="flex items-center px-4 border-b border-slate-800 bg-slate-950/70">
          <Search className="h-5 w-5 text-teal-400 mr-3" />
          <input
            type="text"
            placeholder="Buscar en proyectos, averías, inventario, clientes..."
            value={query}
            onChange={(e) => setQuery(e.target.value)}
            className="w-full py-4 text-sm text-slate-100 placeholder-slate-500 bg-transparent focus:outline-none"
            autoFocus
          />
          <button
            onClick={onClose}
            className="p-1 rounded text-slate-400 hover:text-white"
          >
            <X className="h-5 w-5" />
          </button>
        </div>

        {/* Results Area */}
        <div className="max-h-96 overflow-y-auto p-4 space-y-4">
          {!q ? (
            <div className="py-8 text-center text-xs text-slate-400">
              Escriba al menos 2 caracteres para buscar en todos los módulos de DataPlus.
            </div>
          ) : !hasResults ? (
            <div className="py-8 text-center text-xs text-slate-400">
              No se encontraron coincidencias para &quot;{query}&quot;.
            </div>
          ) : (
            <>
              {matchingProyectos.length > 0 && (
                <div>
                  <h4 className="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <FolderKanban className="h-3.5 w-3.5 text-teal-400" />
                    Proyectos ({matchingProyectos.length})
                  </h4>
                  <div className="space-y-1">
                    {matchingProyectos.map((p) => (
                      <button
                        key={p.id}
                        onClick={() => {
                          onSelectAction('proyectos');
                          onClose();
                        }}
                        className="w-full flex items-center justify-between p-2 rounded-lg bg-slate-950/50 hover:bg-slate-800 text-left transition-colors text-xs"
                      >
                        <div>
                          <span className="font-mono text-teal-400 mr-2">[{p.codigo}]</span>
                          <span className="text-white font-medium">{p.nombre}</span>
                        </div>
                        <ArrowRight className="h-3 w-3 text-slate-500" />
                      </button>
                    ))}
                  </div>
                </div>
              )}

              {matchingIncidencias.length > 0 && (
                <div>
                  <h4 className="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <AlertCircle className="h-3.5 w-3.5 text-rose-400" />
                    Incidencias ({matchingIncidencias.length})
                  </h4>
                  <div className="space-y-1">
                    {matchingIncidencias.map((i) => (
                      <button
                        key={i.id}
                        onClick={() => {
                          onSelectAction('incidencias');
                          onClose();
                        }}
                        className="w-full flex items-center justify-between p-2 rounded-lg bg-slate-950/50 hover:bg-slate-800 text-left transition-colors text-xs"
                      >
                        <div>
                          <span className="font-mono text-rose-400 mr-2">[{i.codigo}]</span>
                          <span className="text-white font-medium">{i.titulo}</span>
                        </div>
                        <ArrowRight className="h-3 w-3 text-slate-500" />
                      </button>
                    ))}
                  </div>
                </div>
              )}

              {matchingItems.length > 0 && (
                <div>
                  <h4 className="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <Boxes className="h-3.5 w-3.5 text-teal-400" />
                    Artículos de Inventario ({matchingItems.length})
                  </h4>
                  <div className="space-y-1">
                    {matchingItems.map((it) => (
                      <button
                        key={it.id}
                        onClick={() => {
                          onSelectAction('inventario');
                          onClose();
                        }}
                        className="w-full flex items-center justify-between p-2 rounded-lg bg-slate-950/50 hover:bg-slate-800 text-left transition-colors text-xs"
                      >
                        <div>
                          <span className="font-mono text-teal-400 mr-2">[{it.codigo}]</span>
                          <span className="text-white font-medium">{it.nombre}</span>
                          <span className="text-slate-400 ml-2">Stock: {it.stock_actual}</span>
                        </div>
                        <ArrowRight className="h-3 w-3 text-slate-500" />
                      </button>
                    ))}
                  </div>
                </div>
              )}

              {matchingClientes.length > 0 && (
                <div>
                  <h4 className="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <Users2 className="h-3.5 w-3.5 text-teal-400" />
                    Clientes ({matchingClientes.length})
                  </h4>
                  <div className="space-y-1">
                    {matchingClientes.map((c) => (
                      <button
                        key={c.id}
                        onClick={() => {
                          onSelectAction('clientes');
                          onClose();
                        }}
                        className="w-full flex items-center justify-between p-2 rounded-lg bg-slate-950/50 hover:bg-slate-800 text-left transition-colors text-xs"
                      >
                        <div>
                          <span className="font-mono text-teal-400 mr-2">[{c.codigo}]</span>
                          <span className="text-white font-medium">{c.nombre}</span>
                          <span className="text-slate-400 ml-2 font-mono">{c.telefono}</span>
                        </div>
                        <ArrowRight className="h-3 w-3 text-slate-500" />
                      </button>
                    ))}
                  </div>
                </div>
              )}
            </>
          )}
        </div>

        <div className="flex items-center justify-between border-t border-slate-800 px-4 py-2 bg-slate-950/80 text-[11px] text-slate-400">
          <span>Presione <kbd className="px-1.5 py-0.5 rounded bg-slate-800 text-slate-300 font-mono">ESC</kbd> para cerrar</span>
          <span>Búsqueda global DataPlus</span>
        </div>
      </div>
    </div>
  );
};
