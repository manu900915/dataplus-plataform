<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventarioMovimientoResource\Pages;
use App\Models\InventarioMovimiento;
use App\Models\Item;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class InventarioMovimientoResource extends Resource
{
    protected static ?string $model = InventarioMovimiento::class;
    protected static ?string $navigationGroup = 'Inventario';
    protected static ?string $navigationLabel = 'Movimientos';
    protected static ?string $modelLabel = 'Movimiento';
    protected static ?string $pluralModelLabel = 'Movimientos';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('item_id')
                ->relationship('item', 'nombre')
                ->getOptionLabelFromRecordUsing(fn (Item $r) => "{$r->codigo} — {$r->nombre} (stock: {$r->stock_actual})")
                ->searchable()
                ->preload()
                ->required(),
            
            Forms\Components\Select::make('almacen_id')
                ->relationship('almacen', 'nombre')
                ->preload(),
            
            Forms\Components\Select::make('tipo')
                ->options([
                    'entrada' => 'Entrada (compra, ajuste +)',
                    'salida' => 'Salida (consumo, ajuste -)',
                    'devolucion' => 'Devolución (+)',
                ])
                ->required()
                ->default('entrada'),
            
            Forms\Components\TextInput::make('cantidad')
                ->numeric()
                ->required()
                ->minValue(0.001),
            
            Forms\Components\TextInput::make('costo_unitario')
                ->numeric()
                ->prefix('$')
                ->helperText('Para entradas: costo de compra. En salidas queda como referencia.'),
            
            Forms\Components\TextInput::make('motivo')
                ->maxLength(255),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                
                TextColumn::make('item.codigo')
                    ->label('Código')
                    ->searchable(),
                
                TextColumn::make('item.nombre')
                    ->label('Item')
                    ->searchable()
                    ->weight('bold'),
                
                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'entrada', 'devolucion' => 'success',
                        'salida' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'entrada' => 'Entrada',
                        'salida' => 'Salida',
                        'devolucion' => 'Devolución',
                        default => $state,
                    }),
                
                TextColumn::make('cantidad')
                    ->numeric()
                    ->sortable(),
                
                TextColumn::make('almacen.nombre')
                    ->label('Almacén')
                    ->toggleable(),
                
                TextColumn::make('costo_unitario')
                    ->label('Costo Unit.')
                    ->money('USD')
                    ->toggleable(),
                
                TextColumn::make('motivo')
                    ->label('Motivo')
                    ->toggleable()
                    ->limit(30),
                
                TextColumn::make('user.name')
                    ->label('Registrado por')
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('tipo')
                    ->options([
                        'entrada' => 'Entrada',
                        'salida' => 'Salida',
                        'devolucion' => 'Devolución',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->recordUrl(null);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInventarioMovimientos::route('/'),
            'create' => Pages\CreateInventarioMovimiento::route('/create'),
            'view' => Pages\ViewInventarioMovimiento::route('/{record}'),
        ];
    }
}