<?php

namespace App\Modules\Actividades\Http\Controllers\Operador;

use App\Http\Controllers\Controller;
use App\Modules\Actividades\Http\Requests\ActividadRequest;
use App\Modules\Actividades\Http\Requests\FiltroActividadRequest;
use App\Modules\Actividades\Models\Actividad;
use App\Models\Atractivo;
use App\Models\Destino;
use App\Modules\Actividades\Services\ActividadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OperadorActividadController extends Controller
{
    public function __construct(
        protected ActividadService $actividadService
    ) {}

    /**
     * Listado administrativo de actividades para el operador.
     */
    public function index(FiltroActividadRequest $request): View
    {
        $filtros = array_merge($request->validated(), ['incluir_inactivos' => true]);
        if (! $request->user()->can('revisar-catalogo')) {
            $filtros['operador_id'] = $request->user()->id;
        }
        $actividades = $this->actividadService->listarConFiltros($filtros, 10);
        $destinos = Destino::activos()->orderBy('nombre')->get();

        return view('actividades::operador.actividades.index', compact('actividades', 'destinos', 'filtros'));
    }

    /**
     * Formulario para registrar una nueva actividad.
     */
    public function create(Request $request): View
    {
        $destinos = Destino::activos()->orderBy('nombre')->get();
        $atractivos = Atractivo::activos()->aprobados()->orderBy('nombre')->get();
        $sugerido = $atractivos->firstWhere('id', $request->integer('atractivo_id'));
        $actividad = $sugerido ? new Actividad([
            'nombre' => 'Visita a '.$sugerido->nombre,
            'descripcion' => $sugerido->descripcion,
            'destino_id' => $sugerido->destino_id,
            'atractivo_id' => $sugerido->id,
            'fuente_url' => $sugerido->fuente_url,
            'sitio_web_oficial' => $sugerido->sitio_web_oficial,
            'latitud' => $sugerido->latitud,
            'longitud' => $sugerido->longitud,
            'tipo_registro' => 'visita',
            'tipo_precio' => 'variable',
            'estado' => 'activo',
        ]) : null;

        return view('actividades::operador.actividades.create', compact('destinos', 'atractivos', 'actividad'));
    }

    /**
     * Guarda la nueva actividad en la base de datos.
     */
    public function store(ActividadRequest $request): RedirectResponse
    {
        $datos = $request->validated();
        $datos['operador_id'] = $request->user()->id;
        $datos['estado_verificacion'] = 'pendiente';
        $datos['fecha_verificacion'] = null;

        $this->actividadService->crear($datos);

        return redirect()
            ->route('operador.actividades.index')
            ->with('exito', '¡La actividad turística fue registrada exitosamente!');
    }

    /**
     * Formulario para editar una actividad existente.
     */
    public function edit(int $id): View
    {
        $actividad = $this->obtenerPropia($id);
        $destinos = Destino::activos()->orderBy('nombre')->get();
        $atractivos = Atractivo::activos()->aprobados()->orderBy('nombre')->get();

        return view('actividades::operador.actividades.edit', compact('actividad', 'destinos', 'atractivos'));
    }

    /**
     * Actualiza la actividad turística.
     */
    public function update(ActividadRequest $request, int $id): RedirectResponse
    {
        $actividad = $this->obtenerPropia($id);
        $datos = $request->validated();
        $datos['estado_verificacion'] = 'pendiente';
        $datos['fecha_verificacion'] = null;
        $this->actividadService->actualizar($actividad, $datos);

        return redirect()
            ->route('operador.actividades.index')
            ->with('exito', '¡La actividad turística fue actualizada correctamente!');
    }

    /**
     * Elimina lógicamente una actividad.
     */
    public function destroy(int $id): RedirectResponse
    {
        $actividad = $this->obtenerPropia($id);
        $this->actividadService->eliminar($actividad);

        return redirect()
            ->route('operador.actividades.index')
            ->with('exito', 'La actividad turística ha sido eliminada.');
    }

    /**
     * Cambia el estado activo / inactivo de la actividad.
     */
    public function toggleEstado(int $id): RedirectResponse
    {
        $actividad = $this->obtenerPropia($id);
        $this->actividadService->toggleEstado($actividad);

        return back()->with('exito', "El estado de la actividad cambió a {$actividad->estado}.");
    }
    protected function obtenerPropia(int $id): Actividad
    {
        $registro = $this->actividadService->obtenerPorId($id);
        abort_unless(auth()->user()->can('revisar-catalogo') || $registro->operador_id === auth()->id(), 404);

        return $registro;
    }
}
