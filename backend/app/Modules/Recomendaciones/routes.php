<?php

use App\Modules\Recomendaciones\Http\Controllers\RecomendacionController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')
    ->middleware(['auth:sanctum', 'throttle:recomendaciones'])
    ->group(function () {
        Route::post('/recomendaciones', [RecomendacionController::class, 'generar']);
    });