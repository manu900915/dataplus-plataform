<?php

namespace App\Filament\Pages;

use App\Filament\Resources\VentaResource;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class VentasPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Finanzas';
    protected static ?string $navigationLabel = 'Ventas';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.pages.placeholder';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public function mount(): void
    {
        $this->redirect(VentaResource::getUrl('index'));
    }

    public static function canAccess(): bool
    {
        $user = Auth::user();
        return $user && (
            $user->hasRole('Administrador') ||
            $user->hasRole('Vendedor') ||
            $user->hasRole('Contable') ||
            $user->hasRole('Supervisor') ||
            $user->hasRole('Comercial')
        );
    }
}