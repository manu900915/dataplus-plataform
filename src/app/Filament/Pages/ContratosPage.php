<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class ContratosPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Operaciones';
    protected static ?string $navigationLabel = 'Contratos';
    protected static ?int $navigationSort = 3;
    protected static string $view = 'filament.pages.placeholder';

        public static function canAccess(): bool
    {
        $user = Auth::user();
        return $user && (
            $user->hasRole('Administrador') ||
            $user->hasRole('Vendedor') ||
            $user->hasRole('Supervisor')
        );
    }
}