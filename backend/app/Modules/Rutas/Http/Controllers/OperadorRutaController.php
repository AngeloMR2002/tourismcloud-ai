<?php

namespace App\Modules\Rutas\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Atractivos\Models\Atractivo;
use App\Modules\Destinos\Models\Destino;
use App\Modules\Rutas\Http\Requests\FiltroRutaOperadorRequest;
use App\Modules\Rutas\Http\Requests\RutaRequest;
use App\Modules\Rutas\Models\Ruta;
use App\Modules\Rutas\Services\RutaService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OperadorRutaController extends Controller
{
    public function __construct(protected RutaService $rutaService) {}

    public function index(FiltroRutaOperadorRequest $request): View
    {
        $operadorId = (int) auth()->id();
        $filtros = $request->validated();
        $porPagina = (int) ($filtros['por_pagina'] ?? 15);
        $rutas = $this->rutaService->listarDelOperador($operadorId, $filtros, $porPagina);
        $destinos = $this->destinosDelOperador($operadorId)->get();

        return view('rutas::operador.index', compact('rutas', 'destinos', 'filtros'));
    }

    public function create(Request $request): View
    {
        $destinos = $this->destinosDelOperador((int) auth()->id())
            ->where('estado', 'activo')
            ->get();
        $destinoSeleccionado = $request->integer('destino_id');

        if (! $destinos->contains('id', $destinoSeleccionado)) {
            $destinoSeleccionado = null;
        }

        $atractivosPorDestino = $this->atractivosPorDestino($destinos->pluck('id')->all());

        return view('rutas::operador.create', compact('destinos', 'destinoSeleccionado', 'atractivosPorDestino'));
    }

    public function store(RutaRequest $request): RedirectResponse
    {
        $datos = $request->safe()->except(['paradas', 'imagen_portada', 'quitar_portada']);
        $this->validarDestinoSeleccionable((int) $datos['destino_id']);

        $ruta = $this->rutaService->crear(
            $datos,
            $request->input('paradas', []),
            $request->file('imagen_portada'),
        );

        return redirect()
            ->route('operador.rutas.index')
            ->with('exito', __('operator_routes.route_created', ['name' => $ruta->nombre]));
    }

    public function edit(Ruta $ruta): View
    {
        $this->verificarPropiedad($ruta);

        $destinos = $this->destinosDelOperador((int) auth()->id())
            ->where(function (Builder $consulta) use ($ruta): void {
                $consulta
                    ->where('estado', 'activo')
                    ->orWhere('id', $ruta->destino_id);
            })
            ->get();
        $atractivosPorDestino = $this->atractivosPorDestino($destinos->pluck('id')->all());

        $ruta->load('paradas.atractivo');

        return view('rutas::operador.edit', compact('ruta', 'destinos', 'atractivosPorDestino'));
    }

    public function update(RutaRequest $request, Ruta $ruta): RedirectResponse
    {
        $this->verificarPropiedad($ruta);

        $datos = $request->safe()->except(['paradas', 'imagen_portada', 'quitar_portada']);
        $this->validarDestinoSeleccionable((int) $datos['destino_id'], $ruta);

        $ruta = $this->rutaService->actualizar(
            $ruta,
            $datos,
            $request->input('paradas', []),
            $request->file('imagen_portada'),
            $request->boolean('quitar_portada'),
        );

        return redirect()
            ->route('operador.rutas.index')
            ->with('exito', __('operator_routes.route_updated', ['name' => $ruta->nombre]));
    }

    public function destroy(Ruta $ruta): RedirectResponse
    {
        $this->verificarPropiedad($ruta);

        $nombre = $ruta->nombre;
        $this->rutaService->eliminar($ruta);

        return redirect()
            ->route('operador.rutas.index')
            ->with('exito', __('operator_routes.route_deleted', ['name' => $nombre]));
    }

    public function toggleEstado(Ruta $ruta): RedirectResponse
    {
        $this->verificarPropiedad($ruta);

        $ruta = $this->rutaService->toggleEstado($ruta);

        return back()->with('exito', __('operator_routes.route_status_changed', ['status' => $ruta->estado === 'activo' ? __('operator_routes.active') : __('operator_routes.inactive_status')]));
    }

    private function verificarPropiedad(Ruta $ruta): void
    {
        $esPropia = $ruta->destino()
            ->where('operador_id', auth()->id())
            ->exists();

        abort_unless($esPropia, 403, __('operator_routes.route_manage_forbidden'));
    }

    private function validarDestinoSeleccionable(int $destinoId, ?Ruta $rutaActual = null): void
    {
        $destino = Destino::query()
            ->where('id', $destinoId)
            ->where('operador_id', auth()->id())
            ->first();

        abort_unless($destino !== null, 403, __('operator_routes.destination_use_forbidden'));

        $conservaDestinoActual = $rutaActual !== null
            && (int) $rutaActual->destino_id === (int) $destino->id;

        if ($destino->estado !== 'activo' && ! $conservaDestinoActual) {
            throw ValidationException::withMessages([
                'destino_id' => __('operator_routes.destination_must_be_active'),
            ]);
        }
    }

    private function destinosDelOperador(int $operadorId): Builder
    {
        return Destino::query()
            ->where('operador_id', $operadorId)
            ->orderBy('nombre');
    }

    /**
     * @param  array<int, int>  $destinoIds
     * @return array<int, array<int, array{id: int, nombre: string, costo: string, duracion: int|null, latitud: string|null, longitud: string|null}>>
     */
    private function atractivosPorDestino(array $destinoIds): array
    {
        $resultado = [];

        foreach ($destinoIds as $destinoId) {
            $resultado[(int) $destinoId] = [];
        }

        if ($destinoIds === []) {
            return $resultado;
        }

        $atractivos = Atractivo::query()
            ->whereIn('destino_id', $destinoIds)
            ->where('estado', 'activo')
            ->orderBy('nombre')
            ->get([
                'id',
                'destino_id',
                'nombre',
                'costo_entrada',
                'duracion_estimada_min',
                'latitud',
                'longitud',
            ]);

        foreach ($atractivos as $atractivo) {
            $resultado[(int) $atractivo->destino_id][] = [
                'id' => (int) $atractivo->id,
                'nombre' => $atractivo->nombre,
                'costo' => (string) $atractivo->costo_entrada,
                'duracion' => $atractivo->duracion_estimada_min,
                'latitud' => $atractivo->latitud,
                'longitud' => $atractivo->longitud,
            ];
        }

        return $resultado;
    }
}
