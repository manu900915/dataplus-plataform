<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoriaItemResource\Pages;
use App\Models\CategoriaItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CategoriaItemResource extends Resource
{
    protected static ?string $model = CategoriaItem::class;
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Inventario'; // 👈 AGREGADO
    protected static ?string $navigationLabel = 'Categorías';
    protected static ?string $modelLabel = 'Categoría';
    protected static ?string $pluralModelLabel = 'Categorías';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('tipo')
                    ->options([
                        'equipamiento' => 'Equipamiento',
                        'material' => 'Material',
                        'ambos' => 'Ambos',
                    ])
                    ->required(),
                Forms\Components\Toggle::make('activo')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('tipo')
                    ->colors([
                        'primary' => 'equipamiento',
                        'success' => 'material',
                        'info' => 'ambos',
                    ]),
                Tables\Columns\IconColumn::make('activo')
                    ->boolean(),
            ])
            ->actions([
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
            'index' => Pages\ListCategoriaItems::route('/'),
            'create' => Pages\CreateCategoriaItem::route('/create'),
            'edit' => Pages\EditCategoriaItem::route('/{record}/edit'),
        ];
    }
}