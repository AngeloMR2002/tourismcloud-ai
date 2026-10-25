<?php

namespace App\Modules\Itinerarios\Services;

use App\Modules\Actividades\Models\Actividad;
use App\Modules\Atractivos\Models\Atractivo;
use App\Modules\Establecimientos\Models\Establecimiento;
use App\Modules\Itinerarios\Models\Itinerario;
use App\Modules\Itinerarios\Models\ItinerarioDia;
use App\Modules\Itinerarios\Models\ItinerarioItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ItinerarioItemService
{
    private const DIAS = ['domingo', 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];

    /** Solo entidades activas, no eliminadas y del mismo destino del itinerario. */
    public function buscarEntidad(?string $tipo, int $id, int $destinoId): ?Model
    {
        $consulta = match ($tipo) {
            'atractivo' => Atractivo::query(),
            'actividad' => Actividad::query(),
            'establecimiento' => Establecimiento::query(),
            default => null,
        };

        return $consulta?->whereKey($id)->where('estado', 'activo')->where('destino_id', $destinoId)->first();
    }

    /** Atractivos y actividades usan el precio publicado; el establecimiento no tiene precio fijo. */
    public function costoDe(Model $entidad, ?float $costoIngresado): float
    {
        return match (true) {
            $entidad instanceof Atractivo => (float) $entidad->costo_entrada,
            $entidad instanceof Actividad => (float) $entidad->costo,
            default => round((float) ($costoIngresado ?? 0), 2),
        };
    }

    public function haySolapamiento(ItinerarioDia $dia, string $inicio, string $fin, ?int $excluirId = null): bool
    {
        return $dia->items()
            ->when($excluirId, fn ($consulta) => $consulta->whereKeyNot($excluirId))
            ->where('hora_inicio', '<', $fin.':00')
            ->where('hora_fin', '>', $inicio.':00')
            ->exists();
    }

    /** Solo se valida si el día tiene fecha y el lugar publicó horarios. */
    public function errorDeHorario(Model $entidad, ItinerarioDia $dia, string $inicio, string $fin): ?string
    {
        if ($dia->fecha === null || $entidad instanceof Actividad || $entidad->horarios === []) {
            return null;
        }

        $horario = $entidad->horarios[self::DIAS[$dia->fecha->dayOfWeek]] ?? null;

        if ($horario === null) {
            return __('modules.visit_closed_day');
        }

        if ($inicio < $horario['abre'] || $fin > $horario['cierra']) {
            return __('modules.visit_outside_hours', ['opens' => $horario['abre'], 'closes' => $horario['cierra']]);
        }

        return null;
    }

    public function excedePresupuesto(Itinerario $itinerario, float $costoNuevo, ?ItinerarioItem $actual = null): bool
    {
        if ($itinerario->presupuesto_total === null) {
            return false;
        }

        $gastado = (float) $itinerario->items()
            ->when($actual, fn ($consulta) => $consulta->whereKeyNot($actual->id))
            ->sum('itinerario_items.costo_estimado');

        return round($gastado + $costoNuevo, 2) > (float) $itinerario->presupuesto_total;
    }

    public function agregar(ItinerarioDia $dia, array $datos): ItinerarioItem
    {
        return DB::transaction(function () use ($dia, $datos): ItinerarioItem {
            $item = $dia->items()->create($datos + ['orden' => $dia->items()->count() + 1]);
            $this->reordenarDia($dia);

            return $item;
        });
    }

    public function actualizar(ItinerarioItem $item, ItinerarioDia $diaDestino, array $datos): void
    {
        DB::transaction(function () use ($item, $diaDestino, $datos): void {
            $diaOriginal = $item->dia;
            $item->update($datos + ['itinerario_dia_id' => $diaDestino->id]);
            $this->reordenarDia($diaDestino);

            if ($diaOriginal->isNot($diaDestino)) {
                $this->reordenarDia($diaOriginal);
            }
        });
    }

    public function quitar(ItinerarioItem $item): void
    {
        DB::transaction(function () use ($item): void {
            $dia = $item->dia;
            $item->delete();
            $this->reordenarDia($dia);
        });
    }

    /** El orden dentro del día sigue a la hora de inicio. */
    private function reordenarDia(ItinerarioDia $dia): void
    {
        $dia->items()->reorder()->orderBy('hora_inicio')->get()
            ->each(fn (ItinerarioItem $item, int $indice) => $item->update(['orden' => $indice + 1]));
    }
}