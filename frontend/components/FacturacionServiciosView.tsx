import React, { useState, useMemo, useRef } from 'react';
import { 
  Radio, 
  DollarSign, 
  Calendar, 
  Clock, 
  CheckCircle2, 
  AlertCircle, 
  AlertTriangle, 
  Search, 
  Filter, 
  Plus, 
  CreditCard, 
  Receipt, 
  Eye, 
  Upload, 
  Image as ImageIcon, 
  Phone, 
  MessageSquare, 
  Printer, 
  Download, 
  Copy, 
  TrendingUp, 
  TrendingDown, 
  Users, 
  ArrowUpRight, 
  ArrowDownLeft, 
  Building2, 
  X, 
  ChevronRight,
  Send,
  Check
} from 'lucide-react';
import { 
  SuscripcionServicio, 
  RegistroPagoServicio, 
  Cliente, 
  Moneda, 
  MetodoPagoServicio, 
  EstadoCobroServicio 
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
  // Pestañas internas
  const [activeTab, setActiveTab] = useState<'cobros' | 'historial' | 'reportes'>('cobros');
  const [searchTerm, setSearchTerm] = useState('');
  const [filtroEstado, setFiltroEstado] = useState<string>('todos');
  const [filtroMoneda, setFiltroMoneda] = useState<string>('todos');

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
  const [pagoFecha, setPagoFecha] = useState<string>(new Date().toISOString().split('T')[0]);
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
  const [subTipoSolucion, setSubTipoSolucion] = useState<string>('Router4g');
  const [subSimNumero, setSubSimNumero] = useState<string>('+53 5 ');
  const [subMoneda, setSubMoneda] = useState<Moneda>('CUP');
  const [subTarifa, setSubTarifa] = useState<number>(3500);
  const [subNotas, setSubNotas] = useState<string>('');

  // Filtro de períodos en reportes
  const [periodoReporte, setPeriodoReporte] = useState<'semanal' | 'mensual' | 'trimestral' | 'semestral' | 'anual'>('mensual');

  const fileInputRef = useRef<HTMLInputElement>(null);

  // Fecha actual (Simulada según el entorno: 10 de Octubre de 2026)
  const today = new Date();
  const diaActual = today.getDate(); // 10
  const diasRestantesPlazo = Math.max(0, 15 - diaActual);
  const periodoRegularAbierto = diaActual <= 15;

  // Selected subscription in Payment Modal
  const selectedSub = useMemo(() => {
    return suscripciones.find(s => s.id === pagoSubId) || suscripciones[0];
  }, [pagoSubId, suscripciones]);

  // Actualizar montos por defecto cuando cambia la suscripción seleccionada
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

  // Manejador de carga de archivo de comprobante (imagen real de WhatsApp)
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

  // Generar comprobante automático de muestra si no sube foto
  const handleGenerateSampleVoucher = () => {
    if (!selectedSub) return;
    const op = pagoNumTransaccion || `TRF-${Math.floor(1000000 + Math.random() * 9000000)}`;
    const montoFormatted = pagoMoneda === 'USD' ? `$${pagoMonto}.00 USD` : `$${pagoMonto.toLocaleString()}.00 CUP`;
    const vUrl = createSampleVoucher(op, montoFormatted, pagoFecha, selectedSub.cliente_nombre, pagoPlataforma);
    setPagoComprobanteUrl(vUrl);
    setPagoComprobanteNombre(`comprobante_whatsapp_${selectedSub.cliente_nombre.toLowerCase().replace(/\s+/g, '_')}.png`);
  };

  // Guardar pago
  const handleSavePago = (e: React.FormEvent) => {
    e.preventDefault();
    if (!selectedSub) return;

    const diaPago = new Date(pagoFecha).getDate();
    const pagoATiempo = diaPago <= 15;
    const deudaPrevia = selectedSub.deuda_acumulada || 0;
    const totalExigible = selectedSub.monto_a_cobrar || (deudaPrevia + selectedSub.tarifa_mensual);
    const saldoRestante = Math.max(0, totalExigible - pagoMonto);

    // Si no cargó comprobante y es transferencia, creamos uno verificado
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

    // Actualizar suscripción a Al Día o Saldo reducido
    const updatedSub: SuscripcionServicio = {
      ...selectedSub,
      tiene_deuda: saldoRestante > 0,
      meses_deuda_count: saldoRestante > 0 ? 1 : 0,
      meses_deuda_detalle: saldoRestante > 0 ? ['Saldo parcial pendiente'] : [],
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

  // Filtrado de Suscripciones
  const filteredSubs = useMemo(() => {
    return suscripciones.filter(s => {
      const matchSearch = 
        s.cliente_nombre.toLowerCase().includes(searchTerm.toLowerCase()) ||
        s.codigo.toLowerCase().includes(searchTerm.toLowerCase()) ||
        (s.sim_numero && s.sim_numero.includes(searchTerm));

      const matchEstado = 
        filtroEstado === 'todos' ||
        (filtroEstado === 'al_dia' && s.estado_cobro_mes_actual === 'Al_Dia') ||
        (filtroEstado === 'pendiente' && s.estado_cobro_mes_actual === 'Pendiente') ||
        (filtroEstado === 'en_mora' && (s.estado_cobro_mes_actual === 'En_Mora' || s.tiene_deuda)) ||
        (filtroEstado === 'con_deuda' && s.tiene_deuda);

      const matchMoneda = 
        filtroMoneda === 'todos' || s.moneda === filtroMoneda;

      return matchSearch && matchEstado && matchMoneda;
    });
  }, [suscripciones, searchTerm, filtroEstado, filtroMoneda]);

  // Cálculos KPI del Mes Actual (Octubre 2026)
  const kpis = useMemo(() => {
    const subsCUP = suscripciones.filter(s => s.moneda === 'CUP');
    const subsUSD = suscripciones.filter(s => s.moneda === 'USD');

    // Pagos registrados en el mes de Octubre 2026
    const pagosOct = pagos.filter(p => p.fecha_pago.startsWith('2026-10'));
    const recaudadoCUP = pagosOct.filter(p => p.moneda === 'CUP').reduce((acc, p) => acc + p.monto_pagado, 0);
    const recaudadoUSD = pagosOct.filter(p => p.moneda === 'USD').reduce((acc, p) => acc + p.monto_pagado, 0);

    // Por cobrar en Octubre (Suscripciones pendientes o en mora)
    const pendienteCUP = subsCUP.filter(s => s.estado_cobro_mes_actual !== 'Al_Dia').reduce((acc, s) => acc + s.monto_a_cobrar, 0);
    const pendienteUSD = subsUSD.filter(s => s.estado_cobro_mes_actual !== 'Al_Dia').reduce((acc, s) => acc + s.monto_a_cobrar, 0);

    // Deuda acumulada atrasada de meses anteriores
    const deudaAtrasadaCUP = subsCUP.reduce((acc, s) => acc + (s.deuda_acumulada || 0), 0);
    const deudaAtrasadaUSD = subsUSD.reduce((acc, s) => acc + (s.deuda_acumulada || 0), 0);

    // Total facturación estimada del mes
    const facturacionTotalCUP = subsCUP.reduce((acc, s) => acc + s.tarifa_mensual, 0);
    const facturacionTotalUSD = subsUSD.reduce((acc, s) => acc + s.tarifa_mensual, 0);

    // Clientes al día vs mora
    const alDiaCount = suscripciones.filter(s => s.estado_cobro_mes_actual === 'Al_Dia').length;
    const enMoraCount = suscripciones.filter(s => s.estado_cobro_mes_actual === 'En_Mora' || s.tiene_deuda).length;
    const pendientesCount = suscripciones.filter(s => s.estado_cobro_mes_actual === 'Pendiente').length;

    return {
      recaudadoCUP,
      recaudadoUSD,
      pendienteCUP,
      pendienteUSD,
      deudaAtrasadaCUP,
      deudaAtrasadaUSD,
      facturacionTotalCUP,
      facturacionTotalUSD,
      alDiaCount,
      enMoraCount,
      pendientesCount,
      totalSubs: suscripciones.length,
      porcentajeCobro: suscripciones.length > 0 ? Math.round((alDiaCount / suscripciones.length) * 100) : 0
    };
  }, [suscripciones, pagos]);

  // Pagos de la semana en curso (04/10/2026 al 10/10/2026)
  const pagosSemana = useMemo(() => {
    // Calculamos los últimos 7 días con respecto a 2026-10-10
    const sieteDiasAtras = new Date('2026-10-04');
    return pagos.filter(p => new Date(p.fecha_pago) >= sieteDiasAtras);
  }, [pagos]);

  const recaudadoSemanaCUP = useMemo(() => {
    return pagosSemana.filter(p => p.moneda === 'CUP').reduce((acc, p) => acc + p.monto_pagado, 0);
  }, [pagosSemana]);

  const recaudadoSemanaUSD = useMemo(() => {
    return pagosSemana.filter(p => p.moneda === 'USD').reduce((acc, p) => acc + p.monto_pagado, 0);
  }, [pagosSemana]);

  // Mayores deudores ordenados por monto de deuda acumulada
  const mayoresDeudores = useMemo(() => {
    return [...suscripciones]
      .filter(s => s.tiene_deuda || s.deuda_acumulada > 0 || s.meses_deuda_count > 0)
      .sort((a, b) => (b.monto_a_cobrar || 0) - (a.monto_a_cobrar || 0));
  }, [suscripciones]);

  // Datos para Reportes por Período: Anual, Semestral, Trimestral y Mensual
  const reportesPeriodos = useMemo(() => {
    // Lista de meses 2026
    const meses = [
      { key: '01', nombre: 'Enero 2026', q: 'Q1', s: 'S1' },
      { key: '02', nombre: 'Febrero 2026', q: 'Q1', s: 'S1' },
      { key: '03', nombre: 'Marzo 2026', q: 'Q1', s: 'S1' },
      { key: '04', nombre: 'Abril 2026', q: 'Q2', s: 'S1' },
      { key: '05', nombre: 'Mayo 2026', q: 'Q2', s: 'S1' },
      { key: '06', nombre: 'Junio 2026', q: 'Q2', s: 'S1' },
      { key: '07', nombre: 'Julio 2026', q: 'Q3', s: 'S2' },
      { key: '08', nombre: 'Agosto 2026', q: 'Q3', s: 'S2' },
      { key: '09', nombre: 'Septiembre 2026', q: 'Q3', s: 'S2' },
      { key: '10', nombre: 'Octubre 2026 (En Curso)', q: 'Q4', s: 'S2' },
      { key: '11', nombre: 'Noviembre 2026', q: 'Q4', s: 'S2' },
      { key: '12', nombre: 'Diciembre 2026', q: 'Q4', s: 'S2' },
    ];

    const datosMensuales = meses.map(m => {
      const pagosDelMes = pagos.filter(p => p.fecha_pago.startsWith(`2026-${m.key}`));
      const cupCobrado = pagosDelMes.filter(p => p.moneda === 'CUP').reduce((acc, p) => acc + p.monto_pagado, 0);
      const usdCobrado = pagosDelMes.filter(p => p.moneda === 'USD').reduce((acc, p) => acc + p.monto_pagado, 0);
      const totalOperaciones = pagosDelMes.length;

      // Estimación de facturación exigible base
      const cupExigible = m.key <= '10' ? 24500 : 0;
      const usdExigible = m.key <= '10' ? 155 : 0;
      const cumplimiento = cupExigible > 0 ? Math.min(100, Math.round((cupCobrado / cupExigible) * 100)) : 0;

      return {
        ...m,
        cupCobrado,
        usdCobrado,
        cupExigible,
        usdExigible,
        totalOperaciones,
        cumplimiento
      };
    });

    // Trimestrales
    const trimestres = [
      { id: 'Q1', nombre: 'Primer Trimestre (Ene - Mar 2026)', meses: ['01', '02', '03'] },
      { id: 'Q2', nombre: 'Segundo Trimestre (Abr - Jun 2026)', meses: ['04', '05', '06'] },
      { id: 'Q3', nombre: 'Tercer Trimestre (Jul - Sep 2026)', meses: ['07', '08', '09'] },
      { id: 'Q4', nombre: 'Cuarto Trimestre (Oct - Dic 2026)', meses: ['10', '11', '12'] },
    ].map(t => {
      const mesesFiltrados = datosMensuales.filter(m => t.meses.includes(m.key));
      const cupCobrado = mesesFiltrados.reduce((acc, m) => acc + m.cupCobrado, 0);
      const usdCobrado = mesesFiltrados.reduce((acc, m) => acc + m.usdCobrado, 0);
      const cupExigible = mesesFiltrados.reduce((acc, m) => acc + m.cupExigible, 0);
      const usdExigible = mesesFiltrados.reduce((acc, m) => acc + m.usdExigible, 0);
      const operaciones = mesesFiltrados.reduce((acc, m) => acc + m.totalOperaciones, 0);
      const cumplimiento = cupExigible > 0 ? Math.round((cupCobrado / cupExigible) * 100) : 0;
      return { ...t, cupCobrado, usdCobrado, cupExigible, usdExigible, operaciones, cumplimiento };
    });

    // Semestrales
    const semestres = [
      { id: 'S1', nombre: 'Primer Semestre (Enero - Junio 2026)', meses: ['01', '02', '03', '04', '05', '06'] },
      { id: 'S2', nombre: 'Segundo Semestre (Julio - Diciembre 2026)', meses: ['07', '08', '09', '10', '11', '12'] },
    ].map(s => {
      const mesesFiltrados = datosMensuales.filter(m => s.meses.includes(m.key));
      const cupCobrado = mesesFiltrados.reduce((acc, m) => acc + m.cupCobrado, 0);
      const usdCobrado = mesesFiltrados.reduce((acc, m) => acc + m.usdCobrado, 0);
      const cupExigible = mesesFiltrados.reduce((acc, m) => acc + m.cupExigible, 0);
      const usdExigible = mesesFiltrados.reduce((acc, m) => acc + m.usdExigible, 0);
      const operaciones = mesesFiltrados.reduce((acc, m) => acc + m.totalOperaciones, 0);
      const cumplimiento = cupExigible > 0 ? Math.round((cupCobrado / cupExigible) * 100) : 0;
      return { ...s, cupCobrado, usdCobrado, cupExigible, usdExigible, operaciones, cumplimiento };
    });

    // Anual
    const totalAnualCUP = datosMensuales.reduce((acc, m) => acc + m.cupCobrado, 0);
    const totalAnualUSD = datosMensuales.reduce((acc, m) => acc + m.usdCobrado, 0);
    const exigibleAnualCUP = datosMensuales.reduce((acc, m) => acc + m.cupExigible, 0);
    const exigibleAnualUSD = datosMensuales.reduce((acc, m) => acc + m.usdExigible, 0);

    return {
      datosMensuales,
      trimestres,
      semestres,
      anual: {
        totalAnualCUP,
        totalAnualUSD,
        exigibleAnualCUP,
        exigibleAnualUSD,
        operacionesTotales: pagos.length,
        cumplimientoGlobal: exigibleAnualCUP > 0 ? Math.round((totalAnualCUP / exigibleAnualCUP) * 100) : 0
      }
    };
  }, [pagos]);

  // Abrir WhatsApp con mensaje personalizado de cobro
  const handleOpenWhatsAppReminder = (sub: SuscripcionServicio) => {
    const telefonoLimpio = (sub.cliente_telefono || '').replace(/[^\d]/g, '');
    const mesesTexto = sub.meses_deuda_detalle.length > 0 ? sub.meses_deuda_detalle.join(', ') : 'Octubre 2026';
    const montoTexto = sub.moneda === 'USD' ? `$${sub.monto_a_cobrar} USD` : `$${sub.monto_a_cobrar.toLocaleString()} CUP`;
    
    const mensaje = encodeURIComponent(
      `Hola ${sub.cliente_nombre}, le contactamos de DataPlus para recordarle el pago de su servicio mensual de ${sub.nombre_servicio}. ` +
      (sub.tiene_deuda 
        ? `Actualmente presenta ${sub.meses_deuda_count} mes(es) pendiente(s) (${mesesTexto}). El monto total a cobrar con deuda incluida es de ${montoTexto}. `
        : `Le recordamos que el pago correspondiente a mes adelantado es de ${montoTexto} y debe efectuarse durante los primeros 15 días del mes. `) +
      `Por favor, recuerde enviar la captura o comprobante de la transferencia a este chat una vez realizada. ¡Muchas gracias!`
    );

    window.open(`https://wa.me/${telefonoLimpio}?text=${mensaje}`, '_blank');
  };

  // Copiar Recibo de Pago para WhatsApp
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

  return (
    <div className="space-y-6">
      {/* ================= ENCABEZADO Y REGLAS DE FACTURACIÓN ================= */}
      <div className="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 bg-slate-900/60 p-5 rounded-2xl border border-slate-800">
        <div>
          <div className="flex items-center gap-2.5">
            <div className="p-2 rounded-xl bg-gradient-to-tr from-sky-500 to-indigo-600 text-white shadow-md shadow-sky-500/20">
              <Radio className="h-5 w-5" />
            </div>
            <div>
              <h2 className="text-xl font-bold text-white tracking-tight flex items-center gap-2">
                Facturación de Servicios & Gestión Remota
                <span className="rounded-full bg-emerald-500/10 px-2 py-0.5 text-[10px] font-mono font-bold text-emerald-400 border border-emerald-500/20">
                  Mes Adelantado
                </span>
              </h2>
              <p className="text-xs text-slate-400 mt-0.5">
                Control de mensualidades (CUP/USD), comprobantes de transferencia por WhatsApp, pagos en efectivo y gestión de deudores.
              </p>
            </div>
          </div>
        </div>

        {/* Regla de los primeros 15 días en tiempo real */}
        <div className="flex flex-wrap items-center gap-3">
          <div className="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-xs">
            <Clock className="h-4 w-4 text-sky-400" />
            <div>
              <p className="text-[10px] uppercase font-bold text-slate-400">Plazo de Cobro (Día 1 al 15)</p>
              <p className="font-semibold text-white">
                {periodoRegularAbierto ? (
                  <span className="text-emerald-400 flex items-center gap-1">
                    <span className="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Día {diaActual} · Quedan {diasRestantesPlazo} días de plazo regular
                  </span>
                ) : (
                  <span className="text-rose-400 font-bold">
                    Período regular vencido (Día {diaActual})
                  </span>
                )}
              </p>
            </div>
          </div>

          <div className="flex items-center gap-2">
            <button
              onClick={() => {
                if (suscripciones.length > 0) {
                  handleSelectSubForPago(suscripciones[0]);
                }
              }}
              className="flex items-center gap-1.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold px-3.5 py-2 text-xs transition-all shadow-md shadow-emerald-500/20 cursor-pointer"
            >
              <Plus className="h-4 w-4 stroke-[2.5]" />
              Registrar Cobro
            </button>
            <button
              onClick={() => {
                setSubClienteId(clientes[0]?.id || '');
                setIsSubModalOpen(true);
              }}
              className="flex items-center gap-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold px-3 py-2 text-xs transition-all border border-slate-700 cursor-pointer"
            >
              <Plus className="h-3.5 w-3.5" />
              Nueva Suscripción
            </button>
          </div>
        </div>
      </div>

      {/* ================= KPI CARDS (MES CORRIENTE OCTUBRE 2026) ================= */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {/* Recaudado CUP */}
        <div className="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 shadow-sm relative overflow-hidden">
          <div className="flex items-center justify-between">
            <span className="text-xs font-semibold text-slate-400">Recaudado este Mes (CUP)</span>
            <div className="rounded-lg bg-emerald-500/10 p-2 text-emerald-400 border border-emerald-500/20">
              <ArrowDownLeft className="h-4 w-4" />
            </div>
          </div>
          <p className="text-2xl font-extrabold text-white mt-2 font-mono">
            ${kpis.recaudadoCUP.toLocaleString()} <span className="text-sm font-sans font-bold text-emerald-400">CUP</span>
          </p>
          <div className="mt-2 flex items-center justify-between text-[11px] text-slate-400">
            <span>Pendiente por cobrar:</span>
            <span className="font-mono font-semibold text-amber-400">${kpis.pendienteCUP.toLocaleString()} CUP</span>
          </div>
        </div>

        {/* Recaudado USD */}
        <div className="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 shadow-sm relative overflow-hidden">
          <div className="flex items-center justify-between">
            <span className="text-xs font-semibold text-slate-400">Recaudado este Mes (USD)</span>
            <div className="rounded-lg bg-sky-500/10 p-2 text-sky-400 border border-sky-500/20">
              <DollarSign className="h-4 w-4" />
            </div>
          </div>
          <p className="text-2xl font-extrabold text-white mt-2 font-mono">
            ${kpis.recaudadoUSD} <span className="text-sm font-sans font-bold text-sky-400">USD</span>
          </p>
          <div className="mt-2 flex items-center justify-between text-[11px] text-slate-400">
            <span>Pendiente por cobrar:</span>
            <span className="font-mono font-semibold text-amber-400">${kpis.pendienteUSD} USD</span>
          </div>
        </div>

        {/* Morosidad / Deuda Atrasada Acumulada */}
        <div className="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 shadow-sm relative overflow-hidden">
          <div className="flex items-center justify-between">
            <span className="text-xs font-semibold text-slate-400">Deuda Atrasada Total</span>
            <div className="rounded-lg bg-rose-500/10 p-2 text-rose-400 border border-rose-500/20">
              <AlertTriangle className="h-4 w-4" />
            </div>
          </div>
          <div className="mt-2 flex items-baseline gap-2">
            <p className="text-xl font-extrabold text-rose-400 font-mono">
              ${kpis.deudaAtrasadaCUP.toLocaleString()} <span className="text-xs font-sans">CUP</span>
            </p>
            <span className="text-slate-500">|</span>
            <p className="text-xl font-extrabold text-rose-400 font-mono">
              ${kpis.deudaAtrasadaUSD} <span className="text-xs font-sans">USD</span>
            </p>
          </div>
          <div className="mt-2 text-[11px] text-rose-400 font-medium flex items-center gap-1">
            <Users className="h-3 w-3" /> {mayoresDeudores.length} clientes con meses en atraso
          </div>
        </div>

        {/* Estado de Cobranza del Mes */}
        <div className="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 shadow-sm relative overflow-hidden">
          <div className="flex items-center justify-between">
            <span className="text-xs font-semibold text-slate-400">Cumplimiento del Plazo</span>
            <span className="text-xs font-bold text-emerald-400 font-mono">{kpis.porcentajeCobro}% al día</span>
          </div>
          {/* Progress bar */}
          <div className="w-full bg-slate-800 rounded-full h-2.5 mt-3 overflow-hidden">
            <div 
              className="bg-gradient-to-r from-emerald-500 to-sky-400 h-2.5 rounded-full transition-all duration-500" 
              style={{ width: `${Math.min(100, Math.max(5, kpis.porcentajeCobro))}%` }}
            ></div>
          </div>
          <div className="mt-2 flex items-center justify-between text-[11px] text-slate-400">
            <span className="text-emerald-400">{kpis.alDiaCount} Pagados</span>
            <span className="text-amber-400">{kpis.pendientesCount} Pendientes</span>
            <span className="text-rose-400">{kpis.enMoraCount} Morosos</span>
          </div>
        </div>
      </div>

      {/* ================= TABS PRINCIPALES ================= */}
      <div className="border-b border-slate-800 flex gap-4 overflow-x-auto">
        <button
          onClick={() => setActiveTab('cobros')}
          className={`pb-3 text-xs font-bold transition-colors cursor-pointer border-b-2 flex items-center gap-2 whitespace-nowrap ${
            activeTab === 'cobros'
              ? 'border-emerald-500 text-emerald-400'
              : 'border-transparent text-slate-400 hover:text-slate-200'
          }`}
        >
          <Radio className="h-4 w-4" />
          Control de Cobros Mensuales ({suscripciones.length})
        </button>
        <button
          onClick={() => setActiveTab('historial')}
          className={`pb-3 text-xs font-bold transition-colors cursor-pointer border-b-2 flex items-center gap-2 whitespace-nowrap ${
            activeTab === 'historial'
              ? 'border-sky-500 text-sky-400'
              : 'border-transparent text-slate-400 hover:text-slate-200'
          }`}
        >
          <CreditCard className="h-4 w-4" />
          Historial de Pagos & Comprobantes ({pagos.length})
        </button>
        <button
          onClick={() => setActiveTab('reportes')}
          className={`pb-3 text-xs font-bold transition-colors cursor-pointer border-b-2 flex items-center gap-2 whitespace-nowrap ${
            activeTab === 'reportes'
              ? 'border-indigo-500 text-indigo-400'
              : 'border-transparent text-slate-400 hover:text-slate-200'
          }`}
        >
          <Receipt className="h-4 w-4" />
          Reportes & Análisis de Cobro (Semanal / Anual / Deudores)
        </button>
      </div>

      {/* ================= CONTENIDO DE SUB-PESTAÑAS ================= */}

      {/* PESTAÑA 1: CONTROL DE COBROS MENSUALES (TABLA DETALLADA) */}
      {activeTab === 'cobros' && (
        <div className="space-y-4">
          {/* Barra de Filtros */}
          <div className="flex flex-col sm:flex-row gap-3 justify-between items-center">
            <div className="relative w-full sm:w-80">
              <Search className="absolute left-3 top-2.5 h-3.5 w-3.5 text-slate-400" />
              <input
                type="text"
                placeholder="Buscar cliente, código o teléfono..."
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
                className="w-full rounded-xl border border-slate-800 bg-slate-900 pl-9 pr-4 py-2 text-xs text-white placeholder-slate-500 focus:border-emerald-500 focus:outline-none"
              />
            </div>

            <div className="flex items-center gap-2 w-full sm:w-auto">
              {/* Filtro Estado */}
              <select
                value={filtroEstado}
                onChange={(e) => setFiltroEstado(e.target.value)}
                className="rounded-xl border border-slate-800 bg-slate-900 px-3 py-2 text-xs text-slate-300 focus:border-emerald-500 focus:outline-none"
              >
                <option value="todos">Todos los Estados</option>
                <option value="al_dia">✓ Al Día</option>
                <option value="pendiente">⏳ Pendientes (1-15 días)</option>
                <option value="en_mora">⚠️ En Mora</option>
                <option value="con_deuda">🚨 Con Deuda Acumulada</option>
              </select>

              {/* Filtro Moneda */}
              <select
                value={filtroMoneda}
                onChange={(e) => setFiltroMoneda(e.target.value)}
                className="rounded-xl border border-slate-800 bg-slate-900 px-3 py-2 text-xs text-slate-300 focus:border-emerald-500 focus:outline-none"
              >
                <option value="todos">CUP y USD</option>
                <option value="CUP">Solo CUP</option>
                <option value="USD">Solo USD</option>
              </select>
            </div>
          </div>

          {/* Tabla de Clientes de Gestión Remota */}
          <div className="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/50 shadow-sm">
            <div className="overflow-x-auto">
              <table className="w-full text-left text-xs text-slate-300">
                <thead className="bg-slate-950/80 text-[11px] font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-800">
                  <tr>
                    <th className="py-3.5 px-4">Cliente / Suscripción</th>
                    <th className="py-3.5 px-4">Servicio & SIM</th>
                    <th className="py-3.5 px-4">Tarifa Mensual</th>
                    <th className="py-3.5 px-4">¿Tiene Deuda?</th>
                    <th className="py-3.5 px-4">Meses Adeudados</th>
                    <th className="py-3.5 px-4 text-right">Monto a Cobrar (Total)</th>
                    <th className="py-3.5 px-4 text-center">Estado Mes</th>
                    <th className="py-3.5 px-4">Último Pago</th>
                    <th className="py-3.5 px-4 text-center">Acciones</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-slate-800/80">
                  {filteredSubs.length === 0 ? (
                    <tr>
                      <td colSpan={9} className="py-8 text-center text-slate-500">
                        No se encontraron suscripciones con los filtros seleccionados.
                      </td>
                    </tr>
                  ) : (
                    filteredSubs.map((sub) => {
                      const isAlDia = sub.estado_cobro_mes_actual === 'Al_Dia';
                      const isPendiente = sub.estado_cobro_mes_actual === 'Pendiente';
                      const isMora = sub.estado_cobro_mes_actual === 'En_Mora' || sub.tiene_deuda;

                      return (
                        <tr key={sub.id} className="hover:bg-slate-800/40 transition-colors">
                          {/* Cliente */}
                          <td className="py-3 px-4">
                            <div className="font-semibold text-white">{sub.cliente_nombre}</div>
                            <div className="text-[10px] text-slate-400 font-mono mt-0.5 flex items-center gap-1.5">
                              <span>{sub.codigo}</span>
                              {sub.cliente_telefono && (
                                <>
                                  <span>·</span>
                                  <span>{sub.cliente_telefono}</span>
                                </>
                              )}
                            </div>
                          </td>

                          {/* Servicio & SIM */}
                          <td className="py-3 px-4">
                            <div className="text-slate-200">{sub.nombre_servicio}</div>
                            <div className="text-[10px] text-slate-400 font-mono">
                              {sub.sim_numero || 'Sin SIM asignada'} ({sub.tipo_solucion || '4G'})
                            </div>
                          </td>

                          {/* Tarifa Mensual */}
                          <td className="py-3 px-4 font-mono font-bold">
                            <span className={sub.moneda === 'USD' ? 'text-sky-300' : 'text-emerald-300'}>
                              {sub.moneda === 'USD' ? `$${sub.tarifa_mensual} USD` : `$${sub.tarifa_mensual.toLocaleString()} CUP`}
                            </span>
                          </td>

                          {/* ¿Tiene Deuda? */}
                          <td className="py-3 px-4">
                            {sub.tiene_deuda ? (
                              <span className="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-500/15 text-rose-300 border border-rose-500/30">
                                SÍ (${sub.moneda === 'USD' ? `${sub.deuda_acumulada} USD` : `${sub.deuda_acumulada.toLocaleString()} CUP`})
                              </span>
                            ) : (
                              <span className="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-800 text-slate-400">
                                NO
                              </span>
                            )}
                          </td>

                          {/* Meses Adeudados */}
                          <td className="py-3 px-4">
                            {sub.meses_deuda_count > 0 ? (
                              <div>
                                <span className="font-bold text-rose-400 text-xs">
                                  {sub.meses_deuda_count} {sub.meses_deuda_count === 1 ? 'mes' : 'meses'}
                                </span>
                                <p className="text-[10px] text-slate-400 truncate max-w-[150px]">
                                  {sub.meses_deuda_detalle.join(', ')}
                                </p>
                              </div>
                            ) : (
                              <span className="text-slate-500 text-[11px]">0 meses</span>
                            )}
                          </td>

                          {/* Monto a Cobrar (Total con Deuda Incluida) */}
                          <td className="py-3 px-4 text-right font-mono font-extrabold text-sm">
                            <span className={sub.tiene_deuda ? 'text-rose-400' : isAlDia ? 'text-slate-400' : 'text-amber-400'}>
                              {sub.moneda === 'USD' 
                                ? `$${sub.monto_a_cobrar} USD` 
                                : `$${sub.monto_a_cobrar.toLocaleString()} CUP`}
                            </span>
                            {sub.tiene_deuda && (
                              <div className="text-[9px] text-rose-400 uppercase font-bold tracking-tight">
                                Deuda Incluida
                              </div>
                            )}
                          </td>

                          {/* Estado del Mes */}
                          <td className="py-3 px-4 text-center">
                            {isAlDia ? (
                              <span className="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                <CheckCircle2 className="h-3 w-3" /> Al Día
                              </span>
                            ) : isPendiente ? (
                              <span className="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                <Clock className="h-3 w-3" /> Pendiente (1-15)
                              </span>
                            ) : (
                              <span className="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                <AlertTriangle className="h-3 w-3" /> En Mora
                              </span>
                            )}
                          </td>

                          {/* Último Pago */}
                          <td className="py-3 px-4 text-[11px] text-slate-400">
                            {sub.ultimo_pago_fecha ? (
                              <div>
                                <p className="text-slate-300 font-semibold">{sub.ultimo_pago_fecha}</p>
                                <p className="text-[10px] text-slate-400">
                                  {sub.ultimo_pago_metodo} · {sub.ultimo_pago_transaccion || 'OK'}
                                </p>
                              </div>
                            ) : (
                              <span className="text-slate-500">Sin registros</span>
                            )}
                          </td>

                          {/* Acciones */}
                          <td className="py-3 px-4 text-center">
                            <div className="flex items-center justify-center gap-1.5">
                              {/* Botón Cobrar */}
                              <button
                                onClick={() => handleSelectSubForPago(sub)}
                                title="Registrar Cobro"
                                className={`px-2.5 py-1 rounded-lg font-bold text-xs flex items-center gap-1 transition-all cursor-pointer ${
                                  isAlDia 
                                    ? 'bg-slate-800 hover:bg-slate-700 text-slate-300' 
                                    : 'bg-emerald-500 hover:bg-emerald-400 text-slate-950 shadow-sm shadow-emerald-500/20'
                                }`}
                              >
                                <Plus className="h-3 w-3" />
                                {isAlDia ? 'Abonar' : 'Cobrar'}
                              </button>

                              {/* WhatsApp Reminder */}
                              <button
                                onClick={() => handleOpenWhatsAppReminder(sub)}
                                title="Enviar Recordatorio por WhatsApp"
                                className="p-1.5 rounded-lg bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 transition-all cursor-pointer"
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

      {/* PESTAÑA 2: HISTORIAL DE PAGOS & COMPROBANTES DE WHATSAPP */}
      {activeTab === 'historial' && (
        <div className="space-y-4">
          <div className="flex justify-between items-center">
            <div>
              <h3 className="text-sm font-bold text-white flex items-center gap-2">
                <CreditCard className="h-4 w-4 text-sky-400" />
                Comprobantes de Pago Registrados ({pagos.length})
              </h3>
              <p className="text-xs text-slate-400">
                Registro de transferencias con imagen de comprobante y cobros en efectivo firmados.
              </p>
            </div>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            {pagos.map((p) => {
              const isTransfer = p.metodo_pago === 'Transferencia';

              return (
                <div key={p.id} className="rounded-2xl border border-slate-800 bg-slate-900/60 p-5 flex flex-col justify-between space-y-4 shadow-sm hover:border-slate-700 transition-colors">
                  <div>
                    {/* Header de la tarjeta */}
                    <div className="flex items-start justify-between">
                      <div>
                        <span className="font-mono text-[10px] text-sky-400 bg-sky-950/60 px-2 py-0.5 rounded border border-sky-500/20">
                          {p.codigo_pago}
                        </span>
                        <h4 className="text-sm font-bold text-white mt-1.5">{p.cliente_nombre}</h4>
                        <p className="text-[11px] text-slate-400">{p.servicio_nombre}</p>
                      </div>

                      <div className="text-right">
                        <span className="text-base font-extrabold font-mono text-emerald-400">
                          {p.moneda === 'USD' ? `$${p.monto_pagado} USD` : `$${p.monto_pagado.toLocaleString()} CUP`}
                        </span>
                        <p className="text-[10px] text-slate-400">{p.mes_nombre_legible}</p>
                      </div>
                    </div>

                    {/* Datos del pago */}
                    <div className="mt-4 space-y-2 text-xs border-t border-slate-800/80 pt-3">
                      <div className="flex justify-between items-center text-slate-400">
                        <span>Fecha & Día:</span>
                        <span className="text-slate-200 font-semibold flex items-center gap-1">
                          <Calendar className="h-3 w-3 text-slate-400" />
                          {p.fecha_pago} (Día {p.dia_pago} ≤ 15)
                        </span>
                      </div>

                      <div className="flex justify-between items-center text-slate-400">
                        <span>Método:</span>
                        <span className={`px-2 py-0.5 rounded text-[10px] font-bold ${
                          isTransfer ? 'bg-sky-500/10 text-sky-400 border border-sky-500/20' : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20'
                        }`}>
                          {p.metodo_pago}
                        </span>
                      </div>

                      {/* Si fue Transferencia */}
                      {isTransfer ? (
                        <>
                          <div className="flex justify-between items-center text-slate-400">
                            <span>Plataforma:</span>
                            <span className="text-slate-200">{p.plataforma_transferencia || 'Transfermóvil'}</span>
                          </div>
                          <div className="flex justify-between items-center text-slate-400">
                            <span>No. Transacción:</span>
                            <span className="font-mono text-sky-400 font-bold">{p.numero_transaccion || 'TRF-OK'}</span>
                          </div>
                        </>
                      ) : (
                        /* Si fue Efectivo */
                        <>
                          <div className="flex justify-between items-center text-slate-400">
                            <span>Recibido por:</span>
                            <span className="text-slate-200 font-semibold">{p.efectivo_quien_recibe || 'Enmanuel Caraballo'}</span>
                          </div>
                          <div className="flex justify-between items-center text-slate-400">
                            <span>Entregado por:</span>
                            <span className="text-slate-200">{p.efectivo_quien_entrega || 'Cliente'}</span>
                          </div>
                        </>
                      )}
                    </div>
                  </div>

                  {/* Thumbnail de comprobante o botón de recibo */}
                  <div className="pt-2 border-t border-slate-800/60 flex items-center justify-between gap-2">
                    {isTransfer && p.comprobante_imagen_url ? (
                      <button
                        onClick={() => setPreviewVoucherUrl({
                          url: p.comprobante_imagen_url!,
                          cliente: p.cliente_nombre,
                          codigo: p.codigo_pago,
                          op: p.numero_transaccion
                        })}
                        className="flex items-center gap-1.5 text-xs text-sky-400 hover:text-sky-300 font-semibold transition-colors cursor-pointer"
                      >
                        <Eye className="h-3.5 w-3.5" />
                        Ver Comprobante WhatsApp
                      </button>
                    ) : (
                      <span className="text-[11px] text-emerald-400 flex items-center gap-1 font-medium">
                        <CheckCircle2 className="h-3 w-3" /> Efectivo Recibido
                      </span>
                    )}

                    <button
                      onClick={() => setReciboModalData(p)}
                      className="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs transition-colors flex items-center gap-1 cursor-pointer"
                      title="Ver Recibo Oficial"
                    >
                      <Receipt className="h-3.5 w-3.5" />
                      Recibo
                    </button>
                  </div>
                </div>
              );
            })}
          </div>
        </div>
      )}

      {/* PESTAÑA 3: REPORTES & ESTADÍSTICAS DEL SERVICIO */}
      {activeTab === 'reportes' && (
        <div className="space-y-6">
          {/* SECCIÓN A: QUIÉNES PAGARON EN LA SEMANA */}
          <div className="rounded-2xl border border-slate-800 bg-slate-900/60 p-5 shadow-sm space-y-4">
            <div className="flex flex-col sm:flex-row justify-between sm:items-center gap-2">
              <div>
                <h3 className="text-sm font-bold text-white flex items-center gap-2">
                  <Calendar className="h-4 w-4 text-emerald-400" />
                  Cobros Realizados en la Semana ({pagosSemana.length} pagos)
                </h3>
                <p className="text-xs text-slate-400">
                  Resumen de clientes que abonaron su mensualidad en los últimos 7 días.
                </p>
              </div>

              <div className="flex items-center gap-3 bg-slate-950 px-3.5 py-1.5 rounded-xl border border-slate-800 text-xs font-mono">
                <span className="text-emerald-400 font-bold">${recaudadoSemanaCUP.toLocaleString()} CUP</span>
                <span className="text-slate-600">|</span>
                <span className="text-sky-400 font-bold">${recaudadoSemanaUSD} USD</span>
              </div>
            </div>

            <div className="overflow-x-auto">
              <table className="w-full text-left text-xs text-slate-300">
                <thead className="bg-slate-950/80 text-[10px] font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-800">
                  <tr>
                    <th className="py-2.5 px-3">Cliente</th>
                    <th className="py-2.5 px-3">Servicio</th>
                    <th className="py-2.5 px-3">Monto Abonado</th>
                    <th className="py-2.5 px-3">Fecha y Día</th>
                    <th className="py-2.5 px-3">Método</th>
                    <th className="py-2.5 px-3">Transacción / Entrega</th>
                    <th className="py-2.5 px-3 text-center">Comprobante</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-slate-800/60">
                  {pagosSemana.map((p) => (
                    <tr key={p.id} className="hover:bg-slate-800/30">
                      <td className="py-2.5 px-3 font-semibold text-white">{p.cliente_nombre}</td>
                      <td className="py-2.5 px-3 text-slate-400">{p.servicio_nombre}</td>
                      <td className="py-2.5 px-3 font-mono font-bold text-emerald-400">
                        {p.moneda === 'USD' ? `$${p.monto_pagado} USD` : `$${p.monto_pagado.toLocaleString()} CUP`}
                      </td>
                      <td className="py-2.5 px-3 text-slate-300">{p.fecha_pago} (Día {p.dia_pago})</td>
                      <td className="py-2.5 px-3">{p.metodo_pago}</td>
                      <td className="py-2.5 px-3 font-mono text-slate-400 text-[11px]">
                        {p.metodo_pago === 'Transferencia' ? p.numero_transaccion : `${p.efectivo_quien_recibe}`}
                      </td>
                      <td className="py-2.5 px-3 text-center">
                        {p.comprobante_imagen_url ? (
                          <button
                            onClick={() => setPreviewVoucherUrl({
                              url: p.comprobante_imagen_url!,
                              cliente: p.cliente_nombre,
                              codigo: p.codigo_pago,
                              op: p.numero_transaccion
                            })}
                            className="p-1 rounded bg-sky-500/10 text-sky-400 hover:bg-sky-500/20 text-[10px] font-semibold"
                          >
                            Ver Foto
                          </button>
                        ) : (
                          <span className="text-[10px] text-slate-500">Recibo Físico</span>
                        )}
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>

          {/* SECCIÓN B: LOS MAYORES DEUDORES */}
          <div className="rounded-2xl border border-slate-800 bg-slate-900/60 p-5 shadow-sm space-y-4">
            <div className="flex justify-between items-center">
              <div>
                <h3 className="text-sm font-bold text-white flex items-center gap-2">
                  <AlertTriangle className="h-4 w-4 text-rose-400" />
                  Ranking de Mayores Deudores de Gestión Remota
                </h3>
                <p className="text-xs text-slate-400">
                  Clientes ordenados por mayor retraso y volumen de deuda acumulada.
                </p>
              </div>
              <span className="text-xs font-bold text-rose-400 bg-rose-500/10 px-2.5 py-1 rounded-full border border-rose-500/20">
                {mayoresDeudores.length} clientes en mora
              </span>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
              {mayoresDeudores.map((sub, idx) => (
                <div key={sub.id} className="p-4 rounded-xl border border-rose-500/30 bg-rose-950/20 flex flex-col justify-between space-y-3">
                  <div>
                    <div className="flex items-center justify-between">
                      <span className="font-mono text-[10px] font-bold text-rose-400">
                        #{idx + 1} DEUDOR
                      </span>
                      <span className="font-mono text-xs font-bold text-rose-300">
                        {sub.meses_deuda_count} meses atrasados
                      </span>
                    </div>
                    <h4 className="text-sm font-bold text-white mt-1">{sub.cliente_nombre}</h4>
                    <p className="text-[11px] text-slate-400">{sub.nombre_servicio}</p>
                    <p className="text-[10px] text-rose-300/80 mt-1 font-mono">
                      Meses: {sub.meses_deuda_detalle.join(', ') || 'Mes corriente'}
                    </p>
                  </div>

                  <div className="pt-2 border-t border-rose-500/20 flex items-center justify-between">
                    <div>
                      <p className="text-[10px] text-slate-400">Deuda + Mes Adelantado:</p>
                      <p className="text-base font-extrabold font-mono text-rose-400">
                        {sub.moneda === 'USD' ? `$${sub.monto_a_cobrar} USD` : `$${sub.monto_a_cobrar.toLocaleString()} CUP`}
                      </p>
                    </div>

                    <div className="flex items-center gap-1.5">
                      <button
                        onClick={() => handleOpenWhatsAppReminder(sub)}
                        title="Cobrar por WhatsApp"
                        className="p-1.5 rounded-lg bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20 border border-emerald-500/30"
                      >
                        <MessageSquare className="h-3.5 w-3.5" />
                      </button>
                      <button
                        onClick={() => handleSelectSubForPago(sub)}
                        className="px-2.5 py-1.5 rounded-lg bg-rose-500 hover:bg-rose-400 text-slate-950 text-xs font-bold"
                      >
                        Cobrar
                      </button>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          </div>

          {/* SECCIÓN C: ESTADO DEL PAGO EN AÑO, SEMESTRE, TRIMESTRE Y MENSUAL */}
          <div className="rounded-2xl border border-slate-800 bg-slate-900/60 p-5 shadow-sm space-y-5">
            <div className="flex flex-col sm:flex-row justify-between sm:items-center gap-3">
              <div>
                <h3 className="text-sm font-bold text-white flex items-center gap-2">
                  <TrendingUp className="h-4 w-4 text-sky-400" />
                  Evolución Temporal de Recaudación (2026)
                </h3>
                <p className="text-xs text-slate-400">
                  Desglose contable comparativo por Año, Semestre, Trimestre y Mes.
                </p>
              </div>

              {/* Selector de Período */}
              <div className="flex items-center p-1 bg-slate-950 rounded-xl border border-slate-800 text-xs">
                {(['mensual', 'trimestral', 'semestral', 'anual'] as const).map((p) => (
                  <button
                    key={p}
                    onClick={() => setPeriodoReporte(p)}
                    className={`px-3 py-1.5 rounded-lg font-bold capitalize transition-all cursor-pointer ${
                      periodoReporte === p 
                        ? 'bg-sky-500 text-slate-950 shadow-sm' 
                        : 'text-slate-400 hover:text-white'
                    }`}
                  >
                    {p}
                  </button>
                ))}
              </div>
            </div>

            {/* VISTA MENSUAL */}
            {periodoReporte === 'mensual' && (
              <div className="space-y-3">
                <div className="overflow-x-auto">
                  <table className="w-full text-left text-xs text-slate-300">
                    <thead className="bg-slate-950 text-[10px] font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-800">
                      <tr>
                        <th className="py-2.5 px-3">Mes</th>
                        <th className="py-2.5 px-3">Cobrado CUP</th>
                        <th className="py-2.5 px-3">Cobrado USD</th>
                        <th className="py-2.5 px-3">Cobros Registrados</th>
                        <th className="py-2.5 px-3">Cumplimiento Estimado</th>
                      </tr>
                    </thead>
                    <tbody className="divide-y divide-slate-800/60">
                      {reportesPeriodos.datosMensuales.map((m) => (
                        <tr key={m.key} className="hover:bg-slate-800/30">
                          <td className="py-2.5 px-3 font-semibold text-white">{m.nombre}</td>
                          <td className="py-2.5 px-3 font-mono font-bold text-emerald-400">
                            ${m.cupCobrado.toLocaleString()} CUP
                          </td>
                          <td className="py-2.5 px-3 font-mono font-bold text-sky-400">
                            ${m.usdCobrado} USD
                          </td>
                          <td className="py-2.5 px-3 text-slate-400">{m.totalOperaciones} pagos</td>
                          <td className="py-2.5 px-3">
                            <div className="flex items-center gap-2">
                              <div className="w-24 bg-slate-800 rounded-full h-2">
                                <div 
                                  className="bg-emerald-500 h-2 rounded-full" 
                                  style={{ width: `${m.cumplimiento}%` }}
                                ></div>
                              </div>
                              <span className="text-[11px] font-mono font-bold text-slate-300">{m.cumplimiento}%</span>
                            </div>
                          </td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              </div>
            )}

            {/* VISTA TRIMESTRAL */}
            {periodoReporte === 'trimestral' && (
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                {reportesPeriodos.trimestres.map((t) => (
                  <div key={t.id} className="p-4 rounded-xl border border-slate-800 bg-slate-950/60 space-y-3">
                    <div className="flex justify-between items-center">
                      <span className="font-mono text-xs font-bold text-sky-400">{t.id}</span>
                      <span className="text-xs font-bold text-emerald-400 font-mono">{t.cumplimiento}% cobrado</span>
                    </div>
                    <h4 className="text-sm font-bold text-white">{t.nombre}</h4>
                    <div className="grid grid-cols-2 gap-3 pt-2 border-t border-slate-800 text-xs">
                      <div>
                        <p className="text-[10px] text-slate-400">Recaudado CUP:</p>
                        <p className="text-sm font-extrabold font-mono text-emerald-400">${t.cupCobrado.toLocaleString()} CUP</p>
                      </div>
                      <div>
                        <p className="text-[10px] text-slate-400">Recaudado USD:</p>
                        <p className="text-sm font-extrabold font-mono text-sky-400">${t.usdCobrado} USD</p>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            )}

            {/* VISTA SEMESTRAL */}
            {periodoReporte === 'semestral' && (
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                {reportesPeriodos.semestres.map((s) => (
                  <div key={s.id} className="p-5 rounded-2xl border border-slate-800 bg-slate-950/60 space-y-4">
                    <div className="flex justify-between items-center">
                      <span className="font-mono text-xs font-bold text-indigo-400">{s.id}</span>
                      <span className="text-xs font-bold text-emerald-400 font-mono">{s.cumplimiento}% efectividad</span>
                    </div>
                    <h4 className="text-base font-bold text-white">{s.nombre}</h4>
                    <div className="space-y-2 pt-2 border-t border-slate-800 text-xs">
                      <div className="flex justify-between text-slate-400">
                        <span>Total CUP Cobrado:</span>
                        <span className="font-mono font-bold text-emerald-400">${s.cupCobrado.toLocaleString()} CUP</span>
                      </div>
                      <div className="flex justify-between text-slate-400">
                        <span>Total USD Cobrado:</span>
                        <span className="font-mono font-bold text-sky-400">${s.usdCobrado} USD</span>
                      </div>
                      <div className="flex justify-between text-slate-400">
                        <span>Operaciones Verificadas:</span>
                        <span className="text-white font-semibold">{s.operaciones}</span>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            )}

            {/* VISTA ANUAL */}
            {periodoReporte === 'anual' && (
              <div className="p-6 rounded-2xl border border-slate-800 bg-gradient-to-r from-slate-950 to-slate-900 space-y-4">
                <div className="flex justify-between items-center">
                  <h4 className="text-base font-bold text-white">Consolidado Anual Ejercicio 2026</h4>
                  <span className="font-mono text-xs font-bold text-emerald-400 bg-emerald-500/10 px-3 py-1 rounded-full border border-emerald-500/20">
                    {reportesPeriodos.anual.cumplimientoGlobal}% Cumplimiento Anual
                  </span>
                </div>
                <div className="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 border-t border-slate-800">
                  <div>
                    <p className="text-xs text-slate-400 font-semibold">Total Cobrado en CUP</p>
                    <p className="text-2xl font-extrabold text-emerald-400 font-mono mt-1">
                      ${reportesPeriodos.anual.totalAnualCUP.toLocaleString()} CUP
                    </p>
                  </div>
                  <div>
                    <p className="text-xs text-slate-400 font-semibold">Total Cobrado en USD</p>
                    <p className="text-2xl font-extrabold text-sky-400 font-mono mt-1">
                      ${reportesPeriodos.anual.totalAnualUSD} USD
                    </p>
                  </div>
                  <div>
                    <p className="text-xs text-slate-400 font-semibold">Operaciones Registradas</p>
                    <p className="text-2xl font-extrabold text-white font-mono mt-1">
                      {reportesPeriodos.anual.operacionesTotales} transferencias & cobros
                    </p>
                  </div>
                </div>
              </div>
            )}
          </div>
        </div>
      )}

      {/* ================= MODAL: REGISTRAR COBRO / PAGO ================= */}
      {isPagoModalOpen && selectedSub && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/75 backdrop-blur-sm p-4 overflow-y-auto">
          <div className="relative w-full max-w-lg rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-2xl my-8">
            <div className="flex items-center justify-between border-b border-slate-800 pb-3 mb-4">
              <div className="flex items-center gap-2">
                <div className="p-1.5 rounded-lg bg-emerald-500/10 text-emerald-400">
                  <CreditCard className="h-5 w-5" />
                </div>
                <div>
                  <h3 className="text-sm font-bold text-white">Registrar Cobro de Mensualidad</h3>
                  <p className="text-[11px] text-slate-400">Gestión Remota · Mes Adelantado (1-15 días)</p>
                </div>
              </div>
              <button onClick={() => setIsPagoModalOpen(false)} className="text-slate-400 hover:text-white">
                <X className="h-4 w-4" />
              </button>
            </div>

            <form onSubmit={handleSavePago} className="space-y-4">
              {/* Selección de Cliente / Suscripción */}
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
                  className="w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-emerald-500 focus:outline-none"
                >
                  {suscripciones.map(s => (
                    <option key={s.id} value={s.id}>
                      {s.cliente_nombre} - {s.nombre_servicio} ({s.moneda === 'USD' ? `$${s.tarifa_mensual} USD` : `$${s.tarifa_mensual.toLocaleString()} CUP`})
                    </option>
                  ))}
                </select>
              </div>

              {/* Banner de Estado de Deuda Previa */}
              <div className={`p-3 rounded-xl border text-xs space-y-1 ${
                selectedSub.tiene_deuda 
                  ? 'border-rose-500/30 bg-rose-950/20 text-rose-300' 
                  : 'border-slate-800 bg-slate-950/40 text-slate-300'
              }`}>
                <div className="flex justify-between font-semibold">
                  <span>Tarifa Mensual Base:</span>
                  <span className="font-mono">
                    {selectedSub.moneda === 'USD' ? `$${selectedSub.tarifa_mensual} USD` : `$${selectedSub.tarifa_mensual.toLocaleString()} CUP`}
                  </span>
                </div>
                {selectedSub.tiene_deuda && (
                  <div className="flex justify-between font-semibold text-rose-400">
                    <span>Deuda Anterior ({selectedSub.meses_deuda_count} meses):</span>
                    <span className="font-mono">
                      + {selectedSub.moneda === 'USD' ? `$${selectedSub.deuda_acumulada} USD` : `$${selectedSub.deuda_acumulada.toLocaleString()} CUP`}
                    </span>
                  </div>
                )}
                <div className="flex justify-between font-bold pt-1 border-t border-slate-800 text-white">
                  <span>Monto Total a Cobrar (Deuda Incluida):</span>
                  <span className="font-mono text-emerald-400 text-sm">
                    {selectedSub.moneda === 'USD' ? `$${selectedSub.monto_a_cobrar} USD` : `$${selectedSub.monto_a_cobrar.toLocaleString()} CUP`}
                  </span>
                </div>
              </div>

              {/* Monto que pagó y Moneda */}
              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">Monto que Pagó el Cliente *</label>
                  <input
                    type="number"
                    required
                    value={pagoMonto}
                    onChange={(e) => setPagoMonto(Number(e.target.value))}
                    className="w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-emerald-400 font-mono font-bold focus:border-emerald-500 focus:outline-none"
                  />
                </div>
                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">Moneda *</label>
                  <select
                    value={pagoMoneda}
                    onChange={(e) => setPagoMoneda(e.target.value as Moneda)}
                    className="w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-emerald-500 focus:outline-none"
                  >
                    <option value="CUP">CUP (Pesos Cubanos)</option>
                    <option value="USD">USD (Dólares / Moneda Fuerte)</option>
                  </select>
                </div>
              </div>

              {/* Fecha y Día que Pagó */}
              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">Fecha en que Pagó *</label>
                  <input
                    type="date"
                    required
                    value={pagoFecha}
                    onChange={(e) => setPagoFecha(e.target.value)}
                    className="w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-emerald-500 focus:outline-none"
                  />
                </div>
                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">Período / Mes Pagado</label>
                  <input
                    type="text"
                    value={pagoPeriodo}
                    onChange={(e) => setPagoPeriodo(e.target.value)}
                    placeholder="Ej. Octubre 2026"
                    className="w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-emerald-500 focus:outline-none"
                  />
                </div>
              </div>

              {/* Método de Pago: Transferencia vs Efectivo */}
              <div>
                <label className="block text-[11px] font-medium text-slate-400 mb-1">Método de Pago *</label>
                <div className="grid grid-cols-2 gap-2">
                  <button
                    type="button"
                    onClick={() => setPagoMetodo('Transferencia')}
                    className={`p-2.5 rounded-xl border text-xs font-bold flex items-center justify-center gap-2 cursor-pointer transition-all ${
                      pagoMetodo === 'Transferencia'
                        ? 'border-sky-500 bg-sky-500/15 text-sky-300'
                        : 'border-slate-800 bg-slate-950 text-slate-400'
                    }`}
                  >
                    <CreditCard className="h-4 w-4" /> Transferencia Bancaria
                  </button>
                  <button
                    type="button"
                    onClick={() => setPagoMetodo('Efectivo')}
                    className={`p-2.5 rounded-xl border text-xs font-bold flex items-center justify-center gap-2 cursor-pointer transition-all ${
                      pagoMetodo === 'Efectivo'
                        ? 'border-emerald-500 bg-emerald-500/15 text-emerald-300'
                        : 'border-slate-800 bg-slate-950 text-slate-400'
                    }`}
                  >
                    <DollarSign className="h-4 w-4" /> Pago en Efectivo
                  </button>
                </div>
              </div>

              {/* CAMPOS ESPECÍFICOS DE TRANSFERENCIA */}
              {pagoMetodo === 'Transferencia' ? (
                <div className="p-3.5 rounded-xl border border-sky-500/20 bg-sky-950/20 space-y-3">
                  <div className="grid grid-cols-2 gap-3">
                    <div>
                      <label className="block text-[10px] font-medium text-sky-200 mb-1">Plataforma / Banco</label>
                      <select
                        value={pagoPlataforma}
                        onChange={(e) => setPagoPlataforma(e.target.value)}
                        className="w-full rounded-lg border border-slate-800 bg-slate-950 px-2.5 py-1.5 text-xs text-slate-200 focus:border-sky-500 focus:outline-none"
                      >
                        <option value="Transfermóvil">Transfermóvil</option>
                        <option value="EnZona">EnZona</option>
                        <option value="Zelle">Zelle</option>
                        <option value="TropiPay">TropiPay</option>
                        <option value="Banco Metropolitano">Banco Metropolitano</option>
                        <option value="BPA">BPA</option>
                        <option value="BANDEC">BANDEC</option>
                      </select>
                    </div>
                    <div>
                      <label className="block text-[10px] font-medium text-sky-200 mb-1">No. Transacción / Ref *</label>
                      <input
                        type="text"
                        required
                        value={pagoNumTransaccion}
                        onChange={(e) => setPagoNumTransaccion(e.target.value)}
                        placeholder="Ej. TRF-9823145"
                        className="w-full rounded-lg border border-slate-800 bg-slate-950 px-2.5 py-1.5 text-xs font-mono font-bold text-sky-400 focus:border-sky-500 focus:outline-none"
                      />
                    </div>
                  </div>

                  {/* Subida o Generación de Comprobante de WhatsApp */}
                  <div>
                    <label className="block text-[10px] font-medium text-sky-200 mb-1">
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
                        className="flex-1 flex items-center justify-center gap-1.5 p-2 rounded-lg border border-slate-700 bg-slate-800 hover:bg-slate-700 text-xs text-slate-200 cursor-pointer"
                      >
                        <Upload className="h-3.5 w-3.5" />
                        {pagoComprobanteNombre ? 'Cambiar Imagen' : 'Subir Imagen de WhatsApp'}
                      </button>
                      <button
                        type="button"
                        onClick={handleGenerateSampleVoucher}
                        className="px-2.5 py-2 rounded-lg bg-sky-500/20 text-sky-300 hover:bg-sky-500/30 text-xs font-semibold cursor-pointer border border-sky-500/30"
                        title="Generar captura de comprobante"
                      >
                        Muestra
                      </button>
                    </div>

                    {pagoComprobanteUrl && (
                      <div className="mt-2 flex items-center justify-between p-2 rounded-lg bg-slate-950 border border-slate-800">
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
                          className="text-rose-400 hover:text-rose-300 p-1"
                        >
                          <X className="h-3.5 w-3.5" />
                        </button>
                      </div>
                    )}
                  </div>
                </div>
              ) : (
                /* CAMPOS ESPECÍFICOS DE EFECTIVO */
                <div className="p-3.5 rounded-xl border border-emerald-500/20 bg-emerald-950/20 space-y-3">
                  <div className="grid grid-cols-2 gap-3">
                    <div>
                      <label className="block text-[10px] font-medium text-emerald-200 mb-1">¿Quién Recibe el Dinero? *</label>
                      <input
                        type="text"
                        required
                        value={pagoEfectivoRecibe}
                        onChange={(e) => setPagoEfectivoRecibe(e.target.value)}
                        placeholder="Ej. Enmanuel Caraballo (DataPlus)"
                        className="w-full rounded-lg border border-slate-800 bg-slate-950 px-2.5 py-1.5 text-xs text-slate-200 focus:border-emerald-500 focus:outline-none"
                      />
                    </div>
                    <div>
                      <label className="block text-[10px] font-medium text-emerald-200 mb-1">¿Quién lo Entrega? *</label>
                      <input
                        type="text"
                        required
                        value={pagoEfectivoEntrega}
                        onChange={(e) => setPagoEfectivoEntrega(e.target.value)}
                        placeholder="Ej. Administrador local / Cliente"
                        className="w-full rounded-lg border border-slate-800 bg-slate-950 px-2.5 py-1.5 text-xs text-slate-200 focus:border-emerald-500 focus:outline-none"
                      />
                    </div>
                  </div>
                </div>
              )}

              {/* Notas */}
              <div>
                <label className="block text-[11px] font-medium text-slate-400 mb-1">Notas / Observaciones</label>
                <input
                  type="text"
                  value={pagoNotas}
                  onChange={(e) => setPagoNotas(e.target.value)}
                  placeholder="Detalles adicionales del abono o comprobante"
                  className="w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-emerald-500 focus:outline-none"
                />
              </div>

              {/* Botones de acción */}
              <div className="flex justify-end gap-2 pt-3 border-t border-slate-800">
                <button
                  type="button"
                  onClick={() => setIsPagoModalOpen(false)}
                  className="rounded-xl border border-slate-700 bg-slate-800 px-3.5 py-2 text-xs font-semibold text-slate-300 cursor-pointer"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  className="rounded-xl bg-emerald-500 hover:bg-emerald-400 px-4 py-2 text-xs font-bold text-slate-950 cursor-pointer shadow-md shadow-emerald-500/20"
                >
                  Guardar Pago
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      {/* ================= MODAL: NUEVA SUSCRIPCIÓN ================= */}
      {isSubModalOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/75 backdrop-blur-sm p-4">
          <div className="relative w-full max-w-md rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-2xl">
            <div className="flex items-center justify-between border-b border-slate-800 pb-3 mb-4">
              <h3 className="text-sm font-bold text-white">Nueva Suscripción a Gestión Remota</h3>
              <button onClick={() => setIsSubModalOpen(false)} className="text-slate-400 hover:text-white">
                <X className="h-4 w-4" />
              </button>
            </div>

            <form onSubmit={handleSaveSuscripcion} className="space-y-4">
              <div>
                <label className="block text-[11px] font-medium text-slate-400 mb-1">Cliente *</label>
                <select
                  value={subClienteId}
                  onChange={(e) => setSubClienteId(e.target.value)}
                  className="w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-sky-500 focus:outline-none"
                >
                  {clientes.map(c => (
                    <option key={c.id} value={c.id}>{c.nombre} ({c.codigo})</option>
                  ))}
                </select>
              </div>

              <div>
                <label className="block text-[11px] font-medium text-slate-400 mb-1">Nombre del Servicio</label>
                <input
                  type="text"
                  required
                  value={subNombreServicio}
                  onChange={(e) => setSubNombreServicio(e.target.value)}
                  className="w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-sky-500 focus:outline-none"
                />
              </div>

              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">Solución Técnica</label>
                  <select
                    value={subTipoSolucion}
                    onChange={(e) => setSubTipoSolucion(e.target.value)}
                    className="w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-sky-500 focus:outline-none"
                  >
                    <option value="Router4g">Router 4G</option>
                    <option value="Router+Modem">Router + Módem</option>
                    <option value="Router+ADSL">Router + ADSL</option>
                    <option value="Otros">Otros</option>
                  </select>
                </div>
                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">Número SIM M2M</label>
                  <input
                    type="text"
                    value={subSimNumero}
                    onChange={(e) => setSubSimNumero(e.target.value)}
                    className="w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-sky-500 focus:outline-none"
                  />
                </div>
              </div>

              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">Moneda de Cobro</label>
                  <select
                    value={subMoneda}
                    onChange={(e) => {
                      const m = e.target.value as Moneda;
                      setSubMoneda(m);
                      setSubTarifa(m === 'USD' ? 45 : 3500);
                    }}
                    className="w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-sky-500 focus:outline-none"
                  >
                    <option value="CUP">CUP (Pesos Cubanos)</option>
                    <option value="USD">USD (Dólares)</option>
                  </select>
                </div>
                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">Tarifa Mensual</label>
                  <input
                    type="number"
                    required
                    value={subTarifa}
                    onChange={(e) => setSubTarifa(Number(e.target.value))}
                    className="w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 font-mono font-bold focus:border-sky-500 focus:outline-none"
                  />
                </div>
              </div>

              <div className="flex justify-end gap-2 pt-3 border-t border-slate-800">
                <button
                  type="button"
                  onClick={() => setIsSubModalOpen(false)}
                  className="rounded-xl border border-slate-700 bg-slate-800 px-3.5 py-2 text-xs font-semibold text-slate-300"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  className="rounded-xl bg-sky-500 hover:bg-sky-400 px-4 py-2 text-xs font-bold text-slate-950"
                >
                  Guardar Suscripción
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      {/* ================= MODAL: VISOR DE COMPROBANTE DE WHATSAPP ================= */}
      {previewVoucherUrl && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-md p-4">
          <div className="relative w-full max-w-md rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-2xl space-y-4">
            <div className="flex items-center justify-between border-b border-slate-800 pb-3">
              <div>
                <h4 className="text-sm font-bold text-white flex items-center gap-2">
                  <ImageIcon className="h-4 w-4 text-emerald-400" />
                  Comprobante WhatsApp
                </h4>
                <p className="text-[11px] text-slate-400 font-mono">
                  {previewVoucherUrl.cliente} · Ref: {previewVoucherUrl.op || previewVoucherUrl.codigo}
                </p>
              </div>
              <button
                onClick={() => setPreviewVoucherUrl(null)}
                className="p-1 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800"
              >
                <X className="h-4 w-4" />
              </button>
            </div>

            <div className="flex justify-center p-2 rounded-xl bg-slate-950 border border-slate-800/80 max-h-[70vh] overflow-auto">
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
                className="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-xs text-slate-200 font-semibold"
              >
                <Download className="h-3.5 w-3.5" />
                Descargar Imagen
              </a>

              <button
                onClick={() => setPreviewVoucherUrl(null)}
                className="px-4 py-1.5 rounded-lg bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs"
              >
                Cerrar
              </button>
            </div>
          </div>
        </div>
      )}

      {/* ================= MODAL: RECIBO OFICIAL DE PAGO DATAPLUS ================= */}
      {reciboModalData && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-md p-4">
          <div className="relative w-full max-w-md rounded-2xl border border-slate-700 bg-slate-900 p-6 shadow-2xl space-y-4">
            <div className="flex items-center justify-between border-b border-slate-800 pb-3">
              <div className="flex items-center gap-2">
                <Receipt className="h-5 w-5 text-emerald-400" />
                <div>
                  <h4 className="text-sm font-bold text-white">Recibo de Cobro de Servicio</h4>
                  <p className="text-[10px] text-slate-400 font-mono">DataPlus CUBA · {reciboModalData.codigo_pago}</p>
                </div>
              </div>
              <button onClick={() => setReciboModalData(null)} className="text-slate-400 hover:text-white">
                <X className="h-4 w-4" />
              </button>
            </div>

            {/* Formato Recibo Impreso */}
            <div className="p-4 rounded-xl bg-slate-950 border border-slate-800 font-mono text-xs space-y-3 text-slate-300">
              <div className="text-center border-b border-slate-800 pb-2">
                <p className="font-extrabold text-white text-sm">DATAPLUS PLATAFORMA</p>
                <p className="text-[10px] text-slate-400">Servicio de Gestión Remota & Enlaces 4G</p>
                <p className="text-[10px] text-emerald-400 mt-1">RECIBO DE COBRO VERIFICADO</p>
              </div>

              <div className="space-y-1 text-[11px]">
                <div className="flex justify-between">
                  <span className="text-slate-400">Código Recibo:</span>
                  <span className="font-bold text-white">{reciboModalData.codigo_pago}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-slate-400">Cliente:</span>
                  <span className="font-bold text-white">{reciboModalData.cliente_nombre}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-slate-400">Servicio:</span>
                  <span>{reciboModalData.servicio_nombre}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-slate-400">Período Liquidado:</span>
                  <span className="text-sky-400 font-bold">{reciboModalData.mes_nombre_legible}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-slate-400">Fecha de Pago:</span>
                  <span>{reciboModalData.fecha_pago} (Día {reciboModalData.dia_pago})</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-slate-400">Método de Pago:</span>
                  <span className="font-bold">{reciboModalData.metodo_pago}</span>
                </div>
                {reciboModalData.metodo_pago === 'Transferencia' ? (
                  <div className="flex justify-between">
                    <span className="text-slate-400">No. Transacción:</span>
                    <span className="text-sky-300 font-bold">{reciboModalData.numero_transaccion || 'TRF-OK'}</span>
                  </div>
                ) : (
                  <>
                    <div className="flex justify-between">
                      <span className="text-slate-400">Recibido por:</span>
                      <span>{reciboModalData.efectivo_quien_recibe}</span>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-slate-400">Entregado por:</span>
                      <span>{reciboModalData.efectivo_quien_entrega}</span>
                    </div>
                  </>
                )}
              </div>

              <div className="pt-2 border-t border-slate-800 flex justify-between items-baseline">
                <span className="text-xs font-bold text-white">TOTAL ABONADO:</span>
                <span className="text-base font-extrabold text-emerald-400">
                  {reciboModalData.moneda === 'USD' 
                    ? `$${reciboModalData.monto_pagado} USD` 
                    : `$${reciboModalData.monto_pagado.toLocaleString()} CUP`}
                </span>
              </div>
            </div>

            {/* Acciones del recibo */}
            <div className="flex justify-between items-center pt-2">
              <button
                onClick={() => handleCopyReceiptText(reciboModalData)}
                className="flex items-center gap-1.5 px-3 py-2 rounded-xl bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20 text-xs font-bold border border-emerald-500/20 cursor-pointer"
              >
                {copiedReceipt ? <Check className="h-3.5 w-3.5" /> : <Copy className="h-3.5 w-3.5" />}
                {copiedReceipt ? '¡Copiado para WhatsApp!' : 'Copiar para WhatsApp'}
              </button>

              <button
                onClick={() => window.print()}
                className="flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-slate-200 cursor-pointer"
              >
                <Printer className="h-3.5 w-3.5" />
                Imprimir
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};
