<?php
namespace App\Modules\Preferencias\Services;

use App\Modules\Preferencias\Models\PreferenciaTurista;
use Illuminate\Support\Facades\DB;

class PreferenciaService {
    public function listar() {
        return PreferenciaTurista::with(['turista', 'categorias'])->get();
    }

    public function obtener(int $id): ?PreferenciaTurista {
        return PreferenciaTurista::with(['turista', 'categorias'])->find($id);
    }

    public function obtenerDelTurista(int $turistaId): ?PreferenciaTurista {
        return PreferenciaTurista::with(['turista', 'categorias'])
            ->where('turista_id', $turistaId)->first(); // Quité 'vigente' porque no está en tu migración
    }

    public function obtenerDelTuristaPorId(int $turistaId, int $preferenciaId): ?PreferenciaTurista {
        return PreferenciaTurista::with(['turista', 'categorias'])
            ->where('id', $preferenciaId)->where('turista_id', $turistaId)->first();
    }

    public function crear(array $datos): PreferenciaTurista {
        return DB::transaction(function () use ($datos) {
            $categorias = $datos['categorias'] ?? [];
            unset($datos['categorias']);
            
            // Tu tabla usa UpdateOrInsert lógicamente porque tiene turista_id UNIQUE
            $preferencia = PreferenciaTurista::updateOrCreate(
                ['turista_id' => $datos['turista_id']],
                $datos
            );
            $preferencia->categorias()->sync($categorias);
            return $preferencia->load(['turista', 'categorias']);
        });
    }

    public function actualizar(int $id, array $datos): ?PreferenciaTurista {
        return DB::transaction(function () use ($id, $datos) {
            $preferencia = PreferenciaTurista::find($id);
            if (!$preferencia) return null;
            $categorias = $datos['categorias'] ?? [];
            unset($datos['categorias']);
            $preferencia->update($datos);
            $preferencia->categorias()->sync($categorias);
            return $preferencia->load(['turista', 'categorias']);
        });
    }

    public function eliminar(int $id): bool {
        $preferencia = PreferenciaTurista::find($id);
        if (!$preferencia) return false;
        return (bool) $preferencia->delete();
    }

    public function obtenerRecientes(int $limite = 10) {
        return PreferenciaTurista::with('categorias')->latest('created_at')->limit($limite)->get();
    }

    public function resumenRecientes(int $limite = 10): array {
        $preferencias = $this->obtenerRecientes($limite);
        return [
            'preferencias' => $preferencias,
            'presupuestos' => $preferencias->map(fn ($p) => ['max' => $p->presupuesto_max, 'fecha' => $p->created_at]),
            'duraciones' => $preferencias->groupBy('dias_disponibles')->map(fn ($g) => $g->count())->sortKeys(),
            'categorias' => $preferencias->flatMap(fn ($p) => $p->categorias)->groupBy('id')
                ->map(fn ($g) => ['nombre' => $g->first()->nombre, 'cantidad' => $g->count()])->sortByDesc('cantidad')->values(),
        ];
    }
}