<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class VentasPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Finanzas';
    protected static ?string $navigationLabel = 'Ventas';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.pages.placeholder';

        public static function canAccess(): bool
    {
        $user = Auth::user();
        return $user && (
            $user->hasRole('Administrador') ||
            $user->hasRole('Vendedor') ||
            $user->hasRole('Contable') ||
            $user->hasRole('Supervisor')
        );
    }
}