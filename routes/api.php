<?php

use App\Http\Controllers\ProyectoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Aquí puedes registrar las rutas de API para tu aplicación. Estas rutas
| son cargadas por el RouteServiceProvider y todas serán asignadas al
| grupo de middleware "api".
|
*/

// Rutas API para gestión de proyectos
Route::prefix('proyectos')->name('api.proyectos.')->group(function () {
    // GET /api/proyectos - Listar todos los proyectos
    Route::get('/', [ProyectoController::class, 'index'])->name('index');
    
    // POST /api/proyectos - Crear un nuevo proyecto
    Route::post('/', [ProyectoController::class, 'store'])->name('store');
    
    // GET /api/proyectos/{id} - Obtener un proyecto por ID
    Route::get('/{id}', [ProyectoController::class, 'show'])->name('show');
    
    // PUT /api/proyectos/{id} - Actualizar un proyecto por ID
    Route::put('/{id}', [ProyectoController::class, 'update'])->name('update');
    
    // DELETE /api/proyectos/{id} - Eliminar un proyecto por ID
    Route::delete('/{id}', [ProyectoController::class, 'destroy'])->name('destroy');
});

// Ruta para obtener los estados disponibles
Route::get('proyectos-estados', function () {
    return response()->json([
        'success' => true,
        'data' => \App\Models\Proyecto::ESTADOS,
        'message' => 'Estados disponibles para proyectos'
    ]);
})->name('api.proyectos.estados');

// Rutas API para UF (Unidad de Fomento)
Route::prefix('uf')->name('api.uf.')->group(function () {
    // GET /api/uf - Obtener valor actual de la UF
    Route::get('/', [\App\Http\Controllers\UfController::class, 'getValorActual'])->name('actual');
    
    // GET /api/uf/fecha - Obtener valor de UF por fecha
    Route::get('/fecha', [\App\Http\Controllers\UfController::class, 'getValorPorFecha'])->name('fecha');
    
    // POST /api/uf/convertir - Convertir pesos a UF
    Route::post('/convertir', [\App\Http\Controllers\UfController::class, 'convertirPesosAUf'])->name('convertir');
    
    // DELETE /api/uf/cache - Limpiar cache
    Route::delete('/cache', [\App\Http\Controllers\UfController::class, 'limpiarCache'])->name('limpiar-cache');
    
    // GET /api/uf/cache - Estado del cache
    Route::get('/cache', [\App\Http\Controllers\UfController::class, 'estadoCache'])->name('estado-cache');
}); 