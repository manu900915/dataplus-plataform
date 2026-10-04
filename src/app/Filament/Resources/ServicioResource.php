<?php

namespace App\Filament\Resources;

use App\Models\Brigada;
use App\Models\Cliente;
use App\Models\ClienteUbicacion;
use App\Models\Servicio;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ServicioResource extends Resource
{
    protected static ?string $model = Servicio::class;

    protected static ?string $navigationIcon = 'heroicon-o-signal';

    protected static ?string $navigationGroup = 'Operaciones';

    protected static ?string $modelLabel = 'Servicio';

    protected static ?string $pluralModelLabel = 'Servicios';

    protected static ?int $navigationSort = 2;

    // Cache local por solicitud para evitar consultas N+1 en opciones
    protected static ?array $brigadasCache = null;
    protected static ?array $tecnicosCache = null;

    public static function getBrigadasOptions(): array
    {
        if (static::$brigadasCache === null) {
            static::$brigadasCache = Brigada::where('activa', true)
                ->orderBy('nombre')
                ->pluck('nombre', 'id')
                ->map(fn ($nombre) => '👥 ' . $nombre)
                ->toArray();
        }
        return static::$brigadasCache;
    }

    public static function getTecnicosOptions(): array
    {
        if (static::$tecnicosCache === null) {
            static::$tecnicosCache = User::where('activo', true)
                ->orderBy('name')
                ->pluck('name', 'id')
                ->map(fn ($nombre) => '👤 ' . $nombre)
                ->toArray();
        }
        return static::$tecnicosCache;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'ubicacion.cliente',
                'brigada',
                'tecnico',
            ]);
    }

    public static function form(Form $form): Form
    {
        $clienteId = request('cliente_id');
        $ubicacionId = request('ubicacion_id');

        return $form
            ->schema([
                // ─── Cliente y Ubicación ───
                Forms\Components\Section::make('Cliente y Ubicación Asociada')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('cliente_id')
                            ->label('Cliente')
                            ->searchable()
                            ->getSearchResultsUsing(fn (string $search): array =>
                                Cliente::where('activo', true)
                                    ->where(function ($q) use ($search) {
                                        $q->where('nombre', 'ilike', "%{$search}%")
                                          ->orWhere('codigo', 'ilike', "%{$search}%");
                                    })
                                    ->limit(30)
                                    ->pluck('nombre', 'id')
                                    ->toArray()
                            )
                            ->getOptionLabelUsing(fn ($value): ?string => Cliente::find($value)?->nombre)
                            ->live()
                            ->default($clienteId)
                            ->afterStateUpdated(fn ($set) => $set('cliente_ubicacion_id', null))
                            ->required()
                            ->dehydrated(false),

                        Forms\Components\Select::make('cliente_ubicacion_id')
                            ->label('Ubicación / Inmueble')
                            ->options(function ($get) {
                                $clienteId = $get('cliente_id');
                                if (!$clienteId) return [];
                                return ClienteUbicacion::where('cliente_id', $clienteId)
                                    ->where('activo', true)
                                    ->get()
                                    ->mapWithKeys(fn ($u) => [$u->id => "{$u->nombre} (" . ($u->tipo === 'negocio' ? 'Negocio' : 'Casa') . ")"])
                                    ->toArray();
                            })
                            ->searchable()
                            ->required()
                            ->default($ubicacionId),
                    ]),

                // ─── Esquema Técnico Completo del Servicio ───
                ...static::getTechnicalServiceSchema(),
            ]);
    }

    /**
     * Retorna el esquema de campos técnicos para un servicio.
     * Reutilizado tanto aquí como dentro del repetidor de locaciones en ClienteResource.
     */
    public static function getTechnicalServiceSchema(): array
    {
        return [
            // ─── 1. Parámetros Principales del Servicio ───
            Forms\Components\Section::make('Parámetros Principales')
                ->columns(3)
                ->schema([
                    Forms\Components\Select::make('tipo')
                        ->label('Tipo de Servicio')
                        ->options([
                            'CCTV'           => '📹 CCTV (Videovigilancia)',
                            'SACI'           => '🚨 SACI (Alarma contra intrusión)',
                            'Gestion_Remota' => '📡 Gestión Remota / Redes',
                        ])
                        ->required()
                        ->live(),

                    Forms\Components\Select::make('estado')
                        ->label('Estado Operativo')
                        ->options([
                            'Activo'        => '🟢 Activo',
                            'Inactivo'      => '⚪ Inactivo',
                            'En_Reparacion' => '🟡 En Reparación',
                            'Suspendido'    => '🔴 Suspendido',
                        ])
                        ->default('Activo')
                        ->required(),

                    Forms\Components\DatePicker::make('fecha_instalacion')
                        ->label('Fecha de Instalación')
                        ->native(false),
                ]),

            // ─── 2. Responsables de la Instalación ───
            Forms\Components\Section::make('Responsables de Instalación y Soporte')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('brigada_id')
                        ->label('Brigada que hizo la instalación')
                        ->options(fn () => static::getBrigadasOptions())
                        ->searchable()
                        ->preload()
                        ->placeholder('Seleccione una brigada...'),

                    Forms\Components\Select::make('tecnico_id')
                        ->label('Técnico / Especialista responsable')
                        ->options(fn () => static::getTecnicosOptions())
                        ->searchable()
                        ->preload()
                        ->placeholder('Seleccione un especialista...'),
                ]),

            // ─── 3. ESPECÍFICO CCTV ───
            Forms\Components\Section::make('Detalles Técnicos de CCTV')
                ->description('Configuración de videograbadores, cámaras y parámetros de conectividad.')
                ->visible(fn (Get $get) => $get('tipo') === 'CCTV')
                ->schema([
                    Forms\Components\Select::make('cctv_solucion')
                        ->label('Solución CCTV')
                        ->options([
                            'DVR'  => '📼 DVR (Grabador Analógico / TVI)',
                            'NVR'  => '🖥️ NVR (Grabador de Red / Cámaras IP)',
                            'Wifi' => '📶 Wifi (Cámaras Inalámbricas Autónomas)',
                        ])
                        ->required(fn (Get $get) => $get('tipo') === 'CCTV')
                        ->live()
                        ->placeholder('Seleccione la solución de CCTV...'),

                    // DVR / NVR: Canales, Marca, Modelo
                    Forms\Components\Grid::make(3)
                        ->visible(fn (Get $get) => in_array($get('cctv_solucion'), ['DVR', 'NVR']))
                        ->schema([
                            Forms\Components\TextInput::make('cctv_canales')
                                ->label('Cantidad de Canales')
                                ->numeric()
                                ->minValue(1)
                                ->placeholder('Ej: 4, 8, 16, 32'),

                            Forms\Components\TextInput::make('cctv_marca')
                                ->label('Marca')
                                ->placeholder('Ej: Hikvision, Dahua, Hilook...'),

                            Forms\Components\TextInput::make('cctv_modelo')
                                ->label('Modelo')
                                ->placeholder('Ej: DS-7208HUHI-K2, NVR2108...'),
                        ]),

                    // DVR / NVR: Credenciales
                    Forms\Components\Grid::make(2)
                        ->visible(fn (Get $get) => in_array($get('cctv_solucion'), ['DVR', 'NVR']))
                        ->schema([
                            Forms\Components\TextInput::make('cctv_usuario')
                                ->label('Usuario del equipo')
                                ->placeholder('Ej: admin'),

                            Forms\Components\TextInput::make('cctv_password')
                                ->label('Contraseña del equipo')
                                ->password()
                                ->revealable()
                                ->placeholder('Contraseña del grabador'),
                        ]),

                    // DVR / NVR: Conexión, IP, App
                    Forms\Components\Grid::make(3)
                        ->visible(fn (Get $get) => in_array($get('cctv_solucion'), ['DVR', 'NVR']))
                        ->schema([
                            Forms\Components\Select::make('cctv_tipo_conexion')
                                ->label('Tipo de Conexión')
                                ->options([
                                    'Local' => '🌐 Local (Red LAN/IP fija)',
                                    'P2P'   => '☁️ P2P (Cloud / Nube del fabricante)',
                                ])
                                ->placeholder('Seleccione tipo...'),

                            Forms\Components\TextInput::make('cctv_ip')
                                ->label('Dirección IP')
                                ->placeholder('Ej: 192.168.1.100'),

                            Forms\Components\TextInput::make('cctv_app')
                                ->label('Aplicación')
                                ->placeholder('Ej: Hik-Connect, DMSS, XMeye...'),
                        ]),

                    // Wifi: Marca, Modelo
                    Forms\Components\Grid::make(2)
                        ->visible(fn (Get $get) => $get('cctv_solucion') === 'Wifi')
                        ->schema([
                            Forms\Components\TextInput::make('cctv_marca')
                                ->label('Marca')
                                ->placeholder('Ej: Ezviz, Tuya, ICSee, Tapo'),

                            Forms\Components\TextInput::make('cctv_modelo')
                                ->label('Modelo')
                                ->placeholder('Ej: C6N, Ranger 2, C3W'),
                        ]),

                    // Wifi: SSID, Password, App
                    Forms\Components\Grid::make(3)
                        ->visible(fn (Get $get) => $get('cctv_solucion') === 'Wifi')
                        ->schema([
                            Forms\Components\TextInput::make('cctv_ssid')
                                ->label('SSID (Red Wi-Fi)')
                                ->placeholder('Nombre de la red Wi-Fi'),

                            Forms\Components\TextInput::make('cctv_password')
                                ->label('Contraseña (Wi-Fi / Equipo)')
                                ->password()
                                ->revealable()
                                ->placeholder('Clave de la red o cámara'),

                            Forms\Components\TextInput::make('cctv_app')
                                ->label('Aplicación')
                                ->placeholder('Ej: Ezviz, Smart Life, Tuya, Tapo'),
                        ]),
                ]),

            // ─── 4. ESPECÍFICO SACI ───
            Forms\Components\Section::make('Detalles Técnicos de Alarma (SACI)')
                ->description('Configuración de centrales de alarma, zonas, sensores y conectividad.')
                ->visible(fn (Get $get) => $get('tipo') === 'SACI')
                ->schema([
                    Forms\Components\Select::make('saci_solucion')
                        ->label('Solución de Alarma')
                        ->options([
                            'Cableada'    => '🔌 Cableada (Zonas convencionales)',
                            'Inalambrica' => '📡 Inalámbrica (Hub / Panel inalámbrico)',
                        ])
                        ->required(fn (Get $get) => $get('tipo') === 'SACI')
                        ->live()
                        ->placeholder('Seleccione solución cableada o inalámbrica...'),

                    // Marca, Modelo y Código de instalador (común a ambas)
                    Forms\Components\Grid::make(3)
                        ->visible(fn (Get $get) => !empty($get('saci_solucion')))
                        ->schema([
                            Forms\Components\TextInput::make('saci_marca')
                                ->label('Marca')
                                ->placeholder('Ej: DSC, Paradox, Honeywell, Ajax'),

                            Forms\Components\TextInput::make('saci_modelo')
                                ->label('Modelo')
                                ->placeholder('Ej: PC1832, SP4000, Vista 48, Hub 2 Plus'),

                            Forms\Components\TextInput::make('saci_codigo_instalador')
                                ->label('Código de Instalador')
                                ->password()
                                ->revealable()
                                ->placeholder('Ej: 5555, 0000, 1234'),
                        ]),

                    // Cantidad de sensores (PIR, magnéticos y perimetrales)
                    Forms\Components\Fieldset::make('Cantidad de Sensores')
                        ->visible(fn (Get $get) => !empty($get('saci_solucion')))
                        ->columns(3)
                        ->schema([
                            Forms\Components\TextInput::make('saci_sensores_pir')
                                ->label('Sensores PIR (Movimiento)')
                                ->numeric()
                                ->default(0)
                                ->minValue(0)
                                ->prefix('🚶'),

                            Forms\Components\TextInput::make('saci_sensores_magneticos')
                                ->label('Sensores Magnéticos (Puertas/Ventanas)')
                                ->numeric()
                                ->default(0)
                                ->minValue(0)
                                ->prefix('🚪'),

                            Forms\Components\TextInput::make('saci_sensores_perimetrales')
                                ->label('Sensores Perimetrales (Barreras)')
                                ->numeric()
                                ->default(0)
                                ->minValue(0)
                                ->prefix('🛡️'),
                        ]),

                    // Si es Cableada: Tarjeta de conexión y credenciales
                    Forms\Components\Grid::make(2)
                        ->visible(fn (Get $get) => $get('saci_solucion') === 'Cableada')
                        ->schema([
                            Forms\Components\Toggle::make('saci_tarjeta_conexion')
                                ->label('¿Tiene Tarjeta de Conexión? (IP / Comunicador)')
                                ->inline(false)
                                ->live(),

                            Forms\Components\TextInput::make('saci_tarjeta_usuario_password')
                                ->label('Usuario / Contraseña de Tarjeta')
                                ->placeholder('Ej: admin / 1234')
                                ->visible(fn (Get $get) => (bool) $get('saci_tarjeta_conexion')),
                        ]),

                    // Si es Inalámbrica: SSID / Password
                    Forms\Components\Grid::make(2)
                        ->visible(fn (Get $get) => $get('saci_solucion') === 'Inalambrica')
                        ->schema([
                            Forms\Components\TextInput::make('saci_ssid_password')
                                ->label('SSID / Contraseña Wi-Fi')
                                ->placeholder('Ej: MiRedCasa / Clave1234'),
                        ]),

                    // Red y Aplicación para SACI
                    Forms\Components\Grid::make(3)
                        ->visible(fn (Get $get) => !empty($get('saci_solucion')))
                        ->schema([
                            Forms\Components\TextInput::make('saci_ip')
                                ->label('Dirección IP')
                                ->placeholder('Ej: 192.168.1.80'),

                            Forms\Components\TextInput::make('saci_app')
                                ->label('Aplicación')
                                ->placeholder('Ej: Connect Alarm, DLS5, Ajax Security, Tuya'),

                            Forms\Components\TextInput::make('saci_app_usuario_password')
                                ->label('Usuario / Password de la Aplicación')
                                ->placeholder('Ej: cuenta@email.com / pass'),
                        ]),
                ]),

            // ─── 5. ESPECÍFICO GESTIÓN REMOTA (Intacto) ───
            Forms\Components\Section::make('Datos de Gestión Remota')
                ->visible(fn (Get $get) => $get('tipo') === 'Gestion_Remota')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('gr_tipo_solucion')
                        ->label('Tipo de solución')
                        ->options([
                            'Router4g'     => 'Router 4G',
                            'Router+Modem' => 'Router + Módem',
                            'Router+ADSL'  => 'Router + ADSL',
                            'Otros'        => 'Otros',
                        ])
                        ->live()
                        ->native(false),

                    Forms\Components\TextInput::make('gr_sim_numero')
                        ->label('Número SIM card')
                        ->tel()
                        ->visible(fn (Get $get) => in_array($get('gr_tipo_solucion'), ['Router4g', 'Router+Modem'])),

                    Forms\Components\TextInput::make('gr_marca_modelo')
                        ->label('Marca / Modelo')
                        ->placeholder('Ej: TP-Link TL-MR6400')
                        ->columnSpanFull(),

                    Forms\Components\Select::make('gr_tipo_internet')
                        ->label('Tipo de internet')
                        ->options([
                            'Abierto'  => 'Abierto',
                            'Filtrado' => 'Filtrado',
                            'Cerrado'  => 'Cerrado',
                        ])
                        ->native(false),

                    Forms\Components\Select::make('gr_recarga_por')
                        ->label('¿Quién recarga?')
                        ->options([
                            'Nosotros' => 'Nosotros',
                            'Cliente'  => 'El cliente',
                        ])
                        ->default('Cliente'),

                    Forms\Components\TextInput::make('gr_recarga_monto')
                        ->label('Monto (CUP)')
                        ->numeric()
                        ->default(360)
                        ->prefix('$'),

                    Forms\Components\DatePicker::make('gr_ultima_recarga')
                        ->label('Última recarga')
                        ->native(false),
                ]),

            // ─── 6. Documentos de Referencia (documentos o imágenes) ───
            Forms\Components\Section::make('Documentos de Referencia')
                ->description('Adjunte diagramas de conexión, fotos de instalación, planos o actas de entrega.')
                ->schema([
                    Forms\Components\FileUpload::make('documentos')
                        ->label('Documentos o Imágenes de Referencia')
                        ->multiple()
                        ->reorderable()
                        ->openable()
                        ->downloadable()
                        ->disk('public')
                        ->directory('servicios-documentos')
                        ->maxSize(15360) // 15MB
                        ->acceptedFileTypes([
                            'application/pdf',
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                            'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                        ]),
                ]),

            // ─── 7. Observaciones ───
            Forms\Components\Section::make('Observaciones')
                ->schema([
                    Forms\Components\Textarea::make('notas')
                        ->label('Observaciones Técnicas')
                        ->placeholder('Detalles de instalación, recomendaciones o notas operativas...')
                        ->rows(2),
                ]),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ubicacion.cliente.nombre')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('ubicacion.nombre')
                    ->label('Ubicación')
                    ->formatStateUsing(fn ($state, $record) => "{$record->ubicacion?->nombre} (" . ($record->ubicacion?->tipo === 'negocio' ? 'Negocio' : 'Casa') . ")")
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('tipo')
                    ->colors([
                        'primary' => 'CCTV',
                        'danger'  => 'SACI',
                        'warning' => 'Gestion_Remota',
                    ]),

                Tables\Columns\TextColumn::make('solucion_detalle')
                    ->label('Solución / Equipo')
                    ->getStateUsing(function ($record): string {
                        if ($record->tipo === 'CCTV') {
                            $sol = $record->cctv_solucion ?? 'CCTV';
                            $can = $record->cctv_canales ? " ({$record->cctv_canales} ch)" : '';
                            return "{$sol}{$can}";
                        }
                        if ($record->tipo === 'SACI') {
                            return "SACI " . ($record->saci_solucion ?? '');
                        }
                        return $record->gr_tipo_solucion ?? 'Gestión Remota';
                    })
                    ->badge()
                    ->color('gray'),

                Tables\Columns\BadgeColumn::make('estado')
                    ->colors([
                        'success' => 'Activo',
                        'danger'  => 'Inactivo',
                        'warning' => 'En_Reparacion',
                        'gray'    => 'Suspendido',
                    ]),

                Tables\Columns\TextColumn::make('fecha_instalacion')
                    ->label('Instalado')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('responsable')
                    ->label('Brigada / Especialista')
                    ->getStateUsing(function ($record): string {
                        if ($record->brigada) return "👥 {$record->brigada->nombre}";
                        if ($record->tecnico) return "👤 {$record->tecnico->name}";
                        return '—';
                    })
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('tipo')
                    ->options([
                        'CCTV'           => 'CCTV',
                        'SACI'           => 'SACI',
                        'Gestion_Remota' => 'Gestión Remota',
                    ]),
                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'Activo'        => 'Activo',
                        'Inactivo'      => 'Inactivo',
                        'En_Reparacion' => 'En Reparación',
                        'Suspendido'    => 'Suspendido',
                    ]),
                Tables\Filters\SelectFilter::make('cliente_id')
                    ->label('Cliente')
                    ->relationship('ubicacion.cliente', 'nombre')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => \App\Filament\Resources\ServicioResource\Pages\ListServicios::route('/'),
            'create' => \App\Filament\Resources\ServicioResource\Pages\CreateServicio::route('/create'),
            'edit'   => \App\Filament\Resources\ServicioResource\Pages\EditServicio::route('/{record}/edit'),
        ];
    }
}
