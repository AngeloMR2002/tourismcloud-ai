<?php

namespace App\Modules\Dashboard\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Destinos\Models\Destino;
use App\Modules\Itinerarios\Models\Itinerario;
use App\Modules\Usuarios\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $esAdministrador = $request->user()->isAdministrador();
        $consultaItinerarios = Itinerario::with('destino');

        // Un turista sólo puede ver el resumen de sus propios itinerarios.
        if (! $esAdministrador) {
            $consultaItinerarios->where('turista_id', $request->user()->id);
        }

        return view('dashboard::index', [
            'esAdministrador' => $esAdministrador,
            'totalItinerarios' => (clone $consultaItinerarios)->count(),
            'totalDestinos' => Destino::where('estado', 'activo')->count(),
            'totalUsuarios' => $esAdministrador ? Usuario::count() : null,
            'itinerariosRecientes' => $consultaItinerarios->latest()->limit(5)->get(),
            'destinosActivos' => Destino::where('estado', 'activo')->orderBy('nombre')->get(['id', 'nombre']),
        ]);
    }
}
