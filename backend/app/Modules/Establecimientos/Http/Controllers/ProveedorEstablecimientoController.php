<?php

namespace App\Modules\Establecimientos\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Atractivos\Models\CategoriaInteres;
use App\Modules\Destinos\Models\Destino;
use App\Modules\Establecimientos\Http\Requests\EstablecimientoRequest;
use App\Modules\Establecimientos\Models\Establecimiento;
use App\Modules\Establecimientos\Services\EstablecimientoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controlador del panel "Mis Establecimientos" para el rol proveedor.
 *
 * Reglas de acceso:
 * - El proveedor solo puede ver/editar establecimientos donde proveedor_id = su propio ID.
 * - Nunca gestiona atractivos.
 * - El scope de proveedor se inyecta siempre al llamar al service.
 *
 * TODO: Cuando feature/auth defina el alias 'role', añadir el middleware
 *       'role:proveedor' al constructor de este controlador.
 */
class ProveedorEstablecimientoController extends Controller
{
    public function __construct(
        protected EstablecimientoService $service
    ) {}

    /**
     * Listado de "Mis Establecimientos" (tabla de gestión).
     */
    public function index(Request $request): View
    {
        // TODO: reemplazar por auth()->id() cuando feature/auth esté disponible.
        $proveedorId = (int) $request->get('_proveedor_id_test', 2);

        $filtros = array_merge(
            $request->only(['destino_id', 'tipo', 'rango_precio', 'busqueda']),
            ['proveedor_id' => $proveedorId]
        );

        $establecimientos = $this->service->listar($filtros, porPagina: 20);
        $destinos         = Destino::orderBy('nombre')->get();

        return view('proveedor.establecimientos.index', compact('establecimientos', 'destinos'));
    }

    /**
     * Formulario de creación de establecimiento.
     */
    public function create(Request $request): View
    {
        $proveedorId = (int) $request->get('_proveedor_id_test', 2);
        $destinos   = Destino::orderBy('nombre')->get();
        $categorias = CategoriaInteres::orderBy('nombre')->get();

        return view('proveedor.establecimientos.create', compact('destinos', 'categorias'));
    }

    /**
     * Persiste un nuevo establecimiento.
     * El proveedor_id se inyecta desde la sesión, no desde el form.
     */
    public function store(EstablecimientoRequest $request): RedirectResponse
    {
        // TODO: reemplazar por auth()->id() cuando feature/auth esté disponible.
        $proveedorId = (int) $request->get('_proveedor_id_test', 2);

        $datos = array_merge(
            $request->safe()->except(['categorias', 'imagen_portada', 'galeria', '_proveedor_id_test', 'eliminar_imagenes']),
            ['proveedor_id' => $proveedorId]
        );

        $establecimiento = $this->service->crear(
            datos:      $datos,
            categorias: $request->input('categorias', []),
            portada:    $request->file('imagen_portada'),
            galeria:    $request->file('galeria', [])
        );

        return redirect()
            ->route('proveedor.establecimientos.index', ['_proveedor_id_test' => $proveedorId])
            ->with('exito', "Establecimiento \"{$establecimiento->nombre}\" creado correctamente.");
    }

    /**
     * Formulario de edición de establecimiento.
     * Verifica que el registro pertenezca al proveedor autenticado.
     */
    public function edit(Request $request, Establecimiento $establecimiento): View
    {
        $proveedorId = (int) $request->get('_proveedor_id_test', 2);
        $this->verificarPropiedad($establecimiento, $proveedorId);

        $destinos   = Destino::orderBy('nombre')->get();
        $categorias = CategoriaInteres::orderBy('nombre')->get();
        $establecimiento->load(['categorias', 'imagenes']);

        return view('proveedor.establecimientos.edit', compact('establecimiento', 'destinos', 'categorias'));
    }

    /**
     * Actualiza un establecimiento existente.
     */
    public function update(EstablecimientoRequest $request, Establecimiento $establecimiento): RedirectResponse
    {
        $proveedorId = (int) $request->get('_proveedor_id_test', 2);
        $this->verificarPropiedad($establecimiento, $proveedorId);

        $datos = $request->safe()->except(['categorias', 'imagen_portada', 'galeria', '_proveedor_id_test', 'eliminar_imagenes']);

        $this->service->actualizar(
            establecimiento:  $establecimiento,
            datos:            $datos,
            categorias:       $request->input('categorias', []),
            portada:          $request->file('imagen_portada'),
            galeria:          $request->file('galeria', []),
            eliminarImagenes: (array) $request->input('eliminar_imagenes', [])
        );

        return redirect()
            ->route('proveedor.establecimientos.index', ['_proveedor_id_test' => $proveedorId])
            ->with('exito', "Establecimiento \"{$establecimiento->nombre}\" actualizado correctamente.");
    }

    /**
     * Elimina una imagen de galería vía AJAX.
     */
    public function eliminarImagen(Request $request, Establecimiento $establecimiento, int $imagen): \Illuminate\Http\JsonResponse
    {
        $proveedorId = (int) $request->get('_proveedor_id_test', 2);
        $this->verificarPropiedad($establecimiento, $proveedorId);

        $this->service->eliminarImagen($establecimiento, $imagen);

        return response()->json(['exito' => true, 'mensaje' => 'Imagen eliminada correctamente.']);
    }

    /**
     * Elimina (soft delete) un establecimiento.
     */
    public function destroy(Request $request, Establecimiento $establecimiento): RedirectResponse
    {
        $proveedorId = (int) $request->get('_proveedor_id_test', 2);
        $this->verificarPropiedad($establecimiento, $proveedorId);

        $nombre = $establecimiento->nombre;
        $this->service->eliminar($establecimiento);

        return redirect()
            ->route('proveedor.establecimientos.index', ['_proveedor_id_test' => $proveedorId])
            ->with('exito', "Establecimiento \"{$nombre}\" eliminado.");
    }

    /**
     * Alterna el estado activo/inactivo (NO hace soft delete).
     * Ruta: PATCH /proveedor/establecimientos/{establecimiento}/toggle-estado
     */
    public function toggleEstado(Request $request, Establecimiento $establecimiento): RedirectResponse
    {
        $proveedorId = (int) $request->get('_proveedor_id_test', 2);
        $this->verificarPropiedad($establecimiento, $proveedorId);

        $establecimiento = $this->service->toggleEstado($establecimiento);

        return back()->with('exito', "Estado cambiado a \"{$establecimiento->estado}\".");
    }

    /**
     * Verifica que el establecimiento pertenece al proveedor autenticado.
     */
    private function verificarPropiedad(Establecimiento $establecimiento, int $proveedorId): void
    {
        abort_unless(
            $establecimiento->proveedor_id === $proveedorId,
            403,
            'No tienes permiso para gestionar este establecimiento.'
        );
    }
}
