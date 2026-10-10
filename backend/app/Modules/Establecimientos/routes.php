<?php

use App\Modules\Establecimientos\Http\Controllers\CatalogoEstablecimientoController;
use App\Modules\Establecimientos\Http\Controllers\ProveedorEstablecimientoController;
use Illuminate\Support\Facades\Route;

// =============================================================================
// CATÁLOGO PÚBLICO — Establecimientos (accesible por turistas y visitantes sin auth)
// =============================================================================
Route::prefix('catalogo')->name('catalogo.')->group(function () {
    Route::get('/establecimientos', [CatalogoEstablecimientoController::class, 'index'])
        ->name('establecimientos.index');
    Route::get('/establecimientos/{establecimiento}', [CatalogoEstablecimientoController::class, 'show'])
        ->name('establecimientos.show');
});

// =============================================================================
// PANEL PROVEEDOR — gestión de "Mis Establecimientos"
// =============================================================================
Route::prefix('proveedor')->name('proveedor.')->group(function () {
    Route::resource('establecimientos', ProveedorEstablecimientoController::class)
        ->names([
            'index' => 'establecimientos.index',
            'create' => 'establecimientos.create',
            'store' => 'establecimientos.store',
            'edit' => 'establecimientos.edit',
            'update' => 'establecimientos.update',
            'destroy' => 'establecimientos.destroy',
        ]);

    Route::patch(
        '/establecimientos/{establecimiento}/toggle-estado',
        [ProveedorEstablecimientoController::class, 'toggleEstado']
    )->name('establecimientos.toggle-estado');

    Route::delete(
        '/establecimientos/{establecimiento}/imagenes/{imagen}',
        [ProveedorEstablecimientoController::class, 'eliminarImagen']
    )->name('establecimientos.imagenes.destroy');
});
