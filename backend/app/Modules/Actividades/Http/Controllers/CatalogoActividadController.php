<?php

namespace App\Modules\Actividades\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Actividades\Http\Requests\FiltroCatalogoActividadRequest;
use App\Modules\Actividades\Models\Actividad;
use App\Modules\Actividades\Services\ActividadService;
use App\Modules\Destinos\Models\Destino;
use Illuminate\View\View;

class CatalogoActividadController extends Controller
{
    public function __construct(protected ActividadService $actividadService) {}

    public function index(FiltroCatalogoActividadRequest $request): View
    {
        $filtros = $request->validated();
        $actividades = $this->actividadService->listarCatalogo($filtros);
        $destinos = Destino::query()
            ->where('estado', 'activo')
            ->orderBy('nombre')
            ->get();

        return view('actividades::catalogo.index', compact('actividades', 'destinos', 'filtros'));
    }

    public function show(Actividad $actividad): View
    {
        $esVisible = $actividad->estado === 'activo'
            && $actividad->destino()->where('estado', 'activo')->exists();

        abort_unless($esVisible, 404);

        $actividad->load('destino');

        return view('actividades::catalogo.show', compact('actividad'));
    }
}
