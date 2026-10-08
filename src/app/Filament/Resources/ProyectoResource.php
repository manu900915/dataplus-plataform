<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProyectoResource\Pages;
use App\Models\Cliente;
use App\Models\ClienteUbicacion;
use App\Models\Item;
use App\Models\Proyecto;
use App\Models\TipoProyecto;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class ProyectoResource extends Resource
{
    protected static ?string $model = Proyecto::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationGroup = 'Proyectos';

    protected static ?string $navigationLabel = 'Proyectos';

    protected static ?string $modelLabel = 'Proyecto';

    protected static ?string $pluralModelLabel = 'Proyectos';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'nombre';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información General')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('codigo')
                            ->label('Código')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->default(fn () => 'PROY-' . now()->year . '-' . str_pad((Proyecto::whereYear('created_at', now()->year)->count() + 1), 4, '0', STR_PAD_LEFT)),

                        Forms\Components\Select::make('tipo_proyecto_id')
                            ->label('Tipo de Proyecto')
                            ->relationship('tipoProyecto', 'nombre')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live(),

                        Forms\Components\Select::make('tipo_seguimiento')
                            ->label('Tipo de Seguimiento')
                            ->options([
                                'instalacion' => 'Instalación (con presupuesto detallado)',
                                'investigacion' => 'I+D (seguimiento Kanban)',
                            ])
                            ->default('instalacion')
                            ->required()
                            ->live()
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('nombre')
                            ->label('Nombre del Proyecto')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('descripcion')
                            ->label('Descripción')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('responsable_id')
                            ->label('Responsable')
                            ->relationship('responsable', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Seleccione un responsable...'),

                        Forms\Components\Select::make('estado')
                            ->label('Estado General')
                            ->options([
                                'borrador' => 'Borrador',
                                'en_progreso' => 'En Progreso',
                                'completado' => 'Completado',
                                'cancelado' => 'Cancelado',
                            ])
                            ->default('borrador')
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state === 'completado') {
                                    $set('estado_kanban', 'completado');
                                } elseif ($state === 'en_progreso') {
                                    $set('estado_kanban', 'en_progreso');
                                } elseif ($state === 'borrador') {
                                    $set('estado_kanban', 'por_hacer');
                                }
                            }),

                        Forms\Components\Select::make('estado_kanban')
                            ->label('Fase en Seguimiento (Kanban)')
                            ->options([
                                'por_hacer' => '📋 Por Hacer',
                                'en_progreso' => '🔄 En Progreso',
                                'en_revision' => '🔍 En Revisión',
                                'completado' => '✅ Completado',
                            ])
                            ->default('por_hacer')
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state === 'completado') {
                                    $set('estado', 'completado');
                                } elseif (in_array($state, ['en_progreso', 'en_revision'])) {
                                    $set('estado', 'en_progreso');
                                }
                            }),

                        Forms\Components\DatePicker::make('fecha_inicio')
                            ->label('Fecha de Inicio')
                            ->native(false),

                        Forms\Components\DatePicker::make('fecha_fin')
                            ->label('Fecha de Fin')
                            ->native(false),
                    ]),

                Forms\Components\Section::make('Cliente y Ubicación')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('cliente_id')
                            ->label('Cliente')
                            ->relationship('cliente', 'nombre')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->placeholder('Seleccione un cliente...'),

                        Forms\Components\Select::make('cliente_ubicacion_id')
                            ->label('Ubicación')
                            ->options(fn (Get $get) =>
                                ClienteUbicacion::where('cliente_id', $get('cliente_id'))
                                    ->pluck('nombre', 'id')
                            )
                            ->searchable()
                            ->preload()
                            ->placeholder('Primero seleccione un cliente...')
                            ->disabled(fn (Get $get) => blank($get('cliente_id'))),
                    ]),

                // PRESUPUESTO - Solo visible si es Instalación
                Forms\Components\Section::make('Presupuesto Detallado')
                    ->description('Agregue las líneas de presupuesto organizadas por categorías independientes')
                    ->visible(fn (Get $get) => $get('tipo_seguimiento') === 'instalacion' || blank($get('tipo_seguimiento')))
                    ->schema([
                        Forms\Components\Tabs::make('categorias')
                            ->tabs([
                                // TAB 1: EQUIPAMIENTO
                                Forms\Components\Tabs\Tab::make('Equipamiento')
                                    ->icon('heroicon-o-cube')
                                    ->badge(fn (Get $get) => count($get('lineasEquipamiento') ?? []) > 0 
                                        ? count($get('lineasEquipamiento')) 
                                        : null)
                                    ->schema([
                                        Forms\Components\Repeater::make('lineasEquipamiento')
                                            ->relationship('lineasEquipamiento')
                                            ->label(false)
                                            ->addActionLabel('Agregar Equipo')
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string =>
                                                ($state['descripcion'] ?? 'Sin descripción') . ' - $' . number_format((float) ($state['subtotal'] ?? ((float) ($state['cantidad'] ?? 1) * (float) ($state['costo_unitario'] ?? 0))), 2)
                                            )
                                            ->defaultItems(0)
                                            ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                                                $data['tipo_linea'] = 'equipamiento';
                                                $cantidad = (float) ($data['cantidad'] ?? 1);
                                                $costo = (float) ($data['costo_unitario'] ?? 0);
                                                $data['subtotal'] = round($cantidad * $costo, 2);
                                                return $data;
                                            })
                                            ->mutateRelationshipDataBeforeSaveUsing(function (array $data): array {
                                                $data['tipo_linea'] = 'equipamiento';
                                                $cantidad = (float) ($data['cantidad'] ?? 1);
                                                $costo = (float) ($data['costo_unitario'] ?? 0);
                                                $data['subtotal'] = round($cantidad * $costo, 2);
                                                return $data;
                                            })
                                            ->schema([
                                                Forms\Components\Grid::make(3)
                                                    ->schema([
                                                        Forms\Components\Hidden::make('tipo_linea')
                                                            ->default('equipamiento')
                                                            ->dehydrated(),

                                                        Forms\Components\Select::make('item_id')
                                                            ->label('Equipo del Inventario')
                                                            ->options(fn () =>
                                                                Item::whereHas('categoria', fn ($q) =>
                                                                    $q->where('activo', true)
                                                                      ->whereIn('tipo', ['equipamiento', 'ambos']))
                                                                    ->orderBy('nombre')
                                                                    ->pluck('nombre', 'id')
                                                                    ->all()
                                                            )
                                                            ->searchable()
                                                            ->preload()
                                                            ->placeholder('Seleccione un equipo...')
                                                            ->required()
                                                            ->live()
                                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                                if ($state) {
                                                                    $item = Item::find($state);
                                                                    if ($item) {
                                                                        $set('descripcion', $item->nombre);
                                                                        $precio = (float) ($item->precio_unitario ?? 0);
                                                                        $set('costo_unitario', $precio);
                                                                        $cantidad = (float) ($get('cantidad') ?? 1);
                                                                        $set('subtotal', round($cantidad * $precio, 2));
                                                                        $set('descontar_inventario', true);
                                                                    }
                                                                }
                                                            }),

                                                        Forms\Components\TextInput::make('descripcion')
                                                            ->label('Descripción')
                                                            ->required()
                                                            ->maxLength(255),
                                                    ]),

                                                Forms\Components\Grid::make(4)
                                                    ->schema([
                                                        Forms\Components\TextInput::make('cantidad')
                                                            ->label('Cantidad')
                                                            ->numeric()
                                                            ->default(1)
                                                            ->minValue(0.001)
                                                            ->required()
                                                            ->live(onBlur: true)
                                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                                $costo = (float) ($get('costo_unitario') ?? 0);
                                                                $set('subtotal', round(((float) $state) * $costo, 2));
                                                            }),

                                                        Forms\Components\TextInput::make('costo_unitario')
                                                            ->label('Costo Unitario')
                                                            ->numeric()
                                                            ->prefix('$')
                                                            ->default(0)
                                                            ->required()
                                                            ->live(onBlur: true)
                                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                                $cantidad = (float) ($get('cantidad') ?? 1);
                                                                $set('subtotal', round($cantidad * ((float) $state), 2));
                                                            }),

                                                        Forms\Components\TextInput::make('subtotal')
                                                            ->label('Subtotal')
                                                            ->numeric()
                                                            ->prefix('$')
                                                            ->readOnly()
                                                            ->dehydrated()
                                                            ->default(0),

                                                        Forms\Components\Toggle::make('descontar_inventario')
                                                            ->label('Descontar del inventario')
                                                            ->default(true),
                                                    ]),
                                            ]),
                                    ]),

                                // TAB 2: MANO DE OBRA
                                Forms\Components\Tabs\Tab::make('Mano de Obra')
                                    ->icon('heroicon-o-user-group')
                                    ->badge(fn (Get $get) => count($get('lineasManoObra') ?? []) > 0 
                                        ? count($get('lineasManoObra')) 
                                        : null)
                                    ->schema([
                                        Forms\Components\Repeater::make('lineasManoObra')
                                            ->relationship('lineasManoObra')
                                            ->label(false)
                                            ->addActionLabel('Agregar Mano de Obra')
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string =>
                                                ($state['descripcion'] ?? 'Sin descripción') . ' - $' . number_format((float) ($state['subtotal'] ?? ((float) ($state['cantidad'] ?? 1) * (float) ($state['costo_unitario'] ?? 0))), 2)
                                            )
                                            ->defaultItems(0)
                                            ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                                                $data['tipo_linea'] = 'mano_obra';
                                                $data['item_id'] = null;
                                                $data['descontar_inventario'] = false;
                                                $cantidad = (float) ($data['cantidad'] ?? 1);
                                                $costo = (float) ($data['costo_unitario'] ?? 0);
                                                $data['subtotal'] = round($cantidad * $costo, 2);
                                                return $data;
                                            })
                                            ->mutateRelationshipDataBeforeSaveUsing(function (array $data): array {
                                                $data['tipo_linea'] = 'mano_obra';
                                                $data['item_id'] = null;
                                                $data['descontar_inventario'] = false;
                                                $cantidad = (float) ($data['cantidad'] ?? 1);
                                                $costo = (float) ($data['costo_unitario'] ?? 0);
                                                $data['subtotal'] = round($cantidad * $costo, 2);
                                                return $data;
                                            })
                                            ->schema([
                                                Forms\Components\Hidden::make('tipo_linea')
                                                    ->default('mano_obra')
                                                    ->dehydrated(),

                                                Forms\Components\TextInput::make('descripcion')
                                                    ->label('Descripción del Servicio / Mano de Obra')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->placeholder('Ej: Instalación y configuración de cámaras, Cableado estructurado...'),

                                                Forms\Components\Grid::make(3)
                                                    ->schema([
                                                        Forms\Components\TextInput::make('cantidad')
                                                            ->label('Cantidad / Horas / Días')
                                                            ->numeric()
                                                            ->default(1)
                                                            ->minValue(0.001)
                                                            ->required()
                                                            ->live(onBlur: true)
                                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                                $costo = (float) ($get('costo_unitario') ?? 0);
                                                                $set('subtotal', round(((float) $state) * $costo, 2));
                                                            }),

                                                        Forms\Components\TextInput::make('costo_unitario')
                                                            ->label('Costo Unitario / Tarifa')
                                                            ->numeric()
                                                            ->prefix('$')
                                                            ->default(0)
                                                            ->required()
                                                            ->live(onBlur: true)
                                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                                $cantidad = (float) ($get('cantidad') ?? 1);
                                                                $set('subtotal', round($cantidad * ((float) $state), 2));
                                                            }),

                                                        Forms\Components\TextInput::make('subtotal')
                                                            ->label('Subtotal')
                                                            ->numeric()
                                                            ->prefix('$')
                                                            ->readOnly()
                                                            ->dehydrated()
                                                            ->default(0),
                                                    ]),
                                            ]),
                                    ]),

                                // TAB 3: MATERIALES
                                Forms\Components\Tabs\Tab::make('Materiales')
                                    ->icon('heroicon-o-archive-box')
                                    ->badge(fn (Get $get) => count($get('lineasMateriales') ?? []) > 0 
                                        ? count($get('lineasMateriales')) 
                                        : null)
                                    ->schema([
                                        Forms\Components\Repeater::make('lineasMateriales')
                                            ->relationship('lineasMateriales')
                                            ->label(false)
                                            ->addActionLabel('Agregar Material')
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string =>
                                                ($state['descripcion'] ?? 'Sin descripción') . ' - $' . number_format((float) ($state['subtotal'] ?? ((float) ($state['cantidad'] ?? 1) * (float) ($state['costo_unitario'] ?? 0))), 2)
                                            )
                                            ->defaultItems(0)
                                            ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                                                $data['tipo_linea'] = 'material';
                                                $cantidad = (float) ($data['cantidad'] ?? 1);
                                                $costo = (float) ($data['costo_unitario'] ?? 0);
                                                $data['subtotal'] = round($cantidad * $costo, 2);
                                                return $data;
                                            })
                                            ->mutateRelationshipDataBeforeSaveUsing(function (array $data): array {
                                                $data['tipo_linea'] = 'material';
                                                $cantidad = (float) ($data['cantidad'] ?? 1);
                                                $costo = (float) ($data['costo_unitario'] ?? 0);
                                                $data['subtotal'] = round($cantidad * $costo, 2);
                                                return $data;
                                            })
                                            ->schema([
                                                Forms\Components\Grid::make(3)
                                                    ->schema([
                                                        Forms\Components\Hidden::make('tipo_linea')
                                                            ->default('material')
                                                            ->dehydrated(),

                                                        Forms\Components\Select::make('item_id')
                                                            ->label('Material del Inventario')
                                                            ->options(fn () =>
                                                                Item::whereHas('categoria', fn ($q) =>
                                                                    $q->where('activo', true)
                                                                      ->whereIn('tipo', ['material', 'ambos']))
                                                                    ->orderBy('nombre')
                                                                    ->pluck('nombre', 'id')
                                                                    ->all()
                                                            )
                                                            ->searchable()
                                                            ->preload()
                                                            ->placeholder('Seleccione un material...')
                                                            ->live()
                                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                                if ($state) {
                                                                    $item = Item::find($state);
                                                                    if ($item) {
                                                                        $set('descripcion', $item->nombre);
                                                                        $precio = (float) ($item->precio_unitario ?? 0);
                                                                        $set('costo_unitario', $precio);
                                                                        $cantidad = (float) ($get('cantidad') ?? 1);
                                                                        $set('subtotal', round($cantidad * $precio, 2));
                                                                        $set('descontar_inventario', true);
                                                                    }
                                                                }
                                                            }),

                                                        Forms\Components\TextInput::make('descripcion')
                                                            ->label('Descripción')
                                                            ->required()
                                                            ->maxLength(255),
                                                    ]),

                                                Forms\Components\Grid::make(4)
                                                    ->schema([
                                                        Forms\Components\TextInput::make('cantidad')
                                                            ->label('Cantidad')
                                                            ->numeric()
                                                            ->default(1)
                                                            ->minValue(0.001)
                                                            ->required()
                                                            ->live(onBlur: true)
                                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                                $costo = (float) ($get('costo_unitario') ?? 0);
                                                                $set('subtotal', round(((float) $state) * $costo, 2));
                                                            }),

                                                        Forms\Components\TextInput::make('costo_unitario')
                                                            ->label('Costo Unitario')
                                                            ->numeric()
                                                            ->prefix('$')
                                                            ->default(0)
                                                            ->required()
                                                            ->live(onBlur: true)
                                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                                $cantidad = (float) ($get('cantidad') ?? 1);
                                                                $set('subtotal', round($cantidad * ((float) $state), 2));
                                                            }),

                                                        Forms\Components\TextInput::make('subtotal')
                                                            ->label('Subtotal')
                                                            ->numeric()
                                                            ->prefix('$')
                                                            ->readOnly()
                                                            ->dehydrated()
                                                            ->default(0),

                                                        Forms\Components\Toggle::make('descontar_inventario')
                                                            ->label('Descontar del inventario')
                                                            ->default(true),
                                                    ]),
                                            ]),
                                    ]),

                                // TAB 4: TRANSPORTE
                                Forms\Components\Tabs\Tab::make('Transporte')
                                    ->icon('heroicon-o-truck')
                                    ->badge(fn (Get $get) => count($get('lineasTransporte') ?? []) > 0 
                                        ? count($get('lineasTransporte')) 
                                        : null)
                                    ->schema([
                                        Forms\Components\Repeater::make('lineasTransporte')
                                            ->relationship('lineasTransporte')
                                            ->label(false)
                                            ->addActionLabel('Agregar Transporte')
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string =>
                                                ($state['descripcion'] ?? 'Sin descripción') . ' - $' . number_format((float) ($state['subtotal'] ?? ((float) ($state['cantidad'] ?? 1) * (float) ($state['costo_unitario'] ?? 0))), 2)
                                            )
                                            ->defaultItems(0)
                                            ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                                                $data['tipo_linea'] = 'transporte';
                                                $data['item_id'] = null;
                                                $data['descontar_inventario'] = false;
                                                $cantidad = (float) ($data['cantidad'] ?? 1);
                                                $costo = (float) ($data['costo_unitario'] ?? 0);
                                                $data['subtotal'] = round($cantidad * $costo, 2);
                                                return $data;
                                            })
                                            ->mutateRelationshipDataBeforeSaveUsing(function (array $data): array {
                                                $data['tipo_linea'] = 'transporte';
                                                $data['item_id'] = null;
                                                $data['descontar_inventario'] = false;
                                                $cantidad = (float) ($data['cantidad'] ?? 1);
                                                $costo = (float) ($data['costo_unitario'] ?? 0);
                                                $data['subtotal'] = round($cantidad * $costo, 2);
                                                return $data;
                                            })
                                            ->schema([
                                                Forms\Components\Hidden::make('tipo_linea')
                                                    ->default('transporte')
                                                    ->dehydrated(),

                                                Forms\Components\TextInput::make('descripcion')
                                                    ->label('Descripción')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->placeholder('Ej: Transporte de materiales, Movilización de brigada...'),

                                                Forms\Components\Grid::make(3)
                                                    ->schema([
                                                        Forms\Components\TextInput::make('cantidad')
                                                            ->label('Viajes / Cantidad')
                                                            ->numeric()
                                                            ->default(1)
                                                            ->minValue(0.001)
                                                            ->required()
                                                            ->live(onBlur: true)
                                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                                $costo = (float) ($get('costo_unitario') ?? 0);
                                                                $set('subtotal', round(((float) $state) * $costo, 2));
                                                            }),

                                                        Forms\Components\TextInput::make('costo_unitario')
                                                            ->label('Costo por Viaje')
                                                            ->numeric()
                                                            ->prefix('$')
                                                            ->default(0)
                                                            ->required()
                                                            ->live(onBlur: true)
                                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                                $cantidad = (float) ($get('cantidad') ?? 1);
                                                                $set('subtotal', round($cantidad * ((float) $state), 2));
                                                            }),

                                                        Forms\Components\TextInput::make('subtotal')
                                                            ->label('Subtotal')
                                                            ->numeric()
                                                            ->prefix('$')
                                                            ->readOnly()
                                                            ->dehydrated()
                                                            ->default(0),
                                                    ]),
                                            ]),
                                    ]),

                                // TAB 5: ALIMENTACIÓN
                                Forms\Components\Tabs\Tab::make('Alimentación')
                                    ->icon('heroicon-o-beaker')
                                    ->badge(fn (Get $get) => count($get('lineasAlimentacion') ?? []) > 0 
                                        ? count($get('lineasAlimentacion')) 
                                        : null)
                                    ->schema([
                                        Forms\Components\Repeater::make('lineasAlimentacion')
                                            ->relationship('lineasAlimentacion')
                                            ->label(false)
                                            ->addActionLabel('Agregar Alimentación')
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string =>
                                                ($state['descripcion'] ?? 'Sin descripción') . ' - $' . number_format((float) ($state['subtotal'] ?? ((float) ($state['cantidad'] ?? 1) * (float) ($state['costo_unitario'] ?? 0))), 2)
                                            )
                                            ->defaultItems(0)
                                            ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                                                $data['tipo_linea'] = 'alimentacion';
                                                $data['item_id'] = null;
                                                $data['descontar_inventario'] = false;
                                                $cantidad = (float) ($data['cantidad'] ?? 1);
                                                $costo = (float) ($data['costo_unitario'] ?? 0);
                                                $data['subtotal'] = round($cantidad * $costo, 2);
                                                return $data;
                                            })
                                            ->mutateRelationshipDataBeforeSaveUsing(function (array $data): array {
                                                $data['tipo_linea'] = 'alimentacion';
                                                $data['item_id'] = null;
                                                $data['descontar_inventario'] = false;
                                                $cantidad = (float) ($data['cantidad'] ?? 1);
                                                $costo = (float) ($data['costo_unitario'] ?? 0);
                                                $data['subtotal'] = round($cantidad * $costo, 2);
                                                return $data;
                                            })
                                            ->schema([
                                                Forms\Components\Hidden::make('tipo_linea')
                                                    ->default('alimentacion')
                                                    ->dehydrated(),

                                                Forms\Components\TextInput::make('descripcion')
                                                    ->label('Descripción')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->placeholder('Ej: Almuerzo (3 técnicos), Dieta de campo...'),

                                                Forms\Components\Grid::make(3)
                                                    ->schema([
                                                        Forms\Components\TextInput::make('cantidad')
                                                            ->label('Raciones / Días')
                                                            ->numeric()
                                                            ->default(1)
                                                            ->minValue(1)
                                                            ->required()
                                                            ->live(onBlur: true)
                                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                                $costo = (float) ($get('costo_unitario') ?? 0);
                                                                $set('subtotal', round(((float) $state) * $costo, 2));
                                                            }),

                                                        Forms\Components\TextInput::make('costo_unitario')
                                                            ->label('Costo por Ración / Día')
                                                            ->numeric()
                                                            ->prefix('$')
                                                            ->default(0)
                                                            ->required()
                                                            ->live(onBlur: true)
                                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                                $cantidad = (float) ($get('cantidad') ?? 1);
                                                                $set('subtotal', round($cantidad * ((float) $state), 2));
                                                            }),

                                                        Forms\Components\TextInput::make('subtotal')
                                                            ->label('Subtotal')
                                                            ->numeric()
                                                            ->prefix('$')
                                                            ->readOnly()
                                                            ->dehydrated()
                                                            ->default(0),
                                                    ]),
                                            ]),
                                    ]),
                            ])
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Información Adicional')
                    ->schema([
                        Forms\Components\Textarea::make('notas')
                            ->label('Notas')
                            ->rows(3),

                        Forms\Components\Placeholder::make('resumen_presupuesto')
                            ->label('Resumen Estimado del Presupuesto')
                            ->content(function (Get $get): HtmlString {
                                $calc = fn ($items) => collect($items ?? [])->sum(fn ($i) => ((float) ($i['cantidad'] ?? 0)) * ((float) ($i['costo_unitario'] ?? 0)));
                                $eq = $calc($get('lineasEquipamiento'));
                                $mo = $calc($get('lineasManoObra'));
                                $mat = $calc($get('lineasMateriales'));
                                $tr = $calc($get('lineasTransporte'));
                                $al = $calc($get('lineasAlimentacion'));
                                $tot = $eq + $mo + $mat + $tr + $al;

                                $html = "<div class='text-xs space-y-1 font-mono text-gray-700 dark:text-gray-300'>
                                    <div>Equipamiento: <strong class='text-primary-600'>$" . number_format($eq, 2) . "</strong></div>
                                    <div>Mano de Obra: <strong class='text-primary-600'>$" . number_format($mo, 2) . "</strong></div>
                                    <div>Materiales: <strong class='text-primary-600'>$" . number_format($mat, 2) . "</strong></div>
                                    <div>Transporte: <strong class='text-primary-600'>$" . number_format($tr, 2) . "</strong></div>
                                    <div>Alimentación: <strong class='text-primary-600'>$" . number_format($al, 2) . "</strong></div>
                                    <div class='pt-2 border-t border-gray-200 dark:border-gray-700 font-bold text-sm text-emerald-600 dark:text-emerald-400'>Total Estimado: $" . number_format($tot, 2) . "</div>
                                </div>";

                                return new HtmlString($html);
                            })
                            ->visible(fn (Get $get) => $get('tipo_seguimiento') === 'instalacion' || blank($get('tipo_seguimiento'))),
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
                    ->weight('font-bold'),

                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('tipoProyecto.nombre')
                    ->label('Tipo')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tipo_seguimiento')
                    ->label('Seguimiento')
                    ->badge()
                    ->default('instalacion')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'instalacion' => 'Instalación',
                        'investigacion' => 'I+D',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'instalacion' => 'primary',
                        'investigacion' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('cliente.nombre')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'borrador' => 'gray',
                        'en_progreso' => 'info',
                        'completado' => 'success',
                        'cancelado' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'borrador' => 'Borrador',
                        'en_progreso' => 'En Progreso',
                        'completado' => 'Completado',
                        'cancelado' => 'Cancelado',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('estado_kanban')
                    ->label('Fase Seguimiento')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'por_hacer' => '📋 Por Hacer',
                        'en_progreso' => '🔄 En Progreso',
                        'en_revision' => '🔍 En Revisión',
                        'completado' => '✅ Completado',
                        default => '📋 Por Hacer',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'por_hacer' => 'gray',
                        'en_progreso' => 'warning',
                        'en_revision' => 'info',
                        'completado' => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('presupuesto_total')
                    ->label('Presupuesto')
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('fecha_inicio')
                    ->label('Inicio')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('fecha_fin')
                    ->label('Fin')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipo_proyecto_id')
                    ->label('Tipo de Proyecto')
                    ->relationship('tipoProyecto', 'nombre'),

                Tables\Filters\SelectFilter::make('tipo_seguimiento')
                    ->label('Tipo de Seguimiento')
                    ->options([
                        'instalacion' => 'Instalación',
                        'investigacion' => 'I+D',
                    ]),

                Tables\Filters\SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'borrador' => 'Borrador',
                        'en_progreso' => 'En Progreso',
                        'completado' => 'Completado',
                        'cancelado' => 'Cancelado',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('exportarExcel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->label('Exportar Excel')
                    ->color('success')
                    ->visible(fn (Proyecto $record): bool => $record->tipo_seguimiento === 'instalacion')
                    ->url(fn (Proyecto $record): string => route('proyectos.presupuesto.excel', $record))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('exportarPDF')
                    ->icon('heroicon-o-document-text')
                    ->label('Exportar PDF')
                    ->color('danger')
                    ->visible(fn (Proyecto $record): bool => $record->tipo_seguimiento === 'instalacion')
                    ->url(fn (Proyecto $record): string => route('proyectos.presupuesto.pdf', $record))
                    ->openUrlInNewTab(),

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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProyectos::route('/'),
            'create' => Pages\CreateProyecto::route('/create'),
            'edit' => Pages\EditProyecto::route('/{record}/edit'),
            'kanban' => Pages\KanbanProyectos::route('/kanban'),
        ];
    }
}
