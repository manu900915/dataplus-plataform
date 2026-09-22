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
        // Filament envía 'email', no 'username'. Buscamos por email.
        $loginField = isset($credentials['email']) ? 'email' : 'name';
        
        if (empty($credentials[$loginField])) {
            return null;
        }

        return User::where($loginField, $credentials[$loginField])->first();
    }

    public function validateCredentials(Authenticatable $user, array $credentials): bool
    {
        // Si tiene ldap_uid, autenticamos contra LDAP
        if ($user->ldap_uid) {
            return $this->authenticateLdap($user, $credentials);
        }

        // Si NO tiene ldap_uid, autenticamos contra la BD local (PostgreSQL)
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
            $bindDn = $ldapUser['dn'];
            $ldap->auth()->attempt($bindDn, $credentials['password']);

            Log::info("LDAP auth successful for user: {$user->email}");
            return true;
        } catch (\Exception $e) {
            Log::error('LDAP auth failed: ' . $e->getMessage());
            return false;
        }
    }
}