<?php

namespace App\Modules\Atractivos\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Atractivos\Http\Requests\FiltroAtractivoRequest;
use App\Modules\Atractivos\Models\Atractivo;
use App\Modules\Atractivos\Models\CategoriaInteres;
use App\Modules\Destinos\Models\Destino;
use App\Modules\Atractivos\Services\AtractivoService;
use Illuminate\View\View;

/**
 * Controlador público del catálogo de atractivos turísticos.
 * Solo lectura — sin middleware de autenticación.
 * Accesible por turistas y cualquier visitante.
 */
class CatalogoAtractivoController extends Controller
{
    public function __construct(
        protected AtractivoService $atractivoService
    ) {}

    /**
     * Catálogo público de atractivos con filtros y paginación.
     * GET /catalogo/atractivos
     */
    public function index(FiltroAtractivoRequest $request): View
    {
        $filtros = array_merge(
            $request->validated(),
            ['solo_activos' => true]
        );

        $porPagina   = (int) $request->input('por_pagina', 15);
        $atractivos  = $this->atractivoService->listar($filtros, $porPagina);
        $destinos    = Destino::orderBy('nombre')->get();
        $categorias  = CategoriaInteres::orderBy('nombre')->get();

        return view('catalogo.atractivos.index', compact(
            'atractivos',
            'destinos',
            'categorias',
            'filtros'
        ));
    }

    /**
     * Detalle público de un atractivo.
     * GET /catalogo/atractivos/{atractivo}
     */
    public function show(Atractivo $atractivo): View
    {
        // Solo se muestran atractivos activos al público.
        abort_unless($atractivo->estado === 'activo', 404);

        $atractivo->load(['destino', 'categorias', 'imagenes']);

        return view('catalogo.atractivos.show', compact('atractivo'));
    }

    /**
     * Alias de compatibilidad hacia index().
     */
    public function atractivos(FiltroAtractivoRequest $request): View
    {
        return $this->index($request);
    }

    /**
     * Alias de compatibilidad hacia show().
     */
    public function atractivoDetalle(Atractivo $atractivo): View
    {
        return $this->show($atractivo);
    }
}
