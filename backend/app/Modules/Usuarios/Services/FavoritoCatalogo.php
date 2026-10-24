<?php

namespace App\Modules\Usuarios\Services;

use App\Modules\Actividades\Models\Actividad;
use App\Modules\Atractivos\Models\Atractivo;
use App\Modules\Establecimientos\Models\Establecimiento;
use App\Modules\Rutas\Models\Ruta;
use App\Modules\Rutas\Services\RutaService;
use Illuminate\Database\Eloquent\Builder;

class FavoritoCatalogo
{
    public const TIPOS = ['atractivo', 'actividad', 'ruta', 'establecimiento'];

    public function __construct(private RutaService $rutas) {}

    public function consulta(string $tipo): Builder
    {
        $query = match ($tipo) {
            'atractivo' => Atractivo::query()->with(['destino', 'categorias'])->withCount('valoraciones')->withAvg('valoraciones', 'puntuacion'),
            'actividad' => Actividad::query()->with('destino'),
            'ruta' => Ruta::query()->with(['destino', 'paradas.atractivo'])->withCount('paradas'),
            'establecimiento' => Establecimiento::query()->with(['destino', 'categorias'])->withCount('valoraciones')->withAvg('valoraciones', 'puntuacion'),
            default => abort(404),
        };

        if ($tipo === 'ruta') {
            $this->rutas->aplicarVisibilidadPublica($query);
        } else {
            $query->where('estado', 'activo');
            if ($tipo === 'actividad') {
                $query->whereHas('destino', fn (Builder $destino) => $destino->where('estado', 'activo'));
            }
        }

        return $query;
    }
}
