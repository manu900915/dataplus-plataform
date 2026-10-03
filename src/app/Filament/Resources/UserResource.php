<?php

namespace App\Filament\Resources;

use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Administración';
    protected static ?string $modelLabel = 'Usuario';
    protected static ?string $pluralModelLabel = 'Usuarios';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Usuario')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre completo')
                            ->required()
                            ->maxLength(255)
                            ->regex('/^[\p{L}\p{N}\s\.\-]+$/u')
                            ->validationMessages([
                                'required' => 'El nombre completo es obligatorio.',
                                'regex' => 'El nombre solo puede contener letras, números, espacios, puntos y guiones.',
                                'max' => 'El nombre no puede exceder los 255 caracteres.',
                            ]),

                        Forms\Components\TextInput::make('email')
                            ->label('Correo electrónico')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->rule(fn ($record) => Rule::unique('users', 'email')->ignore($record?->id))
                            ->autocomplete('new-email')
                            ->validationMessages([
                                'required' => 'El correo electrónico es obligatorio.',
                                'email' => 'Por favor, introduce una dirección de correo válida.',
                                'unique' => 'Este correo electrónico ya está registrado.',
                                'max' => 'El correo no puede exceder los 255 caracteres.',
                            ]),

                        Forms\Components\Section::make('Contacto')
                            ->columns(2)
                            ->columnSpan(1)
                            ->schema([
                                Forms\Components\Select::make('country_code')
                                    ->label('Código país')
                                    ->options([
                                        '+53' => '🇨🇺 Cuba (+53)',
                                        '+52' => '🇲🇽 México (+52)',
                                        '+34' => '🇪 España (+34)',
                                        '+1'  => '🇺🇸 EE.UU./Canadá (+1)',
                                        '+54' => '🇦🇷 Argentina (+54)',
                                        '+56' => '🇨🇱 Chile (+56)',
                                        '+57' => '🇨🇴 Colombia (+57)',
                                        '+51' => '🇵🇪 Perú (+51)',
                                        '+58' => '🇪 Venezuela (+58)',
                                        '+593' => '🇪 Ecuador (+593)',
                                        '+502' => '🇬🇹 Guatemala (+502)',
                                        '+504' => '🇭🇳 Honduras (+504)',
                                        '+505' => '🇮 Nicaragua (+505)',
                                        '+506' => '🇷 Costa Rica (+506)',
                                        '+507' => '🇵🇦 Panamá (+507)',
                                        '+591' => '🇧🇴 Bolivia (+591)',
                                        '+595' => '🇵🇾 Paraguay (+595)',
                                        '+598' => '🇺🇾 Uruguay (+598)',
                                        '+351' => '🇵🇹 Portugal (+351)',
                                        '+39'  => '🇹 Italia (+39)',
                                        '+33'  => '🇫🇷 Francia (+33)',
                                        '+44'  => '🇬🇧 Reino Unido (+44)',
                                        '+49'  => '🇩🇪 Alemania (+49)',
                                    ])
                                    ->default('+53')
                                    ->required()
                                    ->searchable()
                                    ->native(false)
                                    ->validationMessages([
                                        'required' => 'Selecciona un código de país.',
                                    ]),

                                Forms\Components\TextInput::make('phone')
                                    ->label('Teléfono')
                                    ->tel()
                                    ->maxLength(20)
                                    ->regex('/^\+?[0-9\s\-]{7,20}$/')
                                    ->helperText('Ej: 5555 1234 o +53 5 5551234')
                                    ->validationMessages([
                                        'regex' => 'El teléfono debe contener solo números, espacios, guiones y el signo +.',
                                        'max' => 'El teléfono no puede exceder los 20 caracteres.',
                                    ]),
                            ]),

                        Forms\Components\FileUpload::make('photo_path')
                            ->label('Foto de perfil')
                            ->image()
                            ->imageEditor()
                            ->directory('avatars')
                            ->avatar()
                            ->alignCenter()
                            ->columnSpan(1)
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->helperText('Máximo 2MB. Formatos: JPG, PNG, WEBP.')
                            ->validationMessages([
                                'max' => 'La imagen no puede pesar más de 2MB.',
                                'mimetypes' => 'Solo se permiten imágenes JPG, PNG o WEBP.',
                            ]),

                        Forms\Components\Select::make('roles')
                            ->label('Rol')
                            ->relationship('roles', 'name')
                            ->options(Role::all()->pluck('name', 'id'))
                            ->preload()
                            ->required()
                            ->validationMessages([
                                'required' => 'Selecciona al menos un rol.',
                            ]),

                        Forms\Components\Toggle::make('activo')
                            ->label('Usuario activo')
                            ->default(true),
                    ]),

                Forms\Components\Section::make('Seguridad')
                    ->description('Dejar en blanco para mantener la contraseña actual al editar.')
                    ->schema([
                        Forms\Components\TextInput::make('password')
                            ->label('Contraseña')
                            ->password()
                            ->revealable()
                            ->autocomplete('new-password')
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->rule(Password::min(8)->mixedCase()->numbers())
                            ->minLength(8)
                            ->validationMessages([
                                'required' => 'La contraseña es obligatoria.',
                                'min' => 'La contraseña debe tener al menos 8 caracteres.',
                                'rule' => 'La contraseña debe contener al menos una mayúscula, una minúscula y un número.',
                            ]),

                        Forms\Components\TextInput::make('password_confirmation')
                            ->label('Confirmar contraseña')
                            ->password()
                            ->revealable()
                            ->autocomplete('new-password')
                            ->same('password')
                            ->required(fn (string $context): bool => $context === 'create')
                            ->dehydrated(false)
                            ->validationMessages([
                                'required' => 'Debes confirmar la contraseña.',
                                'same' => 'Las contraseñas no coinciden.',
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Correo')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Rol')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Administrador' => 'danger',
                        'Supervisor' => 'info',
                        'Técnico' => 'primary',
                        'Contable' => 'success',
                        'Vendedor' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('activo')
                    ->label('Activo')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('roles')
                    ->label('Filtrar por rol')
                    ->relationship('roles', 'name')
                    ->preload(),
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
            ->emptyStateHeading('No hay usuarios registrados')
            ->emptyStateDescription('Crea el primer usuario del sistema.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Crear usuario'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\UserResource\Pages\ListUsers::route('/'),
            'create' => \App\Filament\Resources\UserResource\Pages\CreateUser::route('/create'),
            'edit' => \App\Filament\Resources\UserResource\Pages\EditUser::route('/{record}/edit'),
        ];
    }
}