@php($registro = $actividad ?? null)
@php
    $diasSeleccionados = old('dias_operacion', $registro?->dias_operacion ?? []);
    $diasSeleccionados = is_array($diasSeleccionados) ? $diasSeleccionados : [];
@endphp
@if($errors->any())
    <div class="tc-alert-error" role="alert"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif
<div class="grid sm:grid-cols-2 gap-4">
    <label class="block text-sm sm:col-span-2">Nombre de la actividad *
        <input name="nombre" value="{{ old('nombre', $registro?->nombre) }}" required maxlength="150" class="tc-input mt-1">
    </label>
    <label class="block text-sm">Destino *
        <select name="destino_id" required class="tc-select mt-1">
            <option value="">Seleccionar</option>
            @foreach($destinos as $destino)
                <option value="{{ $destino->id }}" @selected(old('destino_id', $registro?->destino_id ?? request('destino_id')) == $destino->id)>{{ $destino->nombre }}</option>
            @endforeach
        </select>
    </label>
    <label class="block text-sm">Atractivo aprobado
        <select name="atractivo_id" class="tc-select mt-1">
            <option value="">Sin atractivo vinculado</option>
            @foreach($atractivos as $atractivo)
                <option value="{{ $atractivo->id }}" @selected(old('atractivo_id', $registro?->atractivo_id ?? request('atractivo_id')) == $atractivo->id)>{{ $atractivo->nombre }}</option>
            @endforeach
        </select>
    </label>
    <label class="block text-sm">Categoría *
        <select name="categoria" required class="tc-select mt-1">
            <option value="">Seleccionar según la fuente</option>
            @foreach(['cultural' => 'Cultural', 'ecoturismo' => 'Ecoturismo', 'aventura' => 'Aventura', 'gastronomica' => 'Gastronómica', 'relax' => 'Relax', 'deportiva' => 'Deportiva'] as $valor => $texto)
                <option value="{{ $valor }}" @selected(old('categoria', $registro?->categoria) === $valor)>{{ $texto }}</option>
            @endforeach
        </select>
    </label>
    <label class="block text-sm">Tipo de actividad *
        <select name="tipo_registro" required class="tc-select mt-1">
            @foreach(['visita' => 'Visita al lugar', 'tour' => 'Tour de un proveedor', 'evento' => 'Evento con fecha'] as $valor => $texto)
                <option value="{{ $valor }}" @selected(old('tipo_registro', $registro?->tipo_registro ?? 'visita') === $valor)>{{ $texto }}</option>
            @endforeach
        </select>
    </label>
    <label class="block text-sm sm:col-span-2">Descripción respaldada por la fuente *
        <textarea name="descripcion" rows="4" required class="tc-input mt-1">{{ old('descripcion', $registro?->descripcion) }}</textarea>
    </label>
    <label class="block text-sm">Duración estimada en minutos
        <input type="number" name="duracion_min" value="{{ old('duracion_min', $registro?->duracion_min) }}" min="1" max="1440" class="tc-input mt-1">
    </label>
    <label class="block text-sm">Dificultad
        <select name="nivel_dificultad" class="tc-select mt-1">
            <option value="">Por confirmar</option>
            @foreach(['baja', 'media', 'alta'] as $valor)
                <option value="{{ $valor }}" @selected(old('nivel_dificultad', $registro?->nivel_dificultad) === $valor)>{{ ucfirst($valor) }}</option>
            @endforeach
        </select>
    </label>
    <label class="block text-sm">Cupo comunicado por el responsable
        <input type="number" name="cupo_maximo" value="{{ old('cupo_maximo', $registro?->cupo_maximo) }}" min="1" max="500" class="tc-input mt-1">
    </label>
    @foreach(['horario_inicio' => 'Horario de inicio publicado', 'horario_fin' => 'Horario de cierre publicado'] as $campo => $texto)
        <label class="block text-sm">{{ $texto }}
            <input type="time" name="{{ $campo }}" value="{{ old($campo, $registro?->$campo ? substr($registro->$campo, 0, 5) : '') }}" class="tc-input mt-1">
        </label>
    @endforeach
    <div class="sm:col-span-2">
        <span class="text-sm">Días publicados por el responsable</span>
        <div class="flex flex-wrap gap-3 mt-2">
            @foreach(['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'] as $dia)
                <label class="text-sm"><input type="checkbox" name="dias_operacion[]" value="{{ $dia }}" @checked(in_array($dia, $diasSeleccionados, true))> {{ ucfirst($dia) }}</label>
            @endforeach
        </div>
    </div>
    @foreach(['evento_inicio' => 'Inicio del evento (si corresponde)', 'evento_fin' => 'Fin del evento (si corresponde)'] as $campo => $texto)
        <label class="block text-sm">{{ $texto }}
            <input type="datetime-local" name="{{ $campo }}" value="{{ old($campo, $registro?->$campo?->format('Y-m-d\TH:i')) }}" class="tc-input mt-1">
        </label>
    @endforeach
    @foreach(['punto_encuentro' => 'Punto de encuentro confirmado', 'incluye' => 'Servicios incluidos confirmados', 'no_incluye' => 'Servicios excluidos confirmados', 'requisitos' => 'Requisitos publicados'] as $campo => $texto)
        <label class="block text-sm">{{ $texto }}
            <textarea name="{{ $campo }}" rows="2" class="tc-input mt-1">{{ old($campo, $registro?->$campo) }}</textarea>
        </label>
    @endforeach
    @foreach(['latitud', 'longitud'] as $campo)
        <label class="block text-sm">{{ ucfirst($campo) }}
            <input type="number" step="any" name="{{ $campo }}" value="{{ old($campo, $registro?->$campo) }}" class="tc-input mt-1">
        </label>
    @endforeach
</div>
@include('actividades::operador.precio', ['campoPrecio' => 'precio'])
@include('actividades::operador.fuentes')
