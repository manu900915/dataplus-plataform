<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoProyecto extends Model
{
    use HasFactory;

    protected $table = 'tipos_proyecto';

    protected $fillable = ['nombre', 'descripcion', 'activo'];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function proyectos(): HasMany
    {
        return $this->hasMany(Proyecto::class, 'tipo_proyecto_id');
    }
}