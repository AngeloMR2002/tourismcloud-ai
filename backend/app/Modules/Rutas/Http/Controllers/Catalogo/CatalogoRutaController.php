<?php

namespace App\Modules\Rutas\Http\Controllers\Catalogo;

use App\Http\Controllers\Controller;
use App\Modules\Rutas\Http\Requests\FiltroRutaRequest;
use App\Models\Destino;
use App\Modules\Rutas\Services\RutaService;
use Illuminate\View\View;

class CatalogoRutaController extends Controller
{
    public function __construct(
        protected RutaService $rutaService
    ) {}

    /**
     * Catálogo público de rutas y circuitos turísticos.
     */
    public function index(FiltroRutaRequest $request): View
    {
        $filtros = $request->validated();
        $rutas = $this->rutaService->listarConFiltros($filtros, 9);
        $destinos = Destino::activos()->orderBy('nombre')->get();

        return view('rutas::catalogo.rutas.index', compact('rutas', 'destinos', 'filtros'));
    }

    /**
     * Vista de detalle de una ruta con su timeline secuencial de paradas.
     */
    public function show(int $id): View
    {
        $ruta = $this->rutaService->obtenerPorId($id, true);

        return view('rutas::catalogo.rutas.show', compact('ruta'));
    }
}
