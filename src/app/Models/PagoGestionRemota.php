<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PagoGestionRemota extends Model
{
    use HasFactory;

    protected $table = "pagos_gestion_remota";

    protected $fillable = [
        "codigo_pago",
        "suscripcion_id",
        "cliente_nombre",
        "mes_servicio",
        "fecha_pago",
        "moneda",
        "monto_esperado",
        "monto_pagado",
        "metodo_pago",
        "numero_transaccion",
        "comprobante_url",
        "comprobante_nombre",
        "efectivo_quien_recibe",
        "efectivo_quien_entrega",
        "registrado_por",
        "notas",
    ];

    protected $casts = [
        "fecha_pago" => "date",
        "monto_esperado" => "decimal:2",
        "monto_pagado" => "decimal:2",
    ];

    public function suscripcion(): BelongsTo
    {
        return $this->belongsTo(SuscripcionGestionRemota::class, "suscripcion_id");
    }
}
