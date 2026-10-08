<?php

namespace App\Filament\Resources\ProyectoResource\Pages;

use App\Filament\Resources\ProyectoResource;
use App\Models\Proyecto;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Builder;

class KanbanProyectos extends Page
{
    protected static string $resource = ProyectoResource::class;

    protected static string $view = 'filament.resources.proyecto-resource.pages.kanban-proyectos';

    protected static ?string $title = 'Seguimiento de Proyectos';

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';

    protected static ?string $navigationLabel = 'Seguimiento';

    protected static ?int $navigationSort = 1;

    public string $filtroTipo = 'todos';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('nuevo_proyecto')
                ->label('Nuevo Proyecto')
                ->icon('heroicon-m-plus')
                ->color('success')
                ->url(\App\Filament\Resources\ProyectoResource::getUrl('create')),
        ];
    }

    public function setFiltro(string $tipo): void
    {
        $this->filtroTipo = $tipo;
    }

    protected function getBaseQuery(): Builder
    {
        $query = Proyecto::query()->with(['tipoProyecto', 'cliente', 'responsable']);

        if ($this->filtroTipo === 'instalacion') {
            $query->where(function (Builder $q) {
                $q->where('tipo_seguimiento', 'instalacion')
                  ->orWhereNull('tipo_seguimiento');
            });
        } elseif ($this->filtroTipo === 'investigacion') {
            $query->where('tipo_seguimiento', 'investigacion');
        }

        return $query;
    }

    public function getCounts(): array
    {
        return [
            'todos' => Proyecto::count(),
            'instalacion' => Proyecto::where(function (Builder $q) {
                $q->where('tipo_seguimiento', 'instalacion')
                  ->orWhereNull('tipo_seguimiento');
            })->count(),
            'investigacion' => Proyecto::where('tipo_seguimiento', 'investigacion')->count(),
        ];
    }

    public function getColumns(): array
    {
        return [
            'por_hacer' => [
                'title' => '📋 Por Hacer',
                'color' => 'gray',
                'proyectos' => $this->getBaseQuery()
                    ->where(function (Builder $query) {
                        $query->where('estado_kanban', 'por_hacer')
                            ->orWhere(function (Builder $q) {
                                $q->where('estado', 'borrador')
                                    ->where(function (Builder $sub) {
                                        $sub->whereNull('estado_kanban')
                                            ->orWhereNotIn('estado_kanban', ['en_progreso', 'en_revision', 'completado']);
                                    });
                            })
                            ->orWhere(function (Builder $q) {
                                $q->whereNull('estado_kanban')
                                    ->whereNotIn('estado', ['en_progreso', 'completado']);
                            });
                    })
                    ->where('estado', '!=', 'completado')
                    ->where('estado_kanban', '!=', 'completado')
                    ->where('estado', '!=', 'en_progreso')
                    ->where('estado_kanban', '!=', 'en_progreso')
                    ->where('estado_kanban', '!=', 'en_revision')
                    ->orderBy('created_at', 'desc')
                    ->get(),
            ],
            'en_progreso' => [
                'title' => '🔄 En Progreso',
                'color' => 'warning',
                'proyectos' => $this->getBaseQuery()
                    ->where(function (Builder $query) {
                        $query->where('estado_kanban', 'en_progreso')
                            ->orWhere(function (Builder $q) {
                                $q->where('estado', 'en_progreso')
                                    ->where(function (Builder $sub) {
                                        $sub->whereNull('estado_kanban')
                                            ->orWhereNotIn('estado_kanban', ['en_revision', 'completado']);
                                    });
                            });
                    })
                    ->where('estado', '!=', 'completado')
                    ->where('estado_kanban', '!=', 'completado')
                    ->orderBy('updated_at', 'desc')
                    ->get(),
            ],
            'en_revision' => [
                'title' => '🔍 En Revisión',
                'color' => 'info',
                'proyectos' => $this->getBaseQuery()
                    ->where('estado_kanban', 'en_revision')
                    ->where('estado', '!=', 'completado')
                    ->where('estado_kanban', '!=', 'completado')
                    ->orderBy('updated_at', 'desc')
                    ->get(),
            ],
            'completado' => [
                'title' => '✅ Completado',
                'color' => 'success',
                'proyectos' => $this->getBaseQuery()
                    ->where(function (Builder $query) {
                        $query->where('estado_kanban', 'completado')
                            ->orWhere('estado', 'completado');
                    })
                    ->orderBy('updated_at', 'desc')
                    ->get(),
            ],
        ];
    }
}
