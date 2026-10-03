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
use LdapRecord\Configuration\DomainConfiguration;

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
        // Permitir opciones legadas en LdapRecord v4 sin lanzar excepción
        if (class_exists(DomainConfiguration::class)) {
            DomainConfiguration::extend('ssl', false);
            DomainConfiguration::extend('use_ssl', false);
            DomainConfiguration::extend('tls', false);
        }
    }

    public function boot(): void
    {
        // 1. Registrar el driver de autenticación híbrido (LDAP + Local)
        Auth::provider('hybrid', function ($app, array $config) {
            return new HybridUserProvider($app->make('hash'), $config['model']);
        });

        // 2. Cargar configuración LDAP desde la BD si existe y está activa
        $useTls = false;
        try {
            $ldapConfig = \App\Models\LdapConfiguration::first();
            if ($ldapConfig && $ldapConfig->is_active) {
                $useTls = (bool) ($ldapConfig->use_tls ?? $ldapConfig->tls ?? $ldapConfig->use_ssl ?? $ldapConfig->ssl ?? false);
                config([
                    'ldap.connections.default.hosts' => [$ldapConfig->host],
                    'ldap.connections.default.port' => (int) $ldapConfig->port,
                    'ldap.connections.default.base_dn' => $ldapConfig->base_dn,
                    'ldap.connections.default.username' => $ldapConfig->username,
                    'ldap.connections.default.password' => $ldapConfig->password,
                    'ldap.connections.default.use_tls' => $useTls,
                    'ldap.connections.default.timeout' => (int) ($ldapConfig->timeout ?? 5),
                ]);
            }
        } catch (\Throwable $e) {
            // Si la tabla no existe o falla la BD, continuar con la del .env
        }

        // Limpiar claves legadas de la configuración de conexión
        $defaultConn = config('ldap.connections.default', []);
        if (is_array($defaultConn)) {
            unset($defaultConn['ssl'], $defaultConn['use_ssl'], $defaultConn['tls']);
            $defaultConn['use_tls'] = $useTls ?: (bool) ($defaultConn['use_tls'] ?? false);
            config(['ldap.connections.default' => $defaultConn]);
        }
    }
}
