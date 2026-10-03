<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AlmacenResource\Pages;
use App\Helpers\CubanLocations;
use App\Models\Almacen;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AlmacenResource extends Resource
{
    protected static ?string $model = Almacen::class;
    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationGroup = 'Inventario';
    protected static ?string $navigationLabel = 'Almacenes';
    protected static ?string $modelLabel = 'Almacén';
    protected static ?string $pluralModelLabel = 'Almacenes';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->label('Nombre del almacén')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Select::make('provincia')
                    ->label('Provincia')
                    ->options(CubanLocations::provincias())
                    ->searchable()
                    ->live()
                    ->placeholder('Seleccione una provincia...'),

                Forms\Components\Select::make('municipio')
                    ->label('Municipio')
                    ->options(function ($get) {
                        $provincia = $get('provincia');
                        return $provincia ? CubanLocations::municipios($provincia) : [];
                    })
                    ->searchable()
                    ->live()
                    ->placeholder('Primero seleccione provincia')
                    ->disabled(function ($get) {
                        return blank($get('provincia'));
                    }),

                Forms\Components\Select::make('responsable_id')
                    ->label('Responsable del almacén')
                    ->relationship('responsable', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Seleccione un responsable...'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->weight('font-bold'),

                Tables\Columns\TextColumn::make('provincia')
                    ->label('Provincia')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('municipio')
                    ->label('Municipio')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('responsable.name')
                    ->label('Responsable')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Sin responsable'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('nombre', 'asc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAlmacens::route('/'),
            'create' => Pages\CreateAlmacen::route('/create'),
            'edit' => Pages\EditAlmacen::route('/{record}/edit'),
        ];
    }
}