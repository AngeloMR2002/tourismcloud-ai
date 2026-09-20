<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Modules\Dashboard\Controllers\DashboardController;
use App\Modules\Agenda\Controllers\AgendaController;
use App\Modules\Itinerarios\Controllers\ItinerariosController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// las rutas de TourismCloud AI
Route::get('/dashboard', [DashboardController::class, 'index']);
Route::get('/agenda', [AgendaController::class, 'index']);
Route::get('/itinerarios', [ItinerariosController::class, 'index']);