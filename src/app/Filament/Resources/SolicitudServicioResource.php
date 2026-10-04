<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SolicitudServicioResource\Pages;
use App\Models\Cliente;
use App\Models\ClienteUbicacion;
use App\Models\Proyecto;
use App\Models\SolicitudServicio;
use App\Models\TipoProyecto;
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

class SolicitudServicioResource extends Resource
{
    protected static ?string $model = SolicitudServicio::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-plus';
    protected static ?string $navigationGroup = 'Operaciones';
    protected static ?string $modelLabel = 'Solicitud Comercial';
    protected static ?string $pluralModelLabel = 'Solicitudes de Proyectos';
    protected static ?int $navigationSort = 1;
    protected static ?string $recordTitleAttribute = 'codigo';

    public static function getNavigationBadge(): ?string
    {
        try {
            $pendientes = static::getModel()::where('estado', 'Pendiente_Aprobacion')->count();
            return $pendientes > 0 ? (string) $pendientes : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Mesa de Entrada Comercial')
                    ->description('Registro de nuevo requerimiento o solicitud de proyecto ingresado por Comercial.')
                    ->columns(3)
                    ->schema([
                        Forms\Components\Select::make('cliente_id')
                            ->label('Cliente')
                            ->relationship('cliente', 'nombre')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('cliente_ubicacion_id', null)),

                        Forms\Components\Select::make('cliente_ubicacion_id')
                            ->label('Ubicación / Sede')
                            ->options(function (Get $get) {
                                $clienteId = $get('cliente_id');
                                if (!$clienteId) return [];
                                return ClienteUbicacion::where('cliente_id', $clienteId)->pluck('nombre', 'id');
                            })
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('tipo_proyecto_id')
                            ->label('Tipo de Proyecto Estimado')
                            ->relationship('tipoProyecto', 'nombre')
                            ->searchable()
                            ->preload(),

                        Forms\Components\TextInput::make('titulo')
                            ->label('Título de la Solicitud')
                            ->columnSpan(2)
                            ->required()
                            ->placeholder('Ej: Nueva instalación de CCTV 16 cámaras y enlace inalámbrico'),

                        Forms\Components\Select::make('prioridad')
                            ->options([
                                'Baja'    => 'Baja',
                                'Media'   => 'Media',
                                'Alta'    => 'Alta',
                                'Urgente' => 'Urgente',
                            ])
                            ->default('Media')
                            ->required(),

                        Forms\Components\Textarea::make('descripcion')
                            ->label('Alcance y Requerimientos del Cliente')
                            ->columnSpanFull()
                            ->required()
                            ->rows(3)
                            ->placeholder('Detalle las necesidades del cliente, requerimientos expresados y cualquier observación relevante...'),

                        Forms\Components\TextInput::make('presupuesto_estimado')
                            ->label('Presupuesto Estimado / Cotización Preliminar ($)')
                            ->numeric()
                            ->prefix('$')
                            ->placeholder('0.00'),

                        Forms\Components\Select::make('estado')
                            ->label('Estado')
                            ->options([
                                'Pendiente_Aprobacion' => 'Pendiente de Aprobación por Supervisor',
                                'Aprobada'             => 'Aprobada',
                                'Convertida_Proyecto'  => 'Convertida en Proyecto Oficial',
                                'Rechazada'            => 'Rechazada',
                            ])
                            ->default('Pendiente_Aprobacion')
                            ->required(),
                    ]),

                Forms\Components\Section::make('Dictamen y Aprobación del Supervisor')
                    ->visible(fn (string $context): bool => $context === 'edit')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('supervisor_id')
                            ->label('Supervisor Revisor')
                            ->relationship('supervisor', 'name')
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('fecha_aprobacion')
                            ->label('Fecha de Evaluación')
                            ->disabled(),

                        Forms\Components\Textarea::make('notas_supervisor')
                            ->label('Notas Técnicas del Supervisor')
                            ->columnSpanFull()
                            ->rows(2),

                        Forms\Components\Select::make('proyecto_id')
                            ->label('Proyecto Oficial Generado')
                            ->relationship('proyecto', 'nombre')
                            ->disabled(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('codigo')
                    ->label('Código')
                    ->weight('font-bold')
                    ->searchable()
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->colors([
                        'warning' => 'Pendiente_Aprobacion',
                        'info'    => 'Aprobada',
                        'success' => 'Convertida_Proyecto',
                        'danger'  => 'Rechazada',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'Pendiente_Aprobacion' => 'Pendiente Supervisor',
                        'Aprobada'             => 'Aprobada',
                        'Convertida_Proyecto'  => 'Proyecto Creado',
                        'Rechazada'            => 'Rechazada',
                        default                => $state,
                    }),

                Tables\Columns\TextColumn::make('titulo')
                    ->label('Requerimiento')
                    ->searchable()
                    ->limit(32)
                    ->tooltip(fn ($record) => $record->titulo),

                Tables\Columns\TextColumn::make('cliente.nombre')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable()
                    ->weight('font-semibold'),

                Tables\Columns\TextColumn::make('tipoProyecto.nombre')
                    ->label('Tipo')
                    ->placeholder('General'),

                Tables\Columns\TextColumn::make('presupuesto_estimado')
                    ->label('Presupuesto')
                    ->money('USD')
                    ->sortable()
                    ->placeholder('$0.00'),

                Tables\Columns\TextColumn::make('comercial.name')
                    ->label('Comercial')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('proyecto.codigo')
                    ->label('Proyecto Asignado')
                    ->badge()
                    ->color('success')
                    ->placeholder('Sin generar')
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha Solicitud')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'Pendiente_Aprobacion' => 'Pendiente Supervisor',
                        'Aprobada'             => 'Aprobada',
                        'Convertida_Proyecto'  => 'Proyecto Creado',
                        'Rechazada'            => 'Rechazada',
                    ]),
                Tables\Filters\SelectFilter::make('cliente_id')
                    ->relationship('cliente', 'nombre')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                // ─── Acción Clave: Supervisor aprueba y crea Proyecto ───
                Tables\Actions\Action::make('crear_proyecto')
                    ->label('Aprobar y Crear Proyecto')
                    ->icon('heroicon-m-rocket-launch')
                    ->color('success')
                    ->visible(fn (SolicitudServicio $record) => $record->estado === 'Pendiente_Aprobacion')
                    ->form([
                        Forms\Components\TextInput::make('nombre_proyecto')
                            ->label('Nombre Oficial del Proyecto')
                            ->required()
                            ->default(fn (SolicitudServicio $record) => $record->titulo),

                        Forms\Components\Select::make('tipo_proyecto_id')
                            ->label('Tipo de Proyecto')
                            ->options(TipoProyecto::pluck('nombre', 'id'))
                            ->default(fn (SolicitudServicio $record) => $record->tipo_proyecto_id)
                            ->required(),

                        Forms\Components\Select::make('responsable_id')
                            ->label('Especialista / Responsable del Proyecto')
                            ->options(User::role(['Supervisor', 'Técnico', 'Administrador'])->pluck('name', 'id'))
                            ->searchable()
                            ->required(),

                        Forms\Components\DatePicker::make('fecha_inicio')
                            ->label('Fecha de Inicio Prevista')
                            ->default(now())
                            ->required(),

                        Forms\Components\DatePicker::make('fecha_fin')
                            ->label('Fecha de Entrega Estimada')
                            ->default(now()->addDays(15)),

                        Forms\Components\TextInput::make('presupuesto_total')
                            ->label('Presupuesto Aprobado ($)')
                            ->numeric()
                            ->prefix('$')
                            ->default(fn (SolicitudServicio $record) => $record->presupuesto_estimado ?? 0),

                        Forms\Components\Textarea::make('notas_supervisor')
                            ->label('Directivas Técnicas para el Especialista')
                            ->rows(2)
                            ->placeholder('Instrucciones iniciales para la brigada...'),
                    ])
                    ->action(function (SolicitudServicio $record, array $data) {
                        // 1. Generar código automático del proyecto
                        $year = now()->year;
                        $count = Proyecto::whereYear('created_at', $year)->count() + 1;
                        $codigoProyecto = "PRJ-{$year}-" . str_pad($count, 4, '0', STR_PAD_LEFT);

                        // 2. Crear Proyecto oficial
                        $proyecto = Proyecto::create([
                            'codigo'               => $codigoProyecto,
                            'nombre'               => $data['nombre_proyecto'],
                            'descripcion'          => $record->descripcion,
                            'tipo_proyecto_id'     => $data['tipo_proyecto_id'],
                            'cliente_id'           => $record->cliente_id,
                            'cliente_ubicacion_id' => $record->cliente_ubicacion_id,
                            'responsable_id'       => $data['responsable_id'],
                            'fecha_inicio'         => $data['fecha_inicio'],
                            'fecha_fin'            => $data['fecha_fin'],
                            'presupuesto_total'    => $data['presupuesto_total'] ?? 0,
                            'notas'                => $data['notas_supervisor'] ?? null,
                            'estado'               => 'borrador',
                        ]);

                        // 3. Actualizar la Solicitud
                        $record->update([
                            'estado'           => 'Convertida_Proyecto',
                            'proyecto_id'      => $proyecto->id,
                            'supervisor_id'    => auth()->id(),
                            'fecha_aprobacion' => now(),
                            'notas_supervisor' => $data['notas_supervisor'] ?? null,
                        ]);

                        Notification::make()
                            ->title('Proyecto Creado Exitosamente')
                            ->body("Se generó el proyecto {$proyecto->codigo} y fue asignado al especialista.")
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
            'index'  => Pages\ListSolicitudServicios::route('/'),
            'create' => Pages\CreateSolicitudServicio::route('/create'),
            'edit'   => Pages\EditSolicitudServicio::route('/{record}/edit'),
        ];
    }
}
