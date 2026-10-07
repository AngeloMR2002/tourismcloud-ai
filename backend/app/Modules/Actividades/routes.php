<?php

use App\Modules\Actividades\Http\Controllers\Api\V1\CatalogoActividadController as ApiCatalogoActividadController;
use App\Modules\Actividades\Http\Controllers\Auth\LoginController;
use App\Modules\Actividades\Http\Controllers\Catalogo\CatalogoActividadController;
use App\Modules\Actividades\Http\Controllers\Operador\ImportacionController;
use App\Modules\Actividades\Http\Controllers\Operador\OperadorActividadController;
use App\Modules\Actividades\Http\Controllers\Operador\RevisionController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function (): void {
    Route::get('/login', [LoginController::class, 'create'])->middleware('guest')->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware(['guest', 'throttle:login']);
    Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

    Route::prefix('catalogo')->name('catalogo.')->group(function (): void {
        Route::get('/actividades', [CatalogoActividadController::class, 'index'])->name('actividades.index');
        Route::get('/actividades/{id}', [CatalogoActividadController::class, 'show'])->name('actividades.show');
    });

    Route::prefix('operador')->name('operador.')->middleware(['auth', 'can:gestionar-catalogo'])->group(function (): void {
        Route::resource('actividades', OperadorActividadController::class)->except('show')->names('actividades');
        Route::patch('actividades/{id}/toggle-estado', [OperadorActividadController::class, 'toggleEstado'])
            ->name('actividades.toggle-estado');


        Route::middleware('can:revisar-catalogo')->group(function (): void {
            Route::get('/importaciones', [ImportacionController::class, 'index'])->name('importaciones.index');
            Route::post('/importaciones', [ImportacionController::class, 'store'])
                ->middleware('throttle:2,1')->name('importaciones.store');
            Route::post('/importaciones/{id}/aprobar', [ImportacionController::class, 'aprobar'])->name('importaciones.aprobar');
            Route::get('/revision', [RevisionController::class, 'index'])->name('revision.index');
            Route::post('/revision/{tipo}/{id}/aprobar', [RevisionController::class, 'aprobar'])
                ->whereIn('tipo', ['actividades', 'rutas'])->name('revision.aprobar');
        });
    });
});

Route::prefix('api/v1/catalogo')->name('api.v1.catalogo.')->middleware(['api', 'throttle:60,1'])->group(function (): void {
    Route::get('actividades', [ApiCatalogoActividadController::class, 'index'])->name('actividades');
});
