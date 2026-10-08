<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class EditProfile extends BaseEditProfile
{
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationLabel = 'Mi Perfil';

    protected static ?string $title = 'Editar Perfil';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Personal')
                    ->description('Actualice los datos personales y de contacto de su cuenta.')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre completo')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->label('Correo electrónico')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Forms\Components\Select::make('country_code')
                            ->label('Código país')
                            ->options([
                                '+53' => '🇨🇺 Cuba (+53)',
                                '+52' => '🇲🇽 México (+52)',
                                '+34' => '🇪🇸 España (+34)',
                                '+1'  => '🇺🇸 EE.UU./Canadá (+1)',
                                '+54' => '🇦🇷 Argentina (+54)',
                                '+56' => '🇨🇱 Chile (+56)',
                                '+57' => '🇨🇴 Colombia (+57)',
                                '+51' => '🇵🇪 Perú (+51)',
                                '+58' => '🇻🇪 Venezuela (+58)',
                            ])
                            ->default('+53')
                            ->required()
                            ->searchable()
                            ->native(false),

                        Forms\Components\TextInput::make('phone')
                            ->label('Teléfono')
                            ->tel()
                            ->maxLength(20),

                        Forms\Components\FileUpload::make('photo_path')
                            ->label('Foto de perfil')
                            ->image()
                            ->imageEditor()
                            ->directory('avatars')
                            ->avatar()
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Cambiar Contraseña')
                    ->description('Dejar en blanco para mantener la contraseña actual.')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('password')
                            ->label('Nueva contraseña')
                            ->password()
                            ->revealable()
                            ->autocomplete('new-password')
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->rules([Password::min(8)])
                            ->minLength(8)
                            ->validationMessages([
                                'min' => 'La contraseña debe tener al menos 8 caracteres.',
                            ]),

                        Forms\Components\TextInput::make('password_confirmation')
                            ->label('Confirmar nueva contraseña')
                            ->password()
                            ->revealable()
                            ->autocomplete('new-password')
                            ->same('password')
                            ->dehydrated(false)
                            ->validationMessages([
                                'same' => 'Las contraseñas no coinciden.',
                            ]),
                    ]),
            ])
            ->operation('edit')
            ->model($this->getUser())
            ->statePath('data');
    }
}
