<?php

namespace App\Modules\Preferencias\Services;

use App\Modules\Preferencias\Models\PreferenciaTurista;
use Illuminate\Support\Facades\DB;

class PreferenciaService
{
    /**
     * Obtener todas las preferencias.
     */
    public function listar()
    {
        return PreferenciaTurista::with([
            'turista',
            'categorias',
        ])->get();
    }

    /**
     * Obtener una preferencia por su ID.
     */
    public function obtener(int $id): ?PreferenciaTurista
    {
        return PreferenciaTurista::with([
            'turista',
            'categorias',
        ])->find($id);
    }

    /**
     * Obtiene las preferencias pertenecientes a un turista.
     *
     * Actualmente se devuelve la preferencia vigente.
     */
    public function obtenerDelTurista(int $turistaId): ?PreferenciaTurista
    {
        return PreferenciaTurista::with([
            'turista',
            'categorias',
        ])
            ->where('turista_id', $turistaId)
            ->where('vigente', true)
            ->latest('created_at')
            ->first();
    }

    /**
     * Obtiene una preferencia específica perteneciente a un turista.
     *
     * Se utiliza para validar que el turista solo pueda editar
     * una preferencia que le pertenece.
     */
    public function obtenerDelTuristaPorId(
        int $turistaId,
        int $preferenciaId
    ): ?PreferenciaTurista {
        return PreferenciaTurista::with([
            'turista',
            'categorias',
        ])
            ->where('id', $preferenciaId)
            ->where('turista_id', $turistaId)
            ->first();
    }

    /**
     * Crear una nueva preferencia.
     */
    public function crear(array $datos): PreferenciaTurista
    {
        return DB::transaction(function () use ($datos) {

            $categorias = $datos['categorias'] ?? [];

            unset($datos['categorias']);

            $preferencia = PreferenciaTurista::create($datos);

            $preferencia->categorias()->sync($categorias);

            return $preferencia->load([
                'turista',
                'categorias',
            ]);
        });
    }

    /**
     * Actualizar una preferencia existente.
     */
    public function actualizar(
        int $id,
        array $datos
    ): ?PreferenciaTurista {

        return DB::transaction(function () use ($id, $datos) {

            $preferencia = PreferenciaTurista::find($id);

            if (!$preferencia) {
                return null;
            }

            $categorias = $datos['categorias'] ?? [];

            unset($datos['categorias']);

            $preferencia->update($datos);

            $preferencia->categorias()->sync($categorias);

            return $preferencia->load([
                'turista',
                'categorias',
            ]);
        });
    }

    /**
     * Eliminar una preferencia.
     */
    public function eliminar(int $id): bool
    {
        $preferencia = PreferenciaTurista::find($id);

        if (!$preferencia) {
            return false;
        }

        return (bool) $preferencia->delete();
    }

    public function obtenerRecientes(int $limite = 10)
    {
        return PreferenciaTurista::with('categorias')
            ->latest('created_at')
            ->limit($limite)
            ->get();
    }

    public function resumenRecientes(int $limite = 10): array
    {
        $preferencias = $this->obtenerRecientes($limite);

        return [
            'preferencias' => $preferencias,

            'presupuestos' => $preferencias->map(function ($preferencia) {
                return [
                    'min' => $preferencia->presupuesto_min,
                    'max' => $preferencia->presupuesto_max,
                    'fecha' => $preferencia->created_at,
                ];
            }),

            'duraciones' => $preferencias
                ->groupBy('dias_disponibles')
                ->map(fn ($grupo) => $grupo->count())
                ->sortKeys(),

            'ritmos' => $preferencias
                ->groupBy('ritmo')
                ->map(fn ($grupo) => $grupo->count()),

            'categorias' => $preferencias
                ->flatMap(fn ($preferencia) => $preferencia->categorias)
                ->groupBy('id')
                ->map(function ($grupo) {
                    return [
                        'nombre' => $grupo->first()->nombre,
                        'cantidad' => $grupo->count(),
                    ];
                })
                ->sortByDesc('cantidad')
                ->values(),
        ];
    }
}