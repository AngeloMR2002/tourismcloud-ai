<?php

namespace App\Modules\Itinerarios\Http\Requests;

use App\Modules\Itinerarios\Models\Itinerario;
use App\Modules\Itinerarios\Models\ItinerarioDia;
use App\Modules\Itinerarios\Models\ItinerarioItem;
use App\Modules\Itinerarios\Services\ItinerarioItemService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class ItinerarioItemRequest extends FormRequest
{
    private float $costo = 0;

    /** Solo el turista dueño del itinerario; y el item debe pertenecer a ese itinerario. */
    public function authorize(): bool
    {
        $itinerario = $this->route('itinerario');
        $item = $this->route('item');

        if (! $itinerario instanceof Itinerario || $itinerario->turista_id !== $this->user()?->id) {
            return false;
        }

        return ! $item instanceof ItinerarioItem || $item->dia?->itinerario_id === $itinerario->id;
    }

    /** El select del detalle envía "tipo:id"; el formulario del catálogo envía los campos separados. */
    protected function prepareForValidation(): void
    {
        if (str_contains((string) $this->input('entidad'), ':')) {
            [$tipo, $id] = explode(':', (string) $this->input('entidad'), 2);
            $this->merge(['entidad_tipo' => $tipo, 'entidad_id' => $id]);
        }
    }

    public function rules(): array
    {
        $reglas = [
            'itinerario_dia_id' => ['bail', 'required', 'integer', Rule::exists('itinerario_dias', 'id')->where('itinerario_id', $this->route('itinerario')->id)],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fin' => ['required', 'date_format:H:i', 'after:hora_inicio'],
            'costo_estimado' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
        ];

        if ($this->isMethod('post')) {
            $reglas['entidad_tipo'] = ['required', Rule::in(['atractivo', 'actividad', 'establecimiento'])];
            $reglas['entidad_id'] = ['required', 'integer', 'min:1'];
        }

        return $reglas;
    }

    public function after(ItinerarioItemService $servicio): array
    {
        return [function (Validator $validator) use ($servicio): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $itinerario = $this->route('itinerario');
            $item = $this->route('item') instanceof ItinerarioItem ? $this->route('item') : null;

            if (! in_array($itinerario->estado, ['generado', 'guardado', 'activo'], true)) {
                $validator->errors()->add('itinerario', __('modules.itinerary_locked'));

                return;
            }

            $entidad = $item
                ? $servicio->buscarEntidad($item->entidad_tipo, $item->entidad_id, $itinerario->destino_id)
                : $servicio->buscarEntidad($this->input('entidad_tipo'), $this->integer('entidad_id'), $itinerario->destino_id);

            if ($entidad === null) {
                $validator->errors()->add('entidad', __('modules.visit_invalid'));

                return;
            }

            $dia = ItinerarioDia::findOrFail($this->integer('itinerario_dia_id'));
            $inicio = (string) $this->input('hora_inicio');
            $fin = (string) $this->input('hora_fin');

            if ($servicio->haySolapamiento($dia, $inicio, $fin, $item?->id)) {
                $validator->errors()->add('hora_inicio', __('modules.visit_overlap'));
            }

            if ($mensaje = $servicio->errorDeHorario($entidad, $dia, $inicio, $fin)) {
                $validator->errors()->add('hora_inicio', $mensaje);
            }

            $this->costo = $servicio->costoDe($entidad, $this->filled('costo_estimado') ? (float) $this->input('costo_estimado') : null);

            if ($servicio->excedePresupuesto($itinerario, $this->costo, $item)) {
                $validator->errors()->add('costo_estimado', __('modules.visit_over_budget'));
            }
        }];
    }

    /** @return array<string, mixed> */
    public function datosItem(): array
    {
        $datos = [
            'hora_inicio' => $this->input('hora_inicio').':00',
            'hora_fin' => $this->input('hora_fin').':00',
            'costo_estimado' => $this->costo,
        ];

        if ($this->isMethod('post')) {
            $datos['entidad_tipo'] = $this->input('entidad_tipo');
            $datos['entidad_id'] = $this->integer('entidad_id');
        }

        return $datos;
    }
}