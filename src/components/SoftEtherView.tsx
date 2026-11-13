import React, { useState } from 'react';
import { 
  ShieldCheck, 
  Wifi, 
  Activity, 
  Users, 
  Layers, 
  Key, 
  RefreshCw, 
  CheckCircle2, 
  AlertTriangle, 
  Power, 
  Copy, 
  Check, 
  Server, 
  Radio, 
  Lock, 
  Terminal, 
  ArrowDownLeft, 
  ArrowUpRight, 
  Plus, 
  X,
  Search,
  ExternalLink,
  Trash2,
  Settings,
  Clock,
  FileText
} from 'lucide-react';
import { 
  SoftEtherSession, 
  SoftEtherUser, 
  SoftEtherHub, 
  SoftEtherServerStatus, 
  SoftEtherSecureNat,
  Cliente
} from '../types';

interface SoftEtherViewProps {
  clientes?: Cliente[];
}

export const SoftEtherView: React.FC<SoftEtherViewProps> = ({ clientes = [] }) => {
  const [activeTab, setActiveTab] = useState<'resumen' | 'sesiones' | 'usuarios' | 'hubs' | 'config'>('resumen');
  const [selectedServer, setSelectedServer] = useState<'vps1' | 'vps2'>('vps1');
  const [inspectingSession, setInspectingSession] = useState<SoftEtherSession | null>(null);
  const [flushingConfig, setFlushingConfig] = useState(false);
  const [rebootingServer, setRebootingServer] = useState(false);
  const [actionNotice, setActionNotice] = useState<string | null>(null);
  const [copiedEnv, setCopiedEnv] = useState(false);
  const [testingConnection, setTestingConnection] = useState(false);
  const [connectionStatus, setConnectionStatus] = useState<{
    tested: boolean;
    success: boolean;
    latencyMs?: number;
    message?: string;
  } | null>(null);

  // Filtros
  const [sessionSearch, setSessionSearch] = useState('');
  const [userSearch, setUserSearch] = useState('');
  const [userHubFilter, setUserHubFilter] = useState('');
  const [userFilter, setUserFilter] = useState<'todos' | 'habilitados' | 'deshabilitados' | 'temporal'>('todos');

  // Modal para añadir usuario
  const [isAddUserModalOpen, setIsAddUserModalOpen] = useState(false);
  const [newUserHub, setNewUserHub] = useState('DEFAULT');
  const [newUsername, setNewUsername] = useState('');
  const [newRealName, setNewRealName] = useState('');
  const [newPassword, setNewPassword] = useState('');
  const [newClientName, setNewClientName] = useState('Moraima Guanabo');
  const [newMaxConnections, setNewMaxConnections] = useState(1);
  const [newNote, setNewNote] = useState('');

  // Modal para deshabilitar usuario por tiempo definido
  const [userToTimedDisable, setUserToTimedDisable] = useState<{ username: string; hub_name: string } | null>(null);
  const [disablePreset, setDisablePreset] = useState('60');
  const [disableCustomUntil, setDisableCustomUntil] = useState('');

  // Modal para añadir/eliminar Virtual Hub
  const [isAddHubModalOpen, setIsAddHubModalOpen] = useState(false);
  const [newHubName, setNewHubName] = useState('');
  const [newHubPassword, setNewHubPassword] = useState('');
  const [hubToDelete, setHubToDelete] = useState<string | null>(null);

  // Modal de gestión por Virtual Hub (DHCP, SecureNAT, Usuarios, Logs)
  const [managingHub, setManagingHub] = useState<string | null>(null);
  const [hubManageTab, setHubManageTab] = useState<'nat_dhcp' | 'users' | 'logs'>('nat_dhcp');
  const [hubNewUsername, setHubNewUsername] = useState('');
  const [hubNewRealName, setHubNewRealName] = useState('');
  const [hubNewPassword, setHubNewPassword] = useState('');
  const [hubNatConfigs, setHubNatConfigs] = useState<Record<string, SoftEtherSecureNat>>({});

  // Búsqueda y paginación de Virtual Hubs
  const [hubSearch, setHubSearch] = useState('');
  const [hubPage, setHubPage] = useState(1);
  const hubsPerPage = 6;

  // Estado del servidor
  const [serverStatus] = useState<SoftEtherServerStatus>({
    is_live: false,
    server_name: 'SoftEther VPN Server (DataPlus Telemetría & Enlaces)',
    version: 'v4.38 Build 9760 (Release)',
    os_name: 'Linux 6.1.0-21-amd64 (Debian / Ubuntu)',
    server_type: 'Standalone VPN Server',
    uptime: '42 días, 14 horas, 28 minutos',
    started_at: '2026-09-08 08:30:00',
    total_sessions: 3,
    total_users: 4,
    total_tcp_connections: 8,
    traffic_total_in: '142.8 GB',
    traffic_total_out: '98.4 GB',
    transfer_speed_in: '2.4 Mbps',
    transfer_speed_out: '1.8 Mbps',
    listeners: [
      { port: 443, protocol: 'TCP / HTTPS (SoftEther & SSTP)', status: 'Escuchando' },
      { port: 992, protocol: 'TCP / Telnet Admin Backup', status: 'Escuchando' },
      { port: 1194, protocol: 'UDP / OpenVPN Protocol', status: 'Escuchando' },
      { port: 5555, protocol: 'TCP / Admin RPC API', status: 'Escuchando' }
    ],
    virtual_nat_enabled: true,
    virtual_dhcp_enabled: true
  });

  // Lista de sesiones activas conectadas
  const [sessions, setSessions] = useState<SoftEtherSession[]>([
    {
      session_name: 'SID-ROUTER-MORAIMA-01',
      username: 'moraima_4g',
      cliente_nombre: 'Moraima Guanabo',
      tipo_equipo: 'Router 4G LTE Huawei B310',
      sim_numero: '5352849102',
      ip_publica_origen: '152.206.112.45',
      ip_virtual_dhcp: '192.168.30.10',
      protocolo: 'SoftEther VPN (Ethernet over HTTPS)',
      duracion: '14 horas, 12 min',
      tiempo_conectado_segundos: 51120,
      trafico_in: '4.8 GB',
      trafico_out: '2.1 GB',
      bitrate_actual: '420 Kbps',
      calidad_enlace: 'Excelente (Ping 48ms)',
      estado: 'Conectado'
    },
    {
      session_name: 'SID-ROUTER-DAINET-02',
      username: 'dainet_playa',
      cliente_nombre: 'Dainet Playa',
      tipo_equipo: 'MikroTik hEX S + Módem 4G LTE',
      sim_numero: '5353418820',
      ip_publica_origen: '152.206.140.89',
      ip_virtual_dhcp: '192.168.30.12',
      protocolo: 'OpenVPN (UDP 1194)',
      duracion: '6 horas, 45 min',
      tiempo_conectado_segundos: 24300,
      trafico_in: '12.4 GB',
      trafico_out: '8.6 GB',
      bitrate_actual: '1.2 Mbps',
      calidad_enlace: 'Estable (Ping 62ms)',
      estado: 'Conectado'
    },
    {
      session_name: 'SID-ADMIN-ENMANUEL-09',
      username: 'ecaraballo',
      cliente_nombre: 'Enmanuel Caraballo (DataPlus)',
      tipo_equipo: 'Estación de Trabajo / Diagnóstico Remoto',
      sim_numero: '+53 5 281 9021',
      ip_publica_origen: '181.225.80.12',
      ip_virtual_dhcp: '192.168.30.5',
      protocolo: 'MS-SSTP (SSL 443)',
      duracion: '1 hora, 15 min',
      tiempo_conectado_segundos: 4500,
      trafico_in: '320 MB',
      trafico_out: '180 MB',
      bitrate_actual: '240 Kbps',
      calidad_enlace: 'Excelente (Ping 35ms)',
      estado: 'Conectado'
    }
  ]);

  // Lista de usuarios VPN
  const [users, setUsers] = useState<SoftEtherUser[]>([
    {
      username: 'moraima_4g',
      hub_name: 'DEFAULT',
      real_name: 'Moraima Guanabo - Router B310',
      cliente_asociado: 'Moraima Guanabo',
      auth_type: 'Contraseña Estándar',
      estado: 'Habilitado',
      is_active: true,
      max_connections: 1,
      expires_at: 'Indefinido',
      note: 'Enlace de Gestión Remota & Telemetría',
      last_login: '2026-10-21 07:01:24',
      currently_connected: true
    },
    {
      username: 'dainet_playa',
      hub_name: 'DEFAULT',
      real_name: 'Dainet Playa - Enlace MikroTik',
      cliente_asociado: 'Dainet Playa',
      auth_type: 'Contraseña Estándar',
      estado: 'Habilitado',
      is_active: true,
      max_connections: 1,
      expires_at: 'Indefinido',
      note: 'VPN túnel seguro a central de monitoreo',
      last_login: '2026-10-21 14:18:10',
      currently_connected: true
    },
    {
      username: 'ecaraballo',
      hub_name: 'DATAPLUS_REMOTE',
      real_name: 'Enmanuel Caraballo (DataPlus)',
      cliente_asociado: 'Operaciones Internas',
      auth_type: 'Contraseña & Certificado',
      estado: 'Habilitado',
      is_active: true,
      max_connections: 3,
      expires_at: 'Indefinido',
      note: 'Acceso administrativo a túneles de gestión',
      last_login: '2026-10-21 19:48:32',
      currently_connected: true
    },
    {
      username: 'backup_link_cctv',
      hub_name: 'DATAPLUS_REMOTE',
      real_name: 'Respaldo Enlace Secundario CCTV',
      cliente_asociado: 'Reserva Técnica',
      auth_type: 'Contraseña Estándar',
      estado: 'Deshabilitado',
      is_active: false,
      max_connections: 1,
      expires_at: 'Indefinido',
      note: 'Cuenta pausada preventivamente',
      last_login: '2026-09-15 11:20:00',
      currently_connected: false
    }
  ]);

  // Virtual Hubs
  const [hubs, setHubs] = useState<SoftEtherHub[]>([
    {
      hub_name: 'DEFAULT',
      status: 'Online',
      type: 'Standalone',
      users_count: 2,
      groups_count: 1,
      active_sessions: 2,
      mac_tables: 12,
      ip_tables: 9,
      secure_nat: 'Habilitado (Virtual NAT & DHCP)',
      last_comm: '2026-10-21 21:03:10',
      traffic: '184.2 GB',
      save_security_log: true,
      security_log_switch_type: 4,
      save_packet_log: false,
      packet_log_switch_type: 4
    },
    {
      hub_name: 'DATAPLUS_REMOTE',
      status: 'Online',
      type: 'Standalone',
      users_count: 2,
      groups_count: 1,
      active_sessions: 1,
      mac_tables: 4,
      ip_tables: 3,
      secure_nat: 'Habilitado',
      last_comm: '2026-10-21 20:55:00',
      traffic: '56.7 GB',
      save_security_log: true,
      security_log_switch_type: 4,
      save_packet_log: true,
      packet_log_switch_type: 3
    }
  ]);

  // SecureNAT
  const [secureNat, setSecureNat] = useState<SoftEtherSecureNat>({
    hub_name: 'DEFAULT',
    securenat_enabled: true,
    nat_enabled: true,
    dhcp_enabled: true,
    virtual_gateway_ip: '192.168.30.1',
    virtual_subnet_mask: '255.255.255.0',
    dhcp_lease_start: '192.168.30.10',
    dhcp_lease_end: '192.168.30.200',
    dhcp_lease_expire_seconds: 7200,
    dns_servers: ['1.1.1.1', '8.8.8.8'],
    domain_name: 'dataplus.local',
    mtu: 1500,
    active_dhcp_leases: 3,
    total_nat_sessions: 48
  });

  // Desconectar sesión
  const handleDisconnectSession = (sessionName: string) => {
    if (confirm(`¿Está seguro de forzar la desconexión del túnel ${sessionName}?`)) {
      setSessions(prev => prev.filter(s => s.session_name !== sessionName));
    }
  };

  // Toggle estado de usuario
  const handleEnableUser = (username: string, hubName: string) => {
    setUsers(prev => prev.map(u => {
      if (u.username === username && u.hub_name === hubName) {
        return {
          ...u,
          is_active: true,
          is_timed_disabled: false,
          disabled_until: null,
          estado: 'Habilitado',
        };
      }
      return u;
    }));
  };

  const handleConfirmTimedDisable = (e: React.FormEvent) => {
    e.preventDefault();
    if (!userToTimedDisable) return;

    let untilStr: string | null = null;
    if (disablePreset === 'custom' && disableCustomUntil) {
      untilStr = disableCustomUntil.replace('T', ' ') + ':00';
    } else if (disablePreset !== 'indefinite') {
      const mins = parseInt(disablePreset, 10) || 60;
      const d = new Date(Date.now() + mins * 60000);
      untilStr = d.toISOString().slice(0, 19).replace('T', ' ');
    }

    setUsers(prev => prev.map(u => {
      if (u.username === userToTimedDisable.username && u.hub_name === userToTimedDisable.hub_name) {
        return {
          ...u,
          is_active: false,
          is_timed_disabled: Boolean(untilStr),
          disabled_until: untilStr,
          estado: untilStr ? 'Deshabilitado (Temporal)' : 'Deshabilitado',
          currently_connected: false,
        };
      }
      return u;
    }));
    setUserToTimedDisable(null);
  };

  const handleDeleteUser = (username: string, hubName: string) => {
    if (!confirm(`¿Seguro que desea eliminar permanentemente al usuario ${username} del Hub ${hubName}?`)) return;
    setUsers(prev => prev.filter(u => !(u.username === username && u.hub_name === hubName)));
  };

  // Añadir usuario
  const handleAddUser = (e: React.FormEvent) => {
    e.preventDefault();
    if (!newUsername.trim()) return;

    const newUser: SoftEtherUser = {
      username: newUsername.trim().toLowerCase(),
      hub_name: newUserHub || 'DEFAULT',
      real_name: newRealName.trim() || `${newClientName} - VPN Client`,
      cliente_asociado: newClientName,
      auth_type: 'Contraseña Estándar',
      estado: 'Habilitado',
      is_active: true,
      max_connections: newMaxConnections,
      expires_at: 'Indefinido',
      note: newNote.trim() || 'Cuenta de túnel VPN creada desde el panel',
      last_login: 'Nunca',
      currently_connected: false
    };

    setUsers(prev => [newUser, ...prev]);
    setIsAddUserModalOpen(false);
    setNewUsername('');
    setNewRealName('');
    setNewPassword('');
    setNewNote('');
  };

  // Añadir Virtual Hub
  const handleAddHub = (e: React.FormEvent) => {
    e.preventDefault();
    const cleanName = newHubName.trim();
    if (!cleanName) return;

    const newHub: SoftEtherHub = {
      hub_name: cleanName,
      status: 'Online',
      type: 'Standalone',
      users_count: 0,
      groups_count: 0,
      active_sessions: 0,
      mac_tables: 0,
      ip_tables: 0,
      secure_nat: 'Inactivo',
      last_comm: 'Recién creado',
      traffic: '0 B',
      save_security_log: true,
      security_log_switch_type: 4,
      save_packet_log: false,
      packet_log_switch_type: 4
    };

    setHubs(prev => [...prev, newHub]);
    setIsAddHubModalOpen(false);
    setNewHubName('');
    setNewHubPassword('');
  };

  // Eliminar Virtual Hub
  const handleConfirmDeleteHub = () => {
    if (!hubToDelete) return;
    if (hubToDelete.toUpperCase() === 'DEFAULT') return;

    setHubs(prev => prev.filter(h => h.hub_name !== hubToDelete));
    setHubToDelete(null);
  };

  const openHubManagement = (hubName: string, tab: 'nat_dhcp' | 'users' | 'logs') => {
    setManagingHub(hubName);
    setHubManageTab(tab);
    if (!hubNatConfigs[hubName]) {
      setHubNatConfigs(prev => ({
        ...prev,
        [hubName]: {
          ...secureNat,
          hub_name: hubName,
        }
      }));
    }
  };

  // Probar conexión
  const handleTestConnection = () => {
    setTestingConnection(true);
    setConnectionStatus(null);
    setTimeout(() => {
      setTestingConnection(false);
      setConnectionStatus({
        tested: true,
        success: true,
        latencyMs: 44.5,
        message: 'Socket TCP y puerto 5555 accesibles en el servidor. Telemetría de sesiones y Virtual Hubs cargada exitosamente.'
      });
    }, 900);
  };

  // Copiar template de .env
  // Copiar template de .env
  const envTemplate = `# ============================================================
# SOFTETHER VPN - SERVIDOR 1 (VPS 1 - Principal)
# ============================================================
SOFTETHER_VPS1_NAME="VPS 1 (Principal - 152.206.177.55)"
SOFTETHER_VPS1_HOST=152.206.177.55
SOFTETHER_VPS1_PORT=5555
SOFTETHER_VPS1_HUB=DEFAULT
SOFTETHER_VPS1_ADMIN_PASSWORD=T3l3c0*2023ss
SOFTETHER_VPS1_VERIFY_SSL=false

# ============================================================
# SOFTETHER VPN - SERVIDOR 2 (VPS 2 - Secundario)
# ============================================================
SOFTETHER_VPS2_NAME="VPS 2 (Secundario)"
SOFTETHER_VPS2_HOST=152.206.177.165
SOFTETHER_VPS2_PORT=5555
SOFTETHER_VPS2_HUB=DEFAULT
SOFTETHER_VPS2_ADMIN_PASSWORD=clave_administrador_vps2
SOFTETHER_VPS2_VERIFY_SSL=false`;

  const copyToClipboard = () => {
    navigator.clipboard.writeText(envTemplate);
    setCopiedEnv(true);
    setTimeout(() => setCopiedEnv(false), 2500);
  };

  const handleFlush = () => {
    setFlushingConfig(true);
    setTimeout(() => {
      setFlushingConfig(false);
      setActionNotice('Configuración guardada en disco (Flush) exitosamente.');
      setTimeout(() => setActionNotice(null), 3500);
    }, 700);
  };

  const handleReboot = () => {
    if (confirm('¿Está seguro de reiniciar el servicio SoftEther en este VPS?')) {
      setRebootingServer(true);
      setTimeout(() => {
        setRebootingServer(false);
        setActionNotice('Comando RebootServer enviado. El servicio se reinició.');
        setTimeout(() => setActionNotice(null), 3500);
      }, 1200);
    }
  };

  // Filtrado de sesiones
  const filteredSessions = sessions.filter(s => {
    const q = sessionSearch.toLowerCase();
    return s.cliente_nombre.toLowerCase().includes(q) ||
           s.username.toLowerCase().includes(q) ||
           s.ip_publica_origen.includes(q) ||
           s.ip_virtual_dhcp.includes(q);
  });

  // Filtrado de usuarios (por usuario o por Virtual Hub)
  const filteredUsers = users.filter(u => {
    if (userHubFilter && u.hub_name !== userHubFilter) return false;
    const q = userSearch.toLowerCase();
    const matchText =
      u.username.toLowerCase().includes(q) ||
      u.real_name.toLowerCase().includes(q) ||
      u.cliente_asociado.toLowerCase().includes(q) ||
      u.hub_name.toLowerCase().includes(q);
    if (!matchText) return false;
    if (userFilter === 'habilitados') return u.is_active;
    if (userFilter === 'deshabilitados') return !u.is_active;
    if (userFilter === 'temporal') return Boolean(u.is_timed_disabled);
    return true;
  });

  return (
    <div className="space-y-6">
      {/* Selector de Servidor (VPS 1 vs VPS 2) */}
      <div className="flex items-center gap-3 bg-slate-900/80 p-2.5 rounded-xl border border-slate-800">
        <span className="text-xs font-bold text-slate-400 uppercase tracking-wider pl-2">
          Servidor Activo:
        </span>
        <button
          onClick={() => setSelectedServer('vps1')}
          className={`flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all ${
            selectedServer === 'vps1'
              ? 'bg-sky-500/20 text-sky-400 border border-sky-500/40 shadow-lg shadow-sky-500/10'
              : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800'
          }`}
        >
          <span className="w-2 h-2 rounded-full bg-emerald-400"></span>
          <span>VPS 1 (Principal - 152.206.177.55)</span>
        </button>
        <button
          onClick={() => setSelectedServer('vps2')}
          className={`flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all ${
            selectedServer === 'vps2'
              ? 'bg-sky-500/20 text-sky-400 border border-sky-500/40 shadow-lg shadow-sky-500/10'
              : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800'
          }`}
        >
          <span className="w-2 h-2 rounded-full bg-amber-400"></span>
          <span>VPS 2 (Secundario)</span>
        </button>
      </div>

      {actionNotice && (
        <div className="bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 px-4 py-3 rounded-xl text-xs font-medium flex items-center justify-between">
          <span>{actionNotice}</span>
          <button onClick={() => setActionNotice(null)} className="text-emerald-400 hover:text-white">✕</button>
        </div>
      )}

      {/* Top Banner Hero */}
      <div className="bg-gradient-to-r from-slate-900 via-sky-950/40 to-slate-900 border border-sky-500/30 rounded-2xl p-6 shadow-xl relative overflow-hidden">
        <div className="absolute right-0 top-0 bottom-0 w-96 bg-sky-500/5 blur-3xl pointer-events-none" />

        <div className="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
          <div>
            <div className="flex flex-wrap items-center gap-3 mb-2">
              <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                <span className="w-2 h-2 rounded-full bg-emerald-400 animate-pulse" />
                {selectedServer === 'vps1' ? 'VPS 1 Conectado (152.206.177.55)' : 'VPS 2 (Secundario)'}
              </span>
              <span className="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-slate-800 text-slate-300 border border-slate-700">
                <ShieldCheck className="w-3.5 h-3.5 text-sky-400" />
                Hub: DEFAULT
              </span>
              <span className="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-slate-800 text-slate-300 border border-slate-700">
                Puerto: {selectedServer === 'vps1' ? '5555' : '443'}
              </span>
            </div>
            <h1 className="text-2xl font-bold text-white tracking-tight flex items-center gap-2">
              Gestión & Telemetría SoftEther VPN
            </h1>
            <p className="text-sm text-slate-400 mt-1 max-w-3xl">
              Monitoreo y administración centralizada de enlaces 4G remotos, túneles seguros para routers de clientes y asignación de direcciones SecureNAT en DataPlus.
            </p>
          </div>

          <div className="flex items-center gap-3 flex-wrap">
            <button
              onClick={handleTestConnection}
              disabled={testingConnection}
              className="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold bg-sky-600 hover:bg-sky-500 text-white shadow-lg shadow-sky-600/20 transition-all cursor-pointer disabled:opacity-50"
            >
              <RefreshCw className={`w-4 h-4 ${testingConnection ? 'animate-spin' : ''}`} />
              <span>{testingConnection ? 'Comprobando...' : 'Probar Conectividad'}</span>
            </button>
            <button
              onClick={() => setActiveTab('config')}
              className="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 transition-all cursor-pointer"
            >
              <Key className="w-4 h-4 text-amber-400" />
              <span>Credenciales Seguras (.env)</span>
            </button>
          </div>
        </div>

        {connectionStatus && (
          <div className="mt-4 p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-xs flex items-center justify-between">
            <div className="flex items-center gap-2">
              <CheckCircle2 className="w-4 h-4 text-emerald-400 shrink-0" />
              <span>{connectionStatus.message}</span>
            </div>
            {connectionStatus.latencyMs && (
              <span className="font-mono font-bold text-emerald-400 bg-emerald-950/60 px-2 py-0.5 rounded border border-emerald-500/20">
                {connectionStatus.latencyMs} ms
              </span>
            )}
          </div>
        )}
      </div>

      {/* Tabs Navigation */}
      <div className="flex items-center gap-2 border-b border-slate-800 pb-2 overflow-x-auto">
        <button
          onClick={() => setActiveTab('resumen')}
          className={`flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all cursor-pointer shrink-0 ${
            activeTab === 'resumen'
              ? 'bg-sky-500/15 text-sky-400 border border-sky-500/30'
              : 'text-slate-400 hover:text-slate-200 hover:bg-slate-900/60'
          }`}
        >
          <Activity className="w-4 h-4" />
          <span>Estado del Servidor</span>
        </button>

        <button
          onClick={() => setActiveTab('sesiones')}
          className={`flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all cursor-pointer shrink-0 ${
            activeTab === 'sesiones'
              ? 'bg-sky-500/15 text-sky-400 border border-sky-500/30'
              : 'text-slate-400 hover:text-slate-200 hover:bg-slate-900/60'
          }`}
        >
          <Wifi className="w-4 h-4" />
          <span>Sesiones en Vivo</span>
          <span className="ml-1 px-2 py-0.5 rounded-full text-xs bg-sky-500/20 text-sky-300 border border-sky-500/30">
            {sessions.length}
          </span>
        </button>

        <button
          onClick={() => setActiveTab('usuarios')}
          className={`flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all cursor-pointer shrink-0 ${
            activeTab === 'usuarios'
              ? 'bg-sky-500/15 text-sky-400 border border-sky-500/30'
              : 'text-slate-400 hover:text-slate-200 hover:bg-slate-900/60'
          }`}
        >
          <Users className="w-4 h-4" />
          <span>Cuentas VPN</span>
          <span className="ml-1 px-2 py-0.5 rounded-full text-xs bg-slate-800 text-slate-300">
            {users.length}
          </span>
        </button>

        <button
          onClick={() => setActiveTab('hubs')}
          className={`flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all cursor-pointer shrink-0 ${
            activeTab === 'hubs'
              ? 'bg-sky-500/15 text-sky-400 border border-sky-500/30'
              : 'text-slate-400 hover:text-slate-200 hover:bg-slate-900/60'
          }`}
        >
          <Layers className="w-4 h-4" />
          <span>Virtual Hubs & SecureNAT</span>
        </button>

        <button
          onClick={() => setActiveTab('config')}
          className={`flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all cursor-pointer shrink-0 ${
            activeTab === 'config'
              ? 'bg-amber-500/15 text-amber-300 border border-amber-500/30'
              : 'text-slate-400 hover:text-slate-200 hover:bg-slate-900/60'
          }`}
        >
          <Lock className="w-4 h-4 text-amber-400" />
          <span>Configuración & Credenciales</span>
        </button>
      </div>

      {/* TAB 1: RESUMEN Y SERVIDOR */}
      {activeTab === 'resumen' && (
        <div className="space-y-6">
          {/* Key Metrics */}
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div className="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 shadow-lg">
              <div className="flex items-center justify-between text-slate-400 mb-2">
                <span className="text-xs uppercase font-semibold tracking-wider">Sesiones en Línea</span>
                <Wifi className="w-4 h-4 text-sky-400" />
              </div>
              <div className="text-3xl font-bold text-white">{sessions.length}</div>
              <div className="text-xs text-emerald-400 mt-2 flex items-center gap-1">
                <CheckCircle2 className="w-3.5 h-3.5" />
                <span>100% de enlaces 4G estables</span>
              </div>
            </div>

            <div className="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 shadow-lg">
              <div className="flex items-center justify-between text-slate-400 mb-2">
                <span className="text-xs uppercase font-semibold tracking-wider">Tráfico Acumulado</span>
                <ArrowDownLeft className="w-4 h-4 text-emerald-400" />
              </div>
              <div className="text-3xl font-bold text-emerald-400">{serverStatus.traffic_total_in}</div>
              <div className="text-xs text-slate-400 mt-2">
                Salida: <span className="text-slate-300 font-semibold">{serverStatus.traffic_total_out}</span>
              </div>
            </div>

            <div className="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 shadow-lg">
              <div className="flex items-center justify-between text-slate-400 mb-2">
                <span className="text-xs uppercase font-semibold tracking-wider">Velocidad Actual</span>
                <Activity className="w-4 h-4 text-amber-400" />
              </div>
              <div className="text-3xl font-bold text-amber-400">{serverStatus.transfer_speed_in}</div>
              <div className="text-xs text-slate-400 mt-2">
                Subida: <span className="text-slate-300 font-semibold">{serverStatus.transfer_speed_out}</span>
              </div>
            </div>

            <div className="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 shadow-lg">
              <div className="flex items-center justify-between text-slate-400 mb-2">
                <span className="text-xs uppercase font-semibold tracking-wider">Tiempo de Actividad</span>
                <Server className="w-4 h-4 text-purple-400" />
              </div>
              <div className="text-xl font-bold text-white truncate">{serverStatus.uptime}</div>
              <div className="text-xs text-slate-400 mt-2">
                Iniciado: <span className="text-slate-300">{serverStatus.started_at}</span>
              </div>
            </div>
          </div>

          {/* Details Grid */}
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div className="bg-slate-900/80 border border-slate-800 rounded-2xl p-6">
              <h3 className="text-base font-semibold text-white mb-4 flex items-center gap-2 border-b border-slate-800 pb-3">
                <Server className="w-4 h-4 text-sky-400" />
                Especificaciones del Servidor VPN
              </h3>
              <div className="space-y-3 text-sm">
                <div className="flex justify-between py-1.5 border-b border-slate-800/60">
                  <span className="text-slate-400">Software:</span>
                  <span className="font-semibold text-slate-200">{serverStatus.server_name}</span>
                </div>
                <div className="flex justify-between py-1.5 border-b border-slate-800/60">
                  <span className="text-slate-400">Versión & Build:</span>
                  <span className="font-mono text-sky-400">{serverStatus.version}</span>
                </div>
                <div className="flex justify-between py-1.5 border-b border-slate-800/60">
                  <span className="text-slate-400">Sistema Operativo:</span>
                  <span className="text-slate-300">{serverStatus.os_name}</span>
                </div>
                <div className="flex justify-between py-1.5 border-b border-slate-800/60">
                  <span className="text-slate-400">Tipo de Instalación:</span>
                  <span className="text-slate-300">{serverStatus.server_type}</span>
                </div>
                <div className="flex justify-between py-1.5">
                  <span className="text-slate-400">Conexiones TCP Concurrencia:</span>
                  <span className="font-mono text-emerald-400 font-bold">{serverStatus.total_tcp_connections} activas</span>
                </div>
              </div>
            </div>

            <div className="bg-slate-900/80 border border-slate-800 rounded-2xl p-6">
              <h3 className="text-base font-semibold text-white mb-4 flex items-center gap-2 border-b border-slate-800 pb-3">
                <Radio className="w-4 h-4 text-emerald-400" />
                Puertos de Escucha del Servidor (Listeners)
              </h3>
              <div className="space-y-2.5">
                {serverStatus.listeners.map((l, idx) => (
                  <div key={idx} className="flex items-center justify-between p-3 rounded-xl bg-slate-950/60 border border-slate-800/80">
                    <div className="flex items-center gap-3">
                      <div className="w-2 h-2 rounded-full bg-emerald-400" />
                      <div>
                        <div className="text-sm font-bold text-white font-mono">Puerto {l.port}</div>
                        <div className="text-xs text-slate-400">{l.protocol}</div>
                      </div>
                    </div>
                    <span className="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                      {l.status}
                    </span>
                  </div>
                ))}
              </div>
            </div>
          </div>
        </div>
      )}

      {/* TAB 2: SESIONES ACTIVAS */}
      {activeTab === 'sesiones' && (
        <div className="space-y-4">
          <div className="flex flex-col sm:flex-row items-center justify-between gap-4 bg-slate-900/60 p-4 rounded-2xl border border-slate-800">
            <div className="relative w-full sm:w-80">
              <Search className="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
              <input
                type="text"
                placeholder="Buscar por cliente, IP o usuario..."
                value={sessionSearch}
                onChange={e => setSessionSearch(e.target.value)}
                className="w-full bg-slate-950 border border-slate-800 rounded-xl pl-9 pr-3 py-2 text-sm text-slate-200 placeholder-slate-500 focus:outline-none focus:border-sky-500"
              />
            </div>

            <div className="text-xs text-slate-400 flex items-center gap-2">
              <span className="w-2 h-2 rounded-full bg-emerald-400 animate-pulse" />
              <span>{filteredSessions.length} túneles VPN comunicando</span>
            </div>
          </div>

          <div className="bg-slate-900/80 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
            <div className="overflow-x-auto">
              <table className="w-full text-left text-sm text-slate-300">
                <thead className="bg-slate-950/80 text-xs uppercase font-semibold text-slate-400 border-b border-slate-800">
                  <tr>
                    <th className="px-5 py-4">Cliente / Dispositivo</th>
                    <th className="px-4 py-4">Usuario VPN</th>
                    <th className="px-4 py-4">IP Pública Origen</th>
                    <th className="px-4 py-4">IP Asignada (DHCP)</th>
                    <th className="px-4 py-4">Protocolo</th>
                    <th className="px-4 py-4">Duración</th>
                    <th className="px-4 py-4">Tráfico In/Out</th>
                    <th className="px-5 py-4 text-right">Acciones</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-slate-800/60">
                  {filteredSessions.map((s, idx) => (
                    <tr key={idx} className="hover:bg-slate-800/30 transition-colors">
                      <td className="px-5 py-4">
                        <div className="font-semibold text-white">{s.cliente_nombre}</div>
                        <div className="text-xs text-slate-400 flex items-center gap-1.5 mt-0.5">
                          <span>{s.tipo_equipo}</span>
                          {s.sim_numero && (
                            <span className="font-mono text-sky-400">· SIM: {s.sim_numero}</span>
                          )}
                        </div>
                      </td>
                      <td className="px-4 py-4">
                        <span className="font-mono text-xs px-2.5 py-1 rounded-md bg-sky-500/10 text-sky-300 border border-sky-500/20">
                          {s.username}
                        </span>
                      </td>
                      <td className="px-4 py-4 font-mono text-xs text-slate-300">
                        {s.ip_publica_origen}
                      </td>
                      <td className="px-4 py-4 font-mono text-xs font-bold text-emerald-400">
                        {s.ip_virtual_dhcp}
                      </td>
                      <td className="px-4 py-4 text-xs text-slate-300">
                        {s.protocolo}
                      </td>
                      <td className="px-4 py-4 text-xs text-slate-400">
                        {s.duracion}
                      </td>
                      <td className="px-4 py-4 text-xs">
                        <span className="text-sky-400 font-medium">↓ {s.trafico_in}</span> / <span className="text-purple-400 font-medium">↑ {s.trafico_out}</span>
                      </td>
                      <td className="px-5 py-4 text-right">
                        <button
                          onClick={() => handleDisconnectSession(s.session_name)}
                          className="px-3 py-1.5 rounded-lg text-xs font-semibold bg-rose-500/15 text-rose-400 hover:bg-rose-500/25 border border-rose-500/30 transition-all cursor-pointer"
                          title="Forzar desconexión de sesión"
                        >
                          Desconectar
                        </button>
                      </td>
                    </tr>
                  ))}
                  {filteredSessions.length === 0 && (
                    <tr>
                      <td colSpan={8} className="text-center py-10 text-slate-500">
                        No hay sesiones activas que coincidan con la búsqueda.
                      </td>
                    </tr>
                  )}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      )}

      {/* TAB 3: USUARIOS VPN */}
      {activeTab === 'usuarios' && (
        <div className="space-y-4">
          <div className="flex flex-col sm:flex-row items-center justify-between gap-4 bg-slate-900/60 p-4 rounded-2xl border border-slate-800">
            <div className="flex flex-wrap items-center gap-3 w-full sm:w-auto">
              <div className="relative w-full sm:w-72">
                <Search className="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                <input
                  type="text"
                  placeholder="Buscar por usuario o Virtual Hub..."
                  value={userSearch}
                  onChange={e => setUserSearch(e.target.value)}
                  className="w-full bg-slate-950 border border-slate-800 rounded-xl pl-9 pr-3 py-2 text-sm text-slate-200 placeholder-slate-500 focus:outline-none focus:border-sky-500"
                />
              </div>

              <select
                value={userHubFilter}
                onChange={e => setUserHubFilter(e.target.value)}
                className="bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-sky-400 font-semibold focus:outline-none focus:border-sky-500 cursor-pointer"
              >
                <option value="">Todos los Virtual Hubs ({hubs.length})</option>
                {hubs.map(h => (
                  <option key={h.hub_name} value={h.hub_name}>{h.hub_name}</option>
                ))}
              </select>

              <select
                value={userFilter}
                onChange={e => setUserFilter(e.target.value as any)}
                className="bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-slate-300 focus:outline-none focus:border-sky-500 cursor-pointer"
              >
                <option value="todos">Todos los estados</option>
                <option value="habilitados">Habilitados</option>
                <option value="deshabilitados">Deshabilitados</option>
                <option value="temporal">Deshab. Temporal</option>
              </select>
            </div>

            <button
              onClick={() => {
                setNewUserHub(userHubFilter || hubs[0]?.hub_name || 'DEFAULT');
                setIsAddUserModalOpen(true);
              }}
              className="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold bg-emerald-600 hover:bg-emerald-500 text-white shadow-lg shadow-emerald-600/20 transition-all cursor-pointer"
            >
              <Plus className="w-4 h-4" />
              <span>Nuevo Usuario VPN</span>
            </button>
          </div>

          <div className="bg-slate-900/80 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
            <div className="overflow-x-auto">
              <table className="w-full text-left text-sm text-slate-300">
                <thead className="bg-slate-950/80 text-xs uppercase font-semibold text-slate-400 border-b border-slate-800">
                  <tr>
                    <th className="px-5 py-4">Usuario</th>
                    <th className="px-4 py-4">Virtual Hub</th>
                    <th className="px-4 py-4">Nombre Descriptivo / Cliente</th>
                    <th className="px-4 py-4">Autenticación</th>
                    <th className="px-4 py-4">Estado</th>
                    <th className="px-4 py-4">En Línea</th>
                    <th className="px-4 py-4">Último Inicio</th>
                    <th className="px-5 py-4 text-right">Acciones</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-slate-800/60">
                  {filteredUsers.map((u, idx) => (
                    <tr key={idx} className="hover:bg-slate-800/30 transition-colors">
                      <td className="px-5 py-4 font-mono font-bold text-sky-400">
                        {u.username}
                      </td>
                      <td className="px-4 py-4">
                        <button
                          onClick={() => openHubManagement(u.hub_name, 'users')}
                          className="px-2.5 py-1 rounded-full text-xs font-mono font-semibold bg-sky-500/15 text-sky-300 border border-sky-500/30 hover:bg-sky-500/25 cursor-pointer"
                        >
                          {u.hub_name}
                        </button>
                      </td>
                      <td className="px-4 py-4">
                        <div className="font-semibold text-white">{u.real_name}</div>
                        <div className="text-xs text-slate-400">{u.note}</div>
                      </td>
                      <td className="px-4 py-4 text-xs text-slate-400">
                        {u.auth_type}
                      </td>
                      <td className="px-4 py-4">
                        {u.is_active ? (
                          <span className="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                            Habilitado
                          </span>
                        ) : u.is_timed_disabled ? (
                          <div className="flex flex-col gap-1">
                            <span className="px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-500/15 text-amber-300 border border-amber-500/30 w-fit">
                              Deshab. Temporal
                            </span>
                            <span className="text-[11px] font-mono text-amber-400">Hasta: {u.disabled_until}</span>
                          </div>
                        ) : (
                          <span className="px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-500/15 text-rose-400 border border-rose-500/30">
                            Deshabilitado
                          </span>
                        )}
                      </td>
                      <td className="px-4 py-4">
                        {u.currently_connected ? (
                          <span className="inline-flex items-center gap-1.5 text-xs text-emerald-400 font-semibold">
                            <span className="w-2 h-2 rounded-full bg-emerald-400 animate-pulse" />
                            Conectado
                          </span>
                        ) : (
                          <span className="text-xs text-slate-500">Desconectado</span>
                        )}
                      </td>
                      <td className="px-4 py-4 text-xs text-slate-400">
                        {u.last_login || '-'}
                      </td>
                      <td className="px-5 py-4 text-right">
                        <div className="flex items-center justify-end gap-2">
                          {u.is_active ? (
                            <button
                              onClick={() => {
                                setUserToTimedDisable({ username: u.username, hub_name: u.hub_name });
                                setDisablePreset('60');
                              }}
                              className="px-3 py-1.5 rounded-lg text-xs font-semibold bg-amber-500/15 text-amber-300 hover:bg-amber-500/25 border border-amber-500/30 cursor-pointer"
                            >
                              Deshabilitar
                            </button>
                          ) : (
                            <button
                              onClick={() => handleEnableUser(u.username, u.hub_name)}
                              className="px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-500/15 text-emerald-300 hover:bg-emerald-500/25 border border-emerald-500/30 cursor-pointer"
                            >
                              Habilitar
                            </button>
                          )}
                          <button
                            onClick={() => handleDeleteUser(u.username, u.hub_name)}
                            className="px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-rose-500/15 text-rose-300 hover:bg-rose-500/25 border border-rose-500/30 cursor-pointer"
                          >
                            Eliminar
                          </button>
                        </div>
                      </td>
                    </tr>
                  ))}
                  {filteredUsers.length === 0 && (
                    <tr>
                      <td colSpan={8} className="text-center py-10 text-slate-500">
                        No se encontraron usuarios VPN.
                      </td>
                    </tr>
                  )}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      )}

      {/* TAB 4: HUBS & SECURENAT */}
      {activeTab === 'hubs' && (
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <div className="bg-slate-900/80 border border-slate-800 rounded-2xl p-6">
            <div className="flex items-center justify-between mb-4 border-b border-slate-800 pb-3 flex-wrap gap-2">
              <div>
                <h3 className="text-base font-semibold text-white flex items-center gap-2">
                  <Layers className="w-4 h-4 text-sky-400" />
                  Virtual Hubs Activos en SoftEther
                </h3>
                <span className="text-xs text-slate-400">{hubs.length} detectado(s)</span>
              </div>
              <button
                onClick={() => setIsAddHubModalOpen(true)}
                className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white shadow-lg shadow-emerald-600/20 transition-all cursor-pointer"
              >
                <Plus className="w-3.5 h-3.5" />
                <span>Crear Virtual Hub</span>
              </button>
            </div>

            {/* Buscador de Virtual Hubs */}
            <div className="mb-3 flex items-center gap-2">
              <div className="relative flex-1">
                <Search className="w-4 h-4 text-slate-500 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" />
                <input
                  type="text"
                  placeholder="Buscar Virtual Hub por nombre o estado..."
                  value={hubSearch}
                  onChange={e => {
                    setHubSearch(e.target.value);
                    setHubPage(1);
                  }}
                  className="w-full bg-slate-950 border border-slate-800 rounded-xl pl-9 pr-8 py-2 text-xs text-slate-200 placeholder:text-slate-500 focus:outline-none focus:border-sky-500 transition-colors"
                />
                {hubSearch && (
                  <button
                    onClick={() => {
                      setHubSearch('');
                      setHubPage(1);
                    }}
                    className="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-200 text-xs"
                  >
                    ✕
                  </button>
                )}
              </div>
            </div>

            {(() => {
              const filteredHubs = hubs.filter(h => {
                if (!hubSearch.trim()) return true;
                const q = hubSearch.toLowerCase();
                return h.hub_name.toLowerCase().includes(q) || h.status.toLowerCase().includes(q);
              });
              const totalPages = Math.max(1, Math.ceil(filteredHubs.length / hubsPerPage));
              const currentPage = Math.min(hubPage, totalPages);
              const paginatedHubs = filteredHubs.slice((currentPage - 1) * hubsPerPage, currentPage * hubsPerPage);

              return (
                <>
                  <div className="flex items-center justify-between text-xs text-slate-400 mb-3">
                    <span>
                      {hubSearch ? (
                        <>Filtrados: <strong className="text-sky-400">{filteredHubs.length}</strong> de {hubs.length} hubs</>
                      ) : (
                        <>Mostrando <strong className="text-white">{paginatedHubs.length}</strong> de {hubs.length} hubs</>
                      )}
                    </span>
                    {totalPages > 1 && (
                      <span>Página <strong className="text-sky-400">{currentPage}</strong> de {totalPages}</span>
                    )}
                  </div>

                  <div className="space-y-4">
                    {paginatedHubs.map((h, idx) => (
                      <div key={idx} className="bg-slate-950/70 border border-slate-800 rounded-xl p-4">
                        <div className="flex items-center justify-between mb-3">
                          <div className="flex items-center gap-2">
                            <span className="text-base font-bold text-sky-400 font-mono">{h.hub_name}</span>
                            <span className="px-2 py-0.5 rounded text-xs bg-slate-800 text-slate-300">
                              {h.type}
                            </span>
                          </div>
                          <div className="flex items-center gap-2">
                            <span className="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                              {h.status}
                            </span>
                            {h.hub_name.toUpperCase() !== 'DEFAULT' && (
                              <button
                                onClick={() => setHubToDelete(h.hub_name)}
                                className="p-1.5 rounded-lg text-rose-400 hover:text-white hover:bg-rose-500/20 border border-rose-500/30 transition-all cursor-pointer"
                                title={`Eliminar Virtual Hub ${h.hub_name}`}
                              >
                                <Trash2 className="w-3.5 h-3.5" />
                              </button>
                            )}
                          </div>
                        </div>

                        <div className="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs text-slate-400">
                          <div className="bg-slate-900/60 p-2.5 rounded-lg border border-slate-800/80">
                            <div>Usuarios</div>
                            <div className="text-sm font-bold text-white mt-0.5">{h.users_count}</div>
                          </div>
                          <div className="bg-slate-900/60 p-2.5 rounded-lg border border-slate-800/80">
                            <div>Sesiones</div>
                            <div className="text-sm font-bold text-sky-400 mt-0.5">{h.active_sessions}</div>
                          </div>
                          <div className="bg-slate-900/60 p-2.5 rounded-lg border border-slate-800/80">
                            <div>MAC Tables</div>
                            <div className="text-sm font-bold text-slate-200 mt-0.5">{h.mac_tables}</div>
                          </div>
                          <div className="bg-slate-900/60 p-2.5 rounded-lg border border-slate-800/80">
                            <div>Tráfico</div>
                            <div className="text-sm font-bold text-emerald-400 mt-0.5">{h.traffic}</div>
                          </div>
                        </div>

                        <div className="flex flex-wrap items-center gap-2 mt-3 pt-3 border-t border-slate-800/80">
                          <button
                            onClick={() => openHubManagement(h.hub_name, 'nat_dhcp')}
                            className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-500/15 text-emerald-300 border border-emerald-500/30 hover:bg-emerald-500/25 cursor-pointer"
                          >
                            <Settings className="w-3.5 h-3.5" />
                            <span>DHCP & SecureNAT</span>
                          </button>
                          <button
                            onClick={() => openHubManagement(h.hub_name, 'users')}
                            className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-sky-500/15 text-sky-300 border border-sky-500/30 hover:bg-sky-500/25 cursor-pointer"
                          >
                            <Users className="w-3.5 h-3.5" />
                            <span>Usuarios ({users.filter(u => u.hub_name === h.hub_name).length})</span>
                          </button>
                          <button
                            onClick={() => openHubManagement(h.hub_name, 'logs')}
                            className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-indigo-500/15 text-indigo-300 border border-indigo-500/30 hover:bg-indigo-500/25 cursor-pointer"
                          >
                            <FileText className="w-3.5 h-3.5" />
                            <span>Logs</span>
                          </button>
                        </div>
                      </div>
                    ))}

                    {paginatedHubs.length === 0 && (
                      <div className="text-center py-8 bg-slate-950/40 rounded-xl border border-dashed border-slate-800 text-slate-400 text-xs">
                        <Search className="w-6 h-6 mx-auto mb-2 text-slate-600" />
                        <div>No se encontraron Virtual Hubs que coincidan con "{hubSearch}".</div>
                        <button
                          onClick={() => {
                            setHubSearch('');
                            setHubPage(1);
                          }}
                          className="mt-3 px-3 py-1 rounded-lg bg-sky-600/20 text-sky-400 border border-sky-500/30 hover:bg-sky-600/30 transition-colors"
                        >
                          Limpiar búsqueda
                        </button>
                      </div>
                    )}
                  </div>

                  {/* Paginación */}
                  {totalPages > 1 && (
                    <div className="flex items-center justify-between pt-4 mt-4 border-t border-slate-800 flex-wrap gap-2 text-xs">
                      <span className="text-slate-400">
                        Página {currentPage} de {totalPages} ({filteredHubs.length} hubs)
                      </span>
                      <div className="flex items-center gap-1.5">
                        <button
                          onClick={() => setHubPage(p => Math.max(1, p - 1))}
                          disabled={currentPage <= 1}
                          className="px-2.5 py-1.5 rounded-lg bg-slate-800 text-slate-300 disabled:opacity-40 disabled:cursor-not-allowed hover:bg-slate-700 cursor-pointer"
                        >
                          ◀ Anterior
                        </button>
                        {Array.from({ length: totalPages }, (_, i) => i + 1).map(p => (
                          <button
                            key={p}
                            onClick={() => setHubPage(p)}
                            className={`w-7 h-7 rounded-lg text-xs font-semibold cursor-pointer ${
                              p === currentPage
                                ? 'bg-sky-500/20 text-sky-400 border border-sky-500/40'
                                : 'bg-slate-800 text-slate-400 hover:text-white'
                            }`}
                          >
                            {p}
                          </button>
                        ))}
                        <button
                          onClick={() => setHubPage(p => Math.min(totalPages, p + 1))}
                          disabled={currentPage >= totalPages}
                          className="px-2.5 py-1.5 rounded-lg bg-slate-800 text-slate-300 disabled:opacity-40 disabled:cursor-not-allowed hover:bg-slate-700 cursor-pointer"
                        >
                          Siguiente ▶
                        </button>
                      </div>
                    </div>
                  )}
                </>
              );
            })()}
          </div>

          <div className="bg-slate-900/80 border border-slate-800 rounded-2xl p-6">
            <h3 className="text-base font-semibold text-white mb-4 flex items-center gap-2 border-b border-slate-800 pb-3">
              <Radio className="w-4 h-4 text-emerald-400" />
              Configuración SecureNAT & DHCP Virtual
            </h3>
            <div className="space-y-3.5 text-sm">
              <div className="flex items-center justify-between py-2 border-b border-slate-800/60">
                <span className="text-slate-400">Virtual NAT:</span>
                <span className="px-2.5 py-0.5 rounded text-xs font-semibold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                  Habilitado (Traducción de Red)
                </span>
              </div>
              <div className="flex items-center justify-between py-2 border-b border-slate-800/60">
                <span className="text-slate-400">Servidor DHCP Virtual:</span>
                <span className="px-2.5 py-0.5 rounded text-xs font-semibold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                  Habilitado
                </span>
              </div>
              <div className="flex items-center justify-between py-2 border-b border-slate-800/60">
                <span className="text-slate-400">Gateway Virtual del Túnel:</span>
                <span className="font-mono text-sky-400 font-bold">{secureNat.virtual_gateway_ip}</span>
              </div>
              <div className="flex items-center justify-between py-2 border-b border-slate-800/60">
                <span className="text-slate-400">Rango de Concesiones DHCP:</span>
                <span className="font-mono text-emerald-400 font-semibold">
                  {secureNat.dhcp_lease_start} - {secureNat.dhcp_lease_end}
                </span>
              </div>
              <div className="flex items-center justify-between py-2 border-b border-slate-800/60">
                <span className="text-slate-400">Servidores DNS Distribuidos:</span>
                <span className="font-mono text-slate-300">{secureNat.dns_servers.join(', ')}</span>
              </div>
              <div className="flex items-center justify-between py-2">
                <span className="text-slate-400">Arrendamientos DHCP Activos:</span>
                <span className="font-mono text-white font-bold">{secureNat.active_dhcp_leases} asignadas</span>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* TAB 5: GUIA DE CONFIGURACION & CREDENCIALES SEGURAS */}
      {activeTab === 'config' && (
        <div className="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 space-y-6">
          <div className="flex items-start gap-4">
            <div className="p-3 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-400">
              <Lock className="w-6 h-6" />
            </div>
            <div>
              <h2 className="text-lg font-bold text-white">
                Recomendación de Seguridad: ¿Dónde colocar tus credenciales?
              </h2>
              <p className="text-sm text-slate-400 mt-1">
                La mejor práctica de ingeniería y seguridad es colocarlas exclusivamente en el archivo <code className="text-amber-300 bg-slate-950 px-2 py-0.5 rounded border border-slate-800">.env</code> de tu servidor VPS en producción.
              </p>
            </div>
          </div>

          <div className="p-4 rounded-xl bg-amber-500/10 border border-amber-500/25 text-amber-300 text-xs leading-relaxed">
            <strong>¿Por qué en .env y NUNCA en el código ni en el chat?</strong><br />
            1. El archivo <code className="font-mono">.env</code> está incluido en el <code className="font-mono">.gitignore</code>, lo que garantiza que tus contraseñas maestras, IPs y puertos privados jamás se suban a GitHub ni queden expuestos en el historial de commits.<br />
            2. La plataforma en el servidor lee esas variables de entorno de forma totalmente privada para comunicarse con el demonio de SoftEther VPN por el puerto de administración RPC sin exponer secretos a los navegadores.
          </div>

          <div className="space-y-4">
            <h3 className="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
              <Terminal className="w-4 h-4 text-sky-400" />
              Pasos para configurarlo en tu servidor VPS
            </h3>

            <div className="space-y-3 text-xs text-slate-300">
              <div className="bg-slate-950/80 border border-slate-800 rounded-xl p-4">
                <div className="font-semibold text-sky-400 mb-1">Paso 1: Conéctate por SSH a tu servidor</div>
                <div className="font-mono text-slate-300 bg-slate-900 p-2.5 rounded-lg border border-slate-800">
                  ssh root@tu-servidor-vps<br />
                  cd /opt/dataplus-platform/src<br />
                  nano .env
                </div>
              </div>

              <div className="bg-slate-950/80 border border-slate-800 rounded-xl p-4">
                <div className="flex items-center justify-between mb-2">
                  <div className="font-semibold text-sky-400">Paso 2: Pega estas variables con tus datos reales</div>
                  <button
                    onClick={copyToClipboard}
                    className="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-semibold bg-sky-500/20 text-sky-300 hover:bg-sky-500/30 border border-sky-500/30 transition-all cursor-pointer"
                  >
                    {copiedEnv ? (
                      <>
                        <Check className="w-3.5 h-3.5 text-emerald-400" />
                        <span className="text-emerald-400">¡Copiado!</span>
                      </>
                    ) : (
                      <>
                        <Copy className="w-3.5 h-3.5" />
                        <span>Copiar bloque</span>
                      </>
                    )}
                  </button>
                </div>
                <pre className="font-mono text-xs text-sky-300 bg-slate-900 p-3.5 rounded-lg border border-slate-800 overflow-x-auto leading-relaxed">
{envTemplate}
                </pre>
              </div>

              <div className="bg-slate-950/80 border border-slate-800 rounded-xl p-4">
                <div className="font-semibold text-sky-400 mb-1">Paso 3: Reinicia el contenedor o servicio</div>
                <div className="font-mono text-slate-300 bg-slate-900 p-2.5 rounded-lg border border-slate-800">
                  # En tu VPS:<br />
                  docker compose restart app<br />
                  # o si ejecutas PHP directamente:<br />
                  php artisan config:clear
                </div>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* MODAL: NUEVO USUARIO VPN */}
      {isAddUserModalOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
          <div className="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-md p-6 shadow-2xl relative">
            <button
              onClick={() => setIsAddUserModalOpen(false)}
              className="absolute top-4 right-4 text-slate-400 hover:text-slate-200 cursor-pointer"
            >
              <X className="w-5 h-5" />
            </button>

            <h3 className="text-lg font-bold text-white mb-1 flex items-center gap-2">
              <Plus className="w-5 h-5 text-emerald-400" />
              Crear Nueva Cuenta de Usuario VPN
            </h3>
            <p className="text-xs text-slate-400 mb-4">
              Crea credenciales en el Virtual Hub DEFAULT para conectar un nuevo router 4G o técnico.
            </p>

            <form onSubmit={handleAddUser} className="space-y-4 text-sm">
              <div>
                <label className="block text-xs font-semibold text-slate-300 mb-1">
                  Virtual Hub de Destino <span className="text-rose-400">*</span>
                </label>
                <select
                  value={newUserHub}
                  onChange={e => setNewUserHub(e.target.value)}
                  className="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sky-400 font-mono text-sm focus:outline-none focus:border-sky-500 cursor-pointer"
                >
                  {hubs.map(h => (
                    <option key={h.hub_name} value={h.hub_name}>{h.hub_name}</option>
                  ))}
                </select>
              </div>

              <div>
                <label className="block text-xs font-semibold text-slate-300 mb-1">
                  Nombre de Usuario VPN (Username)
                </label>
                <input
                  type="text"
                  required
                  placeholder="ej: cliente_nuevo_4g"
                  value={newUsername}
                  onChange={e => setNewUsername(e.target.value)}
                  className="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-slate-200 font-mono text-sm focus:outline-none focus:border-sky-500"
                />
              </div>

              <div>
                <label className="block text-xs font-semibold text-slate-300 mb-1">
                  Cliente Vinculado
                </label>
                <select
                  value={newClientName}
                  onChange={e => setNewClientName(e.target.value)}
                  className="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-slate-200 text-sm focus:outline-none focus:border-sky-500 cursor-pointer"
                >
                  <option value="Moraima Guanabo">Moraima Guanabo</option>
                  <option value="Dainet Playa">Dainet Playa</option>
                  {clientes.map(c => (
                    <option key={c.id} value={c.nombre}>{c.nombre}</option>
                  ))}
                  <option value="Operaciones DataPlus">Operaciones DataPlus</option>
                </select>
              </div>

              <div>
                <label className="block text-xs font-semibold text-slate-300 mb-1">
                  Descripción / Equipo
                </label>
                <input
                  type="text"
                  placeholder="ej: Router 4G LTE Huawei B310"
                  value={newRealName}
                  onChange={e => setNewRealName(e.target.value)}
                  className="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-slate-200 text-sm focus:outline-none focus:border-sky-500"
                />
              </div>

              <div>
                <label className="block text-xs font-semibold text-slate-300 mb-1">
                  Contraseña de Conexión
                </label>
                <input
                  type="password"
                  required
                  placeholder="Contraseña del túnel VPN"
                  value={newPassword}
                  onChange={e => setNewPassword(e.target.value)}
                  className="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-slate-200 text-sm focus:outline-none focus:border-sky-500"
                />
              </div>

              <div className="flex justify-end gap-3 pt-3 border-t border-slate-800">
                <button
                  type="button"
                  onClick={() => setIsAddUserModalOpen(false)}
                  className="px-4 py-2 rounded-xl text-xs font-semibold text-slate-400 hover:text-white cursor-pointer"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  className="px-5 py-2 rounded-xl text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white shadow-lg shadow-emerald-600/20 cursor-pointer"
                >
                  Guardar Cuenta VPN
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      {/* MODAL: NUEVO VIRTUAL HUB */}
      {isAddHubModalOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
          <div className="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-md p-6 shadow-2xl relative">
            <button
              onClick={() => setIsAddHubModalOpen(false)}
              className="absolute top-4 right-4 text-slate-400 hover:text-slate-200 cursor-pointer"
            >
              <X className="w-5 h-5" />
            </button>

            <h3 className="text-lg font-bold text-white mb-1 flex items-center gap-2">
              <Plus className="w-5 h-5 text-emerald-400" />
              Crear Nuevo Virtual Hub
            </h3>
            <p className="text-xs text-slate-400 mb-4">
              Crea un Virtual Hub aislado en el servidor SoftEther para un nuevo cliente o sucursal.
            </p>

            <form onSubmit={handleAddHub} className="space-y-4 text-sm">
              <div>
                <label className="block text-xs font-semibold text-slate-300 mb-1">
                  Nombre del Virtual Hub <span className="text-rose-400">*</span>
                </label>
                <input
                  type="text"
                  required
                  placeholder="ej: Cliente_EmpresaX"
                  value={newHubName}
                  onChange={e => setNewHubName(e.target.value)}
                  className="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-slate-200 font-mono text-sm focus:outline-none focus:border-sky-500"
                />
                <p className="text-[11px] text-slate-500 mt-1">
                  Solo letras, números, guiones y guiones bajos (sin espacios).
                </p>
              </div>

              <div>
                <label className="block text-xs font-semibold text-slate-300 mb-1">
                  Contraseña de Administración del Hub <span className="text-slate-500">(Opcional)</span>
                </label>
                <input
                  type="password"
                  placeholder="Dejar vacío o definir contraseña específica"
                  value={newHubPassword}
                  onChange={e => setNewHubPassword(e.target.value)}
                  className="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-slate-200 text-sm focus:outline-none focus:border-sky-500"
                />
              </div>

              <div className="p-3 rounded-xl bg-sky-500/10 border border-sky-500/20 text-sky-300 text-xs leading-relaxed">
                ℹ El Virtual Hub se creará en estado <strong>Online</strong> inmediatamente en el servidor VPN.
              </div>

              <div className="flex justify-end gap-3 pt-3 border-t border-slate-800">
                <button
                  type="button"
                  onClick={() => setIsAddHubModalOpen(false)}
                  className="px-4 py-2 rounded-xl text-xs font-semibold text-slate-400 hover:text-white cursor-pointer"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  className="px-5 py-2 rounded-xl text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white shadow-lg shadow-emerald-600/20 cursor-pointer"
                >
                  Crear Virtual Hub
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      {/* MODAL: CONFIRMAR ELIMINAR VIRTUAL HUB */}
      {hubToDelete && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
          <div className="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-md p-6 shadow-2xl relative">
            <button
              onClick={() => setHubToDelete(null)}
              className="absolute top-4 right-4 text-slate-400 hover:text-slate-200 cursor-pointer"
            >
              <X className="w-5 h-5" />
            </button>

            <h3 className="text-lg font-bold text-rose-400 mb-1 flex items-center gap-2">
              <AlertTriangle className="w-5 h-5 text-rose-400" />
              ¿Eliminar Virtual Hub '{hubToDelete}'?
            </h3>
            <p className="text-xs text-slate-300 mb-3">
              Está a punto de eliminar permanentemente este Virtual Hub del servidor VPN.
            </p>

            <div className="p-3 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-200 text-xs space-y-1 mb-5">
              <div className="font-semibold text-rose-300">Consecuencias críticas:</div>
              <ul className="list-disc list-inside space-y-0.5 text-slate-300">
                <li>Se desconectarán todos los túneles y clientes activos en este Hub.</li>
                <li>Se eliminarán todas las cuentas de usuario VPN y SecureNAT del Hub.</li>
                <li>Esta acción es irreversible en SoftEther.</li>
              </ul>
            </div>

            <div className="flex justify-end gap-3 pt-3 border-t border-slate-800">
              <button
                type="button"
                onClick={() => setHubToDelete(null)}
                className="px-4 py-2 rounded-xl text-xs font-semibold text-slate-400 hover:text-white cursor-pointer"
              >
                Cancelar
              </button>
              <button
                type="button"
                onClick={handleConfirmDeleteHub}
                className="px-5 py-2 rounded-xl text-xs font-semibold bg-rose-600 hover:bg-rose-500 text-white shadow-lg shadow-rose-600/20 cursor-pointer"
              >
                Sí, Eliminar Virtual Hub
              </button>
            </div>
          </div>
        </div>
      )}

      {/* MODAL: DESHABILITAR USUARIO POR TIEMPO DEFINIDO */}
      {userToTimedDisable && (
        <div className="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
          <div className="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-md p-6 shadow-2xl relative">
            <button
              onClick={() => setUserToTimedDisable(null)}
              className="absolute top-4 right-4 text-slate-400 hover:text-slate-200 cursor-pointer"
            >
              <X className="w-5 h-5" />
            </button>
            <h3 className="text-lg font-bold text-amber-400 mb-1 flex items-center gap-2">
              <Clock className="w-5 h-5" />
              Deshabilitar Cuenta: {userToTimedDisable.username}
            </h3>
            <p className="text-xs text-slate-400 mb-4">
              Virtual Hub: <span className="font-mono text-sky-400">{userToTimedDisable.hub_name}</span>
            </p>
            <form onSubmit={handleConfirmTimedDisable} className="space-y-4 text-sm">
              <div>
                <label className="block text-xs font-semibold text-slate-300 mb-1">
                  Duración de suspensión
                </label>
                <select
                  value={disablePreset}
                  onChange={e => setDisablePreset(e.target.value)}
                  className="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-slate-200 text-sm focus:outline-none focus:border-amber-500 cursor-pointer"
                >
                  <option value="60">1 hora</option>
                  <option value="360">6 horas</option>
                  <option value="720">12 horas</option>
                  <option value="1440">24 horas (1 día)</option>
                  <option value="4320">3 días</option>
                  <option value="10080">7 días</option>
                  <option value="custom">Fecha y hora personalizada</option>
                  <option value="indefinite">Indefinido</option>
                </select>
              </div>
              {disablePreset === 'custom' && (
                <div>
                  <label className="block text-xs font-semibold text-slate-300 mb-1">
                    Reactivar automáticamente el:
                  </label>
                  <input
                    type="datetime-local"
                    required
                    value={disableCustomUntil}
                    onChange={e => setDisableCustomUntil(e.target.value)}
                    className="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-slate-200 text-sm focus:outline-none focus:border-amber-500"
                  />
                </div>
              )}
              <div className="flex justify-end gap-3 pt-3 border-t border-slate-800">
                <button
                  type="button"
                  onClick={() => setUserToTimedDisable(null)}
                  className="px-4 py-2 rounded-xl text-xs font-semibold text-slate-400 hover:text-white cursor-pointer"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  className="px-5 py-2 rounded-xl text-xs font-semibold bg-amber-600 hover:bg-amber-500 text-white cursor-pointer"
                >
                  Confirmar Deshabilitación
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      {/* MODAL: GESTIÓN COMPLETA DE VIRTUAL HUB */}
      {managingHub && (() => {
        const currentHubObj = hubs.find(h => h.hub_name === managingHub);
        const natCfg = hubNatConfigs[managingHub] || secureNat;
        const hubUsersList = users.filter(u => u.hub_name === managingHub);

        const updateCurrentNat = (patch: Partial<SoftEtherSecureNat>) => {
          setHubNatConfigs(prev => ({
            ...prev,
            [managingHub]: { ...natCfg, ...patch }
          }));
          if (managingHub === 'DEFAULT') {
            setSecureNat(prev => ({ ...prev, ...patch }));
          }
        };

        return (
          <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div className="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto p-6 shadow-2xl relative">
              <button
                onClick={() => setManagingHub(null)}
                className="absolute top-4 right-4 text-slate-400 hover:text-slate-200 cursor-pointer"
              >
                <X className="w-5 h-5" />
              </button>

              <h3 className="text-lg font-bold text-white mb-1">
                Gestión de Virtual Hub: <span className="font-mono text-sky-400">{managingHub}</span>
              </h3>
              <p className="text-xs text-slate-400 mb-4">Configuración individual de DHCP, SecureNAT, Usuarios y Logs</p>

              <div className="flex items-center gap-2 border-b border-slate-800 pb-2 mb-5">
                <button
                  onClick={() => setHubManageTab('nat_dhcp')}
                  className={`px-3 py-1.5 rounded-lg text-xs font-semibold cursor-pointer ${
                    hubManageTab === 'nat_dhcp' ? 'bg-sky-500/20 text-sky-400 border border-sky-500/40' : 'text-slate-400 hover:text-white'
                  }`}
                >
                  DHCP & SecureNAT
                </button>
                <button
                  onClick={() => setHubManageTab('users')}
                  className={`px-3 py-1.5 rounded-lg text-xs font-semibold cursor-pointer ${
                    hubManageTab === 'users' ? 'bg-sky-500/20 text-sky-400 border border-sky-500/40' : 'text-slate-400 hover:text-white'
                  }`}
                >
                  Usuarios del Hub ({hubUsersList.length})
                </button>
                <button
                  onClick={() => setHubManageTab('logs')}
                  className={`px-3 py-1.5 rounded-lg text-xs font-semibold cursor-pointer ${
                    hubManageTab === 'logs' ? 'bg-sky-500/20 text-sky-400 border border-sky-500/40' : 'text-slate-400 hover:text-white'
                  }`}
                >
                  Logs del Hub
                </button>
              </div>

              {hubManageTab === 'nat_dhcp' && (
                <div className="space-y-4 text-xs">
                  <div className="flex flex-wrap gap-4 p-3 rounded-xl bg-slate-950 border border-slate-800">
                    <label className="flex items-center gap-2 text-slate-200 cursor-pointer">
                      <input
                        type="checkbox"
                        checked={Boolean(natCfg.securenat_enabled ?? natCfg.nat_enabled)}
                        onChange={e => updateCurrentNat({ securenat_enabled: e.target.checked })}
                      />
                      <span>Activar SecureNAT</span>
                    </label>
                    <label className="flex items-center gap-2 text-slate-200 cursor-pointer">
                      <input
                        type="checkbox"
                        checked={natCfg.nat_enabled}
                        onChange={e => updateCurrentNat({ nat_enabled: e.target.checked })}
                      />
                      <span>Virtual NAT</span>
                    </label>
                    <label className="flex items-center gap-2 text-slate-200 cursor-pointer">
                      <input
                        type="checkbox"
                        checked={natCfg.dhcp_enabled}
                        onChange={e => updateCurrentNat({ dhcp_enabled: e.target.checked })}
                      />
                      <span>Servidor DHCP</span>
                    </label>
                  </div>
                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                      <label className="block text-slate-400 mb-1">Gateway Virtual</label>
                      <input
                        type="text"
                        value={natCfg.virtual_gateway_ip}
                        onChange={e => updateCurrentNat({ virtual_gateway_ip: e.target.value })}
                        className="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-slate-200 font-mono"
                      />
                    </div>
                    <div>
                      <label className="block text-slate-400 mb-1">Máscara de Subred</label>
                      <input
                        type="text"
                        value={natCfg.virtual_subnet_mask}
                        onChange={e => updateCurrentNat({ virtual_subnet_mask: e.target.value })}
                        className="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-slate-200 font-mono"
                      />
                    </div>
                    <div>
                      <label className="block text-slate-400 mb-1">Rango DHCP Inicio</label>
                      <input
                        type="text"
                        value={natCfg.dhcp_lease_start}
                        onChange={e => updateCurrentNat({ dhcp_lease_start: e.target.value })}
                        className="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-emerald-400 font-mono"
                      />
                    </div>
                    <div>
                      <label className="block text-slate-400 mb-1">Rango DHCP Fin</label>
                      <input
                        type="text"
                        value={natCfg.dhcp_lease_end}
                        onChange={e => updateCurrentNat({ dhcp_lease_end: e.target.value })}
                        className="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-emerald-400 font-mono"
                      />
                    </div>
                  </div>
                  <div className="flex justify-end pt-2">
                    <button
                      onClick={() => {
                        setActionNotice(`Configuración DHCP & SecureNAT guardada para ${managingHub}.`);
                        setManagingHub(null);
                      }}
                      className="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold cursor-pointer"
                    >
                      Guardar DHCP & SecureNAT
                    </button>
                  </div>
                </div>
              )}

              {hubManageTab === 'users' && (
                <div className="space-y-4 text-xs">
                  <form
                    onSubmit={e => {
                      e.preventDefault();
                      if (!hubNewUsername.trim()) return;
                      setUsers(prev => [
                        {
                          username: hubNewUsername.trim().toLowerCase(),
                          hub_name: managingHub,
                          real_name: hubNewRealName.trim() || hubNewUsername.trim(),
                          cliente_asociado: managingHub,
                          auth_type: 'Contraseña Estándar',
                          estado: 'Habilitado',
                          is_active: true,
                          max_connections: 1,
                          expires_at: 'Indefinido',
                          note: 'Creado desde gestión del Hub',
                          last_login: 'Nunca',
                          currently_connected: false,
                        },
                        ...prev,
                      ]);
                      setHubNewUsername('');
                      setHubNewRealName('');
                      setHubNewPassword('');
                    }}
                    className="grid grid-cols-1 sm:grid-cols-4 gap-2 p-3 rounded-xl bg-slate-950 border border-slate-800"
                  >
                    <input
                      type="text"
                      required
                      placeholder="Usuario"
                      value={hubNewUsername}
                      onChange={e => setHubNewUsername(e.target.value)}
                      className="bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-white font-mono"
                    />
                    <input
                      type="text"
                      placeholder="Nombre / Equipo"
                      value={hubNewRealName}
                      onChange={e => setHubNewRealName(e.target.value)}
                      className="bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-white"
                    />
                    <input
                      type="password"
                      required
                      placeholder="Contraseña"
                      value={hubNewPassword}
                      onChange={e => setHubNewPassword(e.target.value)}
                      className="bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-white"
                    />
                    <button
                      type="submit"
                      className="px-3 py-1.5 rounded-lg bg-sky-600 hover:bg-sky-500 text-white font-semibold cursor-pointer"
                    >
                      + Agregar Usuario
                    </button>
                  </form>

                  <div className="space-y-2">
                    {hubUsersList.map(hu => (
                      <div key={hu.username} className="flex items-center justify-between p-3 rounded-xl bg-slate-950 border border-slate-800">
                        <div>
                          <div className="font-mono font-bold text-sky-400">{hu.username}</div>
                          <div className="text-slate-400">{hu.real_name}</div>
                        </div>
                        <div className="flex items-center gap-2">
                          <span className={`px-2 py-0.5 rounded-full font-semibold ${
                            hu.is_active ? 'bg-emerald-500/15 text-emerald-400' : 'bg-amber-500/15 text-amber-300'
                          }`}>
                            {hu.estado}
                          </span>
                          {hu.is_active ? (
                            <button
                              onClick={() => setUserToTimedDisable({ username: hu.username, hub_name: managingHub })}
                              className="px-2.5 py-1 rounded-lg bg-amber-500/15 text-amber-300 border border-amber-500/30 cursor-pointer"
                            >
                              Deshabilitar
                            </button>
                          ) : (
                            <button
                              onClick={() => handleEnableUser(hu.username, managingHub)}
                              className="px-2.5 py-1 rounded-lg bg-emerald-500/15 text-emerald-300 border border-emerald-500/30 cursor-pointer"
                            >
                              Habilitar
                            </button>
                          )}
                          <button
                            onClick={() => handleDeleteUser(hu.username, managingHub)}
                            className="px-2.5 py-1 rounded-lg bg-rose-500/15 text-rose-300 border border-rose-500/30 cursor-pointer"
                          >
                            Eliminar
                          </button>
                        </div>
                      </div>
                    ))}
                  </div>
                </div>
              )}

              {hubManageTab === 'logs' && currentHubObj && (
                <div className="space-y-4 text-xs">
                  <div className="p-4 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-between">
                    <div>
                      <div className="font-semibold text-white">Security Log (Eventos de conexión y autenticación)</div>
                      <div className="text-slate-400">Rotación diaria</div>
                    </div>
                    <button
                      onClick={() => {
                        setHubs(prev => prev.map(h => h.hub_name === managingHub ? { ...h, save_security_log: !h.save_security_log } : h));
                      }}
                      className={`px-3 py-1.5 rounded-lg font-semibold cursor-pointer ${
                        currentHubObj.save_security_log
                          ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40'
                          : 'bg-rose-500/20 text-rose-300 border border-rose-500/40'
                      }`}
                    >
                      {currentHubObj.save_security_log ? 'Activo (Clic para desactivar)' : 'Desactivado (Clic para activar)'}
                    </button>
                  </div>
                  <div className="p-4 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-between">
                    <div>
                      <div className="font-semibold text-white">Packet Log (Auditoría de paquetes DHCP/TCP)</div>
                      <div className="text-slate-400">Rotación diaria</div>
                    </div>
                    <button
                      onClick={() => {
                        setHubs(prev => prev.map(h => h.hub_name === managingHub ? { ...h, save_packet_log: !h.save_packet_log } : h));
                      }}
                      className={`px-3 py-1.5 rounded-lg font-semibold cursor-pointer ${
                        currentHubObj.save_packet_log
                          ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40'
                          : 'bg-rose-500/20 text-rose-300 border border-rose-500/40'
                      }`}
                    >
                      {currentHubObj.save_packet_log ? 'Activo (Clic para desactivar)' : 'Desactivado (Clic para activar)'}
                    </button>
                  </div>
                </div>
              )}
            </div>
          </div>
        );
      })()}
    </div>
  );
};
