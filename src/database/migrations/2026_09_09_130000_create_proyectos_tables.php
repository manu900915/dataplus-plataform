<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tipos de proyecto (dinámicos)
        Schema::create('tipos_proyecto', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // Proyectos
        Schema::create('proyectos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipo_proyecto_id')->constrained('tipos_proyecto')->cascadeOnDelete();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->foreignId('cliente_ubicacion_id')->nullable()->constrained('cliente_ubicaciones')->nullOnDelete();
            $table->string('codigo')->unique();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->foreignId('responsable_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('estado', ['borrador', 'en_progreso', 'completado', 'cancelado'])->default('borrador');
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->decimal('presupuesto_total', 12, 2)->default(0);
            $table->text('notas')->nullable();
            $table->timestamps();
        });

        // Líneas de presupuesto
        Schema::create('lineas_presupuesto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyecto_id')->constrained('proyectos')->cascadeOnDelete();
            $table->foreignId('item_id')->nullable()->constrained('items')->nullOnDelete();
            $table->enum('tipo_linea', ['equipamiento', 'material', 'mano_obra', 'transporte', 'alimentacion', 'otro'])->required();
            $table->string('descripcion');
            $table->decimal('cantidad', 12, 3)->default(1);
            $table->decimal('costo_unitario', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->boolean('descontar_inventario')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lineas_presupuesto');
        Schema::dropIfExists('proyectos');
        Schema::dropIfExists('tipos_proyecto');
    }
};