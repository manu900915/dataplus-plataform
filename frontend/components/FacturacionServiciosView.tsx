import React, { useState, useMemo, useRef, useEffect } from 'react';
import { 
  Radio,
  Calendar, 
  CreditCard, 
  Receipt, 
  Phone, 
  MessageSquare, 
  Plus, 
  Upload, 
  Image as ImageIcon, 
  Download, 
  Copy, 
  TrendingUp, 
  X, 
  Check, 
  AlertCircle,
  Clock,
  CheckCircle2,
  AlertTriangle,
  Search,
  Filter,
  DollarSign,
  ArrowUpRight,
  ExternalLink
} from 'lucide-react';
import { 
  SuscripcionServicio, 
  RegistroPagoServicio, 
  Cliente, 
  Moneda, 
  MetodoPagoServicio 
} from '../types';
import { createSampleVoucher } from '../data/mockData';

interface FacturacionServiciosViewProps {
  suscripciones: SuscripcionServicio[];
  pagos: RegistroPagoServicio[];
  clientes: Cliente[];
  onAddPago: (pago: Omit<RegistroPagoServicio, 'id' | 'codigo_pago' | 'created_at'>) => void;
  onAddSuscripcion: (sub: Omit<SuscripcionServicio, 'id' | 'codigo'>) => void;
  onUpdateSuscripcion: (sub: SuscripcionServicio) => void;
}

export const FacturacionServiciosView: React.FC<FacturacionServiciosViewProps> = ({
  suscripciones,
  pagos,
  clientes,
  onAddPago,
  onAddSuscripcion,
  onUpdateSuscripcion
}) => {
  // Pestañas principales de control
  const [activeTab, setActiveTab] = useState<'cobros' | 'semana' | 'deudores' | 'reporte'>('cobros');
  
  // Filtros de búsqueda
  const [searchTerm, setSearchTerm] = useState('');
  const [filtroMoneda, setFiltroMoneda] = useState<'Todas' | 'CUP' | 'USD'>('Todas');
  const [filtroEstado, setFiltroEstado] = useState<'Todos' | 'Cobrados' | 'Pendientes' | 'En Mora'>('Todos');

  // Modales
  const [isPagoModalOpen, setIsPagoModalOpen] = useState(false);
  const [isSubModalOpen, setIsSubModalOpen] = useState(false);
  const [previewVoucherUrl, setPreviewVoucherUrl] = useState<{ url: string; cliente: string; codigo: string; op?: string } | null>(null);
  const [reciboModalData, setReciboModalData] = useState<RegistroPagoServicio | null>(null);
  const [copiedReceipt, setCopiedReceipt] = useState(false);

  // Formulario de Cobro
  const [pagoSubId, setPagoSubId] = useState<string>('');
  const [pagoMonto, setPagoMonto] = useState<number>(3500);
  const [pagoMoneda, setPagoMoneda] = useState<Moneda>('CUP');
  const [pagoMetodo, setPagoMetodo] = useState<MetodoPagoServicio>('Transferencia');
  const [pagoFecha, setPagoFecha] = useState<string>('2026-10-11');
  const [pagoPeriodo, setPagoPeriodo] = useState<string>('Octubre 2026');
  const [pagoPlataforma, setPagoPlataforma] = useState<string>('Transfermóvil');
  const [pagoNumTransaccion, setPagoNumTransaccion] = useState<string>('');
  const [pagoComprobanteUrl, setPagoComprobanteUrl] = useState<string>('');
  const [pagoComprobanteNombre, setPagoComprobanteNombre] = useState<string>('');
  const [pagoEfectivoRecibe, setPagoEfectivoRecibe] = useState<string>('Enmanuel Caraballo (DataPlus)');
  const [pagoEfectivoEntrega, setPagoEfectivoEntrega] = useState<string>('');
  const [pagoNotas, setPagoNotas] = useState<string>('');

  // Formulario de Nueva Suscripción
  const [subClienteId, setSubClienteId] = useState<string>('');
  const [subNombreServicio, setSubNombreServicio] = useState<string>('Gestión Remota Router 4G & Monitoreo');
  const [subTipoSolucion, setSubTipoSolucion] = useState<string>('Router 4G Huawei B310');
  const [subSimNumero, setSubSimNumero] = useState<string>('5352849102');
  const [subMoneda, setSubMoneda] = useState<Moneda>('CUP');
  const [subTarifa, setSubTarifa] = useState<number>(3500);
  const [subNotas, setSubNotas] = useState<string>('');

  // Filtro temporal para el reporte gráfico
  const [periodoReporte, setPeriodoReporte] = useState<'mensual' | 'trimestral' | 'semestral' | 'anual'>('mensual');
  const fileInputRef = useRef<HTMLInputElement>(null);

  // Parámetros de la norma operativa
  const diaActual = 11;
  const diasLimite = 15;
  const diasRestantes = Math.max(0, diasLimite - diaActual);

  // Cerrar modales con tecla Escape
  useEffect(() => {
    const handleKeyDown = (e: KeyboardEvent) => {
      if (e.key === 'Escape') {
        setIsPagoModalOpen(false);
        setIsSubModalOpen(false);
        setPreviewVoucherUrl(null);
        setReciboModalData(null);
      }
    };
    window.addEventListener('keydown', handleKeyDown);
    return () => window.removeEventListener('keydown', handleKeyDown);
  }, []);

  // Suscripción seleccionada actualmente para cobro
  const selectedSub = useMemo(() => {
    return suscripciones.find(s => s.id === pagoSubId) || suscripciones[0];
  }, [pagoSubId, suscripciones]);

  // Abrir modal de cobro para una suscripción
  const handleSelectSubForPago = (sub: SuscripcionServicio) => {
    setPagoSubId(sub.id);
    setPagoMoneda(sub.moneda);
    setPagoMonto(sub.monto_a_cobrar || sub.tarifa_mensual);
    setPagoPeriodo('Octubre 2026');
    setPagoNumTransaccion(`TRF-${Math.floor(1000000 + Math.random() * 9000000)}`);
    setPagoComprobanteUrl('');
    setPagoComprobanteNombre('');
    setPagoEfectivoEntrega(sub.cliente_nombre);
    setIsPagoModalOpen(true);
  };

  // Ver recibo digital de una suscripción pagada
  const handleVerRecibo = (sub: SuscripcionServicio) => {
    const pagoExistente = pagos.find(p => p.suscripcion_id === sub.id || p.cliente_nombre === sub.cliente_nombre);
    if (pagoExistente) {
      setReciboModalData(pagoExistente);
    } else {
      setReciboModalData({
        id: `rec-${sub.id}`,
        codigo_pago: `PAG-2026-000${sub.codigo.replace(/\D/g, '') || '1'}`,
        suscripcion_id: sub.id,
        cliente_id: sub.cliente_id,
        cliente_nombre: sub.cliente_nombre,
        cliente_telefono: sub.cliente_telefono,
        servicio_nombre: sub.nombre_servicio,
        mes_periodo: '2026-10',
        mes_nombre_legible: 'Octubre 2026',
        moneda: sub.moneda,
        tarifa_servicio: sub.tarifa_mensual,
        deuda_previa: 0,
        meses_deuda_previos: 0,
        monto_total_exigible: sub.tarifa_mensual,
        monto_pagado: sub.tarifa_mensual,
        saldo_restante: 0,
        fecha_pago: sub.ultimo_pago_fecha || '2026-10-06',
        dia_pago: 6,
        pago_a_tiempo: true,
        metodo_pago: sub.ultimo_pago_metodo || 'Transferencia',
        plataforma_transferencia: 'Transfermóvil',
        numero_transaccion: sub.ultimo_pago_transaccion || 'TRF-5918234',
        registrado_por: 'Enmanuel Caraballo',
        created_at: '2026-10-06T10:00:00Z'
      });
    }
  };

  // Carga de archivo de imagen para comprobante
  const handleFileUpload = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (file) {
      setPagoComprobanteNombre(file.name);
      const reader = new FileReader();
      reader.onload = (event) => {
        if (event.target?.result) {
          setPagoComprobanteUrl(event.target.result as string);
        }
      };
      reader.readAsDataURL(file);
    }
  };

  // Generar muestra de comprobante Transfermóvil
  const handleGenerateSampleVoucher = () => {
    if (!selectedSub) return;
    const op = pagoNumTransaccion || `TRF-${Math.floor(1000000 + Math.random() * 9000000)}`;
    const montoFormatted = pagoMoneda === 'USD' ? `$${pagoMonto}.00 USD` : `$${pagoMonto.toLocaleString()}.00 CUP`;
    const vUrl = createSampleVoucher(op, montoFormatted, pagoFecha, selectedSub.cliente_nombre, pagoPlataforma);
    setPagoComprobanteUrl(vUrl);
    setPagoComprobanteNombre(`comprobante_whatsapp_${selectedSub.cliente_nombre.toLowerCase().replace(/\s+/g, '_')}.png`);
  };

  // Guardar cobro realizado
  const handleSavePago = (e: React.FormEvent) => {
    e.preventDefault();
    if (!selectedSub) return;

    const diaPago = new Date(pagoFecha).getDate();
    const pagoATiempo = diaPago <= 15;
    const deudaPrevia = selectedSub.deuda_acumulada || 0;
    const totalExigible = selectedSub.monto_a_cobrar || (deudaPrevia + selectedSub.tarifa_mensual);
    const saldoRestante = Math.max(0, totalExigible - pagoMonto);

    let finalComprobanteUrl = pagoComprobanteUrl;
    if (pagoMetodo === 'Transferencia' && !finalComprobanteUrl) {
      const op = pagoNumTransaccion || `TRF-${Math.floor(1000000 + Math.random() * 9000000)}`;
      const montoFormatted = pagoMoneda === 'USD' ? `$${pagoMonto}.00 USD` : `$${pagoMonto.toLocaleString()}.00 CUP`;
      finalComprobanteUrl = createSampleVoucher(op, montoFormatted, pagoFecha, selectedSub.cliente_nombre, pagoPlataforma);
    }

    onAddPago({
      suscripcion_id: selectedSub.id,
      cliente_id: selectedSub.cliente_id,
      cliente_nombre: selectedSub.cliente_nombre,
      cliente_telefono: selectedSub.cliente_telefono,
      servicio_nombre: selectedSub.nombre_servicio,
      mes_periodo: '2026-10',
      mes_nombre_legible: pagoPeriodo,
      moneda: pagoMoneda,
      tarifa_servicio: selectedSub.tarifa_mensual,
      deuda_previa: deudaPrevia,
      meses_deuda_previos: selectedSub.meses_deuda_count,
      meses_deuda_liquidados: selectedSub.meses_deuda_detalle,
      monto_total_exigible: totalExigible,
      monto_pagado: pagoMonto,
      saldo_restante: saldoRestante,
      fecha_pago: pagoFecha,
      dia_pago: diaPago,
      pago_a_tiempo: pagoATiempo,
      metodo_pago: pagoMetodo,
      plataforma_transferencia: pagoMetodo === 'Transferencia' ? pagoPlataforma : undefined,
      numero_transaccion: pagoMetodo === 'Transferencia' ? (pagoNumTransaccion || 'TRF-OK') : undefined,
      comprobante_imagen_url: pagoMetodo === 'Transferencia' ? finalComprobanteUrl : undefined,
      comprobante_nombre: pagoMetodo === 'Transferencia' ? (pagoComprobanteNombre || 'comprobante_whatsapp.jpg') : undefined,
      efectivo_quien_recibe: pagoMetodo === 'Efectivo' ? pagoEfectivoRecibe : undefined,
      efectivo_quien_entrega: pagoMetodo === 'Efectivo' ? pagoEfectivoEntrega : undefined,
      notas: pagoNotas,
      registrado_por: 'Enmanuel Caraballo'
    });

    const updatedSub: SuscripcionServicio = {
      ...selectedSub,
      tiene_deuda: saldoRestante > 0,
      meses_deuda_count: saldoRestante > 0 ? 1 : 0,
      meses_deuda_detalle: saldoRestante > 0 ? ['Saldo restante pendiente'] : [],
      deuda_acumulada: saldoRestante,
      monto_a_cobrar: saldoRestante > 0 ? saldoRestante : selectedSub.tarifa_mensual,
      estado_cobro_mes_actual: saldoRestante === 0 ? 'Al_Dia' : 'Pago_Parcial',
      ultimo_pago_fecha: pagoFecha,
      ultimo_pago_monto: pagoMonto,
      ultimo_pago_metodo: pagoMetodo,
      ultimo_pago_transaccion: pagoMetodo === 'Transferencia' ? pagoNumTransaccion : 'Efectivo'
    };
    onUpdateSuscripcion(updatedSub);

    setIsPagoModalOpen(false);
  };

  // Crear nueva suscripción
  const handleSaveSuscripcion = (e: React.FormEvent) => {
    e.preventDefault();
    const cli = clientes.find(c => c.id === subClienteId) || clientes[0];
    if (!cli) return;

    onAddSuscripcion({
      cliente_id: cli.id,
      cliente_nombre: cli.nombre,
      cliente_telefono: cli.telefono,
      nombre_servicio: subNombreServicio,
      tipo_solucion: subTipoSolucion,
      sim_numero: subSimNumero,
      moneda: subMoneda,
      tarifa_mensual: subTarifa,
      deuda_acumulada: 0,
      meses_deuda_count: 0,
      meses_deuda_detalle: [],
      tiene_deuda: false,
      monto_a_cobrar: subTarifa,
      estado_cobro_mes_actual: 'Pendiente',
      activo: true,
      notas: subNotas
    });

    setIsSubModalOpen(false);
  };

  // Recordatorio por WhatsApp
  const handleOpenWhatsAppReminder = (sub: SuscripcionServicio) => {
    const tel = sub.cliente_telefono || '5350000000';
    const telefonoLimpio = tel.replace(/\D/g, '');
    const montoTexto = sub.moneda === 'USD' ? `$${sub.monto_a_cobrar} USD` : `$${sub.monto_a_cobrar.toLocaleString()} CUP`;
    
    const mensaje = encodeURIComponent(
      `Estimado cliente *${sub.cliente_nombre}*,\n\n` +
      `Le saludamos del equipo de *DataPlus Telecomunicaciones* respecto al servicio de *${sub.nombre_servicio}*.\n\n` +
      (sub.tiene_deuda 
        ? `Le informamos que actualmente presenta un saldo pendiente acumulado de ${montoTexto} correspondiente a meses anteriores y periodo en curso. `
        : `Le recordamos que el pago correspondiente a mes adelantado es de ${montoTexto} y debe efectuarse durante los primeros 15 días del mes. `) +
      `Por favor, recuerde enviar el comprobante de su transferencia a este chat una vez realizada. ¡Muchas gracias!`
    );
    window.open(`https://wa.me/${telefonoLimpio}?text=${mensaje}`, '_blank');
  };

  // Copiar recibo digital
  const handleCopyReceiptText = (p: RegistroPagoServicio) => {
    const texto = `*COMPROBANTE OFICIAL DE PAGO - DATAPLUS*\n` +
      `----------------------------------------\n` +
      `Código de Pago: ${p.codigo_pago}\n` +
      `Cliente: ${p.cliente_nombre}\n` +
      `Servicio: ${p.servicio_nombre}\n` +
      `Mes Liquidado: ${p.mes_nombre_legible}\n` +
      `Monto Abonado: ${p.moneda === 'USD' ? `$${p.monto_pagado} USD` : `$${p.monto_pagado.toLocaleString()} CUP`}\n` +
      `Fecha de Pago: ${p.fecha_pago} (Día ${p.dia_pago} de 15)\n` +
      `Método: ${p.metodo_pago}${p.numero_transaccion ? ` (Ref: ${p.numero_transaccion})` : ''}\n` +
      (p.metodo_pago === 'Efectivo' ? `Entregado por: ${p.efectivo_quien_entrega || 'Cliente'} / Recibido por: ${p.efectivo_quien_recibe}\n` : '') +
      `Estado: PAGO VERIFICADO Y REGISTRADO\n` +
      `Atendido por: ${p.registrado_por} - DataPlus Operaciones`;

    navigator.clipboard.writeText(texto);
    setCopiedReceipt(true);
    setTimeout(() => setCopiedReceipt(false), 2500);
  };

  // Subconjuntos según pestañas
  const subsSemana = useMemo(() => {
    return suscripciones.filter(s => s.estado_cobro_mes_actual === 'Al_Dia' || s.ultimo_pago_fecha?.startsWith('2026-10'));
  }, [suscripciones]);

  const subsDeudores = useMemo(() => {
    return suscripciones.filter(s => s.tiene_deuda || s.estado_cobro_mes_actual === 'En_Mora');
  }, [suscripciones]);

  const baseSubs = useMemo(() => {
    if (activeTab === 'semana') return subsSemana;
    if (activeTab === 'deudores') return subsDeudores;
    return suscripciones;
  }, [activeTab, suscripciones, subsSemana, subsDeudores]);

  // Filtrado final
  const filteredSubs = useMemo(() => {
    return baseSubs.filter(s => {
      const matchSearch = 
        s.cliente_nombre.toLowerCase().includes(searchTerm.toLowerCase()) ||
        s.codigo.toLowerCase().includes(searchTerm.toLowerCase()) ||
        (s.sim_numero && s.sim_numero.includes(searchTerm)) ||
        (s.cliente_telefono && s.cliente_telefono.includes(searchTerm));

      const matchMoneda = 
        filtroMoneda === 'Todas' || s.moneda === filtroMoneda;

      const matchEstado = 
        filtroEstado === 'Todos' ||
        (filtroEstado === 'Cobrados' && s.estado_cobro_mes_actual === 'Al_Dia') ||
        (filtroEstado === 'Pendientes' && s.estado_cobro_mes_actual === 'Pendiente') ||
        (filtroEstado === 'En Mora' && (s.estado_cobro_mes_actual === 'En_Mora' || s.tiene_deuda));

      return matchSearch && matchMoneda && matchEstado;
    });
  }, [baseSubs, searchTerm, filtroMoneda, filtroEstado]);

  // Métricas agregadas del mes actual
  const totalCobradoCUP = useMemo(() => {
    return suscripciones
      .filter(s => s.moneda === 'CUP' && s.estado_cobro_mes_actual === 'Al_Dia')
      .reduce((acc, s) => acc + (s.ultimo_pago_monto || s.tarifa_mensual), 0);
  }, [suscripciones]);

  const metaCobroCUP = useMemo(() => {
    return suscripciones
      .filter(s => s.moneda === 'CUP')
      .reduce((acc, s) => acc + s.tarifa_mensual, 0);
  }, [suscripciones]);

  const totalCobradoUSD = useMemo(() => {
    return suscripciones
      .filter(s => s.moneda === 'USD' && s.estado_cobro_mes_actual === 'Al_Dia')
      .reduce((acc, s) => acc + (s.ultimo_pago_monto || s.tarifa_mensual), 0);
  }, [suscripciones]);

  const metaCobroUSD = useMemo(() => {
    return suscripciones
      .filter(s => s.moneda === 'USD')
      .reduce((acc, s) => acc + s.tarifa_mensual, 0);
  }, [suscripciones]);

  // Cálculos de reportes temporales
  const reportesPeriodos = useMemo(() => {
    const meses = [
      { key: '01', nombre: 'Enero 2026', q: 'Q1', s: 'S1', cup: 21000, usd: 140, expCup: 24500 },
      { key: '02', nombre: 'Febrero 2026', q: 'Q1', s: 'S1', cup: 23500, usd: 150, expCup: 24500 },
      { key: '03', nombre: 'Marzo 2026', q: 'Q1', s: 'S1', cup: 24500, usd: 215, expCup: 24500 },
      { key: '04', nombre: 'Abril 2026', q: 'Q2', s: 'S1', cup: 22000, usd: 180, expCup: 24500 },
      { key: '05', nombre: 'Mayo 2026', q: 'Q2', s: 'S1', cup: 24500, usd: 215, expCup: 24500 },
      { key: '06', nombre: 'Junio 2026', q: 'Q2', s: 'S1', cup: 24500, usd: 215, expCup: 24500 },
      { key: '07', nombre: 'Julio 2026', q: 'Q3', s: 'S2', cup: 24500, usd: 135, expCup: 24500 },
      { key: '08', nombre: 'Agosto 2026', q: 'Q3', s: 'S2', cup: 20700, usd: 140, expCup: 24500 },
      { key: '09', nombre: 'Septiembre 2026', q: 'Q3', s: 'S2', cup: 16900, usd: 60, expCup: 24500 },
      { key: '10', nombre: 'Octubre 2026', q: 'Q4', s: 'S2', cup: totalCobradoCUP || 7700, usd: totalCobradoUSD || 0, expCup: metaCobroCUP || 24500 },
      { key: '11', nombre: 'Noviembre 2026', q: 'Q4', s: 'S2', cup: 0, usd: 0, expCup: 24500 },
      { key: '12', nombre: 'Diciembre 2026', q: 'Q4', s: 'S2', cup: 0, usd: 0, expCup: 24500 },
    ];

    const datosMensuales = meses.map(m => {
      const cumplimiento = m.expCup > 0 ? Math.min(100, Math.round((m.cup / m.expCup) * 100)) : 0;
      return {
        ...m,
        cupCobrado: m.cup,
        usdCobrado: m.usd,
        cupExigible: m.expCup,
        cumplimiento,
        totalOperaciones: m.cup > 0 ? (m.key === '10' ? subsSemana.length : 5) : 0
      };
    });

    const trimestres = [
      { id: 'Q1', nombre: 'Primer Trimestre (Ene - Mar 2026)', meses: ['01', '02', '03'] },
      { id: 'Q2', nombre: 'Segundo Trimestre (Abr - Jun 2026)', meses: ['04', '05', '06'] },
      { id: 'Q3', nombre: 'Tercer Trimestre (Jul - Sep 2026)', meses: ['07', '08', '09'] },
      { id: 'Q4', nombre: 'Cuarto Trimestre (Oct - Dic 2026)', meses: ['10', '11', '12'] },
    ].map(t => {
      const ms = datosMensuales.filter(m => t.meses.includes(m.key));
      const cupCobrado = ms.reduce((a, b) => a + b.cupCobrado, 0);
      const usdCobrado = ms.reduce((a, b) => a + b.usdCobrado, 0);
      const cupExigible = ms.reduce((a, b) => a + b.cupExigible, 0);
      const cumplimiento = cupExigible > 0 ? Math.round((cupCobrado / cupExigible) * 100) : 0;
      return { ...t, cupCobrado, usdCobrado, cupExigible, cumplimiento };
    });

    const semestres = [
      { id: 'S1', nombre: 'Primer Semestre (Enero - Junio 2026)', meses: ['01', '02', '03', '04', '05', '06'] },
      { id: 'S2', nombre: 'Segundo Semestre (Julio - Diciembre 2026)', meses: ['07', '08', '09', '10', '11', '12'] },
    ].map(s => {
      const ms = datosMensuales.filter(m => s.meses.includes(m.key));
      const cupCobrado = ms.reduce((a, b) => a + b.cupCobrado, 0);
      const usdCobrado = ms.reduce((a, b) => a + b.usdCobrado, 0);
      const cupExigible = ms.reduce((a, b) => a + b.cupExigible, 0);
      const cumplimiento = cupExigible > 0 ? Math.round((cupCobrado / cupExigible) * 100) : 0;
      return { ...s, cupCobrado, usdCobrado, cupExigible, cumplimiento };
    });

    const totalAnualCUP = datosMensuales.reduce((a, b) => a + b.cupCobrado, 0);
    const totalAnualUSD = datosMensuales.reduce((a, b) => a + b.usdCobrado, 0);
    const totalExigibleCUP = datosMensuales.reduce((a, b) => a + b.cupExigible, 0);
    const cumplimientoGlobal = totalExigibleCUP > 0 ? Math.round((totalAnualCUP / totalExigibleCUP) * 100) : 0;

    return {
      datosMensuales,
      trimestres,
      semestres,
      anual: {
        totalAnualCUP,
        totalAnualUSD,
        cumplimientoGlobal
      }
    };
  }, [totalCobradoCUP, totalCobradoUSD, metaCobroCUP, subsSemana.length]);

  return (
    <div className="space-y-6">
      {/* ================= ENCABEZADO OFICIAL DE FACTURACIÓN ================= */}
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h2 className="text-xl font-bold text-white tracking-tight flex items-center gap-2">
            <Radio className="h-5 w-5 text-sky-400" />
            Facturación de Gestión Remota & Servicios
          </h2>
          <p className="text-xs text-slate-400">
            Control de cobros mensuales, pólizas de conectividad, historial de pagos y reportes periódicos.
          </p>
        </div>

        <div className="flex items-center gap-2">
          <button
            onClick={() => {
              setSubClienteId(clientes[0]?.id || '');
              setIsSubModalOpen(true);
            }}
            className="flex items-center gap-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-bold px-3 py-2 text-xs transition-all cursor-pointer"
          >
            <Plus className="h-4 w-4 text-sky-400" />
            Nueva Suscripción
          </button>

          <button
            onClick={() => {
              const primerPendiente = suscripciones.find(s => s.estado_cobro_mes_actual !== 'Al_Dia') || suscripciones[0];
              if (primerPendiente) {
                handleSelectSubForPago(primerPendiente);
              }
            }}
            className="flex items-center gap-1.5 rounded-lg bg-sky-500 hover:bg-sky-400 text-slate-950 font-bold px-4 py-2 text-xs transition-all shadow-md shadow-sky-500/20 cursor-pointer"
          >
            <CreditCard className="h-4 w-4 stroke-[2.5]" />
            Registrar Cobro
          </button>
        </div>
      </div>

      {/* ================= HERO BANNER PRINCIPAL (ESTILO REPORTE MENSUAL) ================= */}
      <div className="rounded-2xl bg-gradient-to-r from-sky-600 to-sky-700 text-white p-6 shadow-xl relative overflow-hidden flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <p className="text-xs font-semibold uppercase tracking-wider text-sky-200">
            Periodo de Cobro: Octubre 2026 · Mes Adelantado
          </p>
          <h2 className="text-2xl font-extrabold text-white mt-1">
            Facturación de Gestión Remota & Servicios Recurrentes
          </h2>
          <p className="text-xs text-sky-100/90 mt-1">
            DataPlus S.R.L. · Plazo en curso: <strong className="text-white">{diasRestantes} días restantes</strong> (Día {diaActual} de {diasLimite}) · Norma operativa: Liquidación en los primeros 15 días.
          </p>
        </div>
        <div className="bg-sky-950/40 backdrop-blur-sm border border-sky-400/30 rounded-xl p-4 text-right min-w-[220px]">
          <p className="text-[10px] font-bold uppercase tracking-wider text-sky-200">Recaudado Mes en Curso</p>
          <p className="text-2xl font-extrabold font-mono text-white mt-0.5">
            ${totalCobradoCUP.toLocaleString()} <span className="text-sm font-sans text-sky-200">CUP</span>
          </p>
          {totalCobradoUSD > 0 && (
            <p className="text-xs font-bold text-sky-300 font-mono mt-0.5">
              +${totalCobradoUSD.toLocaleString()} USD
            </p>
          )}
        </div>
      </div>

      {/* ================= TARJETAS KPI / ESTADO OPERATIVO ================= */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {/* Tarjeta 1: Periodo & Norma Operativa */}
        <div className="rounded-2xl border border-slate-800 bg-slate-900/60 p-4">
          <div className="flex items-center justify-between">
            <span className="text-xs font-semibold text-slate-400">Periodo en Curso</span>
            <div className="rounded-lg bg-sky-500/10 p-2 text-sky-400 border border-sky-500/20">
              <Calendar className="h-4 w-4" />
            </div>
          </div>
          <p className="text-xl font-bold text-white mt-2">Octubre 2026</p>
          <div className="mt-1 flex items-center justify-between text-[11px]">
            <span className="text-slate-400">Día {diaActual} de {diasLimite}</span>
            <span className="rounded bg-amber-500/10 text-amber-400 border border-amber-500/20 px-1.5 py-0.5 font-bold">
              {diasRestantes} días restantes
            </span>
          </div>
        </div>

        {/* Tarjeta 2: Recaudación CUP */}
        <div className="rounded-2xl border border-slate-800 bg-slate-900/60 p-4">
          <div className="flex items-center justify-between">
            <span className="text-xs font-semibold text-slate-400">Recaudación CUP</span>
            <div className="rounded-lg bg-emerald-500/10 p-2 text-emerald-400 border border-emerald-500/20">
              <DollarSign className="h-4 w-4" />
            </div>
          </div>
          <p className="text-xl font-bold text-emerald-400 mt-2">
            ${totalCobradoCUP.toLocaleString()} CUP
          </p>
          <p className="text-[11px] text-slate-400 mt-1 flex items-center justify-between">
            <span>Meta: ${metaCobroCUP.toLocaleString()} CUP</span>
            <span className="font-semibold text-slate-300">
              {metaCobroCUP > 0 ? Math.round((totalCobradoCUP / metaCobroCUP) * 100) : 0}%
            </span>
          </p>
        </div>

        {/* Tarjeta 3: Recaudación USD */}
        <div className="rounded-2xl border border-slate-800 bg-slate-900/60 p-4">
          <div className="flex items-center justify-between">
            <span className="text-xs font-semibold text-slate-400">Recaudación USD</span>
            <div className="rounded-lg bg-indigo-500/10 p-2 text-indigo-400 border border-indigo-500/20">
              <CreditCard className="h-4 w-4" />
            </div>
          </div>
          <p className="text-xl font-bold text-white mt-2">
            ${totalCobradoUSD} USD
          </p>
          <p className="text-[11px] text-slate-400 mt-1 flex items-center justify-between">
            <span>Meta: ${metaCobroUSD} USD</span>
            <span className="font-semibold text-slate-300">
              {metaCobroUSD > 0 ? Math.round((totalCobradoUSD / metaCobroUSD) * 100) : 0}%
            </span>
          </p>
        </div>

        {/* Tarjeta 4: Estado de Clientes */}
        <div className="rounded-2xl border border-slate-800 bg-slate-900/60 p-4">
          <div className="flex items-center justify-between">
            <span className="text-xs font-semibold text-slate-400">Cumplimiento Global</span>
            <div className="rounded-lg bg-sky-500/10 p-2 text-sky-400 border border-sky-500/20">
              <CheckCircle2 className="h-4 w-4" />
            </div>
          </div>
          <p className="text-xl font-bold text-white mt-2">
            <span className="text-emerald-400">{subsSemana.length}</span> / <span className="text-slate-300">{suscripciones.length}</span> clientes
          </p>
          <p className="text-[11px] text-slate-400 mt-1 flex items-center justify-between">
            <span>En mora / Deuda:</span>
            <span className={`font-semibold ${subsDeudores.length > 0 ? 'text-rose-400' : 'text-emerald-400'}`}>
              {subsDeudores.length}
            </span>
          </p>
        </div>
      </div>

      {/* ================= PESTAÑAS PRINCIPALES DEL MÓDULO ================= */}
      <div className="border-b border-slate-800 flex gap-4 overflow-x-auto">
        <button
          onClick={() => setActiveTab('cobros')}
          className={`pb-3 text-xs font-bold transition-colors cursor-pointer border-b-2 flex items-center gap-2 whitespace-nowrap ${
            activeTab === 'cobros'
              ? 'border-sky-500 text-sky-400'
              : 'border-transparent text-slate-400 hover:text-slate-200'
          }`}
        >
          <Receipt className="h-4 w-4" />
          Control de Cobros Mensuales
          <span className="ml-1 rounded bg-slate-800 px-1.5 py-0.5 text-[10px] text-slate-300 font-mono">
            {suscripciones.length}
          </span>
        </button>

        <button
          onClick={() => setActiveTab('semana')}
          className={`pb-3 text-xs font-bold transition-colors cursor-pointer border-b-2 flex items-center gap-2 whitespace-nowrap ${
            activeTab === 'semana'
              ? 'border-emerald-500 text-emerald-400'
              : 'border-transparent text-slate-400 hover:text-slate-200'
          }`}
        >
          <CheckCircle2 className="h-4 w-4" />
          Pagaron esta Semana
          <span className="ml-1 rounded bg-emerald-500/10 px-1.5 py-0.5 text-[10px] text-emerald-400 font-mono border border-emerald-500/20">
            {subsSemana.length}
          </span>
        </button>

        <button
          onClick={() => setActiveTab('deudores')}
          className={`pb-3 text-xs font-bold transition-colors cursor-pointer border-b-2 flex items-center gap-2 whitespace-nowrap ${
            activeTab === 'deudores'
              ? 'border-rose-500 text-rose-400'
              : 'border-transparent text-slate-400 hover:text-slate-200'
          }`}
        >
          <AlertCircle className="h-4 w-4" />
          Mayores Deudores
          {subsDeudores.length > 0 && (
            <span className="ml-1 rounded bg-rose-500/10 px-1.5 py-0.5 text-[10px] text-rose-400 font-mono border border-rose-500/20">
              {subsDeudores.length}
            </span>
          )}
        </button>

        <button
          onClick={() => setActiveTab('reporte')}
          className={`pb-3 text-xs font-bold transition-colors cursor-pointer border-b-2 flex items-center gap-2 whitespace-nowrap ${
            activeTab === 'reporte'
              ? 'border-indigo-500 text-indigo-400'
              : 'border-transparent text-slate-400 hover:text-slate-200'
          }`}
        >
          <TrendingUp className="h-4 w-4" />
          Reporte Temporal (Año / Sem / Trim / Mes)
        </button>
      </div>

      {/* ================= CONTENIDO DE TABLAS O REPORTE ================= */}
      {activeTab !== 'reporte' ? (
        <div className="space-y-4">
          {/* BARRA DE FILTROS & BÚSQUEDA */}
          <div className="flex flex-col sm:flex-row items-center justify-between gap-3 bg-slate-900/70 p-3.5 rounded-xl border border-slate-800">
            <div className="relative w-full sm:w-80">
              <Search className="absolute left-3 top-2.5 h-4 w-4 text-slate-400" />
              <input
                type="text"
                placeholder="Buscar por cliente, SIM, router o código..."
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
                className="w-full rounded-lg border border-slate-700 bg-slate-950 pl-9 pr-4 py-1.5 text-xs text-slate-200 placeholder-slate-400 focus:border-sky-500 focus:outline-none"
              />
            </div>

            <div className="flex items-center gap-2 w-full sm:w-auto">
              <select
                value={filtroMoneda}
                onChange={(e) => setFiltroMoneda(e.target.value as any)}
                className="rounded-lg border border-slate-700 bg-slate-950 px-3 py-1.5 text-xs text-slate-300 focus:border-sky-500 focus:outline-none"
              >
                <option value="Todas">Todas las Monedas</option>
                <option value="CUP">Moneda: CUP</option>
                <option value="USD">Moneda: USD</option>
              </select>

              <select
                value={filtroEstado}
                onChange={(e) => setFiltroEstado(e.target.value as any)}
                className="rounded-lg border border-slate-700 bg-slate-950 px-3 py-1.5 text-xs text-slate-300 focus:border-sky-500 focus:outline-none"
              >
                <option value="Todos">Todos los Estados</option>
                <option value="Cobrados">Cobrados / Al Día</option>
                <option value="Pendientes">Pendientes (Día 1-15)</option>
                <option value="En Mora">En Mora / Con Deuda</option>
              </select>
            </div>
          </div>

          {/* TABLA PRINCIPAL DE GESTIÓN */}
          <div className="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/50 shadow-sm">
            <div className="overflow-x-auto">
              <table className="w-full text-left text-xs text-slate-300">
                <thead className="bg-slate-950/80 text-[11px] font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-800">
                  <tr>
                    <th className="py-3.5 px-4">Código</th>
                    <th className="py-3.5 px-4">Cliente / Contacto</th>
                    <th className="py-3.5 px-4">Solución / SIM</th>
                    <th className="py-3.5 px-4">Mensualidad</th>
                    <th className="py-3.5 px-4">Deuda Anterior</th>
                    <th className="py-3.5 px-4">Total a Cobrar</th>
                    <th className="py-3.5 px-4 text-center">Estado</th>
                    <th className="py-3.5 px-4 text-center">Acciones</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-slate-800/80">
                  {filteredSubs.length === 0 ? (
                    <tr>
                      <td colSpan={8} className="py-8 text-center text-slate-500">
                        No se encontraron suscripciones con los filtros seleccionados.
                      </td>
                    </tr>
                  ) : (
                    filteredSubs.map((sub) => {
                      const isAlDia = sub.estado_cobro_mes_actual === 'Al_Dia';
                      const isPendiente = sub.estado_cobro_mes_actual === 'Pendiente';
                      const isEnMora = sub.estado_cobro_mes_actual === 'En_Mora' || sub.tiene_deuda;

                      return (
                        <tr key={sub.id} className="hover:bg-slate-800/40 transition-colors">
                          {/* Código */}
                          <td className="py-3.5 px-4 font-mono font-bold text-sky-400 whitespace-nowrap">
                            {sub.codigo}
                          </td>

                          {/* Cliente / Contacto */}
                          <td className="py-3.5 px-4">
                            <span className="font-semibold text-white block">{sub.cliente_nombre}</span>
                            {sub.cliente_telefono && (
                              <span className="text-[11px] text-slate-400 flex items-center gap-1 mt-0.5 font-mono">
                                <Phone className="h-3 w-3 text-teal-400" />
                                {sub.cliente_telefono}
                              </span>
                            )}
                          </td>

                          {/* Solución / SIM */}
                          <td className="py-3.5 px-4">
                            <span className="text-slate-200 block">{sub.tipo_solucion || sub.nombre_servicio}</span>
                            <span className="text-[11px] text-sky-400 font-mono mt-0.5 block">
                              SIM: {sub.sim_numero || 'Sin asignar'}
                            </span>
                          </td>

                          {/* Mensualidad */}
                          <td className="py-3.5 px-4 font-mono font-semibold text-slate-200 whitespace-nowrap">
                            {sub.moneda === 'USD' 
                              ? `$${sub.tarifa_mensual}.00 USD` 
                              : `$${sub.tarifa_mensual.toLocaleString()}.00 CUP`}
                          </td>

                          {/* Deuda Anterior */}
                          <td className="py-3.5 px-4 whitespace-nowrap">
                            {sub.tiene_deuda && sub.deuda_acumulada > 0 ? (
                              <div>
                                <span className="font-mono font-bold text-rose-400">
                                  +{sub.moneda === 'USD' 
                                    ? `$${sub.deuda_acumulada}.00` 
                                    : `$${sub.deuda_acumulada.toLocaleString()}.00`}
                                </span>
                                <span className="block text-[10px] text-slate-400">
                                  ({sub.meses_deuda_count} {sub.meses_deuda_count === 1 ? 'mes' : 'meses'})
                                </span>
                              </div>
                            ) : (
                              <span className="font-mono text-slate-500">$0.00</span>
                            )}
                          </td>

                          {/* Total a Cobrar */}
                          <td className="py-3.5 px-4 font-mono font-extrabold text-white whitespace-nowrap">
                            {sub.moneda === 'USD'
                              ? `$${sub.monto_a_cobrar}.00 USD`
                              : `$${sub.monto_a_cobrar.toLocaleString()}.00 CUP`}
                          </td>

                          {/* Estado */}
                          <td className="py-3.5 px-4 text-center whitespace-nowrap">
                            {isAlDia && (
                              <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded text-[11px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                <Check className="h-3 w-3 stroke-[2.5]" />
                                Cobrado
                              </span>
                            )}
                            {isPendiente && (
                              <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded text-[11px] font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                <Clock className="h-3 w-3" />
                                Pendiente (Día 1-15)
                              </span>
                            )}
                            {isEnMora && (
                              <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded text-[11px] font-semibold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                <AlertTriangle className="h-3 w-3" />
                                En Mora
                              </span>
                            )}
                          </td>

                          {/* Acciones */}
                          <td className="py-3.5 px-4 text-center whitespace-nowrap">
                            <div className="flex items-center justify-center gap-2">
                              {isAlDia ? (
                                <button
                                  onClick={() => handleVerRecibo(sub)}
                                  className="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-medium transition-colors cursor-pointer"
                                  title="Ver Recibo Oficial Digital"
                                >
                                  <Receipt className="h-3.5 w-3.5 text-teal-400" />
                                  <span>Recibo</span>
                                </button>
                              ) : (
                                <button
                                  onClick={() => handleSelectSubForPago(sub)}
                                  className="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-sky-500/10 hover:bg-sky-500/20 text-sky-400 border border-sky-500/30 text-xs font-semibold transition-colors cursor-pointer"
                                  title="Registrar cobro de la mensualidad"
                                >
                                  <CreditCard className="h-3.5 w-3.5" />
                                  <span>Cobrar</span>
                                </button>
                              )}

                              <button
                                onClick={() => handleOpenWhatsAppReminder(sub)}
                                title="Enviar recordatorio formal por WhatsApp"
                                className="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-emerald-400 border border-slate-700 transition-colors cursor-pointer"
                              >
                                <MessageSquare className="h-3.5 w-3.5" />
                              </button>
                            </div>
                          </td>
                        </tr>
                      );
                    })
                  )}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      ) : (
        /* ================= PESTAÑA DE REPORTES TEMPORALES & GRÁFICA ================= */
        <div className="rounded-2xl border border-slate-800 bg-slate-900/60 p-5 sm:p-6 space-y-6">
          <div className="flex flex-col sm:flex-row justify-between sm:items-center gap-4 border-b border-slate-800 pb-4">
            <div>
              <h3 className="text-base font-bold text-white flex items-center gap-2">
                <TrendingUp className="h-5 w-5 text-indigo-400" />
                Evolución Temporal & Comparativa de Recaudación (2026)
              </h3>
              <p className="text-xs text-slate-400 mt-0.5">
                Desglose analítico de cobros y efectividad por Año, Semestre, Trimestre y Mes.
              </p>
            </div>

            {/* Selector de Período estilo DataPlus */}
            <div className="flex items-center p-1 bg-slate-950 rounded-xl border border-slate-800 text-xs">
              {(['mensual', 'trimestral', 'semestral', 'anual'] as const).map((per) => (
                <button
                  key={per}
                  onClick={() => setPeriodoReporte(per)}
                  className={`px-3 py-1.5 rounded-lg font-bold transition-all capitalize cursor-pointer ${
                    periodoReporte === per
                      ? 'bg-sky-500 text-slate-950 shadow-sm'
                      : 'text-slate-400 hover:text-slate-200'
                  }`}
                >
                  {per}
                </button>
              ))}
            </div>
          </div>

          {/* VISTA MENSUAL: GRÁFICO INTERACTIVO DE BARRAS */}
          {periodoReporte === 'mensual' && (() => {
            const maxVal = Math.max(...reportesPeriodos.datosMensuales.map(m => m.cupCobrado), 24500);

            return (
              <div className="space-y-6">
                {/* Gráfico de barras */}
                <div className="rounded-xl border border-slate-800 bg-slate-950/70 p-5">
                  <div className="flex justify-between items-center mb-6">
                    <div>
                      <span className="text-xs font-semibold text-slate-400">Recaudación Mensual en CUP</span>
                      <p className="text-xs text-slate-500 mt-0.5">Pasa el cursor sobre las barras para ver detalles</p>
                    </div>
                    <div className="flex items-center gap-4 text-[11px] text-slate-400">
                      <div className="flex items-center gap-1.5">
                        <span className="w-2.5 h-2.5 rounded bg-sky-500"></span>
                        <span>Cobrado</span>
                      </div>
                      <div className="flex items-center gap-1.5">
                        <span className="w-3 border-t border-dashed border-amber-400"></span>
                        <span>Meta mensual ($24.5k CUP)</span>
                      </div>
                    </div>
                  </div>

                  {/* Lienzo del gráfico */}
                  <div className="relative h-64 pt-6 pb-2 flex items-end justify-between gap-1 sm:gap-2">
                    {/* Línea de meta discontinua */}
                    <div 
                      className="absolute inset-x-0 border-t border-dashed border-amber-400/50 pointer-events-none z-10"
                      style={{ bottom: `${Math.round((24500 / maxVal) * 85)}%` }}
                    >
                      <span className="absolute right-0 -top-5 text-[10px] font-mono text-amber-400/80 bg-slate-950/90 px-1 rounded">
                        Meta: $24,500 CUP
                      </span>
                    </div>

                    {reportesPeriodos.datosMensuales.map((m) => {
                      const pct = Math.round((m.cupCobrado / maxVal) * 85);
                      const isCurrentMonth = m.key === '10';

                      return (
                        <div key={m.key} className="flex-1 flex flex-col items-center h-full justify-end group relative">
                          {/* Tooltip flotante */}
                          <div className="absolute -top-16 opacity-0 group-hover:opacity-100 transition-opacity bg-slate-900 border border-slate-700 text-white rounded-lg px-2.5 py-1.5 text-[11px] shadow-2xl pointer-events-none z-30 whitespace-nowrap font-sans">
                            <p className="font-bold text-sky-400">{m.nombre}</p>
                            <p className="font-mono text-xs">${m.cupCobrado.toLocaleString()} CUP</p>
                            {m.usdCobrado > 0 && <p className="font-mono text-indigo-400">${m.usdCobrado} USD</p>}
                            <p className="text-[10px] text-slate-400">{m.cumplimiento}% de la meta</p>
                          </div>

                          {/* Barra de progreso */}
                          <div className="w-full max-w-[32px] bg-slate-800/80 rounded-t-lg h-full flex items-end overflow-hidden p-0.5">
                            <div
                              style={{ height: `${pct}%` }}
                              className={`w-full rounded-t transition-all duration-500 ${
                                isCurrentMonth
                                  ? 'bg-gradient-to-t from-sky-600 to-sky-400 shadow-md shadow-sky-500/30'
                                  : m.cupCobrado > 0
                                  ? 'bg-gradient-to-t from-teal-600 to-teal-400 group-hover:from-teal-500 group-hover:to-teal-300'
                                  : 'bg-slate-800'
                              }`}
                            ></div>
                          </div>

                          {/* Etiqueta del Mes */}
                          <span className={`text-[10px] font-mono mt-2 transition-colors ${
                            isCurrentMonth ? 'text-sky-400 font-bold' : 'text-slate-400 group-hover:text-slate-200'
                          }`}>
                            {m.nombre.substring(0, 3)}
                          </span>
                        </div>
                      );
                    })}
                  </div>
                </div>

                {/* Tabla desglosada mensual */}
                <div className="overflow-hidden rounded-xl border border-slate-800 bg-slate-950/70">
                  <table className="w-full text-left text-xs text-slate-300">
                    <thead className="border-b border-slate-800 bg-slate-900/80 text-[11px] font-semibold text-slate-400 uppercase">
                      <tr>
                        <th className="py-2.5 px-4">Mes</th>
                        <th className="py-2.5 px-4">Cobrado CUP</th>
                        <th className="py-2.5 px-4">Cobrado USD</th>
                        <th className="py-2.5 px-4">Cumplimiento</th>
                      </tr>
                    </thead>
                    <tbody className="divide-y divide-slate-800/60 font-mono">
                      {reportesPeriodos.datosMensuales.map((m) => (
                        <tr key={m.key} className="hover:bg-slate-800/30 transition-colors">
                          <td className="py-2.5 px-4 font-sans font-medium text-white flex items-center gap-2">
                            {m.nombre}
                            {m.key === '10' && (
                              <span className="rounded bg-sky-500/10 text-sky-400 border border-sky-500/20 px-1.5 py-0.2 text-[9px] font-bold font-sans">
                                En curso
                              </span>
                            )}
                          </td>
                          <td className="py-2.5 px-4 text-emerald-400 font-bold">${m.cupCobrado.toLocaleString()}</td>
                          <td className="py-2.5 px-4 text-slate-300">${m.usdCobrado}</td>
                          <td className="py-2.5 px-4">
                            <div className="flex items-center gap-2">
                              <div className="w-24 bg-slate-800 h-1.5 rounded-full overflow-hidden">
                                <div 
                                  className="bg-sky-500 h-full rounded-full" 
                                  style={{ width: `${m.cumplimiento}%` }}
                                ></div>
                              </div>
                              <span className="text-[11px] text-slate-400">{m.cumplimiento}%</span>
                            </div>
                          </td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              </div>
            );
          })()}

          {/* VISTA TRIMESTRAL */}
          {periodoReporte === 'trimestral' && (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
              {reportesPeriodos.trimestres.map((t) => (
                <div key={t.id} className="p-4 rounded-xl border border-slate-800 bg-slate-950/70 space-y-3">
                  <div className="flex justify-between items-center">
                    <span className="font-mono text-xs font-bold text-sky-400 border border-sky-500/20 bg-sky-500/10 px-2 py-0.5 rounded">
                      {t.id}
                    </span>
                    <span className="text-xs font-mono font-bold text-emerald-400">{t.cumplimiento}%</span>
                  </div>
                  <h4 className="text-xs font-bold text-white">{t.nombre}</h4>
                  <div className="w-full bg-slate-800 h-1.5 rounded-full overflow-hidden">
                    <div className="bg-sky-500 h-full rounded-full" style={{ width: `${t.cumplimiento}%` }}></div>
                  </div>
                  <div className="grid grid-cols-2 gap-2 pt-2 border-t border-slate-800 text-xs">
                    <div>
                      <p className="text-[10px] text-slate-400">CUP:</p>
                      <p className="text-xs font-bold font-mono text-emerald-400">${t.cupCobrado.toLocaleString()}</p>
                    </div>
                    <div>
                      <p className="text-[10px] text-slate-400">USD:</p>
                      <p className="text-xs font-bold font-mono text-slate-200">${t.usdCobrado}</p>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          )}

          {/* VISTA SEMESTRAL */}
          {periodoReporte === 'semestral' && (
            <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
              {reportesPeriodos.semestres.map((s) => (
                <div key={s.id} className="p-5 rounded-xl border border-slate-800 bg-slate-950/70 space-y-4">
                  <div className="flex justify-between items-center">
                    <span className="font-mono text-xs font-bold text-sky-400 border border-sky-500/20 bg-sky-500/10 px-2 py-0.5 rounded">
                      {s.id}
                    </span>
                    <span className="text-xs font-mono font-bold text-emerald-400">{s.cumplimiento}% efectividad</span>
                  </div>
                  <h4 className="text-sm font-bold text-white">{s.nombre}</h4>
                  <div className="w-full bg-slate-800 h-2 rounded-full overflow-hidden">
                    <div className="bg-sky-500 h-full rounded-full" style={{ width: `${s.cumplimiento}%` }}></div>
                  </div>
                  <div className="space-y-2 pt-2 border-t border-slate-800 text-xs">
                    <div className="flex justify-between text-slate-400">
                      <span>Total CUP Cobrado:</span>
                      <span className="font-mono font-bold text-emerald-400">${s.cupCobrado.toLocaleString()} CUP</span>
                    </div>
                    <div className="flex justify-between text-slate-400">
                      <span>Total USD Cobrado:</span>
                      <span className="font-mono font-bold text-slate-200">${s.usdCobrado} USD</span>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          )}

          {/* VISTA ANUAL */}
          {periodoReporte === 'anual' && (
            <div className="p-6 rounded-xl border border-slate-800 bg-slate-950/70 space-y-5">
              <div className="flex justify-between items-center">
                <h4 className="text-base font-bold text-white">Consolidado Anual Ejercicio Fiscal 2026</h4>
                <span className="font-mono text-xs font-bold border border-sky-500/30 bg-sky-500/10 text-sky-400 px-3 py-1 rounded-full">
                  {reportesPeriodos.anual.cumplimientoGlobal}% Cumplimiento Anual
                </span>
              </div>
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-slate-800">
                <div className="p-4 rounded-xl border border-slate-800 bg-slate-900/60">
                  <p className="text-xs text-slate-400 font-semibold">Total Cobrado en CUP</p>
                  <p className="text-2xl font-extrabold text-emerald-400 font-mono mt-1">
                    ${reportesPeriodos.anual.totalAnualCUP.toLocaleString()} CUP
                  </p>
                </div>
                <div className="p-4 rounded-xl border border-slate-800 bg-slate-900/60">
                  <p className="text-xs text-slate-400 font-semibold">Total Cobrado en USD</p>
                  <p className="text-2xl font-extrabold text-white font-mono mt-1">
                    ${reportesPeriodos.anual.totalAnualUSD} USD
                  </p>
                </div>
              </div>
            </div>
          )}
        </div>
      )}

      {/* ========================================================================= */}
      {/* MODALES FLOTANTES (CIERRE SEGURO: CLICK BACKDROP, ESCAPE, BOTÓN X)       */}
      {/* ========================================================================= */}

      {/* 1. MODAL REGISTRAR COBRO */}
      {isPagoModalOpen && selectedSub && (
        <div 
          onClick={() => setIsPagoModalOpen(false)} 
          className="fixed inset-0 z-50 flex items-center justify-center bg-black/75 backdrop-blur-sm p-4 overflow-y-auto cursor-pointer"
        >
          <div 
            onClick={(e) => e.stopPropagation()} 
            className="relative w-full max-w-lg rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-2xl my-8 text-slate-100 cursor-default"
          >
            <div className="flex items-center justify-between border-b border-slate-800 pb-3 mb-5">
              <div className="flex items-center gap-2">
                <div className="rounded-lg bg-sky-500/10 p-2 text-sky-400 border border-sky-500/20">
                  <CreditCard className="h-5 w-5" />
                </div>
                <div>
                  <h3 className="text-base font-bold text-white">Registrar Cobro de Mensualidad</h3>
                  <p className="text-xs text-slate-400">Gestión Remota · Mes Adelantado (1-15 días)</p>
                </div>
              </div>
              <button 
                onClick={() => setIsPagoModalOpen(false)} 
                className="text-slate-400 hover:text-white p-1 rounded-lg transition-colors cursor-pointer"
              >
                <X className="h-5 w-5" />
              </button>
            </div>

            <form onSubmit={handleSavePago} className="space-y-4">
              <div>
                <label className="block text-[11px] font-medium text-slate-400 mb-1">Cliente / Servicio *</label>
                <select
                  value={pagoSubId}
                  onChange={(e) => {
                    const sub = suscripciones.find(s => s.id === e.target.value);
                    if (sub) {
                      setPagoSubId(sub.id);
                      setPagoMoneda(sub.moneda);
                      setPagoMonto(sub.monto_a_cobrar || sub.tarifa_mensual);
                      setPagoEfectivoEntrega(sub.cliente_nombre);
                    }
                  }}
                  className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-sky-500 focus:outline-none"
                >
                  {suscripciones.map(s => (
                    <option key={s.id} value={s.id}>
                      {s.cliente_nombre} ({s.codigo}) · Tarifa: {s.moneda === 'USD' ? `$${s.tarifa_mensual} USD` : `$${s.tarifa_mensual.toLocaleString()} CUP`}
                    </option>
                  ))}
                </select>
              </div>

              {/* Resumen del estado de cuenta */}
              <div className="p-3.5 rounded-xl border border-slate-800 bg-slate-950/70 space-y-2 text-xs">
                <div className="flex justify-between text-slate-400">
                  <span>Mensualidad base:</span>
                  <span className="font-mono text-white font-bold">
                    {selectedSub.moneda === 'USD' ? `$${selectedSub.tarifa_mensual} USD` : `$${selectedSub.tarifa_mensual.toLocaleString()} CUP`}
                  </span>
                </div>
                {selectedSub.tiene_deuda && (
                  <div className="flex justify-between text-slate-400">
                    <span>Deuda anterior acumulada:</span>
                    <span className="font-mono text-rose-400 font-bold">
                      +{selectedSub.moneda === 'USD' ? `$${selectedSub.deuda_acumulada} USD` : `$${selectedSub.deuda_acumulada.toLocaleString()} CUP`}
                    </span>
                  </div>
                )}
                <div className="flex justify-between text-slate-200 font-bold border-t border-slate-800 pt-2">
                  <span>Total exigible a liquidar:</span>
                  <span className="font-mono text-sm text-sky-400">
                    {selectedSub.moneda === 'USD' ? `$${selectedSub.monto_a_cobrar} USD` : `$${selectedSub.monto_a_cobrar.toLocaleString()} CUP`}
                  </span>
                </div>
              </div>

              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">Monto a Abonar *</label>
                  <input
                    type="number"
                    required
                    value={pagoMonto}
                    onChange={(e) => setPagoMonto(Number(e.target.value))}
                    className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs font-mono font-bold text-white focus:border-sky-500 focus:outline-none"
                  />
                </div>
                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">Fecha de Cobro *</label>
                  <input
                    type="date"
                    required
                    value={pagoFecha}
                    onChange={(e) => setPagoFecha(e.target.value)}
                    className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-sky-500 focus:outline-none"
                  />
                </div>
              </div>

              <div>
                <label className="block text-[11px] font-medium text-slate-400 mb-1">Método de Cobro *</label>
                <div className="grid grid-cols-2 gap-3">
                  <button
                    type="button"
                    onClick={() => setPagoMetodo('Transferencia')}
                    className={`p-2.5 rounded-lg border text-xs font-bold transition-all cursor-pointer ${
                      pagoMetodo === 'Transferencia'
                        ? 'border-sky-500/50 bg-sky-500/10 text-sky-400'
                        : 'border-slate-800 bg-slate-950 text-slate-400 hover:text-slate-200'
                    }`}
                  >
                    Transferencia Bancaria
                  </button>
                  <button
                    type="button"
                    onClick={() => setPagoMetodo('Efectivo')}
                    className={`p-2.5 rounded-lg border text-xs font-bold transition-all cursor-pointer ${
                      pagoMetodo === 'Efectivo'
                        ? 'border-sky-500/50 bg-sky-500/10 text-sky-400'
                        : 'border-slate-800 bg-slate-950 text-slate-400 hover:text-slate-200'
                    }`}
                  >
                    Pago en Efectivo
                  </button>
                </div>
              </div>

              {pagoMetodo === 'Transferencia' ? (
                <div className="p-3.5 rounded-xl border border-slate-800 bg-slate-950/70 space-y-3">
                  <div className="grid grid-cols-2 gap-3">
                    <div>
                      <label className="block text-[10px] font-medium text-slate-400 mb-1">Plataforma</label>
                      <select
                        value={pagoPlataforma}
                        onChange={(e) => setPagoPlataforma(e.target.value)}
                        className="w-full rounded-lg border border-slate-800 bg-slate-950 px-2.5 py-1.5 text-xs text-slate-200 focus:border-sky-500 focus:outline-none"
                      >
                        <option value="Transfermóvil">Transfermóvil</option>
                        <option value="EnZona">EnZona</option>
                        <option value="Zelle">Zelle</option>
                        <option value="TropiPay">TropiPay</option>
                      </select>
                    </div>
                    <div>
                      <label className="block text-[10px] font-medium text-slate-400 mb-1">No. Transacción / Ref *</label>
                      <input
                        type="text"
                        required
                        value={pagoNumTransaccion}
                        onChange={(e) => setPagoNumTransaccion(e.target.value)}
                        placeholder="TRF-5918234"
                        className="w-full rounded-lg border border-slate-800 bg-slate-950 px-2.5 py-1.5 text-xs font-mono font-bold text-white focus:border-sky-500 focus:outline-none"
                      />
                    </div>
                  </div>

                  <div>
                    <label className="block text-[10px] font-medium text-slate-400 mb-1">
                      Comprobante de WhatsApp (Captura)
                    </label>
                    <div className="flex items-center gap-2">
                      <input
                        type="file"
                        ref={fileInputRef}
                        accept="image/*"
                        onChange={handleFileUpload}
                        className="hidden"
                      />
                      <button
                        type="button"
                        onClick={() => fileInputRef.current?.click()}
                        className="flex-1 flex items-center justify-center gap-1.5 p-2 rounded-lg border border-slate-700 bg-slate-800 hover:bg-slate-700 text-xs text-slate-200 transition-colors cursor-pointer"
                      >
                        <Upload className="h-3.5 w-3.5 text-sky-400" />
                        {pagoComprobanteNombre ? 'Cambiar Imagen' : 'Subir Imagen de WhatsApp'}
                      </button>
                      <button
                        type="button"
                        onClick={handleGenerateSampleVoucher}
                        className="px-3 py-2 rounded-lg border border-slate-700 bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-slate-300 transition-colors cursor-pointer"
                      >
                        Muestra
                      </button>
                    </div>

                    {pagoComprobanteUrl && (
                      <div className="mt-2 flex items-center justify-between p-2 rounded-lg bg-slate-900 border border-slate-800">
                        <div className="flex items-center gap-2 overflow-hidden">
                          <img src={pagoComprobanteUrl} alt="Comprobante" className="h-8 w-8 rounded object-cover border border-slate-700" />
                          <span className="text-xs text-slate-300 truncate max-w-[200px]">{pagoComprobanteNombre || 'comprobante.png'}</span>
                        </div>
                        <button
                          type="button"
                          onClick={() => {
                            setPagoComprobanteUrl('');
                            setPagoComprobanteNombre('');
                          }}
                          className="text-slate-400 hover:text-rose-400 p-1 transition-colors"
                        >
                          <X className="h-3.5 w-3.5" />
                        </button>
                      </div>
                    )}
                  </div>
                </div>
              ) : (
                <div className="p-3.5 rounded-xl border border-slate-800 bg-slate-950/70 space-y-3">
                  <div>
                    <label className="block text-[10px] font-medium text-slate-400 mb-1">¿Quién Recibe el Dinero? *</label>
                    <input
                      type="text"
                      required
                      value={pagoEfectivoRecibe}
                      onChange={(e) => setPagoEfectivoRecibe(e.target.value)}
                      className="w-full rounded-lg border border-slate-800 bg-slate-950 px-2.5 py-1.5 text-xs text-slate-200 focus:border-sky-500 focus:outline-none"
                    />
                  </div>
                  <div>
                    <label className="block text-[10px] font-medium text-slate-400 mb-1">¿Quién lo Entrega? *</label>
                    <input
                      type="text"
                      required
                      value={pagoEfectivoEntrega}
                      onChange={(e) => setPagoEfectivoEntrega(e.target.value)}
                      className="w-full rounded-lg border border-slate-800 bg-slate-950 px-2.5 py-1.5 text-xs text-slate-200 focus:border-sky-500 focus:outline-none"
                    />
                  </div>
                </div>
              )}

              <div className="flex justify-end gap-2 pt-4 border-t border-slate-800">
                <button
                  type="button"
                  onClick={() => setIsPagoModalOpen(false)}
                  className="rounded-lg border border-slate-700 bg-slate-800 px-4 py-2 text-xs font-semibold text-slate-300 hover:bg-slate-700 hover:text-white transition-colors cursor-pointer"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  className="rounded-lg bg-sky-500 hover:bg-sky-400 text-slate-950 px-5 py-2 text-xs font-bold transition-all shadow-md shadow-sky-500/20 cursor-pointer"
                >
                  Confirmar Cobro
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      {/* 2. MODAL NUEVA SUSCRIPCIÓN */}
      {isSubModalOpen && (
        <div 
          onClick={() => setIsSubModalOpen(false)} 
          className="fixed inset-0 z-50 flex items-center justify-center bg-black/75 backdrop-blur-sm p-4 overflow-y-auto cursor-pointer"
        >
          <div 
            onClick={(e) => e.stopPropagation()} 
            className="relative w-full max-w-md rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-2xl my-8 text-slate-100 cursor-default"
          >
            <div className="flex items-center justify-between border-b border-slate-800 pb-3 mb-5">
              <div className="flex items-center gap-2">
                <div className="rounded-lg bg-sky-500/10 p-2 text-sky-400 border border-sky-500/20">
                  <Radio className="h-5 w-5" />
                </div>
                <div>
                  <h3 className="text-base font-bold text-white">Nueva Suscripción de Gestión Remota</h3>
                  <p className="text-xs text-slate-400">Póliza mensual recurrente</p>
                </div>
              </div>
              <button 
                onClick={() => setIsSubModalOpen(false)} 
                className="text-slate-400 hover:text-white p-1 rounded-lg transition-colors cursor-pointer"
              >
                <X className="h-5 w-5" />
              </button>
            </div>

            <form onSubmit={handleSaveSuscripcion} className="space-y-4">
              <div>
                <label className="block text-[11px] font-medium text-slate-400 mb-1">Cliente *</label>
                <select
                  value={subClienteId}
                  onChange={(e) => setSubClienteId(e.target.value)}
                  className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-sky-500 focus:outline-none"
                >
                  {clientes.map(c => (
                    <option key={c.id} value={c.id}>{c.nombre} ({c.codigo})</option>
                  ))}
                </select>
              </div>

              <div>
                <label className="block text-[11px] font-medium text-slate-400 mb-1">Nombre del Servicio *</label>
                <input
                  type="text"
                  required
                  value={subNombreServicio}
                  onChange={(e) => setSubNombreServicio(e.target.value)}
                  className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-sky-500 focus:outline-none"
                />
              </div>

              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">Router / Solución</label>
                  <input
                    type="text"
                    value={subTipoSolucion}
                    onChange={(e) => setSubTipoSolucion(e.target.value)}
                    placeholder="Router 4G Huawei B310"
                    className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-sky-500 focus:outline-none"
                  />
                </div>
                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">No. SIM Asignada</label>
                  <input
                    type="text"
                    value={subSimNumero}
                    onChange={(e) => setSubSimNumero(e.target.value)}
                    placeholder="5352849102"
                    className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs font-mono text-slate-200 focus:border-sky-500 focus:outline-none"
                  />
                </div>
              </div>

              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">Moneda *</label>
                  <select
                    value={subMoneda}
                    onChange={(e) => setSubMoneda(e.target.value as Moneda)}
                    className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-sky-500 focus:outline-none"
                  >
                    <option value="CUP">CUP</option>
                    <option value="USD">USD</option>
                  </select>
                </div>
                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">Tarifa Mensual *</label>
                  <input
                    type="number"
                    required
                    value={subTarifa}
                    onChange={(e) => setSubTarifa(Number(e.target.value))}
                    className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs font-mono font-bold text-white focus:border-sky-500 focus:outline-none"
                  />
                </div>
              </div>

              <div className="flex justify-end gap-2 pt-4 border-t border-slate-800">
                <button
                  type="button"
                  onClick={() => setIsSubModalOpen(false)}
                  className="rounded-lg border border-slate-700 bg-slate-800 px-4 py-2 text-xs font-semibold text-slate-300 hover:bg-slate-700 hover:text-white transition-colors cursor-pointer"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  className="rounded-lg bg-sky-500 hover:bg-sky-400 text-slate-950 px-5 py-2 text-xs font-bold transition-all shadow-md shadow-sky-500/20 cursor-pointer"
                >
                  Guardar Suscripción
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      {/* 3. MODAL VISOR DE RECIBO DIGITAL OFICIAL */}
      {reciboModalData && (
        <div 
          onClick={() => setReciboModalData(null)} 
          className="fixed inset-0 z-50 flex items-center justify-center bg-black/75 backdrop-blur-sm p-4 overflow-y-auto cursor-pointer"
        >
          <div 
            onClick={(e) => e.stopPropagation()} 
            className="relative w-full max-w-md rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-2xl my-8 text-slate-100 space-y-4 cursor-default"
          >
            <div className="flex items-center justify-between border-b border-slate-800 pb-3">
              <div className="flex items-center gap-2">
                <div className="rounded-lg bg-teal-500/10 p-2 text-teal-400 border border-teal-500/20">
                  <Receipt className="h-5 w-5" />
                </div>
                <div>
                  <h4 className="text-base font-bold text-white">Recibo Digital de Pago</h4>
                  <p className="text-[11px] text-slate-400 font-mono">DataPlus CUBA · {reciboModalData.codigo_pago}</p>
                </div>
              </div>
              <button 
                onClick={() => setReciboModalData(null)} 
                className="text-slate-400 hover:text-white p-1 rounded-lg transition-colors cursor-pointer"
              >
                <X className="h-5 w-5" />
              </button>
            </div>

            <div className="p-4 rounded-xl bg-slate-950 border border-slate-800 text-xs space-y-3">
              <div className="text-center border-b border-slate-800 pb-2">
                <p className="font-extrabold text-white text-sm tracking-wider">DATAPLUS PLATAFORMA</p>
                <p className="text-[10px] text-slate-400">Servicio de Gestión Remota & Enlaces 4G</p>
                <span className="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                  COBRO VERIFICADO Y CONCILIADO
                </span>
              </div>

              <div className="space-y-1.5 text-[11px]">
                <div className="flex justify-between">
                  <span className="text-slate-400">Cliente:</span>
                  <span className="font-bold text-white">{reciboModalData.cliente_nombre}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-slate-400">Periodo liquidado:</span>
                  <span className="text-slate-200">{reciboModalData.mes_nombre_legible}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-slate-400">Fecha de Pago:</span>
                  <span className="text-slate-200">{reciboModalData.fecha_pago}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-slate-400">Método:</span>
                  <span className="text-slate-200">{reciboModalData.metodo_pago}</span>
                </div>
                {reciboModalData.numero_transaccion && (
                  <div className="flex justify-between">
                    <span className="text-slate-400">Referencia:</span>
                    <span className="font-mono text-sky-400">{reciboModalData.numero_transaccion}</span>
                  </div>
                )}
                <div className="flex justify-between border-t border-slate-800 pt-2 text-xs">
                  <span className="font-bold text-white">Monto Abonado:</span>
                  <span className="font-extrabold text-emerald-400 font-mono text-sm">
                    {reciboModalData.moneda === 'USD' 
                      ? `$${reciboModalData.monto_pagado}.00 USD` 
                      : `$${reciboModalData.monto_pagado.toLocaleString()}.00 CUP`}
                  </span>
                </div>
              </div>
            </div>

            <div className="flex justify-between items-center pt-2">
              <button
                type="button"
                onClick={() => handleCopyReceiptText(reciboModalData)}
                className="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-700 bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-slate-200 transition-colors cursor-pointer"
              >
                {copiedReceipt ? <Check className="h-3.5 w-3.5 text-emerald-400" /> : <Copy className="h-3.5 w-3.5" />}
                <span>{copiedReceipt ? 'Copiado al Portapapeles' : 'Copiar Texto'}</span>
              </button>

              <button
                onClick={() => setReciboModalData(null)}
                className="px-4 py-1.5 rounded-lg bg-sky-500 hover:bg-sky-400 text-slate-950 font-bold text-xs transition-colors cursor-pointer"
              >
                Cerrar
              </button>
            </div>
          </div>
        </div>
      )}

      {/* 4. MODAL VISOR DE COMPROBANTE WHATSAPP */}
      {previewVoucherUrl && (
        <div 
          onClick={() => setPreviewVoucherUrl(null)} 
          className="fixed inset-0 z-50 flex items-center justify-center bg-black/75 backdrop-blur-sm p-4 overflow-y-auto cursor-pointer"
        >
          <div 
            onClick={(e) => e.stopPropagation()} 
            className="relative w-full max-w-md rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-2xl my-8 text-slate-100 space-y-4 cursor-default"
          >
            <div className="flex items-center justify-between border-b border-slate-800 pb-3">
              <div className="flex items-center gap-2">
                <div className="rounded-lg bg-emerald-500/10 p-2 text-emerald-400 border border-emerald-500/20">
                  <ImageIcon className="h-5 w-5" />
                </div>
                <div>
                  <h4 className="text-base font-bold text-white">Comprobante de WhatsApp</h4>
                  <p className="text-xs text-slate-400">{previewVoucherUrl.cliente} · {previewVoucherUrl.codigo}</p>
                </div>
              </div>
              <button 
                onClick={() => setPreviewVoucherUrl(null)} 
                className="text-slate-400 hover:text-white p-1 rounded-lg transition-colors cursor-pointer"
              >
                <X className="h-5 w-5" />
              </button>
            </div>

            <div className="rounded-xl overflow-hidden border border-slate-800 bg-slate-950 p-2 flex items-center justify-center">
              <img 
                src={previewVoucherUrl.url} 
                alt="Comprobante Transferencia" 
                className="max-h-[380px] w-auto rounded-lg object-contain shadow-lg"
              />
            </div>

            <div className="flex justify-between items-center pt-2">
              <a
                href={previewVoucherUrl.url}
                download={`comprobante_${previewVoucherUrl.codigo.toLowerCase()}.svg`}
                className="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-700 bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-slate-200 transition-colors cursor-pointer"
              >
                <Download className="h-3.5 w-3.5 text-sky-400" />
                <span>Descargar</span>
              </a>

              <button
                onClick={() => setPreviewVoucherUrl(null)}
                className="px-4 py-1.5 rounded-lg bg-sky-500 hover:bg-sky-400 text-slate-950 font-bold text-xs transition-colors cursor-pointer"
              >
                Cerrar
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};
