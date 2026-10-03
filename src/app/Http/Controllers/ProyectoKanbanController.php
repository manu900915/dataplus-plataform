<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use Illuminate\Http\Request;

class ProyectoKanbanController extends Controller
{
    public function update(Proyecto $proyecto, Request $request)
    {
        $request->validate([
            'estado_kanban' => 'required|in:por_hacer,en_progreso,en_revision,completado',
        ]);

        $proyecto->update([
            'estado_kanban' => $request->estado_kanban,
        ]);

        return back()->with('success', 'Estado actualizado correctamente');
    }
}