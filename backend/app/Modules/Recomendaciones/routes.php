<?php

use App\Modules\Recomendaciones\Http\Controllers\RecomendacionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:turista'])
    ->get('/recomendaciones', [RecomendacionController::class, 'index'])
    ->name('recomendaciones.index');
