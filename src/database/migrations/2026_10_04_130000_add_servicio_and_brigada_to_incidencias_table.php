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
            $table->foreignId('servicio_id')
                ->nullable()
                ->after('cliente_ubicacion_id')
                ->constrained('servicios')
                ->nullOnDelete();

            $table->foreignId('brigada_id')
                ->nullable()
                ->after('tecnico_id')
                ->constrained('brigadas')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidencias', function (Blueprint $table) {
            $table->dropForeign(['servicio_id']);
            $table->dropColumn('servicio_id');

            $table->dropForeign(['brigada_id']);
            $table->dropColumn('brigada_id');
        });
    }
};
