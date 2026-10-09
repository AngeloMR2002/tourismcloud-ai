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
Route::view('/usuarios', 'principal.workspace', ['section' => 'usuarios'])->name('principal.usuarios');
Route::view('/organizaciones', 'principal.workspace', ['section' => 'organizaciones'])->name('principal.organizaciones');
Route::view('/permisos', 'principal.workspace', ['section' => 'permisos'])->name('principal.permisos');

