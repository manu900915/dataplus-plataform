import React, { useState } from 'react';
import { 
  Truck, 
  Plus, 
  User, 
  MapPin, 
  Phone, 
  CheckCircle2, 
  Clock, 
  Wrench, 
  AlertTriangle,
  X
} from 'lucide-react';
import { Brigada } from '../types';

interface BrigadasViewProps {
  brigadas: Brigada[];
  onAddBrigada: (brigada: Brigada) => void;
  onUpdateBrigada: (brigada: Brigada) => void;
}

export const BrigadasView: React.FC<BrigadasViewProps> = ({
  brigadas,
  onAddBrigada,
  onUpdateBrigada,
}) => {
  const [showModal, setShowModal] = useState(false);
  const [nombre, setNombre] = useState('');
  const [lider, setLider] = useState('');
  const [tecnicosStr, setTecnicosStr] = useState('');
  const [zona, setZona] = useState('La Habana (Plaza, Playa, Centro)');
  const [telefono, setTelefono] = useState('');

  const handleCreate = (e: React.FormEvent) => {
    e.preventDefault();
    if (!nombre || !lider) return;

    const newBrg: Brigada = {
      id: `brg-${Date.now()}`,
      nombre,
      lider,
      tecnicos: tecnicosStr.split(',').map(s => s.trim()).filter(Boolean),
      zona,
      servicios_activos: 0,
      estado: 'disponible',
      telefono_contacto: telefono
    };

    onAddBrigada(newBrg);
    setShowModal(false);
    setNombre('');
    setLider('');
    setTecnicosStr('');
    setTelefono('');
  };

  const getStatusBadge = (estado: Brigada['estado']) => {
    switch (estado) {
      case 'en_terreno':
        return <span className="rounded bg-sky-500/20 text-sky-300 border border-sky-500/30 px-2 py-0.5 text-[10px] font-bold">En Terreno / Activa</span>;
      case 'mantenimiento':
        return <span className="rounded bg-amber-500/20 text-amber-300 border border-amber-500/30 px-2 py-0.5 text-[10px] font-bold">Mantenimiento de Vehículo</span>;
      default:
        return <span className="rounded bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 px-2 py-0.5 text-[10px] font-bold">Disponible en Base</span>;
    }
  };

  return (
    <div className="space-y-6">
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h2 className="text-xl font-bold text-white tracking-tight flex items-center gap-2">
            <Truck className="h-5 w-5 text-teal-400" />
            Brigadas Técnicas de Campo
          </h2>
          <p className="text-xs text-slate-400">
            Cuadrillas móviles especializadas en tendido de fibra, CCTV, cableado y soporte perimetral.
          </p>
        </div>

        <button
          onClick={() => setShowModal(true)}
          className="flex items-center gap-1.5 rounded-lg bg-teal-500 hover:bg-teal-400 text-slate-950 font-bold px-4 py-2 text-xs transition-all shadow-md shadow-teal-500/20 cursor-pointer"
        >
          <Plus className="h-4 w-4" />
          Registrar Brigada
        </button>
      </div>

      {/* Brigadas Cards */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-5">
        {brigadas.map((brg) => (
          <div
            key={brg.id}
            className="rounded-2xl border border-slate-800 bg-slate-900/70 p-5 shadow-sm hover:border-slate-700 transition-all flex flex-col justify-between"
          >
            <div>
              <div className="flex items-center justify-between pb-3 border-b border-slate-800">
                <div className="flex items-center gap-2">
                  <div className="rounded-lg bg-teal-500/10 p-2 text-teal-400 border border-teal-500/20">
                    <Truck className="h-4 w-4" />
                  </div>
                  <span className="font-mono text-xs font-semibold text-teal-400">
                    {brg.servicios_activos} contratos
                  </span>
                </div>
                {getStatusBadge(brg.estado)}
              </div>

              <h3 className="text-base font-bold text-white mt-3">{brg.nombre}</h3>

              <div className="mt-3 space-y-2 text-xs text-slate-300">
                <div className="flex items-center gap-2">
                  <User className="h-3.5 w-3.5 text-teal-400 flex-shrink-0" />
                  <span>Jefe Técnico: <strong className="text-slate-100">{brg.lider}</strong></span>
                </div>
                <div className="flex items-center gap-2">
                  <Phone className="h-3.5 w-3.5 text-teal-400 flex-shrink-0" />
                  <span className="font-mono text-slate-200">{brg.telefono_contacto}</span>
                </div>
                <div className="flex items-center gap-2">
                  <MapPin className="h-3.5 w-3.5 text-slate-400 flex-shrink-0" />
                  <span>Zona: {brg.zona}</span>
                </div>
              </div>

              {/* Tecnicos Team */}
              <div className="mt-4 pt-3 border-t border-slate-800/80">
                <div className="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-2">
                  Técnicos Integrantes ({brg.tecnicos.length})
                </div>
                <div className="flex flex-wrap gap-1.5">
                  {brg.tecnicos.map((tec, i) => (
                    <span key={i} className="rounded-md bg-slate-800 border border-slate-700 px-2 py-0.5 text-[11px] text-slate-300">
                      {tec}
                    </span>
                  ))}
                </div>
              </div>
            </div>

            <div className="mt-5 pt-3 border-t border-slate-800 flex items-center justify-between">
              <span className="text-[11px] text-slate-400">Estado operativo:</span>
              <div className="flex gap-1">
                {(['disponible', 'en_terreno', 'mantenimiento'] as Brigada['estado'][]).map((st) => (
                  <button
                    key={st}
                    onClick={() => onUpdateBrigada({ ...brg, estado: st })}
                    className={`px-2 py-0.5 rounded text-[10px] font-semibold capitalize border transition-all cursor-pointer ${
                      brg.estado === st
                        ? 'bg-teal-500 text-slate-950 border-teal-400 font-bold'
                        : 'bg-slate-800 text-slate-400 border-slate-700 hover:text-white'
                    }`}
                  >
                    {st.replace('_', ' ')}
                  </button>
                ))}
              </div>
            </div>
          </div>
        ))}
      </div>

      {/* Modal */}
      {showModal && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 p-4 backdrop-blur-sm">
          <div className="w-full max-w-md rounded-2xl border border-slate-800 bg-slate-900 shadow-2xl overflow-hidden">
            <div className="flex items-center justify-between border-b border-slate-800 px-6 py-4 bg-slate-950/60">
              <h3 className="text-base font-bold text-white flex items-center gap-2">
                <Truck className="h-4 w-4 text-teal-400" />
                Registrar Nueva Brigada de Campo
              </h3>
              <button
                onClick={() => setShowModal(false)}
                className="text-slate-400 hover:text-white"
              >
                <X className="h-5 w-5" />
              </button>
            </div>

            <form onSubmit={handleCreate} className="p-6 space-y-4">
              <div>
                <label className="block text-[11px] font-medium text-slate-400 mb-1">
                  Nombre de la Brigada
                </label>
                <input
                  type="text"
                  required
                  placeholder="Ej: Brigada Delta - Fibra y Enlaces"
                  value={nombre}
                  onChange={(e) => setNombre(e.target.value)}
                  className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-teal-500 focus:outline-none"
                />
              </div>

              <div>
                <label className="block text-[11px] font-medium text-slate-400 mb-1">
                  Líder / Responsable Técnico
                </label>
                <input
                  type="text"
                  required
                  placeholder="Ej: Ing. Jorge Valdés"
                  value={lider}
                  onChange={(e) => setLider(e.target.value)}
                  className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-teal-500 focus:outline-none"
                />
              </div>

              <div>
                <label className="block text-[11px] font-medium text-slate-400 mb-1">
                  Técnicos (separados por coma)
                </label>
                <input
                  type="text"
                  placeholder="Ej: Pedro Ramos, Mario Díaz, Raúl Gómez"
                  value={tecnicosStr}
                  onChange={(e) => setTecnicosStr(e.target.value)}
                  className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-teal-500 focus:outline-none"
                />
              </div>

              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">
                    Zona Territorial
                  </label>
                  <input
                    type="text"
                    value={zona}
                    onChange={(e) => setZona(e.target.value)}
                    className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-teal-500 focus:outline-none"
                  />
                </div>
                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">
                    Teléfono Móvil
                  </label>
                  <input
                    type="text"
                    placeholder="+53 5 000 0000"
                    value={telefono}
                    onChange={(e) => setTelefono(e.target.value)}
                    className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs font-mono text-slate-200 focus:border-teal-500 focus:outline-none"
                  />
                </div>
              </div>

              <div className="flex justify-end gap-3 pt-3 border-t border-slate-800">
                <button
                  type="button"
                  onClick={() => setShowModal(false)}
                  className="rounded-lg border border-slate-700 bg-slate-800 px-4 py-2 text-xs font-medium text-slate-300 hover:bg-slate-700 cursor-pointer"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  className="rounded-lg bg-teal-500 hover:bg-teal-400 text-slate-950 px-5 py-2 text-xs font-bold transition-all shadow-md shadow-teal-500/20 cursor-pointer"
                >
                  Registrar Brigada
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
};
