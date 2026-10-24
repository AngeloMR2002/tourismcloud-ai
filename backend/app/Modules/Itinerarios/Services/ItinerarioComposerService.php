<?php

namespace App\Modules\Itinerarios\Services;

use App\Modules\Atractivos\Models\Atractivo;
use App\Modules\Itinerarios\Models\Itinerario;
use App\Modules\Preferencias\Models\PreferenciaTurista;
use Illuminate\Support\Collection;

/**
 * Compone visitas de un itinerario con una regla determinista y explicable:
 * reparte atractivos activos del destino entre los días disponibles, respetando
 * el horario preferido y el presupuesto declarado, y priorizando las categorías
 * de interés del turista cuando existen. No es el optimizador de restricciones
 * con LLM previsto en el enunciado para la unidad siguiente; esa evaluación
 * inteligente se formula como trabajo posterior y no se presenta como terminada aquí.
 */
class ItinerarioComposerService
{
    private const HORA_INICIO_DEFECTO = '09:00';

    private const HORA_FIN_DEFECTO = '18:00';

    private const DURACION_DEFECTO_MIN = 90;

    private const SEPARACION_ENTRE_VISITAS_MIN = 30;

    public function componer(Itinerario $itinerario, ?PreferenciaTurista $preferencia): void
    {
        [$inicioJornada, $finJornada] = $this->jornada($preferencia);
        $candidatos = $this->candidatos($itinerario->destino_id, $preferencia);
        $presupuestoRestante = $itinerario->presupuesto_total !== null ? (float) $itinerario->presupuesto_total : null;
        $orden = 1;

        foreach ($itinerario->diasItinerario as $dia) {
            $minutoActual = $inicioJornada;

            while ($candidatos->isNotEmpty()) {
                $atractivo = $candidatos->first();
                $duracion = (int) ($atractivo->duracion_estimada_min ?: self::DURACION_DEFECTO_MIN);
                $costo = (float) $atractivo->costo_entrada;

                if ($minutoActual + $duracion > $finJornada) {
                    break;
                }

                if ($presupuestoRestante !== null && $costo > $presupuestoRestante) {
                    $candidatos->shift();

                    continue;
                }

                $dia->items()->create([
                    'entidad_tipo' => 'atractivo',
                    'entidad_id' => $atractivo->id,
                    'hora_inicio' => $this->horaDesdeMinutos($minutoActual),
                    'hora_fin' => $this->horaDesdeMinutos($minutoActual + $duracion),
                    'orden' => $orden++,
                    'costo_estimado' => $costo,
                ]);

                if ($presupuestoRestante !== null) {
                    $presupuestoRestante -= $costo;
                }

                $minutoActual += $duracion + self::SEPARACION_ENTRE_VISITAS_MIN;
                $candidatos->shift();
            }
        }
    }

    /**
     * @return array{0: int, 1: int}
     */
    private function jornada(?PreferenciaTurista $preferencia): array
    {
        $inicio = $this->minutosDesdeMedianoche($preferencia?->hora_inicio_preferida ?? self::HORA_INICIO_DEFECTO);
        $fin = $this->minutosDesdeMedianoche($preferencia?->hora_fin_preferida ?? self::HORA_FIN_DEFECTO);

        if ($fin <= $inicio) {
            return [$this->minutosDesdeMedianoche(self::HORA_INICIO_DEFECTO), $this->minutosDesdeMedianoche(self::HORA_FIN_DEFECTO)];
        }

        return [$inicio, $fin];
    }

    private function candidatos(int $destinoId, ?PreferenciaTurista $preferencia): Collection
    {
        $categoriaIds = $preferencia?->categorias->pluck('id')->all() ?? [];

        if ($categoriaIds !== []) {
            $filtrados = $this->baseQuery($destinoId)
                ->whereHas('categorias', fn ($query) => $query->whereIn('categorias_interes.id', $categoriaIds))
                ->get();

            if ($filtrados->isNotEmpty()) {
                return $filtrados;
            }
        }

        return $this->baseQuery($destinoId)->get();
    }

    private function baseQuery(int $destinoId)
    {
        return Atractivo::query()->activos()->deDestino($destinoId)->orderBy('costo_entrada');
    }

    private function minutosDesdeMedianoche(string $hora): int
    {
        [$horas, $minutos] = array_map('intval', explode(':', substr($hora, 0, 5)));

        return ($horas * 60) + $minutos;
    }

    private function horaDesdeMinutos(int $minutos): string
    {
        $horas = intdiv($minutos, 60) % 24;
        $resto = $minutos % 60;

        return sprintf('%02d:%02d:00', $horas, $resto);
    }
}
