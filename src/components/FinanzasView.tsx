import React, { useState } from 'react';
import { 
  DollarSign, 
  ShoppingCart, 
  TrendingUp, 
  TrendingDown, 
  Plus, 
  Search, 
  CreditCard, 
  ArrowUpRight, 
  ArrowDownLeft,
  X,
  Radio,
  Trash2,
  Image as ImageIcon,
  RotateCcw,
  Percent
} from 'lucide-react';
import {
  Venta,
  VentaItemLine,
  MetodoPagoVenta,
  Gasto,
  Cliente,
  ItemInventario,
  Almacen,
  SuscripcionServicio,
  RegistroPagoServicio,
  Servicio
} from '../types';
import { FacturacionServiciosView } from './FacturacionServiciosView';

interface FinanzasViewProps {
  ventas: Venta[];
  gastos: Gasto[];
  clientes: Cliente[];
  items?: ItemInventario[];
  almacenes?: Almacen[];
  servicios?: Servicio[];
  suscripciones?: SuscripcionServicio[];
  pagosServicio?: RegistroPagoServicio[];
  onAddVenta: (v: Omit<Venta, 'id' | 'codigo'>) => void;
  onAnularVenta?: (ventaId: string) => void;
  onAddGasto: (g: Omit<Gasto, 'id' | 'codigo'>) => void;
  onAddPagoServicio?: (pago: Omit<RegistroPagoServicio, 'id' | 'codigo_pago' | 'created_at'>) => void;
  onAddSuscripcion?: (sub: Omit<SuscripcionServicio, 'id' | 'codigo'>) => void;
  onUpdateSuscripcion?: (sub: SuscripcionServicio) => void;
  initialTab?: 'facturacion' | 'ventas' | 'gastos';
}

interface CartLineDraft {
  item_id: string;
  almacen_id: string;
  cantidad: number;
  precio_costo_unitario: number;
  margen_ganancia: number;
  precio_unitario: number;
}

export const FinanzasView: React.FC<FinanzasViewProps> = ({
  ventas,
  gastos,
  clientes,
  items = [],
  almacenes = [],
  servicios = [],
  suscripciones = [],
  pagosServicio = [],
  onAddVenta,
  onAnularVenta,
  onAddGasto,
  onAddPagoServicio,
  onAddSuscripcion,
  onUpdateSuscripcion,
  initialTab = 'facturacion'
}) => {
  const [activeSubTab, setActiveSubTab] = useState<'facturacion' | 'ventas' | 'gastos'>(initialTab);
  const [filterMetodo, setFilterMetodo] = useState<string>('todos');
  const [searchTerm, setSearchTerm] = useState('');
  const [isVentaModalOpen, setIsVentaModalOpen] = useState(false);
  const [isGastoModalOpen, setIsGastoModalOpen] = useState(false);

  // New Venta form
  const [ventaClienteId, setVentaClienteId] = useState('');
  const [ventaClienteNombre, setVentaClienteNombre] = useState('Consumidor Final');
  const [ventaAlmacenId, setVentaAlmacenId] = useState(almacenes[0]?.id || '');
  const [ventaConcepto, setVentaConcepto] = useState('Venta de Productos');
  const [ventaMetodo, setVentaMetodo] = useState<MetodoPagoVenta>('CUP');
  const [ventaTasaCambio, setVentaTasaCambio] = useState<number>(1);
  const [ventaMargenGlobal, setVentaMargenGlobal] = useState<string>('');
  const [ventaReferencia, setVentaReferencia] = useState('');
  const [ventaDescuento, setVentaDescuento] = useState<number>(0);
  const [ventaLines, setVentaLines] = useState<CartLineDraft[]>([]);
  const [ventaError, setVentaError] = useState<string>('');

  // New Gasto form
  const [gastoCategoria, setGastoCategoria] = useState<Gasto['categoria']>('Insumos y Repuestos');
  const [gastoConcepto, setGastoConcepto] = useState('');
  const [gastoMonto, setGastoMonto] = useState(12000);
  const [gastoResponsable, setGastoResponsable] = useState('Enmanuel Caraballo');

  const ventaMoneda: 'CUP' | 'USD' =
    ventaMetodo === 'USD' || ventaMetodo === 'Tarjeta Clasica' ? 'USD' : 'CUP';

  const ventasActivas = ventas.filter(v => v.estado === 'cobrado' || v.estado === 'completada');
  const totalVentasCup = ventasActivas
    .filter(v => (v.moneda || (v.metodo_pago === 'USD' || v.metodo_pago === 'Tarjeta Clasica' ? 'USD' : 'CUP')) === 'CUP')
    .reduce((acc, v) => acc + v.monto_total, 0);
  const totalVentasUsd = ventasActivas
    .filter(v => (v.moneda || (v.metodo_pago === 'USD' || v.metodo_pago === 'Tarjeta Clasica' ? 'USD' : 'CUP')) === 'USD')
    .reduce((acc, v) => acc + v.monto_total, 0);
  const totalGastos = gastos.reduce((acc, g) => acc + g.monto, 0);
  const balanceNeto = totalVentasCup - totalGastos;

  const filteredVentas = ventas.filter(v => {
    const matchSearch =
      !searchTerm ||
      v.codigo.toLowerCase().includes(searchTerm.toLowerCase()) ||
      v.cliente_nombre.toLowerCase().includes(searchTerm.toLowerCase()) ||
      v.concepto.toLowerCase().includes(searchTerm.toLowerCase());
    const matchMetodo = filterMetodo === 'todos' || v.metodo_pago === filterMetodo;
    return matchSearch && matchMetodo;
  });

  const openNewVentaModal = () => {
    setVentaError('');
    setVentaClienteId('');
    setVentaClienteNombre('Consumidor Final');
    setVentaAlmacenId(almacenes[0]?.id || items[0]?.almacen_id || '');
    setVentaConcepto('Venta de Productos');
    setVentaMetodo('CUP');
    setVentaTasaCambio(1);
    setVentaMargenGlobal('');
    setVentaReferencia('');
    setVentaDescuento(0);

    const firstItem = items.find(i => i.stock_actual > 0) || items[0];
    if (firstItem) {
      const costo = firstItem.precio_costo ?? firstItem.precio_unitario ?? 0;
      const margen = firstItem.margen_ganancia ?? 60;
      const precio = Math.round(costo * (1 + margen / 100) * 100) / 100;
      setVentaLines([
        {
          item_id: firstItem.id,
          almacen_id: firstItem.almacen_id || almacenes[0]?.id || '',
          cantidad: 1,
          precio_costo_unitario: costo,
          margen_ganancia: margen,
          precio_unitario: precio,
        },
      ]);
    } else {
      setVentaLines([]);
    }
    setIsVentaModalOpen(true);
  };

  const handleAddLine = () => {
    const firstItem = items[0];
    if (!firstItem) return;
    const tasa = ventaMoneda === 'CUP' ? Math.max(0.0001, ventaTasaCambio || 1) : 1;
    const costo = Math.round((firstItem.precio_costo ?? firstItem.precio_unitario ?? 0) * tasa * 100) / 100;
    const margen = ventaMargenGlobal !== '' ? parseFloat(ventaMargenGlobal) : (firstItem.margen_ganancia ?? 60);
    const precio = Math.round(costo * (1 + margen / 100) * 100) / 100;

    setVentaLines(prev => [
      ...prev,
      {
        item_id: firstItem.id,
        almacen_id: ventaAlmacenId || firstItem.almacen_id || '',
        cantidad: 1,
        precio_costo_unitario: costo,
        margen_ganancia: margen,
        precio_unitario: precio,
      },
    ]);
  };

  const handleSelectItemInLine = (index: number, itemId: string) => {
    const item = items.find(i => i.id === itemId);
    if (!item) return;
    const tasa = ventaMoneda === 'CUP' ? Math.max(0.0001, ventaTasaCambio || 1) : 1;
    const costo = Math.round((item.precio_costo ?? item.precio_unitario ?? 0) * tasa * 100) / 100;
    const margen = ventaMargenGlobal !== '' ? parseFloat(ventaMargenGlobal) : (item.margen_ganancia ?? 60);
    const precio = Math.round(costo * (1 + margen / 100) * 100) / 100;

    setVentaLines(prev =>
      prev.map((l, idx) =>
        idx === index
          ? {
              ...l,
              item_id: itemId,
              almacen_id: ventaAlmacenId || item.almacen_id || l.almacen_id,
              precio_costo_unitario: costo,
              margen_ganancia: margen,
              precio_unitario: precio,
            }
          : l
      )
    );
  };

  const handleMetodoChange = (nuevoMetodo: MetodoPagoVenta) => {
    setVentaMetodo(nuevoMetodo);
    const nuevaMoneda = nuevoMetodo === 'USD' || nuevoMetodo === 'Tarjeta Clasica' ? 'USD' : 'CUP';
    const nuevaTasa = nuevaMoneda === 'USD' ? 1 : Math.max(0.0001, ventaTasaCambio || 1);
    if (nuevaMoneda === 'USD') setVentaTasaCambio(1);

    setVentaLines(prev =>
      prev.map(l => {
        const item = items.find(i => i.id === l.item_id);
        const costoBase = item ? (item.precio_costo ?? item.precio_unitario ?? 0) : l.precio_costo_unitario;
        const costo = Math.round(costoBase * nuevaTasa * 100) / 100;
        const precio = Math.round(costo * (1 + l.margen_ganancia / 100) * 100) / 100;
        return { ...l, precio_costo_unitario: costo, precio_unitario: precio };
      })
    );
  };

  const handleTasaChange = (val: number) => {
    const tasa = Math.max(0.0001, val || 1);
    setVentaTasaCambio(val);
    setVentaLines(prev =>
      prev.map(l => {
        const item = items.find(i => i.id === l.item_id);
        const costoBase = item ? (item.precio_costo ?? item.precio_unitario ?? 0) : l.precio_costo_unitario;
        const costo = Math.round(costoBase * tasa * 100) / 100;
        const precio = Math.round(costo * (1 + l.margen_ganancia / 100) * 100) / 100;
        return { ...l, precio_costo_unitario: costo, precio_unitario: precio };
      })
    );
  };

  const handleMargenGlobalChange = (val: string) => {
    setVentaMargenGlobal(val);
    if (val === '') return;
    const margen = parseFloat(val);
    if (Number.isNaN(margen)) return;

    setVentaLines(prev =>
      prev.map(l => ({
        ...l,
        margen_ganancia: margen,
        precio_unitario: Math.round(l.precio_costo_unitario * (1 + margen / 100) * 100) / 100,
      }))
    );
  };

  const updateLine = (index: number, patch: Partial<CartLineDraft>, mode: 'margen' | 'precio' | 'other' = 'other') => {
    setVentaLines(prev =>
      prev.map((l, idx) => {
        if (idx !== index) return l;
        const next = { ...l, ...patch };
        if (mode === 'margen') {
          next.precio_unitario = Math.round(next.precio_costo_unitario * (1 + next.margen_ganancia / 100) * 100) / 100;
        } else if (mode === 'precio' && next.precio_costo_unitario > 0) {
          next.margen_ganancia = Math.round(((next.precio_unitario / next.precio_costo_unitario) - 1) * 100 * 100) / 100;
        }
        return next;
      })
    );
  };

  const draftCostoTotal = ventaLines.reduce((acc, l) => acc + l.precio_costo_unitario * l.cantidad, 0);
  const draftSubtotal = ventaLines.reduce((acc, l) => acc + l.precio_unitario * l.cantidad, 0);
  const draftTotal = Math.max(0, draftSubtotal - (ventaDescuento || 0));
  const draftGanancia = draftTotal - draftCostoTotal;

  const handleCreateVenta = (e: React.FormEvent) => {
    e.preventDefault();
    setVentaError('');

    if (ventaLines.length === 0) {
      setVentaError('Agrega al menos un producto del inventario para registrar la venta.');
      return;
    }

    // Validar stock agregado por item
    const qtyByItem = new Map<string, number>();
    for (const l of ventaLines) {
      qtyByItem.set(l.item_id, (qtyByItem.get(l.item_id) || 0) + l.cantidad);
    }

    for (const [itemId, qty] of qtyByItem.entries()) {
      const it = items.find(i => i.id === itemId);
      if (it && qty > it.stock_actual) {
        setVentaError(`Stock insuficiente para "${it.nombre}": disponible ${it.stock_actual} ${it.unidad_medida}.`);
        return;
      }
    }

    const cli = clientes.find(c => c.id === ventaClienteId);
    const almMain = almacenes.find(a => a.id === ventaAlmacenId);

    const formattedLines: VentaItemLine[] = ventaLines.map(l => {
      const it = items.find(i => i.id === l.item_id);
      const alm = almacenes.find(a => a.id === l.almacen_id) || almMain;
      const subtotal = Math.round(l.cantidad * l.precio_unitario * 100) / 100;
      const ganancia = Math.round((l.precio_unitario - l.precio_costo_unitario) * l.cantidad * 100) / 100;
      return {
        item_id: l.item_id,
        item_nombre: it?.nombre || 'Producto',
        item_imagen: it?.imagen,
        almacen_id: l.almacen_id,
        almacen_nombre: alm?.nombre || it?.almacen_nombre || 'Almacén Principal',
        cantidad: l.cantidad,
        precio_costo_unitario: l.precio_costo_unitario,
        margen_ganancia: l.margen_ganancia,
        precio_unitario: l.precio_unitario,
        subtotal,
        ganancia_linea: ganancia,
      };
    });

    const summaryConcept =
      formattedLines.length > 0
        ? formattedLines.map(l => `${l.cantidad}x ${l.item_nombre}`).join(', ')
        : ventaConcepto;

    onAddVenta({
      cliente_id: ventaClienteId,
      cliente_nombre: cli ? cli.nombre : (ventaClienteNombre || 'Consumidor Final'),
      almacen_id: ventaAlmacenId,
      almacen_nombre: almMain?.nombre || 'Almacén Principal',
      concepto: ventaConcepto || summaryConcept,
      fecha: new Date().toISOString().split('T')[0],
      moneda: ventaMoneda,
      tasa_cambio: ventaTasaCambio,
      margen_ganancia_global: ventaMargenGlobal !== '' ? parseFloat(ventaMargenGlobal) : undefined,
      referencia_pago: ventaReferencia || undefined,
      costo_total: Math.round(draftCostoTotal * 100) / 100,
      monto_total: Math.round(draftTotal * 100) / 100,
      ganancia_total: Math.round(draftGanancia * 100) / 100,
      metodo_pago: ventaMetodo,
      items: formattedLines,
      estado: 'completada',
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
            Finanzas, Ventas & Almacén
          </h2>
          <p className="text-xs text-slate-400">
            Ventas en CUP, USD, Transferencia CUP o Tarjeta Clásica con descuento automático de almacén y margen editable.
          </p>
        </div>

        <div className="flex items-center gap-2">
          <button
            onClick={openNewVentaModal}
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
            <span className="text-xs font-semibold text-slate-400">Ventas Completadas</span>
            <div className="rounded-lg bg-emerald-500/10 p-2 text-emerald-400 border border-emerald-500/20">
              <ArrowDownLeft className="h-4 w-4" />
            </div>
          </div>
          <p className="text-xl font-extrabold text-white mt-2">
            ${totalVentasCup.toLocaleString()} CUP
            <span className="text-sm font-bold text-amber-400 ml-2">/ ${totalVentasUsd.toLocaleString()} USD</span>
          </p>
          <p className="text-[11px] text-emerald-400 mt-1 flex items-center gap-1 font-medium">
            <TrendingUp className="h-3 w-3" /> {ventasActivas.length} ventas con salida en almacén
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
            <span className="text-xs font-semibold text-slate-400">Balance Operativo CUP</span>
            <div className="rounded-lg bg-sky-500/10 p-2 text-sky-400 border border-sky-500/20">
              <DollarSign className="h-4 w-4" />
            </div>
          </div>
          <p className={`text-2xl font-extrabold mt-2 ${balanceNeto >= 0 ? 'text-emerald-400' : 'text-rose-400'}`}>
            ${balanceNeto.toLocaleString()} CUP
          </p>
          <p className="text-[11px] text-slate-400 mt-1">Ingresos CUP menos egresos operativos</p>
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
          Ventas de Productos ({ventas.length})
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
          servicios={servicios}
          onAddPago={onAddPagoServicio || (() => {})}
          onAddSuscripcion={onAddSuscripcion || (() => {})}
          onUpdateSuscripcion={onUpdateSuscripcion || (() => {})}
        />
      ) : activeSubTab === 'ventas' ? (
        <div className="space-y-3">
          <div className="flex flex-col sm:flex-row gap-3">
            <div className="relative flex-1">
              <Search className="absolute left-3 top-2.5 h-3.5 w-3.5 text-slate-400" />
              <input
                type="text"
                placeholder="Buscar venta por código, cliente o producto..."
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
                className="w-full rounded-lg border border-slate-800 bg-slate-950 pl-9 pr-3 py-2 text-xs text-slate-200 placeholder-slate-500 focus:border-emerald-500 focus:outline-none"
              />
            </div>
            <select
              value={filterMetodo}
              onChange={(e) => setFilterMetodo(e.target.value)}
              className="rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-emerald-500 focus:outline-none"
            >
              <option value="todos">Todos los métodos de pago</option>
              <option value="CUP">CUP</option>
              <option value="USD">USD</option>
              <option value="Transferencia CUP">Transferencia CUP</option>
              <option value="Tarjeta Clasica">Tarjeta Clásica</option>
            </select>
          </div>

          <div className="overflow-x-auto rounded-2xl border border-slate-800 bg-slate-900/50 shadow-sm">
            <table className="w-full text-left text-xs text-slate-300">
              <thead className="bg-slate-950/80 text-[11px] font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-800">
                <tr>
                  <th className="py-3.5 px-4">Código</th>
                  <th className="py-3.5 px-4">Cliente</th>
                  <th className="py-3.5 px-4">Productos / Almacén</th>
                  <th className="py-3.5 px-4">Método de Pago</th>
                  <th className="py-3.5 px-4 text-right">Total</th>
                  <th className="py-3.5 px-4 text-right">Ganancia</th>
                  <th className="py-3.5 px-4">Estado</th>
                  <th className="py-3.5 px-4 text-center">Acción</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-800/80">
                {filteredVentas.map((v) => {
                  const moneda = v.moneda || (v.metodo_pago === 'USD' || v.metodo_pago === 'Tarjeta Clasica' ? 'USD' : 'CUP');
                  const isCancelled = v.estado === 'anulada' || v.estado === 'anulado';

                  return (
                    <tr key={v.id} className="hover:bg-slate-800/40 transition-colors">
                      <td className="py-3 px-4 font-mono font-bold text-white">
                        {v.codigo}
                        <div className="text-[10px] font-normal text-slate-400">{v.fecha}</div>
                      </td>
                      <td className="py-3 px-4 font-semibold text-white">{v.cliente_nombre}</td>
                      <td className="py-3 px-4 text-slate-300">
                        {v.items && v.items.length > 0 ? (
                          <div className="space-y-1">
                            {v.items.map((l, idx) => (
                              <div key={idx} className="flex items-center gap-2 text-[11px]">
                                {l.item_imagen ? (
                                  <img src={l.item_imagen} alt={l.item_nombre} className="h-5 w-5 rounded object-cover" />
                                ) : (
                                  <ImageIcon className="h-3.5 w-3.5 text-slate-500" />
                                )}
                                <span>{l.cantidad}x {l.item_nombre}</span>
                                <span className="text-[10px] text-slate-400">({l.margen_ganancia}% marg.)</span>
                              </div>
                            ))}
                            <div className="text-[10px] text-teal-400">Almacén: {v.almacen_nombre || 'Principal'}</div>
                          </div>
                        ) : (
                          <span>{v.concepto}</span>
                        )}
                      </td>
                      <td className="py-3 px-4">
                        <span className={`inline-flex px-2 py-0.5 rounded text-[10px] font-bold border ${
                          v.metodo_pago === 'CUP'
                            ? 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30'
                            : v.metodo_pago === 'Transferencia CUP'
                            ? 'bg-sky-500/15 text-sky-300 border-sky-500/30'
                            : v.metodo_pago === 'USD'
                            ? 'bg-amber-500/15 text-amber-300 border-amber-500/30'
                            : 'bg-indigo-500/15 text-indigo-300 border-indigo-500/30'
                        }`}>
                          {v.metodo_pago}
                        </span>
                        {v.referencia_pago && (
                          <div className="text-[10px] text-slate-400 font-mono mt-0.5">Ref: {v.referencia_pago}</div>
                        )}
                      </td>
                      <td className="py-3 px-4 text-right font-mono font-bold text-white">
                        ${v.monto_total.toLocaleString()} {moneda}
                      </td>
                      <td className="py-3 px-4 text-right font-mono font-semibold text-emerald-400">
                        +${(v.ganancia_total ?? 0).toLocaleString()} {moneda}
                      </td>
                      <td className="py-3 px-4">
                        <span className={`inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border ${
                          isCancelled
                            ? 'bg-rose-500/10 text-rose-400 border-rose-500/20'
                            : 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20'
                        }`}>
                          {isCancelled ? 'Anulada' : 'Completada'}
                        </span>
                      </td>
                      <td className="py-3 px-4 text-center">
                        {!isCancelled && onAnularVenta && (
                          <button
                            type="button"
                            onClick={() => onAnularVenta(v.id)}
                            className="inline-flex items-center gap-1 rounded border border-rose-500/30 bg-rose-500/10 hover:bg-rose-500/20 px-2 py-1 text-[10px] font-semibold text-rose-300 cursor-pointer"
                            title="Anular venta y devolver stock al almacén"
                          >
                            <RotateCcw className="h-3 w-3" />
                            Anular
                          </button>
                        )}
                      </td>
                    </tr>
                  );
                })}
                {filteredVentas.length === 0 && (
                  <tr>
                    <td colSpan={8} className="py-8 text-center text-slate-400">
                      No hay ventas registradas aún. Haz clic en "Nueva Venta" para vender productos del almacén.
                    </td>
                  </tr>
                )}
              </tbody>
            </table>
          </div>
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
        <div onClick={() => setIsVentaModalOpen(false)} className="fixed inset-0 z-50 flex items-center justify-center bg-black/75 backdrop-blur-sm p-4">
          <div onClick={(e) => e.stopPropagation()} className="relative w-full max-w-4xl rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-2xl max-h-[90vh] overflow-y-auto">
            <div className="flex items-center justify-between border-b border-slate-800 pb-3 mb-4">
              <h3 className="text-sm font-bold text-white flex items-center gap-2">
                <ShoppingCart className="h-4 w-4 text-emerald-400" />
                Registrar Venta de Productos (Correlacionada con Almacén)
              </h3>
              <button onClick={() => setIsVentaModalOpen(false)} className="text-slate-400 hover:text-white cursor-pointer">
                <X className="h-4 w-4" />
              </button>
            </div>

            {ventaError && (
              <div className="mb-4 rounded-lg border border-rose-500/40 bg-rose-500/10 px-3 py-2 text-xs text-rose-300">
                {ventaError}
              </div>
            )}

            <form onSubmit={handleCreateVenta} className="space-y-4">
              <div className="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">Cliente Registrado</label>
                  <select
                    value={ventaClienteId}
                    onChange={(e) => {
                      const id = e.target.value;
                      setVentaClienteId(id);
                      const cli = clientes.find(c => c.id === id);
                      setVentaClienteNombre(cli ? cli.nombre : 'Consumidor Final');
                    }}
                    className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-emerald-500 focus:outline-none"
                  >
                    <option value="">Venta Directa / Consumidor Final</option>
                    {clientes.map(c => (
                      <option key={c.id} value={c.id}>{c.nombre}</option>
                    ))}
                  </select>
                </div>

                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">Nombre en Comprobante</label>
                  <input
                    type="text"
                    required
                    value={ventaClienteNombre}
                    onChange={(e) => setVentaClienteNombre(e.target.value)}
                    className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-emerald-500 focus:outline-none"
                  />
                </div>

                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">Almacén de Salida</label>
                  <select
                    value={ventaAlmacenId}
                    onChange={(e) => setVentaAlmacenId(e.target.value)}
                    className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-emerald-500 focus:outline-none"
                  >
                    {almacenes.map(a => (
                      <option key={a.id} value={a.id}>{a.nombre}</option>
                    ))}
                  </select>
                </div>
              </div>

              <div className="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">Método de Pago *</label>
                  <select
                    value={ventaMetodo}
                    onChange={(e) => handleMetodoChange(e.target.value as MetodoPagoVenta)}
                    className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-emerald-500 focus:outline-none"
                  >
                    <option value="CUP">CUP</option>
                    <option value="USD">USD</option>
                    <option value="Transferencia CUP">Transferencia CUP</option>
                    <option value="Tarjeta Clasica">Tarjeta Clásica</option>
                  </select>
                </div>

                {ventaMoneda === 'CUP' && (
                  <div>
                    <label className="block text-[11px] font-medium text-slate-400 mb-1">Tasa Conversión (si costo es USD)</label>
                    <input
                      type="number"
                      min="0.01"
                      step="0.01"
                      value={ventaTasaCambio}
                      onChange={(e) => handleTasaChange(parseFloat(e.target.value) || 1)}
                      className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs font-mono text-slate-200 focus:border-emerald-500 focus:outline-none"
                    />
                  </div>
                )}

                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1 flex items-center gap-1">
                    <Percent className="h-3 w-3 text-emerald-400" />
                    Margen Global (%)
                  </label>
                  <input
                    type="number"
                    min="0"
                    max="1000"
                    step="0.1"
                    placeholder="Por producto"
                    value={ventaMargenGlobal}
                    onChange={(e) => handleMargenGlobalChange(e.target.value)}
                    className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs font-mono text-emerald-400 focus:border-emerald-500 focus:outline-none"
                  />
                </div>

                {(ventaMetodo === 'Transferencia CUP' || ventaMetodo === 'Tarjeta Clasica') && (
                  <div>
                    <label className="block text-[11px] font-medium text-slate-400 mb-1">N° Operación / Voucher</label>
                    <input
                      type="text"
                      placeholder="Ej: TRF-928312"
                      value={ventaReferencia}
                      onChange={(e) => setVentaReferencia(e.target.value)}
                      className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs font-mono text-slate-200 focus:border-emerald-500 focus:outline-none"
                    />
                  </div>
                )}
              </div>

              {/* Products Cart */}
              <div className="rounded-xl border border-slate-800 bg-slate-950/60 p-4 space-y-3">
                <div className="flex items-center justify-between">
                  <span className="text-xs font-bold text-white">Productos de la Venta (se descuentan del almacén)</span>
                  <button
                    type="button"
                    onClick={handleAddLine}
                    disabled={items.length === 0}
                    className="inline-flex items-center gap-1 rounded-lg bg-slate-800 hover:bg-slate-700 border border-slate-700 px-2.5 py-1 text-[11px] font-semibold text-teal-300 cursor-pointer disabled:opacity-50"
                  >
                    <Plus className="h-3.5 w-3.5" />
                    Agregar Producto
                  </button>
                </div>

                {ventaLines.map((line, index) => {
                  const selectedItem = items.find(i => i.id === line.item_id);
                  const subtotal = Math.round(line.cantidad * line.precio_unitario * 100) / 100;
                  const ganancia = Math.round((line.precio_unitario - line.precio_costo_unitario) * line.cantidad * 100) / 100;

                  return (
                    <div key={index} className="grid grid-cols-12 gap-2 items-end rounded-lg border border-slate-800 bg-slate-900/70 p-3">
                      <div className="col-span-1 flex items-center justify-center">
                        {selectedItem?.imagen ? (
                          <img src={selectedItem.imagen} alt={selectedItem.nombre} className="h-10 w-10 rounded object-cover border border-slate-700" />
                        ) : (
                          <div className="h-10 w-10 rounded border border-dashed border-slate-700 flex items-center justify-center text-slate-600">
                            <ImageIcon className="h-4 w-4" />
                          </div>
                        )}
                      </div>

                      <div className="col-span-11 sm:col-span-4">
                        <label className="block text-[10px] text-slate-400 mb-1">
                          Producto {selectedItem ? `(Stock: ${selectedItem.stock_actual} ${selectedItem.unidad_medida})` : ''}
                        </label>
                        <select
                          value={line.item_id}
                          onChange={(e) => handleSelectItemInLine(index, e.target.value)}
                          className="w-full rounded border border-slate-800 bg-slate-950 px-2 py-1.5 text-xs text-slate-200 focus:border-emerald-500 focus:outline-none"
                        >
                          {items.map(i => (
                            <option key={i.id} value={i.id}>
                              [{i.codigo}] {i.nombre} (Disp: {i.stock_actual})
                            </option>
                          ))}
                        </select>
                      </div>

                      <div className="col-span-3 sm:col-span-1">
                        <label className="block text-[10px] text-slate-400 mb-1">Cant.</label>
                        <input
                          type="number"
                          min="0.01"
                          step="1"
                          value={line.cantidad}
                          onChange={(e) => updateLine(index, { cantidad: Math.max(0.01, parseFloat(e.target.value) || 0) })}
                          className="w-full rounded border border-slate-800 bg-slate-950 px-2 py-1.5 text-xs font-mono text-white"
                        />
                      </div>

                      <div className="col-span-3 sm:col-span-2">
                        <label className="block text-[10px] text-slate-400 mb-1">Costo Unit.</label>
                        <input
                          type="number"
                          min="0"
                          step="0.01"
                          value={line.precio_costo_unitario}
                          onChange={(e) => updateLine(index, { precio_costo_unitario: parseFloat(e.target.value) || 0 }, 'margen')}
                          className="w-full rounded border border-slate-800 bg-slate-950 px-2 py-1.5 text-xs font-mono text-slate-300"
                        />
                      </div>

                      <div className="col-span-3 sm:col-span-1">
                        <label className="block text-[10px] text-emerald-400 mb-1">Margen %</label>
                        <input
                          type="number"
                          min="-99"
                          max="1000"
                          step="0.1"
                          value={line.margen_ganancia}
                          onChange={(e) => updateLine(index, { margen_ganancia: parseFloat(e.target.value) || 0 }, 'margen')}
                          className="w-full rounded border border-emerald-500/40 bg-slate-950 px-2 py-1.5 text-xs font-mono text-emerald-400"
                        />
                      </div>

                      <div className="col-span-3 sm:col-span-2">
                        <label className="block text-[10px] text-slate-400 mb-1">P. Venta ({ventaMoneda})</label>
                        <input
                          type="number"
                          min="0"
                          step="0.01"
                          value={line.precio_unitario}
                          onChange={(e) => updateLine(index, { precio_unitario: parseFloat(e.target.value) || 0 }, 'precio')}
                          className="w-full rounded border border-slate-800 bg-slate-950 px-2 py-1.5 text-xs font-mono text-white"
                        />
                      </div>

                      <div className="col-span-12 sm:col-span-1 flex items-center justify-between sm:justify-end gap-2">
                        <div className="text-right">
                          <div className="text-[11px] font-mono font-bold text-white">${subtotal.toFixed(2)}</div>
                          <div className="text-[10px] font-mono text-emerald-400">+${ganancia.toFixed(2)}</div>
                        </div>
                        <button
                          type="button"
                          onClick={() => setVentaLines(prev => prev.filter((_, idx) => idx !== index))}
                          className="text-rose-400 hover:text-rose-300 p-1 cursor-pointer"
                        >
                          <Trash2 className="h-3.5 w-3.5" />
                        </button>
                      </div>
                    </div>
                  );
                })}
              </div>

              <div className="grid grid-cols-1 sm:grid-cols-3 gap-3 rounded-xl border border-slate-800 bg-slate-950/60 p-3 text-xs">
                <div>
                  <label className="block text-[10px] text-slate-400 mb-1">Descuento ({ventaMoneda})</label>
                  <input
                    type="number"
                    min="0"
                    step="0.01"
                    value={ventaDescuento}
                    onChange={(e) => setVentaDescuento(parseFloat(e.target.value) || 0)}
                    className="w-full rounded border border-slate-800 bg-slate-900 px-2.5 py-1.5 text-xs font-mono text-white"
                  />
                </div>
                <div className="flex flex-col justify-center">
                  <span className="text-[10px] text-slate-400">Costo / Ganancia Estimada</span>
                  <span className="font-mono font-bold text-emerald-400">
                    Costo: ${draftCostoTotal.toFixed(2)} | Ganancia: +${draftGanancia.toFixed(2)} {ventaMoneda}
                  </span>
                </div>
                <div className="flex flex-col justify-center sm:text-right">
                  <span className="text-[10px] text-slate-400">Total a Cobrar ({ventaMetodo})</span>
                  <span className="text-lg font-extrabold font-mono text-white">
                    ${draftTotal.toFixed(2)} {ventaMoneda}
                  </span>
                </div>
              </div>

              <div className="flex justify-end gap-2 pt-2 border-t border-slate-800">
                <button
                  type="button"
                  onClick={() => setIsVentaModalOpen(false)}
                  className="rounded-lg border border-slate-700 bg-slate-800 px-3 py-1.5 text-xs text-slate-300 cursor-pointer"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  className="rounded-lg bg-emerald-500 hover:bg-emerald-400 px-4 py-1.5 text-xs font-bold text-slate-950 cursor-pointer"
                >
                  Confirmar Venta y Descontar Almacén
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      {/* Modal Nuevo Gasto */}
      {isGastoModalOpen && (
        <div onClick={() => setIsGastoModalOpen(false)} className="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4">
          <div onClick={(e) => e.stopPropagation()} className="relative w-full max-w-md rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-2xl">
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
