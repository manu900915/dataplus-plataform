<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('servicios', function (Blueprint $table) {
            $table->enum('gr_sim_tipo', ['Particular', 'Corporativa'])
                  ->nullable()
                  ->after('gr_sim_numero')
                  ->comment('Tipo de SIM: Particular (recargamos nosotros) o Corporativa (contrato ETECSA del cliente)');
        });
    }

    public function down(): void
    {
        Schema::table('servicios', function (Blueprint $table) {
            $table->dropColumn('gr_sim_tipo');
        });
    }
};