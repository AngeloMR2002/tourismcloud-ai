<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Destinos\Http\Controllers\DestinoController;

Route::middleware(['auth', 'role:administrador'])->prefix('admin')->name('admin.')->group(function () {
    
    Route::resource('destinos', DestinoController::class)->except(['show', 'destroy']);
    
    Route::patch('/destinos/{destino}/toggle-estado', [DestinoController::class, 'toggleEstado'])
        ->name('destinos.toggle-estado');
});