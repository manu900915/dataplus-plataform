<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClienteResource\Pages;
use App\Filament\Resources\ServicioResource;

use App\Helpers\CubanLocations;
use App\Models\Brigada;
use App\Models\Cliente;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ClienteResource extends Resource
{
    protected static ?string $model = Cliente::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Administración';

    protected static ?string $modelLabel = 'Cliente';

    protected static ?string $pluralModelLabel = 'Clientes';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('ClienteTabs')
                    ->tabs([
                        // ══════════════════════════════════════════════
                        // PESTAÑA 1: DATOS GENERALES DEL CLIENTE
                        // ══════════════════════════════════════════════
                        Forms\Components\Tabs\Tab::make('Datos del Cliente')
                            ->icon('heroicon-o-identification')
                            ->schema([
                                // ─── Identificación y Tipo de Persona ───
                                Forms\Components\Section::make('Identificación Fiscal y Registro')
                                    ->description('Parámetros de código, tipo de personería y documento de identidad.')
                                    ->columns(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('codigo')
                                            ->label('ID Cliente (Año - Número)')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(30)
                                            ->prefix('CLI-')
                                            ->placeholder('2026-44')
                                            ->default(function () {
                                                $year = now()->year;
                                                $maxId = Cliente::where('codigo', 'like', "CLI-{$year}-%")
                                                    ->get()
                                                    ->map(function ($c) use ($year) {
                                                        if (preg_match("/^CLI-{$year}-(\d+)$/", $c->codigo, $matches)) {
                                                            return (int) $matches[1];
                                                        }
                                                        return 0;
                                                    })
                                                    ->max();

                                                $next = ($maxId ?? 0) + 1;
                                                return "{$year}-{$next}";
                                            })
                                            ->afterStateHydrated(function (Forms\Components\TextInput $component, $state) {
                                                if (is_string($state) && str_starts_with($state, 'CLI-')) {
                                                    $component->state(substr($state, 4));
                                                }
                                            })
                                            ->dehydrateStateUsing(function ($state) {
                                                if (empty($state)) return $state;
                                                if (str_starts_with($state, 'CLI-')) {
                                                    return $state;
                                                }
                                                // Si solo escribió el número, le asignamos el año actual
                                                if (is_numeric($state)) {
                                                    $year = now()->year;
                                                    return "CLI-{$year}-{$state}";
                                                }
                                                return 'CLI-' . $state;
                                            })
                                            ->regex('/^[a-zA-Z0-9\-]+$/')
                                            ->validationMessages([
                                                'regex' => 'El código solo puede contener letras, números y guiones.',
                                            ])
                                            ->helperText('Puedes editar libremente el año y el ID (ej: 2026-44 o 2025-10).'),

                                        Forms\Components\Select::make('tipo_persona')
                                            ->label('Tipo de persona')
                                            ->options([
                                                'juridica' => '🏢 Persona Jurídica (Empresa / Negocio)',
                                                'natural'  => '👤 Persona Natural (Particular)',
                                            ])
                                            ->required()
                                            ->default('juridica')
                                            ->live(),

                                        Forms\Components\TextInput::make('documento')
                                            ->label(fn (Get $get) => $get('tipo_persona') === 'juridica' ? 'NIT' : 'Carnet de Identidad')
                                            ->placeholder(fn (Get $get) => $get('tipo_persona') === 'juridica' ? 'Ej: 50001234567' : 'Ej: 85010112345')
                                            ->helperText(fn (Get $get) => $get('tipo_persona') === 'juridica' ? 'Número de Identificación Tributaria (NIT)' : 'Carnet de Identidad personal')
                                            ->nullable()
                                            ->maxLength(25),
                                    ]),

                                // ─── Nombre / Razón Social ───
                                Forms\Components\Section::make('Denominación Comercial y Razón Social')
                                    ->columns(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('nombre')
                                            ->label(fn (Get $get) => $get('tipo_persona') === 'juridica' ? 'Razón Social / Denominación' : 'Nombre Completo')
                                            ->placeholder(fn (Get $get) => $get('tipo_persona') === 'juridica' ? 'Ej: Yoandry DluceSalon SRL' : 'Ej: Juan Pérez González')
                                            ->required()
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('nombre_comercial')
                                            ->label('Nombre comercial / Marca')
                                            ->placeholder('Ej: Dluce Salón')
                                            ->maxLength(255)
                                            ->visible(fn (Get $get) => $get('tipo_persona') === 'juridica'),
                                    ]),

                                // ─── Contacto Principal ───
                                Forms\Components\Section::make('Contacto Principal')
                                    ->columns(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('email')
                                            ->label('Correo electrónico')
                                            ->email()
                                            ->placeholder('contacto@empresa.cu')
                                            ->maxLength(255),

                                        Forms\Components\Fieldset::make('Teléfono principal')
                                            ->columns(2)
                                            ->schema([
                                                Forms\Components\Select::make('country_code')
                                                    ->label('Código país')
                                                    ->options([
                                                        '+53' => '🇨🇺 Cuba (+53)',
                                                        '+52' => '🇲🇽 México (+52)',
                                                        '+34' => '🇪🇸 España (+34)',
                                                        '+1'  => '🇺🇸 EE.UU./Canadá (+1)',
                                                        '+54' => '🇦🇷 Argentina (+54)',
                                                        '+56' => '🇨🇱 Chile (+56)',
                                                        '+57' => '🇨🇴 Colombia (+57)',
                                                        '+51' => '🇵🇪 Perú (+51)',
                                                        '+58' => '🇻🇪 Venezuela (+58)',
                                                    ])
                                                    ->default('+53')
                                                    ->searchable()
                                                    ->native(false),

                                                Forms\Components\TextInput::make('telefono')
                                                    ->label('Número')
                                                    ->tel()
                                                    ->placeholder('Ej: 52345678')
                                                    ->maxLength(20),
                                            ]),
                                    ]),

                                // ─── Contactos Adicionales ───
                                Forms\Components\Section::make('Contactos Adicionales')
                                    ->description('Personas de contacto secundarias, gerentes o administradores')
                                    ->collapsible()
                                    ->collapsed()
                                    ->schema([
                                        Forms\Components\Repeater::make('contactos')
                                            ->relationship('contactos')
                                            ->label(false)
                                            ->addActionLabel('➕ Agregar otro contacto')
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string =>
                                                ($state['nombre'] ?? 'Nuevo contacto') . ' - ' . ($state['responsabilidad'] ?? 'Sin responsabilidad')
                                            )
                                            ->schema([
                                                Forms\Components\Grid::make(3)
                                                    ->schema([
                                                        Forms\Components\TextInput::make('nombre')
                                                            ->label('Nombre del contacto')
                                                            ->required()
                                                            ->maxLength(255),

                                                        Forms\Components\TextInput::make('cargo')
                                                            ->label('Cargo')
                                                            ->placeholder('Ej: Administrador, Encargado...')
                                                            ->maxLength(255),

                                                        Forms\Components\TextInput::make('responsabilidad')
                                                            ->label('Responsabilidad')
                                                            ->placeholder('Ej: Pagos, Soporte...')
                                                            ->required()
                                                            ->maxLength(255),
                                                    ]),

                                                Forms\Components\Grid::make(2)
                                                    ->schema([
                                                        Forms\Components\TextInput::make('telefono')
                                                            ->label('Teléfono')
                                                            ->tel()
                                                            ->placeholder('Ej: +53 52345678')
                                                            ->maxLength(30),

                                                        Forms\Components\TextInput::make('email')
                                                            ->label('Correo electrónico')
                                                            ->email()
                                                            ->placeholder('persona@empresa.cu')
                                                            ->maxLength(255),
                                                    ]),
                                            ]),
                                    ]),

                                // ─── Notas y Estado ───
                                Forms\Components\Section::make('Información Adicional')
                                    ->columns(2)
                                    ->schema([
                                        Forms\Components\Textarea::make('notas')
                                            ->label('Notas generales del cliente')
                                            ->placeholder('Observaciones internas sobre este cliente...')
                                            ->rows(3),

                                        Forms\Components\Toggle::make('activo')
                                            ->label('Cliente activo en la plataforma')
                                            ->default(true)
                                            ->inline(false),
                                    ]),
                            ]),

                        // ══════════════════════════════════════════════
                        // PESTAÑA 2: LOCACIONES Y SERVICIOS ASOCIADOS
                        // ══════════════════════════════════════════════
                        Forms\Components\Tabs\Tab::make('Locaciones y Servicios')
                            ->icon('heroicon-o-map-pin')
                            ->badge(fn ($record) => $record ? $record->ubicaciones()->count() : null)
                            ->badgeColor('primary')
                            ->schema([
                                Forms\Components\Section::make('Locaciones del Cliente')
                                    ->description('Un cliente puede tener varias locaciones (casas o negocios), y cada una puede contar con múltiples servicios técnicos.')
                                    ->schema([
                                        Forms\Components\Repeater::make('ubicaciones')
                                            ->relationship('ubicaciones')
                                            ->label(false)
                                            ->addActionLabel('➕ Agregar Nueva Locación (Casa o Negocio)')
                                            ->collapsible()
                                            ->cloneable()
                                            ->itemLabel(fn (array $state): ?string =>
                                                (($state['tipo'] ?? '') === 'negocio' ? '🏢 Negocio: ' : '🏠 Casa: ') .
                                                ($state['nombre'] ?? 'Nueva Locación')
                                            )
                                            ->schema([
                                                // 1. Datos básicos de la locación
                                                Forms\Components\Grid::make(3)
                                                    ->schema([
                                                        Forms\Components\TextInput::make('nombre')
                                                            ->label('Nombre de la locación')
                                                            ->placeholder('Ej: Sede Principal, Casa Playa, Almacén...')
                                                            ->required(),

                                                        Forms\Components\Select::make('tipo')
                                                            ->label('Tipo de locación')
                                                            ->options([
                                                                'negocio'     => '🏢 Negocio / Local Comercial',
                                                                'residencial' => '🏠 Casa / Residencia Particular',
                                                            ])
                                                            ->required()
                                                            ->default('negocio')
                                                            ->live(),

                                                        Forms\Components\Select::make('tipo_negocio_id')
                                                            ->label('Giro del negocio')
                                                            ->relationship('tipoNegocio', 'nombre')
                                                            ->searchable()
                                                            ->preload()
                                                            ->placeholder('Seleccione giro...')
                                                            ->visible(fn (Get $get) => $get('tipo') === 'negocio'),
                                                    ]),

                                                // 2. Ubicación física
                                                Forms\Components\Grid::make(3)
                                                    ->schema([
                                                        Forms\Components\Select::make('provincia')
                                                            ->label('Provincia')
                                                            ->options(CubanLocations::provincias())
                                                            ->searchable()
                                                            ->live()
                                                            ->placeholder('Seleccione provincia...'),

                                                        Forms\Components\Select::make('municipio')
                                                            ->label('Municipio')
                                                            ->options(fn (Get $get): array =>
                                                                CubanLocations::municipios($get('provincia') ?? '')
                                                            )
                                                            ->searchable()
                                                            ->placeholder('Primero seleccione provincia')
                                                            ->disabled(fn (Get $get): bool => blank($get('provincia'))),

                                                        Forms\Components\TextInput::make('direccion')
                                                            ->label('Dirección completa')
                                                            ->placeholder('Calle, número, entre calles...')
                                                            ->maxLength(255),
                                                    ]),

                                                // 3. Contacto en sitio
                                                Forms\Components\Grid::make(2)
                                                    ->schema([
                                                        Forms\Components\TextInput::make('contacto_nombre')
                                                            ->label('Contacto en sitio')
                                                            ->placeholder('Nombre de quien atiende en el lugar'),

                                                        Forms\Components\TextInput::make('contacto_telefono')
                                                            ->label('Teléfono del sitio')
                                                            ->placeholder('Ej: 52345678')
                                                            ->tel(),
                                                    ]),

                                                Forms\Components\Textarea::make('notas')
                                                    ->label('Notas de la locación')
                                                    ->placeholder('Detalles de acceso, puntos de referencia...')
                                                    ->rows(2),

                                                // 4. SERVICIOS ASOCIADOS A ESTA LOCACIÓN ESPECÍFICA
                                                Forms\Components\Section::make('Servicios Técnicos en esta Locación')
                                                    ->description('Administre los sistemas (CCTV, SACI, Gestión Remota) instalados en esta locación.')
                                                    ->schema([
                                                        Forms\Components\Repeater::make('servicios')
                                                            ->relationship('servicios')
                                                            ->label(false)
                                                            ->addActionLabel('➕ Agregar Servicio a esta Locación')
                                                            ->collapsible()
                                                            ->itemLabel(fn (array $state): ?string =>
                                                                '🔧 ' . ($state['tipo'] ?? 'Servicio') . ' — Estado: ' . ($state['estado'] ?? 'Activo')
                                                            )
                                                            ->schema(ServicioResource::getTechnicalServiceSchema()),
                                                    ]),
                                            ]),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('codigo')
                    ->label('ID Cliente')
                    ->searchable()
                    ->sortable()
                    ->weight('font-bold')
                    ->color('primary')
                    ->copyable(),

                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre / Razón Social')
                    ->searchable()
                    ->sortable()
                    ->weight('font-medium'),

                Tables\Columns\TextColumn::make('documento')
                    ->label('NIT / CI')
                    ->searchable()
                    ->placeholder('—')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('tipo_persona')
                    ->label('Tipo Persona')
                    ->badge()
                    ->colors([
                        'info'    => 'natural',
                        'success' => 'juridica',
                    ])
                    ->formatStateUsing(fn (?string $state): string => $state === 'juridica' ? 'Jurídica' : 'Natural'),

                Tables\Columns\TextColumn::make('telefono')
                    ->label('Teléfono')
                    ->searchable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('negocios_count')
                    ->label('Negocios')
                    ->badge()
                    ->color('warning')
                    ->getStateUsing(function ($record): int {
                        return $record->ubicaciones()->where('tipo', 'negocio')->count();
                    }),

                Tables\Columns\TextColumn::make('residenciales_count')
                    ->label('Residencias')
                    ->badge()
                    ->color('info')
                    ->getStateUsing(function ($record): int {
                        return $record->ubicaciones()->where('tipo', 'residencial')->count();
                    }),

                Tables\Columns\TextColumn::make('ubicaciones_count')
                    ->label('Total Ubic.')
                    ->counts('ubicaciones')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\IconColumn::make('activo')
                    ->label('Activo')
                    ->boolean(),
            ])
            ->defaultSort('id', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('tipo_persona')
                    ->label('Tipo de persona')
                    ->options([
                        'juridica' => 'Jurídica',
                        'natural'  => 'Natural',
                    ]),
                Tables\Filters\TernaryFilter::make('activo')
                    ->label('Solo activos'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Editar'),
                Tables\Actions\DeleteAction::make()
                    ->label('Borrar'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListClientes::route('/'),
            'create' => Pages\CreateCliente::route('/create'),
            'edit'   => Pages\EditCliente::route('/{record}/edit'),
        ];
    }
}
