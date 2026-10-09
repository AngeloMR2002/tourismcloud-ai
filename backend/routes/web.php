<?php

use Illuminate\Support\Facades\Route;

Route::view('/login', 'principal.login')->name('login');
Route::view('/usuarios', 'principal.workspace', ['section' => 'usuarios'])->name('principal.usuarios');
Route::view('/destinos', 'principal.workspace', ['section' => 'destinos'])->name('principal.destinos');
Route::view('/organizaciones', 'principal.workspace', ['section' => 'organizaciones'])->name('principal.organizaciones');
Route::view('/permisos', 'principal.workspace', ['section' => 'permisos'])->name('principal.permisos');

// ─── Página de inicio ─────────────────────────────────────────────────────────
Route::get('/', function () {
    return view('welcome');
});

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

// =============================================================================
// PANEL OPERADOR TURÍSTICO — gestión de "Mis Atractivos"
//
// TODO: Activar middleware 'role:operador_turistico' cuando feature/auth
//       defina el alias real en app/Http/Kernel.php (o bootstrap/app.php).
//       Por ahora se usa solo 'auth' comentado para no romper el arranque.
// =============================================================================
Route::prefix('operador')->name('operador.')->group(
    // ->middleware(['auth', 'role:operador_turistico'])  // <-- activar post-merge feature/auth
    function () {
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
    }
);

// =============================================================================
// PANEL PROVEEDOR — gestión de "Mis Establecimientos"
//
// TODO: Activar middleware 'role:proveedor' cuando feature/auth defina el
//       alias real en app/Http/Kernel.php (o bootstrap/app.php).
// =============================================================================
Route::prefix('proveedor')->name('proveedor.')->group(
    // ->middleware(['auth', 'role:proveedor'])  // <-- activar post-merge feature/auth
    function () {
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
    }
);
