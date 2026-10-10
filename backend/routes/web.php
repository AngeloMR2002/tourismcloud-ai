<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Dashboard\Controllers\DashboardController;
use App\Modules\Agenda\Controllers\AgendaController;
use App\Modules\Itinerarios\Controllers\ItinerariosController;

// redirigir la raíz al dashboard por defecto
Route::get('/', function () {
    return redirect('/dashboard');
});

// rutas conectadas a los controladores modulares
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/agenda', [AgendaController::class, 'index'])->name('agenda.index');

// modulo Itinerarios 
Route::get('/itinerarios', [ItinerariosController::class, 'index'])->name('itinerarios.index');
Route::get('/itinerarios/{id}', [ItinerariosController::class, 'show'])->name('itinerarios.show');
Route::post('/itinerarios', [ItinerariosController::class, 'store'])->name('itinerarios.store');
Route::delete('/itinerarios/{id}', [ItinerariosController::class, 'destroy'])->name('itinerarios.destroy');