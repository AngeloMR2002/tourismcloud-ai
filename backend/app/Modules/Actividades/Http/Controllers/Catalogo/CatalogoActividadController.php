<?php

namespace App\Modules\Actividades\Http\Controllers\Catalogo;

use App\Http\Controllers\Controller;
use App\Modules\Actividades\Http\Requests\FiltroActividadRequest;
use App\Models\Destino;
use App\Modules\Actividades\Services\ActividadService;
use Illuminate\View\View;

class CatalogoActividadController extends Controller
{
    public function __construct(
        protected ActividadService $actividadService
    ) {}

    /**
     * Catálogo público de actividades para turistas.
     */
    public function index(FiltroActividadRequest $request): View
    {
        $filtros = $request->validated();
        $actividades = $this->actividadService->listarConFiltros($filtros, 9);
        $destinos = Destino::activos()->orderBy('nombre')->get();

        return view('actividades::catalogo.actividades.index', compact('actividades', 'destinos', 'filtros'));
    }

    /**
     * Vista de detalle de una experiencia o actividad turística.
     */
    public function show(int $id): View
    {
        $actividad = $this->actividadService->obtenerPorId($id, true);

        return view('actividades::catalogo.actividades.show', compact('actividad'));
    }
}
