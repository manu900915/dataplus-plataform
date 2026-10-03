<?php

namespace App\Console\Commands;

use App\Services\LdapSyncService;
use Illuminate\Console\Command;

class SyncLdapUsersCommand extends Command
{
    protected $signature = 'ldap:sync-users {--role= : Rol por defecto para los usuarios importados}';

    protected $description = 'Sincroniza todos los usuarios desde el directorio LDAP/LLDAP a la base de datos de DataPlus';

    public function handle(LdapSyncService $syncService): int
    {
        $this->info('Iniciando sincronización con el servidor LDAP...');

        $test = $syncService->testConnection();
        if (!$test['success']) {
            $this->error("No se pudo conectar al servidor LDAP: {$test['message']}");
            return 1;
        }

        $this->line("Conexión establecida con {$test['host']}:{$test['port']} ({$test['latency']}ms)");

        $role = $this->option('role');
        $result = $syncService->syncAllUsers($role);

        $this->table(
            ['Total en LDAP', 'Nuevos Importados', 'Actualizados', 'Errores'],
            [[$result['total'], $result['imported'], $result['updated'], count($result['errors'])]]
        );

        if (!empty($result['errors'])) {
            foreach ($result['errors'] as $err) {
                $this->warn($err);
            }
        }

        $this->info('✅ Sincronización completada con éxito.');

        return 0;
    }
}
