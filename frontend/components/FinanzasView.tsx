import React, { useState } from 'react';
import { BanknotesIcon } from '@heroicon/react/24/outline';
import { 
  DollarSign, 
  ShoppingCart, 
  TrendingUp, 
  TrendingDown, 
  Plus, 
  Search, 
  CreditCard, 
  Calendar, 
  CheckCircle2, 
  Clock, 
  ArrowUpRight, 
  ArrowDownLeft,
  X,
  Radio
} from 'lucide-react';
import { Venta, Gasto, Cliente, SuscripcionServicio, RegistroPagoServicio } from '../types';
import { FacturacionServiciosView } from './FacturacionServiciosView';

interface FinanzasViewProps {
  ventas: Venta[];
  gastos: Gasto[];
  clientes: Cliente[];
  suscripciones?: SuscripcionServicio[];
  pagosServicio?: RegistroPagoServicio[];
  onAddVenta: (v: Omit<Venta, 'id' | 'codigo'>) => void;
  onAddGasto: (g: Omit<Gasto, 'id' | 'codigo'>) => void;
  onAddPagoServicio?: (pago: Omit<RegistroPagoServicio, 'id' | 'codigo_pago' | 'created_at'>) => void;
  onAddSuscripcion?: (sub: Omit<SuscripcionServicio, 'id' | 'codigo'>) => void;
  onUpdateSuscripcion?: (sub: SuscripcionServicio) => void;
  initialTab?: 'facturacion' | 'ventas' | 'gastos';
}

export const FinanzasView: React.FC<FinanzasViewProps> = ({
  ventas,
  gastos,
  clientes,
  suscripciones = [],
  pagosServicio = [],
  onAddVenta,
  onAddGasto,
  onAddPagoServicio,
  onAddSuscripcion,
  onUpdateSuscripcion,
  initialTab = 'facturacion'
}) => {
  const [activeSubTab, setActiveSubTab] = useState<'facturacion' | 'ventas' | 'gastos'>(initialTab);
  const [searchTerm, setSearchTerm] = useState('');
  const [isVentaModalOpen, setIsVentaModalOpen] = useState(false);
  const [isGastoModalOpen, setIsGastoModalOpen] = useState(false);

  // New Venta form
  const [ventaClienteId, setVentaClienteId] = useState('');
  const [ventaConcepto, setVentaConcepto] = useState('');
  const [ventaMonto, setVentaMonto] = useState(45000);
  const [ventaMetodo, setVentaMetodo] = useState<Venta['metodo_pago']>('Transferencia CUP');

  // New Gasto form
  const [gastoCategoria, setGastoCategoria] = useState<Gasto['categoria']>('Insumos y Repuestos');
  const [gastoConcepto, setGastoConcepto] = useState('');
  const [gastoMonto, setGastoMonto] = useState(12000);
  const [gastoResponsable, setGastoResponsable] = useState('Enmanuel Caraballo');

  const totalVentas = ventas.reduce((acc, v) => acc + (v.estado === 'cobrado' ? v.monto_total : 0), 0);
  const totalGastos = gastos.reduce((acc, g) => acc + g.monto, 0);
  const balanceNeto = totalVentas - totalGastos;

  const handleCreateVenta = (e: React.FormEvent) => {
    e.preventDefault();
    const cli = clientes.find(c => c.id === ventaClienteId);
    onAddVenta({
      cliente_id: ventaClienteId,
      cliente_nombre: cli ? cli.nombre : 'Cliente',
      concepto: ventaConcepto,
      fecha: new Date().toISOString().split('T')[0],
      monto_total: ventaMonto,
      metodo_pago: ventaMetodo,
      estado: 'cobrado'
    });
    setIsVentaModalOpen(false);
  };

  const handleCreateGasto = (e: React.FormEvent) => {
    e.preventDefault();
    onAddGasto({
      categoria: gastoCategoria,
      concepto: gastoConcepto,
      fecha: new Date().toISOString().split('T')[0],
      monto: gastoMonto,
      responsable: gastoResponsable
    });
    setIsGastoModalOpen(false);
  };

  return (
    <div className="space-y-6">
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h2 className="text-xl font-bold text-white tracking-tight flex items-center gap-2">
            <DollarSign className="h-5 w-5 text-emerald-400" />
            Finanzas & Facturación (Ventas y Gastos)
          </h2>
          <p className="text-xs text-slate-400">
            Control contable de ingresos por obras, pólizas mensuales y costos operativos (Filament: VentasPage / GastosPage).
          </p>
        </div>

        <div className="flex items-center gap-2">
          <button
            onClick={() => {
              setVentaClienteId(clientes[0]?.id || '');
              setVentaConcepto('Facturación Instalación Sistema CCTV');
              setIsVentaModalOpen(true);
            }}
            className="flex items-center gap-1.5 rounded-lg bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold px-3 py-2 text-xs transition-all shadow-md shadow-emerald-500/20 cursor-pointer"
          >
            <Plus className="h-3.5 w-3.5 stroke-[2.5]" />
            Nueva Venta
          </button>
          <button
            onClick={() => {
              setGastoConcepto('Compra de conectores BNC y bobina Cat6');
              setIsGastoModalOpen(true);
            }}
            className="flex items-center gap-1.5 rounded-lg bg-rose-500 hover:bg-rose-400 text-slate-950 font-bold px-3 py-2 text-xs transition-all shadow-md shadow-rose-500/20 cursor-pointer"
          >
            <Plus className="h-3.5 w-3.5 stroke-[2.5]" />
            Registrar Gasto
          </button>
        </div>
      </div>

      {/* Summary KPI Cards */}
      <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div className="rounded-2xl border border-slate-800 bg-slate-900/60 p-5">
          <div className="flex items-center justify-between">
            <span className="text-xs font-semibold text-slate-400">Ingresos Cobrados</span>
            <div className="rounded-lg bg-emerald-500/10 p-2 text-emerald-400 border border-emerald-500/20">
              <ArrowDownLeft className="h-4 w-4" />
            </div>
          </div>
          <p className="text-2xl font-extrabold text-white mt-2">${totalVentas.toLocaleString()} CUP</p>
          <p className="text-[11px] text-emerald-400 mt-1 flex items-center gap-1 font-medium">
            <TrendingUp className="h-3 w-3" /> {ventas.filter(v => v.estado === 'cobrado').length} cobros efectuados
          </p>
        </div>

        <div className="rounded-2xl border border-slate-800 bg-slate-900/60 p-5">
          <div className="flex items-center justify-between">
            <span className="text-xs font-semibold text-slate-400">Gastos Operativos</span>
            <div className="rounded-lg bg-rose-500/10 p-2 text-rose-400 border border-rose-500/20">
              <ArrowUpRight className="h-4 w-4" />
            </div>
          </div>
          <p className="text-2xl font-extrabold text-white mt-2">${totalGastos.toLocaleString()} CUP</p>
          <p className="text-[11px] text-rose-400 mt-1 flex items-center gap-1 font-medium">
            <TrendingDown className="h-3 w-3" /> {gastos.length} registros de egresos
          </p>
        </div>

        <div className="rounded-2xl border border-slate-800 bg-slate-900/60 p-5">
          <div className="flex items-center justify-between">
            <span className="text-xs font-semibold text-slate-400">Balance Operativo Neto</span>
            <div className="rounded-lg bg-sky-500/10 p-2 text-sky-400 border border-sky-500/20">
              <DollarSign className="h-4 w-4" />
            </div>
          </div>
          <p className={`text-2xl font-extrabold mt-2 ${balanceNeto >= 0 ? 'text-emerald-400' : 'text-rose-400'}`}>
            ${balanceNeto.toLocaleString()} CUP
          </p>
          <p className="text-[11px] text-slate-400 mt-1">Margen positivo en obras de campo</p>
        </div>
      </div>

      {/* Tabs Facturación vs Ventas vs Gastos */}
      <div className="border-b border-slate-800 flex gap-4 overflow-x-auto">
        <button
          onClick={() => setActiveSubTab('facturacion')}
          className={`pb-3 text-xs font-bold transition-colors cursor-pointer border-b-2 flex items-center gap-2 whitespace-nowrap ${
            activeSubTab === 'facturacion'
              ? 'border-sky-500 text-sky-400'
              : 'border-transparent text-slate-400 hover:text-slate-200'
          }`}
        >
          <Radio className="h-4 w-4" />
          Facturación de Servicios ({suscripciones.length})
          {suscripciones.some(s => s.tiene_deuda) && (
            <span className="h-2 w-2 rounded-full bg-rose-500"></span>
          )}
        </button>
        <button
          onClick={() => setActiveSubTab('ventas')}
          className={`pb-3 text-xs font-bold transition-colors cursor-pointer border-b-2 flex items-center gap-2 whitespace-nowrap ${
            activeSubTab === 'ventas'
              ? 'border-emerald-500 text-emerald-400'
              : 'border-transparent text-slate-400 hover:text-slate-200'
          }`}
        >
          <ShoppingCart className="h-4 w-4" />
          Ventas y Obras ({ventas.length})
        </button>
        <button
          onClick={() => setActiveSubTab('gastos')}
          className={`pb-3 text-xs font-bold transition-colors cursor-pointer border-b-2 flex items-center gap-2 whitespace-nowrap ${
            activeSubTab === 'gastos'
              ? 'border-rose-500 text-rose-400'
              : 'border-transparent text-slate-400 hover:text-slate-200'
          }`}
        >
          <CreditCard className="h-4 w-4" />
          Gastos Operativos ({gastos.length})
        </button>
      </div>

      {/* Subtab Content */}
      {activeSubTab === 'facturacion' ? (
        <FacturacionServiciosView
          suscripciones={suscripciones}
          pagos={pagosServicio}
          clientes={clientes}
          onAddPago={onAddPagoServicio || (() => {})}
          onAddSuscripcion={onAddSuscripcion || (() => {})}
          onUpdateSuscripcion={onUpdateSuscripcion || (() => {})}
        />
      ) : activeSubTab === 'ventas' ? (
        <div className="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/50 shadow-sm">
          <table className="w-full text-left text-xs text-slate-300">
            <thead className="bg-slate-950/80 text-[11px] font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-800">
              <tr>
                <th className="py-3.5 px-4">Factura / Código</th>
                <th className="py-3.5 px-4">Cliente</th>
                <th className="py-3.5 px-4">Concepto</th>
                <th className="py-3.5 px-4">Monto</th>
                <th className="py-3.5 px-4">Método</th>
                <th className="py-3.5 px-4">Fecha</th>
                <th className="py-3.5 px-4">Estado</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-800/80">
              {ventas.map((v) => (
                <tr key={v.id} className="hover:bg-slate-800/40 transition-colors">
                  <td className="py-3 px-4 font-mono font-bold text-white">{v.codigo}</td>
                  <td className="py-3 px-4 font-semibold text-white">{v.cliente_nombre}</td>
                  <td className="py-3 px-4 text-slate-300">{v.concepto}</td>
                  <td className="py-3 px-4 font-mono font-bold text-emerald-400">
                    ${v.monto_total.toLocaleString()} CUP
                  </td>
                  <td className="py-3 px-4 text-slate-400">{v.metodo_pago}</td>
                  <td className="py-3 px-4 text-slate-400 text-[11px]">{v.fecha}</td>
                  <td className="py-3 px-4">
                    <span className="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                      Cobrado
                    </span>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      ) : (
        <div className="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/50 shadow-sm">
          <table className="w-full text-left text-xs text-slate-300">
            <thead className="bg-slate-950/80 text-[11px] font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-800">
              <tr>
                <th className="py-3.5 px-4">Código</th>
                <th className="py-3.5 px-4">Categoría</th>
                <th className="py-3.5 px-4">Concepto</th>
                <th className="py-3.5 px-4">Monto</th>
                <th className="py-3.5 px-4">Responsable</th>
                <th className="py-3.5 px-4">Fecha</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-800/80">
              {gastos.map((g) => (
                <tr key={g.id} className="hover:bg-slate-800/40 transition-colors">
                  <td className="py-3 px-4 font-mono font-bold text-white">{g.codigo}</td>
                  <td className="py-3 px-4">
                    <span className="bg-slate-800 border border-slate-700 px-2 py-0.5 rounded text-[11px] text-slate-300">
                      {g.categoria}
                    </span>
                  </td>
                  <td className="py-3 px-4 text-slate-300">{g.concepto}</td>
                  <td className="py-3 px-4 font-mono font-bold text-rose-400">
                    -${g.monto.toLocaleString()} CUP
                  </td>
                  <td className="py-3 px-4 text-slate-300">{g.responsable}</td>
                  <td className="py-3 px-4 text-slate-400 text-[11px]">{g.fecha}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}

      {/* Modal Nueva Venta */}
      {isVentaModalOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4">
          <div className="relative w-full max-w-md rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-2xl">
            <div className="flex items-center justify-between border-b border-slate-800 pb-3 mb-4">
              <h3 className="text-sm font-bold text-white">Registrar Factura / Venta</h3>
              <button onClick={() => setIsVentaModalOpen(false)} className="text-slate-400 hover:text-white">
                <X className="h-4 w-4" />
              </button>
            </div>
            <form onSubmit={handleCreateVenta} className="space-y-4">
              <div>
                <label className="block text-[11px] font-medium text-slate-400 mb-1">Cliente *</label>
                <select
                  value={ventaClienteId}
                  onChange={(e) => setVentaClienteId(e.target.value)}
                  className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-emerald-500 focus:outline-none"
                >
                  {clientes.map(c => (
                    <option key={c.id} value={c.id}>{c.nombre}</option>
                  ))}
                </select>
              </div>
              <div>
                <label className="block text-[11px] font-medium text-slate-400 mb-1">Concepto</label>
                <input
                  type="text"
                  required
                  value={ventaConcepto}
                  onChange={(e) => setVentaConcepto(e.target.value)}
                  className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-emerald-500 focus:outline-none"
                />
              </div>
              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">Monto (CUP)</label>
                  <input
                    type="number"
                    required
                    value={ventaMonto}
                    onChange={(e) => setVentaMonto(Number(e.target.value))}
                    className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-emerald-500 focus:outline-none"
                  />
                </div>
                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">Método de Pago</label>
                  <select
                    value={ventaMetodo}
                    onChange={(e) => setVentaMetodo(e.target.value as any)}
                    className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-emerald-500 focus:outline-none"
                  >
                    <option value="Transferencia CUP">Transferencia CUP</option>
                    <option value="Efectivo">Efectivo</option>
                    <option value="USD / MLC">USD / MLC</option>
                  </select>
                </div>
              </div>
              <div className="flex justify-end gap-2 pt-2 border-t border-slate-800">
                <button
                  type="button"
                  onClick={() => setIsVentaModalOpen(false)}
                  className="rounded-lg border border-slate-700 bg-slate-800 px-3 py-1.5 text-xs text-slate-300"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  className="rounded-lg bg-emerald-500 hover:bg-emerald-400 px-4 py-1.5 text-xs font-bold text-slate-950"
                >
                  Guardar Venta
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      {/* Modal Nuevo Gasto */}
      {isGastoModalOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4">
          <div className="relative w-full max-w-md rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-2xl">
            <div className="flex items-center justify-between border-b border-slate-800 pb-3 mb-4">
              <h3 className="text-sm font-bold text-white">Registrar Gasto Operativo</h3>
              <button onClick={() => setIsGastoModalOpen(false)} className="text-slate-400 hover:text-white">
                <X className="h-4 w-4" />
              </button>
            </div>
            <form onSubmit={handleCreateGasto} className="space-y-4">
              <div>
                <label className="block text-[11px] font-medium text-slate-400 mb-1">Categoría</label>
                <select
                  value={gastoCategoria}
                  onChange={(e) => setGastoCategoria(e.target.value as any)}
                  className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-rose-500 focus:outline-none"
                >
                  <option value="Insumos y Repuestos">Insumos y Repuestos</option>
                  <option value="Combustible y Transporte">Combustible y Transporte</option>
                  <option value="Dietas y Brigadas">Dietas y Brigadas</option>
                  <option value="Equipos y Herramientas">Equipos y Herramientas</option>
                  <option value="Servicios y Telecom">Servicios y Telecom</option>
                </select>
              </div>
              <div>
                <label className="block text-[11px] font-medium text-slate-400 mb-1">Concepto</label>
                <input
                  type="text"
                  required
                  value={gastoConcepto}
                  onChange={(e) => setGastoConcepto(e.target.value)}
                  className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-rose-500 focus:outline-none"
                />
              </div>
              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">Monto (CUP)</label>
                  <input
                    type="number"
                    required
                    value={gastoMonto}
                    onChange={(e) => setGastoMonto(Number(e.target.value))}
                    className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-rose-500 focus:outline-none"
                  />
                </div>
                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">Responsable</label>
                  <input
                    type="text"
                    required
                    value={gastoResponsable}
                    onChange={(e) => setGastoResponsable(e.target.value)}
                    className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-rose-500 focus:outline-none"
                  />
                </div>
              </div>
              <div className="flex justify-end gap-2 pt-2 border-t border-slate-800">
                <button
                  type="button"
                  onClick={() => setIsGastoModalOpen(false)}
                  className="rounded-lg border border-slate-700 bg-slate-800 px-3 py-1.5 text-xs text-slate-300"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  className="rounded-lg bg-rose-500 hover:bg-rose-400 px-4 py-1.5 text-xs font-bold text-slate-950"
                >
                  Guardar Gasto
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
};
