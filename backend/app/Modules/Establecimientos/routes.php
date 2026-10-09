<?php

use App\Modules\Establecimientos\Http\Controllers\ProveedorEstablecimientoController;
use Illuminate\Support\Facades\Route;

// =============================================================================
// PANEL PROVEEDOR — gestión de "Mis Establecimientos"
//
// TODO: Activar middleware 'role:proveedor' cuando feature/auth defina el
//       alias real en bootstrap/app.php.
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
