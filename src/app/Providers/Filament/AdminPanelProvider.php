<?php

namespace App\Providers\Filament;

use App\Filament\Pages\EditProfile;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\HtmlString;
use Filament\Support\Assets\Css;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->profile(EditProfile::class)
            ->brandLogo(fn () => view('filament.components.brand-logo'))
            ->brandName('Dataplus S.R.L.')
            ->brandLogoHeight('2.5rem')
            ->colors([
                'primary' => Color::Sky,
            ])
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->globalSearchFieldKeyBindingSuffix()
            ->navigationGroups([
                NavigationGroup::make('Operaciones')
                    ->icon('heroicon-o-cpu-chip'),
                NavigationGroup::make('Proyectos')
                    ->icon('heroicon-o-briefcase'),
                NavigationGroup::make('Inventario')
                    ->icon('heroicon-o-archive-box'),
                NavigationGroup::make('Finanzas')
                    ->icon('heroicon-o-banknotes'),
                NavigationGroup::make('Reportes')
                    ->icon('heroicon-o-chart-bar'),
                NavigationGroup::make('Administración')
                    ->icon('heroicon-o-shield-check'),
                NavigationGroup::make('Configuración')
                    ->icon('heroicon-o-cog-6-tooth'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): HtmlString => new HtmlString('
                    <style>
                        /* Prevenir completamente cursor de texto (caret) en toda la UI fuera de inputs */
                        *:not(input):not(textarea):not([contenteditable="true"]):not([contenteditable=""]) {
                            caret-color: transparent !important;
                        }

                        /* Menú lateral, cabeceras, botones, enlaces, tabs y breadcrumbs */
                        .fi-sidebar, .fi-sidebar *,
                        .fi-sidebar-nav, .fi-sidebar-nav *,
                        .fi-sidebar-item, .fi-sidebar-item *,
                        .fi-sidebar-item-button, .fi-sidebar-item-button *,
                        .fi-sidebar-item-label, .fi-sidebar-group-label,
                        .fi-topbar, .fi-topbar *,
                        .fi-header, .fi-header *,
                        .fi-breadcrumbs, .fi-breadcrumbs *,
                        .fi-tabs, .fi-tabs *,
                        .fi-btn, .fi-btn *,
                        .dp-kanban-tab, .dp-kanban-tab *,
                        a, a *,
                        button, button *,
                        [role="button"], [role="button"] *,
                        [role="tab"], [role="tab"] * {
                            -webkit-user-select: none !important;
                            -moz-user-select: none !important;
                            -ms-user-select: none !important;
                            user-select: none !important;
                            caret-color: transparent !important;
                            -webkit-user-modify: read-only !important;
                        }

                        .fi-sidebar-item-button,
                        .fi-sidebar-nav a,
                        .fi-sidebar-nav button,
                        .fi-sidebar-group-button,
                        a, button, [role="button"], [role="tab"] {
                            cursor: pointer !important;
                            outline: none !important;
                        }

                        .fi-sidebar-item-label,
                        .fi-sidebar-group-label,
                        .fi-sidebar-nav span,
                        .dp-kanban-tab span,
                        a > span,
                        button > span {
                            pointer-events: none !important;
                            caret-color: transparent !important;
                        }

                        a:focus, a:focus-visible,
                        button:focus, button:focus-visible,
                        .fi-sidebar-item-button:focus,
                        .fi-sidebar-item-button:focus-visible {
                            outline: none !important;
                            box-shadow: none !important;
                            caret-color: transparent !important;
                        }
                    </style>
                ')
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): HtmlString => new HtmlString('
                    <script>
                        document.addEventListener("mousedown", function (e) {
                            var isInput = e.target.closest("input, textarea, [contenteditable=\"true\"], [contenteditable=\"\"]");
                            if (!isInput && window.getSelection) {
                                window.getSelection().removeAllRanges();
                            }
                        }, true);

                        document.addEventListener("click", function (e) {
                            var isInput = e.target.closest("input, textarea, [contenteditable=\"true\"], [contenteditable=\"\"]");
                            if (!isInput) {
                                if (window.getSelection) {
                                    window.getSelection().removeAllRanges();
                                }
                                var clickable = e.target.closest("a, button, [role=\"button\"], [role=\"tab\"], .fi-sidebar-item-button");
                                if (clickable) {
                                    clickable.blur();
                                }
                            }
                        }, true);
                    </script>
                ')
            )

            ->assets([
                Css::make('custom-sidebar', resource_path('css/filament/admin.css')),
            ]);
    }
}
