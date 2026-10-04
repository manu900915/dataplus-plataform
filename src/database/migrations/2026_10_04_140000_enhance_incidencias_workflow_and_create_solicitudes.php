<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Ampliar campos de Incidencias para Revisión de Supervisor y Cierre Comercial
        Schema::table('incidencias', function (Blueprint $table) {
            $table->foreignId('revisado_por')
                ->nullable()
                ->after('tecnico_id')
                ->constrained('users')
                ->nullOnDelete();

            $table->dateTime('fecha_revision_supervisor')
                ->nullable()
                ->after('fecha_resolucion');

            $table->text('notas_supervisor')
                ->nullable()
                ->after('solucion');

            $table->foreignId('cerrado_por')
                ->nullable()
                ->after('creado_por')
                ->constrained('users')
                ->nullOnDelete();

            $table->boolean('conformidad_cliente')
                ->nullable()
                ->after('requiere_repuestos');

            $table->text('observaciones_cierre_comercial')
                ->nullable()
                ->after('notas_supervisor');
        });

        // 2. Modificar constraint del estado según el motor de base de datos (PostgreSQL / MySQL)
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'pgsql') {
            DB::statement("ALTER TABLE incidencias DROP CONSTRAINT IF EXISTS incidencias_estado_check");
            DB::statement("ALTER TABLE incidencias ADD CONSTRAINT incidencias_estado_check CHECK (estado::text IN ('Pendiente', 'Asignada', 'En_Progreso', 'En_Espera', 'Resuelta', 'Revisada_Supervisor', 'Cerrada', 'Cancelada'))");
        } elseif ($driver === 'mysql') {
            DB::statement("ALTER TABLE incidencias MODIFY COLUMN estado ENUM('Pendiente', 'Asignada', 'En_Progreso', 'En_Espera', 'Resuelta', 'Revisada_Supervisor', 'Cerrada', 'Cancelada') NOT NULL DEFAULT 'Pendiente'");
        }

        // 3. Crear tabla de Solicitudes Comerciales (Mesa de Entrada para Nuevos Proyectos)
        Schema::create('solicitudes_servicio', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignId('cliente_ubicacion_id')->nullable()->constrained('cliente_ubicaciones')->nullOnDelete();
            $table->foreignId('comercial_id')->constrained('users')->cascadeOnDelete();
            $table->string('titulo');
            $table->text('descripcion');
            $table->foreignId('tipo_proyecto_id')->nullable()->constrained('tipos_proyecto')->nullOnDelete();
            $table->string('prioridad')->default('Media');
            $table->decimal('presupuesto_estimado', 12, 2)->nullable();
            $table->string('estado')->default('Pendiente_Aprobacion');
            $table->foreignId('supervisor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('fecha_aprobacion')->nullable();
            $table->text('notas_supervisor')->nullable();
            $table->foreignId('proyecto_id')->nullable()->constrained('proyectos')->nullOnDelete();
            $table->timestamps();

            $table->index(['cliente_id', 'estado']);
        });

        if ($driver === 'pgsql') {
            DB::statement("ALTER TABLE solicitudes_servicio ADD CONSTRAINT solicitudes_prioridad_check CHECK (prioridad IN ('Baja', 'Media', 'Alta', 'Urgente'))");
            DB::statement("ALTER TABLE solicitudes_servicio ADD CONSTRAINT solicitudes_estado_check CHECK (estado IN ('Pendiente_Aprobacion', 'Aprobada', 'Rechazada', 'Convertida_Proyecto'))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitudes_servicio');

        Schema::table('incidencias', function (Blueprint $table) {
            $table->dropForeign(['revisado_por']);
            $table->dropColumn('revisado_por');
            $table->dropColumn('fecha_revision_supervisor');
            $table->dropColumn('notas_supervisor');
            $table->dropForeign(['cerrado_por']);
            $table->dropColumn('cerrado_por');
            $table->dropColumn('conformidad_cliente');
            $table->dropColumn('observaciones_cierre_comercial');
        });
    }
};
