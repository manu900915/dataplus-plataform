<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PresupuestoController;
use App\Http\Controllers\ProyectoKanbanController;

// Landing page
Route::get('/', function () {
    return view('welcome');
});

// Redirecciones para evitar confusiones
Route::redirect('/login', '/admin/login');
Route::redirect('/dashboard', '/admin');
Route::redirect('/admin/edit-profile', '/admin/profile');

// Tus rutas personalizadas
Route::get('/proyectos/{proyecto}/presupuesto/excel', [PresupuestoController::class, 'exportarExcel'])
    ->name('proyectos.presupuesto.excel');
    
Route::get('/proyectos/{proyecto}/presupuesto/pdf', [PresupuestoController::class, 'exportarPDF'])
    ->name('proyectos.presupuesto.pdf');

Route::patch('/proyectos/{proyecto}/kanban', [ProyectoKanbanController::class, 'update'])
    ->name('proyectos.kanban.update');

// Rutas de autenticación de Breeze (necesarias para el middleware)
require __DIR__.'/auth.php';