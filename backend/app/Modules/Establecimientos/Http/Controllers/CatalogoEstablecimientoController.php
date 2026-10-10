<?php

namespace App\Modules\Establecimientos\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Establecimientos\Http\Requests\FiltroEstablecimientoRequest;
use App\Modules\Atractivos\Models\CategoriaInteres;
use App\Modules\Destinos\Models\Destino;
use App\Modules\Establecimientos\Models\Establecimiento;
use App\Modules\Establecimientos\Services\EstablecimientoService;
use Illuminate\View\View;

/**
 * Controlador público del catálogo de establecimientos.
 * Solo lectura — sin middleware de autenticación.
 * Accesible por turistas y cualquier visitante.
 */
class CatalogoEstablecimientoController extends Controller
{
    public function __construct(
        protected EstablecimientoService $establecimientoService
    ) {}

    /**
     * Catálogo público de establecimientos con filtros y paginación.
     * GET /catalogo/establecimientos
     */
    public function index(FiltroEstablecimientoRequest $request): View
    {
        $filtros = array_merge(
            $request->validated(),
            ['solo_activos' => true]
        );

        $porPagina        = (int) $request->input('por_pagina', 15);
        $establecimientos = $this->establecimientoService->listar($filtros, $porPagina);
        $destinos         = Destino::orderBy('nombre')->get();
        $categorias       = CategoriaInteres::orderBy('nombre')->get();

        return view('catalogo.establecimientos.index', compact(
            'establecimientos',
            'destinos',
            'categorias',
            'filtros'
        ));
    }

    /**
     * Detalle público de un establecimiento.
     * GET /catalogo/establecimientos/{establecimiento}
     */
    public function show(Establecimiento $establecimiento): View
    {
        abort_unless($establecimiento->estado === 'activo', 404);

        $establecimiento->load(['destino', 'categorias', 'imagenes']);

        return view('catalogo.establecimientos.show', compact('establecimiento'));
    }

    /**
     * Alias de compatibilidad hacia index().
     */
    public function establecimientos(FiltroEstablecimientoRequest $request): View
    {
        return $this->index($request);
    }

    /**
     * Alias de compatibilidad hacia show().
     */
    public function establecimientoDetalle(Establecimiento $establecimiento): View
    {
        return $this->show($establecimiento);
    }
}
