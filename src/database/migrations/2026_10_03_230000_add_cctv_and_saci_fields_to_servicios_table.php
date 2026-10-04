<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('servicios', function (Blueprint $table) {
            // ─── CCTV ───
            $table->string('cctv_solucion')->nullable()->comment('DVR, NVR, Wifi');
            $table->integer('cctv_canales')->nullable();
            $table->string('cctv_marca')->nullable();
            $table->string('cctv_modelo')->nullable();
            $table->string('cctv_usuario')->nullable();
            $table->string('cctv_password')->nullable();
            $table->string('cctv_tipo_conexion')->nullable()->comment('Local, P2P');
            $table->string('cctv_ip')->nullable();
            $table->string('cctv_app')->nullable();
            $table->string('cctv_ssid')->nullable();

            // ─── SACI ───
            $table->string('saci_solucion')->nullable()->comment('Cableada, Inalambrica');
            $table->string('saci_marca')->nullable();
            $table->string('saci_modelo')->nullable();
            $table->integer('saci_sensores_pir')->nullable()->default(0);
            $table->integer('saci_sensores_magneticos')->nullable()->default(0);
            $table->integer('saci_sensores_perimetrales')->nullable()->default(0);
            $table->string('saci_codigo_instalador')->nullable();
            $table->boolean('saci_tarjeta_conexion')->nullable()->default(false);
            $table->string('saci_tarjeta_usuario_password')->nullable();
            $table->string('saci_ssid_password')->nullable();
            $table->string('saci_ip')->nullable();
            $table->string('saci_app')->nullable();
            $table->string('saci_app_usuario_password')->nullable();

            // ─── Documentos de Referencia ───
            $table->json('documentos')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('servicios', function (Blueprint $table) {
            $table->dropColumn([
                'cctv_solucion',
                'cctv_canales',
                'cctv_marca',
                'cctv_modelo',
                'cctv_usuario',
                'cctv_password',
                'cctv_tipo_conexion',
                'cctv_ip',
                'cctv_app',
                'cctv_ssid',
                'saci_solucion',
                'saci_marca',
                'saci_modelo',
                'saci_sensores_pir',
                'saci_sensores_magneticos',
                'saci_sensores_perimetrales',
                'saci_codigo_instalador',
                'saci_tarjeta_conexion',
                'saci_tarjeta_usuario_password',
                'saci_ssid_password',
                'saci_ip',
                'saci_app',
                'saci_app_usuario_password',
                'documentos',
            ]);
        });
    }
};
