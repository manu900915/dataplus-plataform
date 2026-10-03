<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('incidencias', function (Blueprint $table) {
            $table->foreignId('cliente_ubicacion_id')
                ->nullable()
                ->after('cliente_id')
                ->constrained('cliente_ubicaciones')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('incidencias', function (Blueprint $table) {
            $table->dropForeign(['cliente_ubicacion_id']);
            $table->dropColumn('cliente_ubicacion_id');
        });
    }
};