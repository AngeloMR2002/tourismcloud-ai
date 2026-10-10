<?php

use Illuminate\Support\Facades\Route;

// ─── Página de inicio ─────────────────────────────────────────────────────────
Route::get('/', function () {
    return view('welcome');
})->name('inicio');

// ─── Rutas Globales / Vistas Base ─────────────────────────────────────────────
Route::redirect('/usuarios', '/admin/usuarios')->name('principal.usuarios');
Route::redirect('/organizaciones', '/admin/usuarios')->name('principal.organizaciones');
Route::redirect('/permisos', '/admin/usuarios')->name('principal.permisos');