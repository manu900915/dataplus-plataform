export type Priority = 'Baja' | 'Media' | 'Alta' | 'Critica';
export type IncidentStatus = 'Pendiente' | 'Asignada' | 'En_Progreso' | 'En_Espera' | 'Resuelta' | 'Cerrada' | 'Cancelada';
export type IncidentType = 'CCTV' | 'SACI' | 'Redes' | 'Gestion_Remota';

export type ProjectStatus = 'borrador' | 'en_progreso' | 'completado' | 'cancelado';
export type KanbanStatus = 'por_hacer' | 'en_progreso' | 'en_revision' | 'completado';
export type BudgetLineType = 'equipamiento' | 'material' | 'mano_obra' | 'transporte' | 'alimentacion' | 'otro';

export type MovementType = 'entrada' | 'salida' | 'ajuste' | 'devolucion';
export type ItemCategoryType = 'equipamiento' | 'material' | 'ambos';

export interface Cliente {
  id: string;
  codigo: string;
  tipo_persona: 'natural' | 'juridica';
  documento?: string;
  nombre: string;
  nombre_comercial?: string;
  email?: string;
  telefono?: string;
  direccion?: string;
  provincia?: string;
  municipio?: string;
  notas?: string;
  activo: boolean;
  ubicaciones: ClienteUbicacion[];
  contactos?: ContactoCliente[];
  created_at: string;
}

export interface ClienteUbicacion {
  id: string;
  cliente_id: string;
  nombre: string;
  tipo: 'residencial' | 'negocio';
  tipo_negocio_nombre?: string;
  direccion?: string;
  provincia?: string;
  municipio?: string;
  contacto_nombre?: string;
  contacto_telefono?: string;
  notas?: string;
  activo: boolean;
}

export interface ContactoCliente {
  id: string;
  cliente_id: string;
  nombre: string;
  cargo?: string;
  telefono?: string;
  email?: string;
}

export interface LineaPresupuesto {
  id: string;
  proyecto_id: string;
  tipo_linea: BudgetLineType;
  item_id?: string;
  descripcion: string;
  cantidad: number;
  costo_unitario: number;
  subtotal: number;
  descontar_inventario: boolean;
}

export interface Proyecto {
  id: string;
  codigo: string;
  nombre: string;
  tipo_proyecto_nombre: string;
  tipo_seguimiento: 'investigacion' | 'instalacion' | 'mantenimiento';
  cliente_id?: string;
  cliente_nombre?: string;
  cliente_ubicacion_id?: string;
  cliente_ubicacion_nombre?: string;
  responsable_nombre: string;
  estado: ProjectStatus;
  estado_kanban: KanbanStatus;
  fecha_inicio?: string;
  fecha_fin?: string;
  presupuesto_total: number;
  progreso: number; // 0 - 100
  descripcion?: string;
  lineas_presupuesto: LineaPresupuesto[];
  created_at: string;
  updated_at: string;
}

export interface TipoProyecto {
  id: string;
  nombre: string;
  descripcion?: string;
  activo: boolean;
  proyectos_count: number;
}

export interface TipoNegocio {
  id: string;
  nombre: string;
  descripcion?: string;
  activo: boolean;
  negocios_count: number;
}

export interface Incidencia {
  id: string;
  codigo: string;
  tipo: IncidentType;
  cliente_id: string;
  cliente_nombre: string;
  ubicacion_nombre?: string;
  direccion_incidencia?: string;
  contacto_local?: string;
  telefono_local?: string;
  titulo: string;
  descripcion: string;
  diagnostico?: string;
  tecnico_id?: string;
  tecnico_nombre?: string;
  estado: IncidentStatus;
  prioridad: Priority;
  fecha_reporte: string;
  fecha_limite?: string;
  fecha_resolucion?: string;
  solucion?: string;
  costo_estimado?: number;
  gasto_transporte?: number;
  gasto_almuerzo?: number;
  cumplio_sla?: boolean;
  requiere_repuestos: boolean;
  creado_por: string;
}

export interface ItemInventario {
  id: string;
  codigo: string;
  nombre: string;
  categoria_id: string;
  categoria_nombre: string;
  descripcion?: string;
  unidad_medida: string;
  precio_unitario: number;
  precio_costo?: number;
  stock_actual: number;
  stock_minimo: number;
  es_equipamiento: boolean;
  numero_serie?: string;
  almacen_id: string;
  almacen_nombre: string;
}

export interface CategoriaItem {
  id: string;
  nombre: string;
  descripcion?: string;
  tipo: ItemCategoryType;
  activo: boolean;
  items_count: number;
}

export interface Almacen {
  id: string;
  nombre: string;
  provincia?: string;
  municipio?: string;
  responsable?: string;
  total_items?: number;
  valor_estimado?: number;
}

export interface InventarioMovimiento {
  id: string;
  fecha: string;
  item_id: string;
  item_nombre: string;
  almacen_id: string;
  almacen_nombre: string;
  tipo: MovementType;
  cantidad: number;
  costo_unitario?: number;
  motivo?: string;
  usuario: string;
}

export interface Brigada {
  id: string;
  nombre: string;
  lider: string;
  tecnicos: string[];
  zona: string;
  servicios_activos: number;
  estado: 'disponible' | 'en_terreno' | 'mantenimiento';
  telefono_contacto: string;
}

export interface Servicio {
  id: string;
  codigo: string;
  cliente_id: string;
  cliente_nombre: string;
  cliente_ubicacion_id?: string;
  ubicacion_nombre?: string;
  tipo: 'CCTV' | 'SACI' | 'Gestion_Remota';
  brigada_id?: string;
  brigada_nombre?: string;
  estado: 'Activo' | 'Inactivo' | 'En_Reparacion' | 'Suspendido';
  fecha_instalacion?: string;
  notas?: string;
  // Campos específicos de Gestión Remota (Laravel / Filament)
  gr_tipo_solucion?: 'Router4g' | 'Router+Modem' | 'Router+ADSL' | 'Otros';
  gr_sim_numero?: string;
  gr_marca_modelo?: string;
  gr_tipo_internet?: 'Abierto' | 'Filtrado' | 'Cerrado';
  gr_recarga_por?: 'Nosotros' | 'Cliente';
  gr_recarga_monto?: number;
  gr_ultima_recarga?: string;
}

export interface Contrato {
  id: string;
  codigo: string;
  cliente_id: string;
  cliente_nombre: string;
  titulo: string;
  tipo: 'Mantenimiento CCTV' | 'Monitoreo SACI' | 'Gestión Remota & SIM' | 'Soporte Integral';
  monto_mensual: number;
  fecha_inicio: string;
  fecha_vencimiento: string;
  estado: 'vigente' | 'por_vencer' | 'vencido';
  renovacion_automatica: boolean;
}

export interface Venta {
  id: string;
  codigo: string;
  cliente_id: string;
  cliente_nombre: string;
  concepto: string;
  fecha: string;
  monto_total: number;
  metodo_pago: 'Transferencia CUP' | 'Efectivo' | 'USD / MLC';
  estado: 'cobrado' | 'pendiente' | 'anulado';
}

export interface Gasto {
  id: string;
  codigo: string;
  categoria: 'Insumos y Repuestos' | 'Combustible y Transporte' | 'Dietas y Brigadas' | 'Equipos y Herramientas' | 'Servicios y Telecom';
  concepto: string;
  fecha: string;
  monto: number;
  responsable: string;
  comprobante?: string;
}

export interface UserAccount {
  id: string;
  name: string;
  email: string;
  role: 'Administrador' | 'Supervisor' | 'Técnico' | 'Vendedor' | 'Contable';
  activo: boolean;
  ldap_uid?: string;
  phone?: string;
  created_at: string;
}
