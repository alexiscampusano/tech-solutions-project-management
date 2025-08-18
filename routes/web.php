<?php

use Illuminate\Support\Facades\Route;

// Main route
Route::get('/', function () {
    return redirect()->route('proyectos.index');
});

// Authentication routes (only views)
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

// ===== WEB ROUTES (Only views - API-First Architecture) =====
// All CRUD operations use the API via JavaScript

// Project list
Route::get('/proyectos', function () {
    return view('proyectos.index');
})->name('proyectos.index');

// Create project form
Route::get('/proyectos/create', function () {
    return view('proyectos.create');
})->name('proyectos.create');

// View project details
Route::get('/proyectos/{id}', function ($id) {
    return view('proyectos.show', ['proyectoId' => $id]);
})->name('proyectos.show');

// Edit project form  
Route::get('/proyectos/{id}/edit', function ($id) {
    return view('proyectos.edit', ['proyectoId' => $id]);
})->name('proyectos.edit');

// Alternative route for the dashboard
Route::get('/dashboard', function () {
    return redirect()->route('proyectos.index');
})->name('dashboard');
