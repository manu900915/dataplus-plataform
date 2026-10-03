<?php

namespace App\Services;

use App\Models\LdapConfiguration;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use LdapRecord\Connection;
use LdapRecord\Container;

class LdapSyncService
{
    /**
     * Obtener o inicializar la conexión LDAP activa.
     */
    public function getConnection(): Connection
    {
        $ldapConfig = LdapConfiguration::first();

        if ($ldapConfig && $ldapConfig->is_active) {
            $useTls = (bool) ($ldapConfig->use_tls ?? $ldapConfig->tls ?? $ldapConfig->use_ssl ?? $ldapConfig->ssl ?? false);

            return new Connection([
                'hosts'    => [$ldapConfig->host],
                'port'     => (int) $ldapConfig->port,
                'base_dn'  => $ldapConfig->base_dn,
                'username' => $ldapConfig->username,
                'password' => $ldapConfig->password,
                'use_tls'  => $useTls,
                'timeout'  => (int) ($ldapConfig->timeout ?? 5),
            ]);
        }

        return Container::getConnection('default');
    }

    /**
     * Probar la conexión y diagnóstico con el servidor LDAP.
     */
    public function testConnection(): array
    {
        $start = microtime(true);

        try {
            $connection = $this->getConnection();
            $connection->connect();

            // Si hay credenciales de Bind DN, probarlas
            $config = $connection->getConfiguration();
            $username = $config->get('username');
            $password = $config->get('password');

            if (!empty($username) && !empty($password)) {
                $connection->auth()->bind($username, $password);
            }

            $elapsed = round((microtime(true) - $start) * 1000, 1);

            return [
                'success' => true,
                'message' => "Conexión exitosa al servidor LDAP ({$elapsed}ms)",
                'latency' => $elapsed,
                'host'    => implode(', ', $config->get('hosts')),
                'port'    => $config->get('port'),
                'base_dn' => $config->get('base_dn'),
            ];
        } catch (\Throwable $e) {
            $elapsed = round((microtime(true) - $start) * 1000, 1);

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'latency' => $elapsed,
                'error'   => $e->getMessage(),
            ];
        }
    }

    /**
     * Obtener la lista de usuarios detectados en el directorio LDAP.
     */
    public function getDirectoryUsers(): array
    {
        try {
            $connection = $this->getConnection();
            $query = $connection->query();

            // Buscar personas/cuentas en LDAP (soporte LLDAP, OpenLDAP, Active Directory)
            $results = $query->orWhereHas('uid')
                ->orWhereHas('mail')
                ->orWhereHas('userPrincipalName')
                ->get();

            $users = [];

            foreach ($results as $ldapEntry) {
                $uid = $this->getAttribute($ldapEntry, 'uid')
                    ?? $this->getAttribute($ldapEntry, 'sAMAccountName')
                    ?? $this->getAttribute($ldapEntry, 'cn');

                // Si no tiene identificador o es el admin técnico del sistema interno de LDAP, evaluar
                if (empty($uid)) {
                    continue;
                }

                $email = $this->getAttribute($ldapEntry, 'mail')
                    ?? $this->getAttribute($ldapEntry, 'userPrincipalName')
                    ?? "{$uid}@dataplus.cu";

                $name = $this->getAttribute($ldapEntry, 'displayName')
                    ?? $this->getAttribute($ldapEntry, 'cn')
                    ?? $uid;

                // Verificar si ya existe en la base de datos local
                $localUser = User::where('email', $email)
                    ->orWhere('ldap_uid', $uid)
                    ->first();

                $users[] = [
                    'uid'           => $uid,
                    'name'          => $name,
                    'email'         => $email,
                    'is_synced'     => $localUser !== null,
                    'local_user_id' => $localUser?->id,
                    'local_role'    => $localUser?->roles->pluck('name')->first() ?? 'Sin rol',
                    'activo'        => $localUser?->activo ?? true,
                    'dn'            => is_array($ldapEntry['dn'] ?? null) ? $ldapEntry['dn'][0] : ($ldapEntry['dn'] ?? ''),
                ];
            }

            return $users;
        } catch (\Throwable $e) {
            Log::error('Error listando usuarios de LDAP: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Sincronizar un único usuario desde los datos de LDAP a PostgreSQL.
     */
    public function syncSingleUser(array $userData, ?string $defaultRole = null): User
    {
        $roleName = $defaultRole ?? config('auth.default_role', 'user');

        $user = User::where('email', $userData['email'])
            ->orWhere('ldap_uid', $userData['uid'])
            ->first();

        if (!$user) {
            $user = new User();
            $user->password = Hash::make(Str::random(32)); // Password dummy, autentica por LDAP
        }

        $user->name = $userData['name'];
        $user->email = $userData['email'];
        $user->ldap_uid = $userData['uid'];
        $user->activo = true;
        $user->save();

        // Asignar rol por defecto si no tiene ninguno asignado
        if (method_exists($user, 'assignRole') && $user->roles->isEmpty()) {
            try {
                $user->assignRole($roleName);
            } catch (\Throwable $e) {
                Log::warning("No se pudo asignar rol {$roleName} a {$user->email}: " . $e->getMessage());
            }
        }

        return $user;
    }

    /**
     * Sincronizar todos los usuarios del directorio LDAP a PostgreSQL.
     */
    public function syncAllUsers(?string $defaultRole = null): array
    {
        $directoryUsers = $this->getDirectoryUsers();
        $imported = 0;
        $updated = 0;
        $errors = [];

        foreach ($directoryUsers as $ldapUser) {
            try {
                $alreadyExists = User::where('email', $ldapUser['email'])
                    ->orWhere('ldap_uid', $ldapUser['uid'])
                    ->exists();

                $this->syncSingleUser($ldapUser, $defaultRole);

                if ($alreadyExists) {
                    $updated++;
                } else {
                    $imported++;
                }
            } catch (\Throwable $e) {
                $errors[] = "Error con {$ldapUser['uid']}: " . $e->getMessage();
            }
        }

        return [
            'total'    => count($directoryUsers),
            'imported' => $imported,
            'updated'  => $updated,
            'errors'   => $errors,
        ];
    }

    /**
     * Extraer atributo de una entidad LDAP de forma segura.
     */
    protected function getAttribute($entry, string $attr): ?string
    {
        $lower = strtolower($attr);

        if (isset($entry[$attr][0])) {
            return (string) $entry[$attr][0];
        }

        if (isset($entry[$lower][0])) {
            return (string) $entry[$lower][0];
        }

        return null;
    }
}
