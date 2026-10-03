import React, { useState } from 'react';
import { 
  Users, 
  Plus, 
  ShieldCheck, 
  Mail, 
  Phone, 
  CheckCircle2, 
  XCircle, 
  Edit2, 
  Trash2, 
  X, 
  Lock, 
  RefreshCw, 
  Server,
  Sparkles
} from 'lucide-react';
import { UserAccount } from '../types';

interface UsuariosViewProps {
  usuarios: UserAccount[];
  onAddUsuario: (u: Omit<UserAccount, 'id' | 'created_at'>) => void;
  onUpdateUsuario: (u: UserAccount) => void;
  onDeleteUsuario: (id: string) => void;
  onSyncLdap: () => void;
  isSyncingLdap?: boolean;
}

export const UsuariosView: React.FC<UsuariosViewProps> = ({
  usuarios,
  onAddUsuario,
  onUpdateUsuario,
  onDeleteUsuario,
  onSyncLdap,
  isSyncingLdap = false,
}) => {
  const [searchTerm, setSearchTerm] = useState('');
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [editingUser, setEditingUser] = useState<UserAccount | null>(null);

  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [phone, setPhone] = useState('+53 5 ');
  const [role, setRole] = useState<UserAccount['role']>('Técnico');
  const [ldapUid, setLdapUid] = useState('');
  const [activo, setActivo] = useState(true);
  const [syncNotice, setSyncNotice] = useState<string | null>(null);

  const handleOpenAdd = () => {
    setEditingUser(null);
    setName('');
    setEmail('');
    setPhone('+53 5 ');
    setRole('Técnico');
    setLdapUid('');
    setActivo(true);
    setIsModalOpen(true);
  };

  const handleOpenEdit = (u: UserAccount) => {
    setEditingUser(u);
    setName(u.name);
    setEmail(u.email);
    setPhone(u.phone || '+53 5 ');
    setRole(u.role);
    setLdapUid(u.ldap_uid || '');
    setActivo(u.activo);
    setIsModalOpen(true);
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (editingUser) {
      onUpdateUsuario({
        ...editingUser,
        name,
        email,
        phone,
        role,
        ldap_uid: ldapUid || undefined,
        activo
      });
    } else {
      onAddUsuario({
        name,
        email,
        phone,
        role,
        ldap_uid: ldapUid || undefined,
        activo
      });
    }
    setIsModalOpen(false);
  };

  const handleTriggerSync = () => {
    onSyncLdap();
    setSyncNotice('Directorio LLDAP consultado con éxito (srv3.dataplus.cu:3890). Usuarios sincronizados.');
    setTimeout(() => setSyncNotice(null), 5000);
  };

  const filteredUsuarios = usuarios.filter(u =>
    u.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
    u.email.toLowerCase().includes(searchTerm.toLowerCase()) ||
    u.role.toLowerCase().includes(searchTerm.toLowerCase())
  );

  return (
    <div className="space-y-6 max-w-5xl">
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h2 className="text-xl font-bold text-white tracking-tight flex items-center gap-2">
            <Users className="h-5 w-5 text-sky-400" />
            Usuarios del Sistema & Permisos
          </h2>
          <p className="text-xs text-slate-400">
            Control de identidades locales y sincronización con el servidor LLDAP (Filament: UserResource).
          </p>
        </div>

        <div className="flex items-center gap-2">
          <button
            onClick={handleTriggerSync}
            disabled={isSyncingLdap}
            className="flex items-center gap-2 rounded-lg border border-sky-500/30 bg-sky-950/40 hover:bg-sky-900/50 text-sky-300 font-bold px-3.5 py-2 text-xs transition-all shadow-sm cursor-pointer disabled:opacity-50"
          >
            <RefreshCw className={`h-3.5 w-3.5 ${isSyncingLdap ? 'animate-spin' : ''}`} />
            Sincronizar con LDAP
          </button>

          <button
            onClick={handleOpenAdd}
            className="flex items-center gap-2 rounded-lg bg-sky-500 hover:bg-sky-400 text-slate-950 font-bold px-4 py-2 text-xs transition-all shadow-md shadow-sky-500/20 cursor-pointer"
          >
            <Plus className="h-4 w-4 stroke-[2.5]" />
            Nuevo Usuario Local
          </button>
        </div>
      </div>

      {syncNotice && (
        <div className="rounded-xl border border-sky-500/40 bg-sky-950/30 p-3 text-xs text-sky-200 flex items-center justify-between">
          <div className="flex items-center gap-2">
            <Server className="h-4 w-4 text-sky-400" />
            <span>{syncNotice}</span>
          </div>
          <button onClick={() => setSyncNotice(null)} className="text-sky-400 hover:text-white">
            <X className="h-4 w-4" />
          </button>
        </div>
      )}

      {/* Stats summary */}
      <div className="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div className="p-3.5 rounded-xl border border-slate-800 bg-slate-900/60">
          <p className="text-[11px] text-slate-400 font-medium">Usuarios Activos</p>
          <p className="text-xl font-bold text-white mt-0.5">{usuarios.length}</p>
        </div>
        <div className="p-3.5 rounded-xl border border-slate-800 bg-slate-900/60">
          <p className="text-[11px] text-slate-400 font-medium">Usuarios Locales</p>
          <p className="text-xl font-bold text-emerald-400 mt-0.5">
            {usuarios.filter(u => !u.ldap_uid).length}
          </p>
        </div>
        <div className="p-3.5 rounded-xl border border-slate-800 bg-slate-900/60">
          <p className="text-[11px] text-slate-400 font-medium">Sincronizados vía LDAP</p>
          <p className="text-xl font-bold text-sky-400 mt-0.5">
            {usuarios.filter(u => !!u.ldap_uid).length}
          </p>
        </div>
      </div>

      <div className="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/50 shadow-sm">
        <table className="w-full text-left text-xs text-slate-300">
          <thead className="bg-slate-950/80 text-[11px] font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-800">
            <tr>
              <th className="py-3.5 px-4">Usuario / Nombre</th>
              <th className="py-3.5 px-4">Correo & Teléfono</th>
              <th className="py-3.5 px-4">Rol Asignado</th>
              <th className="py-3.5 px-4">Origen / Identidad</th>
              <th className="py-3.5 px-4">Estado</th>
              <th className="py-3.5 px-4 text-right">Acciones</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-slate-800/80">
            {filteredUsuarios.map((u) => (
              <tr key={u.id} className="hover:bg-slate-800/40 transition-colors">
                <td className="py-3 px-4 font-bold text-white flex items-center gap-2">
                  <div className="h-7 w-7 rounded-full bg-gradient-to-tr from-sky-500 to-blue-600 flex items-center justify-center text-white text-[11px] font-bold">
                    {u.name.charAt(0)}
                  </div>
                  <span>{u.name}</span>
                </td>
                <td className="py-3 px-4">
                  <span className="text-slate-300 block flex items-center gap-1">
                    <Mail className="h-3 w-3 text-slate-400" /> {u.email}
                  </span>
                  {u.phone && (
                    <span className="text-slate-400 text-[11px] flex items-center gap-1 mt-0.5 font-mono">
                      <Phone className="h-3 w-3 text-slate-400" /> {u.phone}
                    </span>
                  )}
                </td>
                <td className="py-3 px-4">
                  <span className={`inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold ${
                    u.role === 'Administrador' ? 'bg-rose-500/10 text-rose-400 border border-rose-500/20' :
                    u.role === 'Supervisor' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' :
                    u.role === 'Contable' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' :
                    'bg-sky-500/10 text-sky-400 border border-sky-500/20'
                  }`}>
                    {u.role}
                  </span>
                </td>
                <td className="py-3 px-4 font-mono text-[11px]">
                  {u.ldap_uid ? (
                    <span className="text-sky-300 bg-sky-950/60 px-2 py-0.5 rounded border border-sky-500/30 flex items-center gap-1.5 w-fit">
                      <Server className="h-3 w-3 text-sky-400" />
                      LDAP: {u.ldap_uid}
                    </span>
                  ) : (
                    <span className="text-emerald-300 bg-emerald-950/40 px-2 py-0.5 rounded border border-emerald-500/30 flex items-center gap-1.5 w-fit">
                      <CheckCircle2 className="h-3 w-3 text-emerald-400" />
                      Local DB
                    </span>
                  )}
                </td>
                <td className="py-3 px-4">
                  <span className={`inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold ${
                    u.activo ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-slate-800 text-slate-400'
                  }`}>
                    {u.activo ? 'Activo' : 'Inactivo'}
                  </span>
                </td>
                <td className="py-3 px-4 text-right">
                  <div className="flex items-center justify-end gap-1.5">
                    <button
                      onClick={() => handleOpenEdit(u)}
                      className="rounded p-1 text-slate-400 hover:bg-slate-800 hover:text-sky-400 transition-colors"
                      title="Editar usuario"
                    >
                      <Edit2 className="h-4 w-4" />
                    </button>
                    {usuarios.length > 1 && (
                      <button
                        onClick={() => onDeleteUsuario(u.id)}
                        className="rounded p-1 text-slate-400 hover:bg-slate-800 hover:text-rose-400 transition-colors"
                        title="Eliminar usuario"
                      >
                        <Trash2 className="h-4 w-4" />
                      </button>
                    )}
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {isModalOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4">
          <div className="relative w-full max-w-md rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-2xl">
            <div className="flex items-center justify-between border-b border-slate-800 pb-3 mb-4">
              <h3 className="text-sm font-bold text-white">
                {editingUser ? 'Editar Usuario' : 'Nuevo Usuario Local'}
              </h3>
              <button onClick={() => setIsModalOpen(false)} className="text-slate-400 hover:text-white">
                <X className="h-4 w-4" />
              </button>
            </div>
            <form onSubmit={handleSubmit} className="space-y-4">
              <div>
                <label className="block text-[11px] font-medium text-slate-400 mb-1">Nombre Completo *</label>
                <input
                  type="text"
                  required
                  value={name}
                  onChange={(e) => setName(e.target.value)}
                  placeholder="Ej: Enmanuel Caraballo"
                  className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-sky-500 focus:outline-none"
                />
              </div>
              <div>
                <label className="block text-[11px] font-medium text-slate-400 mb-1">Correo Institucional *</label>
                <input
                  type="email"
                  required
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  placeholder="usuario@dataplus.cu"
                  className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-sky-500 focus:outline-none"
                />
              </div>
              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">Teléfono</label>
                  <input
                    type="text"
                    value={phone}
                    onChange={(e) => setPhone(e.target.value)}
                    className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-sky-500 focus:outline-none"
                  />
                </div>
                <div>
                  <label className="block text-[11px] font-medium text-slate-400 mb-1">Rol en Sistema</label>
                  <select
                    value={role}
                    onChange={(e) => setRole(e.target.value as any)}
                    className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 focus:border-sky-500 focus:outline-none"
                  >
                    <option value="Administrador">Administrador</option>
                    <option value="Supervisor">Supervisor</option>
                    <option value="Técnico">Técnico</option>
                    <option value="Vendedor">Vendedor</option>
                    <option value="Contable">Contable</option>
                  </select>
                </div>
              </div>
              <div>
                <label className="block text-[11px] font-medium text-slate-400 mb-1">UID LDAP (Opcional)</label>
                <input
                  type="text"
                  value={ldapUid}
                  onChange={(e) => setLdapUid(e.target.value)}
                  placeholder="ej: ecaraballo"
                  className="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs font-mono text-slate-200 focus:border-sky-500 focus:outline-none"
                />
              </div>
              <label className="flex items-center gap-2 cursor-pointer">
                <input
                  type="checkbox"
                  checked={activo}
                  onChange={(e) => setActivo(e.target.checked)}
                  className="rounded border-slate-700 bg-slate-950 text-sky-500 focus:ring-sky-500"
                />
                <span className="text-xs text-slate-300">Usuario Activo con Acceso al Panel</span>
              </label>
              <div className="flex justify-end gap-2 pt-2 border-t border-slate-800">
                <button
                  type="button"
                  onClick={() => setIsModalOpen(false)}
                  className="rounded-lg border border-slate-700 bg-slate-800 px-3 py-1.5 text-xs text-slate-300"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  className="rounded-lg bg-sky-500 hover:bg-sky-400 px-4 py-1.5 text-xs font-bold text-slate-950"
                >
                  Guardar
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
};
