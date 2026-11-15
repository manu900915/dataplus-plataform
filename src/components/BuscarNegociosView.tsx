import React, { useState } from 'react';
import { 
  Search, 
  Building2, 
  MapPin, 
  Phone, 
  Mail, 
  User, 
  ExternalLink, 
  FolderPlus, 
  AlertCircle 
} from 'lucide-react';
import { Cliente } from '../types';

interface BuscarNegociosViewProps {
  clientes: Cliente[];
  onNavigateToProjects: (clienteId: string) => void;
  onNavigateToIncidencias: (clienteId: string) => void;
}

export const BuscarNegociosView: React.FC<BuscarNegociosViewProps> = ({
  clientes,
  onNavigateToProjects,
  onNavigateToIncidencias,
}) => {
  const [query, setQuery] = useState('');

  // Flatten all business locations
  const allNegocios = clientes.flatMap(cli => 
    cli.ubicaciones
      .filter(ub => ub.tipo === 'negocio' && ub.activo)
      .map(ub => ({
        clienteId: cli.id,
        clienteCodigo: cli.codigo,
        clienteNombre: cli.nombre,
        clienteTelefono: cli.telefono,
        clienteEmail: cli.email,
        ubicacionId: ub.id,
        ubicacionNombre: ub.nombre,
        ubicacionDireccion: ub.direccion,
        municipio: ub.municipio || cli.municipio,
        provincia: ub.provincia || cli.provincia,
        tipoNegocio: ub.tipo_negocio_nombre || 'Comercial General',
        contactoNombre: ub.contacto_nombre || cli.nombre,
        contactoTelefono: ub.contacto_telefono || cli.telefono
      }))
  );

  const filtered = query.trim().length >= 2 
    ? allNegocios.filter(item => 
        item.ubicacionNombre.toLowerCase().includes(query.toLowerCase()) ||
        item.clienteNombre.toLowerCase().includes(query.toLowerCase()) ||
        item.tipoNegocio.toLowerCase().includes(query.toLowerCase()) ||
        (item.municipio && item.municipio.toLowerCase().includes(query.toLowerCase())) ||
        (item.contactoNombre && item.contactoNombre.toLowerCase().includes(query.toLowerCase()))
      )
    : allNegocios;

  return (
    <div className="space-y-6">
      <div>
        <h2 className="text-xl font-bold text-white tracking-tight flex items-center gap-2">
          <Search className="h-5 w-5 text-teal-400" />
          Búsqueda Avanzada de Negocios & Sedes
        </h2>
        <p className="text-xs text-slate-400">
          Localice establecimientos comerciales, talleres y locales contratantes por nombre, contacto o zona.
        </p>
      </div>

      {/* Search Input matching Filament style */}
      <div className="rounded-2xl border border-slate-800 bg-slate-900/70 p-5 shadow-sm space-y-3">
        <label className="block text-xs font-semibold uppercase tracking-wider text-slate-300">
          Buscar establecimiento comercial
        </label>
        <div className="relative">
          <Search className="absolute left-4 top-3.5 h-4 w-4 text-slate-400" />
          <input
            type="text"
            value={query}
            onChange={(e) => setQuery(e.target.value)}
            placeholder="Ej: Vinil Arte, Cucaña, El Patio, Peaky Pan, Vedado, Miramar..."
            className="w-full rounded-xl border border-slate-700 bg-slate-950 pl-11 pr-4 py-3 text-sm text-slate-100 placeholder-slate-500 focus:border-teal-500 focus:outline-none shadow-inner"
            autoFocus
          />
        </div>
        <div className="flex flex-wrap items-center gap-2 text-xs text-slate-400">
          <span>Sugerencias rápidas:</span>
          {['Vinil Arte', 'Cucaña', 'El Patio', 'Peaky Pan'].map((tag) => (
            <button
              key={tag}
              onClick={() => setQuery(tag)}
              className="rounded-md bg-slate-800 px-2 py-0.5 text-[11px] text-teal-400 hover:bg-slate-700 transition-colors cursor-pointer"
            >
              {tag}
            </button>
          ))}
          {query && (
            <button
              onClick={() => setQuery('')}
              className="text-[11px] text-slate-500 hover:text-slate-300 underline ml-auto cursor-pointer"
            >
              Limpiar filtro
            </button>
          )}
        </div>
      </div>

      {/* Results Count */}
      <div className="flex items-center justify-between text-xs text-slate-400 px-1">
        <span>Resultados encontrados: <strong className="text-white">{filtered.length}</strong></span>
        <span>Mostrando locales comerciales en servicio</span>
      </div>

      {/* Results Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
        {filtered.map((negocio) => (
          <div
            key={negocio.ubicacionId}
            className="rounded-xl border border-slate-800 bg-slate-900/80 p-4 shadow-sm hover:border-teal-500/30 transition-all flex flex-col justify-between"
          >
            <div>
              <div className="flex items-center justify-between pb-2 border-b border-slate-800">
                <span className="font-mono text-xs font-semibold text-teal-400">
                  {negocio.clienteCodigo}
                </span>
                <span className="rounded bg-teal-500/10 px-2 py-0.5 text-[10px] font-semibold text-teal-400 border border-teal-500/20">
                  {negocio.tipoNegocio}
                </span>
              </div>

              <div className="mt-2.5">
                <h3 className="text-base font-bold text-white">{negocio.ubicacionNombre}</h3>
                <div className="text-xs text-slate-300 font-medium">{negocio.clienteNombre}</div>
              </div>

              <div className="mt-3 space-y-1.5 text-xs text-slate-300 bg-slate-950/60 p-3 rounded-lg border border-slate-800/80">
                <div className="flex items-center gap-2">
                  <MapPin className="h-3.5 w-3.5 text-slate-400 flex-shrink-0" />
                  <span>{negocio.ubicacionDireccion}, {negocio.municipio}</span>
                </div>
                <div className="flex items-center gap-2">
                  <User className="h-3.5 w-3.5 text-slate-400 flex-shrink-0" />
                  <span>Encargado: <strong className="text-slate-200">{negocio.contactoNombre}</strong></span>
                </div>
                <div className="flex items-center gap-2">
                  <Phone className="h-3.5 w-3.5 text-slate-400 flex-shrink-0" />
                  <span className="font-mono text-teal-400">{negocio.contactoTelefono || negocio.clienteTelefono}</span>
                </div>
              </div>
            </div>

            <div className="mt-4 pt-3 border-t border-slate-800 flex items-center justify-between gap-2">
              <button
                onClick={() => onNavigateToIncidencias(negocio.clienteId)}
                className="flex items-center gap-1 text-[11px] font-medium text-rose-400 hover:text-rose-300 transition-colors cursor-pointer"
              >
                <AlertCircle className="h-3 w-3" />
                Reportar Avería
              </button>

              <button
                onClick={() => onNavigateToProjects(negocio.clienteId)}
                className="flex items-center gap-1 rounded bg-teal-500/10 hover:bg-teal-500/20 text-teal-400 border border-teal-500/20 px-2.5 py-1 text-[11px] font-medium transition-colors cursor-pointer"
              >
                <FolderPlus className="h-3 w-3" />
                Crear Proyecto
              </button>
            </div>
          </div>
        ))}

        {filtered.length === 0 && (
          <div className="col-span-2 py-12 text-center text-slate-400 rounded-2xl border border-slate-800 bg-slate-900/40">
            <Search className="h-8 w-8 text-slate-500 mx-auto mb-2" />
            <p className="text-sm">No se encontraron negocios con el término ingresado.</p>
            <p className="text-xs text-slate-500 mt-1">Pruebe buscando por nombre comercial o municipio.</p>
          </div>
        )}
      </div>
    </div>
  );
};
