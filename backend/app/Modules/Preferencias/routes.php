<?php

use App\Modules\Preferencias\Http\Controllers\PreferenciaController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function () {
    // Tendencias para el proveedor: no expone preferencias de un turista en particular.
    Route::middleware('role:proveedor')->group(function () {
        Route::get('/preferencias/recientes', [PreferenciaController::class, 'recientes'])->name('preferencias.recientes');
    });

    // El turista registra su propia preferencia desde aquí; el administrador puede registrarla por cualquier turista.
    Route::middleware('role:administrador,turista')->group(function () {
        Route::get('/preferencias/create', [PreferenciaController::class, 'create'])->name('preferencias.create');
        Route::post('/preferencias', [PreferenciaController::class, 'store'])->name('preferencias.store');
    });

    // Gestión administrativa de cualquier preferencia por id: nunca accesible a un turista.
    Route::middleware('role:administrador')->group(function () {
        Route::get('/preferencias', [PreferenciaController::class, 'index'])->name('preferencias.index');
        Route::get('/preferencias/{preferencia}', [PreferenciaController::class, 'show'])->name('preferencias.show');
        Route::get('/preferencias/{preferencia}/edit', [PreferenciaController::class, 'edit'])->name('preferencias.edit');
        Route::put('/preferencias/{preferencia}', [PreferenciaController::class, 'update'])->name('preferencias.update');
        Route::delete('/preferencias/{preferencia}', [PreferenciaController::class, 'destroy'])->name('preferencias.destroy');
    });

    // Autoservicio del turista: el controlador además comprueba que {turistaId} sea el usuario autenticado.
    Route::middleware('role:turista')->group(function () {
        Route::get('/turista/{turistaId}/preferencias', [PreferenciaController::class, 'turista'])->name('preferencias.turista');
        Route::get('/turista/{turistaId}/preferencias/{preferenciaId}/edit', [PreferenciaController::class, 'editarTurista'])->name('preferencias.turista.edit');
        Route::put('/turista/{turistaId}/preferencias/{preferenciaId}', [PreferenciaController::class, 'actualizarTurista'])->name('preferencias.turista.update');
    });
});
