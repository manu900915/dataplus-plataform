<?php

namespace App\Filament\Pages;

use App\Models\Proyecto;
use Filament\Pages\Page;

class SeguimientoProyectos extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-queue-list';
    protected static ?string $navigationLabel = 'Seguimiento';
    protected static ?string $title = 'Seguimiento de Proyectos I+D';
    protected static ?string $navigationGroup = 'Proyectos';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.pages.seguimiento-proyectos';

    public function getColumns(): array
    {
        return [
            'por_hacer' => [
                'title' => '📋 Por Hacer',
                'color' => 'gray',
                'proyectos' => Proyecto::where('tipo_seguimiento', 'investigacion')
                    ->where('estado_kanban', 'por_hacer')
                    ->orderBy('created_at')
                    ->get(),
            ],
            'en_progreso' => [
                'title' => '🔄 En Progreso',
                'color' => 'warning',
                'proyectos' => Proyecto::where('tipo_seguimiento', 'investigacion')
                    ->where('estado_kanban', 'en_progreso')
                    ->orderBy('updated_at', 'desc')
                    ->get(),
            ],
            'en_revision' => [
                'title' => ' En Revisión',
                'color' => 'info',
                'proyectos' => Proyecto::where('tipo_seguimiento', 'investigacion')
                    ->where('estado_kanban', 'en_revision')
                    ->orderBy('updated_at', 'desc')
                    ->get(),
            ],
            'completado' => [
                'title' => '✅ Completado',
                'color' => 'success',
                'proyectos' => Proyecto::where('tipo_seguimiento', 'investigacion')
                    ->where('estado_kanban', 'completado')
                    ->orderBy('updated_at', 'desc')
                    ->get(),
            ],
        ];
    }
}