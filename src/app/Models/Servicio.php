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
        'gr_ultima_recarga',
        'gr_mensualidad_monto',
        'gr_mensualidad_moneda',
        'gr_periodicidad_pago',
        'gr_meses_pago',
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
        'gr_mensualidad_monto'       => 'decimal:2',
        'gr_meses_pago'              => 'integer',
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

    public function getGrPeriodicidadNormalizada(): string
    {
        $p = strtolower((string) ($this->gr_periodicidad_pago ?: 'mensual'));
        return in_array($p, ['mensual', 'trimestral', 'personalizado'], true) ? $p : 'mensual';
    }

    public function getGrMesesFacturacion(): int
    {
        return match ($this->getGrPeriodicidadNormalizada()) {
            'trimestral' => 3,
            'personalizado' => max(1, (int) ($this->gr_meses_pago ?: 1)),
            default => 1,
        };
    }

    public function getGrMontoPeriodo(): float
    {
        return round(max(0, (float) ($this->gr_mensualidad_monto ?? 0)) * $this->getGrMesesFacturacion(), 2);
    }

    protected static function booted(): void
    {
        static::saving(function (Servicio $servicio) {
            if ($servicio->tipo === 'Gestion_Remota') {
                $periodicidad = $servicio->getGrPeriodicidadNormalizada();
                $servicio->gr_periodicidad_pago = $periodicidad;
                $servicio->gr_meses_pago = match ($periodicidad) {
                    'trimestral' => 3,
                    'personalizado' => max(1, (int) ($servicio->gr_meses_pago ?: 1)),
                    default => 1,
                };
                $moneda = strtoupper((string) ($servicio->gr_mensualidad_moneda ?: 'CUP'));
                $servicio->gr_mensualidad_moneda = in_array($moneda, ['CUP', 'USD'], true) ? $moneda : 'CUP';
                if ($servicio->gr_mensualidad_monto === null) {
                    $servicio->gr_mensualidad_monto = 0.00;
                }
            }
        });

        static::saved(function (Servicio $servicio) {
            if ($servicio->tipo === 'Gestion_Remota') {
                $servicio->sincronizarFacturacion();
            }
        });
    }

    /**
     * Sincronizar automáticamente este servicio de Gestión Remota en la tabla de facturación
     */
    public function sincronizarFacturacion(bool $forceUpdateTarifa = false): void
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

            // 1. Buscar primero suscripción vinculada directamente a este servicio
            $sub = SuscripcionGestionRemota::where('servicio_id', $this->id)->first();

            // 2. Si no existe, buscar suscripción legado del mismo cliente sin servicio asignado
            if (!$sub) {
                $sub = SuscripcionGestionRemota::whereNull('servicio_id')
                    ->where(function ($q) use ($cliente, $nombre) {
                        $q->where('cliente_id', $cliente->id)
                          ->orWhereRaw('LOWER(TRIM(cliente_nombre)) = ?', [strtolower($nombre)]);
                    })
                    ->first();
            }

            $solucion = trim($this->gr_tipo_solucion ?: ($this->gr_marca_modelo ?: 'Router 4G LTE'));
            $sim = trim($this->gr_sim_numero ?: '');
            $tel = trim($cliente->telefono ?: ($cliente->contactos()->first()?->telefono ?? ''));
            $moneda = in_array(strtoupper((string) ($this->gr_mensualidad_moneda ?: 'CUP')), ['CUP', 'USD'], true)
                ? strtoupper((string) $this->gr_mensualidad_moneda)
                : 'CUP';
            $tarifa = max(0, (float) ($this->gr_mensualidad_monto ?? 0));
            $periodicidad = $this->getGrPeriodicidadNormalizada();
            $mesesPeriodo = $this->getGrMesesFacturacion();
            $activo = $this->estado !== 'Inactivo';

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
                    'servicio_id' => $this->id,
                    'cliente_nombre' => $nombre,
                    'cliente_telefono' => $tel,
                    'servicio_tipo' => 'Gestion_Remota',
                    'tipo_solucion' => $solucion,
                    'sim_numero' => $sim,
                    'moneda' => $moneda,
                    'monto_mensual' => $tarifa,
                    'periodicidad_pago' => $periodicidad,
                    'meses_periodo' => $mesesPeriodo,
                    'dia_pago_limite' => 15,
                    'mes_adelantado' => true,
                    'tiene_deuda' => false,
                    'meses_deuda_count' => 0,
                    'meses_deuda_detalle' => json_encode([]),
                    'deuda_acumulada' => 0.00,
                    'ultimo_mes_pagado' => null,
                    'estado_cobro' => 'pendiente',
                    'activo' => $activo,
                    'notas' => 'Sincronizado automáticamente desde Servicio de Gestión Remota.',
                ]);
            } else {
                $cambios = [];
                if (!$sub->servicio_id) {
                    $cambios['servicio_id'] = $this->id;
                }
                if (!$sub->cliente_id) {
                    $cambios['cliente_id'] = $cliente->id;
                }
                if (!empty($nombre) && $sub->cliente_nombre !== $nombre) {
                    $cambios['cliente_nombre'] = $nombre;
                }
                if (!empty($tel) && (empty($sub->cliente_telefono) || $this->wasChanged('cliente_ubicacion_id'))) {
                    $cambios['cliente_telefono'] = $tel;
                }
                if (!empty($sim) && ($sub->sim_numero !== $sim)) {
                    $cambios['sim_numero'] = $sim;
                }
                if (!empty($solucion) && ($sub->tipo_solucion !== $solucion)) {
                    $cambios['tipo_solucion'] = $solucion;
                }

                if ($forceUpdateTarifa || $this->wasRecentlyCreated || $this->wasChanged('gr_mensualidad_monto') || $tarifa > 0) {
                    $cambios['monto_mensual'] = $tarifa;
                }
                if ($forceUpdateTarifa || $this->wasRecentlyCreated || $this->wasChanged('gr_mensualidad_moneda') || $tarifa > 0) {
                    $cambios['moneda'] = $moneda;
                }
                if ($forceUpdateTarifa || $this->wasRecentlyCreated || $this->wasChanged(['gr_periodicidad_pago', 'gr_meses_pago']) || $periodicidad !== 'mensual') {
                    $cambios['periodicidad_pago'] = $periodicidad;
                    $cambios['meses_periodo'] = $mesesPeriodo;
                }
                if ($this->wasChanged('estado')) {
                    $cambios['activo'] = $activo;
                }

                if (!empty($cambios)) {
                    $sub->update($cambios);
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Error sincronizando servicio {$this->id} en facturación: " . $e->getMessage());
        }
    }
}
