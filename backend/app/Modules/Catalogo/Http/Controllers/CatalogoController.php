<?php

namespace App\Modules\Catalogo\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Atractivos\Http\Requests\FiltroAtractivoRequest;
use App\Modules\Establecimientos\Http\Requests\FiltroEstablecimientoRequest;
use App\Modules\Atractivos\Models\Atractivo;
use App\Modules\Atractivos\Models\CategoriaInteres;
use App\Modules\Destinos\Models\Destino;
use App\Modules\Establecimientos\Models\Establecimiento;
use App\Modules\Atractivos\Services\AtractivoService;
use App\Modules\Establecimientos\Services\EstablecimientoService;
use Illuminate\View\View;

/**
 * Controlador público del catálogo turístico.
 * Solo lectura — sin middleware de autenticación.
 * Accesible por turistas y cualquier visitante.
 */
class CatalogoController extends Controller
{
    public function __construct(
        protected AtractivoService        $atractivoService,
        protected EstablecimientoService $establecimientoService
    ) {}

    // ─── Atractivos ──────────────────────────────────────────────────────────

    /**
     * Catálogo público de atractivos con filtros y paginación.
     * GET /catalogo/atractivos
     */
    public function atractivos(FiltroAtractivoRequest $request): View
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
    public function atractivoDetalle(Atractivo $atractivo): View
    {
        // Solo se muestran atractivos activos al público.
        abort_unless($atractivo->estado === 'activo', 404);

        $atractivo->load(['destino', 'categorias', 'imagenes']);

        return view('catalogo.atractivos.show', compact('atractivo'));
    }

    // ─── Establecimientos ────────────────────────────────────────────────────

    /**
     * Catálogo público de establecimientos con filtros y paginación.
     * GET /catalogo/establecimientos
     */
    public function establecimientos(FiltroEstablecimientoRequest $request): View
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
    public function establecimientoDetalle(Establecimiento $establecimiento): View
    {
        abort_unless($establecimiento->estado === 'activo', 404);

        $establecimiento->load(['destino', 'categorias', 'imagenes']);

        return view('catalogo.establecimientos.show', compact('establecimiento'));
    }
}
