import { 
  Cliente, 
  Proyecto, 
  Incidencia, 
  ItemInventario, 
  Almacen, 
  InventarioMovimiento, 
  Brigada, 
  Servicio,
  TipoProyecto,
  TipoNegocio,
  Contrato,
  Venta,
  Gasto,
  CategoriaItem,
  UserAccount,
  SuscripcionServicio,
  RegistroPagoServicio
} from '../types';

export const PROVINCIAS_CUBA: Record<string, string[]> = {
  'La Habana': [
    'Plaza de la Revolución', 'Playa', 'Centro Habana', 'La Habana Vieja', 
    'Diez de Octubre', 'Cerro', 'Marianao', 'La Lisa', 'Boyeros', 'Arroyo Naranjo', 'Guanabacoa'
  ],
  'Matanzas': ['Matanzas', 'Cárdenas', 'Varadero', 'Jovellanos', 'Colón', 'Jagüey Grande'],
  'Villa Clara': ['Santa Clara', 'Caibarién', 'Placetas', 'Camajuaní', 'Sagua la Grande'],
  'Cienfuegos': ['Cienfuegos', 'Cruces', 'Palmira', 'Cumanayagua'],
  'Sancti Spíritus': ['Sancti Spíritus', 'Trinidad', 'Cabaiguán', 'Jatibonico'],
  'Santiago de Cuba': ['Santiago de Cuba', 'Palma Soriano', 'Contramaestre'],
  'Holguín': ['Holguín', 'Banes', 'Gibara', 'Moa']
};

// ============================================================
// 1. CLIENTES (Limpio para inicio real en producción)
// ============================================================
export const INITIAL_CLIENTES: Cliente[] = [];

// ============================================================
// 2. PROYECTOS Y PRESUPUESTOS (Limpio)
// ============================================================
export const INITIAL_PROYECTOS: Proyecto[] = [];

// ============================================================
// 3. INCIDENCIAS TÉCNICAS (Limpio)
// ============================================================
export const INITIAL_INCIDENCIAS: Incidencia[] = [];

// ============================================================
// 4. VENTAS Y GASTOS (Limpio)
// ============================================================
export const INITIAL_VENTAS: Venta[] = [];
export const INITIAL_GASTOS: Gasto[] = [];
export const INITIAL_CONTRATOS: Contrato[] = [];
export const INITIAL_SERVICIOS: Servicio[] = [];

// Helper SVG para generar comprobantes realistas de transferencias de WhatsApp (Transfermóvil / EnZona / Zelle)
export const createSampleVoucher = (
  op: string,
  monto: string,
  fecha: string,
  cliente: string,
  banco: string = 'Transfermóvil (BPA / Metropolitano)'
): string => {
  const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="400" height="520" viewBox="0 0 400 520">
    <rect width="400" height="520" rx="16" fill="#0f172a"/>
    <rect x="0" y="0" width="400" height="90" rx="16" fill="#047857"/>
    <circle cx="50" cy="45" r="24" fill="#10b981"/>
    <path d="M42 45l6 6 12-12" stroke="#ffffff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
    <text x="85" y="40" fill="#ffffff" font-family="Arial, sans-serif" font-size="15" font-weight="bold">${banco}</text>
    <text x="85" y="60" fill="#a7f3d0" font-family="Arial, sans-serif" font-size="11">Transferencia Exitosa Bancaria</text>
    <rect x="20" y="105" width="360" height="395" rx="12" fill="#1e293b" stroke="#334155"/>
    <text x="40" y="135" fill="#94a3b8" font-family="Arial, sans-serif" font-size="11">Monto Confirmado</text>
    <text x="40" y="170" fill="#34d399" font-family="Arial, sans-serif" font-size="26" font-weight="bold">${monto}</text>
    <line x1="40" y1="195" x2="360" y2="195" stroke="#334155" stroke-dasharray="4"/>
    <text x="40" y="222" fill="#94a3b8" font-family="Arial, sans-serif" font-size="11">Beneficiario / Cuenta Destino</text>
    <text x="40" y="242" fill="#f8fafc" font-family="Arial, sans-serif" font-size="13" font-weight="600">DATAPLUS CUBA · GESTIÓN REMOTA</text>
    <text x="40" y="275" fill="#94a3b8" font-family="Arial, sans-serif" font-size="11">Cliente / Titular Ordenante</text>
    <text x="40" y="295" fill="#f8fafc" font-family="Arial, sans-serif" font-size="13" font-weight="600">${cliente}</text>
    <text x="40" y="330" fill="#94a3b8" font-family="Arial, sans-serif" font-size="11">Número de Transacción / Referencia</text>
    <text x="40" y="352" fill="#38bdf8" font-family="monospace" font-size="14" font-weight="bold">${op}</text>
    <text x="40" y="388" fill="#94a3b8" font-family="Arial, sans-serif" font-size="11">Fecha y Hora de la Operación</text>
    <text x="40" y="408" fill="#f8fafc" font-family="Arial, sans-serif" font-size="12">${fecha} - 10:24 AM</text>
    <rect x="35" y="440" width="330" height="40" rx="8" fill="#064e3b" stroke="#059669"/>
    <text x="200" y="465" text-anchor="middle" fill="#6ee7b7" font-family="Arial, sans-serif" font-size="11" font-weight="bold">COMPROBANTE RECIBIDO POR WHATSAPP</text>
  </svg>`;
  return `data:image/svg+xml;utf8,${encodeURIComponent(svg)}`;
};

// ============================================================
// 4.1. SUSCRIPCIONES DE GESTIÓN REMOTA (FACTURACIÓN MENSUAL)
// ============================================================
export const INITIAL_SUSCRIPCIONES_SERVICIO: SuscripcionServicio[] = [
  {
    id: 'sub-1',
    codigo: 'SUB-001',
    cliente_id: 'cli-1',
    cliente_nombre: 'Moraima Guanabo',
    cliente_telefono: '+53 5 200 0017',
    cliente_email: 'contacto@moraimaguanabo.cu',
    nombre_servicio: 'Gestión Remota Router 4G & Monitoreo',
    tipo_solucion: 'Router4g',
    sim_numero: '+53 5 281 9021',
    moneda: 'CUP',
    tarifa_mensual: 3500,
    dia_limite_pago: 15,
    activo: true,
    fecha_inicio: '2026-01-01',
    notas: 'Pagador habitual por Transfermóvil. Comprobante enviado por WhatsApp.',
    tiene_deuda: false,
    meses_deuda_count: 0,
    meses_deuda_detalle: [],
    deuda_acumulada: 0,
    monto_a_cobrar: 3500,
    estado_cobro_mes_actual: 'Al_Dia',
    ultimo_pago_fecha: '2026-10-05',
    ultimo_pago_monto: 3500,
    ultimo_pago_metodo: 'Transferencia',
    ultimo_pago_transaccion: 'TRF-9823145'
  },
  {
    id: 'sub-2',
    codigo: 'SUB-002',
    cliente_id: 'cli-2',
    cliente_nombre: 'Dainet Playa',
    cliente_telefono: '+53 5 200 0034',
    cliente_email: 'contacto@dainetplaya.cu',
    nombre_servicio: 'Gestión Remota Router 4G & M2M SIM',
    tipo_solucion: 'Router4g',
    sim_numero: '+53 5 330 1482',
    moneda: 'CUP',
    tarifa_mensual: 3500,
    dia_limite_pago: 15,
    activo: true,
    fecha_inicio: '2026-01-15',
    notas: 'Pago recibido esta semana vía transferencia.',
    tiene_deuda: false,
    meses_deuda_count: 0,
    meses_deuda_detalle: [],
    deuda_acumulada: 0,
    monto_a_cobrar: 3500,
    estado_cobro_mes_actual: 'Al_Dia',
    ultimo_pago_fecha: '2026-10-08',
    ultimo_pago_monto: 3500,
    ultimo_pago_metodo: 'Transferencia',
    ultimo_pago_transaccion: 'TRF-9940122'
  },
  {
    id: 'sub-3',
    codigo: 'SUB-003',
    cliente_id: 'cli-3',
    cliente_nombre: 'Restaurante El Biky',
    cliente_telefono: '+53 5 288 4410',
    cliente_email: 'admin@elbiky.cu',
    nombre_servicio: 'Gestión Remota Router 4G Dual WAN',
    tipo_solucion: 'Router4g',
    sim_numero: '+53 5 449 8192',
    moneda: 'CUP',
    tarifa_mensual: 4000,
    dia_limite_pago: 15,
    activo: true,
    fecha_inicio: '2026-02-01',
    notas: 'Pendiente de cobro mes de Octubre (estamos dentro de los primeros 15 días).',
    tiene_deuda: false,
    meses_deuda_count: 0,
    meses_deuda_detalle: [],
    deuda_acumulada: 0,
    monto_a_cobrar: 4000,
    estado_cobro_mes_actual: 'Pendiente',
    ultimo_pago_fecha: '2026-09-06',
    ultimo_pago_monto: 4000,
    ultimo_pago_metodo: 'Transferencia',
    ultimo_pago_transaccion: 'TRF-9102488'
  },
  {
    id: 'sub-4',
    codigo: 'SUB-004',
    cliente_id: 'cli-4',
    cliente_nombre: 'Hostal Habana Bella',
    cliente_telefono: '+53 5 311 9022',
    cliente_email: 'reservas@habanabella.com',
    nombre_servicio: 'Gestión Remota Enlace Satelital & Router 4G',
    tipo_solucion: 'Router4g',
    sim_numero: '+53 5 551 2938',
    moneda: 'USD',
    tarifa_mensual: 45,
    dia_limite_pago: 15,
    activo: true,
    fecha_inicio: '2026-02-15',
    notas: 'Mayor deudor en USD. Debe 2 meses atrasados (Agosto y Septiembre).',
    tiene_deuda: true,
    meses_deuda_count: 2,
    meses_deuda_detalle: ['Agosto 2026', 'Septiembre 2026'],
    deuda_acumulada: 90,
    monto_a_cobrar: 135, // 90 deuda + 45 mes corriente adelantado
    estado_cobro_mes_actual: 'En_Mora',
    ultimo_pago_fecha: '2026-07-12',
    ultimo_pago_monto: 45,
    ultimo_pago_metodo: 'Transferencia',
    ultimo_pago_transaccion: 'ZEL-710293'
  },
  {
    id: 'sub-5',
    codigo: 'SUB-005',
    cliente_id: 'cli-5',
    cliente_nombre: 'Taller Automotriz San Lázaro',
    cliente_telefono: '+53 5 440 1199',
    cliente_email: 'taller@sanlazaro.cu',
    nombre_servicio: 'Gestión Remota Router + Módem',
    tipo_solucion: 'Router+Modem',
    sim_numero: '+53 5 662 9012',
    moneda: 'CUP',
    tarifa_mensual: 3800,
    dia_limite_pago: 15,
    activo: true,
    fecha_inicio: '2026-03-01',
    notas: 'Deudor en CUP. Adeuda el mes de Septiembre 2026.',
    tiene_deuda: true,
    meses_deuda_count: 1,
    meses_deuda_detalle: ['Septiembre 2026'],
    deuda_acumulada: 3800,
    monto_a_cobrar: 7600, // 3800 deuda + 3800 mes corriente
    estado_cobro_mes_actual: 'En_Mora',
    ultimo_pago_fecha: '2026-08-11',
    ultimo_pago_monto: 3800,
    ultimo_pago_metodo: 'Efectivo',
    ultimo_pago_transaccion: 'EFE-08-11'
  },
  {
    id: 'sub-6',
    codigo: 'SUB-006',
    cliente_id: 'cli-6',
    cliente_nombre: 'Bar Roma Miramar',
    cliente_telefono: '+53 5 991 3044',
    cliente_email: 'gerencia@barromahabana.com',
    nombre_servicio: 'Gestión Remota & Monitoreo Cloud',
    tipo_solucion: 'Router4g',
    sim_numero: '+53 5 771 9901',
    moneda: 'USD',
    tarifa_mensual: 50,
    dia_limite_pago: 15,
    activo: true,
    fecha_inicio: '2026-03-15',
    notas: 'Pagó puntualmente esta semana por Zelle.',
    tiene_deuda: false,
    meses_deuda_count: 0,
    meses_deuda_detalle: [],
    deuda_acumulada: 0,
    monto_a_cobrar: 50,
    estado_cobro_mes_actual: 'Al_Dia',
    ultimo_pago_fecha: '2026-10-07',
    ultimo_pago_monto: 50,
    ultimo_pago_metodo: 'Transferencia',
    ultimo_pago_transaccion: 'ZEL-884129'
  },
  {
    id: 'sub-7',
    codigo: 'SUB-007',
    cliente_id: 'cli-7',
    cliente_nombre: 'Cafetería La Palma',
    cliente_telefono: '+53 5 321 0088',
    cliente_email: 'lapalma@palmacuba.cu',
    nombre_servicio: 'Gestión Remota Router 4G Económico',
    tipo_solucion: 'Router4g',
    sim_numero: '+53 5 882 1432',
    moneda: 'CUP',
    tarifa_mensual: 3500,
    dia_limite_pago: 15,
    activo: true,
    fecha_inicio: '2026-04-01',
    notas: 'Pagó ayer en efectivo. Enmanuel recibió el dinero.',
    tiene_deuda: false,
    meses_deuda_count: 0,
    meses_deuda_detalle: [],
    deuda_acumulada: 0,
    monto_a_cobrar: 3500,
    estado_cobro_mes_actual: 'Al_Dia',
    ultimo_pago_fecha: '2026-10-09',
    ultimo_pago_monto: 3500,
    ultimo_pago_metodo: 'Efectivo',
    ultimo_pago_transaccion: 'EFE-REC-0910'
  },
  {
    id: 'sub-8',
    codigo: 'SUB-008',
    cliente_id: 'cli-8',
    cliente_nombre: 'Distribuidora del Caribe',
    cliente_telefono: '+53 5 660 7711',
    cliente_email: 'contabilidad@distcaribe.com',
    nombre_servicio: 'Gestión Remota Redes & SACI Integral',
    tipo_solucion: 'Router4g',
    sim_numero: '+53 5 993 1184',
    moneda: 'USD',
    tarifa_mensual: 60,
    dia_limite_pago: 15,
    activo: true,
    fecha_inicio: '2026-01-10',
    notas: 'Mayor deudor crítico en USD: debe 3 meses (Julio, Agosto, Septiembre 2026).',
    tiene_deuda: true,
    meses_deuda_count: 3,
    meses_deuda_detalle: ['Julio 2026', 'Agosto 2026', 'Septiembre 2026'],
    deuda_acumulada: 180,
    monto_a_cobrar: 240, // 180 deuda + 60 mes actual
    estado_cobro_mes_actual: 'En_Mora',
    ultimo_pago_fecha: '2026-06-14',
    ultimo_pago_monto: 60,
    ultimo_pago_metodo: 'Transferencia',
    ultimo_pago_transaccion: 'TRP-10924'
  },
  {
    id: 'sub-9',
    codigo: 'SUB-009',
    cliente_id: 'cli-9',
    cliente_nombre: 'Panadería Dulce Sabor',
    cliente_telefono: '+53 5 432 9011',
    cliente_email: 'dulcesabor@pan.cu',
    nombre_servicio: 'Gestión Remota Router 4G & Monitoreo NVR',
    tipo_solucion: 'Router4g',
    sim_numero: '+53 5 210 9943',
    moneda: 'CUP',
    tarifa_mensual: 3500,
    dia_limite_pago: 15,
    activo: true,
    fecha_inicio: '2026-05-01',
    notas: 'Pendiente de cobro mes actual. Faltan 5 días del límite.',
    tiene_deuda: false,
    meses_deuda_count: 0,
    meses_deuda_detalle: [],
    deuda_acumulada: 0,
    monto_a_cobrar: 3500,
    estado_cobro_mes_actual: 'Pendiente',
    ultimo_pago_fecha: '2026-09-08',
    ultimo_pago_monto: 3500,
    ultimo_pago_metodo: 'Transferencia',
    ultimo_pago_transaccion: 'TRF-889104'
  },
  {
    id: 'sub-10',
    codigo: 'SUB-010',
    cliente_id: 'cli-10',
    cliente_nombre: 'Gimnasio Titanes Vedado',
    cliente_telefono: '+53 5 777 2200',
    cliente_email: 'fitness@titanes.cu',
    nombre_servicio: 'Gestión Remota Router 4G & Acceso Cloud',
    tipo_solucion: 'Router4g',
    sim_numero: '+53 5 119 4433',
    moneda: 'CUP',
    tarifa_mensual: 4200,
    dia_limite_pago: 15,
    activo: true,
    fecha_inicio: '2026-04-15',
    notas: 'Pagó el 3 de Octubre dentro de los primeros 15 días.',
    tiene_deuda: false,
    meses_deuda_count: 0,
    meses_deuda_detalle: [],
    deuda_acumulada: 0,
    monto_a_cobrar: 4200,
    estado_cobro_mes_actual: 'Al_Dia',
    ultimo_pago_fecha: '2026-10-03',
    ultimo_pago_monto: 4200,
    ultimo_pago_metodo: 'Transferencia',
    ultimo_pago_transaccion: 'ENZ-441092'
  }
];

// ============================================================
// 4.2. HISTORIAL DE PAGOS REGISTRADOS DE GESTIÓN REMOTA
// ============================================================
export const INITIAL_PAGOS_SERVICIO: RegistroPagoServicio[] = [
  // --- PAGOS DE LA SEMANA EN CURSO (04/10/2026 al 10/10/2026) ---
  {
    id: 'pag-101',
    codigo_pago: 'PAG-2026-0091',
    suscripcion_id: 'sub-7',
    cliente_id: 'cli-7',
    cliente_nombre: 'Cafetería La Palma',
    cliente_telefono: '+53 5 321 0088',
    servicio_nombre: 'Gestión Remota Router 4G Económico',
    mes_periodo: '2026-10',
    mes_nombre_legible: 'Octubre 2026',
    moneda: 'CUP',
    tarifa_servicio: 3500,
    deuda_previa: 0,
    meses_deuda_previos: 0,
    monto_total_exigible: 3500,
    monto_pagado: 3500,
    saldo_restante: 0,
    fecha_pago: '2026-10-09',
    dia_pago: 9,
    pago_a_tiempo: true,
    metodo_pago: 'Efectivo',
    efectivo_quien_recibe: 'Enmanuel Caraballo (DataPlus)',
    efectivo_quien_entrega: 'Alejandro Peña (Gerente La Palma)',
    notas: 'Pago mensualidad adelantada en efectivo. Entregado en el local.',
    registrado_por: 'Enmanuel Caraballo',
    created_at: '2026-10-09T14:30:00Z'
  },
  {
    id: 'pag-102',
    codigo_pago: 'PAG-2026-0090',
    suscripcion_id: 'sub-2',
    cliente_id: 'cli-2',
    cliente_nombre: 'Dainet Playa',
    cliente_telefono: '+53 5 200 0034',
    servicio_nombre: 'Gestión Remota Router 4G & M2M SIM',
    mes_periodo: '2026-10',
    mes_nombre_legible: 'Octubre 2026',
    moneda: 'CUP',
    tarifa_servicio: 3500,
    deuda_previa: 0,
    meses_deuda_previos: 0,
    monto_total_exigible: 3500,
    monto_pagado: 3500,
    saldo_restante: 0,
    fecha_pago: '2026-10-08',
    dia_pago: 8,
    pago_a_tiempo: true,
    metodo_pago: 'Transferencia',
    plataforma_transferencia: 'Transfermóvil',
    numero_transaccion: 'TRF-9940122',
    comprobante_imagen_url: createSampleVoucher('TRF-9940122', '$3,500.00 CUP', '2026-10-08', 'Dainet Playa'),
    comprobante_nombre: 'comprobante_whatsapp_dainet_octubre.jpg',
    notas: 'Comprobante recibido por WhatsApp a las 10:24 AM.',
    registrado_por: 'Enmanuel Caraballo',
    created_at: '2026-10-08T10:45:00Z'
  },
  {
    id: 'pag-103',
    codigo_pago: 'PAG-2026-0089',
    suscripcion_id: 'sub-6',
    cliente_id: 'cli-6',
    cliente_nombre: 'Bar Roma Miramar',
    cliente_telefono: '+53 5 991 3044',
    servicio_nombre: 'Gestión Remota & Monitoreo Cloud',
    mes_periodo: '2026-10',
    mes_nombre_legible: 'Octubre 2026',
    moneda: 'USD',
    tarifa_servicio: 50,
    deuda_previa: 0,
    meses_deuda_previos: 0,
    monto_total_exigible: 50,
    monto_pagado: 50,
    saldo_restante: 0,
    fecha_pago: '2026-10-07',
    dia_pago: 7,
    pago_a_tiempo: true,
    metodo_pago: 'Transferencia',
    plataforma_transferencia: 'Zelle / Transferencia Internacional',
    numero_transaccion: 'ZEL-884129',
    comprobante_imagen_url: createSampleVoucher('ZEL-884129', '$50.00 USD', '2026-10-07', 'Bar Roma Miramar', 'Zelle Payment'),
    comprobante_nombre: 'whatsapp_capture_zelle_bar_roma.png',
    notas: 'Captura de pantalla enviada por el dueño vía WhatsApp.',
    registrado_por: 'Enmanuel Caraballo',
    created_at: '2026-10-07T16:10:00Z'
  },
  {
    id: 'pag-104',
    codigo_pago: 'PAG-2026-0088',
    suscripcion_id: 'sub-1',
    cliente_id: 'cli-1',
    cliente_nombre: 'Moraima Guanabo',
    cliente_telefono: '+53 5 200 0017',
    servicio_nombre: 'Gestión Remota Router 4G & Monitoreo',
    mes_periodo: '2026-10',
    mes_nombre_legible: 'Octubre 2026',
    moneda: 'CUP',
    tarifa_servicio: 3500,
    deuda_previa: 0,
    meses_deuda_previos: 0,
    monto_total_exigible: 3500,
    monto_pagado: 3500,
    saldo_restante: 0,
    fecha_pago: '2026-10-05',
    dia_pago: 5,
    pago_a_tiempo: true,
    metodo_pago: 'Transferencia',
    plataforma_transferencia: 'Transfermóvil',
    numero_transaccion: 'TRF-9823145',
    comprobante_imagen_url: createSampleVoucher('TRF-9823145', '$3,500.00 CUP', '2026-10-05', 'Moraima Guanabo'),
    comprobante_nombre: 'comprobante_moraima_oct26.jpg',
    notas: 'Pago adelantado de octubre enviado por WhatsApp.',
    registrado_por: 'Enmanuel Caraballo',
    created_at: '2026-10-05T09:15:00Z'
  },
  // --- PAGO ANTERIOR DE OCTUBRE (DÍA 3) ---
  {
    id: 'pag-105',
    codigo_pago: 'PAG-2026-0087',
    suscripcion_id: 'sub-10',
    cliente_id: 'cli-10',
    cliente_nombre: 'Gimnasio Titanes Vedado',
    cliente_telefono: '+53 5 777 2200',
    servicio_nombre: 'Gestión Remota Router 4G & Acceso Cloud',
    mes_periodo: '2026-10',
    mes_nombre_legible: 'Octubre 2026',
    moneda: 'CUP',
    tarifa_servicio: 4200,
    deuda_previa: 0,
    meses_deuda_previos: 0,
    monto_total_exigible: 4200,
    monto_pagado: 4200,
    saldo_restante: 0,
    fecha_pago: '2026-10-03',
    dia_pago: 3,
    pago_a_tiempo: true,
    metodo_pago: 'Transferencia',
    plataforma_transferencia: 'EnZona',
    numero_transaccion: 'ENZ-441092',
    comprobante_imagen_url: createSampleVoucher('ENZ-441092', '$4,200.00 CUP', '2026-10-03', 'Gimnasio Titanes', 'EnZona Transferencia'),
    comprobante_nombre: 'recibo_enzona_titanes.png',
    notas: 'Transferencia EnZona directa verificada.',
    registrado_por: 'Enmanuel Caraballo',
    created_at: '2026-10-03T11:00:00Z'
  },
  // --- HISTORIAL SEPTIEMBRE 2026 ---
  {
    id: 'pag-091',
    codigo_pago: 'PAG-2026-0078',
    suscripcion_id: 'sub-1',
    cliente_id: 'cli-1',
    cliente_nombre: 'Moraima Guanabo',
    servicio_nombre: 'Gestión Remota Router 4G & Monitoreo',
    mes_periodo: '2026-09',
    mes_nombre_legible: 'Septiembre 2026',
    moneda: 'CUP',
    tarifa_servicio: 3500,
    deuda_previa: 0,
    meses_deuda_previos: 0,
    monto_total_exigible: 3500,
    monto_pagado: 3500,
    saldo_restante: 0,
    fecha_pago: '2026-09-04',
    dia_pago: 4,
    pago_a_tiempo: true,
    metodo_pago: 'Transferencia',
    plataforma_transferencia: 'Transfermóvil',
    numero_transaccion: 'TRF-8109231',
    comprobante_imagen_url: createSampleVoucher('TRF-8109231', '$3,500.00 CUP', '2026-09-04', 'Moraima Guanabo'),
    registrado_por: 'Enmanuel Caraballo',
    created_at: '2026-09-04T10:00:00Z'
  },
  {
    id: 'pag-092',
    codigo_pago: 'PAG-2026-0079',
    suscripcion_id: 'sub-2',
    cliente_id: 'cli-2',
    cliente_nombre: 'Dainet Playa',
    servicio_nombre: 'Gestión Remota Router 4G & M2M SIM',
    mes_periodo: '2026-09',
    mes_nombre_legible: 'Septiembre 2026',
    moneda: 'CUP',
    tarifa_servicio: 3500,
    deuda_previa: 0,
    meses_deuda_previos: 0,
    monto_total_exigible: 3500,
    monto_pagado: 3500,
    saldo_restante: 0,
    fecha_pago: '2026-09-05',
    dia_pago: 5,
    pago_a_tiempo: true,
    metodo_pago: 'Transferencia',
    numero_transaccion: 'TRF-8114092',
    comprobante_imagen_url: createSampleVoucher('TRF-8114092', '$3,500.00 CUP', '2026-09-05', 'Dainet Playa'),
    registrado_por: 'Enmanuel Caraballo',
    created_at: '2026-09-05T12:00:00Z'
  },
  {
    id: 'pag-093',
    codigo_pago: 'PAG-2026-0080',
    suscripcion_id: 'sub-3',
    cliente_id: 'cli-3',
    cliente_nombre: 'Restaurante El Biky',
    servicio_nombre: 'Gestión Remota Router 4G Dual WAN',
    mes_periodo: '2026-09',
    mes_nombre_legible: 'Septiembre 2026',
    moneda: 'CUP',
    tarifa_servicio: 4000,
    deuda_previa: 0,
    meses_deuda_previos: 0,
    monto_total_exigible: 4000,
    monto_pagado: 4000,
    saldo_restante: 0,
    fecha_pago: '2026-09-06',
    dia_pago: 6,
    pago_a_tiempo: true,
    metodo_pago: 'Transferencia',
    numero_transaccion: 'TRF-9102488',
    registrado_por: 'Enmanuel Caraballo',
    created_at: '2026-09-06T15:00:00Z'
  },
  {
    id: 'pag-094',
    codigo_pago: 'PAG-2026-0081',
    suscripcion_id: 'sub-6',
    cliente_id: 'cli-6',
    cliente_nombre: 'Bar Roma Miramar',
    servicio_nombre: 'Gestión Remota & Monitoreo Cloud',
    mes_periodo: '2026-09',
    mes_nombre_legible: 'Septiembre 2026',
    moneda: 'USD',
    tarifa_servicio: 50,
    deuda_previa: 0,
    meses_deuda_previos: 0,
    monto_total_exigible: 50,
    monto_pagado: 50,
    saldo_restante: 0,
    fecha_pago: '2026-09-10',
    dia_pago: 10,
    pago_a_tiempo: true,
    metodo_pago: 'Transferencia',
    numero_transaccion: 'ZEL-794012',
    registrado_por: 'Enmanuel Caraballo',
    created_at: '2026-09-10T11:00:00Z'
  },
  {
    id: 'pag-095',
    codigo_pago: 'PAG-2026-0082',
    suscripcion_id: 'sub-7',
    cliente_id: 'cli-7',
    cliente_nombre: 'Cafetería La Palma',
    servicio_nombre: 'Gestión Remota Router 4G Económico',
    mes_periodo: '2026-09',
    mes_nombre_legible: 'Septiembre 2026',
    moneda: 'CUP',
    tarifa_servicio: 3500,
    deuda_previa: 0,
    meses_deuda_previos: 0,
    monto_total_exigible: 3500,
    monto_pagado: 3500,
    saldo_restante: 0,
    fecha_pago: '2026-09-12',
    dia_pago: 12,
    pago_a_tiempo: true,
    metodo_pago: 'Efectivo',
    efectivo_quien_recibe: 'Enmanuel Caraballo (DataPlus)',
    efectivo_quien_entrega: 'Alejandro Peña (Gerente)',
    registrado_por: 'Enmanuel Caraballo',
    created_at: '2026-09-12T16:00:00Z'
  },
  {
    id: 'pag-096',
    codigo_pago: 'PAG-2026-0083',
    suscripcion_id: 'sub-9',
    cliente_id: 'cli-9',
    cliente_nombre: 'Panadería Dulce Sabor',
    servicio_nombre: 'Gestión Remota Router 4G & Monitoreo NVR',
    mes_periodo: '2026-09',
    mes_nombre_legible: 'Septiembre 2026',
    moneda: 'CUP',
    tarifa_servicio: 3500,
    deuda_previa: 0,
    meses_deuda_previos: 0,
    monto_total_exigible: 3500,
    monto_pagado: 3500,
    saldo_restante: 0,
    fecha_pago: '2026-09-08',
    dia_pago: 8,
    pago_a_tiempo: true,
    metodo_pago: 'Transferencia',
    numero_transaccion: 'TRF-889104',
    registrado_por: 'Enmanuel Caraballo',
    created_at: '2026-09-08T14:00:00Z'
  },
  {
    id: 'pag-097',
    codigo_pago: 'PAG-2026-0084',
    suscripcion_id: 'sub-10',
    cliente_id: 'cli-10',
    cliente_nombre: 'Gimnasio Titanes Vedado',
    servicio_nombre: 'Gestión Remota Router 4G & Acceso Cloud',
    mes_periodo: '2026-09',
    mes_nombre_legible: 'Septiembre 2026',
    moneda: 'CUP',
    tarifa_servicio: 4200,
    deuda_previa: 0,
    meses_deuda_previos: 0,
    monto_total_exigible: 4200,
    monto_pagado: 4200,
    saldo_restante: 0,
    fecha_pago: '2026-09-03',
    dia_pago: 3,
    pago_a_tiempo: true,
    metodo_pago: 'Transferencia',
    numero_transaccion: 'ENZ-399102',
    registrado_por: 'Enmanuel Caraballo',
    created_at: '2026-09-03T10:00:00Z'
  },
  // --- HISTORIAL AGOSTO 2026 ---
  {
    id: 'pag-081',
    codigo_pago: 'PAG-2026-0065',
    suscripcion_id: 'sub-1',
    cliente_id: 'cli-1',
    cliente_nombre: 'Moraima Guanabo',
    servicio_nombre: 'Gestión Remota Router 4G & Monitoreo',
    mes_periodo: '2026-08',
    mes_nombre_legible: 'Agosto 2026',
    moneda: 'CUP',
    tarifa_servicio: 3500,
    deuda_previa: 0,
    meses_deuda_previos: 0,
    monto_total_exigible: 3500,
    monto_pagado: 3500,
    saldo_restante: 0,
    fecha_pago: '2026-08-04',
    dia_pago: 4,
    pago_a_tiempo: true,
    metodo_pago: 'Transferencia',
    numero_transaccion: 'TRF-7102941',
    registrado_por: 'Enmanuel Caraballo',
    created_at: '2026-08-04T10:00:00Z'
  },
  {
    id: 'pag-082',
    codigo_pago: 'PAG-2026-0066',
    suscripcion_id: 'sub-2',
    cliente_id: 'cli-2',
    cliente_nombre: 'Dainet Playa',
    servicio_nombre: 'Gestión Remota Router 4G & M2M SIM',
    mes_periodo: '2026-08',
    mes_nombre_legible: 'Agosto 2026',
    moneda: 'CUP',
    tarifa_servicio: 3500,
    deuda_previa: 0,
    meses_deuda_previos: 0,
    monto_total_exigible: 3500,
    monto_pagado: 3500,
    saldo_restante: 0,
    fecha_pago: '2026-08-06',
    dia_pago: 6,
    pago_a_tiempo: true,
    metodo_pago: 'Transferencia',
    numero_transaccion: 'TRF-7103841',
    registrado_por: 'Enmanuel Caraballo',
    created_at: '2026-08-06T11:00:00Z'
  },
  {
    id: 'pag-083',
    codigo_pago: 'PAG-2026-0067',
    suscripcion_id: 'sub-5',
    cliente_id: 'cli-5',
    cliente_nombre: 'Taller Automotriz San Lázaro',
    servicio_nombre: 'Gestión Remota Router + Módem',
    mes_periodo: '2026-08',
    mes_nombre_legible: 'Agosto 2026',
    moneda: 'CUP',
    tarifa_servicio: 3800,
    deuda_previa: 0,
    meses_deuda_previos: 0,
    monto_total_exigible: 3800,
    monto_pagado: 3800,
    saldo_restante: 0,
    fecha_pago: '2026-08-11',
    dia_pago: 11,
    pago_a_tiempo: true,
    metodo_pago: 'Efectivo',
    efectivo_quien_recibe: 'Enmanuel Caraballo',
    efectivo_quien_entrega: 'Jorge San Lázaro',
    registrado_por: 'Enmanuel Caraballo',
    created_at: '2026-08-11T15:00:00Z'
  },
  {
    id: 'pag-084',
    codigo_pago: 'PAG-2026-0068',
    suscripcion_id: 'sub-6',
    cliente_id: 'cli-6',
    cliente_nombre: 'Bar Roma Miramar',
    servicio_nombre: 'Gestión Remota & Monitoreo Cloud',
    mes_periodo: '2026-08',
    mes_nombre_legible: 'Agosto 2026',
    moneda: 'USD',
    tarifa_servicio: 50,
    deuda_previa: 0,
    meses_deuda_previos: 0,
    monto_total_exigible: 50,
    monto_pagado: 50,
    saldo_restante: 0,
    fecha_pago: '2026-08-08',
    dia_pago: 8,
    pago_a_tiempo: true,
    metodo_pago: 'Transferencia',
    numero_transaccion: 'ZEL-610283',
    registrado_por: 'Enmanuel Caraballo',
    created_at: '2026-08-08T12:00:00Z'
  },
  // --- HISTORIAL SEMESTRE 1 (Enero - Junio 2026) ---
  {
    id: 'pag-061',
    codigo_pago: 'PAG-2026-0045',
    suscripcion_id: 'sub-1',
    cliente_id: 'cli-1',
    cliente_nombre: 'Moraima Guanabo',
    servicio_nombre: 'Gestión Remota Router 4G & Monitoreo',
    mes_periodo: '2026-06',
    mes_nombre_legible: 'Junio 2026',
    moneda: 'CUP',
    tarifa_servicio: 3500,
    deuda_previa: 0,
    meses_deuda_previos: 0,
    monto_total_exigible: 3500,
    monto_pagado: 3500,
    saldo_restante: 0,
    fecha_pago: '2026-06-03',
    dia_pago: 3,
    pago_a_tiempo: true,
    metodo_pago: 'Transferencia',
    numero_transaccion: 'TRF-590124',
    registrado_por: 'Enmanuel Caraballo',
    created_at: '2026-06-03T09:00:00Z'
  },
  {
    id: 'pag-062',
    codigo_pago: 'PAG-2026-0046',
    suscripcion_id: 'sub-8',
    cliente_id: 'cli-8',
    cliente_nombre: 'Distribuidora del Caribe',
    servicio_nombre: 'Gestión Remota Redes & SACI Integral',
    mes_periodo: '2026-06',
    mes_nombre_legible: 'Junio 2026',
    moneda: 'USD',
    tarifa_servicio: 60,
    deuda_previa: 0,
    meses_deuda_previos: 0,
    monto_total_exigible: 60,
    monto_pagado: 60,
    saldo_restante: 0,
    fecha_pago: '2026-06-14',
    dia_pago: 14,
    pago_a_tiempo: true,
    metodo_pago: 'Transferencia',
    numero_transaccion: 'TRP-10924',
    registrado_por: 'Enmanuel Caraballo',
    created_at: '2026-06-14T11:00:00Z'
  },
  {
    id: 'pag-041',
    codigo_pago: 'PAG-2026-0028',
    suscripcion_id: 'sub-1',
    cliente_id: 'cli-1',
    cliente_nombre: 'Moraima Guanabo',
    servicio_nombre: 'Gestión Remota Router 4G & Monitoreo',
    mes_periodo: '2026-04',
    mes_nombre_legible: 'Abril 2026',
    moneda: 'CUP',
    tarifa_servicio: 3500,
    deuda_previa: 0,
    meses_deuda_previos: 0,
    monto_total_exigible: 3500,
    monto_pagado: 3500,
    saldo_restante: 0,
    fecha_pago: '2026-04-05',
    dia_pago: 5,
    pago_a_tiempo: true,
    metodo_pago: 'Transferencia',
    numero_transaccion: 'TRF-401923',
    registrado_por: 'Enmanuel Caraballo',
    created_at: '2026-04-05T09:00:00Z'
  },
  {
    id: 'pag-021',
    codigo_pago: 'PAG-2026-0012',
    suscripcion_id: 'sub-1',
    cliente_id: 'cli-1',
    cliente_nombre: 'Moraima Guanabo',
    servicio_nombre: 'Gestión Remota Router 4G & Monitoreo',
    mes_periodo: '2026-02',
    mes_nombre_legible: 'Febrero 2026',
    moneda: 'CUP',
    tarifa_servicio: 3500,
    deuda_previa: 0,
    meses_deuda_previos: 0,
    monto_total_exigible: 3500,
    monto_pagado: 3500,
    saldo_restante: 0,
    fecha_pago: '2026-02-04',
    dia_pago: 4,
    pago_a_tiempo: true,
    metodo_pago: 'Transferencia',
    numero_transaccion: 'TRF-201931',
    registrado_por: 'Enmanuel Caraballo',
    created_at: '2026-02-04T09:00:00Z'
  }
];

// ============================================================
// 5. USUARIOS DEL SISTEMA (Solo 1 usuario local + LDAP Sync)
// ============================================================
export const INITIAL_USUARIOS: UserAccount[] = [
  {
    id: 'usr-1',
    name: 'Enmanuel Caraballo',
    email: 'ecaraballo@dataplus.cu',
    role: 'Administrador',
    activo: true,
    phone: '+53 5 281 9021',
    created_at: '2026-01-01'
  }
];

// Usuarios disponibles en el directorio LDAP para sincronizar
export const LDAP_DIRECTORY_USERS: UserAccount[] = [
  {
    id: 'ldap-usr-1',
    name: 'Carlos Mendoza',
    email: 'cmendoza@dataplus.cu',
    role: 'Supervisor',
    activo: true,
    ldap_uid: 'cmendoza',
    phone: '+53 5 330 1482',
    created_at: '2026-02-01'
  },
  {
    id: 'ldap-usr-2',
    name: 'Yasmany Ortiz',
    email: 'yortiz@dataplus.cu',
    role: 'Técnico',
    activo: true,
    ldap_uid: 'yortiz',
    phone: '+53 5 449 8192',
    created_at: '2026-02-01'
  },
  {
    id: 'ldap-usr-3',
    name: 'Dayron Morales',
    email: 'dmorales@dataplus.cu',
    role: 'Técnico',
    activo: true,
    ldap_uid: 'dmorales',
    phone: '+53 5 551 2938',
    created_at: '2026-02-15'
  },
  {
    id: 'ldap-usr-4',
    name: 'Lisandra Peña',
    email: 'lpena@dataplus.cu',
    role: 'Vendedor',
    activo: true,
    ldap_uid: 'lpena',
    phone: '+53 5 662 9012',
    created_at: '2026-03-01'
  },
  {
    id: 'ldap-usr-5',
    name: 'Roberto Santana',
    email: 'rsantana@dataplus.cu',
    role: 'Contable',
    activo: true,
    ldap_uid: 'rsantana',
    phone: '+53 5 771 9901',
    created_at: '2026-03-10'
  }
];

// ============================================================
// 6. CATÁLOGOS ESTRUCTURALES DEL SISTEMA
// ============================================================
export const INITIAL_TIPOS_PROYECTO: TipoProyecto[] = [
  { id: 'tp-1', nombre: 'Instalación de CCTV & Videovigilancia', descripcion: 'Montaje y configuración de cámaras de seguridad IP/HD, grabadores NVR/XVR, cableado y visualización remota.', activo: true, proyectos_count: 0 },
  { id: 'tp-2', nombre: 'Sistemas de Alarma Contra Intrusión (SACI)', descripcion: 'Instalación de centrales de alarma, sensores de movimiento, contactos magnéticos, sirenas y teclados de armado.', activo: true, proyectos_count: 0 },
  { id: 'tp-3', nombre: 'Control de Acceso & Cerraduras Electrónicas', descripcion: 'Cerraduras digitales biométricas, electroimanes, lectores RFID, pulsadores y control de asistencia.', activo: true, proyectos_count: 0 },
  { id: 'tp-4', nombre: 'Redes de Datos & Cableado Estructurado', descripcion: 'Tendido y conectorización Cat6/Cat6A, gabinetes rack, patch panels, switches y certificación de puntos de red.', activo: true, proyectos_count: 0 },
  { id: 'tp-5', nombre: 'Wi-Fi Empresarial & Enlaces Inalámbricos', descripcion: 'Puntos de acceso de alta densidad, controladores centralizados, antenas y enlaces de radio punto a punto.', activo: true, proyectos_count: 0 },
  { id: 'tp-6', nombre: 'Video Porteros & Intercomunicación IP', descripcion: 'Sistemas de timbre inteligente, monitores interiores de videoportero y apertura remota de accesos.', activo: true, proyectos_count: 0 },
  { id: 'tp-7', nombre: 'Detección de Incendio & Seguridad Perimetral', descripcion: 'Detectores de humo fotoeléctricos, estaciones manuales, sirenas estroboscópicas y sensores perimetrales.', activo: true, proyectos_count: 0 },
  { id: 'tp-8', nombre: 'Respaldo Energético & UPS para Sistemas Críticos', descripcion: 'Bancos de baterías, inversores y UPS para alimentación ininterrumpida de cámaras y servidores.', activo: true, proyectos_count: 0 },
  { id: 'tp-9', nombre: 'Mantenimiento Preventivo & Pólizas de Soporte', descripcion: 'Revisión periódica de equipos, limpieza de ópticas, ajuste de cableado y soporte técnico prioritario.', activo: true, proyectos_count: 0 },
  { id: 'tp-10', nombre: 'Investigación & Desarrollo (I+D) / Obras Especiales', descripcion: 'Proyectos de innovación tecnológica, prototipado de hardware, automatización e integraciones personalizadas.', activo: true, proyectos_count: 0 }
];

export const INITIAL_TIPOS_NEGOCIO: TipoNegocio[] = [
  { id: 'tn-1', nombre: 'Restaurante & Bar (Paladar)', descripcion: 'Establecimientos gastronómicos con cocina y salón', activo: true, negocios_count: 0 },
  { id: 'tn-2', nombre: 'Taller & Servicios Técnicos', descripcion: 'Talleres de reparación, mecánica o electrónica', activo: true, negocios_count: 0 },
  { id: 'tn-3', nombre: 'Estética, Peluquería & Salón', descripcion: 'Salones de belleza y cuidado personal', activo: true, negocios_count: 0 },
  { id: 'tn-4', nombre: 'Producción de Alimentos & Panadería', descripcion: 'Fábricas de embutidos, paneras y dulcerías', activo: true, negocios_count: 0 },
  { id: 'tn-5', nombre: 'Comercio Minorista & Tienda', descripcion: 'Puntos de venta directa y boutiques', activo: true, negocios_count: 0 },
  { id: 'tn-6', nombre: 'Residencial / Domicilio Particular', descripcion: 'Viviendas particulares y fincas residenciales', activo: true, negocios_count: 0 },
  { id: 'tn-7', nombre: 'Infraestructura TI & Servidores', descripcion: 'Nodos de red, racks y centros de datos', activo: true, negocios_count: 0 }
];

export const INITIAL_CATEGORIAS_ITEM: CategoriaItem[] = [
  { id: 'cat-1', nombre: 'CCTV - Cámaras y Dispositivos de Captura', descripcion: 'Cámaras IP, domos, tubulares/bullet, PTZ, térmicas y minidomos con audio', tipo: 'equipamiento', activo: true, items_count: 0 },
  { id: 'cat-2', nombre: 'CCTV - Grabadores y Almacenamiento (NVR / DVR)', descripcion: 'Grabadores NVR, DVR/XVR, discos duros para videovigilancia (WD Purple, SkyHawk) y tarjetas industriales', tipo: 'equipamiento', activo: true, items_count: 0 },
  { id: 'cat-3', nombre: 'SACI - Paneles de Alarma y Centrales de Control', descripcion: 'Centrales de alarma cableadas e inalámbricas, paneles de control, expansores y comunicadores GSM/IP', tipo: 'equipamiento', activo: true, items_count: 0 },
  { id: 'cat-4', nombre: 'SACI - Sensores y Detección de Intrusión', descripcion: 'Sensores de movimiento PIR interiores/exteriores, contactos magnéticos, detectores de impacto y barreras perimetrales', tipo: 'equipamiento', activo: true, items_count: 0 },
  { id: 'cat-5', nombre: 'SACI - Notificación Sonora y Señalización', descripcion: 'Sirenas exteriores con flash/estroboscopio, sirenas piezoeléctricas de interior, campanas y módulos de voz', tipo: 'equipamiento', activo: true, items_count: 0 },
  { id: 'cat-6', nombre: 'Redes, Conectividad y Gestión Remota', descripcion: 'Routers 4G/LTE industriales, módems, switches PoE/Gigabit gestionables, access points y tarjetas SIM M2M', tipo: 'equipamiento', activo: true, items_count: 0 },
  { id: 'cat-7', nombre: 'Control de Acceso e Intercomunicación', descripcion: 'Controladores de acceso IP, lectores biométricos (huella/facial), lectores RFID, electroimanes, cantoneras y pulsadores No Touch', tipo: 'equipamiento', activo: true, items_count: 0 },
  { id: 'cat-8', nombre: 'Energía, Respaldo y Protección Eléctrica', descripcion: 'Sistemas UPS ininterrumpidos, fuentes conmutadas centralizadas (12V/24V), baterías AGM (12V 4Ah/7Ah) y supresores de sobretensión', tipo: 'ambos', activo: true, items_count: 0 },
  { id: 'cat-9', nombre: 'Cableado Estructurado y Conectividad Pasiva', descripcion: 'Bobinas UTP/FTP Cat 6/6A para interior y exterior, conectores RJ45 apantallados, patch panels, jacks y patch cords', tipo: 'material', activo: true, items_count: 0 },
  { id: 'cat-10', nombre: 'Canalizaciones, Tuberías y Cajas de Conexión', descripcion: 'Tuberías conduit metálicas EMT y PVC, canaletas ranuradas/decorativas, uniones, curvas y cajas estancas IP65/IP66', tipo: 'material', activo: true, items_count: 0 },
  { id: 'cat-11', nombre: 'Fijación, Tornillería y Consumibles de Montaje', descripcion: 'Tarugos plásticos y metálicos, tornillos autoperforantes, amarres/bridas UV, cinta vulcanizada y silicona', tipo: 'material', activo: true, items_count: 0 },
  { id: 'cat-12', nombre: 'Herramientas de Trabajo e Instrumental de Medición', descripcion: 'Crimpeadoras RJ45, herramientas punch-down, probadores/testers de red, multímetros, rotomartillos y escaleras', tipo: 'equipamiento', activo: true, items_count: 0 },
  { id: 'cat-13', nombre: 'Equipos de Protección Individual (EPI / EPP)', descripcion: 'Cascos dieléctricos, arneses anticaídas de cuerpo entero, guantes técnicos, gafas de seguridad y chalecos reflectivos', tipo: 'material', activo: true, items_count: 0 }
];

export const INITIAL_ALMACENES: Almacen[] = [
  {
    id: 'alm-1',
    nombre: 'Almacén Central Vedado',
    provincia: 'La Habana',
    municipio: 'Plaza de la Revolución',
    responsable: 'Enmanuel Caraballo',
    total_items: 0,
    valor_estimado: 0
  },
  {
    id: 'alm-2',
    nombre: 'Almacén Secundario Playa',
    provincia: 'La Habana',
    municipio: 'Playa',
    responsable: 'Enmanuel Caraballo',
    total_items: 0,
    valor_estimado: 0
  }
];

export const INITIAL_ITEMS: ItemInventario[] = [];

export const INITIAL_MOVIMIENTOS: InventarioMovimiento[] = [];

export const INITIAL_BRIGADAS: Brigada[] = [
  {
    id: 'brg-1',
    nombre: 'Brigada Alfa - Instalaciones Capital',
    lider: 'Enmanuel Caraballo',
    tecnicos: ['Enmanuel Caraballo'],
    zona: 'La Habana',
    servicios_activos: 0,
    estado: 'disponible',
    telefono_contacto: '+53 5 281 9021'
  }
];

// Helper to save and load state with LocalStorage (versión limpia v2)
const STORAGE_PREFIX = 'dataplus_v2_clean_';

export function loadFromStorage<T>(key: string, defaultValue: T): T {
  try {
    const item = localStorage.getItem(STORAGE_PREFIX + key);
    if (!item) return defaultValue;
    return JSON.parse(item);
  } catch {
    return defaultValue;
  }
}

export function saveToStorage<T>(key: string, value: T): void {
  try {
    localStorage.setItem(STORAGE_PREFIX + key, JSON.stringify(value));
  } catch (err) {
    console.error('Error saving to storage', err);
  }
}
