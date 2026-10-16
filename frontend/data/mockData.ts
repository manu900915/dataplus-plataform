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
export const INITIAL_CLIENTES: Cliente[] = [
  {
    id: 'cli-la-ceiba',
    codigo: 'CLI-0005',
    tipo_persona: 'juridica',
    nombre: 'Taller Automotriz La Ceiba',
    nombre_comercial: 'Taller Automotriz La Ceiba',
    email: 'contacto@laceiba.cu',
    telefono: '+53 5409 1123',
    direccion: 'Playa, La Habana',
    provincia: 'La Habana',
    municipio: 'Playa',
    activo: true,
    created_at: '2026-01-10T00:00:00Z',
    ubicaciones: [
      {
        id: 'ub-ceiba-1',
        cliente_id: 'cli-la-ceiba',
        nombre: 'Sede Principal Taller',
        tipo: 'negocio',
        tipo_negocio_nombre: 'Taller Automotriz',
        direccion: 'Playa, La Habana',
        provincia: 'La Habana',
        municipio: 'Playa',
        contacto_nombre: 'Administración La Ceiba',
        contacto_telefono: '+53 5409 1123',
        activo: true
      }
    ]
  },
  {
    id: 'cli-caribe',
    codigo: 'CLI-0006',
    tipo_persona: 'juridica',
    nombre: 'Distribuidora del Caribe',
    nombre_comercial: 'Distribuidora del Caribe',
    email: 'ventas@caribe.cu',
    telefono: '+53 5100 2299',
    direccion: 'Centro Habana, La Habana',
    provincia: 'La Habana',
    municipio: 'Centro Habana',
    activo: true,
    created_at: '2026-01-12T00:00:00Z',
    ubicaciones: [
      {
        id: 'ub-caribe-1',
        cliente_id: 'cli-caribe',
        nombre: 'Almacén y Distribución',
        tipo: 'negocio',
        tipo_negocio_nombre: 'Distribución Mayorista',
        direccion: 'Centro Habana, La Habana',
        provincia: 'La Habana',
        municipio: 'Centro Habana',
        contacto_nombre: 'Gerencia Caribe',
        contacto_telefono: '+53 5100 2299',
        activo: true
      }
    ]
  },
  {
    id: 'cli-el-biky',
    codigo: 'CLI-0003',
    tipo_persona: 'juridica',
    nombre: 'Restaurante El Biky',
    nombre_comercial: 'Restaurante El Biky',
    email: 'reservas@elbiky.cu',
    telefono: '+53 7832 9940',
    direccion: 'Infanta y San Lázaro, Centro Habana',
    provincia: 'La Habana',
    municipio: 'Centro Habana',
    activo: true,
    created_at: '2026-01-05T00:00:00Z',
    ubicaciones: [
      {
        id: 'ub-biky-1',
        cliente_id: 'cli-el-biky',
        nombre: 'Restaurante y Dulcería El Biky',
        tipo: 'negocio',
        tipo_negocio_nombre: 'Restaurante & Bar (Paladar)',
        direccion: 'Infanta y San Lázaro, Centro Habana',
        provincia: 'La Habana',
        municipio: 'Centro Habana',
        contacto_nombre: 'Administración Biky',
        contacto_telefono: '+53 7832 9940',
        activo: true
      }
    ]
  },
  {
    id: 'cli-moraima',
    codigo: 'CLI-0001',
    tipo_persona: 'juridica',
    nombre: 'Moraima Guanabo',
    nombre_comercial: 'Moraima Guanabo',
    email: 'contacto@moraimaguanabo.cu',
    telefono: '+53 5284 9102',
    direccion: 'Guanabo, Habana del Este',
    provincia: 'La Habana',
    municipio: 'Habana del Este',
    activo: true,
    created_at: '2026-01-02T00:00:00Z',
    ubicaciones: [
      {
        id: 'ub-moraima-1',
        cliente_id: 'cli-moraima',
        nombre: 'Sede Guanabo',
        tipo: 'negocio',
        tipo_negocio_nombre: 'Comercial',
        direccion: 'Guanabo, Habana del Este',
        provincia: 'La Habana',
        municipio: 'Habana del Este',
        contacto_nombre: 'Moraima',
        contacto_telefono: '+53 5284 9102',
        activo: true
      }
    ]
  },
  {
    id: 'cli-dainet',
    codigo: 'CLI-0002',
    tipo_persona: 'juridica',
    nombre: 'Dainet Playa',
    nombre_comercial: 'Dainet Playa',
    email: 'contacto@dainetplaya.cu',
    telefono: '+53 5341 8820',
    direccion: 'Playa, La Habana',
    provincia: 'La Habana',
    municipio: 'Playa',
    activo: true,
    created_at: '2026-01-03T00:00:00Z',
    ubicaciones: [
      {
        id: 'ub-dainet-1',
        cliente_id: 'cli-dainet',
        nombre: 'Sede Playa',
        tipo: 'negocio',
        tipo_negocio_nombre: 'Comercial',
        direccion: 'Playa, La Habana',
        provincia: 'La Habana',
        municipio: 'Playa',
        contacto_nombre: 'Dainet',
        contacto_telefono: '+53 5341 8820',
        activo: true
      }
    ]
  },
  {
    id: 'cli-habana-bella',
    codigo: 'CLI-0004',
    tipo_persona: 'juridica',
    nombre: 'Hostal Habana Bella',
    nombre_comercial: 'Hostal Habana Bella',
    email: 'reservas@habanabella.cu',
    telefono: '+53 5214 7789',
    direccion: 'Vedado, Plaza de la Revolución',
    provincia: 'La Habana',
    municipio: 'Plaza de la Revolución',
    activo: true,
    created_at: '2026-01-08T00:00:00Z',
    ubicaciones: [
      {
        id: 'ub-habana-1',
        cliente_id: 'cli-habana-bella',
        nombre: 'Hostal y Habitaciones',
        tipo: 'negocio',
        tipo_negocio_nombre: 'Alojamiento & Hospedaje',
        direccion: 'Vedado, Plaza de la Revolución',
        provincia: 'La Habana',
        municipio: 'Plaza de la Revolución',
        contacto_nombre: 'Recepción Habana Bella',
        contacto_telefono: '+53 5214 7789',
        activo: true
      }
    ]
  }
];

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
export const INITIAL_SERVICIOS: Servicio[] = [
  {
    id: 'srv-001',
    codigo: 'SRV-0001',
    cliente_id: 'cli-moraima',
    cliente_nombre: 'Moraima Guanabo',
    tipo: 'Gestion_Remota',
    estado: 'Activo',
    fecha_instalacion: '2026-01-01',
    gr_tipo_solucion: 'Router4g',
    gr_marca_modelo: 'Huawei B310 4G LTE',
    gr_sim_numero: '5352849102',
    gr_tipo_internet: 'Filtrado',
    gr_recarga_por: 'Nosotros',
    gr_recarga_monto: 3500,
    gr_ultima_recarga: '2026-10-01',
    notas: 'Servicio de enlace y gestión remota activo.'
  },
  {
    id: 'srv-002',
    codigo: 'SRV-0002',
    cliente_id: 'cli-dainet',
    cliente_nombre: 'Dainet Playa',
    tipo: 'Gestion_Remota',
    estado: 'Activo',
    fecha_instalacion: '2026-02-15',
    gr_tipo_solucion: 'Router+Modem',
    gr_marca_modelo: 'MikroTik hEX S + Módem 4G',
    gr_sim_numero: '5353418820',
    gr_tipo_internet: 'Abierto',
    gr_recarga_por: 'Nosotros',
    gr_recarga_monto: 4200,
    gr_ultima_recarga: '2026-10-01',
    notas: 'Enlace de respaldo para cámaras y facturación.'
  },
  {
    id: 'srv-003',
    codigo: 'SRV-0003',
    cliente_id: 'cli-el-biky',
    cliente_nombre: 'Restaurante El Biky',
    tipo: 'Gestion_Remota',
    estado: 'Activo',
    fecha_instalacion: '2026-03-10',
    gr_tipo_solucion: 'Router4g',
    gr_marca_modelo: 'Teltonika RUT240 Industrial',
    gr_sim_numero: '5359114422',
    gr_tipo_internet: 'Cerrado',
    gr_recarga_por: 'Cliente',
    gr_recarga_monto: 75,
    gr_ultima_recarga: '2026-09-01',
    notas: 'VPN túnel seguro a central de monitoreo.'
  },
  {
    id: 'srv-004',
    codigo: 'SRV-0004',
    cliente_id: 'cli-habana-bella',
    cliente_nombre: 'Hostal Habana Bella',
    tipo: 'Gestion_Remota',
    estado: 'Activo',
    fecha_instalacion: '2026-04-05',
    gr_tipo_solucion: 'Router4g',
    gr_marca_modelo: 'TP-Link Archer MR600 4G Cat6',
    gr_sim_numero: '5352147789',
    gr_tipo_internet: 'Abierto',
    gr_recarga_por: 'Nosotros',
    gr_recarga_monto: 60,
    gr_ultima_recarga: '2026-09-01',
    notas: 'Acceso a huéspedes y monitoreo CCTV.'
  },
  {
    id: 'srv-005',
    codigo: 'SRV-0005',
    cliente_id: 'cli-la-ceiba',
    cliente_nombre: 'Taller Automotriz La Ceiba',
    tipo: 'Gestion_Remota',
    estado: 'Activo',
    fecha_instalacion: '2026-05-12',
    gr_tipo_solucion: 'Router4g',
    gr_marca_modelo: 'Huawei B315s-22',
    gr_sim_numero: '5354091123',
    gr_tipo_internet: 'Filtrado',
    gr_recarga_por: 'Nosotros',
    gr_recarga_monto: 3800,
    gr_ultima_recarga: '2026-08-01',
    notas: 'Telemetría y control de acceso vehicular.'
  },
  {
    id: 'srv-006',
    codigo: 'SRV-0006',
    cliente_id: 'cli-caribe',
    cliente_nombre: 'Distribuidora del Caribe',
    tipo: 'Gestion_Remota',
    estado: 'Activo',
    fecha_instalacion: '2026-06-20',
    gr_tipo_solucion: 'Router+Modem',
    gr_marca_modelo: 'Balanceador Dual SIM 4G',
    gr_sim_numero: '5351002299',
    gr_tipo_internet: 'Abierto',
    gr_recarga_por: 'Nosotros',
    gr_recarga_monto: 80,
    gr_ultima_recarga: '2026-07-01',
    notas: 'Doble enlace para alta disponibilidad logística.'
  }
];

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
// ============================================================
// 4.1. SUSCRIPCIONES DE GESTIÓN REMOTA (FACTURACIÓN MENSUAL)
// ============================================================
export const INITIAL_SUSCRIPCIONES_SERVICIO: SuscripcionServicio[] = [
  {
    id: 'sub-005',
    codigo: 'SUB-005',
    cliente_id: 'cli-la-ceiba',
    cliente_nombre: 'Taller Automotriz La Ceiba',
    cliente_telefono: '+53 5409 1123',
    nombre_servicio: 'Gestión Remota Router 4G & Monitoreo',
    tipo_solucion: 'Router 4G Huawei B315',
    sim_numero: '5354091123',
    moneda: 'CUP',
    tarifa_mensual: 3800,
    dia_limite_pago: 15,
    activo: true,
    fecha_inicio: '2026-08-01',
    tiene_deuda: true,
    meses_deuda_count: 2,
    meses_deuda_detalle: ['Agosto 2026', 'Septiembre 2026'],
    deuda_acumulada: 7600,
    monto_a_cobrar: 11400,
    estado_cobro_mes_actual: 'En_Mora',
    ultimo_pago_fecha: '2026-07-10',
    ultimo_pago_monto: 3800,
    ultimo_pago_metodo: 'Transferencia',
    ultimo_pago_transaccion: 'TRF-3918201'
  },
  {
    id: 'sub-006',
    codigo: 'SUB-006',
    cliente_id: 'cli-caribe',
    cliente_nombre: 'Distribuidora del Caribe',
    cliente_telefono: '+53 5100 2299',
    nombre_servicio: 'Gestión Remota Router 4G & Monitoreo',
    tipo_solucion: 'Router Balanceador 4G Dual SIM',
    sim_numero: '5351002299',
    moneda: 'USD',
    tarifa_mensual: 80,
    dia_limite_pago: 15,
    activo: true,
    fecha_inicio: '2026-07-01',
    tiene_deuda: true,
    meses_deuda_count: 3,
    meses_deuda_detalle: ['Julio 2026', 'Agosto 2026', 'Septiembre 2026'],
    deuda_acumulada: 240,
    monto_a_cobrar: 320,
    estado_cobro_mes_actual: 'En_Mora',
    ultimo_pago_fecha: '2026-06-12',
    ultimo_pago_monto: 80,
    ultimo_pago_metodo: 'Transferencia',
    ultimo_pago_transaccion: 'ZLL-901842'
  },
  {
    id: 'sub-003',
    codigo: 'SUB-003',
    cliente_id: 'cli-el-biky',
    cliente_nombre: 'Restaurante El Biky',
    cliente_telefono: '+53 7832 9940',
    nombre_servicio: 'Gestión Remota Router 4G & Monitoreo',
    tipo_solucion: 'Router Industrial Teltonika RUT240',
    sim_numero: '5359114422',
    moneda: 'USD',
    tarifa_mensual: 75,
    dia_limite_pago: 15,
    activo: true,
    fecha_inicio: '2026-09-01',
    tiene_deuda: true,
    meses_deuda_count: 1,
    meses_deuda_detalle: ['Septiembre 2026'],
    deuda_acumulada: 75,
    monto_a_cobrar: 150,
    estado_cobro_mes_actual: 'En_Mora',
    ultimo_pago_fecha: '2026-08-14',
    ultimo_pago_monto: 75,
    ultimo_pago_metodo: 'Transferencia',
    ultimo_pago_transaccion: 'TRF-881920'
  },
  {
    id: 'sub-001',
    codigo: 'SUB-001',
    cliente_id: 'cli-moraima',
    cliente_nombre: 'Moraima Guanabo',
    cliente_telefono: '+53 5284 9102',
    nombre_servicio: 'Gestión Remota Router 4G & Monitoreo',
    tipo_solucion: 'Router 4G LTE Huawei B310',
    sim_numero: '5352849102',
    moneda: 'CUP',
    tarifa_mensual: 3500,
    dia_limite_pago: 15,
    activo: true,
    fecha_inicio: '2026-01-01',
    tiene_deuda: false,
    meses_deuda_count: 0,
    meses_deuda_detalle: [],
    deuda_acumulada: 0,
    monto_a_cobrar: 3500,
    estado_cobro_mes_actual: 'Al_Dia',
    ultimo_pago_fecha: '2026-10-06',
    ultimo_pago_monto: 3500,
    ultimo_pago_metodo: 'Transferencia',
    ultimo_pago_transaccion: 'TRF-5918234'
  },
  {
    id: 'sub-002',
    codigo: 'SUB-002',
    cliente_id: 'cli-dainet',
    cliente_nombre: 'Dainet Playa',
    cliente_telefono: '+53 5341 8820',
    nombre_servicio: 'Gestión Remota Router 4G & Monitoreo',
    tipo_solucion: 'MikroTik + Módem 4G LTE',
    sim_numero: '5353418820',
    moneda: 'CUP',
    tarifa_mensual: 4200,
    dia_limite_pago: 15,
    activo: true,
    fecha_inicio: '2026-02-01',
    tiene_deuda: false,
    meses_deuda_count: 0,
    meses_deuda_detalle: [],
    deuda_acumulada: 0,
    monto_a_cobrar: 4200,
    estado_cobro_mes_actual: 'Al_Dia',
    ultimo_pago_fecha: '2026-10-08',
    ultimo_pago_monto: 4200,
    ultimo_pago_metodo: 'Transferencia',
    ultimo_pago_transaccion: 'TRF-7102941'
  },
  {
    id: 'sub-004',
    codigo: 'SUB-004',
    cliente_id: 'cli-habana-bella',
    cliente_nombre: 'Hostal Habana Bella',
    cliente_telefono: '+53 5214 7789',
    nombre_servicio: 'Gestión Remota Router 4G & Monitoreo',
    tipo_solucion: 'TP-Link Archer MR600 4G Cat6',
    sim_numero: '5352147789',
    moneda: 'USD',
    tarifa_mensual: 60,
    dia_limite_pago: 15,
    activo: true,
    fecha_inicio: '2026-03-01',
    tiene_deuda: false,
    meses_deuda_count: 0,
    meses_deuda_detalle: [],
    deuda_acumulada: 0,
    monto_a_cobrar: 60,
    estado_cobro_mes_actual: 'Pendiente',
    ultimo_pago_fecha: '2026-09-09',
    ultimo_pago_monto: 60,
    ultimo_pago_metodo: 'Transferencia',
    ultimo_pago_transaccion: 'TRF-491028'
  }
];

// ============================================================
// 4.2. HISTORIAL DE PAGOS REGISTRADOS DE GESTIÓN REMOTA
// ============================================================
export const INITIAL_PAGOS_SERVICIO: RegistroPagoServicio[] = [
  {
    id: 'pag-001',
    codigo_pago: 'PAG-2026-0001',
    suscripcion_id: 'sub-001',
    cliente_id: 'cli-moraima',
    cliente_nombre: 'Moraima Guanabo',
    cliente_telefono: '+53 5284 9102',
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
    fecha_pago: '2026-10-06',
    dia_pago: 6,
    pago_a_tiempo: true,
    metodo_pago: 'Transferencia',
    plataforma_transferencia: 'Transfermóvil',
    numero_transaccion: 'TRF-5918234',
    comprobante_imagen_url: createSampleVoucher('TRF-5918234', '$3,500.00 CUP', '2026-10-06', 'Moraima Guanabo'),
    comprobante_nombre: 'comprobante_moraima_octubre.jpg',
    registrado_por: 'Enmanuel Caraballo',
    created_at: '2026-10-06T10:15:00Z'
  },
  {
    id: 'pag-002',
    codigo_pago: 'PAG-2026-0002',
    suscripcion_id: 'sub-002',
    cliente_id: 'cli-dainet',
    cliente_nombre: 'Dainet Playa',
    cliente_telefono: '+53 5341 8820',
    servicio_nombre: 'Gestión Remota Router 4G & Monitoreo',
    mes_periodo: '2026-10',
    mes_nombre_legible: 'Octubre 2026',
    moneda: 'CUP',
    tarifa_servicio: 4200,
    deuda_previa: 0,
    meses_deuda_previos: 0,
    monto_total_exigible: 4200,
    monto_pagado: 4200,
    saldo_restante: 0,
    fecha_pago: '2026-10-08',
    dia_pago: 8,
    pago_a_tiempo: true,
    metodo_pago: 'Transferencia',
    plataforma_transferencia: 'Transfermóvil',
    numero_transaccion: 'TRF-7102941',
    comprobante_imagen_url: createSampleVoucher('TRF-7102941', '$4,200.00 CUP', '2026-10-08', 'Dainet Playa'),
    comprobante_nombre: 'comprobante_dainet_octubre.jpg',
    registrado_por: 'Enmanuel Caraballo',
    created_at: '2026-10-08T14:30:00Z'
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
const STORAGE_PREFIX = 'dataplus_v4_prod_';

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
