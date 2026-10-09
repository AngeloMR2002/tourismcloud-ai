<?php

use Illuminate\Support\Facades\Route;

// ─── Página de inicio ─────────────────────────────────────────────────────────
Route::get('/', function () {
    return view('welcome');
});

// ─── Rutas Globales / Vistas Base ─────────────────────────────────────────────
Route::view('/login', 'principal.login')->name('login');
Route::view('/usuarios', 'principal.workspace', ['section' => 'usuarios'])->name('principal.usuarios');
Route::view('/destinos', 'principal.workspace', ['section' => 'destinos'])->name('principal.destinos');
Route::view('/organizaciones', 'principal.workspace', ['section' => 'organizaciones'])->name('principal.organizaciones');
Route::view('/permisos', 'principal.workspace', ['section' => 'permisos'])->name('principal.permisos');

