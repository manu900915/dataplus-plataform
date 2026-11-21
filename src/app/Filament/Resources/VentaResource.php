<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VentaResource\Pages;
use App\Filament\Resources\VentaResource\Widgets\VentaStatsOverview;
use App\Models\Almacen;
use App\Models\Cliente;
use App\Models\Item;
use App\Models\Venta;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class VentaResource extends Resource
{
    protected static ?string $model = Venta::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Finanzas';
    protected static ?string $navigationLabel = 'Ventas';
    protected static ?string $modelLabel = 'Venta';
    protected static ?string $pluralModelLabel = 'Ventas';
    protected static ?int $navigationSort = 1;
    protected static ?string $recordTitleAttribute = 'codigo';

    public static function canAccess(): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        if (method_exists($user, 'hasAnyRole')) {
            return $user->hasAnyRole([
                'Administrador',
                'Vendedor',
                'Contable',
                'Supervisor',
                'Comercial',
                'admin',
                'vendedor',
                'contable',
                'supervisor',
                'comercial',
            ]);
        }

        return true;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['cliente', 'almacen', 'vendedor', 'items.item', 'items.almacen']);
    }

    /**
     * Recalcula subtotal y ganancia de una fila usando costo y margen (o precio personalizado).
     */
    protected static function recalcularFilaDesdeMargen(
        float $cantidad,
        float $costo,
        float $margen,
        Set $set
    ): void {
        $precio = round($costo * (1 + ($margen / 100)), 2);
        $subtotal = round(max(0, $cantidad) * $precio, 2);
        $ganancia = round(($precio - $costo) * max(0, $cantidad), 2);

        $set('precio_unitario', $precio);
        $set('subtotal', $subtotal);
        $set('ganancia_linea', $ganancia);
    }

    /**
     * Recalcula el arreglo completo de items al cambiar tasa de cambio o margen global.
     */
    protected static function recalcularTodasLasFilas(
        array $rows,
        float $tasaCambio,
        ?float $margenGlobal,
        ?int $almacenDefaultId,
        bool $recalcularCostoDesdeItem = false
    ): array {
        $tasa = $tasaCambio > 0 ? $tasaCambio : 1.0;

        foreach ($rows as $key => $row) {
            $itemId = $row['item_id'] ?? null;
            $item = $itemId ? Item::find($itemId) : null;
            $cantidad = max(0, (float) ($row['cantidad'] ?? 1));

            if ($recalcularCostoDesdeItem && $item) {
                $costoBase = (float) ($item->precio_costo ?: $item->precio_unitario ?: 0);
                $costo = round($costoBase * $tasa, 2);
            } else {
                $costo = max(0, (float) ($row['precio_costo_unitario'] ?? 0));
            }

            $margen = $margenGlobal !== null
                ? $margenGlobal
                : (float) ($row['margen_ganancia'] ?? ($item?->margen_ganancia ?? 60));

            $precio = round($costo * (1 + ($margen / 100)), 2);
            $subtotal = round($cantidad * $precio, 2);
            $ganancia = round(($precio - $costo) * $cantidad, 2);

            $rows[$key]['almacen_id'] = $row['almacen_id'] ?: $almacenDefaultId;
            $rows[$key]['precio_costo_unitario'] = $costo;
            $rows[$key]['margen_ganancia'] = round($margen, 2);
            $rows[$key]['precio_unitario'] = $precio;
            $rows[$key]['subtotal'] = $subtotal;
            $rows[$key]['ganancia_linea'] = $ganancia;
        }

        return $rows;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Datos de la Venta & Forma de Pago')
                    ->description('Selecciona el cliente, almacén de salida y moneda / método de cobro (CUP, USD, Transferencia CUP o Tarjeta Clásica).')
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('codigo')
                            ->label('Código de Venta')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(40)
                            ->default(fn () => Venta::generarCodigo())
                            ->readOnly()
                            ->dehydrated(),

                        Forms\Components\DateTimePicker::make('fecha_venta')
                            ->label('Fecha de Venta')
                            ->required()
                            ->default(fn () => now()),

                        Forms\Components\Select::make('estado')
                            ->label('Estado de la Venta')
                            ->options([
                                'completada' => 'Completada (Descuenta Almacén)',
                                'pendiente' => 'Pendiente (Reserva sin descontar)',
                                'anulada' => 'Anulada (Stock devuelto)',
                            ])
                            ->default('completada')
                            ->required()
                            ->live()
                            ->disabled(fn (?Venta $record) => $record?->estado === 'anulada')
                            ->dehydrated(),

                        Forms\Components\Select::make('cliente_id')
                            ->label('Cliente Registrado (Opcional)')
                            ->relationship('cliente', 'nombre')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->placeholder('Venta directa / Consumidor Final')
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set) {
                                if ($state) {
                                    $cliente = Cliente::find($state);
                                    if ($cliente) {
                                        $set('cliente_nombre', $cliente->nombre);
                                    }
                                } else {
                                    $set('cliente_nombre', 'Consumidor Final');
                                }
                            }),

                        Forms\Components\TextInput::make('cliente_nombre')
                            ->label('Nombre en Factura / Comprobante')
                            ->required()
                            ->default('Consumidor Final')
                            ->maxLength(255),

                        Forms\Components\Select::make('almacen_id')
                            ->label('Almacén de Despacho Principal')
                            ->relationship('almacen', 'nombre')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->default(fn () => Almacen::orderBy('id')->value('id'))
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                $rows = $get('items') ?? [];
                                if (!is_array($rows) || !$state) {
                                    return;
                                }
                                foreach ($rows as $key => $row) {
                                    if (blank($row['almacen_id'] ?? null)) {
                                        $rows[$key]['almacen_id'] = $state;
                                    }
                                }
                                $set('items', $rows);
                            })
                            ->helperText('Todo lo vendido se descuenta automáticamente de este almacén (o del elegido por línea).'),

                        Forms\Components\Select::make('metodo_pago')
                            ->label('Método / Moneda de Pago')
                            ->options(Venta::METODOS_PAGO)
                            ->default('CUP')
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                $moneda = Venta::monedaDesdeMetodo((string) $state);
                                $set('moneda', $moneda);

                                $tasaActual = (float) ($get('tasa_cambio') ?? 1);
                                $nuevaTasa = $moneda === 'USD' ? 1.0 : ($tasaActual > 0 ? $tasaActual : 1.0);
                                if ($moneda === 'USD') {
                                    $set('tasa_cambio', 1);
                                }

                                $margenGlobalRaw = $get('margen_ganancia_global');
                                $margenGlobal = filled($margenGlobalRaw) ? (float) $margenGlobalRaw : null;

                                $rows = self::recalcularTodasLasFilas(
                                    $get('items') ?? [],
                                    $nuevaTasa,
                                    $margenGlobal,
                                    $get('almacen_id') ? (int) $get('almacen_id') : null,
                                    recalcularCostoDesdeItem: true
                                );
                                $set('items', $rows);
                            }),

                        Forms\Components\TextInput::make('moneda')
                            ->label('Moneda')
                            ->default('CUP')
                            ->readOnly()
                            ->dehydrated(),

                        Forms\Components\TextInput::make('tasa_cambio')
                            ->label('Tasa de Conversión (si costo base es USD)')
                            ->numeric()
                            ->default(1)
                            ->minValue(0.0001)
                            ->step(0.01)
                            ->visible(fn (Get $get) => Venta::monedaDesdeMetodo((string) ($get('metodo_pago') ?? 'CUP')) === 'CUP')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                $tasa = max(0.0001, (float) ($state ?? 1));
                                $margenGlobalRaw = $get('margen_ganancia_global');
                                $margenGlobal = filled($margenGlobalRaw) ? (float) $margenGlobalRaw : null;

                                $rows = self::recalcularTodasLasFilas(
                                    $get('items') ?? [],
                                    $tasa,
                                    $margenGlobal,
                                    $get('almacen_id') ? (int) $get('almacen_id') : null,
                                    recalcularCostoDesdeItem: true
                                );
                                $set('items', $rows);
                            })
                            ->helperText('Deja 1 si tus costos ya están en CUP, o indica ej. 350 para convertir desde USD.'),

                        Forms\Components\TextInput::make('referencia_pago')
                            ->label('N° Transferencia / Autorización Tarjeta')
                            ->maxLength(120)
                            ->placeholder('Ej: TRF-839201 o Auth 4412')
                            ->visible(fn (Get $get) => in_array($get('metodo_pago'), ['Transferencia CUP', 'Tarjeta Clasica'], true))
                            ->columnSpan(2),
                    ]),

                Forms\Components\Section::make('Margen de Ganancia & Descuento sobre la Venta')
                    ->description('Cambia el margen de ganancia global para toda la venta o ajústalo individualmente en cada producto.')
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('margen_ganancia_global')
                            ->label('Margen de Ganancia sobre la Venta (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(1000)
                            ->suffix('%')
                            ->placeholder('Por defecto: margen del producto')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                $margenGlobal = filled($state) ? (float) $state : null;
                                $tasa = max(0.0001, (float) ($get('tasa_cambio') ?? 1));

                                $rows = self::recalcularTodasLasFilas(
                                    $get('items') ?? [],
                                    $tasa,
                                    $margenGlobal,
                                    $get('almacen_id') ? (int) $get('almacen_id') : null,
                                    recalcularCostoDesdeItem: false
                                );
                                $set('items', $rows);
                            })
                            ->helperText('Si introduces un % aquí, recalcula el precio de todos los productos de la venta.'),

                        Forms\Components\TextInput::make('descuento')
                            ->label('Descuento Adicional')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->prefix(fn (Get $get) => $get('moneda') === 'USD' ? 'USD $' : 'CUP $')
                            ->live(onBlur: true),

                        Forms\Components\Placeholder::make('resumen_margen_info')
                            ->label('Resumen Rápido')
                            ->content(function (Get $get): HtmlString {
                                $moneda = $get('moneda') ?? 'CUP';
                                $rows = $get('items') ?? [];
                                $costo = 0.0;
                                $sub = 0.0;
                                foreach ($rows as $row) {
                                    $qty = max(0, (float) ($row['cantidad'] ?? 0));
                                    $c = max(0, (float) ($row['precio_costo_unitario'] ?? 0));
                                    $p = max(0, (float) ($row['precio_unitario'] ?? 0));
                                    $costo += $qty * $c;
                                    $sub += $qty * $p;
                                }
                                $desc = max(0, (float) ($get('descuento') ?? 0));
                                $total = max(0, $sub - $desc);
                                $ganancia = $total - $costo;
                                $margenReal = $costo > 0 ? ($ganancia / $costo) * 100 : 0;

                                $color = $ganancia >= 0 ? '#10b981' : '#ef4444';
                                return new HtmlString(
                                    '<div style="font-size:0.875rem;line-height:1.4;">'
                                    . '<div>Total: <strong>$' . number_format($total, 2) . ' ' . e($moneda) . '</strong></div>'
                                    . '<div style="color:' . $color . ';">Ganancia: <strong>$' . number_format($ganancia, 2) . ' ' . e($moneda) . '</strong> (' . number_format($margenReal, 1) . '%)</div>'
                                    . '</div>'
                                );
                            }),
                    ]),

                Forms\Components\Section::make('Productos Vendidos (Correlación con Almacén)')
                    ->description('Al completar la venta, cada línea genera automáticamente una salida de inventario en el almacén seleccionado.')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->label('Productos de la Venta')
                            ->relationship()
                            ->defaultItems(1)
                            ->minItems(1)
                            ->addActionLabel('Agregar Producto')
                            ->columns(12)
                            ->schema([
                                Forms\Components\Placeholder::make('preview_imagen')
                                    ->label('Foto')
                                    ->columnSpan(2)
                                    ->content(function (Get $get): HtmlString {
                                        $itemId = $get('item_id');
                                        $item = $itemId ? Item::find($itemId) : null;

                                        if ($item && $item->imagen_url) {
                                            return new HtmlString(
                                                '<img src="' . e($item->imagen_url) . '" alt="' . e($item->nombre) . '" style="width:52px;height:52px;object-fit:cover;border-radius:8px;border:1px solid rgba(148,163,184,0.35);" />'
                                            );
                                        }

                                        return new HtmlString(
                                            '<div style="width:52px;height:52px;border-radius:8px;border:1px dashed rgba(148,163,184,0.4);display:flex;align-items:center;justify-content:center;font-size:11px;color:#94a3b8;">Sin foto</div>'
                                        );
                                    }),

                                Forms\Components\Select::make('item_id')
                                    ->label('Producto')
                                    ->relationship('item', 'nombre')
                                    ->getOptionLabelFromRecordUsing(function (Item $r): string {
                                        return "{$r->codigo} — {$r->nombre} (Stock: {$r->stock_actual} {$r->unidad_medida} | Costo: \${$r->precio_costo})";
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->columnSpan(5)
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        if (!$state) {
                                            return;
                                        }

                                        $item = Item::find($state);
                                        if (!$item) {
                                            return;
                                        }

                                        $almacenDefault = $get('../../almacen_id');
                                        if (!$get('almacen_id') && $almacenDefault) {
                                            $set('almacen_id', $almacenDefault);
                                        }

                                        $moneda = Venta::monedaDesdeMetodo((string) ($get('../../metodo_pago') ?? 'CUP'));
                                        $tasa = $moneda === 'CUP'
                                            ? max(0.0001, (float) ($get('../../tasa_cambio') ?? 1))
                                            : 1.0;

                                        $costoBase = (float) ($item->precio_costo ?: $item->precio_unitario ?: 0);
                                        $costo = round($costoBase * $tasa, 2);

                                        $margenGlobalRaw = $get('../../margen_ganancia_global');
                                        $margen = filled($margenGlobalRaw)
                                            ? (float) $margenGlobalRaw
                                            : (float) ($item->margen_ganancia ?? 60);

                                        $cantidad = max(0.001, (float) ($get('cantidad') ?: 1));

                                        $set('precio_costo_unitario', $costo);
                                        $set('margen_ganancia', round($margen, 2));
                                        self::recalcularFilaDesdeMargen($cantidad, $costo, $margen, $set);
                                    }),

                                Forms\Components\Select::make('almacen_id')
                                    ->label('Almacén')
                                    ->relationship('almacen', 'nombre')
                                    ->default(fn (Get $get) => $get('../../almacen_id') ?: Almacen::orderBy('id')->value('id'))
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->columnSpan(3),

                                Forms\Components\Placeholder::make('stock_disponible_info')
                                    ->label('Disponible')
                                    ->columnSpan(2)
                                    ->content(function (Get $get): HtmlString {
                                        $itemId = $get('item_id');
                                        if (!$itemId) {
                                            return new HtmlString('<span style="color:#94a3b8;font-size:12px;">—</span>');
                                        }
                                        $item = Item::find($itemId);
                                        if (!$item) {
                                            return new HtmlString('<span style="color:#94a3b8;font-size:12px;">—</span>');
                                        }
                                        $almacenId = $get('almacen_id') ?: $get('../../almacen_id');
                                        $stockAlm = $almacenId ? $item->stockEnAlmacen((int) $almacenId) : (float) $item->stock_actual;
                                        $qty = (float) ($get('cantidad') ?? 0);
                                        $ok = $stockAlm >= $qty && (float) $item->stock_actual >= $qty;
                                        $color = $ok ? '#10b981' : '#ef4444';

                                        return new HtmlString(
                                            '<div style="font-size:12px;font-weight:700;color:' . $color . ';">'
                                            . number_format($stockAlm, 2) . ' ' . e($item->unidad_medida)
                                            . '<div style="font-size:10px;font-weight:400;color:#94a3b8;">Global: ' . number_format((float) $item->stock_actual, 2) . '</div>'
                                            . '</div>'
                                        );
                                    }),

                                Forms\Components\TextInput::make('cantidad')
                                    ->label('Cantidad')
                                    ->numeric()
                                    ->required()
                                    ->default(1)
                                    ->minValue(0.001)
                                    ->live(onBlur: true)
                                    ->columnSpan(2)
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        $cantidad = max(0, (float) ($state ?? 0));
                                        $costo = max(0, (float) ($get('precio_costo_unitario') ?? 0));
                                        $precio = max(0, (float) ($get('precio_unitario') ?? 0));
                                        $set('subtotal', round($cantidad * $precio, 2));
                                        $set('ganancia_linea', round(($precio - $costo) * $cantidad, 2));
                                    })
                                    ->rules([
                                        fn (Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                                            if ($get('../../estado') !== 'completada') {
                                                return;
                                            }
                                            $itemId = $get('item_id');
                                            if (!$itemId) {
                                                return;
                                            }
                                            $item = Item::find($itemId);
                                            if (!$item) {
                                                return;
                                            }
                                            $qty = (float) $value;
                                            $almacenId = $get('almacen_id') ?: $get('../../almacen_id');
                                            $stockAlmacen = $almacenId ? $item->stockEnAlmacen((int) $almacenId) : (float) $item->stock_actual;
                                            $stockGlobal = (float) $item->stock_actual;

                                            $ventaId = $get('../../id');
                                            if ($ventaId) {
                                                $venta = Venta::find($ventaId);
                                                if ($venta && $venta->inventario_descontado) {
                                                    $prevQty = (float) $venta->items()
                                                        ->where('item_id', $itemId)
                                                        ->when($almacenId, fn ($q) => $q->where('almacen_id', $almacenId))
                                                        ->sum('cantidad');
                                                    $stockAlmacen += $prevQty;
                                                    $stockGlobal += $prevQty;
                                                }
                                            }

                                            if ($qty > $stockGlobal) {
                                                $fail("Stock insuficiente para {$item->nombre} (disponible: {$stockGlobal} {$item->unidad_medida}).");
                                            } elseif ($almacenId && $item->movimientos()->whereNotNull('almacen_id')->exists() && $qty > $stockAlmacen) {
                                                $fail("Stock insuficiente en este almacén para {$item->nombre} (disponible: {$stockAlmacen} {$item->unidad_medida}).");
                                            }
                                        },
                                    ]),

                                Forms\Components\TextInput::make('precio_costo_unitario')
                                    ->label('Costo Unit.')
                                    ->numeric()
                                    ->required()
                                    ->default(0)
                                    ->minValue(0)
                                    ->prefix('$')
                                    ->live(onBlur: true)
                                    ->columnSpan(2)
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        $costo = max(0, (float) ($state ?? 0));
                                        $margen = (float) ($get('margen_ganancia') ?? 60);
                                        $cantidad = max(0, (float) ($get('cantidad') ?? 1));
                                        self::recalcularFilaDesdeMargen($cantidad, $costo, $margen, $set);
                                    }),

                                Forms\Components\TextInput::make('margen_ganancia')
                                    ->label('Margen (%)')
                                    ->numeric()
                                    ->required()
                                    ->default(60)
                                    ->minValue(-99)
                                    ->maxValue(1000)
                                    ->suffix('%')
                                    ->live(onBlur: true)
                                    ->columnSpan(2)
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        $margen = (float) ($state ?? 0);
                                        $costo = max(0, (float) ($get('precio_costo_unitario') ?? 0));
                                        $cantidad = max(0, (float) ($get('cantidad') ?? 1));
                                        self::recalcularFilaDesdeMargen($cantidad, $costo, $margen, $set);
                                    }),

                                Forms\Components\TextInput::make('precio_unitario')
                                    ->label('Precio Venta Unit.')
                                    ->numeric()
                                    ->required()
                                    ->default(0)
                                    ->minValue(0)
                                    ->prefix('$')
                                    ->live(onBlur: true)
                                    ->columnSpan(2)
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        $precio = max(0, (float) ($state ?? 0));
                                        $costo = max(0, (float) ($get('precio_costo_unitario') ?? 0));
                                        $cantidad = max(0, (float) ($get('cantidad') ?? 1));

                                        if ($costo > 0) {
                                            $margen = round((($precio / $costo) - 1) * 100, 2);
                                            $set('margen_ganancia', $margen);
                                        }
                                        $set('subtotal', round($cantidad * $precio, 2));
                                        $set('ganancia_linea', round(($precio - $costo) * $cantidad, 2));
                                    }),

                                Forms\Components\TextInput::make('subtotal')
                                    ->label('Subtotal')
                                    ->numeric()
                                    ->prefix('$')
                                    ->readOnly()
                                    ->dehydrated()
                                    ->default(0)
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('ganancia_linea')
                                    ->label('Ganancia')
                                    ->numeric()
                                    ->prefix('$')
                                    ->readOnly()
                                    ->dehydrated()
                                    ->default(0)
                                    ->columnSpan(2),
                            ]),
                    ]),

                Forms\Components\Section::make('Observaciones')
                    ->schema([
                        Forms\Components\Textarea::make('notas')
                            ->label('Notas de la Venta / Entrega')
                            ->rows(2)
                            ->placeholder('Detalles de entrega, garantía o observaciones del pago...')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Comprobante de Venta')
                    ->columns(4)
                    ->schema([
                        Infolists\Components\TextEntry::make('codigo')
                            ->label('Código')
                            ->badge()
                            ->color('primary'),
                        Infolists\Components\TextEntry::make('fecha_venta')
                            ->label('Fecha')
                            ->dateTime('d/m/Y H:i'),
                        Infolists\Components\TextEntry::make('estado')
                            ->label('Estado')
                            ->badge()
                            ->color(fn (string $state) => match ($state) {
                                'completada' => 'success',
                                'pendiente' => 'warning',
                                'anulada' => 'danger',
                                default => 'gray',
                            }),
                        Infolists\Components\IconEntry::make('inventario_descontado')
                            ->label('Almacén Descontado')
                            ->boolean(),
                        Infolists\Components\TextEntry::make('cliente_nombre')
                            ->label('Cliente'),
                        Infolists\Components\TextEntry::make('almacen.nombre')
                            ->label('Almacén Principal')
                            ->placeholder('Por línea'),
                        Infolists\Components\TextEntry::make('metodo_pago')
                            ->label('Método de Pago')
                            ->badge(),
                        Infolists\Components\TextEntry::make('referencia_pago')
                            ->label('Referencia / Transacción')
                            ->placeholder('—'),
                    ]),

                Infolists\Components\Section::make('Totales Financieros')
                    ->columns(4)
                    ->schema([
                        Infolists\Components\TextEntry::make('costo_total')
                            ->label('Costo Total')
                            ->formatStateUsing(fn ($state, Venta $record) => '$' . number_format((float) $state, 2) . ' ' . $record->moneda),
                        Infolists\Components\TextEntry::make('descuento')
                            ->label('Descuento')
                            ->formatStateUsing(fn ($state, Venta $record) => '$' . number_format((float) $state, 2) . ' ' . $record->moneda),
                        Infolists\Components\TextEntry::make('monto_total')
                            ->label('Total Cobrado')
                            ->weight('bold')
                            ->color('success')
                            ->formatStateUsing(fn ($state, Venta $record) => '$' . number_format((float) $state, 2) . ' ' . $record->moneda),
                        Infolists\Components\TextEntry::make('ganancia_total')
                            ->label('Ganancia Neta')
                            ->weight('bold')
                            ->color('info')
                            ->formatStateUsing(fn ($state, Venta $record) => '$' . number_format((float) $state, 2) . ' ' . $record->moneda),
                    ]),

                Infolists\Components\Section::make('Productos Vendidos')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('items')
                            ->label('')
                            ->columns(7)
                            ->schema([
                                Infolists\Components\ImageEntry::make('item.imagen')
                                    ->label('Foto')
                                    ->disk('public')
                                    ->square()
                                    ->size(48),
                                Infolists\Components\TextEntry::make('item.nombre')
                                    ->label('Producto')
                                    ->weight('bold'),
                                Infolists\Components\TextEntry::make('almacen.nombre')
                                    ->label('Almacén')
                                    ->placeholder('Principal'),
                                Infolists\Components\TextEntry::make('cantidad')
                                    ->label('Cant.'),
                                Infolists\Components\TextEntry::make('margen_ganancia')
                                    ->label('Margen')
                                    ->suffix('%'),
                                Infolists\Components\TextEntry::make('precio_unitario')
                                    ->label('P. Unit.')
                                    ->prefix('$'),
                                Infolists\Components\TextEntry::make('subtotal')
                                    ->label('Subtotal')
                                    ->weight('bold')
                                    ->prefix('$'),
                            ]),
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
                    ->color('primary')
                    ->copyable(),

                Tables\Columns\TextColumn::make('fecha_venta')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('cliente_nombre')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('almacen.nombre')
                    ->label('Almacén')
                    ->badge()
                    ->color('gray')
                    ->placeholder('Múltiple'),

                Tables\Columns\TextColumn::make('items_count')
                    ->label('Productos')
                    ->counts('items')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('metodo_pago')
                    ->label('Pago')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'CUP' => 'success',
                        'Transferencia CUP' => 'info',
                        'USD' => 'warning',
                        'Tarjeta Clasica' => 'primary',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('monto_total')
                    ->label('Total')
                    ->sortable()
                    ->weight('font-bold')
                    ->formatStateUsing(fn ($state, Venta $record): string => '$' . number_format((float) $state, 2) . ' ' . $record->moneda),

                Tables\Columns\TextColumn::make('ganancia_total')
                    ->label('Ganancia')
                    ->sortable()
                    ->color('success')
                    ->formatStateUsing(fn ($state, Venta $record): string => '+$' . number_format((float) $state, 2) . ' ' . $record->moneda),

                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completada' => 'success',
                        'pendiente' => 'warning',
                        'anulada' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => Venta::ESTADOS[$state] ?? $state),

                Tables\Columns\IconColumn::make('inventario_descontado')
                    ->label('Almacén Desc.')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->toggleable(),
            ])
            ->defaultSort('fecha_venta', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('metodo_pago')
                    ->label('Método de Pago')
                    ->options(Venta::METODOS_PAGO),

                Tables\Filters\SelectFilter::make('moneda')
                    ->label('Moneda')
                    ->options([
                        'CUP' => 'CUP',
                        'USD' => 'USD',
                    ]),

                Tables\Filters\SelectFilter::make('almacen_id')
                    ->label('Almacén')
                    ->relationship('almacen', 'nombre'),

                Tables\Filters\SelectFilter::make('estado')
                    ->label('Estado')
                    ->options(Venta::ESTADOS),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Ver'),

                Tables\Actions\Action::make('completar')
                    ->label('Completar y Descontar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Completar Venta y Descontar de Almacén')
                    ->modalDescription('Se descontarán las cantidades vendidas del inventario del almacén correspondiente.')
                    ->visible(fn (Venta $record): bool => $record->estado === 'pendiente')
                    ->action(function (Venta $record): void {
                        try {
                            $record->update(['estado' => 'completada']);
                            $record->aplicarDescuentoInventario();

                            Notification::make()
                                ->title('Venta completada')
                                ->body("Stock descontado del almacén para {$record->codigo}.")
                                ->success()
                                ->send();
                        } catch (\Throwable $e) {
                            $record->updateQuietly(['estado' => 'pendiente']);
                            Notification::make()
                                ->title('No se pudo descontar del almacén')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Tables\Actions\Action::make('anular')
                    ->label('Anular')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Anular Venta')
                    ->modalDescription('Si esta venta ya había descontado inventario, los productos se devolverán automáticamente al almacén.')
                    ->visible(fn (Venta $record): bool => $record->estado !== 'anulada')
                    ->action(function (Venta $record): void {
                        if ($record->inventario_descontado) {
                            $record->revertirInventario("Anulación de Venta {$record->codigo}");
                        }
                        $record->update(['estado' => 'anulada']);

                        Notification::make()
                            ->title('Venta anulada')
                            ->body("La venta {$record->codigo} fue anulada y el inventario fue restaurado.")
                            ->warning()
                            ->send();
                    }),

                Tables\Actions\EditAction::make()
                    ->label('Editar')
                    ->visible(fn (Venta $record): bool => $record->estado !== 'anulada'),

                Tables\Actions\DeleteAction::make()
                    ->label('Borrar'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getWidgets(): array
    {
        return [
            VentaStatsOverview::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVentas::route('/'),
            'create' => Pages\CreateVenta::route('/create'),
            'view' => Pages\ViewVenta::route('/{record}'),
            'edit' => Pages\EditVenta::route('/{record}/edit'),
        ];
    }
}
