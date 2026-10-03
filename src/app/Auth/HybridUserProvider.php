<?php

namespace App\Auth;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Support\Facades\Log;
use LdapRecord\Container;

class HybridUserProvider implements UserProvider
{
    protected Hasher $hasher;

    public function __construct(Hasher $hasher)
    {
        $this->hasher = $hasher;
    }

    public function retrieveById($identifier): ?Authenticatable
    {
        return User::find($identifier);
    }

    public function retrieveByToken($identifier, $token): ?Authenticatable
    {
        $user = User::find($identifier);
        return $user && $user->remember_token === $token ? $user : null;
    }

    public function updateRememberToken(Authenticatable $user, $token): void
    {
        $user->setRememberToken($token);
        $user->save();
    }

    public function retrieveByCredentials(array $credentials): ?Authenticatable
    {
        // Filament envía 'email'. Si el usuario no existe localmente,
        // intentamos sincronizarlo desde LLDAP al vuelo.
        $email = $credentials['email'] ?? ($credentials['username'] ?? null);

        if (empty($email)) {
            return null;
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            $user = $this->syncFromLdap($email);
        }

        return $user;
    }

         protected function syncFromLdap(string $email): ?User
    {
        try {
            // 1. DEBUG: Registrar qué configuración está usando Laravel en este instante
            $currentHost = config('ldap.connections.default.hosts')[0] ?? 'UNKNOWN';
            \Illuminate\Support\Facades\Log::info("Intentando sincronizar LDAP. Host en config: {$currentHost}, Email: {$email}");

            $ldap = \LdapRecord\Container::getConnection('default');
            
            // 2. DEBUG: Verificar qué hosts tiene la conexión real de LdapRecord
            $ldapConfig = $ldap->getConfiguration();
            \Illuminate\Support\Facades\Log::info("Hosts reales en la conexión LdapRecord: " . json_encode($ldapConfig->get('hosts')));

            $ldapUser = $ldap->query()->whereEquals('mail', $email)->first();

            if (!$ldapUser) {
                \Illuminate\Support\Facades\Log::warning("Usuario no encontrado en LDAP: {$email}");
                return null;
            }

            $user = new User();
            $user->name = $ldapUser['displayName'][0] ?? $email;
            $user->email = $email;
            $user->ldap_uid = $ldapUser['uid'][0] ?? $email;
            $user->password = ''; // autenticación vía LDAP
            $user->activo = true;
            $user->save();

            \Illuminate\Support\Facades\Log::info("Usuario sincronizado exitosamente desde LLDAP: {$email}");

            if (method_exists($user, 'assignRole')) {
                try {
                    $user->assignRole(config('auth.default_role', 'Tecnico')); // Nota: Mayúscula inicial si así lo creaste
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('No se pudo asignar rol por defecto: '.$e->getMessage());
                }
            }

            return $user;
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Fallo sincronizando usuario LDAP: '.$e->getMessage());
            return null;
        }
    }

    public function validateCredentials(Authenticatable $user, array $credentials): bool
    {
        // Si tiene ldap_uid, autenticamos contra LDAP.
        // Fallback: si el bind LDAP falla (host inaccesible, clave cambiada, etc.)
        // y el usuario local tiene un hash de contraseña válido, se permite acceso local.
        if ($user->ldap_uid) {
            if ($this->authenticateLdap($user, $credentials)) {
                return true;
            }
            if (!empty($user->getAuthPassword())) {
                Log::warning("LDAP falló para {$user->email}; intentando fallback local.");
                return $this->hasher->check($credentials['password'] ?? '', $user->getAuthPassword());
            }
            return false;
        }

        // Usuario desactivado: denegar siempre
        if ($user->activo === false) {
            return false;
        }

        // Bloquear bypass LDAP: si el registro local no tiene password hasheado
        // pero sí ldap_uid vacío/anómalo, no permitir check contra hash ''
        if (!$user->ldap_uid && empty($user->getAuthPassword())) {
            return false;
        }

        // Si NO tiene ldap_uid, autenticamos contra la BD local (PostgreSQL)
        if (empty($credentials['password'])) {
            return false;
        }

        return $this->hasher->check($credentials['password'], $user->getAuthPassword());
    }

    public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false): void
    {
        if (!$user->ldap_uid && ($force || $this->hasher->needsRehash($user->getAuthPassword()))) {
            $user->forceFill(['password' => $this->hasher->make($credentials['password'])])->save();
        }
    }

    protected function authenticateLdap(Authenticatable $user, array $credentials): bool
    {
        try {
            $ldap = Container::getConnection('default');
            
            // Buscar usuario en LDAP
            $ldapUser = $ldap->query()
                ->where('uid', $user->ldap_uid)
                ->orWhere('mail', $user->email)
                ->first();

            if (!$ldapUser) {
                Log::warning("LDAP user not found: {$user->ldap_uid}");
                return false;
            }

            // Intentar bind con la contraseña proporcionada
            $bindDn = is_array($ldapUser['dn']) ? $ldapUser['dn'][0] : $ldapUser['dn'];
            if (!$ldap->auth()->attempt($bindDn, $credentials['password'], true)) {
                return false;
            }

            Log::info("LDAP auth successful for user: {$user->email}");
            return true;
        } catch (\Exception $e) {
            Log::error('LDAP auth failed: ' . $e->getMessage());
            return false;
        }
    }
}
