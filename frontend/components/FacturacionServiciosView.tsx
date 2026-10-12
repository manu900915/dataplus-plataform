import React, { useState, useMemo, useRef, useEffect } from 'react';
import { 
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
  Radio,
  FileText
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
  // Pestañas principales idénticas a la imagen:
  // 1: Control de Cobros Mensuales
  // 2: Pagaron esta Semana
  // 3: Mayores Deudores
  // 4: Reporte Temporal (Año/Sem/Trim/Mes)
  const [activeTab, setActiveTab] = useState<'cobros' | 'semana' | 'deudores' | 'reporte'>('cobros');
  
  // Filtros
  const [searchTerm, setSearchTerm] = useState('');
  const [filtroMoneda, setFiltroMoneda] = useState<'Todas' | 'CUP' | 'USD'>('Todas');
  const [filtroEstado, setFiltroEstado] = useState<'Todos' | 'Cobrados' | 'Pendientes' | 'En Mora'>('Todos');

  // Modales
  const [isPagoModalOpen, setIsPagoModalOpen] = useState(false);
  const [isSubModalOpen, setIsSubModalOpen] = useState(false);
  const [previewVoucherUrl, setPreviewVoucherUrl] = useState<{ url: string; cliente: string; codigo: string; op?: string } | null>(null);
  const [reciboModalData, setReciboModalData] = useState<RegistroPagoServicio | null>(null);
  const [copiedReceipt, setCopiedReceipt] = useState(false);

  // Formulario de Pago
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

  // Filtro temporal del Reporte
  const [periodoReporte, setPeriodoReporte] = useState<'mensual' | 'trimestral' | 'semestral' | 'anual'>('mensual');

  const fileInputRef = useRef<HTMLInputElement>(null);

  // Parámetros de tiempo exactos según la imagen:
  // "Día del mes actual: 11 / 15" y "Plazo en Curso: 4 días restantes"
  const diaActual = 11;
  const diasLimite = 15;
  const diasRestantes = Math.max(0, diasLimite - diaActual); // 4

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

  // Suscripción seleccionada para pagar
  const selectedSub = useMemo(() => {
    return suscripciones.find(s => s.id === pagoSubId) || suscripciones[0];
  }, [pagoSubId, suscripciones]);

  // Selección de suscripción para cobrar
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

  // Ver recibo de un cliente pagado
  const handleVerRecibo = (sub: SuscripcionServicio) => {
    const pagoExistente = pagos.find(p => p.suscripcion_id === sub.id || p.cliente_nombre === sub.cliente_nombre);
    if (pagoExistente) {
      setReciboModalData(pagoExistente);
    } else {
      // Crear objeto de recibo verificado al vuelo
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

  // Carga de archivo de comprobante
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

  // Generar muestra de comprobante si no tiene captura
  const handleGenerateSampleVoucher = () => {
    if (!selectedSub) return;
    const op = pagoNumTransaccion || `TRF-${Math.floor(1000000 + Math.random() * 9000000)}`;
    const montoFormatted = pagoMoneda === 'USD' ? `$${pagoMonto}.00 USD` : `$${pagoMonto.toLocaleString()}.00 CUP`;
    const vUrl = createSampleVoucher(op, montoFormatted, pagoFecha, selectedSub.cliente_nombre, pagoPlataforma);
    setPagoComprobanteUrl(vUrl);
    setPagoComprobanteNombre(`comprobante_whatsapp_${selectedSub.cliente_nombre.toLowerCase().replace(/\s+/g, '_')}.png`);
  };

  // Guardar cobro
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

    // Actualizar suscripción
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

  // Crear suscripción
  const handleSaveSuscripcion = (e: React.FormEvent) => {
    e.preventDefault();
    const cli = clientes.find(c => c.id === subClienteId);
    const cliNombre = cli ? cli.nombre : 'Cliente DataPlus';
    const cliTel = cli?.telefono || '+53 5 281 9021';

    onAddSuscripcion({
      cliente_id: subClienteId || `cli-${Date.now()}`,
      cliente_nombre: cliNombre,
      cliente_telefono: cliTel,
      nombre_servicio: subNombreServicio,
      tipo_solucion: subTipoSolucion,
      sim_numero: subSimNumero,
      moneda: subMoneda,
      tarifa_mensual: subTarifa,
      dia_limite_pago: 15,
      activo: true,
      fecha_inicio: new Date().toISOString().split('T')[0],
      notas: subNotas,
      tiene_deuda: false,
      meses_deuda_count: 0,
      meses_deuda_detalle: [],
      deuda_acumulada: 0,
      monto_a_cobrar: subTarifa,
      estado_cobro_mes_actual: 'Pendiente'
    });
    setIsSubModalOpen(false);
  };

  // Enviar mensaje de WhatsApp
  const handleOpenWhatsAppReminder = (sub: SuscripcionServicio) => {
    const telefonoLimpio = (sub.cliente_telefono || '').replace(/[^\d]/g, '');
    const mesesTexto = sub.meses_deuda_detalle.length > 0 ? sub.meses_deuda_detalle.join(', ') : 'Octubre 2026';
    const montoTexto = sub.moneda === 'USD' ? `$${sub.monto_a_cobrar} USD` : `$${sub.monto_a_cobrar.toLocaleString()} CUP`;
    
    const mensaje = encodeURIComponent(
      `Hola ${sub.cliente_nombre}, le contactamos de DataPlus para recordarle el pago de su servicio mensual de ${sub.nombre_servicio}. ` +
      (sub.tiene_deuda 
        ? `Actualmente presenta ${sub.meses_deuda_count} mes(es) pendiente(s) (${mesesTexto}). El monto total a liquidar con deuda acumulada es de ${montoTexto}. `
        : `Le recordamos que el pago correspondiente a mes adelantado es de ${montoTexto} y debe efectuarse durante los primeros 15 días del mes. `) +
      `Por favor, recuerde enviar el comprobante de su transferencia a este chat una vez realizada. ¡Muchas gracias!`
    );
    window.open(`https://wa.me/${telefonoLimpio}?text=${mensaje}`, '_blank');
  };

  // Copiar recibo
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

  // Listas según pestaña
  const subsSemana = useMemo(() => {
    return suscripciones.filter(s => s.estado_cobro_mes_actual === 'Al_Dia' || s.ultimo_pago_fecha?.startsWith('2026-10'));
  }, [suscripciones]);

  const subsDeudores = useMemo(() => {
    return suscripciones.filter(s => s.tiene_deuda || s.estado_cobro_mes_actual === 'En_Mora');
  }, [suscripciones]);

  // Lista base según pestaña activa
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

  // Cálculos para la evolución temporal y gráfica del Reporte
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
      { key: '10', nombre: 'Octubre 2026', q: 'Q4', s: 'S2', cup: 7700, usd: 0, expCup: 24500 },
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
        totalOperaciones: m.cup > 0 ? (m.key === '10' ? 2 : 5) : 0
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
  }, []);

  return (
    <div className="space-y-6 font-sans text-white select-none">
      {/* ================= TÍTULO PRINCIPAL (IDÉNTICO A IMAGEN 1) ================= */}
      <div>
        <h1 className="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">
          Facturación de Gestión Remota & Servicios
        </h1>
      </div>

      {/* ================= BANNER RECTANGULAR PRINCIPAL (IDÉNTICO A IMAGEN 1) ================= */}
      <div className="rounded-xl border border-white/20 bg-black p-4 sm:p-5 flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4">
        {/* Lado izquierdo: Periodo y norma operativa */}
        <div className="flex items-center gap-4">
          <div className="h-10 w-10 rounded-full border border-white/20 flex items-center justify-center shrink-0 text-white">
            <Calendar className="h-5 w-5 stroke-[1.5]" />
          </div>
          <div>
            <div className="flex items-center gap-2.5">
              <span className="text-sm sm:text-base font-bold text-white tracking-tight">
                Periodo de Cobro: Octubre 2026
              </span>
              <span className="px-2.5 py-0.5 rounded-full border border-white/30 text-[11px] font-medium text-white">
                Mes Adelantado
              </span>
            </div>
            <p className="text-xs text-white/70 mt-1">
              Norma operativa: El servicio de Gestión Remota se liquida en los <strong className="text-white font-bold">primeros 15 días</strong> del mes en curso.
            </p>
          </div>
        </div>

        {/* Lado derecho: Tarjetas redondeadas + Botón Nueva Suscripción */}
        <div className="flex flex-wrap items-center gap-3 shrink-0">
          {/* Tarjeta: Día del mes actual */}
          <div className="rounded-xl border border-white/20 px-4 py-2.5 bg-black min-w-[130px] text-center">
            <div className="text-xs font-semibold text-white/90">Día del mes actual</div>
            <div className="text-sm font-bold text-white mt-0.5 font-mono">
              <span className="text-lg font-extrabold text-white">{diaActual}</span>
              <span className="text-white/60 text-xs font-normal"> / {diasLimite}</span>
            </div>
          </div>

          {/* Tarjeta: Plazo en Curso */}
          <div className="rounded-xl border border-white/20 px-4 py-2.5 bg-black min-w-[140px] text-center">
            <div className="text-xs font-semibold text-white/90">Plazo en Curso</div>
            <div className="text-xs font-mono font-bold text-white mt-1">
              {diasRestantes} días restantes
            </div>
          </div>

          {/* Botón: + Nueva Suscripción */}
          <button
            onClick={() => {
              setSubClienteId(clientes[0]?.id || '');
              setIsSubModalOpen(true);
            }}
            className="flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-white hover:text-white/80 transition-colors cursor-pointer whitespace-nowrap ml-1"
          >
            <span className="text-sm font-light">+</span>
            <span>Nueva Suscripción</span>
          </button>
        </div>
      </div>

      {/* ================= PESTAÑAS TIPO PÍLDORA (IDÉNTICO A IMAGEN 2) ================= */}
      <div className="flex flex-wrap items-center gap-3">
        {/* Tab 1: Control de Cobros Mensuales */}
        <button
          onClick={() => setActiveTab('cobros')}
          className={`px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer ${
            activeTab === 'cobros'
              ? 'border border-white/30 bg-black text-white shadow-sm'
              : 'text-white/70 hover:text-white border border-transparent'
          }`}
        >
          <div className="grid grid-cols-2 gap-0.5 w-3.5 h-3.5 border border-current p-0.5 rounded-sm">
            <div className="bg-current rounded-[1px]"></div>
            <div className="bg-current rounded-[1px]"></div>
            <div className="bg-current rounded-[1px]"></div>
            <div className="bg-current rounded-[1px]"></div>
          </div>
          <span>Control de Cobros Mensuales</span>
          <span className="ml-1 text-xs font-mono">{suscripciones.length}</span>
        </button>

        {/* Tab 2: Pagaron esta Semana */}
        <button
          onClick={() => setActiveTab('semana')}
          className={`px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer ${
            activeTab === 'semana'
              ? 'border border-white/30 bg-black text-white shadow-sm'
              : 'text-white/70 hover:text-white border border-transparent'
          }`}
        >
          <div className="h-3.5 w-3.5 rounded-full border border-current flex items-center justify-center text-[9px] font-bold">
            ✓
          </div>
          <span>Pagaron esta Semana</span>
          <span className="ml-1 text-xs font-mono">{subsSemana.length}</span>
        </button>

        {/* Tab 3: Mayores Deudores */}
        <button
          onClick={() => setActiveTab('deudores')}
          className={`px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer ${
            activeTab === 'deudores'
              ? 'border border-white/30 bg-black text-white shadow-sm'
              : 'text-white/70 hover:text-white border border-transparent'
          }`}
        >
          <span className="text-xs">⚠️</span>
          <span>Mayores Deudores</span>
          <span className="ml-1 text-xs font-mono">{subsDeudores.length}</span>
        </button>

        {/* Tab 4: Reporte Temporal (Año/Sem/Trim/Mes) */}
        <button
          onClick={() => setActiveTab('reporte')}
          className={`px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer ${
            activeTab === 'reporte'
              ? 'border border-white/30 bg-black text-white shadow-sm'
              : 'text-white/70 hover:text-white border border-transparent'
          }`}
        >
          <TrendingUp className="h-3.5 w-3.5" />
          <span>Reporte Temporal (Año/Sem/Trim/Mes)</span>
        </button>
      </div>

      {/* ================= CONTENIDO DE LAS PESTAÑAS ================= */}

      {/* PESTAÑAS 1, 2 Y 3: TABLA DE GESTIÓN DE SUSCRIPCIONES (IDÉNTICA A IMAGEN 2) */}
      {activeTab !== 'reporte' && (
        <div className="space-y-4">
          {/* BARRA DE FILTROS (IDÉNTICA A IMAGEN 2) */}
          <div className="flex flex-col md:flex-row gap-3 items-stretch md:items-center">
            {/* Buscador */}
            <div className="flex-1">
              <input
                type="text"
                placeholder="Buscar por cliente, código, SIM..."
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
                className="w-full rounded-xl border border-white/20 bg-black px-4 py-2 text-xs text-white placeholder:text-white/50 focus:border-white focus:outline-none"
              />
            </div>

            {/* Filtro Moneda */}
            <div className="flex items-center rounded-xl border border-white/20 bg-black px-3 py-1.5 text-xs">
              <span className="text-white/70 mr-2">Moneda:</span>
              <div className="flex items-center gap-2 font-medium">
                {(['Todas', 'CUP', 'USD'] as const).map((m) => (
                  <button
                    key={m}
                    onClick={() => setFiltroMoneda(m)}
                    className={`px-2 py-0.5 rounded cursor-pointer transition-colors ${
                      filtroMoneda === m ? 'text-white font-bold bg-white/10' : 'text-white/70 hover:text-white'
                    }`}
                  >
                    {m}
                  </button>
                ))}
              </div>
            </div>

            {/* Filtro Estado */}
            <div className="flex items-center rounded-xl border border-white/20 bg-black px-3 py-1.5 text-xs">
              <span className="text-white/70 mr-2">Estado:</span>
              <div className="flex items-center gap-2 font-medium">
                {(['Todos', 'Cobrados', 'Pendientes', 'En Mora'] as const).map((est) => (
                  <button
                    key={est}
                    onClick={() => setFiltroEstado(est)}
                    className={`px-2 py-0.5 rounded cursor-pointer transition-colors ${
                      filtroEstado === est ? 'text-white font-bold bg-white/10' : 'text-white/70 hover:text-white'
                    }`}
                  >
                    {est}
                  </button>
                ))}
              </div>
            </div>
          </div>

          {/* TABLA PRINCIPAL (IDÉNTICA A IMAGEN 2) */}
          <div className="overflow-hidden rounded-xl border border-white/20 bg-black">
            <div className="overflow-x-auto">
              <table className="w-full text-left text-xs text-white">
                <thead className="border-b border-white/20 bg-black text-[11px] font-semibold text-white">
                  <tr>
                    <th className="py-3 px-4">Código</th>
                    <th className="py-3 px-4">Cliente / Teléfono</th>
                    <th className="py-3 px-4">Solución / SIM</th>
                    <th className="py-3 px-4">Mensualidad</th>
                    <th className="py-3 px-4">Deuda Anterior</th>
                    <th className="py-3 px-4">Total a Cobrar</th>
                    <th className="py-3 px-4 text-center">Estado</th>
                    <th className="py-3 px-4 text-center">Acciones</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-white/15 bg-black">
                  {filteredSubs.length === 0 ? (
                    <tr>
                      <td colSpan={8} className="py-8 text-center text-white/50">
                        No se encontraron registros con los filtros seleccionados.
                      </td>
                    </tr>
                  ) : (
                    filteredSubs.map((sub) => {
                      const isAlDia = sub.estado_cobro_mes_actual === 'Al_Dia';
                      const isPendiente = sub.estado_cobro_mes_actual === 'Pendiente';
                      const isEnMora = sub.estado_cobro_mes_actual === 'En_Mora' || sub.tiene_deuda;

                      return (
                        <tr key={sub.id} className="hover:bg-white/[0.03] transition-colors">
                          {/* Código */}
                          <td className="py-3.5 px-4 font-mono font-semibold text-white whitespace-nowrap">
                            {sub.codigo}
                          </td>

                          {/* Cliente / Teléfono */}
                          <td className="py-3.5 px-4">
                            <div className="font-semibold text-white">{sub.cliente_nombre}</div>
                            {sub.cliente_telefono && (
                              <div className="text-[11px] text-white/70 font-mono mt-0.5 flex items-center gap-1">
                                <Phone className="h-3 w-3 inline text-white/60" />
                                <span>{sub.cliente_telefono}</span>
                              </div>
                            )}
                          </td>

                          {/* Solución / SIM */}
                          <td className="py-3.5 px-4">
                            <div className="text-white text-xs">{sub.tipo_solucion || sub.nombre_servicio}</div>
                            <div className="text-[11px] text-white/70 font-mono mt-0.5">
                              SIM: {sub.sim_numero || 'Sin asignar'}
                            </div>
                          </td>

                          {/* Mensualidad */}
                          <td className="py-3.5 px-4 font-mono font-semibold text-white whitespace-nowrap">
                            {sub.moneda === 'USD' 
                              ? `$${sub.tarifa_mensual}.00 USD` 
                              : `$${sub.tarifa_mensual.toLocaleString()}.00 CUP`}
                          </td>

                          {/* Deuda Anterior */}
                          <td className="py-3.5 px-4 whitespace-nowrap">
                            {sub.tiene_deuda && sub.deuda_acumulada > 0 ? (
                              <div>
                                <span className="font-mono font-semibold text-white">
                                  {sub.moneda === 'USD'
                                    ? `+$${sub.deuda_acumulada}.00`
                                    : `+$${sub.deuda_acumulada.toLocaleString()}.00`}
                                </span>
                                <span className="block text-[10px] text-white/70">
                                  ({sub.meses_deuda_count} {sub.meses_deuda_count === 1 ? 'mes' : 'meses'})
                                </span>
                              </div>
                            ) : (
                              <span className="font-mono text-white/80">$0.00</span>
                            )}
                          </td>

                          {/* Total a Cobrar */}
                          <td className="py-3.5 px-4 font-mono font-bold text-white whitespace-nowrap">
                            {sub.moneda === 'USD'
                              ? `$${sub.monto_a_cobrar}.00 USD`
                              : `$${sub.monto_a_cobrar.toLocaleString()}.00 CUP`}
                          </td>

                          {/* Estado */}
                          <td className="py-3.5 px-4 text-center whitespace-nowrap">
                            {isEnMora && (
                              <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border border-white/30 text-white bg-black">
                                <span className="flex h-3.5 w-3.5 items-center justify-center rounded-full border border-white/60 text-[9px] font-bold">!</span>
                                <span>En Mora</span>
                              </span>
                            )}

                            {isAlDia && (
                              <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border border-white/30 text-white bg-black">
                                <span className="flex h-3.5 w-3.5 items-center justify-center rounded-full border border-white/60 text-[9px] font-bold">✓</span>
                                <span>Cobrado</span>
                              </span>
                            )}

                            {isPendiente && (
                              <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border border-white/30 text-white bg-black">
                                <Clock className="h-3 w-3 text-white/80" />
                                <span>Pendiente (Día 1-15)</span>
                              </span>
                            )}
                          </td>

                          {/* Acciones */}
                          <td className="py-3.5 px-4 text-center whitespace-nowrap">
                            <div className="flex items-center justify-center gap-2">
                              {/* Botón Cobrar o Recibo */}
                              {isAlDia ? (
                                <button
                                  onClick={() => handleVerRecibo(sub)}
                                  className="flex items-center gap-1 px-3 py-1 rounded-lg border border-white/20 bg-black text-xs font-semibold text-white hover:bg-white/10 transition-colors cursor-pointer"
                                >
                                  <Receipt className="h-3.5 w-3.5" />
                                  <span>Recibo</span>
                                </button>
                              ) : (
                                <button
                                  onClick={() => handleSelectSubForPago(sub)}
                                  className="flex items-center gap-1 px-3 py-1 rounded-lg border border-white/20 bg-black text-xs font-semibold text-white hover:bg-white/10 transition-colors cursor-pointer"
                                >
                                  <CreditCard className="h-3.5 w-3.5" />
                                  <span>Cobrar</span>
                                </button>
                              )}

                              {/* Botón WhatsApp */}
                              <button
                                onClick={() => handleOpenWhatsAppReminder(sub)}
                                title="Enviar recordatorio por WhatsApp"
                                className="p-1.5 rounded-lg border border-white/20 bg-black text-white hover:bg-white/10 transition-colors cursor-pointer"
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
      )}

      {/* ================= PESTAÑA 4: REPORTE TEMPORAL (AÑO / SEM / TRIM / MES) ================= */}
      {activeTab === 'reporte' && (
        <div className="rounded-xl border border-white/20 bg-black p-5 sm:p-6 space-y-6">
          {/* Header del Reporte */}
          <div className="flex flex-col sm:flex-row justify-between sm:items-center gap-4 border-b border-white/15 pb-4">
            <div>
              <h3 className="text-base font-bold text-white flex items-center gap-2">
                <TrendingUp className="h-5 w-5 text-white" />
                Evolución Temporal & Comparativa de Recaudación (2026)
              </h3>
              <p className="text-xs text-white/70 mt-0.5">
                Desglose analítico de cobros y efectividad por Año, Semestre, Trimestre y Mes.
              </p>
            </div>

            {/* Selector de Período con botones limpios tipo píldora */}
            <div className="flex items-center p-1 bg-black rounded-xl border border-white/20 text-xs">
              {(['mensual', 'trimestral', 'semestral', 'anual'] as const).map((p) => (
                <button
                  key={p}
                  onClick={() => setPeriodoReporte(p)}
                  className={`px-3.5 py-1.5 rounded-lg font-bold capitalize transition-all cursor-pointer ${
                    periodoReporte === p 
                      ? 'border border-white/30 bg-white/15 text-white shadow-sm' 
                      : 'text-white/60 hover:text-white'
                  }`}
                >
                  {p}
                </button>
              ))}
            </div>
          </div>

          {/* VISTA MENSUAL: GRÁFICO DE LÍNEAS INTERACTIVO (LINE CHART) */}
          {periodoReporte === 'mensual' && (() => {
            const data = reportesPeriodos.datosMensuales;
            const maxVal = 28000;
            const width = 860;
            const height = 240;
            const padX = 50;
            const padY = 30;
            const innerW = width - padX * 2;
            const innerH = height - padY * 2;

            // Coordenadas calculadas para la línea continua
            const points = data.map((d, i) => {
              const x = padX + (i / (data.length - 1)) * innerW;
              const y = height - padY - (d.cupCobrado / maxVal) * innerH;
              return { x, y, ...d };
            });

            // Generador de curva suave SVG
            const makePath = (pts: { x: number; y: number }[]) => {
              if (pts.length === 0) return '';
              let path = `M ${pts[0].x} ${pts[0].y}`;
              for (let i = 0; i < pts.length - 1; i++) {
                const p0 = pts[i];
                const p1 = pts[i + 1];
                const cp1x = p0.x + (p1.x - p0.x) / 2;
                const cp1y = p0.y;
                const cp2x = p0.x + (p1.x - p0.x) / 2;
                const cp2y = p1.y;
                path += ` C ${cp1x} ${cp1y}, ${cp2x} ${cp2y}, ${p1.x} ${p1.y}`;
              }
              return path;
            };

            const linePath = makePath(points);
            const areaPath = `${linePath} L ${points[points.length - 1].x} ${height - padY} L ${points[0].x} ${height - padY} Z`;
            const metaY = height - padY - (24500 / maxVal) * innerH;

            return (
              <div className="space-y-6">
                {/* Visualización gráfica de líneas */}
                <div className="rounded-xl border border-white/15 bg-black p-5 sm:p-6 space-y-4">
                  {/* Leyenda de la gráfica */}
                  <div className="flex flex-wrap items-center justify-between gap-3 text-xs border-b border-white/10 pb-3">
                    <div className="flex items-center gap-6">
                      <div className="flex items-center gap-2">
                        <span className="w-5 h-[3px] bg-white rounded-full"></span>
                        <span className="text-white font-semibold">Recaudación CUP (Línea Continua)</span>
                      </div>
                      <div className="flex items-center gap-2">
                        <span className="w-5 h-[2px] border-b-2 border-dashed border-sky-400"></span>
                        <span className="text-sky-300">Recaudación USD</span>
                      </div>
                      <div className="flex items-center gap-2">
                        <span className="w-5 h-[1.5px] border-b border-dotted border-white/40"></span>
                        <span className="text-white/60">Meta Exigible (~$24,500 CUP)</span>
                      </div>
                    </div>
                    <div className="text-[11px] font-mono text-white/70">
                      Total 2026: <strong className="text-white">${reportesPeriodos.anual.totalAnualCUP.toLocaleString()} CUP</strong>
                    </div>
                  </div>

                  {/* Canvas SVG del Gráfico de Líneas */}
                  <div className="w-full overflow-x-auto">
                    <svg viewBox={`0 0 ${width} ${height}`} className="w-full min-w-[650px] h-64 overflow-visible">
                      <defs>
                        <linearGradient id="cupAreaGradient" x1="0" y1="0" x2="0" y2="1">
                          <stop offset="0%" stopColor="#ffffff" stopOpacity="0.22" />
                          <stop offset="100%" stopColor="#ffffff" stopOpacity="0.00" />
                        </linearGradient>
                      </defs>

                      {/* Líneas guía horizontales de fondo */}
                      {[0, 7000, 14000, 21000, 28000].map((val) => {
                        const y = height - padY - (val / maxVal) * innerH;
                        return (
                          <g key={val}>
                            <line
                              x1={padX}
                              y1={y}
                              x2={width - padX}
                              y2={y}
                              stroke="rgba(255, 255, 255, 0.08)"
                              strokeDasharray="3 3"
                            />
                            <text
                              x={padX - 8}
                              y={y + 3}
                              fill="rgba(255, 255, 255, 0.4)"
                              fontSize="10"
                              fontFamily="monospace"
                              textAnchor="end"
                            >
                              ${val / 1000}k
                            </text>
                          </g>
                        );
                      })}

                      {/* Línea horizontal de Meta mensual */}
                      <line
                        x1={padX}
                        y1={metaY}
                        x2={width - padX}
                        y2={metaY}
                        stroke="rgba(255, 255, 255, 0.35)"
                        strokeDasharray="4 4"
                        strokeWidth="1.5"
                      />
                      <text
                        x={width - padX + 5}
                        y={metaY + 3}
                        fill="rgba(255, 255, 255, 0.5)"
                        fontSize="9"
                        fontFamily="monospace"
                      >
                        Meta
                      </text>

                      {/* Área sombreada bajo la línea de CUP */}
                      <path d={areaPath} fill="url(#cupAreaGradient)" />

                      {/* Línea principal trazada de recaudación CUP */}
                      <path
                        d={linePath}
                        fill="none"
                        stroke="#ffffff"
                        strokeWidth="2.5"
                        strokeLinecap="round"
                        strokeLinejoin="round"
                      />

                      {/* Puntos y etiquetas interactivas de cada mes */}
                      {points.map((p) => {
                        const isOct = p.key === '10';
                        return (
                          <g key={p.key} className="group cursor-pointer">
                            {/* Línea vertical tenue al punto */}
                            <line
                              x1={p.x}
                              y1={p.y}
                              x2={p.x}
                              y2={height - padY}
                              stroke={isOct ? "rgba(255, 255, 255, 0.3)" : "rgba(255, 255, 255, 0.06)"}
                              strokeDasharray="2 2"
                            />

                            {/* Círculo del punto de la línea */}
                            <circle
                              cx={p.x}
                              cy={p.y}
                              r={isOct ? "5.5" : "4"}
                              fill={p.cupCobrado > 0 ? "#ffffff" : "#18181b"}
                              stroke={isOct ? "#38bdf8" : "#ffffff"}
                              strokeWidth={isOct ? "2" : "1.5"}
                              className="transition-all duration-200 group-hover:r-7"
                            />

                            {/* Tooltip flotante SVG */}
                            <g className="opacity-0 group-hover:opacity-100 transition-opacity duration-150 pointer-events-none">
                              <rect
                                x={p.x - 48}
                                y={p.y - 42}
                                width="96"
                                height="34"
                                rx="6"
                                fill="#09090b"
                                stroke="rgba(255, 255, 255, 0.25)"
                                strokeWidth="1"
                              />
                              <text
                                x={p.x}
                                y={p.y - 28}
                                fill="#ffffff"
                                fontSize="10"
                                fontWeight="bold"
                                textAnchor="middle"
                              >
                                {p.nombre.slice(0, 3)}: ${p.cupCobrado.toLocaleString()}
                              </text>
                              <text
                                x={p.x}
                                y={p.y - 14}
                                fill="#a1a1aa"
                                fontSize="9"
                                textAnchor="middle"
                              >
                                {p.cumplimiento}% meta
                              </text>
                            </g>

                            {/* Etiqueta del Mes en el eje X */}
                            <text
                              x={p.x}
                              y={height - padY + 16}
                              fill={isOct ? "#ffffff" : "rgba(255, 255, 255, 0.6)"}
                              fontSize="10"
                              fontWeight={isOct ? "bold" : "normal"}
                              fontFamily="monospace"
                              textAnchor="middle"
                            >
                              {p.nombre.slice(0, 3)}
                            </text>
                          </g>
                        );
                      })}
                    </svg>
                  </div>

                  <div className="flex justify-between items-center text-[11px] text-white/60 px-2 pt-2 border-t border-white/10">
                    <span>Enero 2026</span>
                    <span>Curva de Recaudación Anual · Línea de Tendencia Continua</span>
                    <span>Diciembre 2026</span>
                  </div>
                </div>

                {/* Tabla de desglose */}
                <div className="overflow-x-auto rounded-xl border border-white/15 bg-black">
                  <table className="w-full text-left text-xs text-white">
                    <thead className="bg-black text-[10px] font-semibold text-white/70 uppercase tracking-wider border-b border-white/15">
                      <tr>
                        <th className="py-2.5 px-4">Mes</th>
                        <th className="py-2.5 px-4">Cobrado CUP</th>
                        <th className="py-2.5 px-4">Cobrado USD</th>
                        <th className="py-2.5 px-4">Operaciones</th>
                        <th className="py-2.5 px-4">Cumplimiento</th>
                      </tr>
                    </thead>
                    <tbody className="divide-y divide-white/10">
                      {reportesPeriodos.datosMensuales.map((m) => (
                        <tr key={m.key} className="hover:bg-white/[0.02]">
                          <td className="py-2.5 px-4 font-semibold text-white">{m.nombre}</td>
                          <td className="py-2.5 px-4 font-mono font-bold text-white">
                            ${m.cupCobrado.toLocaleString()} CUP
                          </td>
                          <td className="py-2.5 px-4 font-mono font-bold text-white">
                            ${m.usdCobrado} USD
                          </td>
                          <td className="py-2.5 px-4 text-white/70">{m.totalOperaciones} pagos</td>
                          <td className="py-2.5 px-4">
                            <div className="flex items-center gap-2">
                              <div className="w-24 bg-white/10 rounded-full h-1.5 overflow-hidden">
                                <div 
                                  className="bg-white h-full rounded-full"
                                  style={{ width: `${m.cumplimiento}%` }}
                                ></div>
                              </div>
                              <span className="text-[11px] font-mono text-white/80">{m.cumplimiento}%</span>
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
                <div key={t.id} className="p-4 rounded-xl border border-white/20 bg-black space-y-3">
                  <div className="flex justify-between items-center">
                    <span className="font-mono text-xs font-bold text-white border border-white/30 px-2 py-0.5 rounded">{t.id}</span>
                    <span className="text-xs font-mono font-bold text-white">{t.cumplimiento}%</span>
                  </div>
                  <h4 className="text-xs font-bold text-white">{t.nombre}</h4>
                  <div className="w-full bg-white/10 h-1.5 rounded-full overflow-hidden">
                    <div className="bg-white h-full rounded-full" style={{ width: `${t.cumplimiento}%` }}></div>
                  </div>
                  <div className="grid grid-cols-2 gap-2 pt-2 border-t border-white/15 text-xs">
                    <div>
                      <p className="text-[10px] text-white/60">CUP:</p>
                      <p className="text-xs font-bold font-mono text-white">${t.cupCobrado.toLocaleString()}</p>
                    </div>
                    <div>
                      <p className="text-[10px] text-white/60">USD:</p>
                      <p className="text-xs font-bold font-mono text-white">${t.usdCobrado}</p>
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
                <div key={s.id} className="p-5 rounded-xl border border-white/20 bg-black space-y-4">
                  <div className="flex justify-between items-center">
                    <span className="font-mono text-xs font-bold border border-white/30 px-2 py-0.5 rounded">{s.id}</span>
                    <span className="text-xs font-mono font-bold text-white">{s.cumplimiento}% efectividad</span>
                  </div>
                  <h4 className="text-sm font-bold text-white">{s.nombre}</h4>
                  <div className="w-full bg-white/10 h-2 rounded-full overflow-hidden">
                    <div className="bg-white h-full rounded-full" style={{ width: `${s.cumplimiento}%` }}></div>
                  </div>
                  <div className="space-y-2 pt-2 border-t border-white/15 text-xs">
                    <div className="flex justify-between text-white/70">
                      <span>Total CUP Cobrado:</span>
                      <span className="font-mono font-bold text-white">${s.cupCobrado.toLocaleString()} CUP</span>
                    </div>
                    <div className="flex justify-between text-white/70">
                      <span>Total USD Cobrado:</span>
                      <span className="font-mono font-bold text-white">${s.usdCobrado} USD</span>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          )}

          {/* VISTA ANUAL */}
          {periodoReporte === 'anual' && (
            <div className="p-6 rounded-xl border border-white/20 bg-black space-y-5">
              <div className="flex justify-between items-center">
                <h4 className="text-base font-bold text-white">Consolidado Anual Ejercicio Fiscal 2026</h4>
                <span className="font-mono text-xs font-bold border border-white/30 px-3 py-1 rounded-full">
                  {reportesPeriodos.anual.cumplimientoGlobal}% Cumplimiento Anual
                </span>
              </div>
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-white/15">
                <div className="p-4 rounded-xl border border-white/20 bg-black">
                  <p className="text-xs text-white/70 font-semibold">Total Cobrado en CUP</p>
                  <p className="text-2xl font-extrabold text-white font-mono mt-1">
                    ${reportesPeriodos.anual.totalAnualCUP.toLocaleString()} CUP
                  </p>
                </div>
                <div className="p-4 rounded-xl border border-white/20 bg-black">
                  <p className="text-xs text-white/70 font-semibold">Total Cobrado en USD</p>
                  <p className="text-2xl font-extrabold text-white font-mono mt-1">
                    ${reportesPeriodos.anual.totalAnualUSD} USD
                  </p>
                </div>
              </div>
            </div>
          )}
        </div>
      )}

      {/* ================= MODALES FLOTANTES CON CIERRE SEGURO (CLICK AFUERA / ESCAPE) ================= */}

      {/* 1. MODAL REGISTRAR COBRO */}
      {isPagoModalOpen && selectedSub && (
        <div 
          onClick={() => setIsPagoModalOpen(false)} 
          className="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4 overflow-y-auto"
        >
          <div 
            onClick={(e) => e.stopPropagation()} 
            className="relative w-full max-w-lg rounded-2xl border border-white/20 bg-zinc-950 p-6 shadow-2xl my-8 text-white"
          >
            <div className="flex items-center justify-between border-b border-white/15 pb-3 mb-4">
              <div className="flex items-center gap-2">
                <CreditCard className="h-5 w-5 text-white" />
                <div>
                  <h3 className="text-sm font-bold text-white">Registrar Cobro de Mensualidad</h3>
                  <p className="text-[11px] text-white/70">Gestión Remota · Mes Adelantado (1-15 días)</p>
                </div>
              </div>
              <button 
                onClick={() => setIsPagoModalOpen(false)} 
                className="text-white/60 hover:text-white p-1 rounded-lg cursor-pointer"
              >
                <X className="h-4 w-4" />
              </button>
            </div>

            <form onSubmit={handleSavePago} className="space-y-4">
              <div>
                <label className="block text-[11px] font-medium text-white/70 mb-1">Cliente / Servicio *</label>
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
                  className="w-full rounded-xl border border-white/20 bg-black px-3 py-2 text-xs text-white focus:border-white focus:outline-none"
                >
                  {suscripciones.map(s => (
                    <option key={s.id} value={s.id}>
                      {s.cliente_nombre} ({s.codigo}) · Tarifa: {s.moneda === 'USD' ? `$${s.tarifa_mensual} USD` : `$${s.tarifa_mensual.toLocaleString()} CUP`}
                    </option>
                  ))}
                </select>
              </div>

              <div className="p-3.5 rounded-xl border border-white/15 bg-black space-y-2 text-xs">
                <div className="flex justify-between text-white/70">
                  <span>Mensualidad base:</span>
                  <span className="font-mono text-white font-bold">
                    {selectedSub.moneda === 'USD' ? `$${selectedSub.tarifa_mensual} USD` : `$${selectedSub.tarifa_mensual.toLocaleString()} CUP`}
                  </span>
                </div>
                {selectedSub.tiene_deuda && (
                  <div className="flex justify-between text-white/70">
                    <span>Deuda anterior:</span>
                    <span className="font-mono text-white font-bold">
                      +{selectedSub.moneda === 'USD' ? `$${selectedSub.deuda_acumulada} USD` : `$${selectedSub.deuda_acumulada.toLocaleString()} CUP`}
                    </span>
                  </div>
                )}
                <div className="flex justify-between text-white font-bold border-t border-white/15 pt-2">
                  <span>Total exigible:</span>
                  <span className="font-mono text-sm">
                    {selectedSub.moneda === 'USD' ? `$${selectedSub.monto_a_cobrar} USD` : `$${selectedSub.monto_a_cobrar.toLocaleString()} CUP`}
                  </span>
                </div>
              </div>

              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block text-[11px] font-medium text-white/70 mb-1">Monto a Abonar *</label>
                  <input
                    type="number"
                    required
                    value={pagoMonto}
                    onChange={(e) => setPagoMonto(Number(e.target.value))}
                    className="w-full rounded-xl border border-white/20 bg-black px-3 py-2 text-xs font-mono font-bold text-white focus:border-white focus:outline-none"
                  />
                </div>
                <div>
                  <label className="block text-[11px] font-medium text-white/70 mb-1">Fecha de Cobro *</label>
                  <input
                    type="date"
                    required
                    value={pagoFecha}
                    onChange={(e) => setPagoFecha(e.target.value)}
                    className="w-full rounded-xl border border-white/20 bg-black px-3 py-2 text-xs text-white focus:border-white focus:outline-none"
                  />
                </div>
              </div>

              <div>
                <label className="block text-[11px] font-medium text-white/70 mb-1">Método de Cobro *</label>
                <div className="grid grid-cols-2 gap-3">
                  <button
                    type="button"
                    onClick={() => setPagoMetodo('Transferencia')}
                    className={`p-2.5 rounded-xl border text-xs font-bold transition-all cursor-pointer ${
                      pagoMetodo === 'Transferencia'
                        ? 'border-white bg-white/15 text-white'
                        : 'border-white/20 bg-black text-white/60'
                    }`}
                  >
                    Transferencia
                  </button>
                  <button
                    type="button"
                    onClick={() => setPagoMetodo('Efectivo')}
                    className={`p-2.5 rounded-xl border text-xs font-bold transition-all cursor-pointer ${
                      pagoMetodo === 'Efectivo'
                        ? 'border-white bg-white/15 text-white'
                        : 'border-white/20 bg-black text-white/60'
                    }`}
                  >
                    Pago en Efectivo
                  </button>
                </div>
              </div>

              {pagoMetodo === 'Transferencia' ? (
                <div className="p-3.5 rounded-xl border border-white/15 bg-black space-y-3">
                  <div className="grid grid-cols-2 gap-3">
                    <div>
                      <label className="block text-[10px] font-medium text-white/70 mb-1">Plataforma</label>
                      <select
                        value={pagoPlataforma}
                        onChange={(e) => setPagoPlataforma(e.target.value)}
                        className="w-full rounded-lg border border-white/20 bg-zinc-950 px-2.5 py-1.5 text-xs text-white focus:border-white focus:outline-none"
                      >
                        <option value="Transfermóvil">Transfermóvil</option>
                        <option value="EnZona">EnZona</option>
                        <option value="Zelle">Zelle</option>
                        <option value="TropiPay">TropiPay</option>
                      </select>
                    </div>
                    <div>
                      <label className="block text-[10px] font-medium text-white/70 mb-1">No. Transacción / Ref *</label>
                      <input
                        type="text"
                        required
                        value={pagoNumTransaccion}
                        onChange={(e) => setPagoNumTransaccion(e.target.value)}
                        placeholder="TRF-5918234"
                        className="w-full rounded-lg border border-white/20 bg-zinc-950 px-2.5 py-1.5 text-xs font-mono font-bold text-white focus:border-white focus:outline-none"
                      />
                    </div>
                  </div>

                  <div>
                    <label className="block text-[10px] font-medium text-white/70 mb-1">
                      Comprobante de WhatsApp (Imagen)
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
                        className="flex-1 flex items-center justify-center gap-1.5 p-2 rounded-lg border border-white/20 bg-zinc-900 hover:bg-zinc-800 text-xs text-white cursor-pointer"
                      >
                        <Upload className="h-3.5 w-3.5" />
                        {pagoComprobanteNombre ? 'Cambiar Imagen' : 'Subir Imagen de WhatsApp'}
                      </button>
                      <button
                        type="button"
                        onClick={handleGenerateSampleVoucher}
                        className="px-2.5 py-2 rounded-lg border border-white/20 bg-zinc-900 hover:bg-zinc-800 text-xs font-semibold cursor-pointer"
                      >
                        Muestra
                      </button>
                    </div>
                    {pagoComprobanteUrl && (
                      <div className="mt-2 flex items-center justify-between p-2 rounded-lg bg-zinc-950 border border-white/20">
                        <div className="flex items-center gap-2 overflow-hidden">
                          <img src={pagoComprobanteUrl} alt="Comprobante" className="h-8 w-8 rounded object-cover border border-white/20" />
                          <span className="text-xs text-white/80 truncate max-w-[200px]">{pagoComprobanteNombre || 'comprobante.png'}</span>
                        </div>
                        <button
                          type="button"
                          onClick={() => {
                            setPagoComprobanteUrl('');
                            setPagoComprobanteNombre('');
                          }}
                          className="text-white/60 hover:text-white p-1"
                        >
                          <X className="h-3.5 w-3.5" />
                        </button>
                      </div>
                    )}
                  </div>
                </div>
              ) : (
                <div className="p-3.5 rounded-xl border border-white/15 bg-black space-y-3">
                  <div>
                    <label className="block text-[10px] font-medium text-white/70 mb-1">¿Quién Recibe el Dinero? *</label>
                    <input
                      type="text"
                      required
                      value={pagoEfectivoRecibe}
                      onChange={(e) => setPagoEfectivoRecibe(e.target.value)}
                      className="w-full rounded-lg border border-white/20 bg-zinc-950 px-2.5 py-1.5 text-xs text-white focus:border-white focus:outline-none"
                    />
                  </div>
                  <div>
                    <label className="block text-[10px] font-medium text-white/70 mb-1">¿Quién lo Entrega? *</label>
                    <input
                      type="text"
                      required
                      value={pagoEfectivoEntrega}
                      onChange={(e) => setPagoEfectivoEntrega(e.target.value)}
                      className="w-full rounded-lg border border-white/20 bg-zinc-950 px-2.5 py-1.5 text-xs text-white focus:border-white focus:outline-none"
                    />
                  </div>
                </div>
              )}

              <div className="flex justify-end gap-2 pt-3 border-t border-white/15">
                <button
                  type="button"
                  onClick={() => setIsPagoModalOpen(false)}
                  className="rounded-xl border border-white/20 bg-black px-4 py-2 text-xs font-semibold text-white/70 hover:text-white cursor-pointer"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  className="rounded-xl border border-white/30 bg-white text-black px-5 py-2 text-xs font-bold hover:bg-white/90 cursor-pointer shadow-md"
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
          className="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4 overflow-y-auto"
        >
          <div 
            onClick={(e) => e.stopPropagation()} 
            className="relative w-full max-w-md rounded-2xl border border-white/20 bg-zinc-950 p-6 shadow-2xl my-8 text-white"
          >
            <div className="flex items-center justify-between border-b border-white/15 pb-3 mb-4">
              <h3 className="text-sm font-bold text-white">Nueva Suscripción a Gestión Remota</h3>
              <button 
                onClick={() => setIsSubModalOpen(false)} 
                className="text-white/60 hover:text-white p-1 rounded-lg cursor-pointer"
              >
                <X className="h-4 w-4" />
              </button>
            </div>

            <form onSubmit={handleSaveSuscripcion} className="space-y-4">
              <div>
                <label className="block text-[11px] font-medium text-white/70 mb-1">Cliente *</label>
                <select
                  value={subClienteId}
                  onChange={(e) => setSubClienteId(e.target.value)}
                  className="w-full rounded-xl border border-white/20 bg-black px-3 py-2 text-xs text-white focus:border-white focus:outline-none"
                >
                  {clientes.map(c => (
                    <option key={c.id} value={c.id}>{c.nombre} ({c.codigo})</option>
                  ))}
                </select>
              </div>

              <div>
                <label className="block text-[11px] font-medium text-white/70 mb-1">Nombre del Servicio *</label>
                <input
                  type="text"
                  required
                  value={subNombreServicio}
                  onChange={(e) => setSubNombreServicio(e.target.value)}
                  className="w-full rounded-xl border border-white/20 bg-black px-3 py-2 text-xs text-white focus:border-white focus:outline-none"
                />
              </div>

              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block text-[11px] font-medium text-white/70 mb-1">Router / Solución</label>
                  <input
                    type="text"
                    value={subTipoSolucion}
                    onChange={(e) => setSubTipoSolucion(e.target.value)}
                    placeholder="Router 4G Huawei B310"
                    className="w-full rounded-xl border border-white/20 bg-black px-3 py-2 text-xs text-white focus:border-white focus:outline-none"
                  />
                </div>
                <div>
                  <label className="block text-[11px] font-medium text-white/70 mb-1">No. SIM Asignada</label>
                  <input
                    type="text"
                    value={subSimNumero}
                    onChange={(e) => setSubSimNumero(e.target.value)}
                    placeholder="5352849102"
                    className="w-full rounded-xl border border-white/20 bg-black px-3 py-2 text-xs font-mono text-white focus:border-white focus:outline-none"
                  />
                </div>
              </div>

              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block text-[11px] font-medium text-white/70 mb-1">Moneda *</label>
                  <select
                    value={subMoneda}
                    onChange={(e) => setSubMoneda(e.target.value as Moneda)}
                    className="w-full rounded-xl border border-white/20 bg-black px-3 py-2 text-xs text-white focus:border-white focus:outline-none"
                  >
                    <option value="CUP">CUP</option>
                    <option value="USD">USD</option>
                  </select>
                </div>
                <div>
                  <label className="block text-[11px] font-medium text-white/70 mb-1">Tarifa Mensual *</label>
                  <input
                    type="number"
                    required
                    value={subTarifa}
                    onChange={(e) => setSubTarifa(Number(e.target.value))}
                    className="w-full rounded-xl border border-white/20 bg-black px-3 py-2 text-xs font-mono font-bold text-white focus:border-white focus:outline-none"
                  />
                </div>
              </div>

              <div className="flex justify-end gap-2 pt-3 border-t border-white/15">
                <button
                  type="button"
                  onClick={() => setIsSubModalOpen(false)}
                  className="rounded-xl border border-white/20 bg-black px-4 py-2 text-xs font-semibold text-white/70 hover:text-white cursor-pointer"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  className="rounded-xl border border-white/30 bg-white text-black px-5 py-2 text-xs font-bold hover:bg-white/90 cursor-pointer shadow-md"
                >
                  Guardar Suscripción
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      {/* 3. MODAL VISOR DE RECIBO OFICIAL DATAPLUS */}
      {reciboModalData && (
        <div 
          onClick={() => setReciboModalData(null)} 
          className="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-md p-4 overflow-y-auto"
        >
          <div 
            onClick={(e) => e.stopPropagation()} 
            className="relative w-full max-w-md rounded-2xl border border-white/20 bg-zinc-950 p-6 shadow-2xl my-8 text-white space-y-4"
          >
            <div className="flex items-center justify-between border-b border-white/15 pb-3">
              <div className="flex items-center gap-2">
                <Receipt className="h-5 w-5 text-white" />
                <div>
                  <h4 className="text-sm font-bold text-white">Recibo Oficial de Pago</h4>
                  <p className="text-[10px] text-white/70 font-mono">DataPlus CUBA · {reciboModalData.codigo_pago}</p>
                </div>
              </div>
              <button 
                onClick={() => setReciboModalData(null)} 
                className="text-white/60 hover:text-white p-1 rounded-lg cursor-pointer"
              >
                <X className="h-4 w-4" />
              </button>
            </div>

            <div className="p-4 rounded-xl bg-black border border-white/15 font-mono text-xs space-y-3 text-white/90">
              <div className="text-center border-b border-white/15 pb-2">
                <p className="font-extrabold text-white text-sm">DATAPLUS PLATAFORMA</p>
                <p className="text-[10px] text-white/60">Servicio de Gestión Remota & Enlaces 4G</p>
                <p className="text-[10px] text-white font-bold mt-1">RECIBO DE COBRO VERIFICADO</p>
              </div>

              <div className="space-y-1.5 text-[11px]">
                <div className="flex justify-between">
                  <span className="text-white/60">Cliente:</span>
                  <span className="font-bold text-white">{reciboModalData.cliente_nombre}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-white/60">Periodo:</span>
                  <span className="text-white">{reciboModalData.mes_nombre_legible}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-white/60">Fecha de Pago:</span>
                  <span className="text-white">{reciboModalData.fecha_pago}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-white/60">Método:</span>
                  <span className="text-white">{reciboModalData.metodo_pago}</span>
                </div>
                {reciboModalData.numero_transaccion && (
                  <div className="flex justify-between">
                    <span className="text-white/60">Referencia:</span>
                    <span className="text-white">{reciboModalData.numero_transaccion}</span>
                  </div>
                )}
                <div className="flex justify-between border-t border-white/15 pt-2 text-xs">
                  <span className="font-bold text-white">Monto Abonado:</span>
                  <span className="font-extrabold text-white">
                    {reciboModalData.moneda === 'USD' ? `$${reciboModalData.monto_pagado}.00 USD` : `$${reciboModalData.monto_pagado.toLocaleString()}.00 CUP`}
                  </span>
                </div>
              </div>
            </div>

            <div className="flex justify-between items-center pt-2">
              <button
                type="button"
                onClick={() => handleCopyReceiptText(reciboModalData)}
                className="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-white/20 bg-black text-xs font-semibold text-white hover:bg-white/10 cursor-pointer"
              >
                {copiedReceipt ? <Check className="h-3.5 w-3.5" /> : <Copy className="h-3.5 w-3.5" />}
                <span>{copiedReceipt ? 'Copiado' : 'Copiar Texto'}</span>
              </button>
              <button
                onClick={() => setReciboModalData(null)}
                className="px-4 py-1.5 rounded-xl border border-white/30 bg-white text-black font-bold text-xs hover:bg-white/90 cursor-pointer"
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
          className="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-md p-4 overflow-y-auto"
        >
          <div 
            onClick={(e) => e.stopPropagation()} 
            className="relative w-full max-w-md rounded-2xl border border-white/20 bg-zinc-950 p-5 shadow-2xl space-y-4 my-8 text-white"
          >
            <div className="flex items-center justify-between border-b border-white/15 pb-3">
              <div>
                <h4 className="text-sm font-bold text-white flex items-center gap-2">
                  <ImageIcon className="h-4 w-4" />
                  Comprobante WhatsApp
                </h4>
                <p className="text-[11px] text-white/60 font-mono">
                  {previewVoucherUrl.cliente} · Ref: {previewVoucherUrl.op || previewVoucherUrl.codigo}
                </p>
              </div>
              <button
                onClick={() => setPreviewVoucherUrl(null)}
                className="p-1 rounded-lg text-white/60 hover:text-white cursor-pointer"
              >
                <X className="h-4 w-4" />
              </button>
            </div>

            <div className="flex justify-center p-2 rounded-xl bg-black border border-white/15 max-h-[70vh] overflow-auto">
              <img
                src={previewVoucherUrl.url}
                alt="Comprobante de Transferencia"
                className="max-h-[60vh] object-contain rounded-lg shadow-lg"
              />
            </div>

            <div className="flex justify-between items-center pt-2">
              <a
                href={previewVoucherUrl.url}
                download={`comprobante_${previewVoucherUrl.codigo}.png`}
                className="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-white/20 bg-black text-xs font-semibold text-white hover:bg-white/10"
              >
                <Download className="h-3.5 w-3.5" />
                <span>Descargar</span>
              </a>
              <button
                onClick={() => setPreviewVoucherUrl(null)}
                className="px-4 py-1.5 rounded-xl border border-white/30 bg-white text-black font-bold text-xs hover:bg-white/90 cursor-pointer"
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
