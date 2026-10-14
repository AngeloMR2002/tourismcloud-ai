<?php

use App\Modules\Dashboard\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:administrador,turista'])->get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
