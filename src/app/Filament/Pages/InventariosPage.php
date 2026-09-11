<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class InventariosPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationGroup = 'Operaciones';
    protected static ?string $navigationLabel = 'Inventarios';
    protected static ?int $navigationSort = 2;
    protected static string $view = 'filament.pages.placeholder';

    // 👇 OCULTAR del sidebar
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function canAccess(): bool
    {
        $user = Auth::user();
        return $user && (
            $user->hasRole('Administrador') ||
            $user->hasRole('Técnico') ||
            $user->hasRole('Supervisor')
        );
    }
}