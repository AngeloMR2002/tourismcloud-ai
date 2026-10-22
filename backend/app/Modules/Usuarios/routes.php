<?php

use App\Modules\Usuarios\Http\Controllers\AuthController;
use App\Modules\Usuarios\Http\Controllers\FavoritoController;
use App\Modules\Usuarios\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

// Rutas para invitados (no logueados)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    Route::get('/registro', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/registro', [AuthController::class, 'register'])->name('register.post');

    // Autenticación con Google
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

    // Recuperación de contraseña con código al correo
    Route::get('/recuperar-password', [AuthController::class, 'showForgotForm'])->name('password.request');
    Route::post('/recuperar-password', [AuthController::class, 'sendResetCode'])->name('password.email');
    Route::get('/verificar-codigo', [AuthController::class, 'showVerifyCodeForm'])->name('password.verify');
    Route::post('/verificar-codigo', [AuthController::class, 'verifyCodeAndReset'])->name('password.update');
});

// Rutas para usuarios autenticados
Route::middleware('auth')->group(function () {
    Route::get('/favoritos', [FavoritoController::class, 'index'])->name('favoritos.index');
    Route::get('/favoritos/guardar/{tipo}/{id}', [FavoritoController::class, 'show'])->whereNumber('id')->name('favoritos.confirmar');
    Route::post('/favoritos/{tipo}/{id}', [FavoritoController::class, 'store'])->whereNumber('id')->name('favoritos.store');
    Route::delete('/favoritos/{tipo}/{id}', [FavoritoController::class, 'destroy'])->whereNumber('id')->name('favoritos.destroy');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Rutas administrativas para gestión de usuarios
Route::middleware(['auth', 'role:administrador'])->prefix('admin')->name('admin.')->group(function () {
    Route::patch('usuarios/{usuario}/estado', [UsuarioController::class, 'toggleEstado'])->name('usuarios.toggle-estado');

    Route::resource('usuarios', UsuarioController::class);
});
