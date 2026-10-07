<?php

use App\Modules\Catalogo\Http\Controllers\CatalogoController;
use Illuminate\Support\Facades\Route;

// =============================================================================
// CATÁLOGO PÚBLICO — accesible por cualquier visitante (turistas, sin auth)
// =============================================================================
Route::prefix('catalogo')->name('catalogo.')->group(function () {

    // Atractivos
    Route::get('/atractivos', [CatalogoController::class, 'atractivos'])
        ->name('atractivos.index');
    Route::get('/atractivos/{atractivo}', [CatalogoController::class, 'atractivoDetalle'])
        ->name('atractivos.show');

    // Establecimientos
    Route::get('/establecimientos', [CatalogoController::class, 'establecimientos'])
        ->name('establecimientos.index');
    Route::get('/establecimientos/{establecimiento}', [CatalogoController::class, 'establecimientoDetalle'])
        ->name('establecimientos.show');
});
