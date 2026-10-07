<?php

namespace App\Modules\Actividades\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class CatalogoActividadResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'descripcion' => Str::substr($this->descripcion, 0, 350),
            'destino' => $this->destino->nombre,
            'categoria' => $this->categoria,
            'tipo' => $this->tipo_registro,
            'duracion_min' => $this->duracion_min,
            'precio_pen' => $this->tienePrecioVerificado() ? (float) $this->precio : null,
            'tipo_precio' => $this->tipo_precio,
            'precio_estado' => $this->tienePrecioVerificado() ? 'publicado_no_garantizado' : 'por_confirmar',
            'precio_fuente' => $this->precio_fuente_url,
            'precio_comprobado_en' => $this->precio_verificado_en?->toIso8601String(),
            'horario' => ['inicio' => $this->horario_inicio, 'fin' => $this->horario_fin, 'dias' => $this->dias_operacion],
            'capacidad_publicada' => $this->cupo_maximo,
            'disponibilidad' => 'consultar_con_responsable',
            'evento' => $this->tipo_registro === 'evento' ? ['inicio' => $this->evento_inicio?->toIso8601String(), 'fin' => $this->evento_fin?->toIso8601String()] : null,
            'ubicacion' => ['latitud' => $this->latitud, 'longitud' => $this->longitud],
            'fuente' => $this->fuente_url,
            'revisado_en' => $this->fecha_verificacion?->toIso8601String(),
            'consulta_oficial' => $this->url_reserva_oficial ?? $this->sitio_web_oficial ?? $this->fuente_url,
        ];
    }
}
