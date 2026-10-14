<?php
namespace App\Modules\Preferencias\Http\Controllers;

use App\Modules\Preferencias\Models\CategoriaInteres;
use App\Modules\Usuarios\Models\Usuario;
use App\Modules\Preferencias\Services\PreferenciaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PreferenciaController {
    protected PreferenciaService $preferenciaService;

    public function __construct(PreferenciaService $preferenciaService) {
        $this->preferenciaService = $preferenciaService;
    }

    public function index() {
        $preferencias = $this->preferenciaService->listar();
        return view('preferencias::preferencias.index', compact('preferencias'));
    }

    public function turista(int $turistaId) {
        $preferencia = $this->preferenciaService->obtenerDelTurista($turistaId);
        return view('preferencias::preferencias.turista.index', compact('preferencia'));
    }

    public function create() {
        /** @var \App\Modules\Usuarios\Models\Usuario|null $user */
        $user = Auth::user();
        if ($user && $user->isTurista()) {
            $turistas = collect([$user]);
        } else {
            $turistas = Usuario::where('rol', 'turista')->where('estado', 'activo')->get();
        }
        $categorias = CategoriaInteres::all();
        return view('preferencias::preferencias.create', compact('turistas', 'categorias'));
    }

    public function store(Request $request) {
        $datos = $request->validate([
            'turista_id' => ['required', 'integer', 'exists:usuarios,id'],
            'presupuesto_max' => ['required', 'numeric', 'min:0'],
            'dias_disponibles' => ['required', 'integer', 'min:1'],
            'hora_inicio_preferida' => ['nullable', 'date_format:H:i'],
            'hora_fin_preferida' => ['nullable', 'date_format:H:i'],
            'categorias' => ['nullable', 'array'],
            'categorias.*' => ['integer', 'exists:categorias_interes,id'],
        ]);

        $this->preferenciaService->crear($datos);

        /** @var \App\Modules\Usuarios\Models\Usuario|null $user */
        $user = Auth::user();
        if ($user && $user->isTurista()) {
            return redirect()->route('preferencias.turista', $user->id)->with('exito', 'Tus preferencias han sido guardadas correctamente.');
        }

        return redirect()->route('preferencias.index')->with('exito', 'Preferencia registrada correctamente.');
    }

    public function show(int $id) {
        $preferencia = $this->preferenciaService->obtener($id);
        if (!$preferencia) abort(404);
        return view('preferencias::preferencias.show', compact('preferencia'));
    }

    public function edit(int $id) {
        $preferencia = $this->preferenciaService->obtener($id);
        if (!$preferencia) abort(404);
        $categorias = CategoriaInteres::all();
        $turistas = Usuario::where('rol', 'turista')->where('estado', 'activo')->get();
        return view('preferencias::preferencias.edit', [
            'preferencia' => $preferencia,
            'categorias' => $categorias,
            'turistas' => $turistas,
            'esTurista' => false
        ]);
    }

    public function editarTurista(int $turistaId, int $preferenciaId) {
        $preferencia = $this->preferenciaService->obtenerDelTuristaPorId($turistaId, $preferenciaId);
        if (!$preferencia) abort(404);
        $categorias = CategoriaInteres::all();
        return view('preferencias::preferencias.edit', [
            'preferencia' => $preferencia,
            'categorias' => $categorias,
            'esTurista' => true
        ]);
    }

    public function update(Request $request, int $id) {
        $datos = $request->validate([
            'turista_id' => ['required', 'integer', 'exists:usuarios,id'],
            'presupuesto_max' => ['required', 'numeric', 'min:0'],
            'dias_disponibles' => ['required', 'integer', 'min:1'],
            'hora_inicio_preferida' => ['nullable', 'date_format:H:i'],
            'hora_fin_preferida' => ['nullable', 'date_format:H:i'],
            'categorias' => ['nullable', 'array'],
            'categorias.*' => ['integer', 'exists:categorias_interes,id'],
        ]);

        $this->preferenciaService->actualizar($id, $datos);

        /** @var \App\Modules\Usuarios\Models\Usuario|null $user */
        $user = Auth::user();
        if ($user && $user->isTurista()) {
            return redirect()->route('preferencias.turista', $user->id)->with('exito', 'Tus preferencias han sido actualizadas.');
        }

        return redirect()->route('preferencias.index')->with('exito', 'Preferencia actualizada correctamente.');
    }

    public function actualizarTurista(Request $request, int $turistaId, int $preferenciaId) {
        $preferencia = $this->preferenciaService->obtenerDelTuristaPorId($turistaId, $preferenciaId);
        if (!$preferencia) abort(404);

        $datos = $request->validate([
            'presupuesto_max' => ['required', 'numeric', 'min:0'],
            'dias_disponibles' => ['required', 'integer', 'min:1'],
            'hora_inicio_preferida' => ['nullable', 'date_format:H:i'],
            'hora_fin_preferida' => ['nullable', 'date_format:H:i'],
            'categorias' => ['nullable', 'array'],
            'categorias.*' => ['integer', 'exists:categorias_interes,id'],
        ]);

        $datos['turista_id'] = $turistaId;
        $this->preferenciaService->actualizar($preferenciaId, $datos);

        return redirect()->route('preferencias.turista', $turistaId)->with('exito', 'Preferencias actualizadas correctamente.');
    }

    public function destroy(int $id) {
        if (!$this->preferenciaService->eliminar($id)) abort(404);
        return redirect()->route('preferencias.index')->with('exito', 'Preferencia eliminada correctamente.');
    }

    public function recientes() {
        $resumen = $this->preferenciaService->resumenRecientes(10);
        return view('preferencias::preferencias.proveedor.recientes', $resumen);
    }
}