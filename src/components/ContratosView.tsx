import React, { useState } from 'react';
import { FileText, Plus, Search, CheckCircle2, Clock, AlertTriangle, ShieldCheck, X } from 'lucide-react';
import { Contrato, Cliente } from '../types';

interface ContratosViewProps {
  contratos: Contrato[];
  clientes: Cliente[];
  onAddContrato: (c: Omit<Contrato, 'id' | 'codigo'>) => void;
  onUpdateContrato: (c: Contrato) => void;
  onDeleteContrato: (id: string) => void;
}

export const ContratosView: React.FC<ContratosViewProps> = ({
  contratos,
  clientes,
  onAddContrato,
  onUpdateContrato,
  onDeleteContrato,
}) => {
  const [searchTerm, setSearchTerm] = useState('');
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [clienteId, setClienteId] = useState('');
  const [titulo, setTitulo] = useState('');
  const [tipo, setTipo] = useState<Contrato['tipo']>('Mantenimiento CCTV');
  const [montoMensual, setMontoMensual] = useState(12500);
  const [fechaInicio, setFechaInicio] = useState(new Date().toISOString().split('T')[0]);
  const [fechaVencimiento, setFechaVencimiento] = useState(
    new Date(Date.now() + 365 * 24 * 3600 * 1000).toISOString().split('T')[0]
  );
  const [renovacionAutomatica, setRenovacionAutomatica] = useState(true);

  const handleOpenAdd = () => {
    setClienteId(clientes[0]?.id || '');
    setTitulo('Contrato Anual de Monitoreo & Mantenimiento');
    setTipo('Mantenimiento CCTV');
    setMontoMensual(12500);
    setIsModalOpen(true);
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    const cli = clientes.find(c => c.id === clienteId);
    onAddContrato({
      cliente_id: clienteId,
      cliente_nombre: cli ? cli.nombre : 'Cliente',
      titulo,
      tipo,
      monto_mensual: montoMensual,
      fecha_inicio: fechaInicio,
      fecha_vencimiento: fechaVencimiento,
      estado: 'vigente',
      renovacion_automatica: renovacionAutomatica
    });
    setIsModalOpen(false);
  };

  const filteredContratos = contratos.filter(c => 
    c.codigo.toLowerCase().includes(searchTerm.toLowerCase()) ||
    c.cliente_nombre.toLowerCase().includes(searchTerm.toLowerCase()) ||
    c.titulo.toLowerCase().includes(searchTerm.toLowerCase())
  );

  return (
    <div className="space-y-6">
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h2 className="text-xl font-bold text-white tracking-tight flex items-center gap-2">
            <FileText className="h-5 w-5 text-sky-400" />
            Contratos de Servicio & Pólizas
          </h2>
          <p className="text-xs text-slate-400">
            Pólizas de mantenimiento CCTV, contratos de soporte SACI y conectividad SIM (Filament: ContratosPage).
          </p>
        </div>

        <button
          onClick={handleOpenAdd}
          className="flex items-center gap-2 rounded-lg bg-sky-500 hover:bg-sky-400 text-slate-950 font-bold px-4 py-2 text-xs transition-all shadow-md shadow-sky-500/20 cursor-pointer"
        >
          <Plus className="h-4 w-4 stroke-[2.5]" />
          Nuevo Contrato
        </button>
      </div>

      <div className="flex items-center justify-between gap-3 bg-slate-900/70 p-3.5 rounded-xl border border-slate-800">
        <div className="relative w-full sm:w-80">
          <Search className="absolute left-3 top-2.5 h-4 w-4 text-slate-400" />
          <input
            type="text"
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
            placeholder="Buscar contrato o cliente..."
            className="w-full rounded-lg border border-slate-700 bg-slate-950 pl-9 pr-4 py-1.5 text-xs text-slate-200 placeholder-slate-400 focus:border-sky-500 focus:outline-none"
          />
        </div>
      </div>

      <div className="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/50 shadow-sm">
        <table className="w-full text-left text-xs text-slate-300">
          <thead className="bg-slate-950/80 text-[11px] font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-800">
            <tr>
              <th className="py-3.5 px-4">Código / Contrato</th>
              <th className="py-3.5 px-4">Cliente</th>
              <th className="py-3.5 px-4">Tipo de Servicio</th>
              <th className="py-3.5 px-4">Tarifa Mensual</th>
              <th className="py-3.5 px-4">Vigencia</th>
              <th className="py-3.5 px-4">Estado</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-slate-800/80">
            {filteredContratos.map((c) => (
              <tr key={c.id} className="hover:bg-slate-800/40 transition-colors">
                <td className="py-3 px-4">
                  <span className="font-mono font-bold text-white block">{c.codigo}</span>
                  <span className="text-[11px] text-slate-400">{c.titulo}</span>
                </td>
                <td className="py-3 px-4 font-semibold text-white">{c.cliente_nombre}</td>
                <td className="py-3 px-4">
                  <span className="bg-slate-800 border border-slate-700 px-2 py-0.5 rounded text-[11px] text-slate-300">
                    {c.tipo}
                  </span>
                </td>
                <td className="py-3 px-4 font-mono font-bold text-emerald-400">
                  ${c.monto_mensual.toLocaleString()} CUP/mes
                </td>
                <td className="py-3 px-4 text-slate-400 text-[11px]">
                  {c.fecha_inicio} al {c.fecha_vencimiento}
                </td>
                <td className="py-3 px-4">
                  <span className={`inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold ${
                    c.estado === 'vigente' 
                      ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' 
                      : c.estado === 'por_vencer' 
                      ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20'
                      : 'bg-rose-500/10 text-rose-400 border border-rose-500/20'
                  }`}>
                    {c.estado === 'vigente' ? 'Vigente' : c.estado === 'por_vencer' ? 'Por Vencer' : 'Vencido'}
                  </span>
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
              <h3 className="text-sm font-bold text-white">Nuevo Contrato de Servicio</h3>
              <button onClick={() => setIsModalOpen(false)} className="text-slate-400 hover:text-white">
                <X className="h-4 w-4" />
              </button>
            </div>
            <form onSubmit={handleSubmit} className="space-y-4">
              <div>
                <label className="block text-[11px] font-medium text-slate-400 mb-1">Cliente *</label>
                <select
                  value={clienteId}
                  onChange={(e) => setClienteId(e.target.value)}
                  className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-sky-500 focus:outline-none"
                >
                  {clientes.map(c => (
                    <option key={c.id} value={c.id}>{c.nombre}</option>
                  ))}
                </select>
              </div>
              <div>
                <label className="block text-[11px] font-medium text-slate-400 mb-1">Título del Contrato</label>
                <input
                  type="text"
                  required
                  value={titulo}
                  onChange={(e) => setTitulo(e.target.value)}
                  className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-sky-500 focus:outline-none"
                />
              </div>
              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">Tipo</label>
                  <select
                    value={tipo}
                    onChange={(e) => setTipo(e.target.value as any)}
                    className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-sky-500 focus:outline-none"
                  >
                    <option value="Mantenimiento CCTV">Mantenimiento CCTV</option>
                    <option value="Monitoreo SACI">Monitoreo SACI</option>
                    <option value="Gestión Remota & SIM">Gestión Remota & SIM</option>
                    <option value="Soporte Integral">Soporte Integral</option>
                  </select>
                </div>
                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">Monto Mensual (CUP)</label>
                  <input
                    type="number"
                    value={montoMensual}
                    onChange={(e) => setMontoMensual(Number(e.target.value))}
                    className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-sky-500 focus:outline-none"
                  />
                </div>
              </div>
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
                  Registrar Contrato
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
};
