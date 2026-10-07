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
  UserAccount
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
  { id: 'tp-1', nombre: 'Instalación de CCTV & Videovigilancia', descripcion: 'Montaje de cámaras IP, cableado y grabadores NVR', activo: true, proyectos_count: 0 },
  { id: 'tp-2', nombre: 'Sistema de Alarma SACI & Anti-Intrusión', descripcion: 'Instalación de centrales DSC/Paradox y sensores de intrusión', activo: true, proyectos_count: 0 },
  { id: 'tp-3', nombre: 'Enlaces 4G y Gestión Remota', descripcion: 'Conectividad con routers 4G LTE y chips SIM Cubacel', activo: true, proyectos_count: 0 },
  { id: 'tp-4', nombre: 'Redes de Datos & WiFi Empresarial', descripcion: 'Cableado estructurado Cat6 y puntos de acceso WiFi', activo: true, proyectos_count: 0 },
  { id: 'tp-5', nombre: 'Fumigación & Control Integral de Plagas', descripcion: 'Aplicación técnica de plaguicidas y desinsectación', activo: true, proyectos_count: 0 },
  { id: 'tp-6', nombre: 'Mantenimiento Preventivo & Pólizas', descripcion: 'Revisión periódica de equipos y líneas de señal', activo: true, proyectos_count: 0 }
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
