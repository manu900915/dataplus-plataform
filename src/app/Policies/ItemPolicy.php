<?php

namespace App\Policies;

use App\Models\Item;
use App\Models\User;

class ItemPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['Administrador', 'Almacenero']) || 
               $user->hasPermissionTo('ver_items');
    }

    public function view(User $user, Item $item): bool
    {
        return $user->hasRole(['Administrador', 'Almacenero']) || 
               $user->hasPermissionTo('ver_items');
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['Administrador', 'Almacenero']) || 
               $user->hasPermissionTo('crear_items');
    }

    public function update(User $user, Item $item): bool
    {
        return $user->hasRole(['Administrador', 'Almacenero']) || 
               $user->hasPermissionTo('editar_items');
    }

    public function delete(User $user, Item $item): bool
    {
        return $user->hasRole('Administrador') || 
               $user->hasPermissionTo('eliminar_items');
    }
}