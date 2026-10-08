<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Proyectos completados en estado general deben tener estado_kanban = completado
        DB::table('proyectos')
            ->where('estado', 'completado')
            ->update(['estado_kanban' => 'completado']);

        // 2. Proyectos con estado_kanban = completado deben tener estado = completado
        DB::table('proyectos')
            ->where('estado_kanban', 'completado')
            ->update(['estado' => 'completado']);

        // 3. Proyectos en progreso
        DB::table('proyectos')
            ->where('estado', 'en_progreso')
            ->where(function ($query) {
                $query->whereNull('estado_kanban')
                      ->orWhere('estado_kanban', 'por_hacer');
            })
            ->update(['estado_kanban' => 'en_progreso']);

        // 4. Si tipo_seguimiento es nulo, asignarle 'instalacion' por defecto
        DB::table('proyectos')
            ->whereNull('tipo_seguimiento')
            ->update(['tipo_seguimiento' => 'instalacion']);
    }

    public function down(): void
    {
        // Operación no destructiva
    }
};
