<?php

namespace App\Modules\Rutas\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class CatalogoRutaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'descripcion' => Str::substr($this->descripcion, 0, 350),
            'destino' => $this->destino->nombre,
            'es_propuesta_tourismcloud' => $this->es_propuesta,
            'tipo' => $this->tipo_ruta,
            'duracion_horas' => $this->duracion_estimada_horas,
            'distancia_km' => $this->distancia_km,
            'transporte_recomendado' => $this->transporte_recomendado,
            'costo_pen' => $this->tieneCostoVerificado() ? (float) $this->costo_estimado : null,
            'tipo_precio' => $this->tipo_precio,
            'costo_fuente' => $this->precio_fuente_url,
            'costo_comprobado_en' => $this->precio_verificado_en?->toIso8601String(),
            'disponibilidad' => 'consultar_con_responsable',
            'paradas' => $this->puntos->map(fn ($punto) => [
                'orden' => $punto->orden,
                'nombre' => $punto->nombre_parada,
                'estadia_min' => $punto->tiempo_estadia_min,
                'traslado_min' => $punto->tiempo_traslado_min,
                'distancia_desde_anterior_km' => $punto->distancia_desde_anterior_km,
                'fuente' => $punto->atractivo?->fuente_url ?? $this->fuente_url,
            ])->values(),
            'fuente' => $this->fuente_url,
            'revisado_en' => $this->fecha_verificacion?->toIso8601String(),
            'consulta_oficial' => $this->url_reserva_oficial ?? $this->sitio_web_oficial ?? $this->fuente_url,
        ];
    }
}
