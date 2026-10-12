<?php

use App\Modules\Agenda\Http\Controllers\AgendaController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:turista'])->get('/agenda', [AgendaController::class, 'index'])->name('agenda.index');
