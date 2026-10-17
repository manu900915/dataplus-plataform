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

        // 1. Sincronizar clientes con servicios de Gestión Remota
        $serviciosGR = \App\Models\Servicio::where('tipo', 'Gestion_Remota')->with(['ubicacion.cliente'])->get();
        $this->line("📡 Servicios de Gestión Remota encontrados: " . $serviciosGR->count());

        $agregados = 0;
        $actualizados = 0;

        if ($serviciosGR->isNotEmpty()) {
            foreach ($serviciosGR as $srv) {
                $c = $srv->ubicacion?->cliente;
                if (!$c && $srv->cliente_ubicacion_id) {
                    $ub = \App\Models\ClienteUbicacion::find($srv->cliente_ubicacion_id);
                    $c = $ub ? Cliente::find($ub->cliente_id) : null;
                }
                if (!$c) continue;

                $nombre = trim($c->nombre ?: ($c->nombre_comercial ?: ('Cliente #' . $c->id)));
                $tel = trim($c->telefono ?: ($c->contactos()->first()?->telefono ?? ''));
                $solucion = trim($srv->gr_tipo_solucion ?: ($srv->gr_marca_modelo ?: 'Router 4G LTE'));
                $sim = trim($srv->gr_sim_numero ?: '');
                $tarifa = max(0, (float) ($srv->gr_recarga_monto ?? 0));

                $sub = SuscripcionGestionRemota::where('cliente_id', $c->id)
                    ->orWhereRaw('LOWER(TRIM(cliente_nombre)) = ?', [strtolower($nombre)])
                    ->first();

                if (!$sub) {
                    $nextNum = ((int) (SuscripcionGestionRemota::max('id') ?? 0)) + 1;
                    $codigo = "SUB-" . str_pad((string) $nextNum, 3, "0", STR_PAD_LEFT);
                    while (SuscripcionGestionRemota::where('codigo', $codigo)->exists()) {
                        $nextNum++;
                        $codigo = "SUB-" . str_pad((string) $nextNum, 3, "0", STR_PAD_LEFT);
                    }

                    SuscripcionGestionRemota::create([
                        'codigo' => $codigo,
                        'cliente_id' => $c->id,
                        'cliente_nombre' => $nombre,
                        'cliente_telefono' => $tel,
                        'servicio_tipo' => 'Gestion_Remota',
                        'tipo_solucion' => $solucion,
                        'sim_numero' => $sim,
                        'moneda' => 'CUP',
                        'monto_mensual' => $tarifa,
                        'dia_pago_limite' => 15,
                        'mes_adelantado' => true,
                        'tiene_deuda' => false,
                        'meses_deuda_count' => 0,
                        'meses_deuda_detalle' => json_encode([]),
                        'deuda_acumulada' => 0.00,
                        'ultimo_mes_pagado' => null,
                        'estado_cobro' => 'pendiente',
                        'activo' => true,
                        'notas' => 'Sincronizado desde Servicio de Gestión Remota.',
                    ]);
                    $agregados++;
                } else {
                    $cambios = [];
                    if (!$sub->cliente_id) $cambios['cliente_id'] = $c->id;
                    if (empty($sub->sim_numero) && !empty($sim)) $cambios['sim_numero'] = $sim;
                    if (empty($sub->tipo_solucion) && !empty($solucion)) $cambios['tipo_solucion'] = $solucion;
                    if (empty($sub->cliente_telefono) && !empty($tel)) $cambios['cliente_telefono'] = $tel;
                    if (!empty($cambios)) {
                        $sub->update($cambios);
                        $actualizados++;
                    }
                }
            }
        } else {
            $clientes = Cliente::where('activo', true)->orderBy('nombre')->get();
            $this->line("📋 Clientes activos encontrados: " . $clientes->count());

            foreach ($clientes as $c) {
                $nombre = trim($c->nombre ?: ('Cliente #' . $c->id));
                if (empty($nombre)) continue;

                $tel = trim($c->telefono ?: ($c->contactos()->first()?->telefono ?? ''));

                $sub = SuscripcionGestionRemota::where('cliente_id', $c->id)
                    ->orWhereRaw('LOWER(TRIM(cliente_nombre)) = ?', [strtolower($nombre)])
                    ->first();

                if (!$sub) {
                    $nextNum = ((int) (SuscripcionGestionRemota::max('id') ?? 0)) + 1;
                    $codigo = "SUB-" . str_pad((string) $nextNum, 3, "0", STR_PAD_LEFT);
                    while (SuscripcionGestionRemota::where('codigo', $codigo)->exists()) {
                        $nextNum++;
                        $codigo = "SUB-" . str_pad((string) $nextNum, 3, "0", STR_PAD_LEFT);
                    }

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
                        'estado_cobro' => 'pendiente',
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
        }

        $this->info("✅ Proceso completado:");
        $this->table(
            ['Nuevos Registros Creados (Base 0)', 'Clientes Existentes Actualizados', 'Total Suscripciones Actuales'],
            [[$agregados, $actualizados, SuscripcionGestionRemota::count()]]
        );

        return Command::SUCCESS;
    }
}
