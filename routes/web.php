<?php

use App\Http\Controllers\ProyectoController;
use Illuminate\Support\Facades\Route;

// Ruta principal
Route::get('/', function () {
    return redirect()->route('proyectos.index');
});

// Rutas web para gestión de proyectos con vistas
Route::resource('proyectos', ProyectoController::class);

// Ruta alternativa para el dashboard
Route::get('/dashboard', function () {
    return redirect()->route('proyectos.index');
})->name('dashboard');
