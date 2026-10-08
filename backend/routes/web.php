<?php

use Illuminate\Support\Facades\Route;

Route::view('/login', 'principal.login')->name('login');
Route::view('/usuarios', 'principal.workspace', ['section' => 'usuarios'])->name('principal.usuarios');
Route::view('/destinos', 'principal.workspace', ['section' => 'destinos'])->name('principal.destinos');
Route::view('/organizaciones', 'principal.workspace', ['section' => 'organizaciones'])->name('principal.organizaciones');
Route::view('/permisos', 'principal.workspace', ['section' => 'permisos'])->name('principal.permisos');

// ─── Página de inicio ─────────────────────────────────────────────────────────
Route::get('/', function () {
    return view('welcome');
});

// Las rutas de cada módulo viven en app/Modules/<Modulo>/routes.php
// y se cargan desde su ServiceProvider (registrado en bootstrap/providers.php).