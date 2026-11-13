<?php

use App\Services\SoftEtherService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('softether:sync-timed-users', function () {
    /** @var SoftEtherService $service */
    $service = app(SoftEtherService::class);
    $total = 0;

    foreach (['vps1', 'vps2'] as $srv) {
        $service->setServer($srv);
        if ($service->isConfigured()) {
            $total += $service->processExpiredTimedDisables($srv);
        }
    }

    $this->info("Usuarios re-habilitados: {$total}");
})->purpose('Re-habilita cuentas VPN cuya suspensión temporal haya expirado');

Schedule::command('softether:sync-timed-users')->everyMinute();

