<?php

namespace App\Filament\Resources\ProyectoResource\Pages;

use App\Filament\Resources\ProyectoResource;
use App\Models\Proyecto;
use Filament\Resources\Pages\Page;

class KanbanProyectos extends Page
{
    protected static string $resource = ProyectoResource::class;
    protected static string $view = 'filament.resources.proyecto-resource.pages.kanban-proyectos';
    protected static ?string $title = 'Seguimiento de Proyectos I+D';
    protected static ?string $navigationIcon = 'heroicon-o-queue-list';
    protected static ?string $navigationLabel = 'Seguimiento';
    protected static ?int $navigationSort = 1;

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
                'title' => '🔍 En Revisión',
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