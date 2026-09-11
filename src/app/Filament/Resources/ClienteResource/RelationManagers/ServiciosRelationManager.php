<?php

namespace App\Filament\Resources\ClienteResource\RelationManagers;

use App\Models\Servicio;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ServiciosRelationManager extends RelationManager
{
    protected static string $relationship = 'servicios';
    protected static ?string $title = 'Todos los Servicios';
    protected static ?string $modelLabel = 'Servicio';

    // Como la relación directa no existe, usamos modifyQueryUsing
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Servicio::query()
            ->whereHas('ubicacion', function ($q) {
                $q->where('cliente_id', request()->route('record'));
            });
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('cliente_ubicacion_id')
                    ->label('Ubicación')
                    ->relationship('ubicacion', 'nombre')
                    ->required(),

                Forms\Components\Select::make('tipo')
                    ->options([
                        'CCTV' => 'CCTV',
                        'SACI' => 'SACI',
                        'Gestion_Remota' => 'Gestión Remota',
                    ])
                    ->required(),

                Forms\Components\DatePicker::make('fecha_instalacion')
                    ->label('Fecha de instalación'),

                Forms\Components\Select::make('estado')
                    ->options([
                        'Activo' => 'Activo',
                        'Inactivo' => 'Inactivo',
                        'En_Reparacion' => 'En Reparación',
                        'Suspendido' => 'Suspendido',
                    ])
                    ->default('Activo'),

                Forms\Components\Textarea::make('notas')
                    ->rows(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ubicacion.nombre')
                    ->label('Ubicación')
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('tipo')
                    ->colors([
                        'primary' => 'CCTV',
                        'danger' => 'SACI',
                        'warning' => 'Gestion_Remota',
                    ]),

                Tables\Columns\BadgeColumn::make('estado')
                    ->colors([
                        'success' => 'Activo',
                        'danger' => 'Inactivo',
                        'warning' => 'En_Reparacion',
                        'gray' => 'Suspendido',
                    ]),

                Tables\Columns\TextColumn::make('fecha_instalacion')
                    ->date('d/m/Y'),

                Tables\Columns\TextColumn::make('brigada.nombre')
                    ->label('Brigada')
                    ->placeholder('Sin asignar'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Agregar servicio'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}