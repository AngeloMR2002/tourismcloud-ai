<?php

use Illuminate\Support\Facades\Route;

// ─── Página de inicio ─────────────────────────────────────────────────────────
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('catalogo.atractivos.index');
    }
    return redirect()->route('login');
});

// ─── Rutas Globales / Vistas Base ─────────────────────────────────────────────
Route::get('/usuarios', function () {
    return redirect()->route('admin.usuarios.index');
})->name('principal.usuarios');

Route::get('/organizaciones', function () {
    return redirect()->route('admin.usuarios.index');
})->name('principal.organizaciones');

Route::get('/permisos', function () {
    return redirect()->route('admin.usuarios.index');
})->name('principal.permisos');

