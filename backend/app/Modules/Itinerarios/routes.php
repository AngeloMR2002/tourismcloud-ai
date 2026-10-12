<?php

use App\Modules\Itinerarios\Http\Controllers\ItinerarioController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:turista'])->prefix('itinerarios')->name('itinerarios.')->group(function (): void {
    Route::get('/', [ItinerarioController::class, 'index'])->name('index');
    Route::get('/crear', [ItinerarioController::class, 'create'])->name('create');
    Route::post('/', [ItinerarioController::class, 'store'])->name('store');
    Route::get('/{itinerario}', [ItinerarioController::class, 'show'])->name('show');
    Route::delete('/{itinerario}', [ItinerarioController::class, 'destroy'])->name('destroy');
});
