<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Servicio extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_ubicacion_id',
        'tipo',
        'fecha_instalacion',
        'brigada_id',
        'tecnico_id',
        'estado',
        'notas',

        // ─── CCTV ───
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

        // ─── SACI ───
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

        // ─── Documentos de Referencia ───
        'documentos',

        // ─── Gestión Remota ───
        'gr_tipo_solucion',
        'gr_sim_numero',
        'gr_sim_tipo',
        'gr_marca_modelo',
        'gr_tipo_internet',
        'gr_recarga_por',
        'gr_recarga_monto',
        'gr_ultima_recarga'
    ];

    protected $casts = [
        'fecha_instalacion'          => 'date',
        'saci_tarjeta_conexion'      => 'boolean',
        'saci_sensores_pir'          => 'integer',
        'saci_sensores_magneticos'   => 'integer',
        'saci_sensores_perimetrales' => 'integer',
        'cctv_canales'               => 'integer',
        'documentos'                 => 'array',
        'gr_recarga_monto'           => 'decimal:2',
        'gr_ultima_recarga'          => 'date',
    ];

    public function ubicacion(): BelongsTo
    {
        return $this->belongsTo(ClienteUbicacion::class, 'cliente_ubicacion_id');
    }

    public function cliente(): BelongsTo
    {
        return $this->hasOneThrough(
            Cliente::class,
            ClienteUbicacion::class,
            'id',
            'id',
            'cliente_ubicacion_id',
            'cliente_id',
        );
    }

    public function brigada(): BelongsTo
    {
        return $this->belongsTo(Brigada::class);
    }

    public function tecnico(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tecnico_id');
    }
}
