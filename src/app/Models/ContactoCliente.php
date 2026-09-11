<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactoCliente extends Model
{
    use HasFactory;

    protected $table = 'contacto_clientes';

    protected $fillable = [
        'cliente_id',
        'nombre',
        'cargo',
        'responsabilidad',
        'country_code',
        'telefono',
        'email',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }
}