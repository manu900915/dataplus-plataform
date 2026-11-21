<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('items') && !Schema::hasColumn('items', 'imagen')) {
            Schema::table('items', function (Blueprint $table) {
                $table->string('imagen')->nullable()->after('descripcion');
            });
        }

        if (!Schema::hasTable('ventas')) {
            Schema::create('ventas', function (Blueprint $table) {
                $table->id();
                $table->string('codigo')->unique();
                $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
                $table->string('cliente_nombre')->nullable();
                $table->foreignId('almacen_id')->nullable()->constrained('almacenes')->nullOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->dateTime('fecha_venta');
                $table->string('metodo_pago', 40)->default('CUP');
                $table->string('moneda', 10)->default('CUP');
                $table->decimal('tasa_cambio', 12, 4)->default(1);
                $table->decimal('margen_ganancia_global', 8, 2)->nullable();
                $table->string('referencia_pago', 120)->nullable();
                $table->decimal('costo_total', 14, 2)->default(0);
                $table->decimal('subtotal', 14, 2)->default(0);
                $table->decimal('descuento', 14, 2)->default(0);
                $table->decimal('monto_total', 14, 2)->default(0);
                $table->decimal('ganancia_total', 14, 2)->default(0);
                $table->string('estado', 30)->default('completada');
                $table->boolean('inventario_descontado')->default(false);
                $table->text('notas')->nullable();
                $table->timestamps();

                $table->index(['estado', 'fecha_venta']);
                $table->index(['metodo_pago', 'moneda']);
            });
        }

        if (!Schema::hasTable('venta_items')) {
            Schema::create('venta_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('venta_id')->constrained('ventas')->cascadeOnDelete();
                $table->foreignId('item_id')->constrained('items')->restrictOnDelete();
                $table->foreignId('almacen_id')->nullable()->constrained('almacenes')->nullOnDelete();
                $table->decimal('cantidad', 12, 3)->default(1);
                $table->decimal('precio_costo_unitario', 14, 2)->default(0);
                $table->decimal('margen_ganancia', 8, 2)->default(60);
                $table->decimal('precio_unitario', 14, 2)->default(0);
                $table->decimal('subtotal', 14, 2)->default(0);
                $table->decimal('ganancia_linea', 14, 2)->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('venta_items');
        Schema::dropIfExists('ventas');

        if (Schema::hasTable('items') && Schema::hasColumn('items', 'imagen')) {
            Schema::table('items', function (Blueprint $table) {
                $table->dropColumn('imagen');
            });
        }
    }
};
