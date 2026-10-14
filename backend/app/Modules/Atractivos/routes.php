<?php

use App\Modules\Atractivos\Http\Controllers\CatalogoAtractivoController;
use App\Modules\Atractivos\Http\Controllers\OperadorAtractivoController;
use Illuminate\Support\Facades\Route;

// =============================================================================
// CATÁLOGO PÚBLICO — Atractivos (accesible por turistas y visitantes sin auth)
// =============================================================================
Route::prefix('catalogo')->name('catalogo.')->group(function () {
    Route::get('/atractivos', [CatalogoAtractivoController::class, 'index'])
        ->name('atractivos.index');
    Route::get('/atractivos/{atractivo}', [CatalogoAtractivoController::class, 'show'])
        ->name('atractivos.show');
});

// =============================================================================
// PANEL OPERADOR TURÍSTICO — gestión de "Mis Atractivos"
// =============================================================================
Route::prefix('operador')->name('operador.')->group(function () {
    Route::resource('atractivos', OperadorAtractivoController::class)
        ->names([
            'index' => 'atractivos.index',
            'create' => 'atractivos.create',
            'store' => 'atractivos.store',
            'edit' => 'atractivos.edit',
            'update' => 'atractivos.update',
            'destroy' => 'atractivos.destroy',
        ]);

    Route::patch(
        '/atractivos/{atractivo}/toggle-estado',
        [OperadorAtractivoController::class, 'toggleEstado']
    )->name('atractivos.toggle-estado');

    Route::delete(
        '/atractivos/{atractivo}/imagenes/{imagen}',
        [OperadorAtractivoController::class, 'eliminarImagen']
    )->name('atractivos.imagenes.destroy');
});
