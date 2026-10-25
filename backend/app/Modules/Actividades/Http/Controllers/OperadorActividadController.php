<?php

namespace App\Modules\Actividades\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Actividades\Http\Requests\ActividadRequest;
use App\Modules\Actividades\Http\Requests\FiltroOperadorActividadRequest;
use App\Modules\Actividades\Models\Actividad;
use App\Modules\Actividades\Services\ActividadService;
use App\Modules\Atractivos\Models\CategoriaInteres;
use App\Modules\Destinos\Models\Destino;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OperadorActividadController extends Controller
{
    public function __construct(protected ActividadService $actividadService) {}

    public function index(FiltroOperadorActividadRequest $request): View
    {
        $operadorId = (int) auth()->id();
        $filtros = $request->validated();
        $actividades = $this->actividadService->listarDelOperador($operadorId, $filtros);
        $destinos = $this->destinosDelOperador($operadorId)->get();

        return view('actividades::operador.index', compact('actividades', 'destinos', 'filtros'));
    }

    public function create(): View
    {
        $destinos = $this->destinosDelOperador((int) auth()->id())
            ->where('estado', 'activo')
            ->get();
        $categorias = CategoriaInteres::query()->orderBy('nombre')->get();

        return view('actividades::operador.create', compact('destinos', 'categorias'));
    }

    public function store(ActividadRequest $request): RedirectResponse
    {
        $datos = $request->safe()->except(['imagen_portada', 'quitar_portada']);
        $this->validarDestinoSeleccionable((int) $datos['destino_id']);

        $actividad = $this->actividadService->crear(
            $datos,
            $request->file('imagen_portada'),
        );

        return redirect()
            ->route('operador.actividades.index')
            ->with('exito', __('operator.activity_created', ['name' => $actividad->nombre]));
    }

    public function edit(Actividad $actividad): View
    {
        $this->verificarPropiedad($actividad);
        $actividad->load('categorias');

        $destinos = $this->destinosDelOperador((int) auth()->id())
            ->where(function (Builder $consulta) use ($actividad): void {
                $consulta
                    ->where('estado', 'activo')
                    ->orWhere('id', $actividad->destino_id);
            })
            ->get();
        $categorias = CategoriaInteres::query()->orderBy('nombre')->get();

        return view('actividades::operador.edit', compact('actividad', 'destinos', 'categorias'));
    }

    public function update(ActividadRequest $request, Actividad $actividad): RedirectResponse
    {
        $this->verificarPropiedad($actividad);

        $datos = $request->safe()->except(['imagen_portada', 'quitar_portada']);
        $this->validarDestinoSeleccionable((int) $datos['destino_id'], $actividad);

        $actividad = $this->actividadService->actualizar(
            $actividad,
            $datos,
            $request->file('imagen_portada'),
            $request->boolean('quitar_portada'),
        );

        return redirect()
            ->route('operador.actividades.index')
            ->with('exito', __('operator.activity_updated', ['name' => $actividad->nombre]));
    }

    public function destroy(Actividad $actividad): RedirectResponse
    {
        $this->verificarPropiedad($actividad);

        $nombre = $actividad->nombre;
        $this->actividadService->eliminar($actividad);

        return redirect()
            ->route('operador.actividades.index')
            ->with('exito', __('operator.activity_deleted', ['name' => $nombre]));
    }

    public function toggleEstado(Actividad $actividad): RedirectResponse
    {
        $this->verificarPropiedad($actividad);

        $actividad = $this->actividadService->toggleEstado($actividad);

        return back()->with('exito', __('operator.activity_status_changed', ['status' => $actividad->estado === 'activo' ? __('operator.active_status') : __('operator.inactive_status')]));
    }

    private function verificarPropiedad(Actividad $actividad): void
    {
        $esPropia = $actividad->destino()
            ->where('operador_id', auth()->id())
            ->exists();

        abort_unless($esPropia, 403, __('operator.activity_manage_forbidden'));
    }

    private function validarDestinoSeleccionable(int $destinoId, ?Actividad $actividadActual = null): void
    {
        $destino = Destino::query()
            ->where('id', $destinoId)
            ->where('operador_id', auth()->id())
            ->first();

        abort_unless($destino !== null, 403, __('operator.destination_use_forbidden'));

        $conservaDestinoActual = $actividadActual !== null
            && (int) $actividadActual->destino_id === (int) $destino->id;

        if ($destino->estado !== 'activo' && ! $conservaDestinoActual) {
            throw ValidationException::withMessages([
                'destino_id' => __('operator.destination_must_be_active'),
            ]);
        }
    }

    private function destinosDelOperador(int $operadorId): Builder
    {
        return Destino::query()
            ->where('operador_id', $operadorId)
            ->orderBy('nombre');
    }
}
