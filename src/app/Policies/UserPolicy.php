<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determinar si el usuario puede ver la lista de usuarios.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Administrador');
    }

    /**
     * Determinar si el usuario puede ver un usuario específico.
     */
    public function view(User $user, User $model): bool
    {
        return $user->hasRole('Administrador');
    }

    /**
     * Determinar si el usuario puede crear nuevos usuarios.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('Administrador');
    }

    /**
     * Determinar si el usuario puede actualizar otro usuario.
     * 👇 IMPORTANTE: Se agrega el segundo parámetro $model (el usuario a editar)
     */
    public function update(User $user, User $model): bool
    {
        // Solo administradores pueden editar
        if (!$user->hasRole('Administrador')) {
            return false;
        }

        // Opcional: Evitar que un admin edite a otro admin (si quieres restringir esto)
        // if ($model->hasRole('Administrador') && $user->id !== $model->id) {
        //     return false; 
        // }

        return true;
    }

    /**
     * Determinar si el usuario puede eliminar otro usuario.
     * 👇 IMPORTANTE: Se agrega el segundo parámetro $model (el usuario a eliminar)
     */
    public function delete(User $user, User $model): bool
    {
        if (!$user->hasRole('Administrador')) {
            return false;
        }

        //  SEGURIDAD CRÍTICA: Un usuario NO puede eliminarse a sí mismo
        if ($user->id === $model->id) {
            return false;
        }

        // 🛑 SEGURIDAD CRÍTICA: Evitar eliminar al último Administrador del sistema
        // (Si el modelo a eliminar es admin y es el único admin, bloquear)
        if ($model->hasRole('Administrador')) {
            $adminCount = User::role('Administrador')->count();
            if ($adminCount <= 1) {
                return false;
            }
        }

        return true;
    }
}