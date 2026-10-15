<?php

namespace App\Console\Commands;

use App\Models\Cliente;
use App\Models\PagoGestionRemota;
use App\Models\SuscripcionGestionRemota;
use App\Services\ClienteSyncService;
use Illuminate\Console\Command;

class SyncFacturacionClientesCommand extends Command
{
    protected $signature = 'facturacion:sync-clientes 
                            {--reset : Limpiar datos de prueba y poner mensualidad y deudas en 0}';

    protected $description = 'Sincroniza todos los clientes con el módulo de Facturación de Servicios (Gestión Remota) con mensualidad y deuda en 0 para asignación por contabilidad';

    public function handle(): int
    {
        $this->info("🚀 Sincronizando clientes con el módulo de Facturación...");

        if ($this->option('reset')) {
            $this->warn("⚠️  Modo --reset activado: Limpiando cobros y suscripciones de prueba...");
            PagoGestionRemota::truncate();
            SuscripcionGestionRemota::truncate();
            $this->line("   Cobros y suscripciones previas eliminadas.");
        }

        // Si no existen clientes, sincronizar desde el CSV oficial
        if (Cliente::count() === 0) {
            $this->line("ℹ️  No hay clientes en BD. Cargando desde clientes_listado_unico.csv...");
            try {
                /** @var ClienteSyncService $syncService */
                $syncService = app(ClienteSyncService::class);
                $syncService->syncFromCsv();
                $this->info("   Clientes importados exitosamente.");
            } catch (\Throwable $e) {
                $this->error("   Error al importar CSV: " . $e->getMessage());
            }
        }

        $clientes = Cliente::where('activo', true)->orderBy('nombre')->get();
        $this->line("📋 Clientes activos encontrados: " . $clientes->count());

        $agregados = 0;
        $actualizados = 0;

        foreach ($clientes as $c) {
            $nombre = trim($c->nombre);
            if (empty($nombre)) continue;

            $tel = $c->telefono ?: ($c->contactos()->first()?->telefono ?? '');

            $sub = SuscripcionGestionRemota::where('cliente_id', $c->id)
                ->orWhere('cliente_nombre', $nombre)
                ->first();

            if (!$sub) {
                $codigo = "SUB-" . str_pad((string) (SuscripcionGestionRemota::count() + 1), 3, "0", STR_PAD_LEFT);
                SuscripcionGestionRemota::create([
                    'codigo' => $codigo,
                    'cliente_id' => $c->id,
                    'cliente_nombre' => $nombre,
                    'cliente_telefono' => $tel,
                    'servicio_tipo' => 'Gestion_Remota',
                    'tipo_solucion' => 'Router 4G LTE',
                    'sim_numero' => '',
                    'moneda' => 'CUP',
                    'monto_mensual' => 0.00,
                    'dia_pago_limite' => 15,
                    'mes_adelantado' => true,
                    'tiene_deuda' => false,
                    'meses_deuda_count' => 0,
                    'meses_deuda_detalle' => json_encode([]),
                    'deuda_acumulada' => 0.00,
                    'ultimo_mes_pagado' => null,
                    'estado_cobro' => 'al_dia',
                    'activo' => true,
                    'notas' => 'Sincronizado desde Directorio de Clientes. Tarifa inicial en $0.00 CUP para asignación por contabilidad.',
                ]);
                $agregados++;
            } else {
                $cambios = [];
                if (!$sub->cliente_id) $cambios['cliente_id'] = $c->id;
                if (empty($sub->cliente_telefono) && !empty($tel)) $cambios['cliente_telefono'] = $tel;
                if (!empty($cambios)) {
                    $sub->update($cambios);
                    $actualizados++;
                }
            }
        }

        $this->info("✅ Proceso completado:");
        $this->table(
            ['Nuevos Registros Creados (Base 0)', 'Clientes Existentes Actualizados', 'Total Suscripciones Actuales'],
            [[$agregados, $actualizados, SuscripcionGestionRemota::count()]]
        );

        return Command::SUCCESS;
    }
}
