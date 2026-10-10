<?php

namespace App\Modules\Itinerarios\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\Itinerario;
use App\Models\Destino;
use Illuminate\Support\Facades\Log;

class ItinerariosController extends Controller
{
    // lista y filtrar
    public function index(Request $request)
    {
        $query = Itinerario::with('destino');

        // filtro por texto la barra de búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nombre', 'ILIKE', "%{$search}%")
                  ->orWhere('descripcion', 'ILIKE', "%{$search}%");
        }

        // filtro por destino o estado
        if ($request->filled('destino_id')) {
            $query->where('destino_id', $request->destino_id);
        }

        $itinerarios = $query->latest()->get();
        $destinos = Destino::where('estado', 'activo')->get();

        $path = base_path('app/Modules/Itinerarios/Views/index.blade.php');
        return view()->file($path, compact('itinerarios', 'destinos'));
    }

    // ver detalles de un itinerario
    public function show($id)
    {
        try {
            // busca el itinerario en la bd
            $itinerario = Itinerario::with(['destino', 'diasItinerario'])->findOrFail($id);

            $path = base_path('app/Modules/Itinerarios/Views/detalles.blade.php');
            return view()->file($path, compact('itinerario'));
        } catch (\Exception $e) {
            return redirect()->route('itinerarios.index')->with('error', 'El itinerario seleccionado no existe.');
        }
    }

    // guarda nuevo itinerario
    public function store(Request $request)
    {
        $validated = $request->validate([
            'destino_id'        => 'required|exists:destinos,id',
            'nombre'            => 'required|string|max:150',
            'descripcion'       => 'nullable|string',
            'presupuesto_total' => 'nullable|numeric|min:0',
            'dias'              => 'required|integer|min:1',
        ]);

        Itinerario::create([
            'turista_id'        => 1, // Usuario por defecto o autenticado
            'destino_id'        => $validated['destino_id'],
            'nombre'            => $validated['nombre'],
            'descripcion'       => $validated['descripcion'] ?? null,
            'presupuesto_total' => $validated['presupuesto_total'] ?? 0,
            'dias'              => $validated['dias'],
            'estado'            => 'generado',
        ]);

        return redirect()->back()->with('success', 'Paquete/Itinerario creado exitosamente.');
    }
}