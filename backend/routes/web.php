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
Route::redirect('/usuarios', '/admin/usuarios')->name('principal.usuarios');
Route::redirect('/organizaciones', '/admin/usuarios')->name('principal.organizaciones');
Route::redirect('/permisos', '/admin/usuarios')->name('principal.permisos');