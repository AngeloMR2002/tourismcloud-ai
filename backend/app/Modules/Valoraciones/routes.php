<?php

use App\Modules\Valoraciones\Http\Controllers\ValoracionController;
use Illuminate\Support\Facades\Route;

// Rutas de Valoraciones y Reseñas (requieren autenticación)
Route::middleware(['web', 'auth'])->group(function () {
    Route::post('/valoraciones', [ValoracionController::class, 'store'])->name('valoraciones.store');
    Route::delete('/valoraciones/{valoracion}', [ValoracionController::class, 'destroy'])->name('valoraciones.destroy');
});
