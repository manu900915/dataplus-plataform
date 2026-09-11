<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ItemResource\Pages;
use App\Models\Almacen;
use App\Models\Item;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ItemResource extends Resource
{
    protected static ?string $model = Item::class;
    protected static ?string $navigationGroup = 'Inventario';
    protected static ?string $navigationLabel = 'Items';
    protected static ?string $modelLabel = 'Item';
    protected static ?string $pluralModelLabel = 'Items';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Información General')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('codigo')
                        ->label('Código')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(20)
                        ->prefix('INV-')
                        ->afterStateHydrated(function (Forms\Components\TextInput $component, $state) {
                            if (is_string($state) && str_starts_with($state, 'INV-')) {
                                $component->state(substr($state, 4));
                            }
                        })
                        ->dehydrateStateUsing(function ($state) {
                            if (is_string($state) && !str_starts_with($state, 'INV-')) {
                                return 'INV-' . $state;
                            }
                            return $state;
                        })
                        ->default(fn () => str_pad((Item::count() + 1), 6, '0', STR_PAD_LEFT))
                        ->helperText('El prefijo "INV-" es fijo.'),

                    Forms\Components\TextInput::make('numero_serie')
                        ->label('Número de Serie')
                        ->maxLength(100)
                        ->unique(ignoreRecord: true)
                        ->placeholder('Ej: SN123456789')
                        ->visible(fn (Get $get) => $get('es_equipamiento')),

                    Forms\Components\TextInput::make('nombre')
                        ->label('Nombre')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Forms\Components\Select::make('categoria_id')
                        ->label('Categoría')
                        ->relationship('categoria', 'nombre')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Forms\Components\Textarea::make('descripcion')
                        ->label('Descripción')
                        ->columnSpanFull()
                        ->rows(3),
                ]),

            Forms\Components\Section::make('Precios')
                ->description('El precio de venta se calcula automáticamente basado en el costo y el margen')
                ->columns(3)
                ->schema([
                    Forms\Components\TextInput::make('precio_costo')
                        ->label('Precio de Costo')
                        ->numeric()
                        ->prefix('$')
                        ->required()
                        ->default(0)
                        ->minValue(0)
                        ->live()
                        ->afterStateUpdated(function ($state, Set $set, Get $get) {
                            $margen = $get('margen_ganancia') ?? 60;
                            $precioVenta = $state * (1 + ($margen / 100));
                            $set('precio_venta', round($precioVenta, 2));
                        }),

                    Forms\Components\TextInput::make('margen_ganancia')
                        ->label('Margen de Ganancia (%)')
                        ->numeric()
                        ->default(60)
                        ->minValue(0)
                        ->maxValue(1000)
                        ->suffix('%')
                        ->live()
                        ->afterStateUpdated(function ($state, Set $set, Get $get) {
                            $costo = $get('precio_costo');
                            if ($costo) {
                                $precioVenta = $costo * (1 + ($state / 100));
                                $set('precio_venta', round($precioVenta, 2));
                            }
                        }),

                    Forms\Components\TextInput::make('precio_venta')
                        ->label('Precio de Venta')
                        ->numeric()
                        ->prefix('$')
                        ->required()
                        ->default(0)
                        ->disabled()
                        ->helperText('Se calcula automáticamente'),
                ]),

            Forms\Components\Section::make('Control de Stock')
                ->columns(3)
                ->schema([
                    Forms\Components\TextInput::make('unidad_medida')
                        ->label('Unidad de Medida')
                        ->required()
                        ->default('u')
                        ->helperText('u=unidades, m=metros, kg=kilos, L=litros'),

                    Forms\Components\TextInput::make('stock_actual')
                        ->label('Stock Actual')
                        ->numeric()
                        ->default(0)
                        ->minValue(0)
                        ->disabled(fn (Get $get) => $get('es_equipamiento'))
                        ->helperText(fn (Get $get) => $get('es_equipamiento') 
                            ? 'Para equipamiento, use Movimientos para agregar/quitar' 
                            : 'Editable solo para materiales'),

                    Forms\Components\TextInput::make('stock_minimo')
                        ->label('Stock Mínimo')
                        ->numeric()
                        ->default(0)
                        ->minValue(0)
                        ->helperText('Alerta cuando el stock baje de este valor'),
                ]),

            Forms\Components\Section::make('Ubicación Inicial')
                ->description('Si es la primera vez que ingresas este item, selecciona el almacén y la cantidad inicial')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('almacen_inicial_id')
                        ->label('Almacén')
                        ->options(fn () => \App\Models\Almacen::pluck('nombre', 'id'))
                        ->searchable()
                        ->preload()
                        ->placeholder('Seleccione almacén...'),

                    Forms\Components\TextInput::make('stock_inicial')
                        ->label('Cantidad Inicial')
                        ->numeric()
                        ->default(0)
                        ->minValue(0)
                        ->helperText('Dejar en 0 si no hay stock inicial'),
                ]),

            Forms\Components\Section::make('Tipo de Item')
                ->columns(2)
                ->schema([
                    Forms\Components\Toggle::make('es_equipamiento')
                        ->label('Es Equipamiento')
                        ->helperText('Equipamiento: routers, PCs, DVR (se controlan individualmente con número de serie). Material: cables, conectores (se controlan por cantidad).'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('codigo')
                    ->label('Código')
                    ->searchable()
                    ->sortable()
                    ->weight('font-bold'),

                TextColumn::make('numero_serie')
                    ->label('N° Serie')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->badge()
                    ->color('info'),

                TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(40),

                TextColumn::make('categoria.nombre')
                    ->label('Categoría')
                    ->badge()
                    ->sortable(),

                TextColumn::make('stock_actual')
                    ->label('Stock')
                    ->sortable()
                    ->suffix(fn (Item $r) => " {$r->unidad_medida}")
                    ->color(fn (Item $r) => match ($r->stock_estado) {
                        'negativo' => 'danger',
                        'bajo' => 'warning',
                        default => 'success',
                    })
                    ->badge(),

                TextColumn::make('stock_minimo')
                    ->label('Mínimo')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('precio_costo')
                    ->label('Costo')
                    ->money('USD')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('precio_venta')
                    ->label('Venta')
                    ->money('USD')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('margen_ganancia')
                    ->label('Margen')
                    ->formatStateUsing(fn ($state) => number_format($state, 0) . '%')
                    ->toggleable(),

                TextColumn::make('valor_inventario')
                    ->label('Valor Total')
                    ->money('USD')
                    ->toggleable(),

                IconColumn::make('es_equipamiento')
                    ->label('Tipo')
                    ->boolean()
                    ->trueIcon('heroicon-o-cube')
                    ->falseIcon('heroicon-o-archive-box'),
            ])
            ->filters([
                SelectFilter::make('categoria_id')
                    ->label('Categoría')
                    ->relationship('categoria', 'nombre'),

                SelectFilter::make('es_equipamiento')
                    ->label('Tipo')
                    ->options([
                        true => 'Equipamiento',
                        false => 'Material/Insumo',
                    ]),

                Tables\Filters\TernaryFilter::make('stock_bajo')
                    ->label('Stock bajo')
                    ->queries(
                        true: fn ($query) => $query->whereColumn('stock_actual', '<=', 'stock_minimo'),
                        false: fn ($query) => $query->whereColumn('stock_actual', '>', 'stock_minimo'),
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('ajustar')
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->label('Movimiento')
                    ->url(fn (Item $r) => InventarioMovimientoResource::getUrl('create', ['item_id' => $r->id])),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListItems::route('/'),
            'create' => Pages\CreateItem::route('/create'),
            'edit' => Pages\EditItem::route('/{record}/edit'),
        ];
    }
}