<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClienteUbicacion extends Model
{
    use HasFactory;

    protected $table = 'cliente_ubicaciones';

    protected $fillable = [
        'cliente_id', 'nombre', 'tipo', 'tipo_negocio_id',
        'direccion', 'provincia', 'municipio',
        'contacto_nombre', 'contacto_telefono', 'notas', 'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function tipoNegocio(): BelongsTo
    {
        return $this->belongsTo(TipoNegocio::class);
    }

    public function servicios(): HasMany
    {
        return $this->hasMany(Servicio::class, 'cliente_ubicacion_id');
    }
}