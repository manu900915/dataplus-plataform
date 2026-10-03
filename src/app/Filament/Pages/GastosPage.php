<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class GastosPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Finanzas';
    protected static ?string $navigationLabel = 'Gastos';
    protected static ?int $navigationSort = 2;
    protected static string $view = 'filament.pages.placeholder';

        public static function canAccess(): bool
    {
        $user = Auth::user();
        return $user && (
            $user->hasRole('Administrador') ||
            $user->hasRole('Contable') ||
            $user->hasRole('Supervisor')
        );
    }
}