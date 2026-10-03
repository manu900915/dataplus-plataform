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
                'ldap.connections.default.port' => $ldapConfig->port,
                'ldap.connections.default.base_dn' => $ldapConfig->base_dn,
                'ldap.connections.default.username' => $ldapConfig->username,
                'ldap.connections.default.password' => $ldapConfig->password,
                'ldap.connections.default.use_ssl' => $ldapConfig->ssl,
                'ldap.connections.default.use_tls' => $ldapConfig->tls,
                'ldap.connections.default.timeout' => $ldapConfig->timeout,
            ]);
        }
    } catch (\Exception $e) {
        // Si la tabla no existe (primera vez), usar configuración del .env
        \Log::debug('Usando configuración LDAP del .env: ' . $e->getMessage());
    }
}
}
