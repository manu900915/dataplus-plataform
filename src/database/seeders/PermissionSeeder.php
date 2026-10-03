<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Resetear caché de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ============================================
        // PERMISOS DE ALMACENES
        // ============================================
        Permission::firstOrCreate(['name' => 'ver_almacenes', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'crear_almacenes', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'editar_almacenes', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'eliminar_almacenes', 'guard_name' => 'web']);

        // ============================================
        // PERMISOS DE ITEMS
        // ============================================
        Permission::firstOrCreate(['name' => 'ver_items', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'crear_items', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'editar_items', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'eliminar_items', 'guard_name' => 'web']);

        // ============================================
        // PERMISOS DE INVENTARIO
        // ============================================
        Permission::firstOrCreate(['name' => 'ver_inventario', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'crear_movimientos', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'editar_movimientos', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'eliminar_movimientos', 'guard_name' => 'web']);

        // ============================================
        // PERMISOS DE USUARIOS
        // ============================================
        Permission::firstOrCreate(['name' => 'ver_usuarios', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'crear_usuarios', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'editar_usuarios', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'eliminar_usuarios', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'asignar_roles', 'guard_name' => 'web']);

        // ============================================
        // PERMISOS DE PROYECTOS
        // ============================================
        Permission::firstOrCreate(['name' => 'ver_proyectos', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'crear_proyectos', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'editar_proyectos', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'eliminar_proyectos', 'guard_name' => 'web']);

        // ============================================
        // PERMISOS DE FINANZAS
        // ============================================
        Permission::firstOrCreate(['name' => 'ver_ventas', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'crear_ventas', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'ver_gastos', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'crear_gastos', 'guard_name' => 'web']);

        // ============================================
        // PERMISOS DE REPORTES
        // ============================================
        Permission::firstOrCreate(['name' => 'ver_reportes', 'guard_name' => 'web']);

        // ============================================
        // PERMISOS DE OPERACIONES
        // ============================================
        Permission::firstOrCreate(['name' => 'ver_incidencias', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'crear_incidencias', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'ver_contratos', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'ver_servicios', 'guard_name' => 'web']);

        // ============================================
        // PERMISOS DE ADMINISTRACION
        // ============================================
        Permission::firstOrCreate(['name' => 'administrar', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'ver_configuracion', 'guard_name' => 'web']);

        // ============================================
        // CREAR ROLES
        // ============================================
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $viewerRole = Role::firstOrCreate(['name' => 'viewer', 'guard_name' => 'web']);

        // Admin tiene todos los permisos
        $adminRole->givePermissionTo(Permission::all());

        // User tiene permisos básicos de operación
        $userRole->givePermissionTo([
            'ver_almacenes',
            'ver_items',
            'ver_inventario',
            'crear_movimientos',
            'ver_proyectos',
            'ver_ventas',
            'ver_gastos',
            'ver_incidencias',
            'crear_incidencias',
            'ver_reportes',
        ]);

        // Viewer solo puede ver
        $viewerRole->givePermissionTo([
            'ver_almacenes',
            'ver_items',
            'ver_inventario',
            'ver_proyectos',
            'ver_ventas',
            'ver_gastos',
            'ver_reportes',
        ]);

        $this->command->info('✅ Permisos y roles creados exitosamente');
        $this->command->info('Permisos totales: ' . Permission::count());
        $this->command->info('Roles totales: ' . Role::count());
    }
}
