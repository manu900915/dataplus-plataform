<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoriaItem extends Model
{
    protected $table = 'categorias_item';

    protected $fillable = ['nombre', 'tipo', 'activo'];

    protected $casts = ['activo' => 'boolean'];

    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'categoria_id');
    }
}