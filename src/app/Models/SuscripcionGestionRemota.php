<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SuscripcionGestionRemota extends Model
{
    use HasFactory;

    protected $table = "suscripciones_gestion_remota";

    protected $fillable = [
        "codigo",
        "cliente_id",
        "cliente_nombre",
        "cliente_telefono",
        "servicio_tipo",
        "tipo_solucion",
        "sim_numero",
        "moneda",
        "monto_mensual",
        "dia_pago_limite",
        "mes_adelantado",
        "tiene_deuda",
        "meses_deuda_count",
        "meses_deuda_detalle",
        "deuda_acumulada",
        "ultimo_mes_pagado",
        "estado_cobro",
        "activo",
        "notas",
    ];

    protected $casts = [
        "monto_mensual" => "decimal:2",
        "deuda_acumulada" => "decimal:2",
        "mes_adelantado" => "boolean",
        "tiene_deuda" => "boolean",
        "meses_deuda_count" => "integer",
        "dia_pago_limite" => "integer",
        "activo" => "boolean",
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, "cliente_id");
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(PagoGestionRemota::class, "suscripcion_id");
    }

    public function getTotalCobrarAttribute(): float
    {
        return (float) ($this->monto_mensual ?? 0) + (float) ($this->deuda_acumulada ?? 0);
    }

    public function getTarifaMensualAttribute(): float
    {
        return (float) ($this->monto_mensual ?? 0);
    }

    public function getMontoACobrarAttribute(): float
    {
        return (float) ($this->monto_mensual ?? 0) + (float) ($this->deuda_acumulada ?? 0);
    }

    public function getEstadoCobroMesActualAttribute(): string
    {
        if ($this->tiene_deuda || (float) $this->deuda_acumulada > 0 || (int) $this->meses_deuda_count > 0) {
            return 'En_Mora';
        }
        if ($this->estado_cobro === 'cobrado' || $this->estado_cobro === 'al_dia') {
            return 'Al_Dia';
        }
        return 'Pendiente';
    }
}
