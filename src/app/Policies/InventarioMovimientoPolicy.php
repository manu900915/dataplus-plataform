<?php

namespace App\Policies;

use App\Models\InventarioMovimiento;
use App\Models\User;

class InventarioMovimientoPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['Administrador', 'Almacenero']) || 
               $user->hasPermissionTo('ver_movimientos');
    }

    public function view(User $user, InventarioMovimiento $movimiento): bool
    {
        return $user->hasRole(['Administrador', 'Almacenero']) || 
               $user->hasPermissionTo('ver_movimientos');
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['Administrador', 'Almacenero']) || 
               $user->hasPermissionTo('crear_movimientos');
    }

    public function update(User $user, InventarioMovimiento $movimiento): bool
    {
        return $user->hasRole('Administrador');
    }

    public function delete(User $user, InventarioMovimiento $movimiento): bool
    {
        return $user->hasRole('Administrador');
    }
}