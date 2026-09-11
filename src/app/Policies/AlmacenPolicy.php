<?php

namespace App\Policies;

use App\Models\Almacen;
use App\Models\User;

class AlmacenPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['Administrador', 'Almacenero']) || 
               $user->hasPermissionTo('ver_almacenes');
    }

    public function view(User $user, Almacen $almacen): bool
    {
        return $user->hasRole(['Administrador', 'Almacenero']) || 
               $user->hasPermissionTo('ver_almacenes');
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['Administrador', 'Almacenero']) || 
               $user->hasPermissionTo('crear_almacenes');
    }

    public function update(User $user, Almacen $almacen): bool
    {
        return $user->hasRole(['Administrador', 'Almacenero']) || 
               $user->hasPermissionTo('editar_almacenes');
    }

    public function delete(User $user, Almacen $almacen): bool
    {
        return $user->hasRole('Administrador') || 
               $user->hasPermissionTo('eliminar_almacenes');
    }
}