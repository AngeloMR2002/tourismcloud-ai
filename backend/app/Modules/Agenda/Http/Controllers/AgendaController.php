<?php

namespace App\Modules\Agenda\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Itinerarios\Models\Itinerario;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AgendaController extends Controller
{
    public function index(Request $request): View
    {
        $consultaAgenda = Itinerario::query()
            ->where('turista_id', $request->user()->id)
            ->whereIn('estado', ['generado', 'guardado', 'activo']);

        $itinerarios = (clone $consultaAgenda)
            ->with(['destino', 'diasItinerario'])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('agenda::index', [
            'itinerarios' => $itinerarios,
            'totalProgramados' => (clone $consultaAgenda)->count(),
            'presupuestoReferencial' => (float) ((clone $consultaAgenda)->sum('presupuesto_total') ?? 0),
            'resumenEstados' => (clone $consultaAgenda)
                ->selectRaw('estado, COUNT(*) as cantidad')
                ->groupBy('estado')
                ->pluck('cantidad', 'estado'),
        ]);
    }
}
