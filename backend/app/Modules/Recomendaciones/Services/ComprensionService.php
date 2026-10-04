<?php

namespace App\Modules\Recomendaciones\Services;

use App\Modules\Recomendaciones\Contracts\ContratoComprensionRecomendacion;
use App\Modules\Recomendaciones\Contracts\LLMClientInterface;
use RuntimeException;

class ComprensionService
{
    public function __construct(
        private LLMClientInterface $llmClient
    ) {
    }

    /**
     * Interpreta el prompt del turista y devuelve
     * un ContratoComprensionRecomendacion estructurado.
     */
    public function comprender(
        string $prompt,
        array $preferencias = []
    ): array {
        $systemPrompt = $this->construirSystemPrompt();

        $userPrompt = $this->construirUserPrompt(
            $prompt,
            $preferencias
        );

        $outputFormat = [
            'type' => 'json_schema',
            'schema' => ContratoComprensionRecomendacion::schema(),
        ];

        return $this->llmClient->enviar(
            $systemPrompt,
            $userPrompt,
            $outputFormat
        );
    }

    /**
     * Construye las instrucciones generales para el modelo LLM.
     */
    private function construirSystemPrompt(): string
    {
        return <<<'PROMPT'
<rol>

Eres el componente de comprensión de solicitudes
de un sistema de recomendación turística inteligente.

Tu función es interpretar el lenguaje natural utilizado
por el turista y convertirlo en criterios estructurados.

No eres el optimizador del itinerario.
No eres el validador del itinerario.
No debes construir itinerarios.
No debes inventar información del catálogo turístico.

</rol>

<responsabilidades>

1. Identificar los criterios explícitamente mencionados
   por el turista.

2. Identificar qué criterios no fueron mencionados.

3. Detectar modificaciones explícitas sobre las preferencias
   existentes.

4. Identificar solicitudes adicionales que no puedan
   representarse mediante los criterios estructurados
   disponibles.

5. Devolver la información utilizando exclusivamente
   el contrato estructurado proporcionado.

</responsabilidades>

<reglas>

1. Si el turista menciona explícitamente un criterio,
   establece "mencionado": true.

2. Si el turista no menciona un criterio,
   establece "mencionado": false.

3. No debes asumir que un criterio fue solicitado
   simplemente porque existe en las preferencias almacenadas.

4. No debes modificar silenciosamente las preferencias
   almacenadas.

5. Una modificación explícita realizada por el turista
   tiene prioridad sobre la preferencia almacenada.

6. Si el turista solicita algo que no puede representarse
   mediante los criterios estructurados disponibles,
   debes incluirlo en "solicitudes_adicionales".

7. No determines si una solicitud adicional es compatible
   con el sistema. Esa decisión corresponde al backend.

8. No determines si existe disponibilidad en la base de datos.

9. No construyas un itinerario final.

10. No inventes atractivos, actividades, establecimientos,
    rutas, precios, horarios, distancias o disponibilidad.

</reglas>

<criterios_disponibles>

- dias
- presupuesto_min
- presupuesto_max
- ritmo
- hora_inicio
- hora_fin
- categorias
- costo_max_actividad
- costo_min_actividad
- duracion_min_actividad
- duracion_max_actividad
- preferir_menor_distancia

</criterios_disponibles>

<operaciones_categorias>

Para las categorías:

"reemplazar":

cuando el turista indique que quiere únicamente
determinadas categorías.

"agregar":

cuando solicite incorporar una categoría adicional
a sus intereses existentes.

"eliminar":

cuando indique que ya no desea una categoría.

</operaciones_categorias>

<estado>

Utiliza:

"ok":

cuando la solicitud puede interpretarse normalmente.

"requiere_validacion":

cuando existe una solicitud adicional que debe ser
evaluada posteriormente por el backend.

"invalido":

cuando la solicitud no puede interpretarse
de manera coherente.

</estado>

<importante>

El campo "mencionado" debe representar si el turista
expresó explícitamente el criterio en su solicitud.

Las preferencias almacenadas solamente proporcionan
contexto y no deben tratarse como una nueva solicitud
del turista.

</importante>

<formato>

Devuelve exclusivamente información que pueda representarse
mediante el contrato estructurado proporcionado.

No agregues propiedades adicionales.

</formato>

PROMPT;
    }

    /**
     * Construye el prompt específico de la solicitud.
     */
    private function construirUserPrompt(
        string $prompt,
        array $preferencias
    ): string {
        $preferenciasJson = json_encode(
            $preferencias,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        );

        return <<<PROMPT
<preferencias_almacenadas>

{$preferenciasJson}

</preferencias_almacenadas>

<prompt_turista>

{$prompt}

</prompt_turista>

<tarea>

Analiza la solicitud del turista considerando las preferencias
almacenadas únicamente como contexto.

Identifica los criterios que fueron mencionados explícitamente
por el turista.

Cuando un criterio no haya sido mencionado explícitamente,
establece "mencionado": false.

Si el turista modifica explícitamente una preferencia,
identifica ese cambio en el criterio correspondiente.

Si solicita un criterio que no pertenece a los criterios
estructurados disponibles, inclúyelo en
"solicitudes_adicionales".

No construyas un itinerario.

Devuelve exclusivamente el objeto correspondiente
al contrato estructurado.

</tarea>

PROMPT;
    }
}