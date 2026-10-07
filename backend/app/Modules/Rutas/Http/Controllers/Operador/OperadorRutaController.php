<?php

namespace App\Modules\Rutas\Http\Controllers\Operador;

use App\Http\Controllers\Controller;
use App\Modules\Rutas\Http\Requests\FiltroRutaRequest;
use App\Modules\Rutas\Http\Requests\RutaRequest;
use App\Models\Atractivo;
use App\Models\Destino;
use App\Modules\Rutas\Models\Ruta;
use App\Modules\Rutas\Services\RutaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OperadorRutaController extends Controller
{
    public function __construct(
        protected RutaService $rutaService
    ) {}

    /**
     * Listado administrativo de rutas para el operador turístico.
     */
    public function index(FiltroRutaRequest $request): View
    {
        $filtros = array_merge($request->validated(), ['incluir_inactivos' => true]);
        if (! $request->user()->can('revisar-catalogo')) {
            $filtros['operador_id'] = $request->user()->id;
        }
        $rutas = $this->rutaService->listarConFiltros($filtros, 10);
        $destinos = Destino::activos()->orderBy('nombre')->get();

        return view('rutas::operador.rutas.index', compact('rutas', 'destinos', 'filtros'));
    }

    /**
     * Formulario interactivo para armar una nueva ruta con constructor dinámico de paradas.
     */
    public function create(): View
    {
        $destinos = Destino::activos()->orderBy('nombre')->get();
        $atractivos = Atractivo::activos()->aprobados()->orderBy('nombre')->get();

        return view('rutas::operador.rutas.create', compact('destinos', 'atractivos'));
    }

    /**
     * Almacena la ruta y sus paradas ordenadas.
     */
    public function store(RutaRequest $request): RedirectResponse
    {
        $datos = $request->validated();
        $puntos = $datos['puntos'] ?? [];
        unset($datos['puntos']);

        $datos['operador_id'] = $request->user()->id;
        $datos['estado_verificacion'] = 'pendiente';
        $datos['fecha_verificacion'] = null;

        $this->rutaService->crearConPuntos($datos, $puntos);

        return redirect()
            ->route('operador.rutas.index')
            ->with('exito', '¡La ruta turística y sus paradas fueron registradas exitosamente!');
    }

    /**
     * Formulario para editar una ruta y reordenar sus paradas.
     */
    public function edit(int $id): View
    {
        $ruta = $this->obtenerPropia($id);
        $destinos = Destino::activos()->orderBy('nombre')->get();
        $atractivos = Atractivo::activos()->aprobados()->orderBy('nombre')->get();

        return view('rutas::operador.rutas.edit', compact('ruta', 'destinos', 'atractivos'));
    }

    /**
     * Actualiza la ruta y sus paradas.
     */
    public function update(RutaRequest $request, int $id): RedirectResponse
    {
        $ruta = $this->obtenerPropia($id);
        $datos = $request->validated();
        $puntos = $datos['puntos'] ?? [];
        unset($datos['puntos']);

        $datos['estado_verificacion'] = 'pendiente';
        $datos['fecha_verificacion'] = null;
        $this->rutaService->actualizarConPuntos($ruta, $datos, $puntos);

        return redirect()
            ->route('operador.rutas.index')
            ->with('exito', '¡La ruta turística y sus paradas fueron actualizadas correctamente!');
    }

    /**
     * Elimina lógicamente una ruta turística.
     */
    public function destroy(int $id): RedirectResponse
    {
        $ruta = $this->obtenerPropia($id);
        $this->rutaService->eliminar($ruta);

        return redirect()
            ->route('operador.rutas.index')
            ->with('exito', 'La ruta turística ha sido eliminada.');
    }

    /**
     * Alterna el estado activo / inactivo de la ruta.
     */
    public function toggleEstado(int $id): RedirectResponse
    {
        $ruta = $this->obtenerPropia($id);
        $this->rutaService->toggleEstado($ruta);

        return back()->with('exito', "El estado de la ruta cambió a {$ruta->estado}.");
    }
    protected function obtenerPropia(int $id): Ruta
    {
        $registro = $this->rutaService->obtenerPorId($id);
        abort_unless(auth()->user()->can('revisar-catalogo') || $registro->operador_id === auth()->id(), 404);

        return $registro;
    }
}
