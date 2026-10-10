<?php

namespace App\Modules\Dashboard\Controllers;

use Illuminate\Routing\Controller;
use App\Models\Itinerario;
use App\Models\Destino;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    //Muestra las métricas generales y resúmenes del Dashboard

    public function index()
    {
        try {
            // métricas generales para las tarjetas del Dashboard

            $totalItinerarios = Itinerario::count();
            $totalDestinos    = Destino::where('estado', 'activo')->count();
            $totalUsuarios   = User::count();

            // ultimos 5 itinerarios creados con su destino relacionado
            $itinerariosRecientes = Itinerario::with('destino')
                ->latest()
                ->take(5)
                ->get();

            $path = base_path('app/Modules/Dashboard/Views/index.blade.php');

            return view()->file($path, compact(
                'totalItinerarios',
                'totalDestinos',
                'totalUsuarios',
                'itinerariosRecientes'
            ));
        } catch (\Exception $e) {
            Log::error('Error en DashboardController@index: ' . $e->getMessage());

            $path = base_path('app/Modules/Dashboard/Views/index.blade.php');
            return view()->file($path, [
                'totalItinerarios' => 0,
                'totalDestinos' => 0,
                'totalUsuarios' => 0,
                'itinerariosRecientes' => collect([]),
                'error' => 'No se pudieron cargar las métricas en tiempo real.'
            ]);
        }
    }
}