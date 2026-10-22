import React from 'react';
import { 
  Columns3, 
  ChevronLeft, 
  ChevronRight, 
  DollarSign, 
  MapPin, 
  User, 
  Clock, 
  CheckCircle2, 
  Layers, 
  Calculator,
  Plus
} from 'lucide-react';
import { Proyecto, KanbanStatus } from '../types';

interface KanbanViewProps {
  proyectos: Proyecto[];
  onUpdateStatus: (proyectoId: string, newKanbanStatus: KanbanStatus) => void;
  onOpenBudget: (proyecto: Proyecto) => void;
}

export const KanbanView: React.FC<KanbanViewProps> = ({
  proyectos,
  onUpdateStatus,
  onOpenBudget,
}) => {
  const columns: { id: KanbanStatus; title: string; color: string; badgeBg: string; borderColor: string }[] = [
    { 
      id: 'por_hacer', 
      title: '📋 Por Hacer', 
      color: 'text-slate-300', 
      badgeBg: 'bg-slate-800 text-slate-300',
      borderColor: 'border-slate-800'
    },
    { 
      id: 'en_progreso', 
      title: '🔄 En Progreso', 
      color: 'text-teal-400', 
      badgeBg: 'bg-teal-500/20 text-teal-300 border border-teal-500/30',
      borderColor: 'border-teal-500/40'
    },
    { 
      id: 'en_revision', 
      title: '🔍 En Revisión', 
      color: 'text-sky-400', 
      badgeBg: 'bg-sky-500/20 text-sky-300 border border-sky-500/30',
      borderColor: 'border-sky-500/40'
    },
    { 
      id: 'completado', 
      title: '✅ Completado', 
      color: 'text-emerald-400', 
      badgeBg: 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30',
      borderColor: 'border-emerald-500/40'
    }
  ];

  const statusOrder: KanbanStatus[] = ['por_hacer', 'en_progreso', 'en_revision', 'completado'];

  const moveLeft = (proyecto: Proyecto) => {
    const currentIndex = statusOrder.indexOf(proyecto.estado_kanban || 'por_hacer');
    if (currentIndex > 0) {
      onUpdateStatus(proyecto.id, statusOrder[currentIndex - 1]);
    }
  };

  const moveRight = (proyecto: Proyecto) => {
    const currentIndex = statusOrder.indexOf(proyecto.estado_kanban || 'por_hacer');
    if (currentIndex < statusOrder.length - 1) {
      onUpdateStatus(proyecto.id, statusOrder[currentIndex + 1]);
    }
  };

  return (
    <div className="space-y-6">
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h2 className="text-xl font-bold text-white tracking-tight flex items-center gap-2">
            <Columns3 className="h-5 w-5 text-teal-400" />
            Tablero Kanban — Seguimiento de Proyectos I+D & Obras
          </h2>
          <p className="text-xs text-slate-400">
            Organice el avance operacional mediante flujo visual entre fases.
          </p>
        </div>
      </div>

      {/* Kanban Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 items-start">
        {columns.map((column) => {
          const colProyectos = proyectos.filter(
            p => (p.estado_kanban || 'por_hacer') === column.id
          );

          return (
            <div
              key={column.id}
              className="flex flex-col rounded-2xl border border-slate-800 bg-slate-900/60 p-3.5 shadow-sm min-h-[500px]"
            >
              {/* Column Header */}
              <div className="flex items-center justify-between pb-3 mb-3 border-b border-slate-800">
                <span className={`text-xs font-bold ${column.color}`}>
                  {column.title}
                </span>
                <span className={`px-2 py-0.5 rounded-full text-[10px] font-mono font-bold ${column.badgeBg}`}>
                  {colProyectos.length}
                </span>
              </div>

              {/* Cards Container */}
              <div className="flex-1 space-y-3 overflow-y-auto">
                {colProyectos.map((proyecto) => {
                  const currentIndex = statusOrder.indexOf(proyecto.estado_kanban || 'por_hacer');
                  const canMoveLeft = currentIndex > 0;
                  const canMoveRight = currentIndex < statusOrder.length - 1;

                  return (
                    <div
                      key={proyecto.id}
                      className="group rounded-xl border border-slate-800 bg-slate-950/80 p-3.5 hover:border-slate-700 transition-all shadow-sm"
                    >
                      <div className="flex items-center justify-between text-[11px] mb-1.5">
                        <span className="font-mono font-semibold text-teal-400">{proyecto.codigo}</span>
                        <span className="text-[10px] text-slate-400">{proyecto.tipo_proyecto_nombre}</span>
                      </div>

                      <h4 className="text-xs font-bold text-white leading-snug mb-2">
                        {proyecto.nombre}
                      </h4>

                      {proyecto.cliente_nombre && (
                        <div className="text-[11px] text-slate-300 flex items-center gap-1.5 mb-1">
                          <MapPin className="h-3 w-3 text-slate-400 flex-shrink-0" />
                          <span className="truncate">{proyecto.cliente_nombre}</span>
                        </div>
                      )}

                      <div className="flex items-center justify-between text-[11px] text-slate-400 my-2 pt-2 border-t border-slate-900">
                        <div className="flex items-center gap-1 text-slate-300">
                          <User className="h-3 w-3 text-slate-400" />
                          <span className="truncate max-w-[90px]">{proyecto.responsable_nombre.split(' ')[0]}</span>
                        </div>
                        <div className="font-mono font-bold text-white text-xs">
                          ${proyecto.presupuesto_total.toLocaleString('en-US', { minimumFractionDigits: 0 })}
                        </div>
                      </div>

                      {/* Progress Bar */}
                      <div className="w-full bg-slate-900 rounded-full h-1.5 overflow-hidden mb-3">
                        <div
                          className="bg-teal-400 h-1.5 rounded-full"
                          style={{ width: `${proyecto.progreso}%` }}
                        />
                      </div>

                      {/* Footer Actions: Move Left, Presupuesto, Move Right */}
                      <div className="flex items-center justify-between pt-1 border-t border-slate-900/80">
                        <button
                          onClick={() => moveLeft(proyecto)}
                          disabled={!canMoveLeft}
                          className="rounded p-1 text-slate-400 hover:bg-slate-800 hover:text-white disabled:opacity-20 cursor-pointer disabled:cursor-not-allowed"
                          title="Mover a fase anterior"
                        >
                          <ChevronLeft className="h-3.5 w-3.5" />
                        </button>

                        <button
                          onClick={() => onOpenBudget(proyecto)}
                          className="flex items-center gap-1 text-[10px] font-medium text-teal-400 hover:text-teal-300 bg-teal-500/10 hover:bg-teal-500/20 px-2 py-0.5 rounded border border-teal-500/20 cursor-pointer"
                        >
                          <Calculator className="h-2.5 w-2.5" />
                          <span>Presupuesto</span>
                        </button>

                        <button
                          onClick={() => moveRight(proyecto)}
                          disabled={!canMoveRight}
                          className="rounded p-1 text-slate-400 hover:bg-slate-800 hover:text-white disabled:opacity-20 cursor-pointer disabled:cursor-not-allowed"
                          title="Avanzar a siguiente fase"
                        >
                          <ChevronRight className="h-3.5 w-3.5" />
                        </button>
                      </div>
                    </div>
                  );
                })}

                {colProyectos.length === 0 && (
                  <div className="h-32 border-2 border-dashed border-slate-800/80 rounded-xl flex items-center justify-center text-xs text-slate-500">
                    No hay proyectos en esta fase
                  </div>
                )}
              </div>
            </div>
          );
        })}
      </div>
    </div>
  );
};
