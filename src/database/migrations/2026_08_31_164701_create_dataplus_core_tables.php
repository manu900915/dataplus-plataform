<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tipos de negocio
        Schema::create('tipo_negocios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->string('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // 2. Clientes
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->enum('tipo_persona', ['natural', 'juridica'])->default('natural');
            $table->string('documento', 20)->nullable();
            $table->string('nombre');
            $table->string('nombre_comercial')->nullable();
            $table->string('email')->nullable();
            $table->string('telefono')->nullable();
            $table->string('direccion')->nullable();
            $table->string('municipio')->nullable();
            $table->string('provincia')->nullable();
            $table->text('notas')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // 3. Ubicaciones del cliente
        Schema::create('cliente_ubicaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->string('nombre');
            $table->enum('tipo', ['residencial', 'negocio'])->default('residencial');
            $table->foreignId('tipo_negocio_id')->nullable()->constrained('tipo_negocios')->nullOnDelete();
            $table->string('direccion')->nullable();
            $table->string('provincia')->nullable();
            $table->string('municipio')->nullable();
            $table->string('contacto_nombre')->nullable();
            $table->string('contacto_telefono')->nullable();
            $table->text('notas')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // 4. Incidencias
        Schema::create('incidencias', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->enum('tipo', ['CCTV', 'SACI', 'Redes', 'Gestion_Remota'])->default('CCTV');
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->string('direccion_incidencia')->nullable();
            $table->string('contacto_local')->nullable();
            $table->string('telefono_local')->nullable();
            $table->string('titulo');
            $table->text('descripcion');
            $table->text('diagnostico')->nullable();
            $table->foreignId('tecnico_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('estado', ['Pendiente', 'Asignada', 'En_Progreso', 'En_Espera', 'Resuelta', 'Cerrada', 'Cancelada'])->default('Pendiente');
            $table->enum('prioridad', ['Baja', 'Media', 'Alta', 'Critica'])->default('Media');
            $table->timestamp('fecha_reporte')->useCurrent();
            $table->timestamp('fecha_asignacion')->nullable();
            $table->timestamp('fecha_inicio_trabajo')->nullable();
            $table->timestamp('fecha_limite')->nullable();
            $table->timestamp('fecha_resolucion')->nullable();
            $table->timestamp('fecha_cierre')->nullable();
            $table->text('solucion')->nullable();
            $table->text('notas_internas')->nullable();
            $table->boolean('requiere_repuestos')->default(false);
            $table->decimal('costo_estimado', 10, 2)->nullable();
            $table->foreignId('creado_por')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incidencias');
        Schema::dropIfExists('cliente_ubicaciones');
        Schema::dropIfExists('clientes');
        Schema::dropIfExists('tipo_negocios');
    }
};