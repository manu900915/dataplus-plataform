<?php

namespace App\Providers;

use App\Auth\HybridUserProvider;
use App\Models\Almacen;
use App\Models\CategoriaItem;
use App\Models\InventarioMovimiento;
use App\Models\Item;
use App\Models\LdapConfiguration;
use App\Policies\AlmacenPolicy;
use App\Policies\CategoriaItemPolicy;
use App\Policies\InventarioMovimientoPolicy;
use App\Policies\ItemPolicy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        Item::class => ItemPolicy::class,
        CategoriaItem::class => CategoriaItemPolicy::class,
        Almacen::class => AlmacenPolicy::class,
        InventarioMovimiento::class => InventarioMovimientoPolicy::class,
    ];

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // 1. Registrar el driver de autenticación híbrido (LDAP + Local)
        Auth::provider('hybrid', function ($app, array $config) {
            return new HybridUserProvider($app->make('hash'), $config['model']);
        });

        // 2. Cargar configuración LDAP desde la BD si existe y está activa
        try {
            $ldapConfig = \App\Models\LdapConfiguration::first();
            if ($ldapConfig && $ldapConfig->is_active) {
                config([
                    'ldap.connections.default.hosts' => [$ldapConfig->host],
                    'ldap.connections.default.port' => (int) $ldapConfig->port,
                    'ldap.connections.default.base_dn' => $ldapConfig->base_dn,
                    'ldap.connections.default.username' => $ldapConfig->username,
                    'ldap.connections.default.password' => $ldapConfig->password,
                    'ldap.connections.default.use_ssl' => (bool) $ldapConfig->ssl,
                    'ldap.connections.default.use_tls' => (bool) $ldapConfig->tls,
                    'ldap.connections.default.timeout' => (int) ($ldapConfig->timeout ?? 5),
                ]);
            }
        } catch (\Throwable $e) {
            // Si la tabla no existe o falla la BD, continuar con la del .env
        }

        // Blindaje contra 'Option ssl does not exist' en LdapRecord
        $defaultConn = config('ldap.connections.default', []);
        if (is_array($defaultConn)) {
            unset($defaultConn['ssl'], $defaultConn['tls']);
            config(['ldap.connections.default' => $defaultConn]);
        }
    }
}
