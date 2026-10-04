<?php

namespace App\Filament\Resources;

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
use App\Filament\Resources\ClienteResource\RelationManagers;

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
                // ─── Identificación ───
                Forms\Components\Section::make('Identificación')
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('codigo')
                            ->label('ID Cliente')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(25)
                            ->prefix('CLI-')
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
                                if (is_string($state) && !str_starts_with($state, 'CLI-')) {
                                    return 'CLI-' . $state;
                                }
                                return $state;
                            })
                            ->regex('/^[a-zA-Z0-9\-]+$/')
                            ->validationMessages([
                                'regex' => 'El ID solo puede contener letras, números y guiones.',
                            ])
                            ->helperText('Formato automático CLI-año-ID (ej: CLI-2026-247).'),

                        Forms\Components\Select::make('tipo_persona')
                            ->label('Tipo de persona')
                            ->options([
                                'natural' => 'Natural',
                                'juridica' => 'Jurídica',
                            ])
                            ->required()
                            ->default('natural')
                            ->live(),

                        Forms\Components\TextInput::make('documento')
                            ->label(fn (Get $get) => $get('tipo_persona') === 'juridica' ? 'RUC' : 'Carnet de Identidad')
                            ->required()
                            ->maxLength(20),
                    ]),

                // ─── Nombre / Razón Social ───
                Forms\Components\Section::make('Nombre / Razón Social')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('nombre')
                            ->label('Nombre completo / Razón Social')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('nombre_comercial')
                            ->label('Nombre comercial')
                            ->placeholder('Si aplica')
                            ->maxLength(255)
                            ->visible(fn (Get $get) => $get('tipo_persona') === 'juridica'),
                    ]),

                // ─── Contacto Principal ───
                Forms\Components\Section::make('Contacto principal')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->label('Correo electrónico')
                            ->email()
                            ->maxLength(255),

                        Forms\Components\Fieldset::make('Teléfono principal')
                            ->columns(2)
                            ->schema([
                                Forms\Components\Select::make('country_code')
                                    ->label('Código país')
                                    ->options([
                                        '+53' => '🇨🇺 Cuba (+53)',
                                        '+52' => '🇲🇽 México (+52)',
                                        '+34' => '🇪 España (+34)',
                                        '+1'  => '🇺🇸 EE.UU./Canadá (+1)',
                                        '+54' => '🇦🇷 Argentina (+54)',
                                        '+56' => '🇨 Chile (+56)',
                                        '+57' => '🇨🇴 Colombia (+57)',
                                        '+51' => '🇵🇪 Perú (+51)',
                                        '+58' => '🇻🇪 Venezuela (+58)',
                                    ])
                                    ->default('+53')
                                    ->required()
                                    ->searchable()
                                    ->native(false),

                                Forms\Components\TextInput::make('telefono')
                                    ->label('Número')
                                    ->tel()
                                    ->maxLength(20)
                                    ->required(),
                            ]),
                    ]),

                // ─── Contactos Adicionales ───
                Forms\Components\Section::make('Contactos adicionales')
                    ->description('Agregue personas de contacto con sus responsabilidades')
                    ->schema([
                        Forms\Components\Repeater::make('contactos')
                            ->relationship('contactos')
                            ->label(false)
                            ->addActionLabel('Agregar contacto')
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string =>
                                ($state['nombre'] ?? 'Nuevo contacto') . ' - ' . ($state['responsabilidad'] ?? 'Sin responsabilidad')
                            )
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('nombre')
                                            ->label('Nombre del contacto')
                                            ->required()
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('cargo')
                                            ->label('Cargo')
                                            ->maxLength(255),
                                    ]),

                                Forms\Components\TextInput::make('responsabilidad')
                                    ->label('Responsabilidad')
                                    ->placeholder('Ej: Responsable de compras, Contacto técnico, Facturación...')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Select::make('country_code')
                                            ->label('Código país')
                                            ->options([
                                                '+53' => '🇨 Cuba (+53)',
                                                '+52' => '🇲🇽 México (+52)',
                                                '+34' => '🇸 España (+34)',
                                                '+1'  => '🇺🇸 EE.UU./Canadá (+1)',
                                                '+54' => '🇦🇷 Argentina (+54)',
                                                '+56' => '🇨🇱 Chile (+56)',
                                                '+57' => '🇨 Colombia (+57)',
                                                '+51' => '🇵🇪 Perú (+51)',
                                                '+58' => '🇻🇪 Venezuela (+58)',
                                            ])
                                            ->default('+53')
                                            ->searchable()
                                            ->native(false),

                                        Forms\Components\TextInput::make('telefono')
                                            ->label('Teléfono')
                                            ->tel()
                                            ->maxLength(20),
                                    ]),

                                Forms\Components\TextInput::make('email')
                                    ->label('Correo electrónico')
                                    ->email()
                                    ->maxLength(255),
                            ]),
                    ]),

                // ─── Ubicaciones con Servicios Anidados ───
                Forms\Components\Section::make('Ubicaciones y Servicios')
                    ->description('Agregue ubicaciones y los servicios asociados a cada una.')
                    ->schema([
                        Forms\Components\Repeater::make('ubicaciones')
                            ->relationship('ubicaciones')
                            ->label(false)
                            ->addActionLabel('Agregar ubicación')
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string =>
                                ($state['nombre'] ?? 'Nueva ubicación') . ' (' . ($state['tipo'] ?? '-') . ')'
                            )
                            ->schema([
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('nombre')
                                            ->label('Nombre de la ubicación')
                                            ->placeholder('Ej: Casa principal, Sucursal Centro, Local 5...')
                                            ->required(),

                                        Forms\Components\Select::make('tipo')
                                            ->label('Tipo')
                                            ->options([
                                                'residencial' => 'Residencial',
                                                'negocio' => 'Negocio',
                                            ])
                                            ->required()
                                            ->default('residencial')
                                            ->live(),

                                        Forms\Components\Select::make('tipo_negocio_id')
                                            ->label('Giro del negocio')
                                            ->relationship('tipoNegocio', 'nombre')
                                            ->searchable()
                                            ->preload()
                                            ->placeholder('Seleccione...')
                                            ->visible(fn (Get $get) => $get('tipo') === 'negocio')
                                            ->required(fn (Get $get) => $get('tipo') === 'negocio'),
                                    ]),

                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Select::make('provincia')
                                            ->label('Provincia')
                                            ->options(CubanLocations::provincias())
                                            ->searchable()
                                            ->live()
                                            ->placeholder('Seleccione...'),

                                        Forms\Components\Select::make('municipio')
                                            ->label('Municipio')
                                            ->options(fn (Get $get): array =>
                                                CubanLocations::municipios($get('provincia') ?? '')
                                            )
                                            ->searchable()
                                            ->placeholder('Primero seleccione provincia')
                                            ->disabled(fn (Get $get): bool => blank($get('provincia'))),
                                    ]),

                                Forms\Components\TextInput::make('direccion')
                                    ->label('Dirección completa')
                                    ->maxLength(255),

                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('contacto_nombre')
                                            ->label('Contacto en sitio')
                                            ->placeholder('Nombre de quien está en el lugar'),

                                        Forms\Components\TextInput::make('contacto_telefono')
                                            ->label('Teléfono del sitio')
                                            ->tel(),
                                    ]),

                                Forms\Components\Textarea::make('notas')
                                    ->label('Notas de la ubicación')
                                    ->rows(2),

                                // 🔥 Servicios asociados a esta ubicación
                                Forms\Components\Section::make('Servicios asociados a esta ubicación')
                                    ->description('Agregue o quite servicios de esta ubicación específica.')
                                    ->schema([
                                        Forms\Components\Repeater::make('servicios')
                                            ->relationship('servicios')
                                            ->label(false)
                                            ->addActionLabel('Agregar servicio')
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string =>
                                                ($state['tipo'] ?? 'Servicio') . ' - ' . ($state['estado'] ?? 'Pendiente')
                                            )
                                            ->schema([
                                                Forms\Components\Grid::make(3)
                                                    ->schema([
                                                        Forms\Components\Select::make('tipo')
                                                            ->label('Tipo de servicio')
                                                            ->options([
                                                                'CCTV' => 'CCTV',
                                                                'SACI' => 'SACI',
                                                                'Gestion_Remota' => 'Gestión Remota',
                                                            ])
                                                            ->required()
                                                            ->live(),

                                                        Forms\Components\Select::make('estado')
                                                            ->label('Estado')
                                                            ->options([
                                                                'Activo' => 'Activo',
                                                                'Inactivo' => 'Inactivo',
                                                                'En_Reparacion' => 'En Reparación',
                                                                'Suspendido' => 'Suspendido',
                                                            ])
                                                            ->default('Activo')
                                                            ->required(),

                                                        Forms\Components\DatePicker::make('fecha_instalacion')
                                                            ->label('Fecha instalación')
                                                            ->native(false),
                                                    ]),

                                                // Brigada y Técnico (siempre visibles para todos los tipos de servicio)
                                                Forms\Components\Grid::make(2)
                                                    ->schema([
                                                        Forms\Components\Select::make('brigada_id')
                                                            ->label('Brigada')
                                                            ->options(fn () =>
                                                                Brigada::where('activa', true)
                                                                    ->orderBy('nombre')
                                                                    ->pluck('nombre', 'id')
                                                                    ->map(fn ($nombre) => '👥 ' . $nombre)
                                                                    ->toArray()
                                                            )
                                                            ->searchable()
                                                            ->preload()
                                                            ->placeholder('Seleccione una brigada...')
                                                            ->createOptionForm([
                                                                Forms\Components\TextInput::make('nombre')
                                                                    ->label('Nombre de la brigada')
                                                                    ->required()
                                                                    ->unique(),
                                                                Forms\Components\Select::make('jefe_id')
                                                                    ->label('Jefe de brigada')
                                                                    ->relationship('jefe', 'name')
                                                                    ->required(),
                                                                Forms\Components\Textarea::make('notas')
                                                                    ->label('Notas')
                                                                    ->rows(2),
                                                            ])
                                                            ->createOptionAction(fn (Forms\Components\Actions\Action $action) =>
                                                                $action->label('Crear nueva brigada')
                                                            ),

                                                        Forms\Components\Select::make('tecnico_id')
                                                            ->label('Técnico responsable')
                                                            ->options(fn () =>
                                                                User::role('Técnico')
                                                                    ->orderBy('name')
                                                                    ->pluck('name', 'id')
                                                                    ->map(fn ($nombre) => '👤 ' . $nombre)
                                                                    ->toArray()
                                                            )
                                                            ->searchable()
                                                            ->preload()
                                                            ->placeholder('Seleccione un técnico...'),
                                                    ]),

                                                // Campos específicos de Gestión Remota
                                                Forms\Components\Section::make('Detalles de Gestión Remota')
                                                    ->schema([
                                                        Forms\Components\Grid::make(2)
                                                            ->schema([
                                                                Forms\Components\Select::make('gr_tipo_solucion')
                                                                    ->label('Tipo de solución')
                                                                    ->options([
                                                                        'Router4g' => 'Router 4G',
                                                                        'Router+Modem' => 'Router + Modem',
                                                                        'Router+ADSL' => 'Router + ADSL',
                                                                        'Otros' => 'Otros',
                                                                    ])
                                                                    ->required()
                                                                    ->live()
                                                                    ->placeholder('Seleccione el tipo de solución...'),

                                                                Forms\Components\TextInput::make('gr_marca_modelo')
                                                                    ->label('Marca/Modelo')
                                                                    ->placeholder('Ej: Huawei B535, TP-Link Archer...'),
                                                            ]),

                                                        // SIM: solo visible si NO es Router+ADSL
                                                        Forms\Components\Grid::make(2)
                                                            ->schema([
                                                                Forms\Components\TextInput::make('gr_sim_numero')
                                                                    ->label('Número SIM')
                                                                    ->maxLength(20)
                                                                    ->placeholder('Ej: 05 1234 5678')
                                                                    ->visible(fn (Get $get) => $get('gr_tipo_solucion') !== 'Router+ADSL'),

                                                                Forms\Components\Select::make('gr_sim_tipo')
                                                                    ->label('Tipo de SIM')
                                                                    ->options([
                                                                        'Particular' => 'Particular (recarga por nuestra parte)',
                                                                        'Corporativa' => 'Corporativa (contrato ETECSA del cliente)',
                                                                    ])
                                                                    ->live()
                                                                    ->placeholder('Seleccione el tipo de SIM...')
                                                                    ->visible(fn (Get $get) =>
                                                                        in_array($get('gr_tipo_solucion'), ['Router4g', 'Router+Modem'])
                                                                    ),
                                                            ]),

                                                        Forms\Components\Select::make('gr_tipo_internet')
                                                            ->label('Tipo de internet')
                                                            ->options([
                                                                'Abierto' => 'Abierto',
                                                                'Filtrado' => 'Filtrado',
                                                                'Cerrado' => 'Cerrado',
                                                            ])
                                                            ->placeholder('Seleccione el tipo de internet...')
                                                            ->visible(fn (Get $get) => $get('gr_tipo_solucion') !== 'Router+ADSL'),

                                                        // Recarga: solo visible si SIM es Particular
                                                        Forms\Components\Grid::make(2)
                                                            ->schema([
                                                                Forms\Components\Select::make('gr_recarga_por')
                                                                    ->label('Recarga por')
                                                                    ->options([
                                                                        'Nosotros' => 'Nosotros',
                                                                        'Cliente' => 'Cliente',
                                                                    ])
                                                                    ->default('Cliente')
                                                                    ->placeholder('Seleccione quién recarga...')
                                                                    ->visible(fn (Get $get) => $get('gr_sim_tipo') === 'Particular'),

                                                                Forms\Components\TextInput::make('gr_recarga_monto')
                                                                    ->label('Monto recarga')
                                                                    ->numeric()
                                                                    ->default(360.00)
                                                                    ->prefix('$')
                                                                    ->visible(fn (Get $get) => $get('gr_sim_tipo') === 'Particular'),
                                                            ]),
                                                    ])
                                                    ->visible(fn (Get $get) => $get('tipo') === 'Gestion_Remota'),

                                                Forms\Components\Textarea::make('notas')
                                                    ->label('Notas del servicio')
                                                    ->rows(2),
                                            ]),
                                    ]),
                            ]),
                    ]),

                // ─── Notas generales ───
                Forms\Components\Section::make('Información adicional')
                    ->schema([
                        Forms\Components\Textarea::make('notas')
                            ->label('Notas generales del cliente')
                            ->rows(3),

                        Forms\Components\Toggle::make('activo')
                            ->label('Cliente activo')
                            ->default(true),
                    ]),
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
                    ->copyable(),

                Tables\Columns\TextColumn::make('nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('tipo_persona')
                    ->colors([
                        'primary' => 'natural',
                        'success' => 'juridica',
                    ])
                    ->formatStateUsing(fn (string $state): string => $state === 'juridica' ? 'Jurídica' : 'Natural'),

                Tables\Columns\TextColumn::make('telefono')
                    ->searchable(),

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
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipo_persona')
                    ->options([
                        'natural' => 'Natural',
                        'juridica' => 'Jurídica',
                    ]),

                Tables\Filters\TernaryFilter::make('activo')
                    ->label('Activo'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            // Si usas el Repeater de ubicaciones en el form, es mejor no usar también el RelationManager
            // RelationManagers\UbicacionesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\ClienteResource\Pages\ListClientes::route('/'),
            'create' => \App\Filament\Resources\ClienteResource\Pages\CreateCliente::route('/create'),
            'edit' => \App\Filament\Resources\ClienteResource\Pages\EditCliente::route('/{record}/edit'),
        ];
    }
}