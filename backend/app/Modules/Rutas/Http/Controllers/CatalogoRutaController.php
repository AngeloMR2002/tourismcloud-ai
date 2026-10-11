<?php

namespace App\Modules\Rutas\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Destinos\Models\Destino;
use App\Modules\Rutas\Http\Requests\FiltroRutaPublicoRequest;
use App\Modules\Rutas\Models\Ruta;
use App\Modules\Rutas\Services\RutaService;
use Illuminate\View\View;

class CatalogoRutaController extends Controller
{
    public function __construct(protected RutaService $rutaService) {}

    public function index(FiltroRutaPublicoRequest $request): View
    {
        $filtros = $request->validated();
        $porPagina = (int) ($filtros['por_pagina'] ?? 12);
        $rutas = $this->rutaService->listarCatalogo($filtros, $porPagina);
        $destinos = Destino::query()
            ->where('estado', 'activo')
            ->orderBy('nombre')
            ->get();

        return view('rutas::catalogo.index', compact('rutas', 'destinos', 'filtros'));
    }

    public function show(Ruta $ruta): View
    {
        $rutaPublica = $this->rutaService->obtenerPublica($ruta);

        abort_unless($rutaPublica !== null, 404);

        return view('rutas::catalogo.show', ['ruta' => $rutaPublica]);
    }
}
