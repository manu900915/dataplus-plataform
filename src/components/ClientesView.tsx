import React, { useState } from 'react';
import { 
  Users2, 
  Plus, 
  Search, 
  MapPin, 
  Phone, 
  Mail, 
  Building2, 
  User, 
  ChevronDown, 
  ChevronUp,
  CheckCircle2,
  XCircle,
  X
} from 'lucide-react';
import { Cliente, ClienteUbicacion } from '../types';

interface ClientesViewProps {
  clientes: Cliente[];
  onAddCliente: (cliente: Cliente) => void;
  onUpdateCliente?: (cliente: Cliente) => void;
  onDeleteCliente?: (id: string) => void;
  onAddUbicacion?: (clienteId: string, ubicacion: ClienteUbicacion) => void;
  onBulkUpdateClientesEstado?: (ids: string[], activo: boolean) => void;
}

export const ClientesView: React.FC<ClientesViewProps> = ({
  clientes,
  onAddCliente,
  onUpdateCliente,
  onAddUbicacion,
  onBulkUpdateClientesEstado,
}) => {
  const [searchTerm, setSearchTerm] = useState('');
  const [statusFilter, setStatusFilter] = useState<'all' | 'active' | 'inactive'>('all');
  const [selectedIds, setSelectedIds] = useState<string[]>([]);
  const [expandedClientId, setExpandedClientId] = useState<string | null>(clientes[0]?.id || null);

  // New Client Modal State
  const [showClientModal, setShowClientModal] = useState(false);
  const [codigo, setCodigo] = useState(`CLI-${String(clientes.length + 1).padStart(3, '0')}`);
  const [tipoPersona, setTipoPersona] = useState<'juridica' | 'natural'>('juridica');
  const [nombre, setNombre] = useState('');
  const [nombreComercial, setNombreComercial] = useState('');
  const [documento, setDocumento] = useState('');
  const [telefono, setTelefono] = useState('');
  const [email, setEmail] = useState('');
  const [direccion, setDireccion] = useState('');
  const [provincia, setProvincia] = useState('La Habana');
  const [municipio, setMunicipio] = useState('Plaza de la Revolución');
  const [notas, setNotas] = useState('');

  // Add Location Modal State
  const [targetClienteForUb, setTargetClienteForUb] = useState<Cliente | null>(null);
  const [ubNombre, setUbNombre] = useState('');
  const [ubTipo, setUbTipo] = useState<'negocio' | 'residencial'>('negocio');
  const [ubTipoNegocio, setUbTipoNegocio] = useState('');
  const [ubDireccion, setUbDireccion] = useState('');
  const [ubContacto, setUbContacto] = useState('');
  const [ubTelefono, setUbTelefono] = useState('');

  const filteredClientes = clientes.filter(c => {
    const matchesSearch =
      c.nombre.toLowerCase().includes(searchTerm.toLowerCase()) ||
      c.codigo.toLowerCase().includes(searchTerm.toLowerCase()) ||
      (c.nombre_comercial && c.nombre_comercial.toLowerCase().includes(searchTerm.toLowerCase())) ||
      (c.telefono && c.telefono.includes(searchTerm));

    const matchesStatus =
      statusFilter === 'all' ||
      (statusFilter === 'active' && c.activo) ||
      (statusFilter === 'inactive' && !c.activo);

    return matchesSearch && matchesStatus;
  });

  const allVisibleSelected =
    filteredClientes.length > 0 &&
    filteredClientes.every((c) => selectedIds.includes(c.id));

  const toggleSelectCliente = (id: string) => {
    setSelectedIds((prev) =>
      prev.includes(id) ? prev.filter((item) => item !== id) : [...prev, id]
    );
  };

  const toggleSelectAllVisible = () => {
    if (allVisibleSelected) {
      const visibleSet = new Set(filteredClientes.map((c) => c.id));
      setSelectedIds((prev) => prev.filter((id) => !visibleSet.has(id)));
    } else {
      const merged = new Set([...selectedIds, ...filteredClientes.map((c) => c.id)]);
      setSelectedIds(Array.from(merged));
    }
  };

  const handleToggleActivoCliente = (cliente: Cliente) => {
    onUpdateCliente?.({
      ...cliente,
      activo: !cliente.activo,
    });
  };

  const handleBulkSetActivo = (activo: boolean) => {
    if (selectedIds.length === 0) return;
    if (onBulkUpdateClientesEstado) {
      onBulkUpdateClientesEstado(selectedIds, activo);
    } else if (onUpdateCliente) {
      clientes
        .filter((c) => selectedIds.includes(c.id))
        .forEach((c) => onUpdateCliente({ ...c, activo }));
    }
    setSelectedIds([]);
  };

  // Escape to close modals
  React.useEffect(() => {
    const handleKeyDown = (e: KeyboardEvent) => {
      if (e.key === 'Escape') {
        setShowClientModal(false);
        setTargetClienteForUb(null);
      }
    };
    window.addEventListener('keydown', handleKeyDown);
    return () => window.removeEventListener('keydown', handleKeyDown);
  }, []);

  const handleCreateCliente = (e: React.FormEvent) => {
    e.preventDefault();
    if (!nombre) return;

    const newCli: Cliente = {
      id: `cli-${Date.now()}`,
      codigo,
      tipo_persona: tipoPersona,
      documento,
      nombre,
      nombre_comercial: nombreComercial,
      email,
      telefono,
      direccion,
      provincia,
      municipio,
      notas,
      activo: true,
      created_at: new Date().toISOString(),
      ubicaciones: [
        {
          id: `ub-${Date.now()}-1`,
          cliente_id: `cli-${Date.now()}`,
          nombre: 'Sede Principal',
          tipo: tipoPersona === 'juridica' ? 'negocio' : 'residencial',
          direccion,
          provincia,
          municipio,
          contacto_nombre: nombre,
          contacto_telefono: telefono,
          activo: true
        }
      ]
    };

    onAddCliente(newCli);
    setShowClientModal(false);
    setNombre('');
    setNombreComercial('');
    setCodigo(`CLI-${String(clientes.length + 2).padStart(3, '0')}`);
  };

  const handleCreateUbicacion = (e: React.FormEvent) => {
    e.preventDefault();
    if (!targetClienteForUb || !ubNombre) return;

    const newUb: ClienteUbicacion = {
      id: `ub-${Date.now()}`,
      cliente_id: targetClienteForUb.id,
      nombre: ubNombre,
      tipo: ubTipo,
      tipo_negocio_nombre: ubTipoNegocio || undefined,
      direccion: ubDireccion,
      provincia: targetClienteForUb.provincia,
      municipio: targetClienteForUb.municipio,
      contacto_nombre: ubContacto,
      contacto_telefono: ubTelefono,
      activo: true
    };

    if (onAddUbicacion) {
      onAddUbicacion(targetClienteForUb.id, newUb);
    } else if (onUpdateCliente) {
      onUpdateCliente({
        ...targetClienteForUb,
        ubicaciones: [...targetClienteForUb.ubicaciones, newUb],
      });
    }
    setTargetClienteForUb(null);
    setUbNombre('');
    setUbDireccion('');
    setUbContacto('');
    setUbTelefono('');
  };

  return (
    <div className="space-y-6">
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h2 className="text-xl font-bold text-white tracking-tight flex items-center gap-2">
            <Users2 className="h-5 w-5 text-teal-400" />
            Directorio de Clientes & Sedes Comerciales
          </h2>
          <p className="text-xs text-slate-400">
            Administración de clientes corporativos, puntos de venta y cuentas residenciales.
          </p>
        </div>

        <button
          onClick={() => setShowClientModal(true)}
          className="flex items-center gap-1.5 rounded-lg bg-teal-500 hover:bg-teal-400 text-slate-950 font-bold px-4 py-2 text-xs transition-all shadow-md shadow-teal-500/20 cursor-pointer"
        >
          <Plus className="h-4 w-4" />
          Registrar Cliente
        </button>
      </div>

      {/* Search & Status Filters + Bulk Actions */}
      <div className="space-y-3 rounded-xl border border-slate-800 bg-slate-900/60 p-3">
        <div className="flex flex-col sm:flex-row gap-3">
          <div className="relative flex-1">
            <Search className="absolute left-3 top-2.5 h-3.5 w-3.5 text-slate-400" />
            <input
              type="text"
              placeholder="Buscar por código, razón social, nombre comercial o teléfono..."
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              className="w-full rounded-lg border border-slate-800 bg-slate-950 pl-9 pr-3 py-2 text-xs text-slate-200 placeholder-slate-500 focus:border-teal-500 focus:outline-none"
            />
          </div>

          <select
            value={statusFilter}
            onChange={(e) => setStatusFilter(e.target.value as 'all' | 'active' | 'inactive')}
            className="rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-teal-500 focus:outline-none"
          >
            <option value="all">Todos los estados ({clientes.length})</option>
            <option value="active">Solo activos ({clientes.filter(c => c.activo).length})</option>
            <option value="inactive">Solo inactivos ({clientes.filter(c => !c.activo).length})</option>
          </select>
        </div>

        <div className="flex flex-wrap items-center justify-between gap-2 pt-2 border-t border-slate-800/70">
          <label className="inline-flex items-center gap-2 text-xs text-slate-300 cursor-pointer select-none">
            <input
              type="checkbox"
              checked={allVisibleSelected}
              onChange={toggleSelectAllVisible}
              disabled={filteredClientes.length === 0}
              className="h-3.5 w-3.5 rounded border-slate-700 bg-slate-950 text-teal-500 focus:ring-teal-500"
            />
            <span>Seleccionar visibles ({filteredClientes.length})</span>
          </label>

          {selectedIds.length > 0 && (
            <div className="flex flex-wrap items-center gap-2">
              <span className="text-xs font-medium text-teal-400">
                {selectedIds.length} seleccionado(s)
              </span>
              <button
                type="button"
                onClick={() => handleBulkSetActivo(true)}
                className="inline-flex items-center gap-1 rounded-lg bg-emerald-500/15 hover:bg-emerald-500/25 border border-emerald-500/40 px-3 py-1.5 text-xs font-semibold text-emerald-300 transition-colors cursor-pointer"
              >
                <CheckCircle2 className="h-3.5 w-3.5" />
                Activar seleccionados
              </button>
              <button
                type="button"
                onClick={() => handleBulkSetActivo(false)}
                className="inline-flex items-center gap-1 rounded-lg bg-amber-500/15 hover:bg-amber-500/25 border border-amber-500/40 px-3 py-1.5 text-xs font-semibold text-amber-300 transition-colors cursor-pointer"
              >
                <XCircle className="h-3.5 w-3.5" />
                Desactivar seleccionados
              </button>
              <button
                type="button"
                onClick={() => setSelectedIds([])}
                className="rounded-lg border border-slate-700 bg-slate-800 px-2.5 py-1.5 text-xs text-slate-300 hover:bg-slate-700 cursor-pointer"
              >
                Limpiar
              </button>
            </div>
          )}
        </div>
      </div>

      {/* Clients Cards / List */}
      <div className="space-y-4">
        {filteredClientes.map((cliente) => {
          const isExpanded = expandedClientId === cliente.id;
          const isSelected = selectedIds.includes(cliente.id);

          return (
            <div
              key={cliente.id}
              className={`rounded-2xl border p-5 shadow-sm transition-all ${
                isSelected
                  ? 'border-teal-500/60 bg-slate-900/90'
                  : 'border-slate-800 bg-slate-900/70'
              } ${!cliente.activo ? 'opacity-75' : ''}`}
            >
              <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div className="flex items-start gap-3">
                  <input
                    type="checkbox"
                    checked={isSelected}
                    onChange={() => toggleSelectCliente(cliente.id)}
                    aria-label={`Seleccionar ${cliente.nombre}`}
                    className="mt-2.5 h-4 w-4 rounded border-slate-700 bg-slate-950 text-teal-500 focus:ring-teal-500 cursor-pointer"
                  />
                  <div className="rounded-xl bg-slate-800 p-2.5 text-teal-400 border border-slate-700">
                    {cliente.tipo_persona === 'juridica' ? (
                      <Building2 className="h-5 w-5" />
                    ) : (
                      <User className="h-5 w-5" />
                    )}
                  </div>
                  <div>
                    <div className="flex flex-wrap items-center gap-2">
                      <span className="font-mono text-xs font-bold text-teal-400">{cliente.codigo}</span>
                      <span className={`px-2 py-0.2 rounded text-[10px] font-semibold uppercase ${
                        cliente.tipo_persona === 'juridica'
                          ? 'bg-sky-500/20 text-sky-300'
                          : 'bg-emerald-500/20 text-emerald-300'
                      }`}>
                        {cliente.tipo_persona === 'juridica' ? 'Persona Jurídica' : 'Persona Natural'}
                      </span>
                      <span className={`px-2 py-0.5 rounded text-[10px] font-semibold uppercase ${
                        cliente.activo
                          ? 'bg-emerald-500/15 text-emerald-300 border border-emerald-500/30'
                          : 'bg-rose-500/15 text-rose-300 border border-rose-500/30'
                      }`}>
                        {cliente.activo ? 'Activo' : 'Inactivo'}
                      </span>
                    </div>
                    <h3 className="text-base font-bold text-white mt-0.5">
                      {cliente.nombre}
                      {cliente.nombre_comercial && (
                        <span className="text-xs font-normal text-slate-400 ml-2">
                          ({cliente.nombre_comercial})
                        </span>
                      )}
                    </h3>
                  </div>
                </div>

                <div className="flex flex-wrap items-center gap-2">
                  <button
                    type="button"
                    onClick={() => handleToggleActivoCliente(cliente)}
                    className={`flex items-center gap-1 rounded border px-3 py-1.5 text-xs font-semibold transition-colors cursor-pointer ${
                      cliente.activo
                        ? 'bg-amber-500/10 hover:bg-amber-500/20 text-amber-300 border-amber-500/30'
                        : 'bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-300 border-emerald-500/30'
                    }`}
                  >
                    {cliente.activo ? (
                      <>
                        <XCircle className="h-3.5 w-3.5" />
                        Desactivar
                      </>
                    ) : (
                      <>
                        <CheckCircle2 className="h-3.5 w-3.5" />
                        Activar
                      </>
                    )}
                  </button>

                  <button
                    type="button"
                    onClick={() => {
                      setTargetClienteForUb(cliente);
                    }}
                    className="flex items-center gap-1 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 px-3 py-1.5 text-xs font-medium transition-colors cursor-pointer"
                  >
                    <Plus className="h-3 w-3 text-teal-400" />
                    + Sede
                  </button>

                  <button
                    type="button"
                    onClick={() => setExpandedClientId(isExpanded ? null : cliente.id)}
                    className="rounded p-1.5 text-slate-400 hover:bg-slate-800 hover:text-white transition-colors cursor-pointer"
                  >
                    {isExpanded ? <ChevronUp className="h-4 w-4" /> : <ChevronDown className="h-4 w-4" />}
                  </button>
                </div>
              </div>

              {/* Main Contact Metadata Row */}
              <div className="mt-3 grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs text-slate-300 border-t border-slate-800/80 pt-3">
                <div className="flex items-center gap-2">
                  <Phone className="h-3.5 w-3.5 text-teal-400 flex-shrink-0" />
                  <span className="font-mono">{cliente.telefono || 'Sin teléfono'}</span>
                </div>
                <div className="flex items-center gap-2">
                  <Mail className="h-3.5 w-3.5 text-teal-400 flex-shrink-0" />
                  <span className="truncate">{cliente.email || 'Sin correo'}</span>
                </div>
                <div className="flex items-center gap-2">
                  <MapPin className="h-3.5 w-3.5 text-teal-400 flex-shrink-0" />
                  <span>{cliente.municipio ? `${cliente.municipio}, ` : ''}{cliente.provincia}</span>
                </div>
              </div>

              {/* Expanded: Locations List */}
              {isExpanded && (
                <div className="mt-4 pt-4 border-t border-slate-800 space-y-3">
                  <div className="flex items-center justify-between text-xs font-semibold text-slate-400 uppercase tracking-wider">
                    <span>Ubicaciones & Sedes ({cliente.ubicaciones.length})</span>
                  </div>

                  <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
                    {cliente.ubicaciones.map((ub) => (
                      <div
                        key={ub.id}
                        className="rounded-xl border border-slate-800 bg-slate-950/60 p-3 space-y-1.5"
                      >
                        <div className="flex items-center justify-between">
                          <span className="font-bold text-white text-xs">{ub.nombre}</span>
                          <span className="text-[10px] uppercase font-mono px-1.5 py-0.2 rounded bg-slate-800 text-teal-400">
                            {ub.tipo}
                          </span>
                        </div>
                        {ub.tipo_negocio_nombre && (
                          <div className="text-[11px] text-teal-300 font-medium">
                            Giro: {ub.tipo_negocio_nombre}
                          </div>
                        )}
                        <div className="text-xs text-slate-400 flex items-center gap-1.5">
                          <MapPin className="h-3 w-3 text-slate-500" />
                          <span>{ub.direccion || 'Sin dirección registrada'}</span>
                        </div>
                        {ub.contacto_nombre && (
                          <div className="text-[11px] text-slate-300 flex items-center justify-between pt-1 border-t border-slate-900">
                            <span>Contacto: {ub.contacto_nombre}</span>
                            <span className="font-mono text-teal-400">{ub.contacto_telefono}</span>
                          </div>
                        )}
                      </div>
                    ))}
                  </div>

                  {cliente.notas && (
                    <div className="rounded-lg bg-slate-950/40 p-2.5 text-xs text-slate-400 border border-slate-800/60">
                      <strong>Observaciones:</strong> {cliente.notas}
                    </div>
                  )}
                </div>
              )}
            </div>
          );
        })}

        {filteredClientes.length === 0 && (
          <div className="rounded-2xl border border-dashed border-slate-800 bg-slate-900/30 p-12 text-center">
            <Users2 className="h-10 w-10 text-slate-600 mx-auto mb-3" />
            <p className="text-sm font-bold text-white">No hay clientes registrados</p>
            <p className="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
              La plataforma está lista para comenzar limpia. Registra tu primer cliente o negocio para iniciar el flujo de operaciones.
            </p>
            <button
              onClick={() => setShowClientModal(true)}
              className="mt-4 inline-flex items-center gap-1.5 rounded-lg bg-teal-500 hover:bg-teal-400 text-slate-950 font-bold px-4 py-2 text-xs transition-all shadow-md shadow-teal-500/20 cursor-pointer"
            >
              <Plus className="h-4 w-4 stroke-[2.5]" />
              Registrar Primer Cliente
            </button>
          </div>
        )}
      </div>

      {/* Modal: New Client */}
      {showClientModal && (
        <div onClick={() => setShowClientModal(false)} className="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 p-4 backdrop-blur-sm">
          <div onClick={(e) => e.stopPropagation()} className="w-full max-w-lg rounded-2xl border border-slate-800 bg-slate-900 shadow-2xl overflow-hidden">
            <div className="flex items-center justify-between border-b border-slate-800 px-6 py-4 bg-slate-950/60">
              <h3 className="text-base font-bold text-white flex items-center gap-2">
                <Users2 className="h-4 w-4 text-teal-400" />
                Registrar Cliente
              </h3>
              <button
                onClick={() => setShowClientModal(false)}
                className="text-slate-400 hover:text-white"
              >
                <X className="h-5 w-5" />
              </button>
            </div>

            <form onSubmit={handleCreateCliente} className="p-6 space-y-4 max-h-[80vh] overflow-y-auto">
              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">
                    Código de Cliente
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
                    Tipo de Persona
                  </label>
                  <select
                    value={tipoPersona}
                    onChange={(e) => setTipoPersona(e.target.value as any)}
                    className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-teal-500 focus:outline-none"
                  >
                    <option value="juridica">Persona Jurídica (Empresa/S.R.L.)</option>
                    <option value="natural">Persona Natural (Particular)</option>
                  </select>
                </div>
              </div>

              <div>
                <label className="block text-[11px] font-medium text-slate-400 mb-1">
                  Nombre o Razón Social
                </label>
                <input
                  type="text"
                  required
                  placeholder="Ej: Vinil Arte S.R.L."
                  value={nombre}
                  onChange={(e) => setNombre(e.target.value)}
                  className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-teal-500 focus:outline-none"
                />
              </div>

              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">
                    Nombre Comercial (Opcional)
                  </label>
                  <input
                    type="text"
                    placeholder="Ej: Vinil Arte Publicidad"
                    value={nombreComercial}
                    onChange={(e) => setNombreComercial(e.target.value)}
                    className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-teal-500 focus:outline-none"
                  />
                </div>
                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">
                    Documento / NIF
                  </label>
                  <input
                    type="text"
                    placeholder="Ej: J-94829102-1"
                    value={documento}
                    onChange={(e) => setDocumento(e.target.value)}
                    className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs font-mono text-slate-200 focus:border-teal-500 focus:outline-none"
                  />
                </div>
              </div>

              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">
                    Teléfono
                  </label>
                  <input
                    type="text"
                    required
                    placeholder="+53 7 832 9401"
                    value={telefono}
                    onChange={(e) => setTelefono(e.target.value)}
                    className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs font-mono text-slate-200 focus:border-teal-500 focus:outline-none"
                  />
                </div>
                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">
                    Correo Electrónico
                  </label>
                  <input
                    type="email"
                    placeholder="contacto@empresa.cu"
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                    className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-teal-500 focus:outline-none"
                  />
                </div>
              </div>

              <div>
                <label className="block text-[11px] font-medium text-slate-400 mb-1">
                  Dirección Principal
                </label>
                <input
                  type="text"
                  required
                  placeholder="Calle, número, entre calles..."
                  value={direccion}
                  onChange={(e) => setDireccion(e.target.value)}
                  className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-teal-500 focus:outline-none"
                />
              </div>

              <div className="flex justify-end gap-3 pt-3 border-t border-slate-800">
                <button
                  type="button"
                  onClick={() => setShowClientModal(false)}
                  className="rounded-lg border border-slate-700 bg-slate-800 px-4 py-2 text-xs font-medium text-slate-300 hover:bg-slate-700 cursor-pointer"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  className="rounded-lg bg-teal-500 hover:bg-teal-400 text-slate-950 px-5 py-2 text-xs font-bold transition-all shadow-md shadow-teal-500/20 cursor-pointer"
                >
                  Registrar
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      {/* Modal: Add Location */}
      {targetClienteForUb && (
        <div onClick={() => setTargetClienteForUb(null)} className="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 p-4 backdrop-blur-sm">
          <div onClick={(e) => e.stopPropagation()} className="w-full max-w-md rounded-2xl border border-slate-800 bg-slate-900 shadow-2xl overflow-hidden">
            <div className="flex items-center justify-between border-b border-slate-800 px-6 py-4 bg-slate-950/60">
              <div>
                <h3 className="text-base font-bold text-white flex items-center gap-2">
                  <MapPin className="h-4 w-4 text-teal-400" />
                  Agregar Sede / Sucursal
                </h3>
                <p className="text-[11px] text-slate-400 mt-0.5">Cliente: {targetClienteForUb.nombre}</p>
              </div>
              <button
                onClick={() => setTargetClienteForUb(null)}
                className="text-slate-400 hover:text-white"
              >
                <X className="h-5 w-5" />
              </button>
            </div>

            <form onSubmit={handleCreateUbicacion} className="p-6 space-y-4">
              <div>
                <label className="block text-[11px] font-medium text-slate-400 mb-1">
                  Nombre de la Sede
                </label>
                <input
                  type="text"
                  required
                  placeholder="Ej: Sucursal Miramar / Taller Este"
                  value={ubNombre}
                  onChange={(e) => setUbNombre(e.target.value)}
                  className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-teal-500 focus:outline-none"
                />
              </div>

              <div>
                <label className="block text-[11px] font-medium text-slate-400 mb-1">
                  Giro Comercial / Tipo de Negocio
                </label>
                <input
                  type="text"
                  placeholder="Ej: Tienda Comercial, Panadería, Hostal..."
                  value={ubTipoNegocio}
                  onChange={(e) => setUbTipoNegocio(e.target.value)}
                  className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-teal-500 focus:outline-none"
                />
              </div>

              <div>
                <label className="block text-[11px] font-medium text-slate-400 mb-1">
                  Dirección Exacta
                </label>
                <input
                  type="text"
                  required
                  placeholder="Calle y número..."
                  value={ubDireccion}
                  onChange={(e) => setUbDireccion(e.target.value)}
                  className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-teal-500 focus:outline-none"
                />
              </div>

              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">
                    Contacto Local
                  </label>
                  <input
                    type="text"
                    placeholder="Nombre del encargado"
                    value={ubContacto}
                    onChange={(e) => setUbContacto(e.target.value)}
                    className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-teal-500 focus:outline-none"
                  />
                </div>
                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">
                    Teléfono Local
                  </label>
                  <input
                    type="text"
                    placeholder="+53 5 000 0000"
                    value={ubTelefono}
                    onChange={(e) => setUbTelefono(e.target.value)}
                    className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs font-mono text-slate-200 focus:border-teal-500 focus:outline-none"
                  />
                </div>
              </div>

              <div className="flex justify-end gap-3 pt-3 border-t border-slate-800">
                <button
                  type="button"
                  onClick={() => setTargetClienteForUb(null)}
                  className="rounded-lg border border-slate-700 bg-slate-800 px-4 py-2 text-xs font-medium text-slate-300 hover:bg-slate-700 cursor-pointer"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  className="rounded-lg bg-teal-500 hover:bg-teal-400 text-slate-950 px-5 py-2 text-xs font-bold transition-all shadow-md shadow-teal-500/20 cursor-pointer"
                >
                  Guardar Sede
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
};
