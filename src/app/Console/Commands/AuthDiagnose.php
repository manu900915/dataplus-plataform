<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use LdapRecord\Container;

class AuthDiagnose extends Command
{
    protected $signature = 'auth:diagnose {email?} {--password=}';
    protected $description = 'Diagnóstico de autenticación: LDAP (conexión, bind, búsqueda) + usuarios locales';

    public function handle(): int
    {
        // ---------- 1. Parámetros de conexión ----------
        $this->info('=== 1. Configuración LDAP efectiva ===');
        $cfg = config('ldap.connections.default');
        if (!$cfg) {
            $this->error('No existe config("ldap.connections.default"). ¿Caché de config vieja? Ejecuta: php artisan config:clear');
            return self::FAILURE;
        }
        $this->line(sprintf(
            'Host(s): %s | Puerto: %s | Base DN: %s',
            implode(', ', $cfg['hosts']),
            $cfg['port'],
            $cfg['base_dn']
        ));
        $this->line('Bind DN: ' . $cfg['username']);
        $this->line('Bind PASS: ' . str_repeat('*', max(strlen((string) $cfg['password']), 1)) . ' (' . strlen((string) $cfg['password']) . ' chars)');

        // ---------- 2. Conexión + bind de servicio ----------
        $this->info("\n=== 2. Conexión y bind de servicio ===");
        try {
            $ldap = Container::getConnection('default');
            $ldap->connect();
            $this->line('✅ TCP/conexión OK');
            if ($ldap->auth()->bind($cfg['username'], $cfg['password'])) {
                $this->line('✅ Bind de servicio OK (credenciales del .env correctas)');
            } else {
                $this->error('❌ Bind de servicio FALLÓ → revisa LDAP_USERNAME / LDAP_PASSWORD en el .env.');
                $this->error('   Sugerencia: prueba manual → ldapsearch -x -H ldap://' . $cfg['hosts'][0] . ':' . $cfg['port'] . " -D '{$cfg['username']}' -w '<pass>' -b '{$cfg['base_dn']}' '(objectClass=person)' dn");
            }
        } catch (\Throwable $e) {
            $this->error('❌ Error de conexión LDAP: ' . $e->getMessage());
            $this->error('   Si es "Unable to connect": problema de red/nombre. Prueba desde el contenedor app:');
            $this->error('   getent hosts lldap  |  nc -zv <host> ' . $cfg['port']);
            return self::FAILURE;
        }

        // ---------- 3. Listar usuarios LDAP ----------
        $this->info("\n=== 3. Usuarios en LLDAP (primeros 20) ===");
        try {
            $users = $ldap->query()
                ->in($cfg['base_dn'])
                ->whereContains('objectclass', 'person')
                ->limit(20)
                ->get();
            if (count($users) === 0) {
                $this->warn('⚠️ 0 personas encontradas con ese base_dn. Verifica LLDAP_LDAP_BASE_DN del contenedor lldap.');
            }
            foreach ($users as $u) {
                $mail = $u['mail'][0] ?? '(sin mail)';
                $uid = $u['uid'][0] ?? '?';
                $dn = is_array($u['dn']) ? $u['dn'][0] : $u['dn'];
                $this->line("  uid={$uid} | mail={$mail} | dn={$dn}");
            }
        } catch (\Throwable $e) {
            $this->error('Búsqueda falló: ' . $e->getMessage());
        }

        // ---------- 4. Usuarios locales ----------
        $this->info("\n=== 4. Usuarios locales (tabla users) ===");
        foreach (User::orderBy('id')->limit(30)->get() as $u) {
            $this->line(sprintf(
                '  id=%d | %s <%s> | ldap_uid=%s | password=%s | activo=%s',
                $u->id,
                $u->name ?? '-',
                $u->email,
                $u->ldap_uid ?: '-',
                empty($u->password) ? 'VACÍO' : 'hash:' . substr($u->password, 0, 10) . '…',
                var_export($u->activo, true)
            ));
        }

        // ---------- 5. Simular login completo ----------
        $email = $this->argument('email');
        $pass = $this->option('password');
        if ($email && $pass) {
            $this->info("\n=== 5. Simulación de login: {$email} ===");
            $user = User::where('email', $email)->first()
                ?? (new \App\Auth\HybridUserProvider(app('hash')))->retrieveByCredentials(['email' => $email]);
            if (!$user) {
                $this->error("❌ Usuario '{$email}' no existe localmente Y tampoco se encontró en LLDAP por atributo 'mail'.");
                $this->line('   En LLDAP el mail puede estar distinto. Busca arriba en la sección 3 el mail exacto.');
                return self::FAILURE;
            }
            $provider = app(\App\Auth\HybridUserProvider::class);
            $ok = $provider->validateCredentials($user, ['email' => $email, 'password' => $pass]);
            $this->line($ok ? '✅ validateCredentials devolvió TRUE (login debería funcionar)'
                           : '❌ validateCredentials devolvió FALSE. Revisa logs: storage/logs/laravel.log');
        } else {
            $this->comment("\nPara simular un login completo:");
            $this->comment('  php artisan auth:diagnose usuario@dataplus.cu --password="SU_CLAVE"');
        }

        return self::SUCCESS;
    }
}
