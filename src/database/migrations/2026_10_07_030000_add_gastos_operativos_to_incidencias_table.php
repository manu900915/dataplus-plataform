<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('incidencias', function (Blueprint $table) {
            $table->decimal('gasto_transporte', 10, 2)->default(0)->nullable()->after('costo_estimado');
            $table->decimal('gasto_almuerzo', 10, 2)->default(0)->nullable()->after('gasto_transporte');
            $table->decimal('monto_facturado', 12, 2)->nullable()->after('gasto_almuerzo');
            $table->text('detalle_gastos')->nullable()->after('monto_facturado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidencias', function (Blueprint $table) {
            $table->dropColumn([
                'gasto_transporte',
                'gasto_almuerzo',
                'monto_facturado',
                'detalle_gastos',
            ]);
        });
    }
};
