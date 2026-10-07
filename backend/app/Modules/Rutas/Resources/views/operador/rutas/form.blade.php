@php
    $registro = $ruta ?? null;
    $puntosIniciales = old('puntos', $registro?->puntos?->map(fn ($p) => $p->only(['atractivo_id', 'nombre_parada', 'descripcion_parada', 'tiempo_estadia_min', 'distancia_desde_anterior_km', 'tiempo_traslado_min', 'tipo_transporte_tramo', 'latitud', 'longitud']))->all() ?? [[], []]);
    $puntosIniciales = is_array($puntosIniciales) ? array_values($puntosIniciales) : [[], []];
@endphp
@if($errors->any())
    <ul role="alert" class="tc-alert-error block list-disc pl-8">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
@endif
<div class="grid sm:grid-cols-2 gap-4">
    <label class="block text-sm">Nombre *
        <input name="nombre" value="{{ old('nombre', $registro?->nombre) }}" required maxlength="150" class="tc-input mt-1">
    </label>
    <label class="block text-sm">Destino *
        <select name="destino_id" required class="tc-select mt-1">
            <option value="">Selecciona un destino</option>
            @foreach($destinos as $destino)<option value="{{ $destino->id }}" @selected((string) old('destino_id', $registro?->destino_id) === (string) $destino->id)>{{ $destino->nombre }}</option>@endforeach
        </select>
    </label>
    <label class="block text-sm">Tipo de ruta *
        <select name="tipo_ruta" class="tc-select mt-1">
            @foreach(['circuito' => 'Circuito', 'lineal' => 'Lineal', 'tematica' => 'Temática', 'senderismo' => 'Senderismo'] as $valor => $texto)
                <option value="{{ $valor }}" @selected(old('tipo_ruta', $registro?->tipo_ruta ?? 'lineal') === $valor)>{{ $texto }}</option>
            @endforeach
        </select>
    </label>
    <label class="block text-sm">Transporte recomendado *
        <select name="transporte_recomendado" required class="tc-select mt-1">
            <option value="">Seleccionar un transporte viable</option>
            @foreach(['a_pie' => 'A pie', 'bicicleta' => 'Bicicleta', 'automovil' => 'Automóvil', 'autobus' => 'Autobús', 'mixto' => 'Mixto'] as $valor => $texto)
                <option value="{{ $valor }}" @selected(old('transporte_recomendado', $registro?->transporte_recomendado) === $valor)>{{ $texto }}</option>
            @endforeach
        </select>
    </label>
    <label class="block text-sm">Duración documentada (horas)
        <input type="number" name="duracion_estimada_horas" value="{{ old('duracion_estimada_horas', $registro?->duracion_estimada_horas) }}" min="0.1" max="100" step="0.1" class="tc-input mt-1">
    </label>
    <label class="block text-sm">Distancia documentada (km)
        <input type="number" name="distancia_km" value="{{ old('distancia_km', $registro?->distancia_km) }}" min="0" step="0.01" class="tc-input mt-1">
    </label>
    <label class="block text-sm">Dificultad publicada
        <select name="nivel_dificultad" class="tc-select mt-1">
            <option value="">Por confirmar</option>
            @foreach(['facil' => 'Fácil', 'moderada' => 'Moderada', 'dificil' => 'Difícil', 'experto' => 'Experto'] as $valor => $texto)
                <option value="{{ $valor }}" @selected(old('nivel_dificultad', $registro?->nivel_dificultad) === $valor)>{{ $texto }}</option>
            @endforeach
        </select>
    </label>
    <label class="block text-sm">Temporada publicada
        <input name="temporada_recomendada" value="{{ old('temporada_recomendada', $registro?->temporada_recomendada) }}" maxlength="100" class="tc-input mt-1">
    </label>
</div>
<label class="block text-sm">Descripción *
    <textarea name="descripcion" rows="4" required maxlength="10000" class="tc-input mt-1">{{ old('descripcion', $registro?->descripcion) }}</textarea>
</label>
<label class="flex items-start gap-2 text-sm">
    <input type="checkbox" name="es_propuesta" value="1" @checked(old('es_propuesta', $registro?->es_propuesta ?? true)) class="mt-1">
    Es una propuesta de TourismCloud, no un paquete ofrecido por una empresa.
</label>
<label class="block text-sm">Recomendaciones documentadas
    <textarea name="recomendaciones" rows="3" maxlength="5000" class="tc-input mt-1">{{ old('recomendaciones', $registro?->recomendaciones) }}</textarea>
</label>
<section class="space-y-4">
    <div class="flex flex-wrap justify-between items-center gap-3">
        <h2 class="text-lg font-bold">Paradas del recorrido</h2>
        <button type="button" id="add-stop" class="tc-btn-outline">Añadir parada</button>
    </div>
    <p class="text-sm text-gray-600">Mínimo dos paradas. El traslado y la distancia corresponden al tramo desde la parada anterior. Deja vacías las medidas desconocidas; solo se suman cuando están completas.</p>
    <div id="route-stops" class="space-y-4"></div>
    <p id="route-order-notice" hidden role="status" class="text-sm text-amber-800">Cambió la secuencia. Se borraron las medidas de los tramos afectados y los totales anteriores: compruébalos para el nuevo recorrido.</p>
    <noscript><p class="tc-alert-error">Activa JavaScript para editar la secuencia de paradas.</p></noscript>
</section>
<template id="stop-template">
    <fieldset class="border rounded-xl p-4 space-y-3" data-stop>
        <legend class="font-semibold px-2" data-stop-title></legend>
        <div class="flex flex-wrap gap-2">
            <button type="button" data-action="up" class="tc-btn-outline" aria-label="Subir parada">↑ Subir</button>
            <button type="button" data-action="down" class="tc-btn-outline" aria-label="Bajar parada">↓ Bajar</button>
            <button type="button" data-action="remove" class="tc-btn-danger">Eliminar</button>
        </div>
        <div class="grid sm:grid-cols-2 gap-3">
            <label class="block text-sm">Atractivo aprobado (opcional)
                <select data-field="atractivo_id" class="tc-select mt-1">
                    <option value="">Parada con fuente propia</option>
                    @foreach($atractivos as $atractivo)
                        <option value="{{ $atractivo->id }}" data-destino="{{ $atractivo->destino_id }}" data-name="{{ $atractivo->nombre }}" data-lat="{{ $atractivo->latitud }}" data-lon="{{ $atractivo->longitud }}">{{ $atractivo->nombre }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block text-sm">Nombre de la parada *
                <input data-field="nombre_parada" required maxlength="150" class="tc-input mt-1">
            </label>
            @foreach(['tiempo_estadia_min' => 'Estadía (min)', 'tiempo_traslado_min' => 'Traslado desde la anterior (min)', 'distancia_desde_anterior_km' => 'Distancia desde la anterior (km)', 'latitud' => 'Latitud', 'longitud' => 'Longitud'] as $campo => $texto)
                <label class="block text-sm">{{ $texto }}
                    <input type="number" data-field="{{ $campo }}" step="{{ str_contains($campo, '_min') ? '1' : 'any' }}" class="tc-input mt-1">
                </label>
            @endforeach
            <label class="block text-sm">Transporte del tramo
                <select data-field="tipo_transporte_tramo" class="tc-select mt-1">
                    <option value="">Usar transporte de la ruta</option>
                    @foreach(['a_pie' => 'A pie', 'bicicleta' => 'Bicicleta', 'automovil' => 'Automóvil', 'autobus' => 'Autobús', 'mixto' => 'Mixto'] as $valor => $texto)<option value="{{ $valor }}">{{ $texto }}</option>@endforeach
                </select>
            </label>
        </div>
        <label class="block text-sm">Descripción de la parada
            <textarea data-field="descripcion_parada" rows="2" maxlength="2000" class="tc-input mt-1"></textarea>
        </label>
    </fieldset>
</template>
@include('actividades::operador.precio', ['campoPrecio' => 'costo_estimado'])
@include('actividades::operador.fuentes')
@push('scripts')
<script>
(() => {
    const stops = document.getElementById('route-stops');
    const template = document.getElementById('stop-template');
    const destination = document.querySelector('[name="destino_id"]');
    const clearTotals = () => {
        document.querySelector('[name="distancia_km"]').value = '';
        document.querySelector('[name="duracion_estimada_horas"]').value = '';
        document.getElementById('route-order-notice').hidden = false;
    };
    const clearLegs = () => {
        stops.querySelectorAll('[data-field="tiempo_traslado_min"], [data-field="distancia_desde_anterior_km"]').forEach(input => input.value = '');
        clearTotals();
    };
    const renumber = () => [...stops.children].forEach((stop, i) => {
        stop.querySelector('[data-stop-title]').textContent = 'Parada ' + (i + 1);
        stop.querySelectorAll('[data-field]').forEach(input => {
            input.name = 'puntos[' + i + '][' + input.dataset.field + ']';
        });
        stop.querySelector('[data-action="up"]').disabled = i === 0;
        stop.querySelector('[data-action="down"]').disabled = i === stops.children.length - 1;
    });
    const filterPlaces = () => stops.querySelectorAll('[data-field="atractivo_id"] option[data-destino]').forEach(option => {
        option.hidden = option.dataset.destino !== destination.value;
        option.disabled = option.hidden;
    });
    const addStop = (data = {}, initial = false) => {
        if (stops.children.length >= 30) return;
        const stop = template.content.firstElementChild.cloneNode(true);
        const validData = data && typeof data === 'object' ? data : {};
        stop.querySelectorAll('[data-field]').forEach(input => input.value = validData[input.dataset.field] ?? '');
        stop.addEventListener('click', event => {
            const button = event.target.closest('[data-action]');
            if (!button) return;
            if (button.dataset.action === 'remove') stop.remove();
            if (button.dataset.action === 'up' && stop.previousElementSibling) stops.insertBefore(stop, stop.previousElementSibling);
            if (button.dataset.action === 'down' && stop.nextElementSibling) stops.insertBefore(stop.nextElementSibling, stop);
            clearLegs();
            renumber();
        });
        stop.querySelector('[data-field="atractivo_id"]').addEventListener('change', event => {
            const option = event.target.selectedOptions[0];
            if (!option?.dataset.name) return;
            stop.querySelector('[data-field="nombre_parada"]').value = option.dataset.name;
            stop.querySelector('[data-field="latitud"]').value = option.dataset.lat;
            stop.querySelector('[data-field="longitud"]').value = option.dataset.lon;
        });
        stops.appendChild(stop);
        if (!initial) clearTotals();
        renumber();
        filterPlaces();
    };
    document.getElementById('add-stop').addEventListener('click', () => addStop());
    destination.addEventListener('change', filterPlaces);
    const initialStops = {{ Illuminate\Support\Js::from($puntosIniciales) }};
    initialStops.forEach(data => addStop(data, true));
})();
</script>
@endpush
