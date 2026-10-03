<?php

namespace App\Filament\Pages;

use App\Models\LdapConfiguration;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use LdapRecord\Container;
use LdapRecord\Connection;

class LdapSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.pages.ldap-settings';

    protected static ?string $navigationIcon = 'heroicon-o-server-stack';

    protected static ?string $navigationGroup = 'Configuración';

    protected static ?string $navigationLabel = 'Configuración LDAP';

    protected static ?string $title = 'Servidor de Autenticación LDAP';

    public ?array $data = [];

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
                Forms\Components\Section::make('Conexión')
                    ->description('Configura los parámetros de red para conectar con LLDAP')
                    ->schema([
                        Forms\Components\TextInput::make('host')
                            ->label('Servidor / Host')
                            ->required()
                            ->helperText('Ej: lldap, host.docker.internal, o una IP'),

                        Forms\Components\TextInput::make('port')
                            ->label('Puerto')
                            ->numeric()
                            ->default(3890)
                            ->required()
                            ->helperText('LLDAP usa 3890 por defecto (o 6360 para SSL)'),

                        Forms\Components\Toggle::make('ssl')
                            ->label('Usar SSL (ldaps://)')
                            ->helperText('Activar solo si el puerto configurado es SSL (ej: 6360)'),

                        Forms\Components\Toggle::make('tls')
                            ->label('Usar STARTTLS')
                            ->helperText('Negociar TLS sobre la conexión estándar'),

                        Forms\Components\TextInput::make('timeout')
                            ->label('Timeout (segundos)')
                            ->numeric()
                            ->default(5)
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Credenciales y Base DN')
                    ->description('Usuario con permisos para buscar en el directorio LDAP')
                    ->schema([
                        Forms\Components\TextInput::make('base_dn')
                            ->label('Base DN')
                            ->required()
                            ->default('dc=dataplus,dc=cu')
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('username')
                            ->label('Bind DN (Usuario Administrador / Servicio)')
                            ->helperText('Ej: uid=admin,ou=people,dc=dataplus,dc=cu')
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('password')
                            ->label('Contraseña de Bind')
                            ->password()
                            ->revealable()
                            ->helperText('Dejar en blanco para mantener la contraseña actual')
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Habilitar autenticación LDAP')
                            ->default(true)
                            ->helperText('Si se desactiva, solo se usará autenticación local'),
                    ]),
            ])
            ->statePath('data');
    }

    public function testConnection(): void
    {
        $data = $this->form->getState();

        try {
            $connection = new Connection([
                'hosts' => [$data['host']],
                'port' => (int) $data['port'],
                'base_dn' => $data['base_dn'],
                'username' => $data['username'],
                'password' => $data['password'],
                'use_tls' => (bool) ($data['ssl'] || $data['tls']),
                'timeout' => (int) ($data['timeout'] ?? 5),
            ]);

            $connection->connect();

            Notification::make()
                ->title('✅ Conexión LDAP exitosa')
                ->body("Conectado correctamente a {$data['host']}:{$data['port']}")
                ->success()
                ->send();

        } catch (\LdapRecord\Auth\BindException $e) {
            Notification::make()
                ->title('❌ Error de autenticación LDAP')
                ->body('Credenciales incorrectas: ' . $e->getMessage())
                ->danger()
                ->send();

        } catch (\LdapRecord\LdapRecordException $e) {
            Notification::make()
                ->title('❌ Error de conexión LDAP')
                ->body('No se pudo contactar el servidor: ' . $e->getMessage())
                ->danger()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('❌ Error inesperado')
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
            ->title('Configuración LDAP guardada exitosamente')
            ->body('Los cambios se aplicarán en la próxima autenticación.')
            ->success()
            ->send();
    }
}
