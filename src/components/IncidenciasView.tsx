import React, { useState } from 'react';
import { 
  AlertCircle, 
  Plus, 
  Search, 
  Filter, 
  CheckCircle2, 
  Clock, 
  User, 
  MapPin, 
  Phone, 
  Wrench, 
  DollarSign, 
  Check, 
  X,
  FileText
} from 'lucide-react';
import { Incidencia, Cliente, Priority, IncidentStatus, IncidentType } from '../types';

interface IncidenciasViewProps {
  incidencias: Incidencia[];
  clientes: Cliente[];
  onAddIncidencia: (inc: Incidencia) => void;
  onUpdateIncidencia: (inc: Incidencia) => void;
}

export const IncidenciasView: React.FC<IncidenciasViewProps> = ({
  incidencias,
  clientes,
  onAddIncidencia,
  onUpdateIncidencia,
}) => {
  const [searchTerm, setSearchTerm] = useState('');
  const [typeFilter, setTypeFilter] = useState<string>('todos');
  const [priorityFilter, setPriorityFilter] = useState<string>('todos');
  const [statusFilter, setStatusFilter] = useState<string>('todos');
  const [showCreateModal, setShowCreateModal] = useState(false);
  const [selectedIncidencia, setSelectedIncidencia] = useState<Incidencia | null>(null);

  // Form State
  const [codigo, setCodigo] = useState(`INC-2026-${String(incidencias.length + 1).padStart(3, '0')}`);
  const [clienteId, setClienteId] = useState('');
  const [ubicacionNombre, setUbicacionNombre] = useState('');
  const [direccion, setDireccion] = useState('');
  const [contactoLocal, setContactoLocal] = useState('');
  const [telefonoLocal, setTelefonoLocal] = useState('');
  const [tipo, setTipo] = useState<IncidentType>('CCTV');
  const [prioridad, setPrioridad] = useState<Priority>('Media');
  const [titulo, setTitulo] = useState('');
  const [descripcion, setDescripcion] = useState('');
  const [tecnicoNombre, setTecnicoNombre] = useState('Javier Domínguez');
  const [costoEstimado, setCostoEstimado] = useState(0);

  // Handle client selection to autofill contact and address
  const handleClientChange = (cId: string) => {
    setClienteId(cId);
    const cli = clientes.find(c => c.id === cId);
    if (cli) {
      const firstUb = cli.ubicaciones[0];
      setUbicacionNombre(firstUb?.nombre || 'Sede Principal');
      setDireccion(firstUb?.direccion || cli.direccion || '');
      setContactoLocal(firstUb?.contacto_nombre || cli.nombre);
      setTelefonoLocal(firstUb?.contacto_telefono || cli.telefono || '');
    }
  };

  const handleCreateSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!titulo || !clienteId) return;

    const cli = clientes.find(c => c.id === clienteId);
    const newInc: Incidencia = {
      id: `inc-${Date.now()}`,
      codigo,
      tipo,
      cliente_id: clienteId,
      cliente_nombre: cli ? cli.nombre : 'Cliente Desconocido',
      ubicacion_nombre: ubicacionNombre,
      direccion_incidencia: direccion,
      contacto_local: contactoLocal,
      telefono_local: telefonoLocal,
      titulo,
      descripcion,
      tecnico_nombre: tecnicoNombre,
      estado: 'Pendiente',
      prioridad,
      fecha_reporte: new Date().toISOString(),
      costo_estimado: costoEstimado,
      requiere_repuestos: false,
      creado_por: 'Supervisión DataPlus'
    };

    onAddIncidencia(newInc);
    setShowCreateModal(false);
    // Reset Form
    setTitulo('');
    setDescripcion('');
    setCodigo(`INC-2026-${String(incidencias.length + 2).padStart(3, '0')}`);
  };

  const filteredIncidencias = incidencias.filter((inc) => {
    const matchesSearch = 
      inc.codigo.toLowerCase().includes(searchTerm.toLowerCase()) ||
      inc.titulo.toLowerCase().includes(searchTerm.toLowerCase()) ||
      inc.cliente_nombre.toLowerCase().includes(searchTerm.toLowerCase());

    const matchesType = typeFilter === 'todos' || inc.tipo === typeFilter;
    const matchesPriority = priorityFilter === 'todos' || inc.prioridad === priorityFilter;
    const matchesStatus = statusFilter === 'todos' || inc.estado === statusFilter;

    return matchesSearch && matchesType && matchesPriority && matchesStatus;
  });

  const getPriorityBadge = (p: Priority) => {
    switch (p) {
      case 'Critica':
        return <span className="rounded bg-rose-500/20 text-rose-300 border border-rose-500/30 px-2 py-0.5 text-[10px] font-bold">Crítica</span>;
      case 'Alta':
        return <span className="rounded bg-amber-500/20 text-amber-300 border border-amber-500/30 px-2 py-0.5 text-[10px] font-bold">Alta</span>;
      case 'Media':
        return <span className="rounded bg-teal-500/20 text-teal-300 border border-teal-500/30 px-2 py-0.5 text-[10px] font-medium">Media</span>;
      default:
        return <span className="rounded bg-slate-700 text-slate-300 px-2 py-0.5 text-[10px] font-medium">Baja</span>;
    }
  };

  const getStatusBadge = (s: IncidentStatus) => {
    switch (s) {
      case 'Resuelta':
        return <span className="rounded bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 px-2 py-0.5 text-[10px] font-semibold">Resuelta</span>;
      case 'Cerrada':
        return <span className="rounded bg-slate-700 text-slate-300 px-2 py-0.5 text-[10px] font-semibold">Cerrada</span>;
      case 'En_Progreso':
        return <span className="rounded bg-teal-500/20 text-teal-300 border border-teal-500/30 px-2 py-0.5 text-[10px] font-semibold">En Progreso</span>;
      case 'Asignada':
        return <span className="rounded bg-sky-500/20 text-sky-300 border border-sky-500/30 px-2 py-0.5 text-[10px] font-semibold">Asignada</span>;
      case 'Cancelada':
        return <span className="rounded bg-rose-500/10 text-rose-400 border border-rose-500/20 px-2 py-0.5 text-[10px] font-semibold">Cancelada</span>;
      default:
        return <span className="rounded bg-amber-500/10 text-amber-300 border border-amber-500/20 px-2 py-0.5 text-[10px] font-semibold">Pendiente</span>;
    }
  };

  return (
    <div className="space-y-6">
      {/* Header Bar */}
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h2 className="text-xl font-bold text-white tracking-tight flex items-center gap-2">
            <AlertCircle className="h-5 w-5 text-rose-400" />
            Centro de Incidencias & Servicios Técnicos
          </h2>
          <p className="text-xs text-slate-400">
            Registro, diagnóstico y resolución de averías en CCTV, SACI, Redes y Gestión Remota.
          </p>
        </div>

        <button
          onClick={() => setShowCreateModal(true)}
          className="flex items-center gap-1.5 rounded-lg bg-teal-500 hover:bg-teal-400 text-slate-950 font-bold px-4 py-2 text-xs transition-all shadow-md shadow-teal-500/20 cursor-pointer"
        >
          <Plus className="h-4 w-4" />
          Reportar Incidencia
        </button>
      </div>

      {/* Filter and Search Bar */}
      <div className="grid grid-cols-1 sm:grid-cols-4 gap-3 rounded-xl border border-slate-800 bg-slate-900/60 p-3">
        <div className="relative sm:col-span-1">
          <Search className="absolute left-3 top-2.5 h-3.5 w-3.5 text-slate-400" />
          <input
            type="text"
            placeholder="Buscar por código, título, cliente..."
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
            className="w-full rounded-lg border border-slate-800 bg-slate-950 pl-9 pr-3 py-1.5 text-xs text-slate-200 placeholder-slate-500 focus:border-teal-500 focus:outline-none"
          />
        </div>

        <div>
          <select
            value={typeFilter}
            onChange={(e) => setTypeFilter(e.target.value)}
            className="w-full rounded-lg border border-slate-800 bg-slate-950 px-2.5 py-1.5 text-xs text-slate-300 focus:border-teal-500 focus:outline-none"
          >
            <option value="todos">Todas las especialidades</option>
            <option value="CCTV">CCTV (Cámaras y Vídeo)</option>
            <option value="SACI">SACI (Alarmas & Detección)</option>
            <option value="Redes">Redes & Conectividad</option>
            <option value="Gestion_Remota">Gestión Remota</option>
          </select>
        </div>

        <div>
          <select
            value={priorityFilter}
            onChange={(e) => setPriorityFilter(e.target.value)}
            className="w-full rounded-lg border border-slate-800 bg-slate-950 px-2.5 py-1.5 text-xs text-slate-300 focus:border-teal-500 focus:outline-none"
          >
            <option value="todos">Todas las prioridades</option>
            <option value="Critica">Crítica</option>
            <option value="Alta">Alta</option>
            <option value="Media">Media</option>
            <option value="Baja">Baja</option>
          </select>
        </div>

        <div>
          <select
            value={statusFilter}
            onChange={(e) => setStatusFilter(e.target.value)}
            className="w-full rounded-lg border border-slate-800 bg-slate-950 px-2.5 py-1.5 text-xs text-slate-300 focus:border-teal-500 focus:outline-none"
          >
            <option value="todos">Todos los estados</option>
            <option value="Pendiente">Pendiente</option>
            <option value="En_Progreso">En Progreso</option>
            <option value="Resuelta">Resuelta</option>
            <option value="Cerrada">Cerrada</option>
          </select>
        </div>
      </div>

      {/* Incidencias List / Table */}
      <div className="rounded-xl border border-slate-800 bg-slate-900/70 overflow-hidden shadow-sm">
        <div className="overflow-x-auto">
          <table className="w-full text-left text-xs">
            <thead className="border-b border-slate-800 bg-slate-950/80 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
              <tr>
                <th className="px-4 py-3">Código / Tipo</th>
                <th className="px-4 py-3">Título & Descripción</th>
                <th className="px-4 py-3">Cliente / Ubicación</th>
                <th className="px-4 py-3">Prioridad</th>
                <th className="px-4 py-3">Estado</th>
                <th className="px-4 py-3">Técnico Asignado</th>
                <th className="px-4 py-3 text-center">Acciones</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-800 text-slate-200">
              {filteredIncidencias.length > 0 ? (
                filteredIncidencias.map((inc) => (
                  <tr key={inc.id} className="hover:bg-slate-800/40 transition-colors">
                    <td className="px-4 py-3">
                      <div className="font-mono font-bold text-teal-400">{inc.codigo}</div>
                      <span className="inline-block mt-0.5 text-[10px] font-semibold uppercase px-1.5 py-0.2 rounded bg-slate-800 text-slate-300 border border-slate-700">
                        {inc.tipo.replace('_', ' ')}
                      </span>
                    </td>
                    <td className="px-4 py-3 max-w-xs">
                      <div className="font-semibold text-white truncate">{inc.titulo}</div>
                      <div className="text-[11px] text-slate-400 line-clamp-1">{inc.descripcion}</div>
                    </td>
                    <td className="px-4 py-3">
                      <div className="font-medium text-slate-200">{inc.cliente_nombre}</div>
                      <div className="text-[11px] text-slate-400 flex items-center gap-1">
                        <MapPin className="h-3 w-3 text-slate-400 flex-shrink-0" />
                        <span className="truncate">{inc.ubicacion_nombre || 'Sede Central'}</span>
                      </div>
                    </td>
                    <td className="px-4 py-3">
                      {getPriorityBadge(inc.prioridad)}
                    </td>
                    <td className="px-4 py-3">
                      {getStatusBadge(inc.estado)}
                    </td>
                    <td className="px-4 py-3 text-slate-300">
                      {inc.tecnico_nombre || 'Sin asignar'}
                    </td>
                    <td className="px-4 py-3 text-center">
                      <button
                        onClick={() => setSelectedIncidencia(inc)}
                        className="rounded bg-teal-500/10 hover:bg-teal-500/20 text-teal-400 border border-teal-500/20 px-2.5 py-1 text-[11px] font-semibold transition-colors cursor-pointer"
                      >
                        Gestionar
                      </button>
                    </td>
                  </tr>
                ))
              ) : (
                <tr>
                  <td colSpan={7} className="px-4 py-8 text-center text-slate-400">
                    No se encontraron incidencias con los criterios seleccionados.
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      </div>

      {/* Manage / Detail Modal */}
      {selectedIncidencia && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 p-4 backdrop-blur-sm">
          <div className="w-full max-w-2xl rounded-2xl border border-slate-800 bg-slate-900 shadow-2xl overflow-hidden">
            <div className="flex items-center justify-between border-b border-slate-800 px-6 py-4 bg-slate-950/60">
              <div className="flex items-center gap-2">
                <span className="font-mono text-sm font-bold text-teal-400">{selectedIncidencia.codigo}</span>
                <span className="text-slate-400">•</span>
                <span className="text-xs text-slate-300">{selectedIncidencia.cliente_nombre}</span>
              </div>
              <button
                onClick={() => setSelectedIncidencia(null)}
                className="text-slate-400 hover:text-white"
              >
                <X className="h-5 w-5" />
              </button>
            </div>

            <div className="p-6 space-y-4 max-h-[80vh] overflow-y-auto">
              <div>
                <h3 className="text-base font-bold text-white">{selectedIncidencia.titulo}</h3>
                <p className="text-xs text-slate-300 mt-1 bg-slate-950 p-3 rounded-lg border border-slate-800">
                  {selectedIncidencia.descripcion}
                </p>
              </div>

              <div className="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs bg-slate-950/50 p-3 rounded-lg border border-slate-800/80">
                <div>
                  <span className="text-slate-400 block text-[10px]">Contacto en Sitio</span>
                  <span className="font-medium text-slate-200">{selectedIncidencia.contacto_local || 'N/A'}</span>
                </div>
                <div>
                  <span className="text-slate-400 block text-[10px]">Teléfono Directo</span>
                  <span className="font-mono text-teal-400">{selectedIncidencia.telefono_local || 'N/A'}</span>
                </div>
                <div>
                  <span className="text-slate-400 block text-[10px]">Técnico Asignado</span>
                  <span className="font-medium text-slate-200">{selectedIncidencia.tecnico_nombre}</span>
                </div>
              </div>

              {/* Status Update */}
              <div>
                <label className="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-2">
                  Actualizar Estado de la Incidencia
                </label>
                <div className="flex flex-wrap gap-2">
                  {(['Pendiente', 'Asignada', 'En_Progreso', 'En_Espera', 'Resuelta', 'Cerrada'] as IncidentStatus[]).map((st) => (
                    <button
                      key={st}
                      type="button"
                      onClick={() => {
                        const updated: Incidencia = {
                          ...selectedIncidencia,
                          estado: st,
                          fecha_resolucion: st === 'Resuelta' ? new Date().toISOString() : selectedIncidencia.fecha_resolucion
                        };
                        setSelectedIncidencia(updated);
                        onUpdateIncidencia(updated);
                      }}
                      className={`px-3 py-1.5 rounded-lg text-xs font-semibold border transition-all cursor-pointer ${
                        selectedIncidencia.estado === st
                          ? 'bg-teal-500 text-slate-950 border-teal-400 shadow-md shadow-teal-500/20'
                          : 'bg-slate-800 text-slate-300 border-slate-700 hover:bg-slate-700'
                      }`}
                    >
                      {st.replace('_', ' ')}
                    </button>
                  ))}
                </div>
              </div>

              {/* Diagnostic Notes */}
              <div>
                <label className="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1">
                  Diagnóstico Técnico
                </label>
                <textarea
                  rows={2}
                  placeholder="Detalles técnicos de la falla encontrada..."
                  value={selectedIncidencia.diagnostico || ''}
                  onChange={(e) => setSelectedIncidencia({ ...selectedIncidencia, diagnostico: e.target.value })}
                  className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-teal-500 focus:outline-none"
                />
              </div>

              {/* Solution Notes */}
              <div>
                <label className="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1">
                  Solución Aplicada
                </label>
                <textarea
                  rows={2}
                  placeholder="Acciones correctivas realizadas, piezas sustituidas..."
                  value={selectedIncidencia.solucion || ''}
                  onChange={(e) => setSelectedIncidencia({ ...selectedIncidencia, solucion: e.target.value })}
                  className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-teal-500 focus:outline-none"
                />
              </div>

              <div className="flex justify-end gap-3 pt-3 border-t border-slate-800">
                <button
                  type="button"
                  onClick={() => setSelectedIncidencia(null)}
                  className="rounded-lg border border-slate-700 bg-slate-800 px-4 py-2 text-xs font-medium text-slate-300 hover:bg-slate-700 cursor-pointer"
                >
                  Cerrar
                </button>
                <button
                  type="button"
                  onClick={() => {
                    onUpdateIncidencia(selectedIncidencia);
                    setSelectedIncidencia(null);
                  }}
                  className="rounded-lg bg-teal-500 hover:bg-teal-400 text-slate-950 px-5 py-2 text-xs font-bold transition-all shadow-md shadow-teal-500/20 cursor-pointer"
                >
                  Guardar Cambios
                </button>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* Create Modal */}
      {showCreateModal && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 p-4 backdrop-blur-sm">
          <div className="w-full max-w-xl rounded-2xl border border-slate-800 bg-slate-900 shadow-2xl overflow-hidden">
            <div className="flex items-center justify-between border-b border-slate-800 px-6 py-4 bg-slate-950/60">
              <h3 className="text-base font-bold text-white flex items-center gap-2">
                <AlertCircle className="h-4 w-4 text-teal-400" />
                Registrar Incidencia Técnica
              </h3>
              <button
                onClick={() => setShowCreateModal(false)}
                className="text-slate-400 hover:text-white"
              >
                <X className="h-5 w-5" />
              </button>
            </div>

            <form onSubmit={handleCreateSubmit} className="p-6 space-y-4 max-h-[80vh] overflow-y-auto">
              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">
                    Código de Ticket
                  </label>
                  <input
                    type="text"
                    required
                    value={codigo}
                    onChange={(e) => setCodigo(e.target.value)}
                    className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs font-mono text-slate-200 focus:border-teal-500 focus:outline-none"
                  />
                </div>
                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">
                    Especialidad Técnica
                  </label>
                  <select
                    value={tipo}
                    onChange={(e) => setTipo(e.target.value as IncidentType)}
                    className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-teal-500 focus:outline-none"
                  >
                    <option value="CCTV">CCTV (Cámaras & Vídeo)</option>
                    <option value="SACI">SACI (Intrusión & Fuego)</option>
                    <option value="Redes">Redes & Conectividad</option>
                    <option value="Gestion_Remota">Gestión Remota</option>
                  </select>
                </div>
              </div>

              <div>
                <label className="block text-[11px] font-medium text-slate-400 mb-1">
                  Cliente Afectado
                </label>
                <select
                  required
                  value={clienteId}
                  onChange={(e) => handleClientChange(e.target.value)}
                  className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-teal-500 focus:outline-none"
                >
                  <option value="">-- Seleccione Cliente --</option>
                  {clientes.map((c) => (
                    <option key={c.id} value={c.id}>{c.nombre}</option>
                  ))}
                </select>
              </div>

              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">
                    Prioridad
                  </label>
                  <select
                    value={prioridad}
                    onChange={(e) => setPrioridad(e.target.value as Priority)}
                    className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-teal-500 focus:outline-none"
                  >
                    <option value="Baja">Baja</option>
                    <option value="Media">Media</option>
                    <option value="Alta">Alta</option>
                    <option value="Critica">Crítica (Interrupción Total)</option>
                  </select>
                </div>
                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">
                    Técnico Encargado
                  </label>
                  <input
                    type="text"
                    value={tecnicoNombre}
                    onChange={(e) => setTecnicoNombre(e.target.value)}
                    className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-teal-500 focus:outline-none"
                  />
                </div>
              </div>

              <div>
                <label className="block text-[11px] font-medium text-slate-400 mb-1">
                  Título de la Avería
                </label>
                <input
                  type="text"
                  required
                  placeholder="Ej: Cámara 3 sin señal PoE tras caída de tensión"
                  value={titulo}
                  onChange={(e) => setTitulo(e.target.value)}
                  className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-teal-500 focus:outline-none"
                />
              </div>

              <div>
                <label className="block text-[11px] font-medium text-slate-400 mb-1">
                  Descripción Detallada
                </label>
                <textarea
                  rows={2}
                  required
                  placeholder="Indique síntomas reportados, zona afectada, etc."
                  value={descripcion}
                  onChange={(e) => setDescripcion(e.target.value)}
                  className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-teal-500 focus:outline-none"
                />
              </div>

              <div className="flex justify-end gap-3 pt-3 border-t border-slate-800">
                <button
                  type="button"
                  onClick={() => setShowCreateModal(false)}
                  className="rounded-lg border border-slate-700 bg-slate-800 px-4 py-2 text-xs font-medium text-slate-300 hover:bg-slate-700 cursor-pointer"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  className="rounded-lg bg-teal-500 hover:bg-teal-400 text-slate-950 px-5 py-2 text-xs font-bold transition-all shadow-md shadow-teal-500/20 cursor-pointer"
                >
                  Registrar Incidencia
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
};
