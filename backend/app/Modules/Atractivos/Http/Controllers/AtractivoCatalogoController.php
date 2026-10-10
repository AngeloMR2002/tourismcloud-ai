<?php

namespace App\Modules\Atractivos\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CategoriaInteres;
use App\Models\Destino;
use App\Modules\Atractivos\Http\Requests\FiltroAtractivoRequest;
use App\Modules\Atractivos\Models\Atractivo;
use App\Modules\Atractivos\Services\AtractivoService;
use Illuminate\View\View;

/**
 * Catálogo público de atractivos.
 * Solo lectura — sin middleware de autenticación.
 */
class AtractivoCatalogoController extends Controller
{
    public function __construct(
        protected AtractivoService $service
    ) {}

    /** GET /catalogo/atractivos */
    public function index(FiltroAtractivoRequest $request): View
    {
        $filtros = array_merge(
            $request->validated(),
            ['solo_activos' => true]
        );

        $porPagina  = (int) $request->input('por_pagina', 15);
        $atractivos = $this->service->listar($filtros, $porPagina);
        $destinos   = Destino::orderBy('nombre')->get();
        $categorias = CategoriaInteres::orderBy('nombre')->get();

        return view('atractivos::catalogo.index', compact(
            'atractivos',
            'destinos',
            'categorias',
            'filtros'
        ));
    }

    /** GET /catalogo/atractivos/{atractivo} */
    public function show(Atractivo $atractivo): View
    {
        // Solo se muestran atractivos activos al público.
        abort_unless($atractivo->estado === 'activo', 404);

        $atractivo->load([
            'destino',
            'categorias',
            'imagenes',
            'horarios',
        ]);

        return view('atractivos::catalogo.show', compact('atractivo'));
    }
}