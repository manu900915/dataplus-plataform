<?php

namespace App\Filament\Pages;

use App\Models\LdapConfiguration;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use LdapRecord\Container;
use LdapRecord\Connection;

class LdapSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-server';
    protected static string $view = 'filament.pages.ldap-settings';
    
    protected static ?string $navigationGroup = 'Administración';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Configuración LDAP';

    public ?array $data = [];

    public function mount(): void
    {
        $config = LdapConfiguration::first();
        $this->form->fill($config ? $config->toArray() : [
            'host' => 'lldap',
            'port' => 3890,
            'base_dn' => 'dc=dataplus,dc=cu',
            'ssl' => false,
            'tls' => false,
            'timeout' => 5,
            'is_active' => true,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Conexión al Servidor')
                    ->description('Configura los parámetros de red para conectar con LLDAP')
                    ->schema([
                        Forms\Components\TextInput::make('host')
                            ->label('Host del Servidor')
                            ->required()
                            ->helperText('Ej: lldap, host.docker.internal, o una IP'),
                        Forms\Components\TextInput::make('port')
                            ->label('Puerto')
                            ->numeric()
                            ->required()
                            ->default(3890),
                        Forms\Components\TextInput::make('base_dn')
                            ->label('Base DN')
                            ->required()
                            ->default('dc=dataplus,dc=cu'),
                        Forms\Components\Toggle::make('ssl')
                            ->label('Usar SSL (ldaps://)')
                            ->default(false),
                        Forms\Components\Toggle::make('tls')
                            ->label('Usar StartTLS')
                            ->default(false)
                            ->helperText('Recomendado para conexiones seguras en puerto 3890'),
                        Forms\Components\TextInput::make('timeout')
                            ->label('Tiempo de espera (segundos)')
                            ->numeric()
                            ->default(5),
                    ])->columns(2),

                Forms\Components\Section::make('Credenciales de Bind')
                    ->description('Usuario con permisos para buscar en el directorio LDAP')
                    ->schema([
                        Forms\Components\TextInput::make('username')
                            ->label('Bind Username (DN completo)')
                            ->placeholder('uid=admin,ou=people,dc=dataplus,dc=cu'),
                        Forms\Components\TextInput::make('password')
                            ->label('Contraseña')
                            ->password()
                            ->revealable(),
                    ])->columns(2),

                Forms\Components\Section::make('Estado')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Habilitar autenticación LDAP')
                            ->default(true)
                            ->helperText('Si se desactiva, el sistema solo usará autenticación local.'),
                    ]),
            ])
            ->statePath('data');
    }

    public function testConnection(): void
    {
        $data = $this->form->getState();

        try {
            // Crear una conexión temporal con los datos del formulario
            $connection = new Connection([
                'hosts' => [$data['host']],
                'port' => $data['port'],
                'base_dn' => $data['base_dn'],
                'username' => $data['username'],
                'password' => $data['password'],
                'timeout' => $data['timeout'],
                'options' => [
                    LDAP_OPT_PROTOCOL_VERSION => 3,
                    LDAP_OPT_NETWORK_TIMEOUT => $data['timeout'],
                ],
            ]);

            // Si SSL está activado, usar ldaps://
            if ($data['ssl']) {
                $connection->getConfiguration()->set('use_ssl', true);
            }

            // Si TLS está activado, usar StartTLS
            if ($data['tls']) {
                $connection->getConfiguration()->set('use_tls', true);
            }

            // Intentar conectar
            $connection->connect();

            // Intentar hacer bind con las credenciales
            if ($data['username'] && $data['password']) {
                $connection->auth()->bind($data['username'], $data['password']);
            }

            Notification::make()
                ->title('✅ Conexión LDAP exitosa')
                ->body("Conectado a {$data['host']}:{$data['port']} correctamente.")
                ->success()
                ->send();

        } catch (\LdapRecord\Auth\BindException $e) {
            Notification::make()
                ->title('❌ Error de autenticación LDAP')
                ->body("No se pudo hacer bind: " . $e->getDetailedError()?->getDiagnosticMessage() ?? $e->getMessage())
                ->danger()
                ->send();
        } catch (\LdapRecord\LdapRecordException $e) {
            Notification::make()
                ->title(' Error de conexión LDAP')
                ->body($e->getMessage())
                ->danger()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title(' Error inesperado')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function save(): void
{
    $data = $this->form->getState();
    
    // Guardar o actualizar la configuración (solo mantenemos 1 registro)
    LdapConfiguration::updateOrCreate(['id' => 1], $data);

    // Actualizar la configuración de Laravel en tiempo de ejecución
    config([
        'ldap.connections.default.hosts' => [$data['host']],
        'ldap.connections.default.port' => (int) $data['port'],
        'ldap.connections.default.base_dn' => $data['base_dn'],
        'ldap.connections.default.username' => $data['username'],
        'ldap.connections.default.password' => $data['password'],
        'ldap.connections.default.use_ssl' => $data['ssl'],
        'ldap.connections.default.use_tls' => $data['tls'],
        'ldap.connections.default.timeout' => (int) ($data['timeout'] ?? 5),
    ]);

    Notification::make()
        ->title('Configuración LDAP guardada exitosamente')
        ->body('Los cambios se aplicarán en la próxima autenticación.')
        ->success()
        ->send();
}
}
