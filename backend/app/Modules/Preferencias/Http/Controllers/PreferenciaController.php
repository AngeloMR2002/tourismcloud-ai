<?php

namespace App\Modules\Preferencias\Http\Controllers;


use App\Modules\Preferencias\Models\CategoriaInteres;
use App\Models\Usuario;
use App\Modules\Preferencias\Services\PreferenciaService;
use Illuminate\Http\Request;

class PreferenciaController
{
    protected PreferenciaService $preferenciaService;

    public function __construct(PreferenciaService $preferenciaService)
    {
        $this->preferenciaService = $preferenciaService;
    }

    /**
     * Lista todas las preferencias.
     *
     * Uso previsto:
     * - Administrador
     * - Operador turístico
     */
    public function index()
    {
        $preferencias = $this->preferenciaService->listar();

        return view('preferencias::preferencias.index', compact('preferencias'));
    }

    /**
     * Muestra las preferencias de un turista.
     *
     * Temporalmente recibe el ID del turista mediante la ruta.
     * Posteriormente se reemplazará por el usuario autenticado.
     */
    public function turista(int $turistaId)
    {
        $preferencia = $this->preferenciaService
            ->obtenerDelTurista($turistaId);

        return view(
            'preferencias::preferencias.turista.index',
            compact('preferencia')
        );
    }

    public function create()
    {
        $turistas = Usuario::where('rol', 'turista')
            ->where('estado', 'activo')
            ->get();

        $categorias = CategoriaInteres::all();

        return view('preferencias::preferencias.create', compact(
            'turistas',
            'categorias'
        ));
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'turista_id' => ['required', 'integer', 'exists:usuarios,id'],
            'presupuesto_min' => ['nullable', 'numeric', 'min:0'],
            'presupuesto_max' => ['required', 'numeric', 'min:0'],
            'dias_disponibles' => ['required', 'integer', 'min:1'],
            'ritmo' => ['required', 'in:relajado,moderado,intenso'],
            'hora_inicio_dia' => ['nullable', 'date_format:H:i'],
            'hora_fin_dia' => ['nullable', 'date_format:H:i'],
            'vigente' => ['nullable', 'boolean'],
            'categorias' => ['nullable', 'array'],
            'categorias.*' => [
                'integer',
                'exists:categorias_interes,id'
            ],
        ]);

        $this->preferenciaService->crear($datos);

        return redirect()
            ->route('preferencias.index')
            ->with(
                'success',
                'Preferencia registrada correctamente.'
            );
    }

    public function show(int $id)
    {
        $preferencia = $this->preferenciaService->obtener($id);

        if (!$preferencia) {
            abort(404);
        }

        return view(
            'preferencias::preferencias.show',
            compact('preferencia')
        );
    }

    public function edit(int $id)
    {
        $preferencia = $this->preferenciaService->obtener($id);

        if (!$preferencia) {
            abort(404);
        }

        $categorias = CategoriaInteres::all();

        return view('preferencias::preferencias.edit', [
            'preferencia' => $preferencia,
            'categorias' => $categorias,
            'esTurista' => false,
        ]);
    }

    /**
     * Muestra el formulario de edición para el turista.
     *
     * Temporalmente recibe el ID del turista mediante la ruta.
     * Posteriormente se reemplazará por el usuario autenticado.
     */
    public function editarTurista(
        int $turistaId,
        int $preferenciaId
    ) {
        $preferencia = $this->preferenciaService
            ->obtenerDelTuristaPorId(
                $turistaId,
                $preferenciaId
            );

        if (!$preferencia) {
            abort(404);
        }

        $categorias = CategoriaInteres::all();

        return view('preferencias::preferencias.edit', [
            'preferencia' => $preferencia,
            'categorias' => $categorias,
            'esTurista' => true,
        ]);
    }

    /**
     * Actualiza una preferencia perteneciente al turista.
     *
     * Temporalmente recibe el ID del turista mediante la ruta.
     * Posteriormente se reemplazará por el usuario autenticado.
     */
    public function actualizarTurista(
        Request $request,
        int $turistaId,
        int $preferenciaId
    ) {
        $preferencia = $this->preferenciaService
            ->obtenerDelTuristaPorId(
                $turistaId,
                $preferenciaId
            );

        if (!$preferencia) {
            abort(404);
        }

        $datos = $request->validate([
            'presupuesto_min' => [
                'nullable',
                'numeric',
                'min:0'
            ],

            'presupuesto_max' => [
                'required',
                'numeric',
                'min:0'
            ],

            'dias_disponibles' => [
                'required',
                'integer',
                'min:1'
            ],

            'ritmo' => [
                'required',
                'in:relajado,moderado,intenso'
            ],

            'hora_inicio_dia' => [
                'nullable',
                'date_format:H:i'
            ],

            'hora_fin_dia' => [
                'nullable',
                'date_format:H:i'
            ],

            'vigente' => [
                'nullable',
                'boolean'
            ],

            'categorias' => [
                'nullable',
                'array'
            ],

            'categorias.*' => [
                'integer',
                'exists:categorias_interes,id'
            ],
        ]);

        // El turista no puede modificar el propietario
        // de la preferencia.
        $datos['turista_id'] = $turistaId;

        $preferencia = $this->preferenciaService->actualizar(
            $preferenciaId,
            $datos
        );

        if (!$preferencia) {
            abort(404);
        }

        return redirect()
            ->route(
                'preferencias.turista',
                $turistaId
            )
            ->with(
                'success',
                'Preferencias actualizadas correctamente.'
            );
    }

    public function update(Request $request, int $id)
    {
        $datos = $request->validate([
            'turista_id' => ['required', 'integer', 'exists:usuarios,id'],
            'presupuesto_min' => ['nullable', 'numeric', 'min:0'],
            'presupuesto_max' => ['required', 'numeric', 'min:0'],
            'dias_disponibles' => ['required', 'integer', 'min:1'],
            'ritmo' => ['required', 'in:relajado,moderado,intenso'],
            'hora_inicio_dia' => ['nullable', 'date_format:H:i'],
            'hora_fin_dia' => ['nullable', 'date_format:H:i'],
            'vigente' => ['nullable', 'boolean'],
            'categorias' => ['nullable', 'array'],
            'categorias.*' => [
                'integer',
                'exists:categorias_interes,id'
            ],
        ]);

        $preferencia = $this->preferenciaService->actualizar(
            $id,
            $datos
        );

        if (!$preferencia) {
            abort(404);
        }

        return redirect()
            ->route('preferencias.index')
            ->with(
                'success',
                'Preferencia actualizada correctamente.'
            );
    }

    public function destroy(int $id)
    {
        $eliminado = $this->preferenciaService->eliminar($id);

        if (!$eliminado) {
            abort(404);
        }

        return redirect()
            ->route('preferencias.index')
            ->with(
                'success',
                'Preferencia eliminada correctamente.'
            );
    }

    public function recientes()
    {
        $resumen = $this->preferenciaService
            ->resumenRecientes(10);

        return view(
            'preferencias::preferencias.proveedor.recientes',
            $resumen
        );
    }

}


