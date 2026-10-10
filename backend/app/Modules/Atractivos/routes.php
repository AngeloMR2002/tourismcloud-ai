<?php

use App\Modules\Atractivos\Http\Controllers\AtractivoCatalogoController;
use App\Modules\Atractivos\Http\Controllers\OperadorAtractivoController;
use Illuminate\Support\Facades\Route;

// `loadRoutesFrom` no aplica el grupo 'web' (sesión, CSRF, bindings): hay que declararlo.
Route::middleware('web')->group(function () {

    // ─── Catálogo público ────────────────────────────────────────────────
    Route::prefix('catalogo')->name('catalogo.')->group(function () {
        Route::get('/atractivos', [AtractivoCatalogoController::class, 'index'])
            ->name('atractivos.index');
        Route::get('/atractivos/{atractivo}', [AtractivoCatalogoController::class, 'show'])
            ->name('atractivos.show');
    });

    // ─── Panel operador turístico ────────────────────────────────────────
    // TODO: activar ->middleware(['auth', 'role:operador_turistico']) cuando llegue feature/auth.
    Route::prefix('operador')->name('operador.')->group(function () {
        Route::resource('atractivos', OperadorAtractivoController::class)
            ->except(['show']);

        Route::patch(
            '/atractivos/{atractivo}/toggle-estado',
            [OperadorAtractivoController::class, 'toggleEstado']
        )->name('atractivos.toggle-estado');
    });
});