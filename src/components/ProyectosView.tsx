import React, { useState } from 'react';
import { 
  FolderKanban, 
  Plus, 
  Search, 
  Filter, 
  Calendar, 
  DollarSign, 
  User, 
  MapPin, 
  ChevronRight, 
  Edit3, 
  Trash2, 
  Calculator, 
  CheckCircle,
  Clock,
  Layers,
  X
} from 'lucide-react';
import { Proyecto, Cliente, ItemInventario, ProjectStatus } from '../types';
import { PresupuestoModal } from './PresupuestoModal';

interface ProyectosViewProps {
  proyectos: Proyecto[];
  clientes: Cliente[];
  items: ItemInventario[];
  onUpdateProyecto: (proyecto: Proyecto) => void;
  onAddProyecto: (proyecto: Proyecto) => void;
  onDeleteProyecto: (id: string) => void;
}

export const ProyectosView: React.FC<ProyectosViewProps> = ({
  proyectos,
  clientes,
  items,
  onUpdateProyecto,
  onAddProyecto,
  onDeleteProyecto,
}) => {
  const [searchTerm, setSearchTerm] = useState('');
  const [statusFilter, setStatusFilter] = useState<string>('todos');
  const [editingBudgetProyecto, setEditingBudgetProyecto] = useState<Proyecto | null>(null);
  const [showCreateModal, setShowCreateModal] = useState(false);

  // New Project Form State
  const [newNombre, setNewNombre] = useState('');
  const [newCodigo, setNewCodigo] = useState(`PRY-2026-${String(proyectos.length + 1).padStart(3, '0')}`);
  const [newClienteId, setNewClienteId] = useState('');
  const [newUbicacionId, setNewUbicacionId] = useState('');
  const [newTipo, setNewTipo] = useState('Instalación CCTV & Telecom');
  const [newTipoSeguimiento, setNewTipoSeguimiento] = useState<'investigacion' | 'instalacion' | 'mantenimiento'>('instalacion');
  const [newResponsable, setNewResponsable] = useState('Javier Domínguez');
  const [newFechaInicio, setNewFechaInicio] = useState(new Date().toISOString().split('T')[0]);
  const [newFechaFin, setNewFechaFin] = useState('');
  const [newDescripcion, setNewDescripcion] = useState('');

  const selectedCliente = clientes.find(c => c.id === newClienteId);

  const filteredProyectos = proyectos.filter(p => {
    const matchesSearch = 
      p.nombre.toLowerCase().includes(searchTerm.toLowerCase()) ||
      p.codigo.toLowerCase().includes(searchTerm.toLowerCase()) ||
      (p.cliente_nombre && p.cliente_nombre.toLowerCase().includes(searchTerm.toLowerCase()));

    const matchesStatus = statusFilter === 'todos' || p.estado === statusFilter;
    return matchesSearch && matchesStatus;
  });

  const handleCreateSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!newNombre || !newCodigo) return;

    const clienteObj = clientes.find(c => c.id === newClienteId);
    const ubicacionObj = clienteObj?.ubicaciones.find(u => u.id === newUbicacionId);

    const newProj: Proyecto = {
      id: `pry-${Date.now()}`,
      codigo: newCodigo,
      nombre: newNombre,
      tipo_proyecto_nombre: newTipo,
      tipo_seguimiento: newTipoSeguimiento,
      cliente_id: clienteObj?.id,
      cliente_nombre: clienteObj?.nombre,
      cliente_ubicacion_id: ubicacionObj?.id,
      cliente_ubicacion_nombre: ubicacionObj?.nombre,
      responsable_nombre: newResponsable,
      estado: 'borrador',
      estado_kanban: 'por_hacer',
      fecha_inicio: newFechaInicio,
      fecha_fin: newFechaFin || undefined,
      presupuesto_total: 0,
      progreso: 0,
      descripcion: newDescripcion,
      lineas_presupuesto: [],
      created_at: new Date().toISOString(),
      updated_at: new Date().toISOString()
    };

    onAddProyecto(newProj);
    setShowCreateModal(false);
    // Reset Form
    setNewNombre('');
    setNewCodigo(`PRY-2026-${String(proyectos.length + 2).padStart(3, '0')}`);
    setNewDescripcion('');
  };

  const getStatusBadge = (estado: ProjectStatus) => {
    switch (estado) {
      case 'completado':
        return <span className="inline-flex items-center gap-1 rounded bg-emerald-500/20 px-2 py-0.5 text-[10px] font-semibold text-emerald-400 border border-emerald-500/30">Completado</span>;
      case 'en_progreso':
        return <span className="inline-flex items-center gap-1 rounded bg-teal-500/20 px-2 py-0.5 text-[10px] font-semibold text-teal-400 border border-teal-500/30">En Progreso</span>;
      case 'cancelado':
        return <span className="inline-flex items-center gap-1 rounded bg-rose-500/20 px-2 py-0.5 text-[10px] font-semibold text-rose-400 border border-rose-500/30">Cancelado</span>;
      default:
        return <span className="inline-flex items-center gap-1 rounded bg-slate-700/80 px-2 py-0.5 text-[10px] font-semibold text-slate-300 border border-slate-600">Borrador</span>;
    }
  };

  return (
    <div className="space-y-6">
      {/* Header Bar */}
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h2 className="text-xl font-bold text-white tracking-tight flex items-center gap-2">
            <FolderKanban className="h-5 w-5 text-teal-400" />
            Proyectos de Telecomunicaciones & Seguridad
          </h2>
          <p className="text-xs text-slate-400">
            Control de presupuestos, cronograma técnico y ejecución en obra.
          </p>
        </div>

        <button
          onClick={() => setShowCreateModal(true)}
          className="flex items-center gap-1.5 rounded-lg bg-teal-500 hover:bg-teal-400 text-slate-950 font-bold px-4 py-2 text-xs transition-all shadow-md shadow-teal-500/20 cursor-pointer"
        >
          <Plus className="h-4 w-4" />
          Nuevo Proyecto
        </button>
      </div>

      {/* Filter and Search Bar */}
      <div className="flex flex-col sm:flex-row gap-3 items-center justify-between rounded-xl border border-slate-800 bg-slate-900/60 p-3">
        <div className="relative w-full sm:w-80">
          <Search className="absolute left-3 top-2.5 h-3.5 w-3.5 text-slate-400" />
          <input
            type="text"
            placeholder="Buscar por código, nombre o cliente..."
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
            className="w-full rounded-lg border border-slate-800 bg-slate-950 pl-9 pr-3 py-1.5 text-xs text-slate-200 placeholder-slate-500 focus:border-teal-500 focus:outline-none"
          />
        </div>

        <div className="flex items-center gap-2 w-full sm:w-auto">
          <Filter className="h-3.5 w-3.5 text-slate-400" />
          <span className="text-xs text-slate-400">Estado:</span>
          <select
            value={statusFilter}
            onChange={(e) => setStatusFilter(e.target.value)}
            className="rounded-lg border border-slate-800 bg-slate-950 px-2.5 py-1.5 text-xs text-slate-300 focus:border-teal-500 focus:outline-none"
          >
            <option value="todos">Todos los estados</option>
            <option value="borrador">Borrador</option>
            <option value="en_progreso">En Progreso</option>
            <option value="completado">Completado</option>
            <option value="cancelado">Cancelado</option>
          </select>
        </div>
      </div>

      {/* Projects Table */}
      <div className="rounded-xl border border-slate-800 bg-slate-900/70 overflow-hidden shadow-sm">
        <div className="overflow-x-auto">
          <table className="w-full text-left text-xs">
            <thead className="border-b border-slate-800 bg-slate-950/80 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
              <tr>
                <th className="px-4 py-3">Código</th>
                <th className="px-4 py-3">Proyecto / Especialidad</th>
                <th className="px-4 py-3">Cliente / Ubicación</th>
                <th className="px-4 py-3">Responsable</th>
                <th className="px-4 py-3">Estado</th>
                <th className="px-4 py-3 text-right">Presupuesto</th>
                <th className="px-4 py-3">Progreso</th>
                <th className="px-4 py-3 text-center">Acciones</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-800 text-slate-200">
              {filteredProyectos.length > 0 ? (
                filteredProyectos.map((proyecto) => (
                  <tr key={proyecto.id} className="hover:bg-slate-800/40 transition-colors">
                    <td className="px-4 py-3 font-mono font-semibold text-teal-400">
                      {proyecto.codigo}
                    </td>
                    <td className="px-4 py-3">
                      <div className="font-semibold text-white">{proyecto.nombre}</div>
                      <div className="text-[11px] text-slate-400">{proyecto.tipo_proyecto_nombre}</div>
                    </td>
                    <td className="px-4 py-3">
                      <div className="font-medium text-slate-200">{proyecto.cliente_nombre || 'N/A'}</div>
                      <div className="text-[11px] text-slate-400 flex items-center gap-1">
                        <MapPin className="h-3 w-3 text-slate-400" />
                        {proyecto.cliente_ubicacion_nombre || 'Sede Principal'}
                      </div>
                    </td>
                    <td className="px-4 py-3 text-slate-300">
                      {proyecto.responsable_nombre}
                    </td>
                    <td className="px-4 py-3">
                      {getStatusBadge(proyecto.estado)}
                    </td>
                    <td className="px-4 py-3 text-right font-mono font-bold text-white">
                      ${proyecto.presupuesto_total.toLocaleString('en-US', { minimumFractionDigits: 2 })}
                    </td>
                    <td className="px-4 py-3 min-w-[120px]">
                      <div className="flex items-center gap-2">
                        <div className="w-full bg-slate-800 rounded-full h-1.5 overflow-hidden">
                          <div 
                            className="bg-teal-400 h-1.5 rounded-full" 
                            style={{ width: `${proyecto.progreso}%` }}
                          />
                        </div>
                        <span className="font-mono text-[10px] text-slate-400">{proyecto.progreso}%</span>
                      </div>
                    </td>
                    <td className="px-4 py-3 text-center">
                      <div className="flex items-center justify-center gap-2">
                        <button
                          onClick={() => setEditingBudgetProyecto(proyecto)}
                          className="flex items-center gap-1 rounded bg-teal-500/10 hover:bg-teal-500/20 text-teal-400 border border-teal-500/20 px-2 py-1 text-[11px] font-medium transition-colors cursor-pointer"
                          title="Gestionar presupuesto y materiales"
                        >
                          <Calculator className="h-3 w-3" />
                          <span>Presupuesto</span>
                        </button>

                        <button
                          onClick={() => {
                            const nextState: Record<ProjectStatus, ProjectStatus> = {
                              borrador: 'en_progreso',
                              en_progreso: 'completado',
                              completado: 'borrador',
                              cancelado: 'borrador'
                            };
                            onUpdateProyecto({
                              ...proyecto,
                              estado: nextState[proyecto.estado],
                              progreso: nextState[proyecto.estado] === 'completado' ? 100 : proyecto.progreso
                            });
                          }}
                          className="p-1 text-slate-400 hover:text-teal-300 transition-colors cursor-pointer"
                          title="Cambiar estado del proyecto"
                        >
                          <CheckCircle className="h-3.5 w-3.5" />
                        </button>

                        <button
                          onClick={() => onDeleteProyecto(proyecto.id)}
                          className="p-1 text-slate-500 hover:text-rose-400 transition-colors cursor-pointer"
                          title="Eliminar proyecto"
                        >
                          <Trash2 className="h-3.5 w-3.5" />
                        </button>
                      </div>
                    </td>
                  </tr>
                ))
              ) : (
                <tr>
                  <td colSpan={8} className="px-4 py-8 text-center text-slate-400">
                    No se encontraron proyectos con los filtros actuales.
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      </div>

      {/* Budget Breakdown Modal */}
      {editingBudgetProyecto && (
        <PresupuestoModal
          proyecto={editingBudgetProyecto}
          items={items}
          onClose={() => setEditingBudgetProyecto(null)}
          onSave={onUpdateProyecto}
        />
      )}

      {/* Create Project Modal */}
      {showCreateModal && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 p-4 backdrop-blur-sm">
          <div className="w-full max-w-xl rounded-2xl border border-slate-800 bg-slate-900 shadow-2xl overflow-hidden">
            <div className="flex items-center justify-between border-b border-slate-800 px-6 py-4 bg-slate-950/60">
              <h3 className="text-base font-bold text-white flex items-center gap-2">
                <FolderKanban className="h-4 w-4 text-teal-400" />
                Registrar Nuevo Proyecto Técnico
              </h3>
              <button
                onClick={() => setShowCreateModal(false)}
                className="text-slate-400 hover:text-white"
              >
                <X className="h-5 w-5" />
              </button>
            </div>

            <form onSubmit={handleCreateSubmit} className="p-6 space-y-4">
              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">
                    Código de Proyecto
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
                    Tipo de Seguimiento
                  </label>
                  <select
                    value={newTipoSeguimiento}
                    onChange={(e) => setNewTipoSeguimiento(e.target.value as any)}
                    className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-teal-500 focus:outline-none capitalize"
                  >
                    <option value="instalacion">Instalación en Sitio</option>
                    <option value="investigacion">Investigación & Desarrollo (I+D)</option>
                    <option value="mantenimiento">Mantenimiento Preventivo</option>
                  </select>
                </div>
              </div>

              <div>
                <label className="block text-[11px] font-medium text-slate-400 mb-1">
                  Nombre del Proyecto
                </label>
                <input
                  type="text"
                  required
                  placeholder="Ej: Instalación Sistema CCTV 16 Canales 4K"
                  value={newNombre}
                  onChange={(e) => setNewNombre(e.target.value)}
                  className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-teal-500 focus:outline-none"
                />
              </div>

              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">
                    Cliente Solicitante
                  </label>
                  <select
                    value={newClienteId}
                    onChange={(e) => {
                      setNewClienteId(e.target.value);
                      setNewUbicacionId('');
                    }}
                    className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-teal-500 focus:outline-none"
                  >
                    <option value="">-- Seleccionar Cliente --</option>
                    {clientes.map((cli) => (
                      <option key={cli.id} value={cli.id}>
                        {cli.nombre}
                      </option>
                    ))}
                  </select>
                </div>

                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">
                    Ubicación / Sede
                  </label>
                  <select
                    value={newUbicacionId}
                    onChange={(e) => setNewUbicacionId(e.target.value)}
                    disabled={!selectedCliente}
                    className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-teal-500 focus:outline-none disabled:opacity-50"
                  >
                    <option value="">-- Seleccionar Sede --</option>
                    {selectedCliente?.ubicaciones.map((ub) => (
                      <option key={ub.id} value={ub.id}>
                        {ub.nombre} ({ub.tipo})
                      </option>
                    ))}
                  </select>
                </div>
              </div>

              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">
                    Especialidad / Tipo
                  </label>
                  <input
                    type="text"
                    value={newTipo}
                    onChange={(e) => setNewTipo(e.target.value)}
                    className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-teal-500 focus:outline-none"
                  />
                </div>
                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">
                    Técnico Responsable
                  </label>
                  <input
                    type="text"
                    value={newResponsable}
                    onChange={(e) => setNewResponsable(e.target.value)}
                    className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-teal-500 focus:outline-none"
                  />
                </div>
              </div>

              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">
                    Fecha de Inicio
                  </label>
                  <input
                    type="date"
                    value={newFechaInicio}
                    onChange={(e) => setNewFechaInicio(e.target.value)}
                    className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-teal-500 focus:outline-none"
                  />
                </div>
                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">
                    Fecha de Finalización Estimada
                  </label>
                  <input
                    type="date"
                    value={newFechaFin}
                    onChange={(e) => setNewFechaFin(e.target.value)}
                    className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-teal-500 focus:outline-none"
                  />
                </div>
              </div>

              <div>
                <label className="block text-[11px] font-medium text-slate-400 mb-1">
                  Descripción u Objetivos
                </label>
                <textarea
                  rows={2}
                  placeholder="Detalles de la instalación, requerimientos especiales..."
                  value={newDescripcion}
                  onChange={(e) => setNewDescripcion(e.target.value)}
                  className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-teal-500 focus:outline-none"
                />
              </div>

              <div className="flex justify-end gap-3 pt-3 border-t border-slate-800">
                <button
                  type="button"
                  onClick={() => setShowCreateModal(false)}
                  className="rounded-lg border border-slate-700 bg-slate-800 px-4 py-2 text-xs font-medium text-slate-300 hover:bg-slate-700 transition-colors cursor-pointer"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  className="rounded-lg bg-teal-500 hover:bg-teal-400 text-slate-950 px-5 py-2 text-xs font-bold transition-all shadow-md shadow-teal-500/20 cursor-pointer"
                >
                  Crear Proyecto
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
};
