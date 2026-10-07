<?php

namespace App\Modules\Rutas\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Modules\Rutas\Http\Requests\FiltroRutaRequest;
use App\Modules\Rutas\Http\Resources\CatalogoRutaResource;
use App\Modules\Rutas\Services\RutaService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CatalogoRutaController extends Controller
{
    public function __construct(protected RutaService $rutas) {}

    public function index(FiltroRutaRequest $request): AnonymousResourceCollection
    {
        return CatalogoRutaResource::collection($this->rutas->listarConFiltros($request->validated(), 10))
            ->additional(['advertencia' => 'Una propuesta de recorrido no es un tour contratado. Valores null son desconocidos; confirmar acceso y condiciones con los responsables.']);
    }
}
