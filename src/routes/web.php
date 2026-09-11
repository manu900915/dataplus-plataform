<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PresupuestoController;
use App\Http\Controllers\ProyectoKanbanController;

// Redirigir dashboard de Breeze al panel de Filament
Route::redirect('/dashboard', '/admin');

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/proyectos/{proyecto}/presupuesto/excel', [PresupuestoController::class, 'exportarExcel'])
    ->name('proyectos.presupuesto.excel');
    
Route::get('/proyectos/{proyecto}/presupuesto/pdf', [PresupuestoController::class, 'exportarPDF'])
    ->name('proyectos.presupuesto.pdf');

Route::patch('/proyectos/{proyecto}/kanban', [ProyectoKanbanController::class, 'update'])
    ->name('proyectos.kanban.update'); 

Route::patch('/proyectos/{proyecto}/kanban', function (\App\Models\Proyecto $proyecto, \Illuminate\Http\Request $request) {
    $request->validate([
        'estado_kanban' => 'required|in:por_hacer,en_progreso,en_revision,completado',
    ]);

    $proyecto->update([
        'estado_kanban' => $request->estado_kanban,
    ]);

    return back()->with('success', 'Estado actualizado correctamente');
})->name('proyectos.kanban.update');

require __DIR__.'/auth.php';
