<?php

namespace App\Filament\Resources;

use App\Models\Cliente;
use App\Models\ClienteUbicacion;
use App\Models\Incidencia;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class IncidenciaResource extends Resource
{
    protected static ?string $model = Incidencia::class;
    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationGroup = 'Operaciones';
    protected static ?string $modelLabel = 'Incidencia';
    protected static ?string $pluralModelLabel = 'Incidencias';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // ─── Información Principal ───
                Forms\Components\Section::make('Información Principal')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('codigo')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Auto-generado'),

                        Forms\Components\Select::make('tipo')
                            ->label('Tipo de Incidencia')
                            ->options([
                                'CCTV' => 'CCTV',
                                'SACI' => 'SACI',
                                'Redes' => 'Redes',
                                'Gestion_Remota' => 'Gestión Remota',
                            ])
                            ->required()
                            ->native(false),
                    ]),

                // ─── Cliente y Ubicación ───
                Forms\Components\Section::make('Cliente y Ubicación')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('cliente_id')
                            ->label('Cliente')
                            ->relationship('cliente', 'nombre')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                $set('cliente_ubicacion_id', null);
                                $set('direccion_incidencia', null);
                                $set('contacto_local', null);
                                $set('telefono_local', null);
                            }),

                        Forms\Components\Select::make('cliente_ubicacion_id')
                            ->label('Ubicación / Negocio')
                            ->options(function (Get $get): array {
                                $clienteId = $get('cliente_id');
                                if (!$clienteId) return [];

                                return ClienteUbicacion::where('cliente_id', $clienteId)
                                    ->where('activo', true)
                                    ->get()
                                    ->mapWithKeys(fn ($u) => [
                                        $u->id => "{$u->nombre} ({$u->tipo})"
                                    ])
                                    ->toArray();
                            })
                            ->searchable()
                            ->live()
                            ->placeholder('Primero seleccione un cliente')
                            ->disabled(fn (Get $get): bool => blank($get('cliente_id')))
                            ->afterStateUpdated(function (Set $set, $state) {
                                if (!$state) return;

                                $ubicacion = ClienteUbicacion::find($state);
                                if ($ubicacion) {
                                    $set('direccion_incidencia', $ubicacion->direccion);
                                    $set('contacto_local', $ubicacion->contacto_nombre);
                                    $set('telefono_local', $ubicacion->contacto_telefono);
                                }
                            }),

                        Forms\Components\TextInput::make('direccion_incidencia')
                            ->label('Dirección de la incidencia')
                            ->placeholder('Se rellena automáticamente al elegir ubicación')
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('contacto_local')
                            ->label('Persona de contacto en sitio'),

                        Forms\Components\TextInput::make('telefono_local')
                            ->label('Teléfono del sitio')
                            ->tel(),
                    ]),

                // ─── Descripción ───
                Forms\Components\Section::make('Descripción del Problema')
                    ->schema([
                        Forms\Components\TextInput::make('titulo')
                            ->label('Título / Resumen')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('descripcion')
                            ->label('Descripción detallada')
                            ->required()
                            ->rows(4),

                        Forms\Components\Textarea::make('diagnostico')
                            ->label('Diagnóstico inicial')
                            ->rows(3)
                            ->placeholder('Primera impresión técnica del problema'),
                    ]),

                // ─── Asignación ───
                Forms\Components\Section::make('Asignación')
                    ->columns(3)
                    ->schema([
                        Forms\Components\Select::make('tecnico_id')
                            ->label('Técnico Asignado')
                            ->relationship('tecnico', 'name', fn (Builder $query) => $query->role(['Técnico', 'Administrador', 'Supervisor']))
                            ->searchable()
                            ->preload()
                            ->placeholder('Sin asignar'),

                        Forms\Components\Select::make('estado')
                            ->options([
                                'Pendiente' => 'Pendiente',
                                'Asignada' => 'Asignada',
                                'En_Progreso' => 'En Progreso',
                                'En_Espera' => 'En Espera (repuestos/aprobación)',
                                'Resuelta' => 'Resuelta',
                                'Cerrada' => 'Cerrada',
                                'Cancelada' => 'Cancelada',
                            ])
                            ->required()
                            ->default('Pendiente')
                            ->native(false),

                        Forms\Components\Select::make('prioridad')
                            ->options([
                                'Baja' => 'Baja',
                                'Media' => 'Media',
                                'Alta' => 'Alta',
                                'Critica' => 'Crítica',
                            ])
                            ->required()
                            ->default('Media')
                            ->native(false),
                    ]),

                Forms\Components\Section::make('Fechas')
                    ->columns(2)
                    ->schema([
                        Forms\Components\DateTimePicker::make('fecha_limite')
                            ->label('Fecha límite (SLA)')
                            ->native(false),

                        Forms\Components\DateTimePicker::make('fecha_reporte')
                            ->label('Fecha de reporte')
                            ->default(now())
                            ->native(false),
                    ]),

                // ─── Resolución (solo edición) ───
                Forms\Components\Section::make('Resolución')
                    ->visible(fn (string $context): bool => $context === 'edit')
                    ->schema([
                        Forms\Components\Textarea::make('solucion')
                            ->label('Solución aplicada')
                            ->rows(4),

                        Forms\Components\Textarea::make('notas_internas')
                            ->label('Notas internas (solo staff)')
                            ->rows(3)
                            ->hint('No visible para el cliente'),

                        Forms\Components\Toggle::make('requiere_repuestos')
                            ->label('¿Requirió repuestos?')
                            ->default(false),

                        Forms\Components\TextInput::make('costo_estimado')
                            ->label('Costo estimado ($)')
                            ->numeric()
                            ->prefix('$')
                            ->maxValue(999999.99),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('codigo')
                    ->searchable()
                    ->sortable()
                    ->weight('font-bold')
                    ->copyable(),

                Tables\Columns\TextColumn::make('titulo')
                    ->searchable()
                    ->limit(35)
                    ->tooltip(fn ($record) => $record->titulo),

                Tables\Columns\BadgeColumn::make('tipo')
                    ->colors([
                        'primary' => 'CCTV',
                        'success' => 'SACI',
                        'warning' => 'Redes',
                        'danger' => 'Gestion_Remota',
                    ]),

                Tables\Columns\BadgeColumn::make('estado')
                    ->colors([
                        'gray' => 'Pendiente',
                        'primary' => 'Asignada',
                        'warning' => 'En_Progreso',
                        'info' => 'En_Espera',
                        'success' => 'Resuelta',
                        'secondary' => 'Cerrada',
                        'danger' => 'Cancelada',
                    ]),

                Tables\Columns\BadgeColumn::make('prioridad')
                    ->colors([
                        'success' => 'Baja',
                        'warning' => 'Media',
                        'danger' => 'Alta',
                        'danger' => 'Critica',
                    ]),

                Tables\Columns\TextColumn::make('cliente.nombre')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('ubicacion.nombre')
                    ->label('Ubicación')
                    ->placeholder('Sin ubicación')
                    ->formatStateUsing(fn ($state, $record) => $record->ubicacion ? "{$record->ubicacion->nombre} ({$record->ubicacion->tipo})" : '-'),

                Tables\Columns\TextColumn::make('tecnico.name')
                    ->label('Técnico')
                    ->placeholder('Sin asignar')
                    ->searchable(),

                Tables\Columns\TextColumn::make('fecha_limite')
                    ->label('Límite SLA')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('Sin fecha')
                    ->color(fn ($record) => $record->fecha_limite && $record->fecha_limite->isPast() && !in_array($record->estado, ['Resuelta', 'Cerrada', 'Cancelada']) ? 'danger' : null),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Reportada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('tipo')
                    ->options([
                        'CCTV' => 'CCTV',
                        'SACI' => 'SACI',
                        'Redes' => 'Redes',
                        'Gestion_Remota' => 'Gestión Remota',
                    ]),

                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'Pendiente' => 'Pendiente',
                        'Asignada' => 'Asignada',
                        'En_Progreso' => 'En Progreso',
                        'En_Espera' => 'En Espera',
                        'Resuelta' => 'Resuelta',
                        'Cerrada' => 'Cerrada',
                        'Cancelada' => 'Cancelada',
                    ])
                    ->multiple(),

                Tables\Filters\SelectFilter::make('prioridad')
                    ->options([
                        'Baja' => 'Baja',
                        'Media' => 'Media',
                        'Alta' => 'Alta',
                        'Critica' => 'Crítica',
                    ]),

                Tables\Filters\SelectFilter::make('cliente_id')
                    ->label('Cliente')
                    ->relationship('cliente', 'nombre')
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
            'index' => \App\Filament\Resources\IncidenciaResource\Pages\ListIncidencias::route('/'),
            'create' => \App\Filament\Resources\IncidenciaResource\Pages\CreateIncidencia::route('/create'),
            'edit' => \App\Filament\Resources\IncidenciaResource\Pages\EditIncidencia::route('/{record}/edit'),
        ];
    }
}