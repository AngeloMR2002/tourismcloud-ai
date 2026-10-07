<?php

namespace App\Modules\Actividades\Http\Controllers\Operador;

use App\Http\Controllers\Controller;
use App\Models\Atractivo;
use App\Models\Destino;
use App\Modules\Actividades\Services\ImportacionLugaresService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class ImportacionController extends Controller
{
    public function index(): View
    {
        $destinos = Destino::activos()->whereIn('ciudad', config('tourism.osm_areas'))->get();
        $lugares = Atractivo::with('destino')->whereNotNull('origen_id')->latest('fecha_importacion')->paginate(20);

        return view('actividades::operador.importaciones.index', compact('destinos', 'lugares'));
    }

    public function store(Request $request, ImportacionLugaresService $service): RedirectResponse
    {
        $data = $request->validate(['destino_id' => ['required', Rule::exists('destinos', 'id')->where('estado', 'activo')]]);
        try {
            $counts = $service->importar(Destino::findOrFail($data['destino_id']));
        } catch (Throwable $error) {
            report($error);
            return back()->with('error', 'La fuente no respondió correctamente. El catálogo se conservó; puedes intentar más tarde.');
        }
        return back()->with('exito', "Importación: {$counts['nuevos']} nuevos, {$counts['actualizados']} pendientes actualizados y {$counts['conservados']} revisados conservados.");
    }

    public function aprobar(Request $request, int $id): RedirectResponse
    {
        $request->validate(['confirmado' => ['accepted']]);
        $lugar = Atractivo::findOrFail($id);
        abort_unless($lugar->fuente_url && $lugar->origen_id, 422);
        $lugar->update(['estado_verificacion' => 'aprobado', 'fecha_verificacion' => now()]);

        return back()->with('exito', 'Lugar revisado. Ahora puedes registrar una actividad con su fuente.');
    }
}
