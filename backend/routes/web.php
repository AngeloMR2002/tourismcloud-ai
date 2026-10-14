<?php

use App\Modules\Actividades\Models\Actividad;
use App\Modules\Destinos\Models\Destino;
use Illuminate\Support\Facades\Route;

// ─── Página de inicio ─────────────────────────────────────────────────────────
Route::get('/', function () {
    $destinosDestacados = Destino::query()
        ->where('estado', 'activo')
        ->orderBy('nombre')
        ->limit(6)
        ->get(['id', 'nombre', 'descripcion', 'pais', 'region', 'ciudad']);

    $actividadesDestacadas = Actividad::query()
        ->activas()
        ->with('destino:id,nombre,estado')
        ->whereHas('destino', fn ($consulta) => $consulta->where('estado', 'activo'))
        ->latest('updated_at')
        ->limit(6)
        ->get();

    return view('welcome', compact('destinosDestacados', 'actividadesDestacadas'));
})->name('inicio');

// ─── Rutas Globales / Vistas Base ─────────────────────────────────────────────
Route::redirect('/usuarios', '/admin/usuarios')->name('principal.usuarios');
Route::redirect('/organizaciones', '/admin/usuarios')->name('principal.organizaciones');
Route::redirect('/permisos', '/admin/usuarios')->name('principal.permisos');
