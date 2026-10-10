<?php

use App\Modules\Establecimientos\Http\Controllers\EstablecimientoCatalogoController;
use App\Modules\Establecimientos\Http\Controllers\ProveedorEstablecimientoController;
use Illuminate\Support\Facades\Route;

// `loadRoutesFrom` no aplica el grupo 'web' (sesión, CSRF, bindings): hay que declararlo.
Route::middleware('web')->group(function () {

    // ─── Catálogo público ────────────────────────────────────────────────
    Route::prefix('catalogo')->name('catalogo.')->group(function () {
        Route::get('/establecimientos', [EstablecimientoCatalogoController::class, 'index'])
            ->name('establecimientos.index');
        Route::get('/establecimientos/{establecimiento}', [EstablecimientoCatalogoController::class, 'show'])
            ->name('establecimientos.show');
    });

    // ─── Panel proveedor ─────────────────────────────────────────────────
    // TODO: activar ->middleware(['auth', 'role:proveedor']) cuando llegue feature/auth.
    Route::prefix('proveedor')->name('proveedor.')->group(function () {
        Route::resource('establecimientos', ProveedorEstablecimientoController::class)
            ->except(['show']);

        Route::patch(
            '/establecimientos/{establecimiento}/toggle-estado',
            [ProveedorEstablecimientoController::class, 'toggleEstado']
        )->name('establecimientos.toggle-estado');
    });
});