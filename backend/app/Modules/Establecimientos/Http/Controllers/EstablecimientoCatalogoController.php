<?php

namespace App\Modules\Establecimientos\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CategoriaInteres;
use App\Models\Destino;
use App\Modules\Establecimientos\Http\Requests\FiltroEstablecimientoRequest;
use App\Modules\Establecimientos\Models\Establecimiento;
use App\Modules\Establecimientos\Services\EstablecimientoService;
use Illuminate\View\View;

/**
 * Catálogo público de establecimientos.
 * Solo lectura — sin middleware de autenticación.
 */
class EstablecimientoCatalogoController extends Controller
{
    public function __construct(
        protected EstablecimientoService $service
    ) {}

    /** GET /catalogo/establecimientos */
    public function index(FiltroEstablecimientoRequest $request): View
    {
        $filtros = array_merge(
            $request->validated(),
            ['solo_activos' => true]
        );

        $porPagina        = (int) $request->input('por_pagina', 15);
        $establecimientos = $this->service->listar($filtros, $porPagina);
        $destinos         = Destino::orderBy('nombre')->get();
        $categorias       = CategoriaInteres::orderBy('nombre')->get();

        return view('establecimientos::catalogo.index', compact(
            'establecimientos',
            'destinos',
            'categorias',
            'filtros'
        ));
    }

    /** GET /catalogo/establecimientos/{establecimiento} */
    public function show(Establecimiento $establecimiento): View
    {
        abort_unless($establecimiento->estado === 'activo', 404);

        $establecimiento->load(['destino', 'categorias', 'imagenes', 'horarios']);

        return view('establecimientos::catalogo.show', compact('establecimiento'));
    }
}