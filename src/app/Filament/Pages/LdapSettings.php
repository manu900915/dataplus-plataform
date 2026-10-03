<?php

namespace App\Filament\Pages;

use App\Models\LdapConfiguration;
use App\Models\User;
use App\Services\LdapSyncService;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use LdapRecord\Connection;

class LdapSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.pages.ldap-settings';

    protected static ?string $navigationIcon = 'heroicon-o-server-stack';

    protected static ?string $navigationGroup = 'Configuración';

    protected static ?string $navigationLabel = 'Servidor LDAP';

    protected static ?string $title = 'Integración y Directorio LDAP';

    public ?array $data = [];

    public string $activeTab = 'config'; // 'config', 'directory', 'diagnostics'

    public ?bool $connectionStatus = null;
    public ?float $connectionLatency = null;
    public ?string $connectionMessage = null;

    public array $directoryUsers = [];
    public bool $loadingUsers = false;

    public function mount(): void
    {
        $config = LdapConfiguration::first();

        $defaultData = [
            'host' => 'lldap',
            'port' => 3890,
            'base_dn' => 'dc=dataplus,dc=cu',
            'ssl' => false,
            'tls' => false,
            'timeout' => 5,
            'is_active' => true,
        ];

        $this->form->fill($config ? $config->toArray() : $defaultData);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\Section::make('Conectividad de Red')
                            ->description('Parámetros de red hacia el contenedor o host LLDAP')
                            ->schema([
                                Forms\Components\TextInput::make('host')
                                    ->label('Servidor / Host')
                                    ->placeholder('lldap o host.docker.internal')
                                    ->required()
                                    ->helperText('Nombre del servicio docker o IP del servidor'),

                                Forms\Components\TextInput::make('port')
                                    ->label('Puerto')
                                    ->numeric()
                                    ->default(3890)
                                    ->required()
                                    ->helperText('3890 estándar LLDAP, 6360 para LDAPS'),

                                Forms\Components\TextInput::make('timeout')
                                    ->label('Timeout (segundos)')
                                    ->numeric()
                                    ->default(5)
                                    ->required(),
                            ])->columnSpan(2),

                        Forms\Components\Section::make('Seguridad de Canal')
                            ->description('Cifrado y activación')
                            ->schema([
                                Forms\Components\Toggle::make('is_active')
                                    ->label('LDAP Habilitado')
                                    ->default(true)
                                    ->helperText('Activa la autenticación híbrida'),

                                Forms\Components\Toggle::make('ssl')
                                    ->label('Cifrado LDAPS')
                                    ->helperText('Requiere puerto SSL (ej: 6360)'),

                                Forms\Components\Toggle::make('tls')
                                    ->label('STARTTLS')
                                    ->helperText('Negociar TLS sobre puerto plano'),
                            ])->columnSpan(1),
                    ]),

                Forms\Components\Section::make('Autenticación y Búsqueda (Bind)')
                    ->description('Credenciales de la cuenta de servicio autorizada para explorar el directorio')
                    ->schema([
                        Forms\Components\TextInput::make('base_dn')
                            ->label('Base DN del Directorio')
                            ->required()
                            ->default('dc=dataplus,dc=cu')
                            ->helperText('Raíz de búsqueda para usuarios y grupos')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('username')
                            ->label('Bind DN (Usuario de Servicio)')
                            ->helperText('Ejemplo: uid=admin,ou=people,dc=dataplus,dc=cu')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('password')
                            ->label('Contraseña de Bind')
                            ->password()
                            ->revealable()
                            ->helperText('Dejar en blanco para conservar la clave actual')
                            ->columnSpanFull(),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    public function testConnection(LdapSyncService $syncService): void
    {
        $result = $syncService->testConnection();

        $this->connectionStatus = $result['success'];
        $this->connectionLatency = $result['latency'] ?? null;
        $this->connectionMessage = $result['message'];

        if ($result['success']) {
            Notification::make()
                ->title('✅ Conexión LDAP Exitosa')
                ->body("Respuesta en {$result['latency']} ms desde {$result['host']}:{$result['port']}")
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('❌ Error al contactar LDAP')
                ->body($result['message'])
                ->danger()
                ->send();
        }
    }

    public function loadDirectoryUsers(LdapSyncService $syncService): void
    {
        $this->loadingUsers = true;
        $this->directoryUsers = $syncService->getDirectoryUsers();
        $this->loadingUsers = false;
        $this->activeTab = 'directory';

        Notification::make()
            ->title('Directorio actualizado')
            ->body(count($this->directoryUsers) . ' usuarios encontrados en LDAP.')
            ->info()
            ->send();
    }

    public function syncAllUsers(LdapSyncService $syncService): void
    {
        $result = $syncService->syncAllUsers();

        // Actualizar lista en pantalla si estaba cargada
        $this->directoryUsers = $syncService->getDirectoryUsers();

        Notification::make()
            ->title('🚀 Sincronización Completada')
            ->body("Total en LDAP: {$result['total']} | Nuevos importados: {$result['imported']} | Actualizados: {$result['updated']}")
            ->success()
            ->send();
    }

    public function syncIndividual(string $uid, string $email, string $name, LdapSyncService $syncService): void
    {
        $syncService->syncSingleUser([
            'uid'   => $uid,
            'email' => $email,
            'name'  => $name,
        ]);

        $this->directoryUsers = $syncService->getDirectoryUsers();

        Notification::make()
            ->title('Usuario sincronizado')
            ->body("El usuario {$name} ({$uid}) ya está disponible en DataPlus.")
            ->success()
            ->send();
    }

    public function save(): void
    {
        $data = $this->form->getState();

        LdapConfiguration::updateOrCreate(['id' => 1], $data);

        $useTls = (bool) ($data['ssl'] || $data['tls']);
        config([
            'ldap.connections.default.hosts' => [$data['host']],
            'ldap.connections.default.port' => (int) $data['port'],
            'ldap.connections.default.base_dn' => $data['base_dn'],
            'ldap.connections.default.username' => $data['username'],
            'ldap.connections.default.password' => $data['password'],
            'ldap.connections.default.use_tls' => $useTls,
            'ldap.connections.default.timeout' => (int) ($data['timeout'] ?? 5),
        ]);

        $defaultConn = config('ldap.connections.default', []);
        if (is_array($defaultConn)) {
            unset($defaultConn['ssl'], $defaultConn['use_ssl'], $defaultConn['tls']);
            $defaultConn['use_tls'] = $useTls;
            config(['ldap.connections.default' => $defaultConn]);
        }

        Notification::make()
            ->title('Configuración guardada')
            ->body('Los parámetros de LDAP han sido actualizados en la base de datos.')
            ->success()
            ->send();
    }

    public function getSyncedCountProperty(): int
    {
        return User::whereNotNull('ldap_uid')->count();
    }

    public function getTotalUsersProperty(): int
    {
        return User::count();
    }
}
