<?php

namespace App\Filament\Resources;

use App\Models\TipoNegocio;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TipoNegocioResource extends Resource
{
    protected static ?string $model = TipoNegocio::class;
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Administración';
    protected static ?string $modelLabel = 'Tipo de Negocio';
    protected static ?string $pluralModelLabel = 'Tipos de Negocio';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(100)
                    ->placeholder('Ej: Restaurante, Barbería, Tienda, etc.'),

                Forms\Components\Textarea::make('descripcion')
                    ->rows(2)
                    ->placeholder('Breve descripción de este tipo de negocio'),

                Forms\Components\Toggle::make('activo')
                    ->label('Activo')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable()
                    ->sortable()
                    ->weight('font-bold'),

                Tables\Columns\TextColumn::make('descripcion')
                    ->limit(50)
                    ->toggleable(),

                Tables\Columns\IconColumn::make('activo')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('activo'),
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
            'index' => \App\Filament\Resources\TipoNegocioResource\Pages\ListTipoNegocios::route('/'),
            'create' => \App\Filament\Resources\TipoNegocioResource\Pages\CreateTipoNegocio::route('/create'),
            'edit' => \App\Filament\Resources\TipoNegocioResource\Pages\EditTipoNegocio::route('/{record}/edit'),
        ];
    }
}