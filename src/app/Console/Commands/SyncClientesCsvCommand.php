<?php

namespace App\Console\Commands;

use App\Services\ClienteSyncService;
use Illuminate\Console\Command;

class SyncClientesCsvCommand extends Command
{
    protected $signature = 'clientes:sync-csv 
                            {--archivo= : Ruta al archivo CSV (por defecto usa database/data/clientes_listado_unico.csv)}
                            {--year=2026 : Año para prefijo del código (ej: 2026 para CLI-2026-ID)}
                            {--pad : Rellenar con ceros a 3 dígitos (ej: CLI-2026-001)}';

    protected $description = 'Corrige y sincroniza los códigos de clientes CLI-año-ID y sus nombres según el listado único oficial en CSV';

    public function handle(ClienteSyncService $syncService): int
    {
        $archivo = $this->option('archivo');
        $year = (int) ($this->option('year') ?: 2026);
        $pad = (bool) $this->option('pad');

        $this->info("🚀 Sincronizando clientes según listado único oficial...");
        $this->line("Formato de código objetivo: CLI-{$year}-" . ($pad ? '001/247' : '1/247'));

        try {
            $result = $syncService->syncFromCsv($archivo, $year, $pad);

            $this->table(
                ['Total Válidos en CSV', 'Nuevos Creados', 'Actualizados / Corregidos', 'Registros Numéricos Depurados'],
                [[$result['total_csv'], $result['created'], $result['updated'], $result['cleaned']]]
            );

            $this->info("✅ Clientes sincronizados exitosamente.");
            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("❌ Error durante la sincronización: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
