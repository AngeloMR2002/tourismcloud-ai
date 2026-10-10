<?php

namespace App\Modules\Agenda\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\Itinerario;
use Illuminate\Support\Facades\Log;

class AgendaController extends Controller
{
    // muestra la agenda con las salidas y rutas programadas.
     
    public function index(Request $request)
    {
        try {

            // se trae itinerarios activos o guardados para la programación de salidas

            $itinerariosAgenda = Itinerario::with(['destino'])
                ->whereIn('estado', ['generado', 'guardado', 'activo'])
                ->orderBy('created_at', 'asc')
                ->get();

            // estadísticas para el widget 

            $totalProgramados = $itinerariosAgenda->count();
            $montoTotalEstimado = $itinerariosAgenda->sum('presupuesto_total');

            $path = base_path('app/Modules/Agenda/Views/index.blade.php');

            return view()->file($path, compact(
                'itinerariosAgenda',
                'totalProgramados',
                'montoTotalEstimado'
            ));
        } catch (\Exception $e) {
            Log::error('Error en AgendaController@index: ' . $e->getMessage());

            $path = base_path('app/Modules/Agenda/Views/index.blade.php');
            return view()->file($path, [
                'itinerariosAgenda' => collect([]),
                'totalProgramados' => 0,
                'montoTotalEstimado' => 0,
                'error' => 'No se pudo cargar la agenda de salidas.'
            ]);
        }
    }
}