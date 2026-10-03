<?php

namespace App\Policies;

use App\Models\CategoriaItem;
use App\Models\User;

class CategoriaItemPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['Administrador', 'Almacenero']) || 
               $user->hasPermissionTo('ver_categorias');
    }

    public function view(User $user, CategoriaItem $categoria): bool
    {
        return $user->hasRole(['Administrador', 'Almacenero']) || 
               $user->hasPermissionTo('ver_categorias');
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['Administrador', 'Almacenero']) || 
               $user->hasPermissionTo('crear_categorias');
    }

    public function update(User $user, CategoriaItem $categoria): bool
    {
        return $user->hasRole(['Administrador', 'Almacenero']) || 
               $user->hasPermissionTo('editar_categorias');
    }

    public function delete(User $user, CategoriaItem $categoria): bool
    {
        return $user->hasRole('Administrador') || 
               $user->hasPermissionTo('eliminar_categorias');
    }
}