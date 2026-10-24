<?php

namespace App\Modules\Atractivos\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Atractivos\Http\Requests\AtractivoRequest;
use App\Modules\Atractivos\Models\Atractivo;
use App\Modules\Atractivos\Models\CategoriaInteres;
use App\Modules\Atractivos\Services\AtractivoService;
use App\Modules\Destinos\Models\Destino;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controlador del panel "Mis Atractivos" para el rol operador_turistico.
 *
 * Reglas de acceso:
 * - El operador solo puede ver/editar atractivos de destinos que le pertenecen
 *   (destinos.operador_id = usuario autenticado).
 * - La verificación de propiedad se hace en cada método antes de delegar al service.
 */
class OperadorAtractivoController extends Controller
{
    public function __construct(
        protected AtractivoService $service
    ) {}

    /**
     * Listado de "Mis Atractivos" (tabla de gestión).
     */
    public function index(Request $request): View
    {
        $operadorId = (int) $request->user()->id;

        $filtros = array_merge(
            $request->only(['destino_id', 'busqueda']),
            ['operador_id' => $operadorId]
        );

        $atractivos = $this->service->listar($filtros, porPagina: 20);
        $destinos = Destino::where('operador_id', $operadorId)->orderBy('nombre')->get();

        return view('operador.atractivos.index', compact('atractivos', 'destinos'));
    }

    /**
     * Formulario de creación de atractivo.
     */
    public function create(Request $request): View
    {
        $operadorId = (int) $request->user()->id;
        $destinos = Destino::where('operador_id', $operadorId)->orderBy('nombre')->get();
        $categorias = CategoriaInteres::orderBy('nombre')->get();

        return view('operador.atractivos.create', compact('destinos', 'categorias'));
    }

    /**
     * Persiste un nuevo atractivo.
     */
    public function store(AtractivoRequest $request): RedirectResponse
    {
        $operadorId = (int) $request->user()->id;
        $datos = $request->safe()->except(['categorias', 'imagen_portada', 'galeria']);
        $datos['horarios'] = (array) $request->input('horarios', []);

        // Verificar que el destino le pertenece al operador.
        $this->verificarPropiedadDestino((int) $datos['destino_id'], $operadorId);

        $atractivo = $this->service->crear(
            datos: $datos,
            categorias: $request->input('categorias', []),
            portada: $request->file('imagen_portada'),
            galeria: $request->file('galeria', [])
        );

        return redirect()
            ->route('operador.atractivos.index')
            ->with('exito', __('operator_attractions.attraction_created', ['name' => $atractivo->nombre]));
    }

    /**
     * Formulario de edición de atractivo.
     */
    public function edit(Request $request, Atractivo $atractivo): View
    {
        $operadorId = (int) $request->user()->id;
        $this->verificarPropiedadDestino($atractivo->destino_id, $operadorId);

        $destinos = Destino::where('operador_id', $operadorId)->orderBy('nombre')->get();
        $categorias = CategoriaInteres::orderBy('nombre')->get();
        $atractivo->load(['categorias', 'imagenes']);

        return view('operador.atractivos.edit', compact('atractivo', 'destinos', 'categorias'));
    }

    /**
     * Actualiza un atractivo existente.
     */
    public function update(AtractivoRequest $request, Atractivo $atractivo): RedirectResponse
    {
        $operadorId = (int) $request->user()->id;
        $this->verificarPropiedadDestino($atractivo->destino_id, $operadorId);

        $datos = $request->safe()->except(['categorias', 'imagen_portada', 'galeria', 'eliminar_imagenes']);
        $datos['horarios'] = (array) $request->input('horarios', []);

        if (isset($datos['destino_id'])) {
            $this->verificarPropiedadDestino((int) $datos['destino_id'], $operadorId);
        }

        $this->service->actualizar(
            atractivo: $atractivo,
            datos: $datos,
            categorias: $request->input('categorias', []),
            portada: $request->file('imagen_portada'),
            galeria: $request->file('galeria', []),
            eliminarImagenes: (array) $request->input('eliminar_imagenes', [])
        );

        return redirect()
            ->route('operador.atractivos.index')
            ->with('exito', __('operator_attractions.attraction_updated', ['name' => $atractivo->nombre]));
    }

    /**
     * Elimina una imagen de galería vía AJAX.
     */
    public function eliminarImagen(Request $request, Atractivo $atractivo, int $imagen): JsonResponse
    {
        $operadorId = (int) $request->user()->id;
        $this->verificarPropiedadDestino($atractivo->destino_id, $operadorId);

        $this->service->eliminarImagen($atractivo, $imagen);

        return response()->json(['exito' => true, 'mensaje' => __('operator_attractions.image_deleted')]);
    }

    /**
     * Elimina (soft delete) un atractivo.
     */
    public function destroy(Request $request, Atractivo $atractivo): RedirectResponse
    {
        $operadorId = (int) $request->user()->id;
        $this->verificarPropiedadDestino($atractivo->destino_id, $operadorId);

        $nombre = $atractivo->nombre;
        $this->service->eliminar($atractivo);

        return redirect()
            ->route('operador.atractivos.index')
            ->with('exito', __('operator_attractions.attraction_deleted', ['name' => $nombre]));
    }

    /**
     * Alterna el estado activo/inactivo (NO hace soft delete).
     * Ruta: PATCH /operador/atractivos/{atractivo}/toggle-estado
     */
    public function toggleEstado(Request $request, Atractivo $atractivo): RedirectResponse
    {
        $operadorId = (int) $request->user()->id;
        $this->verificarPropiedadDestino($atractivo->destino_id, $operadorId);

        $atractivo = $this->service->toggleEstado($atractivo);

        return back()->with('exito', __('operator_attractions.status_changed', [
            'status' => __('operator_attractions.'.$atractivo->estado),
        ]));
    }

    /**
     * Verifica que el destino pertenece al operador autenticado.
     * Lanza 403 si no hay correspondencia.
     */
    private function verificarPropiedadDestino(int $destinoId, int $operadorId): void
    {
        $pertenece = Destino::where('id', $destinoId)
            ->where('operador_id', $operadorId)
            ->exists();

        abort_unless($pertenece, 403, __('operator_attractions.destination_forbidden'));
    }
}
