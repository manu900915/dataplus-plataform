<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            Role::firstOrCreate(['name' => 'Comercial', 'guard_name' => 'web']);
            Role::firstOrCreate(['name' => 'Especialista', 'guard_name' => 'web']);
            Role::firstOrCreate(['name' => 'Supervisor', 'guard_name' => 'web']);
            Role::firstOrCreate(['name' => 'Técnico', 'guard_name' => 'web']);
            Role::firstOrCreate(['name' => 'Administrador', 'guard_name' => 'web']);
        } catch (\Throwable $e) {
            // Silencioso si la tabla de roles aún no ha corrido en pruebas
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No destructive rollback of roles
    }
};
