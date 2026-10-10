<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Usuarios\Http\Controllers\AuthController;
use App\Modules\Usuarios\Http\Controllers\UsuarioController;

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
Route::middleware(['auth', 'role:administrador'])->prefix('admin')->name('admin.')->group(function () {
    Route::patch('usuarios/{usuario}/password', [UsuarioController::class, 'resetPassword'])->name('usuarios.password');
    Route::patch('usuarios/{usuario}/estado', [UsuarioController::class, 'toggleEstado'])->name('usuarios.toggle-estado');

    Route::resource('usuarios', UsuarioController::class);
});