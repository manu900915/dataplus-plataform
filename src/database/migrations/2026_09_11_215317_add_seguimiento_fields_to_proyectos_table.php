<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proyectos', function (Blueprint $table) {
            $table->enum('tipo_seguimiento', ['instalacion', 'investigacion'])
                  ->default('instalacion')
                  ->after('tipo_proyecto_id');
            $table->enum('estado_kanban', ['por_hacer', 'en_progreso', 'en_revision', 'completado'])
                  ->default('por_hacer')
                  ->after('estado');
        });
    }

    public function down(): void
    {
        Schema::table('proyectos', function (Blueprint $table) {
            $table->dropColumn(['tipo_seguimiento', 'estado_kanban']);
        });
    }
};