<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('servicios')) {
            Schema::table('servicios', function (Blueprint $table) {
                if (!Schema::hasColumn('servicios', 'gr_mensualidad_monto')) {
                    $table->decimal('gr_mensualidad_monto', 12, 2)->nullable()->default(0.00)->after('gr_ultima_recarga');
                }
                if (!Schema::hasColumn('servicios', 'gr_mensualidad_moneda')) {
                    $table->string('gr_mensualidad_moneda', 10)->default('CUP')->after('gr_mensualidad_monto');
                }
                if (!Schema::hasColumn('servicios', 'gr_periodicidad_pago')) {
                    $table->string('gr_periodicidad_pago', 20)->default('mensual')->after('gr_mensualidad_moneda');
                }
                if (!Schema::hasColumn('servicios', 'gr_meses_pago')) {
                    $table->unsignedSmallInteger('gr_meses_pago')->default(1)->after('gr_periodicidad_pago');
                }
            });
        }

        if (Schema::hasTable('suscripciones_gestion_remota')) {
            Schema::table('suscripciones_gestion_remota', function (Blueprint $table) {
                if (!Schema::hasColumn('suscripciones_gestion_remota', 'servicio_id')) {
                    $table->foreignId('servicio_id')
                        ->nullable()
                        ->after('cliente_id')
                        ->constrained('servicios')
                        ->nullOnDelete();
                }
                if (!Schema::hasColumn('suscripciones_gestion_remota', 'periodicidad_pago')) {
                    $table->string('periodicidad_pago', 20)->default('mensual')->after('monto_mensual');
                }
                if (!Schema::hasColumn('suscripciones_gestion_remota', 'meses_periodo')) {
                    $table->unsignedSmallInteger('meses_periodo')->default(1)->after('periodicidad_pago');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('suscripciones_gestion_remota')) {
            Schema::table('suscripciones_gestion_remota', function (Blueprint $table) {
                if (Schema::hasColumn('suscripciones_gestion_remota', 'servicio_id')) {
                    $table->dropForeign(['servicio_id']);
                    $table->dropColumn('servicio_id');
                }
                if (Schema::hasColumn('suscripciones_gestion_remota', 'meses_periodo')) {
                    $table->dropColumn('meses_periodo');
                }
                if (Schema::hasColumn('suscripciones_gestion_remota', 'periodicidad_pago')) {
                    $table->dropColumn('periodicidad_pago');
                }
            });
        }

        if (Schema::hasTable('servicios')) {
            Schema::table('servicios', function (Blueprint $table) {
                $cols = array_filter([
                    'gr_mensualidad_monto',
                    'gr_mensualidad_moneda',
                    'gr_periodicidad_pago',
                    'gr_meses_pago',
                ], fn ($col) => Schema::hasColumn('servicios', $col));

                if (!empty($cols)) {
                    $table->dropColumn(array_values($cols));
                }
            });
        }
    }
};
