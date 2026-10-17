<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

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

    public function cliente(): HasOneThrough
    {
        return $this->hasOneThrough(
            Cliente::class,
            ClienteUbicacion::class,
            'id',
            'id',
            'cliente_ubicacion_id',
            'cliente_id'
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

    protected static function booted(): void
    {
        static::saved(function (Servicio $servicio) {
            if ($servicio->tipo === 'Gestion_Remota') {
                $servicio->sincronizarFacturacion();
            }
        });
    }

    /**
     * Sincronizar automáticamente este servicio de Gestión Remota en la tabla de facturación
     */
    public function sincronizarFacturacion(): void
    {
        try {
            $cliente = $this->ubicacion?->cliente;
            if (!$cliente && $this->cliente_ubicacion_id) {
                $ub = ClienteUbicacion::find($this->cliente_ubicacion_id);
                $cliente = $ub ? Cliente::find($ub->cliente_id) : null;
            }

            if (!$cliente) {
                return;
            }

            $nombre = trim($cliente->nombre ?: ($cliente->nombre_comercial ?: ('Cliente #' . $cliente->id)));

            $sub = SuscripcionGestionRemota::where('cliente_id', $cliente->id)
                ->orWhereRaw('LOWER(TRIM(cliente_nombre)) = ?', [strtolower($nombre)])
                ->first();

            $solucion = trim($this->gr_tipo_solucion ?: ($this->gr_marca_modelo ?: 'Router 4G LTE'));
            $sim = trim($this->gr_sim_numero ?: '');
            $tel = trim($cliente->telefono ?: ($cliente->contactos()->first()?->telefono ?? ''));
            $tarifa = max(0, (float) ($this->gr_recarga_monto ?? 0));

            if (!$sub) {
                $nextNum = ((int) (SuscripcionGestionRemota::max('id') ?? 0)) + 1;
                $codigo = "SUB-" . str_pad((string) $nextNum, 3, "0", STR_PAD_LEFT);
                while (SuscripcionGestionRemota::where('codigo', $codigo)->exists()) {
                    $nextNum++;
                    $codigo = "SUB-" . str_pad((string) $nextNum, 3, "0", STR_PAD_LEFT);
                }

                SuscripcionGestionRemota::create([
                    'codigo' => $codigo,
                    'cliente_id' => $cliente->id,
                    'cliente_nombre' => $nombre,
                    'cliente_telefono' => $tel,
                    'servicio_tipo' => 'Gestion_Remota',
                    'tipo_solucion' => $solucion,
                    'sim_numero' => $sim,
                    'moneda' => 'CUP',
                    'monto_mensual' => $tarifa,
                    'dia_pago_limite' => 15,
                    'mes_adelantado' => true,
                    'tiene_deuda' => false,
                    'meses_deuda_count' => 0,
                    'meses_deuda_detalle' => json_encode([]),
                    'deuda_acumulada' => 0.00,
                    'ultimo_mes_pagado' => null,
                    'estado_cobro' => 'pendiente',
                    'activo' => true,
                    'notas' => 'Sincronizado automáticamente al registrar Servicio de Gestión Remota.',
                ]);
            } else {
                $cambios = [];
                if (!$sub->cliente_id) $cambios['cliente_id'] = $cliente->id;
                if (empty($sub->sim_numero) && !empty($sim)) $cambios['sim_numero'] = $sim;
                if (empty($sub->tipo_solucion) && !empty($solucion)) $cambios['tipo_solucion'] = $solucion;
                if (empty($sub->cliente_telefono) && !empty($tel)) $cambios['cliente_telefono'] = $tel;
                if (!empty($cambios)) {
                    $sub->update($cambios);
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Error sincronizando servicio {$this->id} en facturación: " . $e->getMessage());
        }
    }
}
