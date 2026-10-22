<?php

namespace App\Modules\Itinerarios\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Destinos\Models\Destino;
use App\Modules\Itinerarios\Models\Itinerario;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ItinerarioController extends Controller
{
    public function index(Request $request): View
    {
        $itinerarios = Itinerario::with('destino')
            ->where('turista_id', $request->user()->id)
            ->when($request->filled('buscar'), fn ($query) => $query->where(function ($q) use ($request) {
                $term = '%'.mb_strtolower($request->string('buscar')->trim()->toString()).'%';
                $q->whereRaw('LOWER(nombre) LIKE ?', [$term])
                    ->orWhereRaw("LOWER(COALESCE(descripcion, '')) LIKE ?", [$term]);
            }))
            ->when($request->filled('destino_id'), fn ($query) => $query->where('destino_id', $request->integer('destino_id')))
            ->latest()->paginate(9)->withQueryString();

        return view('itinerarios::index', [
            'itinerarios' => $itinerarios,
            'destinos' => Destino::where('estado', 'activo')->orderBy('nombre')->get(),
        ]);
    }

    public function create(Request $request): View
    {
        $destinoInicial = $request->integer('destino_id');
        $destinoInicial = Destino::whereKey($destinoInicial)->where('estado', 'activo')->exists() ? $destinoInicial : null;
        $diasIniciales = min(max($request->integer('dias', 2), 1), 60);

        return view('itinerarios::create', [
            'destinos' => Destino::where('estado', 'activo')->orderBy('nombre')->get(),
            'destinoInicial' => $destinoInicial,
            'diasIniciales' => $diasIniciales,
        ]);
    }

    public function show(Request $request, Itinerario $itinerario): View
    {
        abort_unless($itinerario->turista_id === $request->user()->id, 403);
        $itinerario->load(['destino', 'diasItinerario']);

        return view('itinerarios::show', compact('itinerario'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'destino_id' => ['required', 'integer', 'exists:destinos,id'],
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string', 'max:5000'],
            'presupuesto_total' => ['nullable', 'numeric', 'min:0', 'max:99999999'],
            'dias' => ['required', 'integer', 'min:1', 'max:60'],
            'fecha_inicio' => ['nullable', 'date'],
        ]);
        abort_unless(Destino::whereKey($data['destino_id'])->where('estado', 'activo')->exists(), 422, __('modules.destination_not_available'));
        $fechaInicio = $data['fecha_inicio'] ?? null;
        unset($data['fecha_inicio']);

        DB::transaction(function () use ($request, $data, $fechaInicio): void {
            $itinerario = Itinerario::create($data + [
                'turista_id' => $request->user()->id,
                'presupuesto_total' => $data['presupuesto_total'] ?? 0,
                'estado' => 'generado',
            ]);
            for ($dia = 1; $dia <= $data['dias']; $dia++) {
                $itinerario->diasItinerario()->create([
                    'numero_dia' => $dia,
                    'fecha' => $fechaInicio ? Carbon::parse($fechaInicio)->addDays($dia - 1)->toDateString() : null,
                ]);
            }
        });

        return redirect()->route('itinerarios.index')->with('exito', __('modules.itinerary_created'));
    }

    public function destroy(Request $request, Itinerario $itinerario): RedirectResponse
    {
        abort_unless($itinerario->turista_id === $request->user()->id, 403);
        $itinerario->delete();

        return redirect()->route('itinerarios.index')->with('exito', __('modules.itinerary_deleted'));
    }
}
