<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Usuarios\Http\Controllers\AuthController;

// Rutas para invitados (no logueados)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    
    Route::get('/registro', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/registro', [AuthController::class, 'register'])->name('register.post');
});

// Rutas para usuarios autenticados
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Rutas administrativas para gestión de usuarios
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('usuarios', \App\Modules\Usuarios\Http\Controllers\UsuarioController::class);
});