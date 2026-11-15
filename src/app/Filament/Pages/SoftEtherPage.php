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
    public string $selectedHub = ''; // Hub seleccionado dinámicamente

    public array $serverStatus = [];
    public array $activeSessions = [];
    public array $vpnUsers = [];
    public array $allVpnUsers = [];
    public array $virtualHubs = [];
    public array $secureNat = [];
    public array $dhcpTable = [];
    public array $allHubRoutes = [];
    public array $l3Switches = [];
    public array $l3Interfaces = [];
    public array $l3Tables = [];
    public array $connectionDetails = [];

    public ?array $testResult = null;
    public ?array $inspectSession = null;

    // Campos modal nuevo usuario
    public bool $isCreatingUser = false;
    public string $newUserHub = '';
    public string $newUsername = '';
    public string $newRealname = '';
    public string $newPassword = '';
    public string $newNote = '';

    // Modal deshabilitar usuario por tiempo definido
    public ?array $userToTimedDisable = null;
    public string $disableDurationPreset = '60';
    public string $disableUntilCustom = '';
    public string $disableReason = '';

    // Campos modal nuevo Virtual Hub
    public bool $isCreatingHub = false;
    public string $newHubName = '';
    public string $newHubPassword = '';

    // Hub a eliminar (confirmación)
    public ?string $hubToDelete = null;

    // Búsqueda y paginación de Virtual Hubs
    public string $hubSearch = '';
    public int $hubPage = 1;
    public int $hubsPerPage = 6;

    // Búsqueda, filtros y paginación de Cuentas VPN
    public string $userSearch = '';
    public string $userHubFilter = '';
    public string $userStatusFilter = 'todos';
    public int $userPage = 1;
    public int $usersPerPage = 15;

    // Modal de Gestión Completa por Virtual Hub (DHCP, SecureNAT, Rutas, Usuarios, Logs)
    public ?string $managingHub = null;
    public string $hubManageTab = 'nat_dhcp'; // nat_dhcp, routes, users, logs
    public array $hubManageSecureNat = [];
    public array $hubManageDhcpTable = [];
    public array $hubManageUsers = [];
    public array $hubManageLog = [];
    public array $natForm = [
        'securenat_enabled' => false,
        'use_nat' => true,
        'use_dhcp' => true,
        'virtual_gateway_ip' => '192.168.30.1',
        'virtual_subnet_mask' => '255.255.255.0',
        'dhcp_gateway_ip' => '192.168.30.1',
        'dhcp_lease_start' => '192.168.30.10',
        'dhcp_lease_end' => '192.168.30.200',
        'dhcp_lease_expire_seconds' => 7200,
        'dns_server_1' => '8.8.8.8',
        'dns_server_2' => '1.1.1.1',
        'domain_name' => '',
        'mtu' => 1500,
        'nat_tcp_timeout' => 1800,
        'nat_udp_timeout' => 60,
        'save_log' => true,
    ];
    public bool $hubApplyPushRoutes = true;
    public array $hubRoutesList = [];
    public string $newHubRouteNetwork = '';
    public string $newHubRouteMask = '255.255.255.0';
    public string $newHubRouteGateway = '';
    public array $logForm = [
        'save_security_log' => true,
        'security_log_switch_type' => 4,
        'save_packet_log' => false,
        'packet_log_switch_type' => 4,
    ];
    public string $hubNewUsername = '';
    public string $hubNewRealname = '';
    public string $hubNewPassword = '';
    public string $hubNewNote = '';

    // Pestaña Interfaces L3 & Rutas
    public string $l3Search = '';
    public string $l3SwitchFilter = '';
    public string $routeHubSearch = '';

    // Modal Crear Switch L3
    public bool $isCreatingL3Switch = false;
    public string $newL3SwitchName = '';

    // Modal Crear/Editar Interfaz L3
    public bool $isEditingL3If = false;
    public ?array $editingL3IfOriginal = null;
    public string $l3IfFormSwitch = '';
    public string $l3IfFormHub = '';
    public string $l3IfFormIp = '';
    public string $l3IfFormMask = '255.255.255.0';

    // Modal Crear/Editar Ruta L3
    public bool $isEditingL3Route = false;
    public ?array $editingL3RouteOriginal = null;
    public string $l3RouteFormSwitch = '';
    public string $l3RouteFormNetwork = '';
    public string $l3RouteFormMask = '255.255.255.0';
    public string $l3RouteFormGateway = '';
    public int $l3RouteFormMetric = 1;

    // Memoización por petición HTTP para evitar re-filtrar listas 3 veces en el mismo render de Blade
    protected ?array $memoFilteredHubs = null;
    protected ?array $memoFilteredUsers = null;
    protected ?array $memoFilteredL3Ifs = null;
    protected ?array $memoFilteredL3Tbls = null;
    protected ?array $memoFilteredAllHubRoutes = null;

    protected function resetFilterMemos(): void
    {
        $this->memoFilteredHubs = null;
        $this->memoFilteredUsers = null;
        $this->memoFilteredL3Ifs = null;
        $this->memoFilteredL3Tbls = null;
        $this->memoFilteredAllHubRoutes = null;
    }

    public function mount(): void
    {
        $this->loadData();
    }

    public function selectServer(string $serverId): void
    {
        $this->selectedServer = $serverId;
        $this->selectedHub = '';
        $this->testResult = null;
        $this->inspectSession = null;
        $this->managingHub = null;
        $this->userHubFilter = '';
        $this->userPage = 1;
        $this->hubPage = 1;
        $this->resetFilterMemos();
        $this->loadData(false, false); // Carga instantánea desde caché

        $srvName = $this->connectionDetails['server_name'] ?? $serverId;
        Notification::make()
            ->title('Servidor seleccionado')
            ->body("Conmutado al servidor: {$srvName}")
            ->info()
            ->send();
    }

    public function selectHub(string $hubName): void
    {
        $this->selectedHub = trim($hubName);
        $this->loadData(false, true);

        Notification::make()
            ->title('Virtual Hub seleccionado')
            ->body("Consultando telemetría del hub: {$this->selectedHub}")
            ->info()
            ->send();
    }

    public function loadData(bool $notify = false, bool $force = false): void
    {
        /** @var SoftEtherService $service */
        $service = app(SoftEtherService::class);
        $service->setServer($this->selectedServer, $this->selectedHub ?: null);

        $telemetry = $service->getTelemetry($force);

        $this->serverStatus = $telemetry['serverStatus'] ?? [];
        $this->activeSessions = $telemetry['activeSessions'] ?? [];
        $this->vpnUsers = $telemetry['vpnUsers'] ?? [];
        $this->allVpnUsers = $telemetry['allVpnUsers'] ?? $this->vpnUsers;
        $this->virtualHubs = $telemetry['virtualHubs'] ?? [];
        $this->secureNat = $telemetry['secureNat'] ?? [];
        $this->dhcpTable = $telemetry['dhcpTable'] ?? [];
        $this->allHubRoutes = $telemetry['allHubRoutes'] ?? [];
        $this->l3Switches = $telemetry['l3Switches'] ?? [];
        $this->l3Interfaces = $telemetry['l3Interfaces'] ?? [];
        $this->l3Tables = $telemetry['l3Tables'] ?? [];
        $this->connectionDetails = $telemetry['connectionDetails'] ?? [];
        $this->resetFilterMemos();

        if (empty($this->selectedHub)) {
            $this->selectedHub = $this->connectionDetails['hub'] ?? 'DEFAULT';
        }

        if ($notify) {
            if ($this->serverStatus['is_live'] ?? false) {
                Notification::make()
                    ->title('Datos de SoftEther actualizados')
                    ->body("Sincronizado con {$this->connectionDetails['server_name']} (Hub: {$this->connectionDetails['hub']}).")
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('Sin respuesta RPC')
                    ->body("No se pudo obtener telemetría de {$this->connectionDetails['server_name']}: " . ($this->serverStatus['error_message'] ?? 'Error de conexión'))
                    ->warning()
                    ->send();
            }
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

    public function abrirModalCrearUsuario(?string $hub = null): void
    {
        $this->newUserHub = $hub ?: ($this->userHubFilter ?: ($this->selectedHub ?: ($this->connectionDetails['hub'] ?? 'DEFAULT')));
        $this->newUsername = '';
        $this->newRealname = '';
        $this->newPassword = '';
        $this->newNote = '';
        $this->isCreatingUser = true;
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

        $targetHub = trim($this->newUserHub) ?: ($this->selectedHub ?: 'DEFAULT');

        /** @var SoftEtherService $service */
        $service = app(SoftEtherService::class);
        $service->setServer($this->selectedServer, $targetHub);

        $res = $service->createUser([
            'hub_name' => $targetHub,
            'username' => trim($this->newUsername),
            'real_name' => trim($this->newRealname) ?: trim($this->newUsername),
            'password' => trim($this->newPassword),
            'note' => trim($this->newNote) ?: 'Cliente VPN DataPlus',
        ], $targetHub);

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
            $this->loadData(false, true);
            if ($this->managingHub === $targetHub) {
                $this->cargarDatosHubGestionado();
            }
        } else {
            Notification::make()
                ->title('Error al crear usuario')
                ->body($res['message'])
                ->danger()
                ->send();
        }
    }

    public function eliminarUsuario(string $username, ?string $hub = null): void
    {
        $targetHub = trim((string) ($hub ?: $this->selectedHub ?: 'DEFAULT'));

        /** @var SoftEtherService $service */
        $service = app(SoftEtherService::class);
        $service->setServer($this->selectedServer, $targetHub);
        $res = $service->deleteUser($username, $targetHub);

        if ($res['success']) {
            Notification::make()
                ->title('Usuario eliminado')
                ->body($res['message'])
                ->success()
                ->send();

            $this->vpnUsers = array_values(array_filter($this->vpnUsers, fn ($u) => !(($u['username'] ?? '') === $username && ($u['hub_name'] ?? $targetHub) === $targetHub)));
            $this->allVpnUsers = array_values(array_filter($this->allVpnUsers, fn ($u) => !(($u['username'] ?? '') === $username && ($u['hub_name'] ?? '') === $targetHub)));
            if ($this->managingHub === $targetHub) {
                $this->hubManageUsers = array_values(array_filter($this->hubManageUsers, fn ($u) => ($u['username'] ?? '') !== $username));
            }
            $this->loadData(false, true);
        } else {
            Notification::make()
                ->title('Error al eliminar usuario')
                ->body($res['message'])
                ->danger()
                ->send();
        }
    }

    public function toggleUsuario(string $username, bool $nuevoEstado, ?string $hub = null): void
    {
        $targetHub = trim((string) ($hub ?: $this->selectedHub ?: 'DEFAULT'));

        /** @var SoftEtherService $service */
        $service = app(SoftEtherService::class);
        $service->setServer($this->selectedServer, $targetHub);
        $res = $service->toggleUser($username, $nuevoEstado, $targetHub);

        if ($res['success']) {
            Notification::make()
                ->title('Estado de usuario modificado')
                ->body($res['message'])
                ->success()
                ->send();

            foreach ($this->allVpnUsers as &$u) {
                if (($u['username'] ?? '') === $username && ($u['hub_name'] ?? $targetHub) === $targetHub) {
                    $u['is_active'] = $nuevoEstado;
                    $u['is_timed_disabled'] = false;
                    $u['disabled_until'] = null;
                    $u['estado'] = $nuevoEstado ? 'Habilitado' : 'Deshabilitado';
                }
            }
            unset($u);

            foreach ($this->vpnUsers as &$u) {
                if (($u['username'] ?? '') === $username && ($u['hub_name'] ?? $targetHub) === $targetHub) {
                    $u['is_active'] = $nuevoEstado;
                    $u['is_timed_disabled'] = false;
                    $u['disabled_until'] = null;
                    $u['estado'] = $nuevoEstado ? 'Habilitado' : 'Deshabilitado';
                }
            }
            unset($u);

            if ($this->managingHub === $targetHub) {
                $this->cargarDatosHubGestionado();
            }
        } else {
            Notification::make()
                ->title('Error al cambiar usuario')
                ->body($res['message'])
                ->danger()
                ->send();
        }
    }

    public function abrirModalDeshabilitarUsuario(string $username, ?string $hub = null): void
    {
        $targetHub = trim((string) ($hub ?: $this->selectedHub ?: 'DEFAULT'));
        $this->userToTimedDisable = [
            'username' => $username,
            'hub_name' => $targetHub,
        ];
        $this->disableDurationPreset = '60';
        $this->disableUntilCustom = date('Y-m-d\TH:i', strtotime('+1 day'));
        $this->disableReason = '';
    }

    public function cerrarModalDeshabilitarUsuario(): void
    {
        $this->userToTimedDisable = null;
        $this->disableReason = '';
    }

    public function confirmarDeshabilitarUsuario(): void
    {
        if (empty($this->userToTimedDisable)) {
            return;
        }

        $username = $this->userToTimedDisable['username'];
        $targetHub = $this->userToTimedDisable['hub_name'];

        $minutes = null;
        $untilDatetime = null;

        if ($this->disableDurationPreset === 'custom') {
            if (empty($this->disableUntilCustom) || strtotime($this->disableUntilCustom) <= time()) {
                Notification::make()
                    ->title('Fecha inválida')
                    ->body('Debe indicar una fecha y hora futura válida para la reactivación automática.')
                    ->warning()
                    ->send();
                return;
            }
            $untilDatetime = $this->disableUntilCustom;
        } elseif ($this->disableDurationPreset !== 'indefinite') {
            $minutes = (int) $this->disableDurationPreset;
        }

        /** @var SoftEtherService $service */
        $service = app(SoftEtherService::class);
        $service->setServer($this->selectedServer, $targetHub);

        $res = $service->disableUserForDuration(
            $username,
            $targetHub,
            $minutes,
            $untilDatetime,
            trim($this->disableReason) ?: null
        );

        if ($res['success']) {
            Notification::make()
                ->title('Usuario deshabilitado')
                ->body($res['message'])
                ->warning()
                ->send();

            $this->userToTimedDisable = null;
            $this->disableReason = '';
            $this->loadData(false, true);

            if ($this->managingHub === $targetHub) {
                $this->cargarDatosHubGestionado();
            }
        } else {
            Notification::make()
                ->title('Error al deshabilitar usuario')
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

    public function abrirModalCrearHub(): void
    {
        $this->newHubName = '';
        $this->newHubPassword = '';
        $this->isCreatingHub = true;
    }

    public function cerrarModalCrearHub(): void
    {
        $this->isCreatingHub = false;
        $this->newHubName = '';
        $this->newHubPassword = '';
    }

    public function crearVirtualHub(): void
    {
        $name = trim($this->newHubName);
        if (empty($name)) {
            Notification::make()
                ->title('Nombre requerido')
                ->body('Debe indicar un nombre para el Virtual Hub.')
                ->warning()
                ->send();
            return;
        }

        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $name)) {
            Notification::make()
                ->title('Nombre inválido')
                ->body('El nombre del Hub solo debe contener letras, números, guiones y guiones bajos (sin espacios).')
                ->warning()
                ->send();
            return;
        }

        /** @var SoftEtherService $service */
        $service = app(SoftEtherService::class);
        $service->setServer($this->selectedServer);

        $res = $service->createHub($name, $this->newHubPassword ?: null);

        if ($res['success']) {
            Notification::make()
                ->title('Virtual Hub Creado')
                ->body($res['message'])
                ->success()
                ->send();

            $this->isCreatingHub = false;
            $this->newHubName = '';
            $this->newHubPassword = '';
            $this->selectedHub = $name;
            $this->loadData(false, true);
        } else {
            Notification::make()
                ->title('Error al crear Virtual Hub')
                ->body($res['message'])
                ->danger()
                ->send();
        }
    }

    public function confirmarEliminarHub(string $hubName): void
    {
        if (strtoupper($hubName) === 'DEFAULT') {
            Notification::make()
                ->title('Operación no permitida')
                ->body('El Virtual Hub "DEFAULT" es el hub principal del sistema y no puede eliminarse.')
                ->warning()
                ->send();
            return;
        }

        $this->hubToDelete = $hubName;
    }

    public function cancelarEliminarHub(): void
    {
        $this->hubToDelete = null;
    }

    public function eliminarVirtualHub(): void
    {
        if (empty($this->hubToDelete)) {
            return;
        }

        $hubName = $this->hubToDelete;

        /** @var SoftEtherService $service */
        $service = app(SoftEtherService::class);
        $service->setServer($this->selectedServer);

        $res = $service->deleteHub($hubName);

        if ($res['success']) {
            Notification::make()
                ->title('Virtual Hub Eliminado')
                ->body($res['message'])
                ->success()
                ->send();

            $this->hubToDelete = null;
            if ($this->selectedHub === $hubName) {
                $this->selectedHub = 'DEFAULT';
            }
            $this->loadData(false, true);
        } else {
            Notification::make()
                ->title('Error al eliminar Virtual Hub')
                ->body($res['message'])
                ->danger()
                ->send();
        }
    }

    public function updatedHubSearch(): void
    {
        $this->hubPage = 1;
        $this->memoFilteredHubs = null;
    }

    public function limpiarBusquedaHub(): void
    {
        $this->hubSearch = '';
        $this->hubPage = 1;
        $this->memoFilteredHubs = null;
    }

    public function setHubPage(int $page): void
    {
        $totalPages = $this->getTotalHubPages();
        if ($page < 1) {
            $page = 1;
        }
        if ($page > $totalPages && $totalPages > 0) {
            $page = $totalPages;
        }
        $this->hubPage = $page;
    }

    public function getFilteredVirtualHubs(): array
    {
        if ($this->memoFilteredHubs !== null) {
            return $this->memoFilteredHubs;
        }

        $search = trim(mb_strtolower($this->hubSearch));
        if (empty($search)) {
            return $this->memoFilteredHubs = $this->virtualHubs;
        }

        return $this->memoFilteredHubs = array_values(array_filter($this->virtualHubs, function ($h) use ($search) {
            $name = mb_strtolower($h['hub_name'] ?? '');
            $status = mb_strtolower($h['status'] ?? '');
            $type = mb_strtolower($h['type'] ?? '');
            return str_contains($name, $search) || str_contains($status, $search) || str_contains($type, $search);
        }));
    }

    public function getPaginatedVirtualHubs(): array
    {
        $filtered = $this->getFilteredVirtualHubs();
        $total = count($filtered);
        $offset = ($this->hubPage - 1) * $this->hubsPerPage;

        if ($offset >= $total && $total > 0) {
            $this->hubPage = max(1, (int) ceil($total / $this->hubsPerPage));
            $offset = ($this->hubPage - 1) * $this->hubsPerPage;
        }

        return array_slice($filtered, $offset, $this->hubsPerPage);
    }

    public function getTotalHubPages(): int
    {
        $total = count($this->getFilteredVirtualHubs());
        return $total > 0 ? (int) ceil($total / $this->hubsPerPage) : 1;
    }

    // =========================================================================
    // FILTRADO Y PAGINACIÓN DE CUENTAS VPN (POR USUARIO Y VIRTUAL HUB)
    // =========================================================================

    public function updatedUserSearch(): void
    {
        $this->userPage = 1;
        $this->memoFilteredUsers = null;
    }

    public function updatedUserHubFilter(): void
    {
        $this->userPage = 1;
        $this->memoFilteredUsers = null;
    }

    public function updatedUserStatusFilter(): void
    {
        $this->userPage = 1;
        $this->memoFilteredUsers = null;
    }

    public function limpiarFiltrosUsuarios(): void
    {
        $this->userSearch = '';
        $this->userHubFilter = '';
        $this->userStatusFilter = 'todos';
        $this->userPage = 1;
        $this->memoFilteredUsers = null;
    }

    public function setUserPage(int $page): void
    {
        $totalPages = $this->getTotalUserPages();
        if ($page < 1) {
            $page = 1;
        }
        if ($page > $totalPages && $totalPages > 0) {
            $page = $totalPages;
        }
        $this->userPage = $page;
    }

    public function getFilteredVpnUsers(): array
    {
        if ($this->memoFilteredUsers !== null) {
            return $this->memoFilteredUsers;
        }

        $source = !empty($this->allVpnUsers) ? $this->allVpnUsers : $this->vpnUsers;
        $search = trim(mb_strtolower($this->userSearch));
        $hubFilter = trim($this->userHubFilter);
        $statusFilter = $this->userStatusFilter;

        return $this->memoFilteredUsers = array_values(array_filter($source, function ($u) use ($search, $hubFilter, $statusFilter) {
            $hubName = $u['hub_name'] ?? ($this->connectionDetails['hub'] ?? 'DEFAULT');

            if (!empty($hubFilter) && strcasecmp($hubName, $hubFilter) !== 0) {
                return false;
            }

            if ($statusFilter === 'habilitados' && empty($u['is_active'])) {
                return false;
            }
            if ($statusFilter === 'deshabilitados' && !empty($u['is_active'])) {
                return false;
            }
            if ($statusFilter === 'temporal' && empty($u['is_timed_disabled'])) {
                return false;
            }

            if (!empty($search)) {
                $username = mb_strtolower($u['username'] ?? '');
                $realName = mb_strtolower($u['real_name'] ?? '');
                $note = mb_strtolower($u['note'] ?? '');
                $hubLow = mb_strtolower($hubName);

                return str_contains($username, $search)
                    || str_contains($realName, $search)
                    || str_contains($note, $search)
                    || str_contains($hubLow, $search);
            }

            return true;
        }));
    }

    public function getPaginatedVpnUsers(): array
    {
        $filtered = $this->getFilteredVpnUsers();
        $total = count($filtered);
        $offset = ($this->userPage - 1) * $this->usersPerPage;

        if ($offset >= $total && $total > 0) {
            $this->userPage = max(1, (int) ceil($total / $this->usersPerPage));
            $offset = ($this->userPage - 1) * $this->usersPerPage;
        }

        return array_slice($filtered, $offset, $this->usersPerPage);
    }

    public function getTotalUserPages(): int
    {
        $total = count($this->getFilteredVpnUsers());
        return $total > 0 ? (int) ceil($total / $this->usersPerPage) : 1;
    }

    // =========================================================================
    // GESTIÓN COMPLETA POR VIRTUAL HUB (DHCP, SECURENAT, USUARIOS, LOGS)
    // =========================================================================

    public function abrirGestionHub(string $hubName, ?string $subTab = null): void
    {
        $this->managingHub = trim($hubName);
        if ($subTab !== null && $subTab !== '') {
            $this->hubManageTab = $subTab;
        }
        $this->hubNewUsername = '';
        $this->hubNewRealname = '';
        $this->hubNewPassword = '';
        $this->hubNewNote = '';
        $this->cargarDatosHubGestionado();
    }

    public function cerrarGestionHub(): void
    {
        $this->managingHub = null;
    }

    public function setHubManageTab(string $subTab): void
    {
        $this->hubManageTab = $subTab;
    }

    public function cargarDatosHubGestionado(): void
    {
        if (empty($this->managingHub)) {
            return;
        }

        /** @var SoftEtherService $service */
        $service = app(SoftEtherService::class);
        $service->setServer($this->selectedServer, $this->managingHub);

        $details = $service->getHubManagementDetails($this->managingHub);
        $nat = $details['secureNat'] ?? [];
        $dhcp = $details['dhcpTable'] ?? [];
        $users = $details['users'] ?? [];
        $logs = $details['logs'] ?? [];

        $this->hubManageSecureNat = $nat;
        $this->hubManageDhcpTable = $dhcp;
        $this->hubManageUsers = $users;
        $this->hubManageLog = $logs;

        $this->natForm = [
            'securenat_enabled' => (bool) ($nat['securenat_enabled'] ?? false),
            'use_nat' => (bool) ($nat['use_nat'] ?? true),
            'use_dhcp' => (bool) ($nat['use_dhcp'] ?? true),
            'virtual_gateway_ip' => $nat['virtual_gateway_ip'] ?: '192.168.30.1',
            'virtual_subnet_mask' => $nat['virtual_subnet_mask'] ?: '255.255.255.0',
            'dhcp_gateway_ip' => $nat['dhcp_gateway_ip'] ?: ($nat['virtual_gateway_ip'] ?: '192.168.30.1'),
            'dhcp_lease_start' => $nat['dhcp_lease_start'] ?: '192.168.30.10',
            'dhcp_lease_end' => $nat['dhcp_lease_end'] ?: '192.168.30.200',
            'dhcp_lease_expire_seconds' => (int) ($nat['dhcp_lease_expire_seconds'] ?: 7200),
            'dns_server_1' => $nat['dns_server_1'] ?? '8.8.8.8',
            'dns_server_2' => $nat['dns_server_2'] ?? '1.1.1.1',
            'domain_name' => $nat['domain_name'] ?? '',
            'mtu' => (int) ($nat['mtu'] ?: 1500),
            'nat_tcp_timeout' => (int) ($nat['nat_tcp_timeout'] ?: 1800),
            'nat_udp_timeout' => (int) ($nat['nat_udp_timeout'] ?: 60),
            'save_log' => (bool) ($nat['save_log'] ?? true),
        ];

        $this->hubApplyPushRoutes = (bool) ($nat['apply_dhcp_push_routes'] ?? !empty($nat['dhcp_push_routes_list']));
        $this->hubRoutesList = array_values($nat['dhcp_push_routes_list'] ?? []);
        $this->newHubRouteNetwork = '';
        $this->newHubRouteMask = '255.255.255.0';
        $this->newHubRouteGateway = $nat['dhcp_gateway_ip'] ?: ($nat['virtual_gateway_ip'] ?: '192.168.30.1');

        $this->logForm = [
            'save_security_log' => (bool) ($logs['save_security_log'] ?? false),
            'security_log_switch_type' => (int) ($logs['security_log_switch_type'] ?? 4),
            'save_packet_log' => (bool) ($logs['save_packet_log'] ?? false),
            'packet_log_switch_type' => (int) ($logs['packet_log_switch_type'] ?? 4),
        ];
    }

    public function toggleHubSecureNat(bool $enable, ?string $hub = null): void
    {
        $targetHub = trim((string) ($hub ?: $this->managingHub ?: $this->selectedHub ?: 'DEFAULT'));

        /** @var SoftEtherService $service */
        $service = app(SoftEtherService::class);
        $service->setServer($this->selectedServer, $targetHub);
        $res = $service->toggleSecureNat($enable, $targetHub);

        if ($res['success']) {
            Notification::make()
                ->title('SecureNAT actualizado')
                ->body($res['message'])
                ->success()
                ->send();

            if ($this->managingHub === $targetHub) {
                $this->natForm['securenat_enabled'] = $enable;
                $this->cargarDatosHubGestionado();
            }
            $this->loadData(false, true);
        } else {
            Notification::make()
                ->title('Error al cambiar SecureNAT')
                ->body($res['message'])
                ->danger()
                ->send();
        }
    }

    public function guardarConfigSecureNatDhcp(): void
    {
        if (empty($this->managingHub)) {
            return;
        }

        /** @var SoftEtherService $service */
        $service = app(SoftEtherService::class);
        $service->setServer($this->selectedServer, $this->managingHub);

        $res = $service->saveSecureNatConfig($this->natForm, $this->managingHub);

        if ($res['success']) {
            Notification::make()
                ->title('SecureNAT & DHCP guardados')
                ->body($res['message'])
                ->success()
                ->send();

            $this->cargarDatosHubGestionado();
            $this->loadData(false, true);
        } else {
            Notification::make()
                ->title('Error al guardar SecureNAT/DHCP')
                ->body($res['message'])
                ->danger()
                ->send();
        }
    }

    public function guardarConfigLogsHub(): void
    {
        if (empty($this->managingHub)) {
            return;
        }

        /** @var SoftEtherService $service */
        $service = app(SoftEtherService::class);
        $service->setServer($this->selectedServer, $this->managingHub);

        $res = $service->setHubLog($this->logForm, $this->managingHub);

        if ($res['success']) {
            Notification::make()
                ->title('Logs del Virtual Hub actualizados')
                ->body($res['message'])
                ->success()
                ->send();

            $this->cargarDatosHubGestionado();
        } else {
            Notification::make()
                ->title('Error al actualizar Logs')
                ->body($res['message'])
                ->danger()
                ->send();
        }
    }

    public function crearUsuarioEnHub(): void
    {
        if (empty($this->managingHub)) {
            return;
        }

        if (empty(trim($this->hubNewUsername)) || empty(trim($this->hubNewPassword))) {
            Notification::make()
                ->title('Campos requeridos')
                ->body('Debe indicar nombre de usuario y contraseña.')
                ->warning()
                ->send();
            return;
        }

        /** @var SoftEtherService $service */
        $service = app(SoftEtherService::class);
        $service->setServer($this->selectedServer, $this->managingHub);

        $res = $service->createUser([
            'hub_name' => $this->managingHub,
            'username' => trim($this->hubNewUsername),
            'real_name' => trim($this->hubNewRealname) ?: trim($this->hubNewUsername),
            'password' => trim($this->hubNewPassword),
            'note' => trim($this->hubNewNote) ?: 'Cliente VPN DataPlus',
        ], $this->managingHub);

        if ($res['success']) {
            Notification::make()
                ->title('Usuario añadido al Hub')
                ->body($res['message'])
                ->success()
                ->send();

            $this->hubNewUsername = '';
            $this->hubNewRealname = '';
            $this->hubNewPassword = '';
            $this->hubNewNote = '';
            $this->cargarDatosHubGestionado();
            $this->loadData(false, true);
        } else {
            Notification::make()
                ->title('Error al crear usuario')
                ->body($res['message'])
                ->danger()
                ->send();
        }
    }

    // =========================================================================
    // RUTAS ESTÁTICAS DE SECURENAT POR VIRTUAL HUB
    // =========================================================================

    public function agregarRutaHub(): void
    {
        $net = trim($this->newHubRouteNetwork);
        $mask = trim($this->newHubRouteMask);
        $gw = trim($this->newHubRouteGateway);

        if (!filter_var($net, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)
            || !filter_var($mask, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)
            || !filter_var($gw, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            Notification::make()
                ->title('Datos de ruta inválidos')
                ->body('Red destino, máscara de subred y gateway deben ser direcciones IPv4 válidas.')
                ->warning()
                ->send();
            return;
        }

        $this->hubRoutesList[] = [
            'network' => $net,
            'mask' => $mask,
            'gateway' => $gw,
        ];

        $this->newHubRouteNetwork = '';
    }

    public function eliminarRutaHub(int $index): void
    {
        if (isset($this->hubRoutesList[$index])) {
            unset($this->hubRoutesList[$index]);
            $this->hubRoutesList = array_values($this->hubRoutesList);
        }
    }

    public function guardarRutasHub(): void
    {
        if (empty($this->managingHub)) {
            return;
        }

        /** @var SoftEtherService $service */
        $service = app(SoftEtherService::class);
        $service->setServer($this->selectedServer, $this->managingHub);

        $res = $service->saveSecureNatPushRoutes($this->hubRoutesList, $this->hubApplyPushRoutes, $this->managingHub);

        if ($res['success']) {
            Notification::make()
                ->title('Rutas SecureNAT guardadas')
                ->body($res['message'])
                ->success()
                ->send();

            $this->cargarDatosHubGestionado();
            $this->loadData(false, true);
        } else {
            Notification::make()
                ->title('Error al guardar rutas')
                ->body($res['message'])
                ->danger()
                ->send();
        }
    }

    // =========================================================================
    // LAYER 3 SWITCHES, INTERFACES L3 Y TABLAS DE RUTAS
    // =========================================================================

    public function updatedL3Search(): void
    {
        $this->memoFilteredL3Ifs = null;
        $this->memoFilteredL3Tbls = null;
    }

    public function updatedL3SwitchFilter(): void
    {
        $this->memoFilteredL3Ifs = null;
        $this->memoFilteredL3Tbls = null;
    }

    public function updatedRouteHubSearch(): void
    {
        $this->memoFilteredAllHubRoutes = null;
    }

    public function getFilteredL3Interfaces(): array
    {
        if ($this->memoFilteredL3Ifs !== null) {
            return $this->memoFilteredL3Ifs;
        }

        $search = trim(mb_strtolower($this->l3Search));
        $swFilter = trim($this->l3SwitchFilter);

        return $this->memoFilteredL3Ifs = array_values(array_filter($this->l3Interfaces, function ($if) use ($search, $swFilter) {
            if ($swFilter !== '' && strcasecmp($if['switch_name'] ?? '', $swFilter) !== 0) {
                return false;
            }
            if ($search !== '') {
                $sw = mb_strtolower($if['switch_name'] ?? '');
                $hub = mb_strtolower($if['hub_name'] ?? '');
                $ip = mb_strtolower($if['ip_address'] ?? '');
                $mask = mb_strtolower($if['subnet_mask'] ?? '');
                return str_contains($sw, $search) || str_contains($hub, $search) || str_contains($ip, $search) || str_contains($mask, $search);
            }
            return true;
        }));
    }

    public function getFilteredL3Tables(): array
    {
        if ($this->memoFilteredL3Tbls !== null) {
            return $this->memoFilteredL3Tbls;
        }

        $search = trim(mb_strtolower($this->l3Search));
        $swFilter = trim($this->l3SwitchFilter);

        return $this->memoFilteredL3Tbls = array_values(array_filter($this->l3Tables, function ($tbl) use ($search, $swFilter) {
            if ($swFilter !== '' && strcasecmp($tbl['switch_name'] ?? '', $swFilter) !== 0) {
                return false;
            }
            if ($search !== '') {
                $sw = mb_strtolower($tbl['switch_name'] ?? '');
                $net = mb_strtolower($tbl['network_address'] ?? '');
                $gw = mb_strtolower($tbl['gateway_address'] ?? '');
                return str_contains($sw, $search) || str_contains($net, $search) || str_contains($gw, $search);
            }
            return true;
        }));
    }

    public function getFilteredAllHubRoutes(): array
    {
        if ($this->memoFilteredAllHubRoutes !== null) {
            return $this->memoFilteredAllHubRoutes;
        }

        $search = trim(mb_strtolower($this->routeHubSearch));
        $items = $this->allHubRoutes;

        if ($search === '') {
            return $this->memoFilteredAllHubRoutes = $items;
        }

        return $this->memoFilteredAllHubRoutes = array_values(array_filter($items, function ($item) use ($search) {
            $hub = mb_strtolower($item['hub_name'] ?? '');
            $raw = mb_strtolower($item['dhcp_push_routes'] ?? '');
            return str_contains($hub, $search) || str_contains($raw, $search);
        }));
    }

    public function abrirModalCrearL3Switch(): void
    {
        $this->newL3SwitchName = '';
        $this->isCreatingL3Switch = true;
    }

    public function cerrarModalCrearL3Switch(): void
    {
        $this->isCreatingL3Switch = false;
        $this->newL3SwitchName = '';
    }

    public function crearL3Switch(): void
    {
        /** @var SoftEtherService $service */
        $service = app(SoftEtherService::class);
        $service->setServer($this->selectedServer);
        $res = $service->createL3Switch($this->newL3SwitchName);

        if ($res['success']) {
            Notification::make()->title('Switch L3 creado')->body($res['message'])->success()->send();
            $this->isCreatingL3Switch = false;
            $this->newL3SwitchName = '';
            $this->loadData(false, true);
        } else {
            Notification::make()->title('Error al crear Switch L3')->body($res['message'])->danger()->send();
        }
    }

    public function toggleL3Switch(string $switchName, bool $start): void
    {
        /** @var SoftEtherService $service */
        $service = app(SoftEtherService::class);
        $service->setServer($this->selectedServer);
        $res = $service->toggleL3Switch($switchName, $start);

        if ($res['success']) {
            Notification::make()->title('Switch L3 actualizado')->body($res['message'])->success()->send();
            $this->loadData(false, true);
        } else {
            Notification::make()->title('Error al cambiar estado del Switch L3')->body($res['message'])->danger()->send();
        }
    }

    public function eliminarL3Switch(string $switchName): void
    {
        /** @var SoftEtherService $service */
        $service = app(SoftEtherService::class);
        $service->setServer($this->selectedServer);
        $res = $service->deleteL3Switch($switchName);

        if ($res['success']) {
            Notification::make()->title('Switch L3 eliminado')->body($res['message'])->success()->send();
            $this->loadData(false, true);
        } else {
            Notification::make()->title('Error al eliminar Switch L3')->body($res['message'])->danger()->send();
        }
    }

    public function abrirModalNuevaInterfazL3(?string $switchName = null): void
    {
        $this->editingL3IfOriginal = null;
        $this->l3IfFormSwitch = $switchName ?: ($this->l3SwitchFilter ?: ($this->l3Switches[0]['name'] ?? ''));
        $this->l3IfFormHub = $this->virtualHubs[0]['hub_name'] ?? 'DEFAULT';
        $this->l3IfFormIp = '';
        $this->l3IfFormMask = '255.255.255.0';
        $this->isEditingL3If = true;
    }

    public function abrirModalEditarInterfazL3(string $switchName, string $hubName, string $ip, string $mask): void
    {
        $this->editingL3IfOriginal = [
            'switch_name' => $switchName,
            'hub_name' => $hubName,
            'ip_address' => $ip,
            'subnet_mask' => $mask,
        ];
        $this->l3IfFormSwitch = $switchName;
        $this->l3IfFormHub = $hubName;
        $this->l3IfFormIp = $ip;
        $this->l3IfFormMask = $mask;
        $this->isEditingL3If = true;
    }

    public function cerrarModalInterfazL3(): void
    {
        $this->isEditingL3If = false;
        $this->editingL3IfOriginal = null;
    }

    public function guardarInterfazL3(): void
    {
        /** @var SoftEtherService $service */
        $service = app(SoftEtherService::class);
        $service->setServer($this->selectedServer);

        if ($this->editingL3IfOriginal) {
            $res = $service->updateL3Interface(
                $this->editingL3IfOriginal['switch_name'],
                $this->editingL3IfOriginal['hub_name'],
                $this->editingL3IfOriginal['ip_address'],
                $this->editingL3IfOriginal['subnet_mask'],
                $this->l3IfFormHub,
                $this->l3IfFormIp,
                $this->l3IfFormMask
            );
        } else {
            $res = $service->addL3Interface(
                $this->l3IfFormSwitch,
                $this->l3IfFormHub,
                $this->l3IfFormIp,
                $this->l3IfFormMask
            );
        }

        if ($res['success']) {
            Notification::make()->title('Interfaz L3 guardada')->body($res['message'])->success()->send();
            $this->isEditingL3If = false;
            $this->editingL3IfOriginal = null;
            $this->loadData(false, true);
        } else {
            Notification::make()->title('Error al guardar interfaz L3')->body($res['message'])->danger()->send();
        }
    }

    public function eliminarInterfazL3(string $switchName, string $hubName): void
    {
        /** @var SoftEtherService $service */
        $service = app(SoftEtherService::class);
        $service->setServer($this->selectedServer);
        $res = $service->deleteL3Interface($switchName, $hubName);

        if ($res['success']) {
            Notification::make()->title('Interfaz L3 eliminada')->body($res['message'])->success()->send();
            $this->loadData(false, true);
        } else {
            Notification::make()->title('Error al eliminar interfaz L3')->body($res['message'])->danger()->send();
        }
    }

    public function abrirModalNuevaRutaL3(?string $switchName = null): void
    {
        $this->editingL3RouteOriginal = null;
        $this->l3RouteFormSwitch = $switchName ?: ($this->l3SwitchFilter ?: ($this->l3Switches[0]['name'] ?? ''));
        $this->l3RouteFormNetwork = '';
        $this->l3RouteFormMask = '255.255.255.0';
        $this->l3RouteFormGateway = '';
        $this->l3RouteFormMetric = 1;
        $this->isEditingL3Route = true;
    }

    public function abrirModalEditarRutaL3(string $switchName, string $network, string $mask, string $gateway, int $metric): void
    {
        $this->editingL3RouteOriginal = [
            'switch_name' => $switchName,
            'network_address' => $network,
            'subnet_mask' => $mask,
            'gateway_address' => $gateway,
            'metric' => $metric,
        ];
        $this->l3RouteFormSwitch = $switchName;
        $this->l3RouteFormNetwork = $network;
        $this->l3RouteFormMask = $mask;
        $this->l3RouteFormGateway = $gateway;
        $this->l3RouteFormMetric = max(1, $metric);
        $this->isEditingL3Route = true;
    }

    public function cerrarModalRutaL3(): void
    {
        $this->isEditingL3Route = false;
        $this->editingL3RouteOriginal = null;
    }

    public function guardarRutaL3(): void
    {
        /** @var SoftEtherService $service */
        $service = app(SoftEtherService::class);
        $service->setServer($this->selectedServer);

        if ($this->editingL3RouteOriginal) {
            $res = $service->updateL3Route(
                $this->editingL3RouteOriginal['switch_name'],
                $this->editingL3RouteOriginal,
                [
                    'network_address' => $this->l3RouteFormNetwork,
                    'subnet_mask' => $this->l3RouteFormMask,
                    'gateway_address' => $this->l3RouteFormGateway,
                    'metric' => $this->l3RouteFormMetric,
                ]
            );
        } else {
            $res = $service->addL3Route(
                $this->l3RouteFormSwitch,
                $this->l3RouteFormNetwork,
                $this->l3RouteFormMask,
                $this->l3RouteFormGateway,
                $this->l3RouteFormMetric
            );
        }

        if ($res['success']) {
            Notification::make()->title('Ruta L3 guardada')->body($res['message'])->success()->send();
            $this->isEditingL3Route = false;
            $this->editingL3RouteOriginal = null;
            $this->loadData(false, true);
        } else {
            Notification::make()->title('Error al guardar ruta L3')->body($res['message'])->danger()->send();
        }
    }

    public function eliminarRutaL3(string $switchName, string $network, string $mask, string $gateway, int $metric): void
    {
        /** @var SoftEtherService $service */
        $service = app(SoftEtherService::class);
        $service->setServer($this->selectedServer);
        $res = $service->deleteL3Route($switchName, $network, $mask, $gateway, $metric);

        if ($res['success']) {
            Notification::make()->title('Ruta L3 eliminada')->body($res['message'])->success()->send();
            $this->loadData(false, true);
        } else {
            Notification::make()->title('Error al eliminar ruta L3')->body($res['message'])->danger()->send();
        }
    }
}
