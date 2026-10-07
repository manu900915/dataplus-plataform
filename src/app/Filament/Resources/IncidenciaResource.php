<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IncidenciaResource\Pages;
use App\Models\Cliente;
use App\Models\ClienteUbicacion;
use App\Models\Incidencia;
use App\Models\Servicio;
use App\Models\User;
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
    protected static ?string $navigationGroup = 'Operaciones';
    protected static ?string $navigationLabel = 'Incidencias';
    protected static ?string $modelLabel = 'Incidencia';
    protected static ?string $pluralModelLabel = 'Incidencias';
    protected static ?string $navigationIcon = 'heroicon-o-exclamation-circle';
    protected static ?int $navigationSort = 2;

    /**
     * Devuelve la lista de usuarios operativos (Técnicos, Especialistas, Supervisores y Administradores).
     */
    public static function getUsuariosOperativosQuery(): Builder
    {
        $rolesValidos = [
            'Técnico', 'tecnico', 'Tecnico',
            'Especialista', 'especialista',
            'Supervisor', 'supervisor',
            'Administrador', 'admin', 'superadmin',
        ];

        return User::query()
            ->where('activo', true)
            ->where(function (Builder $q) use ($rolesValidos) {
                $q->whereHas('roles', fn ($r) => $r->whereIn('name', $rolesValidos))
                  ->orWhereDoesntHave('roles');
            })
            ->orderBy('name');
    }

    public static function getNavigationBadge(): ?string
    {
        try {
            $count = Incidencia::whereNotIn('estado', ['Resuelta', 'Revisada_Supervisor', 'Cerrada', 'Cancelada'])->count();
            return $count > 0 ? (string) $count : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    public static function getNavigationBadgeColor(): ?string
    {
        try {
            $criticasOVencidas = Incidencia::whereNotIn('estado', ['Resuelta', 'Revisada_Supervisor', 'Cerrada', 'Cancelada'])
                ->where(function ($q) {
                    $q->where('prioridad', 'Critica')
                      ->orWhere(function ($sub) {
                          $sub->whereNotNull('fecha_limite')->where('fecha_limite', '<', now());
                      });
                })->exists();

            return $criticasOVencidas ? 'danger' : 'warning';
        } catch (\Throwable $e) {
            return 'warning';
        }
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Incidencias activas pendientes de atención o resolución';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // ─── SECCIÓN 1: Clasificación y Origen ───
                Forms\Components\Section::make('Cliente y Sistema Afectado')
                    ->description('Sede y tecnología afectada reportada por el cliente.')
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
                                        'CCTV'           => "CCTV: " . ($serv->cctv_marca ?? 'DVR/NVR') . " (" . ($serv->cctv_canales ? $serv->cctv_canales . ' CH' : 'Cámaras') . ")",
                                        'SACI'           => "SACI: " . ($serv->saci_marca ?? 'Alarma') . " (" . ($serv->saci_solucion ?? 'Sistema') . ")",
                                        'Gestion_Remota' => "Gestión Remota: " . ($serv->gr_tipo_solucion ?? 'Router'),
                                        default          => "{$serv->tipo} - Estado: {$serv->estado}",
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
                Forms\Components\Section::make('Clasificación y Estado')
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
                            ->label('Prioridad / SLA')
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
                                $horas = match ($state) {
                                    'Critica' => 8,
                                    'Alta'    => 24,
                                    'Media'   => 48,
                                    'Baja'    => 168,
                                    default   => 48,
                                };
                                $set('fecha_limite', now()->addHours($horas)->format('Y-m-d H:i'));
                            }),

                        Forms\Components\Select::make('estado')
                            ->label('Estado Operativo')
                            ->options([
                                'Pendiente'           => '1. Pendiente (Sin responsable asignado)',
                                'Asignada'            => '2. Asignada (A técnico o brigada)',
                                'En_Progreso'         => '3. En Progreso (Intervención técnica)',
                                'En_Espera'           => '4. En Espera (Repuestos/Cliente)',
                                'Resuelta'            => '5. Culminada por Técnico (Por Revisar Supervisor)',
                                'Revisada_Supervisor' => '6. Visto Bueno Supervisor (Por Cerrar Comercial)',
                                'Cerrada'             => '7. Cerrada (Conformidad Cliente)',
                                'Cancelada'           => 'Cancelada',
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
                            ->label('Asunto / Título de la Falla')
                            ->required()
                            ->placeholder('Ej: Pérdida de señal en cámara entrada principal')
                            ->maxLength(255),

                        Forms\Components\Textarea::make('descripcion')
                            ->label('Descripción Detallada')
                            ->required()
                            ->rows(3)
                            ->placeholder('Indique síntomas, cuándo comenzó el problema y cualquier detalle aportado por el cliente...'),

                        Forms\Components\Textarea::make('diagnostico')
                            ->label('Diagnóstico Técnico Preliminar')
                            ->rows(2)
                            ->placeholder('Observaciones preliminares del coordinador o técnico al recibir el reporte...'),
                    ]),

                // ─── SECCIÓN 4: Contacto en Sitio y Asignación Operativa ───
                Forms\Components\Section::make('Contacto en Sitio y Asignación')
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
                            ->label('Responsable (Técnico / Especialista / Supervisor)')
                            ->relationship('tecnico', 'name', fn (Builder $query) => static::getUsuariosOperativosQuery())
                            ->searchable()
                            ->preload()
                            ->placeholder('Seleccione responsable...')
                            ->live()
                            ->afterStateUpdated(function (Set $set, $state, Get $get) {
                                // Si se selecciona un responsable y el estado estaba en Pendiente, pasa a Asignada
                                if ($state && ($get('estado') === 'Pendiente' || empty($get('estado')))) {
                                    $set('estado', 'Asignada');
                                }
                            }),

                        Forms\Components\Select::make('brigada_id')
                            ->label('Brigada Asignada')
                            ->relationship('brigada', 'nombre')
                            ->searchable()
                            ->preload()
                            ->placeholder('Sin brigada (opcional)'),

                        Forms\Components\DateTimePicker::make('fecha_limite')
                            ->label('Fecha Límite (SLA)')
                            ->helperText('Plazo máximo objetivo de solución acordado con el cliente')
                            ->native(false),
                    ]),

                // ─── SECCIÓN 5: Tiempos de Atención y Registro SLA ───
                Forms\Components\Section::make('Tiempos de Atención y SLA')
                    ->description('Registro de hitos temporales para evaluación del SLA de servicio.')
                    ->columns(3)
                    ->schema([
                        Forms\Components\DateTimePicker::make('fecha_reporte')
                            ->label('Fecha/Hora de Reporte')
                            ->default(now())
                            ->native(false)
                            ->helperText('Momento en que se registró la incidencia'),

                        Forms\Components\DateTimePicker::make('fecha_resolucion')
                            ->label('Fecha/Hora de Resolución Técnica')
                            ->native(false)
                            ->helperText('Momento en que el técnico concluyó la reparación'),

                        Forms\Components\Placeholder::make('metrica_sla')
                            ->label('Estado Actual del SLA')
                            ->content(function (?Incidencia $record): string {
                                if (!$record) return 'Se calculará al guardar';
                                if ($record->fecha_resolucion) {
                                    if ($record->cumplio_sla === true) {
                                        return '✅ CUMPLIDO: Resuelto dentro del plazo de SLA (' . ($record->tiempo_resolucion_texto ?? '') . ')';
                                    }
                                    if ($record->cumplio_sla === false) {
                                        return '❌ EXCEDIDO: Superó el plazo del SLA pactado';
                                    }
                                    return 'Resuelta (sin plazo límite definido)';
                                }
                                if ($record->esta_vencida) {
                                    return '⚠️ SLA Vencido hace ' . $record->horas_vencida . ' horas';
                                }
                                return $record->horas_restantes !== null
                                    ? '⏳ Quedan ' . $record->horas_restantes . ' horas para el límite'
                                    : 'Sin fecha límite configurada';
                            }),
                    ]),

                // ─── SECCIÓN 6: Resolución Técnica (Técnico / Especialista) ───
                Forms\Components\Section::make('Resolución Técnica y Costos')
                    ->visible(fn (string $context): bool => $context === 'edit')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Textarea::make('solucion')
                            ->label('Solución Aplicada')
                            ->columnSpanFull()
                            ->rows(3)
                            ->placeholder('Detalle de las acciones realizadas para resolver la falla...'),

                        Forms\Components\Toggle::make('requiere_repuestos')
                            ->label('¿Requirió Repuestos / Materiales?')
                            ->default(false),

                        Forms\Components\TextInput::make('costo_estimado')
                            ->label('Costo Total Intervención ($)')
                            ->numeric()
                            ->prefix('$')
                            ->maxValue(999999.99),
                    ]),

                // ─── SECCIÓN 7: Control de Calidad del Supervisor ───
                Forms\Components\Section::make('Control de Calidad (Supervisor)')
                    ->visible(fn (string $context, ?Incidencia $record): bool => $context === 'edit' && in_array($record?->estado, ['Resuelta', 'Revisada_Supervisor', 'Cerrada']))
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('revisado_por')
                            ->label('Supervisor Revisor')
                            ->relationship('supervisorRevisor', 'name')
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('fecha_revision_supervisor')
                            ->label('Fecha de Visto Bueno')
                            ->disabled(),

                        Forms\Components\Textarea::make('notas_supervisor')
                            ->label('Dictamen Técnico del Supervisor')
                            ->columnSpanFull()
                            ->rows(2)
                            ->placeholder('Conformidad técnica del trabajo realizado...'),
                    ]),

                // ─── SECCIÓN 8: Cierre Comercial y del Cliente ───
                Forms\Components\Section::make('Cierre Comercial y Conformidad del Cliente')
                    ->visible(fn (string $context, ?Incidencia $record): bool => $context === 'edit' && in_array($record?->estado, ['Revisada_Supervisor', 'Cerrada']))
                    ->columns(2)
                    ->schema([
                        Forms\Components\Toggle::make('conformidad_cliente')
                            ->label('¿Cliente conforme con el servicio y resolución?')
                            ->default(true),

                        Forms\Components\Select::make('cerrado_por')
                            ->label('Comercial que Cierra')
                            ->relationship('cerrador', 'name')
                            ->disabled(),

                        Forms\Components\Textarea::make('observaciones_cierre_comercial')
                            ->label('Observaciones de Cierre Comercial')
                            ->columnSpanFull()
                            ->rows(2)
                            ->placeholder('Notas de contacto con el cliente (satisfacción, garantía o facturación)...'),
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
                        'gray'    => 'Pendiente',
                        'info'    => 'Asignada',
                        'warning' => 'En_Progreso',
                        'purple'  => 'En_Espera',
                        'primary' => 'Resuelta',
                        'success' => ['Revisada_Supervisor', 'Cerrada'],
                        'danger'  => 'Cancelada',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'Pendiente'           => 'Pendiente',
                        'Asignada'            => 'Asignada',
                        'En_Progreso'         => 'En Progreso',
                        'En_Espera'           => 'En Espera',
                        'Resuelta'            => 'Culminada (Técnico)',
                        'Revisada_Supervisor' => 'VºBº Supervisor',
                        'Cerrada'             => 'Cerrada (Comercial)',
                        'Cancelada'           => 'Cancelada',
                        default               => $state,
                    }),

                Tables\Columns\TextColumn::make('titulo')
                    ->label('Asunto')
                    ->searchable()
                    ->limit(30)
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
                    ->label('Responsable')
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

                // ─── Columna de Tiempos y Resolución SLA ───
                Tables\Columns\TextColumn::make('fecha_resolucion')
                    ->label('Resolución / SLA')
                    ->sortable()
                    ->formatStateUsing(function ($state, Incidencia $record) {
                        if ($record->fecha_resolucion) {
                            return "⏱ {$record->tiempo_resolucion_texto}";
                        }
                        if ($record->fecha_limite) {
                            return "Límite: " . $record->fecha_limite->format('d/m H:i');
                        }
                        return 'Sin SLA';
                    })
                    ->badge(fn (Incidencia $record) => !empty($record->fecha_resolucion))
                    ->color(function (Incidencia $record) {
                        if ($record->fecha_resolucion) {
                            return $record->cumplio_sla === false ? 'danger' : 'success';
                        }
                        if ($record->esta_vencida) {
                            return 'danger';
                        }
                        if ($record->horas_restantes !== null && $record->horas_restantes <= 8 && $record->horas_restantes >= 0) {
                            return 'warning';
                        }
                        return 'info';
                    })
                    ->description(function (Incidencia $record) {
                        if ($record->fecha_resolucion) {
                            if ($record->cumplio_sla === true) {
                                return '✓ Resuelta en SLA';
                            }
                            if ($record->cumplio_sla === false) {
                                return '⚠ Superó plazo SLA';
                            }
                            return 'Resuelta: ' . $record->fecha_resolucion->format('d/m H:i');
                        }
                        if ($record->esta_vencida) {
                            return "¡Vencida hace {$record->horas_vencida}h!";
                        }
                        if ($record->horas_restantes !== null) {
                            return "Restan {$record->horas_restantes}h de SLA";
                        }
                        return "Abierta hace {$record->tiempo_transcurrido_texto}";
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'Pendiente'           => 'Pendiente',
                        'Asignada'            => 'Asignada',
                        'En_Progreso'         => 'En Progreso',
                        'En_Espera'           => 'En Espera',
                        'Resuelta'            => 'Culminada por Técnico',
                        'Revisada_Supervisor' => 'VºBº Supervisor',
                        'Cerrada'             => 'Cerrada',
                        'Cancelada'           => 'Cancelada',
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
                    ->query(fn (Builder $query) => $query->where('fecha_limite', '<', now())->whereNotIn('estado', ['Resuelta', 'Revisada_Supervisor', 'Cerrada', 'Cancelada'])),
            ])
            ->actions([
                // ─── 1. Tomar Incidencia: SOLO si NO TIENE responsable asignado ───
                Tables\Actions\Action::make('tomar')
                    ->label('Tomar')
                    ->icon('heroicon-m-hand-raised')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('¿Tomar esta incidencia?')
                    ->modalDescription('Se te asignará como responsable técnico y pasará a "En Progreso".')
                    ->visible(fn (Incidencia $record) => empty($record->tecnico_id) && in_array($record->estado, ['Pendiente', 'Asignada']))
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

                // ─── 2. Asignar Técnico: SOLO si NO TIENE responsable asignado ───
                Tables\Actions\Action::make('asignar')
                    ->label('Asignar')
                    ->icon('heroicon-m-user-plus')
                    ->color('warning')
                    ->visible(fn (Incidencia $record) => empty($record->tecnico_id) && in_array($record->estado, ['Pendiente', 'Asignada']))
                    ->form([
                        Forms\Components\Select::make('tecnico_id')
                            ->label('Responsable (Técnico / Especialista / Supervisor)')
                            ->options(fn () => static::getUsuariosOperativosQuery()->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('brigada_id')
                            ->label('Brigada Asignada (Opcional)')
                            ->relationship('brigada', 'nombre')
                            ->searchable()
                            ->preload(),
                    ])
                    ->action(function (Incidencia $record, array $data) {
                        $record->update([
                            'tecnico_id'       => $data['tecnico_id'],
                            'brigada_id'       => $data['brigada_id'] ?? null,
                            'estado'           => 'Asignada',
                            'fecha_asignacion' => now(),
                        ]);

                        Notification::make()
                            ->title('Incidencia Asignada')
                            ->body("La incidencia {$record->codigo} ha sido asignada.")
                            ->success()
                            ->send();
                    }),

                // ─── 3. Iniciar Trabajo (cuando ya está asignada) ───
                Tables\Actions\Action::make('iniciar')
                    ->label('Iniciar')
                    ->icon('heroicon-m-play')
                    ->color('warning')
                    ->visible(fn (Incidencia $record) => !empty($record->tecnico_id) && $record->estado === 'Asignada')
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

                // ─── 4. Reasignar (Supervisor cambia de responsable) ───
                Tables\Actions\Action::make('reasignar')
                    ->label('Reasignar')
                    ->icon('heroicon-m-arrows-right-left')
                    ->color('gray')
                    ->visible(fn (Incidencia $record) => !empty($record->tecnico_id) && in_array($record->estado, ['Asignada', 'En_Progreso', 'En_Espera']))
                    ->form([
                        Forms\Components\Select::make('tecnico_id')
                            ->label('Nuevo Responsable')
                            ->options(fn () => static::getUsuariosOperativosQuery()->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('brigada_id')
                            ->label('Brigada Asignada (Opcional)')
                            ->relationship('brigada', 'nombre')
                            ->searchable()
                            ->preload(),
                    ])
                    ->action(function (Incidencia $record, array $data) {
                        $record->update([
                            'tecnico_id' => $data['tecnico_id'],
                            'brigada_id' => $data['brigada_id'] ?? null,
                        ]);

                        Notification::make()
                            ->title('Incidencia Reasignada')
                            ->body("La incidencia {$record->codigo} ahora está a cargo del nuevo responsable.")
                            ->info()
                            ->send();
                    }),

                // ─── 5. Culminar / Resolver (con selector de fecha real de resolución) ───
                Tables\Actions\Action::make('resolver')
                    ->label('Culminar')
                    ->icon('heroicon-m-check-circle')
                    ->color('primary')
                    ->visible(fn (Incidencia $record) => in_array($record->estado, ['Asignada', 'En_Progreso', 'En_Espera']))
                    ->form([
                        Forms\Components\DateTimePicker::make('fecha_resolucion')
                            ->label('Fecha y Hora Real de Resolución')
                            ->default(now())
                            ->required()
                            ->native(false)
                            ->helperText('Indique cuándo concluyó realmente la reparación técnica (se contrastará con el SLA)'),

                        Forms\Components\Textarea::make('solucion')
                            ->label('Solución Aplicada / Trabajo Realizado')
                            ->required()
                            ->rows(3)
                            ->placeholder('Describa qué se reparó o reemplazó...'),

                        Forms\Components\Toggle::make('requiere_repuestos')
                            ->label('¿Requirió Repuestos o Materiales?')
                            ->default(false),

                        Forms\Components\TextInput::make('costo_estimado')
                            ->label('Costo Estimado Intervención ($)')
                            ->numeric()
                            ->prefix('$')
                            ->placeholder('0.00'),
                    ])
                    ->action(function (Incidencia $record, array $data) {
                        $record->update([
                            'estado'             => 'Resuelta',
                            'solucion'           => $data['solucion'],
                            'requiere_repuestos' => $data['requiere_repuestos'] ?? false,
                            'costo_estimado'     => $data['costo_estimado'] ?? null,
                            'fecha_resolucion'   => $data['fecha_resolucion'] ?? now(),
                        ]);

                        Notification::make()
                            ->title('Incidencia Culminada')
                            ->body("Pasa a revisión del Supervisor.")
                            ->success()
                            ->send();
                    }),

                // ─── 6. Visto Bueno (Supervisor) ───
                Tables\Actions\Action::make('aprobar_supervisor')
                    ->label('VºBº Supervisor')
                    ->icon('heroicon-m-shield-check')
                    ->color('success')
                    ->visible(fn (Incidencia $record) => $record->estado === 'Resuelta')
                    ->form([
                        Forms\Components\Textarea::make('notas_supervisor')
                            ->label('Dictamen Técnico del Supervisor')
                            ->required()
                            ->rows(2)
                            ->placeholder('Certifico que la solución técnica es conforme y los materiales son correctos.'),
                    ])
                    ->action(function (Incidencia $record, array $data) {
                        $record->update([
                            'estado'                    => 'Revisada_Supervisor',
                            'revisado_por'              => auth()->id(),
                            'fecha_revision_supervisor' => now(),
                            'notas_supervisor'          => $data['notas_supervisor'],
                        ]);

                        Notification::make()
                            ->title('Visto Bueno Otorgado')
                            ->body("Incidencia {$record->codigo} aprobada. Lista para cierre comercial con el cliente.")
                            ->success()
                            ->send();
                    }),

                // ─── 7. Cierre Comercial con Cliente ───
                Tables\Actions\Action::make('cierre_comercial')
                    ->label('Cerrar')
                    ->icon('heroicon-m-document-check')
                    ->color('success')
                    ->visible(fn (Incidencia $record) => $record->estado === 'Revisada_Supervisor')
                    ->form([
                        Forms\Components\Toggle::make('conformidad_cliente')
                            ->label('¿Cliente confirma satisfacción y buen funcionamiento?')
                            ->default(true)
                            ->required(),

                        Forms\Components\Textarea::make('observaciones_cierre_comercial')
                            ->label('Observaciones de Cierre Comercial')
                            ->rows(2)
                            ->placeholder('Detalles de la llamada o acta de conformidad (garantía / facturado)...'),
                    ])
                    ->action(function (Incidencia $record, array $data) {
                        $record->update([
                            'estado'                         => 'Cerrada',
                            'cerrado_por'                    => auth()->id(),
                            'conformidad_cliente'            => $data['conformidad_cliente'] ?? true,
                            'observaciones_cierre_comercial' => $data['observaciones_cierre_comercial'] ?? null,
                            'fecha_cierre'                   => now(),
                        ]);

                        Notification::make()
                            ->title('Incidencia Cerrada')
                            ->body("El ciclo de la incidencia {$record->codigo} ha sido cerrado comercialmente.")
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
