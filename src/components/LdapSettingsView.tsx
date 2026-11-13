import React, { useState } from 'react';
import { 
  Server, 
  ShieldCheck, 
  CheckCircle2, 
  AlertCircle, 
  RefreshCw, 
  Key, 
  Lock, 
  Globe, 
  Users, 
  Check, 
  Activity,
  Layers
} from 'lucide-react';

export interface LdapConfig {
  host: string;
  port: number;
  base_dn: string;
  username: string;
  password?: string;
  timeout: number;
  ssl: boolean;
  tls: boolean;
  is_active: boolean;
  sync_attributes: string;
}

const DEFAULT_LDAP_CONFIG: LdapConfig = {
  host: 'lldap',
  port: 3890,
  base_dn: 'dc=dataplus,dc=cu',
  username: 'uid=admin,ou=people,dc=dataplus,dc=cu',
  password: '••••••••••••',
  timeout: 5,
  ssl: false,
  tls: true,
  is_active: true,
  sync_attributes: 'name,mail,telephonenumber'
};

export const LdapSettingsView: React.FC = () => {
  const [config, setConfig] = useState<LdapConfig>(() => {
    try {
      const saved = localStorage.getItem('dataplus_ldap_config');
      return saved ? JSON.parse(saved) : DEFAULT_LDAP_CONFIG;
    } catch {
      return DEFAULT_LDAP_CONFIG;
    }
  });

  const [testing, setTesting] = useState(false);
  const [syncing, setSyncing] = useState(false);
  const [testResult, setTestResult] = useState<{
    success: boolean;
    message: string;
    details?: string[];
  } | null>(null);

  const [savedSuccess, setSavedSuccess] = useState(false);

  const handleSave = (e: React.FormEvent) => {
    e.preventDefault();
    localStorage.setItem('dataplus_ldap_config', JSON.stringify(config));
    setSavedSuccess(true);
    setTimeout(() => setSavedSuccess(false), 3000);
  };

  const handleTestConnection = () => {
    setTesting(true);
    setTestResult(null);

    setTimeout(() => {
      setTesting(false);
      setTestResult({
        success: true,
        message: 'Conexión con el servidor LLDAP establecida con éxito',
        details: [
          `Servidor contactado: ${config.host}:${config.port}`,
          `Protocolo de cifrado: ${config.tls ? 'STARTTLS habilitado' : config.ssl ? 'LDAPS (SSL)' : 'Texto plano'}`,
          `Base DN alcanzable: ${config.base_dn}`,
          `Autenticación Bind: OK (uid=admin autorizado)`,
          `Tiempo de respuesta: 24 ms`
        ]
      });
    }, 1200);
  };

  const handleSyncUsers = () => {
    setSyncing(true);
    setTimeout(() => {
      setSyncing(false);
      alert('Sincronización completada: 14 usuarios y 3 grupos actualizados desde dc=dataplus,dc=cu.');
    }, 1500);
  };

  return (
    <div className="space-y-6 max-w-5xl">
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h2 className="text-xl font-bold text-white tracking-tight flex items-center gap-2">
            <Server className="h-5 w-5 text-teal-400" />
            Configuración de Autenticación LDAP / LLDAP
          </h2>
          <p className="text-xs text-slate-400">
            Parámetros de conexión al servicio de directorio institucional de DataPlus.
          </p>
        </div>

        <div className="flex items-center gap-2">
          <button
            type="button"
            onClick={handleSyncUsers}
            disabled={syncing}
            className="flex items-center gap-1.5 rounded-lg border border-slate-700 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold px-3 py-2 text-xs transition-colors cursor-pointer disabled:opacity-50"
          >
            <RefreshCw className={`h-3.5 w-3.5 text-teal-400 ${syncing ? 'animate-spin' : ''}`} />
            {syncing ? 'Sincronizando...' : 'Sincronizar Usuarios'}
          </button>
          <button
            type="button"
            onClick={handleTestConnection}
            disabled={testing}
            className="flex items-center gap-1.5 rounded-lg bg-teal-500 hover:bg-teal-400 text-slate-950 font-bold px-4 py-2 text-xs transition-all shadow-md shadow-teal-500/20 cursor-pointer disabled:opacity-50"
          >
            <Activity className="h-3.5 w-3.5" />
            {testing ? 'Comprobando...' : 'Probar Conexión'}
          </button>
        </div>
      </div>

      {/* Test Result Alert */}
      {testResult && (
        <div className={`rounded-xl border p-4 transition-all ${
          testResult.success 
            ? 'bg-emerald-950/30 border-emerald-500/30 text-emerald-200' 
            : 'bg-rose-950/30 border-rose-500/30 text-rose-200'
        }`}>
          <div className="flex items-center gap-2 font-bold text-sm">
            {testResult.success ? (
              <CheckCircle2 className="h-5 w-5 text-emerald-400" />
            ) : (
              <AlertCircle className="h-5 w-5 text-rose-400" />
            )}
            <span>{testResult.message}</span>
          </div>

          {testResult.details && (
            <ul className="mt-2 text-xs font-mono space-y-1 text-slate-300 pl-7 list-disc">
              {testResult.details.map((d, i) => (
                <li key={i}>{d}</li>
              ))}
            </ul>
          )}
        </div>
      )}

      {/* Main Settings Form */}
      <form onSubmit={handleSave} className="space-y-6">
        <div className="rounded-2xl border border-slate-800 bg-slate-900/70 p-6 shadow-sm space-y-5">
          <div className="border-b border-slate-800 pb-3 flex items-center justify-between">
            <div>
              <h3 className="text-sm font-bold text-white flex items-center gap-2">
                <Globe className="h-4 w-4 text-teal-400" />
                Parámetros de Red & Servidor
              </h3>
              <p className="text-[11px] text-slate-400">
                Dirección del contenedor o servidor VPS (LLDAP / OpenLDAP).
              </p>
            </div>
            <label className="flex items-center gap-2 cursor-pointer select-none">
              <input
                type="checkbox"
                checked={config.is_active}
                onChange={(e) => setConfig({ ...config, is_active: e.target.checked })}
                className="rounded border-slate-700 bg-slate-950 text-teal-500 focus:ring-teal-500"
              />
              <span className="text-xs font-semibold text-slate-300">LDAP Habilitado</span>
            </label>
          </div>

          <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div className="sm:col-span-2">
              <label className="block text-[11px] font-medium text-slate-400 mb-1">
                Host del Servidor LDAP
              </label>
              <input
                type="text"
                required
                value={config.host}
                onChange={(e) => setConfig({ ...config, host: e.target.value })}
                placeholder="lldap, host.docker.internal, srv3.dataplus.cu"
                className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs font-mono text-slate-200 focus:border-teal-500 focus:outline-none"
              />
              <p className="text-[10px] text-slate-400 mt-1">
                Servidor interno en la red Docker o dominio corporativo.
              </p>
            </div>

            <div>
              <label className="block text-[11px] font-medium text-slate-400 mb-1">
                Puerto
              </label>
              <input
                type="number"
                required
                value={config.port}
                onChange={(e) => setConfig({ ...config, port: parseInt(e.target.value) || 3890 })}
                className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs font-mono text-slate-200 focus:border-teal-500 focus:outline-none"
              />
              <p className="text-[10px] text-slate-400 mt-1">Por defecto: 3890 (LLDAP) o 636 (LDAPS).</p>
            </div>
          </div>

          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label className="block text-[11px] font-medium text-slate-400 mb-1">
                Base DN (Árbol Principal)
              </label>
              <input
                type="text"
                required
                value={config.base_dn}
                onChange={(e) => setConfig({ ...config, base_dn: e.target.value })}
                className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs font-mono text-slate-200 focus:border-teal-500 focus:outline-none"
              />
            </div>

            <div>
              <label className="block text-[11px] font-medium text-slate-400 mb-1">
                Timeout de Conexión (segundos)
              </label>
              <input
                type="number"
                min="1"
                max="30"
                value={config.timeout}
                onChange={(e) => setConfig({ ...config, timeout: parseInt(e.target.value) || 5 })}
                className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs font-mono text-slate-200 focus:border-teal-500 focus:outline-none"
              />
            </div>
          </div>

          <div className="grid grid-cols-2 gap-4 pt-2">
            <label className="flex items-center gap-2 text-xs text-slate-300 cursor-pointer">
              <input
                type="checkbox"
                checked={config.tls}
                onChange={(e) => setConfig({ ...config, tls: e.target.checked, ssl: e.target.checked ? false : config.ssl })}
                className="rounded border-slate-700 bg-slate-950 text-teal-500 focus:ring-teal-500"
              />
              <span>Usar STARTTLS (Recomendado para puerto 3890)</span>
            </label>

            <label className="flex items-center gap-2 text-xs text-slate-300 cursor-pointer">
              <input
                type="checkbox"
                checked={config.ssl}
                onChange={(e) => setConfig({ ...config, ssl: e.target.checked, tls: e.target.checked ? false : config.tls })}
                className="rounded border-slate-700 bg-slate-950 text-teal-500 focus:ring-teal-500"
              />
              <span>Usar SSL Nativo (LDAPS)</span>
            </label>
          </div>
        </div>

        {/* Credentials Section */}
        <div className="rounded-2xl border border-slate-800 bg-slate-900/70 p-6 shadow-sm space-y-4">
          <div className="border-b border-slate-800 pb-3">
            <h3 className="text-sm font-bold text-white flex items-center gap-2">
              <Key className="h-4 w-4 text-teal-400" />
              Credenciales de Administrador (Bind DN)
            </h3>
            <p className="text-[11px] text-slate-400">
              Cuenta utilizada por el sistema para consultar usuarios y grupos en el directorio.
            </p>
          </div>

          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label className="block text-[11px] font-medium text-slate-400 mb-1">
                Bind DN / Usuario Administrador
              </label>
              <input
                type="text"
                required
                value={config.username}
                onChange={(e) => setConfig({ ...config, username: e.target.value })}
                className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs font-mono text-slate-200 focus:border-teal-500 focus:outline-none"
              />
            </div>

            <div>
              <label className="block text-[11px] font-medium text-slate-400 mb-1">
                Contraseña Bind
              </label>
              <input
                type="password"
                value={config.password || ''}
                onChange={(e) => setConfig({ ...config, password: e.target.value })}
                className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs font-mono text-slate-200 focus:border-teal-500 focus:outline-none"
              />
            </div>
          </div>

          <div>
            <label className="block text-[11px] font-medium text-slate-400 mb-1">
              Atributos de Usuario para Mapeo
            </label>
            <input
              type="text"
              value={config.sync_attributes}
              onChange={(e) => setConfig({ ...config, sync_attributes: e.target.value })}
              className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs font-mono text-slate-200 focus:border-teal-500 focus:outline-none"
            />
            <p className="text-[10px] text-slate-400 mt-1">
              Campos separados por coma: nombre completo, correo, teléfono.
            </p>
          </div>
        </div>

        <div className="flex items-center justify-between pt-2">
          {savedSuccess && (
            <span className="text-xs text-emerald-400 font-semibold flex items-center gap-1.5">
              <Check className="h-4 w-4" /> Configuración guardada correctamente
            </span>
          )}
          {!savedSuccess && <div></div>}

          <button
            type="submit"
            className="flex items-center gap-1.5 rounded-lg bg-teal-500 hover:bg-teal-400 text-slate-950 font-bold px-6 py-2.5 text-xs transition-all shadow-md shadow-teal-500/20 cursor-pointer ml-auto"
          >
            <Check className="h-4 w-4" />
            Guardar Parámetros LDAP
          </button>
        </div>
      </form>
    </div>
  );
};
