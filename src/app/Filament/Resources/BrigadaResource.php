<?php

namespace App\Filament\Resources;

use App\Models\Brigada;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BrigadaResource extends Resource
{
    protected static ?string $model = Brigada::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Administración';
    protected static ?string $modelLabel = 'Brigada';
    protected static ?string $pluralModelLabel = 'Brigadas';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Datos de la Brigada')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('nombre')
                            ->label('Nombre de la brigada')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(100)
                            ->placeholder('Ej: Brigada A, Equipo Norte, Técnicos Centro...'),

                        Forms\Components\Select::make('jefe_id')
                            ->label('Jefe de Brigada')
                            ->relationship('jefe', 'name', fn (Builder $query) => $query->role(['Técnico', 'Administrador', 'Supervisor']))
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Textarea::make('notas')
                            ->label('Notas')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Integrantes')
                    ->description('Selecciona los técnicos que forman parte de esta brigada. El jefe también puede ser integrante.')
                    ->schema([
                        Forms\Components\Select::make('tecnicos')
                            ->label('Técnicos')
                            ->relationship('tecnicos', 'name', fn (Builder $query) => $query->role(['Técnico', 'Administrador', 'Supervisor']))
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->placeholder('Seleccionar técnicos...'),
                    ]),

                Forms\Components\Toggle::make('activa')
                    ->label('Brigada activa')
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

                Tables\Columns\TextColumn::make('jefe.name')
                    ->label('Jefe')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('warning'),

                Tables\Columns\TextColumn::make('tecnicos_count')
                    ->label('Integrantes')
                    ->counts('tecnicos')
                    ->badge()
                    ->color('info'),

                Tables\Columns\IconColumn::make('activa')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creada')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('activa')
                    ->label('Activa'),
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
            'index' => \App\Filament\Resources\BrigadaResource\Pages\ListBrigadas::route('/'),
            'create' => \App\Filament\Resources\BrigadaResource\Pages\CreateBrigada::route('/create'),
            'edit' => \App\Filament\Resources\BrigadaResource\Pages\EditBrigada::route('/{record}/edit'),
        ];
    }
}