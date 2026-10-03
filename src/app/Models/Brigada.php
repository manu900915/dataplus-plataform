<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Brigada extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'jefe_id', 'notas', 'activa'];

    protected $casts = [
        'activa' => 'boolean',
    ];

    public function jefe(): BelongsTo
    {
        return $this->belongsTo(User::class, 'jefe_id');
    }

    public function tecnicos(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'brigada_tecnico', 'brigada_id', 'tecnico_id')
            ->withTimestamps();
    }

    public function integrantesCount(): int
    {
        return $this->tecnicos()->count();
    }
}