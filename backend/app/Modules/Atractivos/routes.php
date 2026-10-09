<?php

use App\Modules\Atractivos\Http\Controllers\OperadorAtractivoController;
use Illuminate\Support\Facades\Route;

// =============================================================================
// PANEL OPERADOR TURÍSTICO — gestión de "Mis Atractivos"
//
// TODO: Activar middleware 'role:operador_turistico' cuando feature/auth
//       defina el alias real en bootstrap/app.php.
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
