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
    public string $selectedServer = 'vps1'; // vps1 o vps2

    public array $serverStatus = [];
    public array $activeSessions = [];
    public array $vpnUsers = [];
    public array $virtualHubs = [];
    public array $secureNat = [];
    public array $dhcpTable = [];
    public array $connectionDetails = [];

    public ?array $testResult = null;
    public ?array $inspectSession = null;

    // Campos modal nuevo usuario
    public bool $isCreatingUser = false;
    public string $newUsername = '';
    public string $newRealname = '';
    public string $newPassword = '';
    public string $newNote = '';

    public function mount(): void
    {
        $this->loadData();
    }

    public function selectServer(string $serverId): void
    {
        $this->selectedServer = $serverId;
        $this->testResult = null;
        $this->inspectSession = null;
        $this->loadData(false, false); // Carga instantánea desde caché (0ms)

        Notification::make()
            ->title('Servidor seleccionado')
            ->body("Conmutado al servidor: {$this->connectionDetails['server_name']}")
            ->info()
            ->send();
    }

    public function loadData(bool $notify = false, bool $force = false): void
    {
        /** @var SoftEtherService $service */
        $service = app(SoftEtherService::class);
        $service->setServer($this->selectedServer);

        $telemetry = $service->getTelemetry($force);

        $this->serverStatus = $telemetry['serverStatus'] ?? [];
        $this->activeSessions = $telemetry['activeSessions'] ?? [];
        $this->vpnUsers = $telemetry['vpnUsers'] ?? [];
        $this->virtualHubs = $telemetry['virtualHubs'] ?? [];
        $this->secureNat = $telemetry['secureNat'] ?? [];
        $this->dhcpTable = $telemetry['dhcpTable'] ?? [];
        $this->connectionDetails = $telemetry['connectionDetails'] ?? [];

        if ($notify) {
            Notification::make()
                ->title('Datos de SoftEther actualizados')
                ->body("Sincronizado con {$this->connectionDetails['server_name']}.")
                ->success()
                ->send();
        }
    }

    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function testConnection(): void
    {
        /** @var SoftEtherService $service */
        $service = app(SoftEtherService::class);
        $service->setServer($this->selectedServer);

        $this->testResult = $service->testConnection();
        $this->loadData(false, true); // Forzar sincronización en vivo tras el test

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

    public function inspeccionarSesion(string $sessionName): void
    {
        /** @var SoftEtherService $service */
        $service = app(SoftEtherService::class);
        $service->setServer($this->selectedServer);

        $this->inspectSession = $service->getSessionStatus($sessionName);

        if (empty($this->inspectSession)) {
            Notification::make()
                ->title('Información no disponible')
                ->body("No se pudo obtener el estado detallado de la sesión {$sessionName}.")
                ->warning()
                ->send();
        }
    }

    public function cerrarModalInspeccion(): void
    {
        $this->inspectSession = null;
    }

    public function desconectarSesion(string $sessionName): void
    {
        /** @var SoftEtherService $service */
        $service = app(SoftEtherService::class);
        $service->setServer($this->selectedServer);
        $res = $service->disconnectSession($sessionName);

        if ($res['success']) {
            Notification::make()
                ->title('Sesión desconectada')
                ->body($res['message'])
                ->success()
                ->send();

            $this->activeSessions = array_values(array_filter($this->activeSessions, fn ($s) => ($s['session_name'] ?? '') !== $sessionName));
        } else {
            Notification::make()
                ->title('Error al desconectar sesión')
                ->body($res['message'])
                ->danger()
                ->send();
        }
    }

    public function crearUsuario(): void
    {
        if (empty(trim($this->newUsername)) || empty(trim($this->newPassword))) {
            Notification::make()
                ->title('Campos requeridos')
                ->body('Debe especificar usuario y contraseña.')
                ->warning()
                ->send();
            return;
        }

        /** @var SoftEtherService $service */
        $service = app(SoftEtherService::class);
        $service->setServer($this->selectedServer);

        $res = $service->createUser([
            'username' => trim($this->newUsername),
            'real_name' => trim($this->newRealname) ?: trim($this->newUsername),
            'password' => trim($this->newPassword),
            'note' => trim($this->newNote) ?: 'Cliente VPN DataPlus',
        ]);

        if ($res['success']) {
            Notification::make()
                ->title('Usuario VPN Creado')
                ->body($res['message'])
                ->success()
                ->send();

            $this->isCreatingUser = false;
            $this->newUsername = '';
            $this->newRealname = '';
            $this->newPassword = '';
            $this->newNote = '';
            $this->loadData();
        } else {
            Notification::make()
                ->title('Error al crear usuario')
                ->body($res['message'])
                ->danger()
                ->send();
        }
    }

    public function eliminarUsuario(string $username): void
    {
        /** @var SoftEtherService $service */
        $service = app(SoftEtherService::class);
        $service->setServer($this->selectedServer);
        $res = $service->deleteUser($username);

        if ($res['success']) {
            Notification::make()
                ->title('Usuario eliminado')
                ->body($res['message'])
                ->success()
                ->send();

            $this->vpnUsers = array_values(array_filter($this->vpnUsers, fn ($u) => ($u['username'] ?? '') !== $username));
        } else {
            Notification::make()
                ->title('Error al eliminar usuario')
                ->body($res['message'])
                ->danger()
                ->send();
        }
    }

    public function toggleUsuario(string $username, bool $nuevoEstado): void
    {
        /** @var SoftEtherService $service */
        $service = app(SoftEtherService::class);
        $service->setServer($this->selectedServer);
        $res = $service->toggleUser($username, $nuevoEstado);

        if ($res['success']) {
            Notification::make()
                ->title('Estado de usuario modificado')
                ->body($res['message'])
                ->success()
                ->send();

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

    public function guardarEnDisco(): void
    {
        /** @var SoftEtherService $service */
        $service = app(SoftEtherService::class);
        $service->setServer($this->selectedServer);
        $res = $service->flushConfig();

        if ($res['success']) {
            Notification::make()
                ->title('Configuración guardada (Flush)')
                ->body($res['message'])
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Error al guardar configuración')
                ->body($res['message'])
                ->danger()
                ->send();
        }
    }

    public function reiniciarServicio(): void
    {
        /** @var SoftEtherService $service */
        $service = app(SoftEtherService::class);
        $service->setServer($this->selectedServer);
        $res = $service->rebootServer();

        if ($res['success']) {
            Notification::make()
                ->title('Servicio Reiniciado')
                ->body($res['message'])
                ->warning()
                ->send();
        } else {
            Notification::make()
                ->title('Error al reiniciar')
                ->body($res['message'])
                ->danger()
                ->send();
        }
    }
}
