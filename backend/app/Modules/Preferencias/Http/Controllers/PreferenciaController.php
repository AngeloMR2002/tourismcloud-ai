<?php

namespace App\Modules\Preferencias\Http\Controllers;

use App\Modules\Preferencias\Models\CategoriaInteres;
use App\Modules\Preferencias\Services\PreferenciaService;
use App\Modules\Usuarios\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PreferenciaController
{
    protected PreferenciaService $preferenciaService;

    public function __construct(PreferenciaService $preferenciaService)
    {
        $this->preferenciaService = $preferenciaService;
    }

    public function index()
    {
        $preferencias = $this->preferenciaService->listar();

        return view('preferencias::preferencias.index', compact('preferencias'));
    }

    public function turista(int $turistaId)
    {
        $this->verificarTitular($turistaId);

        $preferencia = $this->preferenciaService->obtenerDelTurista($turistaId);

        return view('preferencias::preferencias.turista.index', compact('preferencia'));
    }

    public function create()
    {
        /** @var Usuario|null $user */
        $user = Auth::user();
        if ($user && $user->isTurista()) {
            $turistas = collect([$user]);
        } else {
            $turistas = Usuario::where('rol', 'turista')->where('estado', 'activo')->get();
        }
        $categorias = CategoriaInteres::all();

        return view('preferencias::preferencias.create', compact('turistas', 'categorias'));
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'turista_id' => ['required', 'integer', 'exists:usuarios,id'],
            'presupuesto_max' => ['required', 'numeric', 'min:0'],
            'dias_disponibles' => ['required', 'integer', 'min:1'],
            ...$this->reglasHorario($request),
            'categorias' => ['nullable', 'array'],
            'categorias.*' => ['integer', 'exists:categorias_interes,id'],
        ], [
            'hora_fin_preferida.after' => __('travel_preferences.end_time_after_start'),
        ]);

        /** @var Usuario|null $user */
        $user = Auth::user();
        if ($user && $user->isTurista()) {
            // El campo del formulario viaja oculto en el cliente; no se confía en su valor.
            $datos['turista_id'] = $user->id;
        }

        $this->preferenciaService->crear($datos);

        if ($user && $user->isTurista()) {
            return redirect()->route('preferencias.turista', $user->id)->with('exito', __('travel_preferences.tourist_saved'));
        }

        return redirect()->route('preferencias.index')->with('exito', __('travel_preferences.registered'));
    }

    public function show(int $id)
    {
        $preferencia = $this->preferenciaService->obtener($id);
        if (! $preferencia) {
            abort(404);
        }

        return view('preferencias::preferencias.show', compact('preferencia'));
    }

    public function edit(int $id)
    {
        $preferencia = $this->preferenciaService->obtener($id);
        if (! $preferencia) {
            abort(404);
        }
        $categorias = CategoriaInteres::all();
        $turistas = Usuario::where('rol', 'turista')->where('estado', 'activo')->get();

        return view('preferencias::preferencias.edit', [
            'preferencia' => $preferencia,
            'categorias' => $categorias,
            'turistas' => $turistas,
            'esTurista' => false,
        ]);
    }

    public function editarTurista(int $turistaId, int $preferenciaId)
    {
        $this->verificarTitular($turistaId);

        $preferencia = $this->preferenciaService->obtenerDelTuristaPorId($turistaId, $preferenciaId);
        if (! $preferencia) {
            abort(404);
        }
        $categorias = CategoriaInteres::all();

        return view('preferencias::preferencias.edit', [
            'preferencia' => $preferencia,
            'categorias' => $categorias,
            'esTurista' => true,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $datos = $request->validate([
            'turista_id' => ['required', 'integer', 'exists:usuarios,id'],
            'presupuesto_max' => ['required', 'numeric', 'min:0'],
            'dias_disponibles' => ['required', 'integer', 'min:1'],
            ...$this->reglasHorario($request),
            'categorias' => ['nullable', 'array'],
            'categorias.*' => ['integer', 'exists:categorias_interes,id'],
        ], [
            'hora_fin_preferida.after' => __('travel_preferences.end_time_after_start'),
        ]);

        $this->preferenciaService->actualizar($id, $datos);

        /** @var Usuario|null $user */
        $user = Auth::user();
        if ($user && $user->isTurista()) {
            return redirect()->route('preferencias.turista', $user->id)->with('exito', __('travel_preferences.tourist_updated'));
        }

        return redirect()->route('preferencias.index')->with('exito', __('travel_preferences.updated'));
    }

    public function actualizarTurista(Request $request, int $turistaId, int $preferenciaId)
    {
        $this->verificarTitular($turistaId);

        $preferencia = $this->preferenciaService->obtenerDelTuristaPorId($turistaId, $preferenciaId);
        if (! $preferencia) {
            abort(404);
        }

        $datos = $request->validate([
            'presupuesto_max' => ['required', 'numeric', 'min:0'],
            'dias_disponibles' => ['required', 'integer', 'min:1'],
            ...$this->reglasHorario($request),
            'categorias' => ['nullable', 'array'],
            'categorias.*' => ['integer', 'exists:categorias_interes,id'],
        ], [
            'hora_fin_preferida.after' => __('travel_preferences.end_time_after_start'),
        ]);

        $datos['turista_id'] = $turistaId;
        $this->preferenciaService->actualizar($preferenciaId, $datos);

        return redirect()->route('preferencias.turista', $turistaId)->with('exito', __('travel_preferences.tourist_updated'));
    }

    public function destroy(int $id)
    {
        if (! $this->preferenciaService->eliminar($id)) {
            abort(404);
        }

        return redirect()->route('preferencias.index')->with('exito', __('travel_preferences.deleted'));
    }

    public function recientes()
    {
        $resumen = $this->preferenciaService->resumenRecientes(10);

        return view('preferencias::preferencias.proveedor.recientes', $resumen);
    }

    private function verificarTitular(int $turistaId): void
    {
        abort_unless($turistaId === (int) Auth::id(), 403, __('auth.access_forbidden'));
    }

    private function reglasHorario(Request $request): array
    {
        $reglasHoraFin = ['nullable', 'date_format:H:i'];
        if ($request->filled('hora_inicio_preferida')) {
            $reglasHoraFin[] = 'after:hora_inicio_preferida';
        }

        return [
            'hora_inicio_preferida' => ['nullable', 'date_format:H:i'],
            'hora_fin_preferida' => $reglasHoraFin,
        ];
    }
}
