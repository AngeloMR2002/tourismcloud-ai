<?php

namespace App\Http\Controllers\Operador;

use App\Http\Controllers\Controller;
use App\Http\Requests\AtractivoRequest;
use App\Models\Atractivo;
use App\Models\CategoriaInteres;
use App\Models\Destino;
use App\Services\AtractivoService;
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
 *
 * TODO: Cuando feature/auth defina el alias 'role', añadir el middleware
 *       'role:operador_turistico' al constructor de este controlador.
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
        // TODO: reemplazar 1 por auth()->id() cuando feature/auth esté disponible.
        $operadorId = $request->get('_operador_id_test', 1);

        $filtros = array_merge(
            $request->only(['destino_id', 'busqueda']),
            ['operador_id' => $operadorId]
        );

        $atractivos = $this->service->listar($filtros, porPagina: 20);
        $destinos   = Destino::where('operador_id', $operadorId)->orderBy('nombre')->get();

        return view('operador.atractivos.index', compact('atractivos', 'destinos'));
    }

    /**
     * Formulario de creación de atractivo.
     */
    public function create(Request $request): View
    {
        $operadorId  = $request->get('_operador_id_test', 1);
        $destinos    = Destino::where('operador_id', $operadorId)->orderBy('nombre')->get();
        $categorias  = CategoriaInteres::orderBy('nombre')->get();

        return view('operador.atractivos.create', compact('destinos', 'categorias'));
    }

    /**
     * Persiste un nuevo atractivo.
     */
    public function store(AtractivoRequest $request): RedirectResponse
    {
        $operadorId = $request->get('_operador_id_test', 1);
        $datos      = $request->safe()->except(['categorias', 'imagen_portada', 'galeria', '_operador_id_test']);

        // Verificar que el destino le pertenece al operador.
        $this->verificarPropiedadDestino((int) $datos['destino_id'], $operadorId);

        $atractivo = $this->service->crear(
            datos:      $datos,
            categorias: $request->input('categorias', []),
            portada:    $request->file('imagen_portada'),
            galeria:    $request->file('galeria', [])
        );

        return redirect()
            ->route('operador.atractivos.index')
            ->with('exito', "Atractivo \"{$atractivo->nombre}\" creado correctamente.");
    }

    /**
     * Formulario de edición de atractivo.
     */
    public function edit(Request $request, Atractivo $atractivo): View
    {
        $operadorId = $request->get('_operador_id_test', 1);
        $this->verificarPropiedadDestino($atractivo->destino_id, $operadorId);

        $destinos   = Destino::where('operador_id', $operadorId)->orderBy('nombre')->get();
        $categorias = CategoriaInteres::orderBy('nombre')->get();
        $atractivo->load(['categorias', 'imagenes']);

        return view('operador.atractivos.edit', compact('atractivo', 'destinos', 'categorias'));
    }

    /**
     * Actualiza un atractivo existente.
     */
    public function update(AtractivoRequest $request, Atractivo $atractivo): RedirectResponse
    {
        $operadorId = $request->get('_operador_id_test', 1);
        $this->verificarPropiedadDestino($atractivo->destino_id, $operadorId);

        $datos = $request->safe()->except(['categorias', 'imagen_portada', 'galeria', '_operador_id_test']);

        $this->service->actualizar(
            atractivo:  $atractivo,
            datos:      $datos,
            categorias: $request->input('categorias', []),
            portada:    $request->file('imagen_portada'),
            galeria:    $request->file('galeria', [])
        );

        return redirect()
            ->route('operador.atractivos.index')
            ->with('exito', "Atractivo \"{$atractivo->nombre}\" actualizado correctamente.");
    }

    /**
     * Elimina (soft delete) un atractivo.
     */
    public function destroy(Request $request, Atractivo $atractivo): RedirectResponse
    {
        $operadorId = $request->get('_operador_id_test', 1);
        $this->verificarPropiedadDestino($atractivo->destino_id, $operadorId);

        $nombre = $atractivo->nombre;
        $this->service->eliminar($atractivo);

        return redirect()
            ->route('operador.atractivos.index')
            ->with('exito', "Atractivo \"{$nombre}\" eliminado.");
    }

    /**
     * Alterna el estado activo/inactivo (NO hace soft delete).
     * Ruta: PATCH /operador/atractivos/{atractivo}/toggle-estado
     */
    public function toggleEstado(Request $request, Atractivo $atractivo): RedirectResponse
    {
        $operadorId = $request->get('_operador_id_test', 1);
        $this->verificarPropiedadDestino($atractivo->destino_id, $operadorId);

        $atractivo = $this->service->toggleEstado($atractivo);

        return back()->with('exito', "Estado cambiado a \"{$atractivo->estado}\".");
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

        abort_unless($pertenece, 403, 'No tienes permiso para gestionar atractivos de este destino.');
    }
}
