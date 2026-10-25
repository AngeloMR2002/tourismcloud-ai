<?php

namespace App\Modules\Itinerarios\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Itinerarios\Http\Requests\ItinerarioItemRequest;
use App\Modules\Itinerarios\Models\Itinerario;
use App\Modules\Itinerarios\Models\ItinerarioItem;
use App\Modules\Itinerarios\Services\ItinerarioItemService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ItinerarioItemController extends Controller
{
    public function __construct(private ItinerarioItemService $servicio) {}

    public function store(ItinerarioItemRequest $request, Itinerario $itinerario): RedirectResponse
    {
        $dia = $itinerario->diasItinerario()->findOrFail($request->integer('itinerario_dia_id'));
        $this->servicio->agregar($dia, $request->datosItem());

        return redirect()->route('itinerarios.show', $itinerario)->with('exito', __('modules.visit_added'));
    }

    public function update(ItinerarioItemRequest $request, Itinerario $itinerario, ItinerarioItem $item): RedirectResponse
    {
        $dia = $itinerario->diasItinerario()->findOrFail($request->integer('itinerario_dia_id'));
        $this->servicio->actualizar($item, $dia, $request->datosItem());

        return redirect()->route('itinerarios.show', $itinerario)->with('exito', __('modules.visit_updated'));
    }

    public function destroy(Request $request, Itinerario $itinerario, ItinerarioItem $item): RedirectResponse
    {
        abort_unless($itinerario->turista_id === $request->user()->id && $item->dia->itinerario_id === $itinerario->id, 403);
        $this->servicio->quitar($item);

        return redirect()->route('itinerarios.show', $itinerario)->with('exito', __('modules.visit_removed'));
    }
}