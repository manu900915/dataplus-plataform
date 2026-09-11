<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class ReportesPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'Reportes';
    protected static ?string $navigationLabel = 'Reportes';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.pages.placeholder';

        public static function canAccess(): bool
    {
        $user = Auth::user();
        return $user && (
            $user->hasRole('Administrador') ||
            $user->hasRole('Supervisor')
        );
    }
}