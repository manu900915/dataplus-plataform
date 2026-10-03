<?php

namespace App\Filament\Resources;

use App\Models\Cliente;
use App\Models\ClienteUbicacion;
use App\Models\Servicio;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ServicioResource extends Resource
{
    protected static ?string $model = Servicio::class;
    protected static ?string $navigationIcon = 'heroicon-o-signal';
    protected static ?string $navigationGroup = 'Operaciones';
    protected static ?string $modelLabel = 'Servicio';
    protected static ?string $pluralModelLabel = 'Servicios';
    protected static ?int $navigationSort = 2;

        public static function form(Form $form): Form
    {
        // Detectar si venimos de un cliente específico
        $clienteId = request('cliente_id');
        $ubicacionId = request('ubicacion_id');

        return $form
            ->schema([
                // ─── Cliente y Ubicación ───
                Forms\Components\Section::make('Cliente y Ubicación')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('cliente_id')
                            ->label('Cliente')
                            ->options(\App\Models\Cliente::where('activo', true)->pluck('nombre', 'id'))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->default($clienteId)
                            ->afterStateUpdated(fn ($set) => $set('cliente_ubicacion_id', null))
                            ->required()
                            ->dehydrated(false),

                        Forms\Components\Select::make('cliente_ubicacion_id')
                            ->label('Ubicación / Negocio')
                            ->options(function ($get) {
                                $clienteId = $get('cliente_id');
                                if (!$clienteId) return [];
                                return \App\Models\ClienteUbicacion::where('cliente_id', $clienteId)
                                    ->where('activo', true)
                                    ->get()
                                    ->mapWithKeys(fn ($u) => [$u->id => "{$u->nombre} ({$u->tipo})"])
                                    ->toArray();
                            })
                            ->searchable()
                            ->required()
                            ->default($ubicacionId)
                            ->placeholder('Primero seleccione un cliente')
                            ->disabled(fn ($get) => blank($get('cliente_id'))),
                    ]),

                // ─── Datos del Servicio ───
                Forms\Components\Section::make('Datos del Servicio')
                    ->columns(3)
                    ->schema([
                        Forms\Components\Select::make('tipo')
                            ->label('Tipo de servicio')
                            ->options([
                                'CCTV' => 'CCTV',
                                'SACI' => 'SACI',
                                'Gestion_Remota' => 'Gestión Remota',
                            ])
                            ->required()
                            ->live()
                            ->native(false),

                        Forms\Components\DatePicker::make('fecha_instalacion')
                            ->label('Fecha de instalación')
                            ->native(false),

                        Forms\Components\Select::make('brigada_id')
                            ->label('Brigada')
                            ->relationship('brigada', 'nombre', fn ($query) => $query->where('activa', true))
                            ->searchable()
                            ->preload()
                            ->placeholder('Seleccionar brigada...'),

                        Forms\Components\Select::make('estado')
                            ->label('Estado')
                            ->options([
                                'Activo' => 'Activo',
                                'Inactivo' => 'Inactivo',
                                'En_Reparacion' => 'En Reparación',
                                'Suspendido' => 'Suspendido',
                            ])
                            ->default('Activo')
                            ->required()
                            ->native(false),

                        Forms\Components\Textarea::make('notas')
                            ->label('Notas')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                // ─── Gestión Remota ───
                Forms\Components\Section::make('Datos de Gestión Remota')
                    ->visible(fn ($get) => $get('tipo') === 'Gestion_Remota')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('gr_tipo_solucion')
                            ->label('Tipo de solución')
                            ->options([
                                'Router4g' => 'Router 4G',
                                'Router+Modem' => 'Router + Módem',
                                'Router+ADSL' => 'Router + ADSL',
                                'Otros' => 'Otros',
                            ])
                            ->live()
                            ->native(false),

                        Forms\Components\TextInput::make('gr_sim_numero')
                            ->label('Número SIM card')
                            ->tel()
                            ->visible(fn ($get) => in_array($get('gr_tipo_solucion'), ['Router4g', 'Router+Modem'])),

                        Forms\Components\TextInput::make('gr_marca_modelo')
                            ->label('Marca / Modelo')
                            ->placeholder('Ej: TP-Link TL-MR6400')
                            ->columnSpanFull(),

                        Forms\Components\Select::make('gr_tipo_internet')
                            ->label('Tipo de internet')
                            ->options([
                                'Abierto' => 'Abierto',
                                'Filtrado' => 'Filtrado',
                                'Cerrado' => 'Cerrado',
                            ])
                            ->native(false),

                        Forms\Components\Select::make('gr_recarga_por')
                            ->label('¿Quién recarga?')
                            ->options([
                                'Nosotros' => 'Nosotros',
                                'Cliente' => 'El cliente',
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
            ]);
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
                    ->formatStateUsing(fn ($state, $record) => "{$record->ubicacion->nombre} ({$record->ubicacion->tipo})")
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('tipo')
                    ->colors([
                        'primary' => 'CCTV',
                        'danger' => 'SACI',
                        'warning' => 'Gestion_Remota',
                    ]),

                Tables\Columns\BadgeColumn::make('estado')
                    ->colors([
                        'success' => 'Activo',
                        'danger' => 'Inactivo',
                        'warning' => 'En_Reparacion',
                        'gray' => 'Suspendido',
                    ]),

                Tables\Columns\TextColumn::make('fecha_instalacion')
                    ->label('Instalado')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('responsable')
                    ->label('Brigada / Técnico')
                    ->getStateUsing(function ($record): string {
                        if ($record->brigada) return "Brigada: {$record->brigada->nombre}";
                        if ($record->tecnico) return "Téc: {$record->tecnico->name}";
                        return 'Sin asignar';
                    })
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('gr_sim_numero')
                    ->label('SIM')
                    ->placeholder('N/A')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('gr_marca_modelo')
                    ->label('Equipo')
                    ->placeholder('N/A')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('tipo')
                    ->options([
                        'CCTV' => 'CCTV',
                        'SACI' => 'SACI',
                        'Gestion_Remota' => 'Gestión Remota',
                    ]),

                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'Activo' => 'Activo',
                        'Inactivo' => 'Inactivo',
                        'En_Reparacion' => 'En Reparación',
                        'Suspendido' => 'Suspendido',
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
            'index' => \App\Filament\Resources\ServicioResource\Pages\ListServicios::route('/'),
            'create' => \App\Filament\Resources\ServicioResource\Pages\CreateServicio::route('/create'),
            'edit' => \App\Filament\Resources\ServicioResource\Pages\EditServicio::route('/{record}/edit'),
        ];
    }
}