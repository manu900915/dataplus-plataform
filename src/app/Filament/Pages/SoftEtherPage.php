<?php

namespace App\Filament\Pages;

use App\Services\SoftEtherService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SoftEtherPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'Operaciones';

    protected static ?string $navigationLabel = 'SoftEther VPN';

    protected static ?int $navigationSort = 3;

    protected static ?string $title = 'Gestión & Monitoreo SoftEther VPN';

    protected static string $view = 'filament.pages.softether-page';

    public string $activeTab = 'resumen'; // resumen, sesiones, usuarios, hubs, config

    public array $serverStatus = [];
    public array $activeSessions = [];
    public array $vpnUsers = [];
    public array $virtualHubs = [];
    public array $secureNat = [];
    public array $connectionDetails = [];

    public ?array $testResult = null;
    public bool $isTesting = false;

    public function mount(): void
    {
        $this->loadData();
    }

    public function loadData(): void
    {
        /** @var SoftEtherService $service */
        $service = app(SoftEtherService::class);

        $this->serverStatus = $service->getServerStatus();
        $this->activeSessions = $service->getActiveSessions();
        $this->vpnUsers = $service->getUsers();
        $this->virtualHubs = $service->getVirtualHubs();
        $this->secureNat = $service->getSecureNatStatus();
        $this->connectionDetails = $service->getConnectionDetails();
    }

    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function testConnection(): void
    {
        /** @var SoftEtherService $service */
        $service = app(SoftEtherService::class);

        $this->testResult = $service->testConnection();

        if ($this->testResult['success']) {
            Notification::make()
                ->title('Conexión con SoftEther exitosa')
                ->body($this->testResult['message'])
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Estado de conexión SoftEther')
                ->body($this->testResult['message'])
                ->warning()
                ->send();
        }
    }

    public function desconectarSesion(string $sessionName): void
    {
        /** @var SoftEtherService $service */
        $service = app(SoftEtherService::class);
        $res = $service->disconnectSession($sessionName);

        if ($res['success']) {
            Notification::make()
                ->title('Sesión desconectada')
                ->body($res['message'])
                ->success()
                ->send();

            // Filtrar localmente la sesión desconectada
            $this->activeSessions = array_values(array_filter($this->activeSessions, fn ($s) => ($s['session_name'] ?? '') !== $sessionName));
        } else {
            Notification::make()
                ->title('Error al desconectar sesión')
                ->body($res['message'])
                ->danger()
                ->send();
        }
    }

    public function toggleUsuario(string $username, bool $nuevoEstado): void
    {
        /** @var SoftEtherService $service */
        $service = app(SoftEtherService::class);
        $res = $service->toggleUser($username, $nuevoEstado);

        if ($res['success']) {
            Notification::make()
                ->title('Estado de usuario modificado')
                ->body($res['message'])
                ->success()
                ->send();

            // Actualizar localmente el usuario
            foreach ($this->vpnUsers as &$u) {
                if (($u['username'] ?? '') === $username) {
                    $u['is_active'] = $nuevoEstado;
                    $u['estado'] = $nuevoEstado ? 'Habilitado' : 'Deshabilitado';
                }
            }
        } else {
            Notification::make()
                ->title('Error al cambiar usuario')
                ->body($res['message'])
                ->danger()
                ->send();
        }
    }
}
