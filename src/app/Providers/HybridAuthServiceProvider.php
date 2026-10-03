<?php

namespace App\Providers;

use App\Auth\HybridUserProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class HybridAuthServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Auth::provider('hybrid', function ($app, array $config) {
            return new HybridUserProvider($app['hash']);
        });
    }
}