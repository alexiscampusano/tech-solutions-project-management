<?php

use App\Http\Controllers\Api\ProyectoController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UfController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here you can register the API routes for your application. These routes
| are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

// Authentication API routes (public - no middleware)
Route::prefix('auth')->name('api.auth.')->group(function () {
    // POST /api/auth/register - Register user
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    
    // POST /api/auth/login - Login
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});

// Authentication API routes (protected - with JWT middleware)
Route::prefix('auth')->middleware('auth:api')->name('api.auth.')->group(function () {
    // POST /api/auth/logout - Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // GET /api/auth/me - Authenticated user data
    Route::get('/me', [AuthController::class, 'me'])->name('me');
    
    // POST /api/auth/refresh - Refresh token
    Route::post('/refresh', [AuthController::class, 'refresh'])->name('refresh');
});

// API routes for project management
Route::prefix('proyectos')->name('api.proyectos.')->group(function () {
    // Public routes (no authentication)
    Route::get('/', [ProyectoController::class, 'index'])->name('index');
    Route::get('/estados', [ProyectoController::class, 'estados'])->name('estados');
    Route::get('/{id}', [ProyectoController::class, 'show'])->name('show');
    
    // Protected routes (require JWT)
    Route::middleware('auth:api')->group(function () {
        Route::post('/', [ProyectoController::class, 'store'])->name('store');
        Route::put('/{id}', [ProyectoController::class, 'update'])->name('update');
        Route::delete('/{id}', [ProyectoController::class, 'destroy'])->name('destroy');
    });
});

// API routes for UF (Unidad de Fomento)
Route::prefix('uf')->name('api.uf.')->group(function () {
    // GET /api/uf - Get current UF value
    Route::get('/', [UfController::class, 'getValorActual'])->name('actual');
    
    // GET /api/uf/date - Get UF value by date
    Route::get('/date', [UfController::class, 'getValorPorFecha'])->name('fecha');
    
    // POST /api/uf/convert - Convert pesos to UF
    Route::post('/convert', [UfController::class, 'convertirPesosAUf'])->name('convertir');
    
    // DELETE /api/uf/cache - Clear cache
    Route::delete('/cache', [UfController::class, 'limpiarCache'])->name('limpiar-cache');
    
    // GET /api/uf/cache - Cache state
    Route::get('/cache', [UfController::class, 'estadoCache'])->name('estado-cache');
}); 