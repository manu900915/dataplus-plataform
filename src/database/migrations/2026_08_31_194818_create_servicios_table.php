<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('servicios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_ubicacion_id')->constrained('cliente_ubicaciones')->cascadeOnDelete();
            $table->enum('tipo', ['CCTV', 'SACI', 'Gestion_Remota'])->default('CCTV');
            $table->date('fecha_instalacion')->nullable();
            $table->string('brigada')->nullable()->comment('Quién o qué brigada instaló');
            $table->enum('estado', ['Activo', 'Inactivo', 'En_Reparacion', 'Suspendido'])->default('Activo');
            $table->text('notas')->nullable();

            // ─── Campos específicos de Gestión Remota ───
            $table->enum('gr_tipo_solucion', ['Router4g', 'Router+Modem', 'Router+ADSL', 'Otros'])->nullable();
            $table->string('gr_sim_numero', 20)->nullable()->comment('Número de la SIM card');
            $table->string('gr_marca_modelo')->nullable()->comment('Marca y modelo del equipo');
            $table->enum('gr_tipo_internet', ['Abierto', 'Filtrado', 'Cerrado'])->nullable();
            $table->enum('gr_recarga_por', ['Nosotros', 'Cliente'])->nullable()->default('Cliente');
            $table->decimal('gr_recarga_monto', 10, 2)->nullable()->default(360.00);
            $table->date('gr_ultima_recarga')->nullable();

            $table->timestamps();

            $table->index(['cliente_ubicacion_id', 'tipo']);
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servicios');
    }
};