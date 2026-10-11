<?php

use App\Modules\Rutas\Http\Controllers\CatalogoRutaController;
use App\Modules\Rutas\Http\Controllers\OperadorRutaController;
use Illuminate\Support\Facades\Route;

Route::prefix('catalogo')->name('catalogo.')->group(function (): void {
    Route::get('/rutas', [CatalogoRutaController::class, 'index'])->name('rutas.index');
    Route::get('/rutas/{ruta}', [CatalogoRutaController::class, 'show'])->name('rutas.show');
});

Route::middleware(['auth', 'role:operador_turistico'])->prefix('operador')->name('operador.')->group(function (): void {
    Route::resource('rutas', OperadorRutaController::class)->except(['show']);

    Route::patch('/rutas/{ruta}/toggle-estado', [OperadorRutaController::class, 'toggleEstado'])
        ->name('rutas.toggle-estado');
});
