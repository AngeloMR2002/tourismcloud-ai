<?php

use App\Modules\Rutas\Http\Controllers\Api\V1\CatalogoRutaController as ApiCatalogoRutaController;
use App\Modules\Rutas\Http\Controllers\Catalogo\CatalogoRutaController;
use App\Modules\Rutas\Http\Controllers\Operador\OperadorRutaController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function (): void {
    Route::prefix('catalogo')->name('catalogo.')->group(function (): void {
        Route::get('/rutas', [CatalogoRutaController::class, 'index'])->name('rutas.index');
        Route::get('/rutas/{id}', [CatalogoRutaController::class, 'show'])->name('rutas.show');
    });

    Route::prefix('operador')->name('operador.')->middleware(['auth', 'can:gestionar-catalogo'])->group(function (): void {
        Route::resource('rutas', OperadorRutaController::class)->except('show')->names('rutas');
        Route::patch('rutas/{id}/toggle-estado', [OperadorRutaController::class, 'toggleEstado'])->name('rutas.toggle-estado');
    });
});

Route::prefix('api/v1/catalogo')->name('api.v1.catalogo.')->middleware(['api', 'throttle:60,1'])->group(function (): void {
    Route::get('rutas', [ApiCatalogoRutaController::class, 'index'])->name('rutas');
});
