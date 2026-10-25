<?php

namespace App\Modules\Itinerarios\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Actividades\Models\Actividad;
use App\Modules\Atractivos\Models\Atractivo;
use App\Modules\Destinos\Models\Destino;
use App\Modules\Establecimientos\Models\Establecimiento;
use App\Modules\Itinerarios\Models\Itinerario;
use App\Modules\Itinerarios\Services\ItinerarioComposerService;
use App\Modules\Preferencias\Models\PreferenciaTurista;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ItinerarioController extends Controller
{
    public function __construct(private ItinerarioComposerService $composer) {}

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
        $itinerario->load(['destino', 'diasItinerario.items']);

        $items = $itinerario->diasItinerario->flatMap->items;
        $idsDe = fn (string $tipo) => $items->where('entidad_tipo', $tipo)->pluck('entidad_id');

        $entidades = [
            'atractivo' => Atractivo::whereIn('id', $idsDe('atractivo'))->get()->keyBy('id'),
            'actividad' => Actividad::whereIn('id', $idsDe('actividad'))->get()->keyBy('id'),
            'establecimiento' => Establecimiento::whereIn('id', $idsDe('establecimiento'))->get()->keyBy('id'),
        ];

        $catalogo = [
            'atractivo' => Atractivo::activos()->deDestino($itinerario->destino_id)->orderBy('nombre')->get(['id', 'nombre', 'costo_entrada']),
            'actividad' => Actividad::activas()->deDestino($itinerario->destino_id)->orderBy('nombre')->get(['id', 'nombre', 'costo']),
            'establecimiento' => Establecimiento::activos()->deDestino($itinerario->destino_id)->orderBy('nombre')->get(['id', 'nombre']),
        ];

        $costoTotal = (float) $items->sum('costo_estimado');

        return view('itinerarios::show', compact('itinerario', 'entidades', 'catalogo', 'costoTotal'));
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

        $preferencia = PreferenciaTurista::where('turista_id', $request->user()->id)->first();
        // Si el turista no indica presupuesto, se usa el de su preferencia registrada en vez de forzar S/ 0,
        // que dejaría el itinerario sin presupuesto real y vaciaría la composición de visitas.
        $presupuestoTotal = $data['presupuesto_total'] ?? $preferencia?->presupuesto_max;

        DB::transaction(function () use ($request, $data, $fechaInicio, $preferencia, $presupuestoTotal): void {
            $itinerario = Itinerario::create($data + [
                'turista_id' => $request->user()->id,
                'preferencia_id' => $preferencia?->id,
                'presupuesto_total' => $presupuestoTotal,
                'estado' => 'generado',
            ]);
            for ($dia = 1; $dia <= $data['dias']; $dia++) {
                $itinerario->diasItinerario()->create([
                    'numero_dia' => $dia,
                    'fecha' => $fechaInicio ? Carbon::parse($fechaInicio)->addDays($dia - 1)->toDateString() : null,
                ]);
            }

            // La composición automática ahora es opcional: el turista puede crear el itinerario vacío y elegir sus visitas.
            if ($request->boolean('componer_automaticamente', true)) {
                $itinerario->load('diasItinerario');
                $this->composer->componer($itinerario, $preferencia);
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