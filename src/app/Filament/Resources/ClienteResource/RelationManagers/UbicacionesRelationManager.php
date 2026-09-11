<?php

namespace App\Filament\Resources\ClienteResource\RelationManagers;

use App\Models\Brigada;
use App\Models\Servicio;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class UbicacionesRelationManager extends RelationManager
{
    protected static string $relationship = 'ubicaciones';
    protected static ?string $title = 'Negocios / Ubicaciones';
    protected static ?string $modelLabel = 'Ubicación';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Select::make('tipo')
                    ->options([
                        'residencial' => 'Residencial',
                        'negocio' => 'Negocio',
                    ])
                    ->required()
                    ->live(),

                Forms\Components\Select::make('tipo_negocio_id')
                    ->label('Giro del negocio')
                    ->relationship('tipoNegocio', 'nombre')
                    ->searchable()
                    ->preload()
                    ->visible(fn ($get) => $get('tipo') === 'negocio'),

                Forms\Components\TextInput::make('direccion')
                    ->maxLength(255),

                Forms\Components\TextInput::make('contacto_nombre')
                    ->label('Contacto en sitio'),

                Forms\Components\TextInput::make('contacto_telefono')
                    ->label('Teléfono del sitio')
                    ->tel(),

                Forms\Components\Textarea::make('notas')
                    ->rows(2),

                Forms\Components\Toggle::make('activo')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable()
                    ->weight('font-bold'),

                Tables\Columns\BadgeColumn::make('tipo')
                    ->colors([
                        'success' => 'residencial',
                        'warning' => 'negocio',
                    ]),

                Tables\Columns\TextColumn::make('direccion')
                    ->placeholder('Sin dirección')
                    ->limit(30),

                Tables\Columns\TextColumn::make('servicios_badges')
                    ->label('Servicios instalados')
                    ->getStateUsing(function ($record): string {
                        $servicios = $record->servicios;
                        if ($servicios->isEmpty()) return '<span class="text-gray-400 text-xs">Sin servicios</span>';

                        return $servicios->map(function ($s) {
                            $color = match($s->estado) {
                                'Activo' => 'text-green-600',
                                'Inactivo' => 'text-red-500',
                                'En_Reparacion' => 'text-amber-500',
                                'Suspendido' => 'text-gray-500',
                                default => 'text-gray-500',
                            };
                            return "<span class='{$color} font-semibold text-xs bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded mr-1'>{$s->tipo}</span>";
                        })->implode(' ');
                    })
                    ->html(),

                Tables\Columns\IconColumn::make('activo')
                    ->boolean(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Agregar ubicación'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                // ─── MODAL: Agregar servicio a ESTA ubicación ───
                Tables\Actions\Action::make('agregarServicio')
                    ->label('Agregar servicio')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->modalHeading(fn ($record) => "Nuevo servicio en: {$record->nombre}")
                    ->modalDescription('Complete los datos del servicio que se instalará en esta ubicación.')
                    ->modalSubmitActionLabel('Guardar servicio')
                    ->successNotificationTitle('Servicio agregado correctamente')
                    ->form([
                        // Tipo de servicio
                        Forms\Components\Select::make('tipo')
                            ->label('Tipo de servicio')
                            ->options([
                                'CCTV' => 'CCTV',
                                'SACI' => 'SACI',
                                'Gestion_Remota' => 'Gestión Remota',
                            ])
                            ->required()
                            ->live()
                            ->native(false),

                        // Fecha y brigada
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('fecha_instalacion')
                                    ->label('Fecha de instalación')
                                    ->native(false)
                                    ->default(now()),

                                Forms\Components\Select::make('responsable')
                                    ->label('Brigada / Especialista')
                                    ->options(function () {
                                        $brigadas = \App\Models\Brigada::where('activa', true)
                                            ->get()
                                            ->mapWithKeys(fn ($b) => ["brigada_{$b->id}" => $b->nombre]);
                                        
                                        $tecnicos = \App\Models\User::role(['Técnico', 'Administrador', 'Supervisor'])
                                            ->where('activo', true)
                                            ->get()
                                            ->mapWithKeys(fn ($t) => ["tecnico_{$t->id}" => $t->name]);
                                        
                                        return [
                                            'Brigadas' => $brigadas->toArray(),
                                            'Técnicos' => $tecnicos->toArray(),
                                        ];
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->placeholder('Seleccionar brigada o técnico...'),
                            ]),

                        // Estado
                        Forms\Components\Select::make('estado')
                            ->label('Estado del servicio')
                            ->options([
                                'Activo' => 'Activo',
                                'Inactivo' => 'Inactivo',
                                'En_Reparacion' => 'En Reparación',
                                'Suspendido' => 'Suspendido',
                            ])
                            ->default('Activo')
                            ->required()
                            ->native(false),

                        // Notas
                        Forms\Components\Textarea::make('notas')
                            ->label('Notas generales')
                            ->rows(2)
                            ->columnSpanFull(),

                        // ─── Gestión Remota (condicional) ───
                        Forms\Components\Section::make('Datos específicos de Gestión Remota')
                            ->icon('heroicon-o-wifi')
                            ->visible(fn ($get) => $get('tipo') === 'Gestion_Remota')
                            ->columns(2)
                            ->schema([
                                Forms\Components\Select::make('gr_tipo_solucion')
                                    ->label('Tipo de solución')
                                    ->options([
                                        'Router4g' => 'Router 4G',
                                        'Router+Modem' => 'Router + Módem',
                                        'Router+ADSL' => 'Router + ADSL',
                                        'Otros' => 'Otros',
                                    ])
                                    ->live()
                                    ->native(false),

                                Forms\Components\TextInput::make('gr_sim_numero')
                                    ->label('Número de SIM card')
                                    ->tel()
                                    ->placeholder('+53 5 ...')
                                    ->visible(fn ($get) => in_array($get('gr_tipo_solucion'), ['Router4g', 'Router+Modem'])),

                                Forms\Components\TextInput::make('gr_marca_modelo')
                                    ->label('Marca / Modelo del equipo')
                                    ->placeholder('Ej: TP-Link TL-MR6400')
                                    ->columnSpanFull(),

                                Forms\Components\Select::make('gr_tipo_internet')
                                    ->label('Tipo de internet')
                                    ->options([
                                        'Abierto' => 'Abierto (sin restricciones)',
                                        'Filtrado' => 'Filtrado (páginas bloqueadas)',
                                        'Cerrado' => 'Cerrado (solo servicios específicos)',
                                    ])
                                    ->native(false),

                                Forms\Components\Select::make('gr_recarga_por')
                                    ->label('¿Quién recarga la SIM?')
                                    ->options([
                                        'Nosotros' => 'Nosotros (Dataplus)',
                                        'Cliente' => 'El cliente',
                                    ])
                                    ->default('Cliente')
                                    ->live()
                                    ->native(false),

                                Forms\Components\TextInput::make('gr_recarga_monto')
                                    ->label('Monto de recarga (CUP)')
                                    ->numeric()
                                    ->prefix('$')
                                    ->default(360)
                                    ->suffix('CUP'),

                                Forms\Components\DatePicker::make('gr_ultima_recarga')
                                    ->label('Fecha de última recarga')
                                    ->native(false),
                            ]),
                    ])
                                        ->action(function (array $data, $record): void {
                        $servicioData = [
                            'cliente_ubicacion_id' => $record->id,
                            'creado_por' => auth()->id(),
                            'tipo' => $data['tipo'],
                            'fecha_instalacion' => $data['fecha_instalacion'] ?? null,
                            'estado' => $data['estado'],
                            'notas' => $data['notas'] ?? null,
                            'brigada_id' => null,
                            'tecnico_id' => null,
                        ];
                        
                        // Parsear responsable (brigada o técnico)
                        $responsable = $data['responsable'] ?? null;
                        if ($responsable) {
                            if (str_starts_with($responsable, 'brigada_')) {
                                $servicioData['brigada_id'] = (int) str_replace('brigada_', '', $responsable);
                            } elseif (str_starts_with($responsable, 'tecnico_')) {
                                $servicioData['tecnico_id'] = (int) str_replace('tecnico_', '', $responsable);
                            }
                        }
                        
                        // Gestión remota
                        if ($data['tipo'] === 'Gestion_Remota') {
                            $servicioData['gr_tipo_solucion'] = $data['gr_tipo_solucion'] ?? null;
                            $servicioData['gr_sim_numero'] = $data['gr_sim_numero'] ?? null;
                            $servicioData['gr_marca_modelo'] = $data['gr_marca_modelo'] ?? null;
                            $servicioData['gr_tipo_internet'] = $data['gr_tipo_internet'] ?? null;
                            $servicioData['gr_recarga_por'] = $data['gr_recarga_por'] ?? 'Cliente';
                            $servicioData['gr_recarga_monto'] = $data['gr_recarga_monto'] ?? 360;
                            $servicioData['gr_ultima_recarga'] = $data['gr_ultima_recarga'] ?? null;
                        }
                        
                        \App\Models\Servicio::create($servicioData);
                    })
                    ->successNotificationTitle('Servicio agregado correctamente'),

                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}