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
        "servicio_id",
        "cliente_nombre",
        "cliente_telefono",
        "servicio_tipo",
        "tipo_solucion",
        "sim_numero",
        "moneda",
        "monto_mensual",
        "periodicidad_pago",
        "meses_periodo",
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
        "servicio_id" => "integer",
        "monto_mensual" => "decimal:2",
        "meses_periodo" => "integer",
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

    public function servicio(): BelongsTo
    {
        return $this->belongsTo(Servicio::class, "servicio_id");
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(PagoGestionRemota::class, "suscripcion_id");
    }

    public function getPeriodicidadEfectivaAttribute(): string
    {
        $p = strtolower((string) ($this->periodicidad_pago ?? 'mensual'));
        return in_array($p, ['mensual', 'trimestral', 'personalizado'], true) ? $p : 'mensual';
    }

    public function getMesesFacturacionAttribute(): int
    {
        return match ($this->periodicidad_efectiva) {
            'trimestral' => 3,
            'personalizado' => max(1, (int) ($this->meses_periodo ?: 1)),
            default => 1,
        };
    }

    public function getPeriodicidadLabelAttribute(): string
    {
        $meses = $this->meses_facturacion;
        return match ($this->periodicidad_efectiva) {
            'trimestral' => 'Trimestral (3 meses)',
            'personalizado' => 'Personalizado (' . $meses . ($meses === 1 ? ' mes)' : ' meses)'),
            default => 'Mensual (1 mes)',
        };
    }

    public function getMontoPeriodoAttribute(): float
    {
        return round((float) ($this->monto_mensual ?? 0) * $this->meses_facturacion, 2);
    }

    public function getTotalCobrarAttribute(): float
    {
        return $this->monto_periodo + (float) ($this->deuda_acumulada ?? 0);
    }

    public function getTarifaMensualAttribute(): float
    {
        return (float) ($this->monto_mensual ?? 0);
    }

    public function getMontoACobrarAttribute(): float
    {
        return $this->monto_periodo + (float) ($this->deuda_acumulada ?? 0);
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
