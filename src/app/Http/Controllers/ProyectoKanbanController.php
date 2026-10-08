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

        $updateData = [
            'estado_kanban' => $request->estado_kanban,
        ];

        // Sincronizar estado general acorde al flujo kanban
        if ($request->estado_kanban === 'completado') {
            $updateData['estado'] = 'completado';
        } elseif (in_array($request->estado_kanban, ['en_progreso', 'en_revision'])) {
            $updateData['estado'] = 'en_progreso';
        } elseif ($request->estado_kanban === 'por_hacer' && $proyecto->estado === 'completado') {
            $updateData['estado'] = 'en_progreso';
        }

        $proyecto->update($updateData);

        return back()->with('success', 'Fase de seguimiento actualizada correctamente');
    }
}
