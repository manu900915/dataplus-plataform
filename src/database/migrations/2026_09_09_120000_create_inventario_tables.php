<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventarioTables extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('categorias_item')) {
            Schema::create('categorias_item', function (Blueprint $table) {
                $table->id();
                $table->string('nombre');
                $table->enum('tipo', ['equipamiento', 'material', 'ambos'])->default('material');
                $table->boolean('activo')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('items')) {
            Schema::create('items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('categoria_id')->constrained('categorias_item')->cascadeOnDelete();
                $table->string('codigo')->unique();
                $table->string('nombre');
                $table->text('descripcion')->nullable();
                $table->string('unidad_medida')->default('u');
                $table->decimal('precio_unitario', 12, 2)->default(0);
                $table->decimal('stock_actual', 12, 3)->default(0);
                $table->decimal('stock_minimo', 12, 3)->default(0);
                $table->boolean('es_equipamiento')->default(false);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('almacenes')) {
            Schema::create('almacenes', function (Blueprint $table) {
                $table->id();
                $table->string('nombre');
                $table->string('provincia')->nullable();
                $table->string('municipio')->nullable();
                $table->foreignId('responsable_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('inventario_movimientos')) {
            Schema::create('inventario_movimientos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
                $table->foreignId('almacen_id')->nullable()->constrained('almacenes')->nullOnDelete();
                $table->enum('tipo', ['entrada', 'salida', 'ajuste', 'devolucion']);
                $table->decimal('cantidad', 12, 3);
                $table->decimal('costo_unitario', 12, 2)->nullable();
                $table->string('motivo')->nullable();
                $table->nullableMorphs('documento');
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('inventario_movimientos');
        Schema::dropIfExists('almacenes');
        Schema::dropIfExists('items');
        Schema::dropIfExists('categorias_item');
    }
}