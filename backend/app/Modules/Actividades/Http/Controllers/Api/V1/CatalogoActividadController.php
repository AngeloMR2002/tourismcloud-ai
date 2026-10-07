<?php

namespace App\Modules\Actividades\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Modules\Actividades\Http\Requests\FiltroActividadRequest;
use App\Modules\Actividades\Http\Resources\CatalogoActividadResource;
use App\Modules\Actividades\Services\ActividadService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CatalogoActividadController extends Controller
{
    public function __construct(protected ActividadService $actividades) {}

    public function index(FiltroActividadRequest $request): AnonymousResourceCollection
    {
        return CatalogoActividadResource::collection($this->actividades->listarConFiltros($request->validated(), 20))
            ->additional(['advertencia' => 'Solo datos aprobados. Los valores null son desconocidos, no cero. Precios publicados no son cotizaciones en tiempo real; confirmar con el responsable.']);
    }

}
