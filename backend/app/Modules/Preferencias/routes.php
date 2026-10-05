<?php

use App\Modules\Preferencias\Http\Controllers\PreferenciaController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {

    Route::get(
        '/preferencias/recientes',
        [PreferenciaController::class, 'recientes']
    )->name('preferencias.recientes');

    Route::resource(
        'preferencias',
        PreferenciaController::class
    );

    Route::get(
        '/turista/{turistaId}/preferencias',
        [PreferenciaController::class, 'turista']
    )->name('preferencias.turista');

    Route::get(
        '/turista/{turistaId}/preferencias/{preferenciaId}/edit',
        [PreferenciaController::class, 'editarTurista']
    )->name('preferencias.turista.edit');

    Route::put(
        '/turista/{turistaId}/preferencias/{preferenciaId}',
        [PreferenciaController::class, 'actualizarTurista']
    )->name('preferencias.turista.update');
});