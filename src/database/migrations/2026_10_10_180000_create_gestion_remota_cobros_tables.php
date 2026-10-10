<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabla de Suscripciones de Gestión Remota
        if (!Schema::hasTable("suscripciones_gestion_remota")) {
            Schema::create("suscripciones_gestion_remota", function (Blueprint $table) {
                $table->id();
                $table->string("codigo", 50)->unique();
                $table->foreignId("cliente_id")->nullable()->constrained("clientes")->nullOnDelete();
                $table->string("cliente_nombre");
                $table->string("cliente_telefono")->nullable();
                $table->string("servicio_tipo")->default("Gestion_Remota");
                $table->string("tipo_solucion")->default("Router 4G LTE");
                $table->string("sim_numero", 50)->nullable();
                $table->enum("moneda", ["CUP", "USD"])->default("CUP");
                $table->decimal("monto_mensual", 12, 2)->default(3500.00);
                $table->integer("dia_pago_limite")->default(15);
                $table->boolean("mes_adelantado")->default(true);
                $table->boolean("tiene_deuda")->default(false);
                $table->integer("meses_deuda_count")->default(0);
                $table->text("meses_deuda_detalle")->nullable();
                $table->decimal("deuda_acumulada", 12, 2)->default(0.00);
                $table->string("ultimo_mes_pagado", 20)->nullable();
                $table->enum("estado_cobro", ["cobrado", "pendiente", "vencido"])->default("pendiente");
                $table->boolean("activo")->default(true);
                $table->text("notas")->nullable();
                $table->timestamps();
            });
        }

        // 2. Tabla de Pagos de Gestión Remota
        if (!Schema::hasTable("pagos_gestion_remota")) {
            Schema::create("pagos_gestion_remota", function (Blueprint $table) {
                $table->id();
                $table->string("codigo_pago", 50)->unique();
                $table->foreignId("suscripcion_id")->constrained("suscripciones_gestion_remota")->cascadeOnDelete();
                $table->string("cliente_nombre");
                $table->string("mes_servicio", 50);
                $table->date("fecha_pago");
                $table->enum("moneda", ["CUP", "USD"])->default("CUP");
                $table->decimal("monto_esperado", 12, 2);
                $table->decimal("monto_pagado", 12, 2);
                $table->enum("metodo_pago", ["transferencia", "efectivo"])->default("transferencia");
                $table->string("numero_transaccion", 100)->nullable();
                $table->longText("comprobante_url")->nullable();
                $table->string("comprobante_nombre")->nullable();
                $table->string("efectivo_quien_recibe")->nullable();
                $table->string("efectivo_quien_entrega")->nullable();
                $table->string("registrado_por")->nullable();
                $table->text("notas")->nullable();
                $table->timestamps();
            });
        }

        // 3. Población inicial con datos si la tabla está vacía
        $count = DB::table("suscripciones_gestion_remota")->count();
        if ($count === 0) {
            $now = now();
            $subs = [
                [
                    "codigo" => "SUB-001",
                    "cliente_nombre" => "Moraima Guanabo",
                    "cliente_telefono" => "+53 5284 9102",
                    "servicio_tipo" => "Gestion_Remota",
                    "tipo_solucion" => "Router 4G LTE Huawei B310",
                    "sim_numero" => "5352849102",
                    "moneda" => "CUP",
                    "monto_mensual" => 3500.00,
                    "dia_pago_limite" => 15,
                    "mes_adelantado" => true,
                    "tiene_deuda" => false,
                    "meses_deuda_count" => 0,
                    "meses_deuda_detalle" => json_encode([]),
                    "deuda_acumulada" => 0.00,
                    "ultimo_mes_pagado" => "2026-10",
                    "estado_cobro" => "cobrado",
                    "activo" => true,
                    "notas" => "Cliente paga puntual vía Transfermóvil primeros 5 días.",
                    "created_at" => $now,
                    "updated_at" => $now,
                ],
                [
                    "codigo" => "SUB-002",
                    "cliente_nombre" => "Dainet Playa",
                    "cliente_telefono" => "+53 5341 8820",
                    "servicio_tipo" => "Gestion_Remota",
                    "tipo_solucion" => "MikroTik + Módem 4G LTE",
                    "sim_numero" => "5353418820",
                    "moneda" => "CUP",
                    "monto_mensual" => 4200.00,
                    "dia_pago_limite" => 15,
                    "mes_adelantado" => true,
                    "tiene_deuda" => false,
                    "meses_deuda_count" => 0,
                    "meses_deuda_detalle" => json_encode([]),
                    "deuda_acumulada" => 0.00,
                    "ultimo_mes_pagado" => "2026-10",
                    "estado_cobro" => "cobrado",
                    "activo" => true,
                    "notas" => "Pagó el 8 de octubre por EnZona.",
                    "created_at" => $now,
                    "updated_at" => $now,
                ],
                [
                    "codigo" => "SUB-003",
                    "cliente_nombre" => "Restaurante El Biky",
                    "cliente_telefono" => "+53 7832 9940",
                    "servicio_tipo" => "Gestion_Remota",
                    "tipo_solucion" => "Router Industrial Teltonika RUT240",
                    "sim_numero" => "5359114422",
                    "moneda" => "USD",
                    "monto_mensual" => 75.00,
                    "dia_pago_limite" => 15,
                    "mes_adelantado" => true,
                    "tiene_deuda" => true,
                    "meses_deuda_count" => 1,
                    "meses_deuda_detalle" => json_encode(["Septiembre 2026"]),
                    "deuda_acumulada" => 75.00,
                    "ultimo_mes_pagado" => "2026-08",
                    "estado_cobro" => "vencido",
                    "activo" => true,
                    "notas" => "Debe septiembre ($75 USD). Total a cobrar con octubre: $150 USD.",
                    "created_at" => $now,
                    "updated_at" => $now,
                ],
                [
                    "codigo" => "SUB-004",
                    "cliente_nombre" => "Hostal Habana Bella",
                    "cliente_telefono" => "+53 5214 7789",
                    "servicio_tipo" => "Gestion_Remota",
                    "tipo_solucion" => "TP-Link Archer MR600 4G Cat6",
                    "sim_numero" => "5352147789",
                    "moneda" => "USD",
                    "monto_mensual" => 60.00,
                    "dia_pago_limite" => 15,
                    "mes_adelantado" => true,
                    "tiene_deuda" => false,
                    "meses_deuda_count" => 0,
                    "meses_deuda_detalle" => json_encode([]),
                    "deuda_acumulada" => 0.00,
                    "ultimo_mes_pagado" => "2026-09",
                    "estado_cobro" => "pendiente",
                    "activo" => true,
                    "notas" => "Pendiente cobro de octubre ($60 USD) antes del día 15.",
                    "created_at" => $now,
                    "updated_at" => $now,
                ],
                [
                    "codigo" => "SUB-005",
                    "cliente_nombre" => "Taller Automotriz La Ceiba",
                    "cliente_telefono" => "+53 5409 1123",
                    "servicio_tipo" => "Gestion_Remota",
                    "tipo_solucion" => "Router 4G Huawei B315",
                    "sim_numero" => "5354091123",
                    "moneda" => "CUP",
                    "monto_mensual" => 3800.00,
                    "dia_pago_limite" => 15,
                    "mes_adelantado" => true,
                    "tiene_deuda" => true,
                    "meses_deuda_count" => 2,
                    "meses_deuda_detalle" => json_encode(["Agosto 2026", "Septiembre 2026"]),
                    "deuda_acumulada" => 7600.00,
                    "ultimo_mes_pagado" => "2026-07",
                    "estado_cobro" => "vencido",
                    "activo" => true,
                    "notas" => "Debe 2 meses ($7,600 CUP). Con octubre suma $11,400 CUP.",
                    "created_at" => $now,
                    "updated_at" => $now,
                ],
                [
                    "codigo" => "SUB-006",
                    "cliente_nombre" => "Distribuidora del Caribe",
                    "cliente_telefono" => "+53 5100 2299",
                    "servicio_tipo" => "Gestion_Remota",
                    "tipo_solucion" => "Router Balanceador 4G Dual SIM",
                    "sim_numero" => "5351002299",
                    "moneda" => "USD",
                    "monto_mensual" => 80.00,
                    "dia_pago_limite" => 15,
                    "mes_adelantado" => true,
                    "tiene_deuda" => true,
                    "meses_deuda_count" => 3,
                    "meses_deuda_detalle" => json_encode(["Julio 2026", "Agosto 2026", "Septiembre 2026"]),
                    "deuda_acumulada" => 240.00,
                    "ultimo_mes_pagado" => "2026-06",
                    "estado_cobro" => "vencido",
                    "activo" => true,
                    "notas" => "Mayor deudor en USD. Deuda acumulada $240 USD. Total $320 USD.",
                    "created_at" => $now,
                    "updated_at" => $now,
                ],
            ];

            foreach ($subs as $sub) {
                $subId = DB::table("suscripciones_gestion_remota")->insertGetId($sub);

                if ($sub["estado_cobro"] === "cobrado") {
                    DB::table("pagos_gestion_remota")->insert([
                        "codigo_pago" => "PAG-2026-000" . $subId,
                        "suscripcion_id" => $subId,
                        "cliente_nombre" => $sub["cliente_nombre"],
                        "mes_servicio" => "Octubre 2026",
                        "fecha_pago" => "2026-10-05",
                        "moneda" => $sub["moneda"],
                        "monto_esperado" => $sub["monto_mensual"],
                        "monto_pagado" => $sub["monto_mensual"],
                        "metodo_pago" => "transferencia",
                        "numero_transaccion" => "TRF-98214" . $subId,
                        "comprobante_nombre" => "Comprobante_WhatsApp_" . $sub["codigo"] . ".jpg",
                        "registrado_por" => "Admin Finanzas",
                        "notas" => "Transferencia confirmada y acreditada.",
                        "created_at" => $now,
                        "updated_at" => $now,
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists("pagos_gestion_remota");
        Schema::dropIfExists("suscripciones_gestion_remota");
    }
};
