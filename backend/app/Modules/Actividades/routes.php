<?php

use App\Modules\Actividades\Http\Controllers\CatalogoActividadController;
use App\Modules\Actividades\Http\Controllers\OperadorActividadController;
use Illuminate\Support\Facades\Route;

Route::prefix('catalogo')->name('catalogo.')->group(function (): void {
    Route::get('actividades', [CatalogoActividadController::class, 'index'])
        ->name('actividades.index');
    Route::get('actividades/{actividad}', [CatalogoActividadController::class, 'show'])
        ->name('actividades.show');
});

Route::middleware(['auth', 'role:operador_turistico'])
    ->prefix('operador')
    ->name('operador.')
    ->group(function (): void {
        Route::patch('actividades/{actividad}/toggle-estado', [OperadorActividadController::class, 'toggleEstado'])
            ->name('actividades.toggle-estado');

        Route::resource('actividades', OperadorActividadController::class)
            ->except(['show'])
            ->parameters(['actividades' => 'actividad']);
    });
