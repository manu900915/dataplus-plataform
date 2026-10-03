import React, { useState } from 'react';
import { 
  Signal, 
  Plus, 
  Search, 
  Filter, 
  Edit3, 
  Trash2, 
  ExternalLink, 
  Radio, 
  ShieldCheck, 
  Camera, 
  CheckCircle2, 
  AlertCircle, 
  RefreshCw, 
  Phone, 
  MapPin, 
  Calendar, 
  Check, 
  X,
  CreditCard
} from 'lucide-react';
import { Servicio, Cliente, Brigada } from '../types';

interface ServiciosViewProps {
  servicios: Servicio[];
  clientes: Cliente[];
  brigadas: Brigada[];
  onAddServicio: (servicio: Omit<Servicio, 'id' | 'codigo'>) => void;
  onUpdateServicio: (servicio: Servicio) => void;
  onDeleteServicio: (id: string) => void;
}

export const ServiciosView: React.FC<ServiciosViewProps> = ({
  servicios,
  clientes,
  brigadas,
  onAddServicio,
  onUpdateServicio,
  onDeleteServicio,
}) => {
  const [searchTerm, setSearchTerm] = useState('');
  const [filterTipo, setFilterTipo] = useState<string>('todos');
  const [filterEstado, setFilterEstado] = useState<string>('todos');
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [editingServicio, setEditingServicio] = useState<Servicio | null>(null);

  // Form state
  const [formData, setFormData] = useState({
    cliente_id: '',
    cliente_ubicacion_id: '',
    tipo: 'CCTV' as 'CCTV' | 'SACI' | 'Gestion_Remota',
    brigada_id: '',
    estado: 'Activo' as 'Activo' | 'Inactivo' | 'En_Reparacion' | 'Suspendido',
    fecha_instalacion: new Date().toISOString().split('T')[0],
    notas: '',
    gr_tipo_solucion: 'Router4g' as 'Router4g' | 'Router+Modem' | 'Router+ADSL' | 'Otros',
    gr_sim_numero: '',
    gr_marca_modelo: '',
    gr_tipo_internet: 'Filtrado' as 'Abierto' | 'Filtrado' | 'Cerrado',
    gr_recarga_por: 'Nosotros' as 'Nosotros' | 'Cliente',
    gr_recarga_monto: 360,
    gr_ultima_recarga: new Date().toISOString().split('T')[0]
  });

  const selectedCliente = clientes.find(c => c.id === formData.cliente_id);

  const handleOpenAdd = () => {
    setEditingServicio(null);
    setFormData({
      cliente_id: clientes[0]?.id || '',
      cliente_ubicacion_id: clientes[0]?.ubicaciones[0]?.id || '',
      tipo: 'CCTV',
      brigada_id: brigadas[0]?.id || '',
      estado: 'Activo',
      fecha_instalacion: new Date().toISOString().split('T')[0],
      notas: '',
      gr_tipo_solucion: 'Router4g',
      gr_sim_numero: '+53 5 ',
      gr_marca_modelo: 'TP-Link TL-MR6400',
      gr_tipo_internet: 'Filtrado',
      gr_recarga_por: 'Nosotros',
      gr_recarga_monto: 360,
      gr_ultima_recarga: new Date().toISOString().split('T')[0]
    });
    setIsModalOpen(true);
  };

  const handleOpenEdit = (s: Servicio) => {
    setEditingServicio(s);
    setFormData({
      cliente_id: s.cliente_id,
      cliente_ubicacion_id: s.cliente_ubicacion_id || '',
      tipo: s.tipo,
      brigada_id: s.brigada_id || '',
      estado: s.estado,
      fecha_instalacion: s.fecha_instalacion || '',
      notas: s.notas || '',
      gr_tipo_solucion: s.gr_tipo_solucion || 'Router4g',
      gr_sim_numero: s.gr_sim_numero || '',
      gr_marca_modelo: s.gr_marca_modelo || '',
      gr_tipo_internet: s.gr_tipo_internet || 'Filtrado',
      gr_recarga_por: s.gr_recarga_por || 'Nosotros',
      gr_recarga_monto: s.gr_recarga_monto ?? 360,
      gr_ultima_recarga: s.gr_ultima_recarga || ''
    });
    setIsModalOpen(true);
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    const cli = clientes.find(c => c.id === formData.cliente_id);
    const ub = cli?.ubicaciones.find(u => u.id === formData.cliente_ubicacion_id);
    const brig = brigadas.find(b => b.id === formData.brigada_id);

    if (editingServicio) {
      onUpdateServicio({
        ...editingServicio,
        cliente_id: formData.cliente_id,
        cliente_nombre: cli ? cli.nombre : editingServicio.cliente_nombre,
        cliente_ubicacion_id: formData.cliente_ubicacion_id,
        ubicacion_nombre: ub?.nombre || '',
        tipo: formData.tipo,
        brigada_id: formData.brigada_id,
        brigada_nombre: brig?.nombre,
        estado: formData.estado,
        fecha_instalacion: formData.fecha_instalacion,
        notas: formData.notas,
        gr_tipo_solucion: formData.tipo === 'Gestion_Remota' ? formData.gr_tipo_solucion : undefined,
        gr_sim_numero: formData.tipo === 'Gestion_Remota' ? formData.gr_sim_numero : undefined,
        gr_marca_modelo: formData.tipo === 'Gestion_Remota' ? formData.gr_marca_modelo : undefined,
        gr_tipo_internet: formData.tipo === 'Gestion_Remota' ? formData.gr_tipo_internet : undefined,
        gr_recarga_por: formData.tipo === 'Gestion_Remota' ? formData.gr_recarga_por : undefined,
        gr_recarga_monto: formData.tipo === 'Gestion_Remota' ? formData.gr_recarga_monto : undefined,
        gr_ultima_recarga: formData.tipo === 'Gestion_Remota' ? formData.gr_ultima_recarga : undefined,
      });
    } else {
      onAddServicio({
        cliente_id: formData.cliente_id,
        cliente_nombre: cli ? cli.nombre : 'Cliente',
        cliente_ubicacion_id: formData.cliente_ubicacion_id,
        ubicacion_nombre: ub?.nombre || '',
        tipo: formData.tipo,
        brigada_id: formData.brigada_id,
        brigada_nombre: brig?.nombre,
        estado: formData.estado,
        fecha_instalacion: formData.fecha_instalacion,
        notas: formData.notas,
        gr_tipo_solucion: formData.tipo === 'Gestion_Remota' ? formData.gr_tipo_solucion : undefined,
        gr_sim_numero: formData.tipo === 'Gestion_Remota' ? formData.gr_sim_numero : undefined,
        gr_marca_modelo: formData.tipo === 'Gestion_Remota' ? formData.gr_marca_modelo : undefined,
        gr_tipo_internet: formData.tipo === 'Gestion_Remota' ? formData.gr_tipo_internet : undefined,
        gr_recarga_por: formData.tipo === 'Gestion_Remota' ? formData.gr_recarga_por : undefined,
        gr_recarga_monto: formData.tipo === 'Gestion_Remota' ? formData.gr_recarga_monto : undefined,
        gr_ultima_recarga: formData.tipo === 'Gestion_Remota' ? formData.gr_ultima_recarga : undefined,
      });
    }
    setIsModalOpen(false);
  };

  const filteredServicios = servicios.filter(s => {
    const matchesSearch = 
      s.codigo.toLowerCase().includes(searchTerm.toLowerCase()) ||
      s.cliente_nombre.toLowerCase().includes(searchTerm.toLowerCase()) ||
      (s.ubicacion_nombre && s.ubicacion_nombre.toLowerCase().includes(searchTerm.toLowerCase())) ||
      (s.gr_sim_numero && s.gr_sim_numero.includes(searchTerm)) ||
      (s.gr_marca_modelo && s.gr_marca_modelo.toLowerCase().includes(searchTerm.toLowerCase()));

    const matchesTipo = filterTipo === 'todos' || s.tipo === filterTipo;
    const matchesEstado = filterEstado === 'todos' || s.estado === filterEstado;

    return matchesSearch && matchesTipo && matchesEstado;
  });

  return (
    <div className="space-y-6">
      {/* Top Banner & Action */}
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h2 className="text-xl font-bold text-white tracking-tight flex items-center gap-2">
            <Radio className="h-5 w-5 text-sky-400" />
            Servicios Técnicos Instalados
          </h2>
          <p className="text-xs text-slate-400">
            Administración de sistemas CCTV, Alarmas SACI y enlaces de Gestión Remota 4G con SIM en campo.
          </p>
        </div>

        <button
          onClick={handleOpenAdd}
          className="flex items-center gap-2 rounded-lg bg-sky-500 hover:bg-sky-400 text-slate-950 font-bold px-4 py-2 text-xs transition-all shadow-md shadow-sky-500/20 cursor-pointer"
        >
          <Plus className="h-4 w-4 stroke-[2.5]" />
          Nuevo Servicio
        </button>
      </div>

      {/* Quick Stats */}
      <div className="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div className="rounded-xl border border-slate-800 bg-slate-900/60 p-4">
          <p className="text-[11px] font-medium text-slate-400">Total Servicios</p>
          <p className="text-2xl font-bold text-white mt-1">{servicios.length}</p>
        </div>
        <div className="rounded-xl border border-slate-800 bg-slate-900/60 p-4">
          <div className="flex items-center justify-between">
            <p className="text-[11px] font-medium text-slate-400">CCTV</p>
            <Camera className="h-4 w-4 text-blue-400" />
          </div>
          <p className="text-2xl font-bold text-blue-400 mt-1">
            {servicios.filter(s => s.tipo === 'CCTV').length}
          </p>
        </div>
        <div className="rounded-xl border border-slate-800 bg-slate-900/60 p-4">
          <div className="flex items-center justify-between">
            <p className="text-[11px] font-medium text-slate-400">Alarmas SACI</p>
            <ShieldCheck className="h-4 w-4 text-emerald-400" />
          </div>
          <p className="text-2xl font-bold text-emerald-400 mt-1">
            {servicios.filter(s => s.tipo === 'SACI').length}
          </p>
        </div>
        <div className="rounded-xl border border-slate-800 bg-slate-900/60 p-4">
          <div className="flex items-center justify-between">
            <p className="text-[11px] font-medium text-slate-400">Gestión Remota 4G</p>
            <Signal className="h-4 w-4 text-amber-400" />
          </div>
          <p className="text-2xl font-bold text-amber-400 mt-1">
            {servicios.filter(s => s.tipo === 'Gestion_Remota').length}
          </p>
        </div>
      </div>

      {/* Filters & Search */}
      <div className="flex flex-col sm:flex-row items-center justify-between gap-3 bg-slate-900/70 p-3.5 rounded-xl border border-slate-800">
        <div className="relative w-full sm:w-80">
          <Search className="absolute left-3 top-2.5 h-4 w-4 text-slate-400" />
          <input
            type="text"
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
            placeholder="Buscar por cliente, SIM, router o código..."
            className="w-full rounded-lg border border-slate-700 bg-slate-950 pl-9 pr-4 py-1.5 text-xs text-slate-200 placeholder-slate-400 focus:border-sky-500 focus:outline-none"
          />
        </div>

        <div className="flex items-center gap-2 w-full sm:w-auto">
          <select
            value={filterTipo}
            onChange={(e) => setFilterTipo(e.target.value)}
            className="rounded-lg border border-slate-700 bg-slate-950 px-3 py-1.5 text-xs text-slate-300 focus:border-sky-500 focus:outline-none"
          >
            <option value="todos">Todos los Tipos</option>
            <option value="CCTV">CCTV</option>
            <option value="SACI">Alarma SACI</option>
            <option value="Gestion_Remota">Gestión Remota</option>
          </select>

          <select
            value={filterEstado}
            onChange={(e) => setFilterEstado(e.target.value)}
            className="rounded-lg border border-slate-700 bg-slate-950 px-3 py-1.5 text-xs text-slate-300 focus:border-sky-500 focus:outline-none"
          >
            <option value="todos">Todos los Estados</option>
            <option value="Activo">Activo</option>
            <option value="Inactivo">Inactivo</option>
            <option value="En_Reparacion">En Reparación</option>
            <option value="Suspendido">Suspendido</option>
          </select>
        </div>
      </div>

      {/* Services Table */}
      <div className="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/50 shadow-sm">
        <div className="overflow-x-auto">
          <table className="w-full text-left text-xs text-slate-300">
            <thead className="bg-slate-950/80 text-[11px] font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-800">
              <tr>
                <th className="py-3.5 px-4">Código / Tipo</th>
                <th className="py-3.5 px-4">Cliente & Ubicación</th>
                <th className="py-3.5 px-4">Parámetros Técnicos</th>
                <th className="py-3.5 px-4">Brigada</th>
                <th className="py-3.5 px-4">Estado</th>
                <th className="py-3.5 px-4 text-right">Acciones</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-800/80">
              {filteredServicios.map((s) => (
                <tr key={s.id} className="hover:bg-slate-800/40 transition-colors">
                  <td className="py-3 px-4">
                    <span className="font-mono font-bold text-white block">{s.codigo}</span>
                    <span className={`inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded text-[10px] font-semibold ${
                      s.tipo === 'CCTV' 
                        ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' 
                        : s.tipo === 'SACI' 
                        ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' 
                        : 'bg-amber-500/10 text-amber-400 border border-amber-500/20'
                    }`}>
                      {s.tipo === 'CCTV' && <Camera className="h-3 w-3" />}
                      {s.tipo === 'SACI' && <ShieldCheck className="h-3 w-3" />}
                      {s.tipo === 'Gestion_Remota' && <Signal className="h-3 w-3" />}
                      {s.tipo === 'Gestion_Remota' ? 'Gestión Remota' : s.tipo}
                    </span>
                  </td>

                  <td className="py-3 px-4">
                    <span className="font-semibold text-white block">{s.cliente_nombre}</span>
                    {s.ubicacion_nombre && (
                      <span className="text-[11px] text-slate-400 flex items-center gap-1 mt-0.5">
                        <MapPin className="h-3 w-3 text-slate-500" />
                        {s.ubicacion_nombre}
                      </span>
                    )}
                  </td>

                  <td className="py-3 px-4">
                    {s.tipo === 'Gestion_Remota' ? (
                      <div className="space-y-0.5">
                        {s.gr_marca_modelo && (
                          <p className="font-mono text-[11px] text-slate-200">{s.gr_marca_modelo}</p>
                        )}
                        {s.gr_sim_numero && (
                          <p className="text-[11px] text-sky-400 flex items-center gap-1 font-mono">
                            <Phone className="h-3 w-3" /> SIM: {s.gr_sim_numero}
                          </p>
                        )}
                        <div className="flex items-center gap-2 text-[10px] text-slate-400 mt-1">
                          <span className="bg-slate-800 px-1.5 py-0.5 rounded">Net: {s.gr_tipo_internet || 'Filtrado'}</span>
                          <span className="bg-slate-800 px-1.5 py-0.5 rounded">Recarga: ${s.gr_recarga_monto || 360} CUP ({s.gr_recarga_por || 'Nosotros'})</span>
                        </div>
                      </div>
                    ) : (
                      <div>
                        <span className="text-[11px] text-slate-400 block">
                          Instalación: {s.fecha_instalacion || 'N/A'}
                        </span>
                        {s.notas && <span className="text-[11px] text-slate-400 line-clamp-1">{s.notas}</span>}
                      </div>
                    )}
                  </td>

                  <td className="py-3 px-4">
                    <span className="text-slate-300 font-medium">{s.brigada_nombre || 'Sin asignar'}</span>
                  </td>

                  <td className="py-3 px-4">
                    <span className={`inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold ${
                      s.estado === 'Activo' ? 'bg-emerald-500/20 text-emerald-300' :
                      s.estado === 'En_Reparacion' ? 'bg-amber-500/20 text-amber-300' :
                      s.estado === 'Suspendido' ? 'bg-rose-500/20 text-rose-300' :
                      'bg-slate-700/50 text-slate-400'
                    }`}>
                      {s.estado.replace('_', ' ')}
                    </span>
                  </td>

                  <td className="py-3 px-4 text-right">
                    <div className="flex items-center justify-end gap-1.5">
                      <button
                        onClick={() => handleOpenEdit(s)}
                        className="rounded p-1 text-slate-400 hover:bg-slate-800 hover:text-sky-400 transition-colors"
                        title="Editar Servicio"
                      >
                        <Edit3 className="h-4 w-4" />
                      </button>
                      <button
                        onClick={() => onDeleteServicio(s.id)}
                        className="rounded p-1 text-slate-400 hover:bg-slate-800 hover:text-rose-400 transition-colors"
                        title="Eliminar Servicio"
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
      </div>

      {/* Modal Crear / Editar Servicio */}
      {isModalOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4 overflow-y-auto">
          <div className="relative w-full max-w-2xl rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-2xl my-8">
            <div className="flex items-center justify-between border-b border-slate-800 pb-3 mb-5">
              <h3 className="text-base font-bold text-white flex items-center gap-2">
                <Radio className="h-5 w-5 text-sky-400" />
                {editingServicio ? `Editar Servicio ${editingServicio.codigo}` : 'Registrar Nuevo Servicio'}
              </h3>
              <button
                onClick={() => setIsModalOpen(false)}
                className="text-slate-400 hover:text-white"
              >
                <X className="h-5 w-5" />
              </button>
            </div>

            <form onSubmit={handleSubmit} className="space-y-4">
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">
                    Cliente *
                  </label>
                  <select
                    required
                    value={formData.cliente_id}
                    onChange={(e) => {
                      const cid = e.target.value;
                      const c = clientes.find(item => item.id === cid);
                      setFormData({
                        ...formData,
                        cliente_id: cid,
                        cliente_ubicacion_id: c?.ubicaciones[0]?.id || ''
                      });
                    }}
                    className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-sky-500 focus:outline-none"
                  >
                    {clientes.map(c => (
                      <option key={c.id} value={c.id}>{c.nombre}</option>
                    ))}
                  </select>
                </div>

                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">
                    Ubicación / Negocio
                  </label>
                  <select
                    value={formData.cliente_ubicacion_id}
                    onChange={(e) => setFormData({ ...formData, cliente_ubicacion_id: e.target.value })}
                    className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-sky-500 focus:outline-none"
                  >
                    {selectedCliente?.ubicaciones.map(u => (
                      <option key={u.id} value={u.id}>{u.nombre} ({u.tipo})</option>
                    ))}
                  </select>
                </div>
              </div>

              <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">
                    Tipo de Servicio *
                  </label>
                  <select
                    value={formData.tipo}
                    onChange={(e) => setFormData({ ...formData, tipo: e.target.value as any })}
                    className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-sky-500 focus:outline-none"
                  >
                    <option value="CCTV">CCTV (Cámaras)</option>
                    <option value="SACI">Alarma SACI</option>
                    <option value="Gestion_Remota">Gestión Remota (4G)</option>
                  </select>
                </div>

                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">
                    Brigada de Instalación
                  </label>
                  <select
                    value={formData.brigada_id}
                    onChange={(e) => setFormData({ ...formData, brigada_id: e.target.value })}
                    className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-sky-500 focus:outline-none"
                  >
                    <option value="">Sin Brigada</option>
                    {brigadas.map(b => (
                      <option key={b.id} value={b.id}>{b.nombre} ({b.zona})</option>
                    ))}
                  </select>
                </div>

                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">
                    Estado
                  </label>
                  <select
                    value={formData.estado}
                    onChange={(e) => setFormData({ ...formData, estado: e.target.value as any })}
                    className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-sky-500 focus:outline-none"
                  >
                    <option value="Activo">Activo</option>
                    <option value="Inactivo">Inactivo</option>
                    <option value="En_Reparacion">En Reparación</option>
                    <option value="Suspendido">Suspendido</option>
                  </select>
                </div>
              </div>

              {/* Sección Condicional: Gestión Remota */}
              {formData.tipo === 'Gestion_Remota' && (
                <div className="rounded-xl border border-sky-500/30 bg-sky-950/20 p-4 space-y-3">
                  <div className="flex items-center gap-2 text-sky-400 font-bold text-xs">
                    <Signal className="h-4 w-4" />
                    Parámetros de Enlace 4G / Gestión Remota (Laravel / Filament)
                  </div>

                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                      <label className="block text-[11px] font-medium text-slate-400 mb-1">
                        Solución Técnica
                      </label>
                      <select
                        value={formData.gr_tipo_solucion}
                        onChange={(e) => setFormData({ ...formData, gr_tipo_solucion: e.target.value as any })}
                        className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-sky-500 focus:outline-none"
                      >
                        <option value="Router4g">Router 4G</option>
                        <option value="Router+Modem">Router + Módem</option>
                        <option value="Router+ADSL">Router + ADSL</option>
                        <option value="Otros">Otros</option>
                      </select>
                    </div>

                    <div>
                      <label className="block text-[11px] font-medium text-slate-400 mb-1">
                        Número SIM Card (Cubacel)
                      </label>
                      <input
                        type="text"
                        value={formData.gr_sim_numero}
                        onChange={(e) => setFormData({ ...formData, gr_sim_numero: e.target.value })}
                        placeholder="+53 5 XXX XXXX"
                        className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs font-mono text-slate-200 focus:border-sky-500 focus:outline-none"
                      />
                    </div>
                  </div>

                  <div className="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                      <label className="block text-[11px] font-medium text-slate-400 mb-1">
                        Marca / Modelo del Equipo
                      </label>
                      <input
                        type="text"
                        value={formData.gr_marca_modelo}
                        onChange={(e) => setFormData({ ...formData, gr_marca_modelo: e.target.value })}
                        placeholder="Ej: TP-Link TL-MR6400, MikroTik"
                        className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-sky-500 focus:outline-none"
                      />
                    </div>

                    <div>
                      <label className="block text-[11px] font-medium text-slate-400 mb-1">
                        Tipo de Internet
                      </label>
                      <select
                        value={formData.gr_tipo_internet}
                        onChange={(e) => setFormData({ ...formData, gr_tipo_internet: e.target.value as any })}
                        className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-sky-500 focus:outline-none"
                      >
                        <option value="Filtrado">Filtrado (VPN / Cámaras)</option>
                        <option value="Abierto">Abierto</option>
                        <option value="Cerrado">Cerrado</option>
                      </select>
                    </div>

                    <div>
                      <label className="block text-[11px] font-medium text-slate-400 mb-1">
                        Responsable Recarga ($360 CUP)
                      </label>
                      <select
                        value={formData.gr_recarga_por}
                        onChange={(e) => setFormData({ ...formData, gr_recarga_por: e.target.value as any })}
                        className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-sky-500 focus:outline-none"
                      >
                        <option value="Nosotros">Nosotros (DataPlus)</option>
                        <option value="Cliente">El Cliente</option>
                      </select>
                    </div>
                  </div>
                </div>
              )}

              <div>
                <label className="block text-[11px] font-medium text-slate-400 mb-1">
                  Notas Técnicas
                </label>
                <textarea
                  rows={2}
                  value={formData.notas}
                  onChange={(e) => setFormData({ ...formData, notas: e.target.value })}
                  placeholder="Detalles de instalación, IP local, puertos abiertos..."
                  className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-sky-500 focus:outline-none"
                />
              </div>

              <div className="flex items-center justify-end gap-2 pt-3 border-t border-slate-800">
                <button
                  type="button"
                  onClick={() => setIsModalOpen(false)}
                  className="rounded-lg border border-slate-700 bg-slate-800 px-4 py-2 text-xs text-slate-300 hover:bg-slate-700"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  className="rounded-lg bg-sky-500 hover:bg-sky-400 px-5 py-2 text-xs font-bold text-slate-950 shadow-md shadow-sky-500/20"
                >
                  Guardar Servicio
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
};
