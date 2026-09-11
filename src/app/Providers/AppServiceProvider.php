<?php

namespace App\Providers;

use App\Models\Almacen;
use App\Models\CategoriaItem;
use App\Models\InventarioMovimiento;
use App\Models\Item;
use App\Policies\AlmacenPolicy;
use App\Policies\CategoriaItemPolicy;
use App\Policies\InventarioMovimientoPolicy;
use App\Policies\ItemPolicy;
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
        //
    }
}