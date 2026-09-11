<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo', 'tipo_persona', 'documento', 'nombre', 'nombre_comercial',
        'email', 'telefono', 'direccion', 'municipio', 'provincia', 'notas', 'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function ubicaciones(): HasMany
    {
        return $this->hasMany(ClienteUbicacion::class);
    }

    public function contactos(): HasMany
    {
        return $this->hasMany(ContactoCliente::class);
    }

    public function servicios(): HasManyThrough
    {
        return $this->hasManyThrough(
            Servicio::class,
            ClienteUbicacion::class,
            'cliente_id',
            'cliente_ubicacion_id',
            'id',
            'id'
        );
    }
}