<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IncidenciaResource\Pages;
use App\Models\Cliente;
use App\Models\ClienteUbicacion;
use App\Models\Incidencia;
use App\Models\Servicio;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class IncidenciaResource extends Resource
{
    protected static ?string $model = Incidencia::class;
    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationGroup = 'Operaciones';
    protected static ?string $modelLabel = 'Incidencia';
    protected static ?string $pluralModelLabel = 'Incidencias Técnicas';
    protected static ?int $navigationSort = 2;
    protected static ?string $recordTitleAttribute = 'codigo';

    public static function getNavigationBadge(): ?string
    {
        $activas = static::getModel()::whereIn('estado', ['Pendiente', 'Asignada', 'En_Progreso', 'En_Espera'])->count();
        return $activas > 0 ? (string) $activas : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $criticasOVencidas = static::getModel()::whereIn('estado', ['Pendiente', 'Asignada', 'En_Progreso', 'En_Espera'])
            ->where(function ($query) {
                $query->where('prioridad', 'Critica')
                    ->orWhere(function ($q) {
                        $q->whereNotNull('fecha_limite')->where('fecha_limite', '<', now());
                    });
            })->exists();

        return $criticasOVencidas ? 'danger' : 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // ─── SECCIÓN 1: Clasificación y Origen ───
                Forms\Components\Section::make('Cliente y Sistema Afectado')
                    ->description('Seleccione el cliente, la sede y el servicio técnico instalado sobre el cual se reporta la falla.')
                    ->columns(3)
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
                                $set('servicio_id', null);
                            }),

                        Forms\Components\Select::make('cliente_ubicacion_id')
                            ->label('Ubicación / Sede')
                            ->options(function (Get $get) {
                                $clienteId = $get('cliente_id');
                                if (!$clienteId) {
                                    return [];
                                }
                                return ClienteUbicacion::where('cliente_id', $clienteId)
                                    ->pluck('nombre', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->live()
                            ->placeholder('Seleccione sede...')
                            ->afterStateUpdated(function (Set $set, $state) {
                                $set('servicio_id', null);
                                if ($state) {
                                    $ubicacion = ClienteUbicacion::find($state);
                                    if ($ubicacion) {
                                        $set('direccion_incidencia', $ubicacion->direccion);
                                        $set('contacto_local', $ubicacion->contacto_nombre);
                                        $set('telefono_local', $ubicacion->contacto_telefono);
                                    }
                                }
                            }),

                        Forms\Components\Select::make('servicio_id')
                            ->label('Servicio / Equipo Instalado')
                            ->options(function (Get $get) {
                                $ubicacionId = $get('cliente_ubicacion_id');
                                $clienteId = $get('cliente_id');

                                $query = Servicio::query();

                                if ($ubicacionId) {
                                    $query->where('cliente_ubicacion_id', $ubicacionId);
                                } elseif ($clienteId) {
                                    $query->whereHas('ubicacion', fn ($q) => $q->where('cliente_id', $clienteId));
                                } else {
                                    return [];
                                }

                                return $query->get()->mapWithKeys(function ($serv) {
                                    $detalle = match ($serv->tipo) {
                                        'CCTV' => "CCTV: " . ($serv->cctv_marca ?? 'DVR/NVR') . " (" . ($serv->cctv_canales ? $serv->cctv_canales . ' CH' : 'Cámaras') . ")",
                                        'SACI' => "SACI: " . ($serv->saci_marca ?? 'Alarma') . " (" . ($serv->saci_solucion ?? 'Sistema') . ")",
                                        'Gestion_Remota' => "Gestión Remota: " . ($serv->gr_tipo_solucion ?? 'Router'),
                                        default => "{$serv->tipo} - Estado: {$serv->estado}",
                                    };
                                    return [$serv->id => $detalle];
                                });
                            })
                            ->searchable()
                            ->live()
                            ->placeholder('Seleccione equipo/servicio...')
                            ->afterStateUpdated(function (Set $set, $state) {
                                if ($state) {
                                    $servicio = Servicio::find($state);
                                    if ($servicio && in_array($servicio->tipo, ['CCTV', 'SACI', 'Redes', 'Gestion_Remota'])) {
                                        $set('tipo', $servicio->tipo);
                                    }
                                }
                            }),
                    ]),

                // ─── SECCIÓN 2: Tipificación y Estado de la Incidencia ───
                Forms\Components\Section::make('Clasificación y SLA')
                    ->columns(3)
                    ->schema([
                        Forms\Components\Select::make('tipo')
                            ->label('Tipo de Servicio')
                            ->options([
                                'CCTV'           => 'CCTV (Videovigilancia)',
                                'SACI'           => 'SACI (Alarma Contra Intrusión/Incendio)',
                                'Redes'          => 'Redes y Telecomunicaciones',
                                'Gestion_Remota' => 'Gestión Remota / Enlaces',
                            ])
                            ->required()
                            ->default('CCTV')
                            ->native(false),

                        Forms\Components\Select::make('prioridad')
                            ->label('Prioridad')
                            ->options([
                                'Baja'    => 'Baja (SLA: 7 días)',
                                'Media'   => 'Media (SLA: 48 horas)',
                                'Alta'    => 'Alta (SLA: 24 horas)',
                                'Critica' => 'Crítica (SLA: 8 horas)',
                            ])
                            ->required()
                            ->default('Media')
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(function (Set $set, $state) {
                                // Sugerir y autocalcular fecha límite según el SLA
                                $horas = match ($state) {
                                    'Critica' => 8,
                                    'Alta'    => 24,
                                    'Media'   => 48,
                                    'Baja'    => 168, // 7 días
                                    default   => 48,
                                };
                                $set('fecha_limite', now()->addHours($horas)->format('Y-m-d H:i'));
                            }),

                        Forms\Components\Select::make('estado')
                            ->label('Estado Operativo')
                            ->options([
                                'Pendiente'   => 'Pendiente (Nueva)',
                                'Asignada'    => 'Asignada a Técnico',
                                'En_Progreso' => 'En Progreso (En Intervención)',
                                'En_Espera'   => 'En Espera (Repuestos/Aprobación)',
                                'Resuelta'    => 'Resuelta (Trabajo Finalizado)',
                                'Cerrada'     => 'Cerrada (Conformidad Cliente)',
                                'Cancelada'   => 'Cancelada',
                            ])
                            ->required()
                            ->default('Pendiente')
                            ->native(false),
                    ]),

                // ─── SECCIÓN 3: Detalle del Problema ───
                Forms\Components\Section::make('Detalle del Problema')
                    ->columns(1)
                    ->schema([
                        Forms\Components\TextInput::make('titulo')
                            ->label('Título / Asunto de la Incidencia')
                            ->required()
                            ->placeholder('Ej: Pérdida de señal en cámara 4 y 5 del almacén')
                            ->maxLength(255),

                        Forms\Components\Textarea::make('descripcion')
                            ->label('Descripción de la falla reportada')
                            ->required()
                            ->rows(3)
                            ->placeholder('Indique síntomas, cuándo comenzó el problema y cualquier detalle aportado por el cliente...'),

                        Forms\Components\Textarea::make('diagnostico')
                            ->label('Diagnóstico Técnico Preliminar')
                            ->rows(2)
                            ->placeholder('Observaciones preliminares del coordinador o técnico al recibir el reporte...'),
                    ]),

                // ─── SECCIÓN 4: Contacto en Sitio y Asignación Operativa ───
                Forms\Components\Section::make('Contacto en Sitio y Asignación Operativa')
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('contacto_local')
                            ->label('Contacto en Sitio')
                            ->placeholder('Nombre de la persona en el local')
                            ->maxLength(100),

                        Forms\Components\TextInput::make('telefono_local')
                            ->label('Teléfono de Contacto')
                            ->placeholder('Móvil o fijo')
                            ->tel()
                            ->maxLength(50),

                        Forms\Components\TextInput::make('direccion_incidencia')
                            ->label('Dirección de Intervención')
                            ->placeholder('Dirección exacta')
                            ->maxLength(255),

                        Forms\Components\Select::make('tecnico_id')
                            ->label('Técnico Responsable')
                            ->relationship('tecnico', 'name', fn (Builder $query) => $query->role(['Técnico', 'Administrador', 'Supervisor']))
                            ->searchable()
                            ->preload()
                            ->placeholder('Sin asignar'),

                        Forms\Components\Select::make('brigada_id')
                            ->label('Brigada Asignada')
                            ->relationship('brigada', 'nombre')
                            ->searchable()
                            ->preload()
                            ->placeholder('Sin brigada (opcional)'),

                        Forms\Components\DateTimePicker::make('fecha_limite')
                            ->label('Fecha Límite (SLA)')
                            ->helperText('Plazo máximo objetivo de solución')
                            ->native(false),
                    ]),

                // ─── SECCIÓN 5: Resolución y Cierre (visible en edición) ───
                Forms\Components\Section::make('Resolución Técnica y Costos')
                    ->visible(fn (string $context): bool => $context === 'edit')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Textarea::make('solucion')
                            ->label('Solución Aplicada')
                            ->columnSpanFull()
                            ->rows(3)
                            ->placeholder('Detalle de las acciones realizadas para resolver la falla...'),

                        Forms\Components\Textarea::make('notas_internas')
                            ->label('Notas Internas (Solo Staff)')
                            ->columnSpanFull()
                            ->rows(2)
                            ->placeholder('Comentarios reservados para el equipo interno...'),

                        Forms\Components\Toggle::make('requiere_repuestos')
                            ->label('¿Requirió Repuestos / Materiales?')
                            ->default(false),

                        Forms\Components\TextInput::make('costo_estimado')
                            ->label('Costo Total Intervención ($)')
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
                    ->label('Código')
                    ->searchable()
                    ->sortable()
                    ->weight('font-bold')
                    ->copyable()
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('prioridad')
                    ->label('Prioridad')
                    ->badge()
                    ->colors([
                        'danger'  => fn ($state) => in_array($state, ['Alta', 'Critica']),
                        'warning' => 'Media',
                        'success' => 'Baja',
                    ]),

                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->colors([
                        'gray'      => 'Pendiente',
                        'info'      => 'Asignada',
                        'warning'   => 'En_Progreso',
                        'purple'    => 'En_Espera',
                        'success'   => 'Resuelta',
                        'secondary' => 'Cerrada',
                        'danger'    => 'Cancelada',
                    ]),

                Tables\Columns\TextColumn::make('titulo')
                    ->label('Asunto')
                    ->searchable()
                    ->limit(32)
                    ->tooltip(fn ($record) => $record->titulo),

                Tables\Columns\TextColumn::make('cliente.nombre')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable()
                    ->weight('font-semibold'),

                Tables\Columns\TextColumn::make('tipo')
                    ->label('Servicio')
                    ->badge()
                    ->colors([
                        'primary' => 'CCTV',
                        'success' => 'SACI',
                        'warning' => 'Redes',
                        'danger'  => 'Gestion_Remota',
                    ]),

                Tables\Columns\TextColumn::make('tecnico.name')
                    ->label('Técnico / Brigada')
                    ->placeholder('Sin asignar')
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->tecnico && $record->brigada) {
                            return "{$record->tecnico->name} ({$record->brigada->nombre})";
                        }
                        if ($record->tecnico) {
                            return $record->tecnico->name;
                        }
                        if ($record->brigada) {
                            return "Brigada: {$record->brigada->nombre}";
                        }
                        return 'Sin asignar';
                    })
                    ->searchable(),

                Tables\Columns\TextColumn::make('fecha_limite')
                    ->label('Límite SLA')
                    ->dateTime('d/m H:i')
                    ->sortable()
                    ->placeholder('Sin SLA')
                    ->color(function ($record) {
                        if ($record->esta_vencida) {
                            return 'danger';
                        }
                        if ($record->horas_restantes !== null && $record->horas_restantes <= 8 && $record->horas_restantes >= 0) {
                            return 'warning';
                        }
                        return null;
                    })
                    ->description(function ($record) {
                        if (in_array($record->estado, ['Resuelta', 'Cerrada', 'Cancelada'])) {
                            return null;
                        }
                        if ($record->esta_vencida) {
                            return '¡Vencida!';
                        }
                        if ($record->horas_restantes !== null) {
                            return "{$record->horas_restantes}h restantes";
                        }
                        return null;
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'Pendiente'   => 'Pendiente',
                        'Asignada'    => 'Asignada',
                        'En_Progreso' => 'En Progreso',
                        'En_Espera'   => 'En Espera',
                        'Resuelta'    => 'Resuelta',
                        'Cerrada'     => 'Cerrada',
                        'Cancelada'   => 'Cancelada',
                    ])
                    ->multiple(),

                Tables\Filters\SelectFilter::make('prioridad')
                    ->options([
                        'Baja'    => 'Baja',
                        'Media'   => 'Media',
                        'Alta'    => 'Alta',
                        'Critica' => 'Crítica',
                    ]),

                Tables\Filters\SelectFilter::make('tipo')
                    ->options([
                        'CCTV'           => 'CCTV',
                        'SACI'           => 'SACI',
                        'Redes'          => 'Redes',
                        'Gestion_Remota' => 'Gestión Remota',
                    ]),

                Tables\Filters\Filter::make('vencidas_sla')
                    ->label('SLA Vencido')
                    ->query(fn (Builder $query) => $query->where('fecha_limite', '<', now())->whereNotIn('estado', ['Resuelta', 'Cerrada', 'Cancelada'])),
            ])
            ->actions([
                // ─── Acción Rápida: Tomar Incidencia (Asignar a mí) ───
                Tables\Actions\Action::make('tomar')
                    ->label('Tomar')
                    ->icon('heroicon-m-hand-raised')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('¿Tomar esta incidencia?')
                    ->modalDescription('Se asignará a tu usuario como responsable y pasará automáticamente al estado "En Progreso".')
                    ->visible(fn (Incidencia $record) => in_array($record->estado, ['Pendiente', 'Asignada']) && $record->tecnico_id !== auth()->id())
                    ->action(function (Incidencia $record) {
                        $record->update([
                            'tecnico_id'           => auth()->id(),
                            'estado'               => 'En_Progreso',
                            'fecha_asignacion'     => now(),
                            'fecha_inicio_trabajo' => $record->fecha_inicio_trabajo ?? now(),
                        ]);

                        Notification::make()
                            ->title('Incidencia Asignada')
                            ->body("Has tomado la incidencia {$record->codigo}.")
                            ->success()
                            ->send();
                    }),

                // ─── Acción Rápida: Iniciar Trabajo ───
                Tables\Actions\Action::make('iniciar')
                    ->label('Iniciar')
                    ->icon('heroicon-m-play')
                    ->color('warning')
                    ->visible(fn (Incidencia $record) => $record->estado === 'Asignada')
                    ->action(function (Incidencia $record) {
                        $record->update([
                            'estado'               => 'En_Progreso',
                            'fecha_inicio_trabajo' => now(),
                        ]);

                        Notification::make()
                            ->title('Trabajo Iniciado')
                            ->body("La incidencia {$record->codigo} está ahora En Progreso.")
                            ->info()
                            ->send();
                    }),

                // ─── Acción Rápida: Resolver Incidencia ───
                Tables\Actions\Action::make('resolver')
                    ->label('Resolver')
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->visible(fn (Incidencia $record) => in_array($record->estado, ['Asignada', 'En_Progreso', 'En_Espera']))
                    ->form([
                        Forms\Components\Textarea::make('solucion')
                            ->label('Solución Aplicada / Trabajo Realizado')
                            ->required()
                            ->rows(3)
                            ->placeholder('Describa brevemente qué se reparó o reemplazó...'),

                        Forms\Components\Toggle::make('requiere_repuestos')
                            ->label('¿Requirió Repuestos o Cambio de Piezas?')
                            ->default(false),

                        Forms\Components\TextInput::make('costo_estimado')
                            ->label('Costo Total ($)')
                            ->numeric()
                            ->prefix('$')
                            ->placeholder('0.00'),

                        Forms\Components\Textarea::make('notas_internas')
                            ->label('Notas Internas (Opcional)')
                            ->rows(2),
                    ])
                    ->action(function (Incidencia $record, array $data) {
                        $record->update([
                            'estado'             => 'Resuelta',
                            'solucion'           => $data['solucion'],
                            'requiere_repuestos' => $data['requiere_repuestos'] ?? false,
                            'costo_estimado'     => $data['costo_estimado'] ?? null,
                            'notas_internas'     => $data['notas_internas'] ?? $record->notas_internas,
                            'fecha_resolucion'   => now(),
                        ]);

                        Notification::make()
                            ->title('Incidencia Resuelta')
                            ->body("La incidencia {$record->codigo} fue marcada como Resuelta exitosamente.")
                            ->success()
                            ->send();
                    }),

                Tables\Actions\EditAction::make(),
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
            'index'  => Pages\ListIncidencias::route('/'),
            'create' => Pages\CreateIncidencia::route('/create'),
            'edit'   => Pages\EditIncidencia::route('/{record}/edit'),
        ];
    }
}
