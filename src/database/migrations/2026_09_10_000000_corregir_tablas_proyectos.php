<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Si ya existe la tabla, la eliminamos para recrear correctamente
        if (Schema::hasTable('lineas_presupuesto')) {
            Schema::dropIfExists('lineas_presupuesto');
        }

        // Crear tabla de líneas de presupuesto con nombre correcto
        Schema::create('lineas_presupuesto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyecto_id')->constrained('proyectos')->cascadeOnDelete();
            $table->foreignId('item_id')->nullable()->constrained('items')->nullOnDelete();
            $table->enum('tipo_linea', [
                'equipamiento', 
                'material', 
                'mano_obra', 
                'transporte', 
                'alimentacion', 
                'otro'
            ])->required();
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
    }
};