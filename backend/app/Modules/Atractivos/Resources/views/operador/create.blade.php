@extends('layouts.app')

@section('title', 'Nuevo Atractivo')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- ── Header ───────────────────────────────────────────────────────────── --}}
    <div class="mb-8">
        <a href="{{ route('operador.atractivos.index') }}"
           class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-gray-600 mb-3 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Mis Atractivos
        </a>

        <h1 style="font-family: var(--font-display); font-size: 1.75rem; font-weight: 800; color: #1a2232;">
            Nuevo Atractivo
        </h1>
    </div>

    {{-- ── Errores de validación ────────────────────────────────────────────── --}}
    @if($errors->any())
        <div class="tc-alert-error mb-6">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>

            <div>
                <p class="font-semibold">Por favor corrige los siguientes errores:</p>

                <ul class="mt-1 list-disc list-inside text-sm space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form method="POST"
          action="{{ route('operador.atractivos.store') }}"
          enctype="multipart/form-data"
          class="space-y-6">

        @csrf

        {{-- Provisional: ID del operador para pruebas locales --}}
        <input type="hidden"
               name="_operador_id_test"
               value="{{ request('_operador_id_test', 1) }}">

        {{-- ── Sección: Información básica ─────────────────────────────────── --}}
        <div class="tc-card p-6">

            <h2 class="text-sm font-bold uppercase tracking-wide mb-5"
                style="color: var(--color-primary-700);">
                Información básica
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                {{-- Destino --}}
                <div class="sm:col-span-2">
                    <label for="destino_id"
                           class="block text-sm font-semibold mb-1.5 text-gray-700">
                        Destino <span class="text-red-500">*</span>
                    </label>

                    <select id="destino_id"
                            name="destino_id"
                            class="tc-select"
                            required>

                        <option value="">Selecciona un destino…</option>

                        @foreach($destinos as $d)
                            <option value="{{ $d->id }}"
                                    @selected(old('destino_id') == $d->id)>
                                {{ $d->nombre }}
                            </option>
                        @endforeach

                    </select>

                    @error('destino_id')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nombre --}}
                <div class="sm:col-span-2">
                    <label for="nombre"
                           class="block text-sm font-semibold mb-1.5 text-gray-700">
                        Nombre <span class="text-red-500">*</span>
                    </label>

                    <input type="text"
                           id="nombre"
                           name="nombre"
                           value="{{ old('nombre') }}"
                           placeholder="Ej: Machu Picchu"
                           class="tc-input"
                           maxlength="150"
                           required>

                    @error('nombre')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Descripción --}}
                <div class="sm:col-span-2">
                    <label for="descripcion"
                           class="block text-sm font-semibold mb-1.5 text-gray-700">
                        Descripción
                    </label>

                    <textarea id="descripcion"
                              name="descripcion"
                              rows="4"
                              placeholder="Describe el atractivo…"
                              class="tc-input resize-none">{{ old('descripcion') }}</textarea>

                    @error('descripcion')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Costo de entrada --}}
                <div>
                    <label for="costo_entrada"
                           class="block text-sm font-semibold mb-1.5 text-gray-700">
                        Costo de entrada (S/) <span class="text-red-500">*</span>
                    </label>

                    <input type="number"
                           id="costo_entrada"
                           name="costo_entrada"
                           value="{{ old('costo_entrada', 0) }}"
                           min="0"
                           step="0.01"
                           class="tc-input"
                           required>

                    <p class="text-xs text-gray-400 mt-1">
                        Usa 0 si es gratuito.
                    </p>

                    @error('costo_entrada')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Duración estimada --}}
                <div>
                    <label for="duracion_estimada_min"
                           class="block text-sm font-semibold mb-1.5 text-gray-700">
                        Duración estimada (minutos)
                    </label>

                    <input type="number"
                           id="duracion_estimada_min"
                           name="duracion_estimada_min"
                           value="{{ old('duracion_estimada_min') }}"
                           min="1"
                           max="10080"
                           placeholder="Ej: 240"
                           class="tc-input">

                    @error('duracion_estimada_min')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Estado --}}
                <div>
                    <label for="estado"
                           class="block text-sm font-semibold mb-1.5 text-gray-700">
                        Estado <span class="text-red-500">*</span>
                    </label>

                    <select id="estado"
                            name="estado"
                            class="tc-select"
                            required>

                        <option value="activo"
                                @selected(old('estado', 'activo') === 'activo')}>
                            ✓ Activo
                        </option>

                        <option value="inactivo"
                                @selected(old('estado') === 'inactivo')}>
                            ⏸ Inactivo
                        </option>

                    </select>

                    @error('estado')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Coordenadas --}}
                <div>
                    <label for="latitud"
                           class="block text-sm font-semibold mb-1.5 text-gray-700">
                        Latitud
                    </label>

                    <input type="number"
                           id="latitud"
                           name="latitud"
                           value="{{ old('latitud') }}"
                           step="0.0000001"
                           min="-90"
                           max="90"
                           placeholder="-13.5319981"
                           class="tc-input">

                    @error('latitud')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="longitud"
                           class="block text-sm font-semibold mb-1.5 text-gray-700">
                        Longitud
                    </label>

                    <input type="number"
                           id="longitud"
                           name="longitud"
                           value="{{ old('longitud') }}"
                           step="0.0000001"
                           min="-180"
                           max="180"
                           placeholder="-71.9674626"
                           class="tc-input">

                    @error('longitud')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

            </div>
        </div>

        {{-- ── Sección: Categorías ──────────────────────────────────────────── --}}
        <div class="tc-card p-6">

            <h2 class="text-sm font-bold uppercase tracking-wide mb-5"
                style="color: var(--color-primary-700);">
                Categorías de interés
            </h2>

            @if($categorias->isEmpty())

                <p class="text-sm text-gray-400">
                    No hay categorías disponibles aún.
                </p>

            @else

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">

                    @foreach($categorias as $cat)

                        <label class="flex items-center gap-2.5 cursor-pointer group">

                            <input type="checkbox"
                                   name="categorias[]"
                                   value="{{ $cat->id }}"
                                   {{ in_array($cat->id, old('categorias', [])) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded"
                                   style="accent-color: var(--color-primary-500);">

                            <span class="text-sm text-gray-700 group-hover:text-gray-900">
                                {{ $cat->nombre }}
                            </span>

                        </label>

                    @endforeach

                </div>

            @endif

            @error('categorias')
                <p class="mt-2 text-xs text-red-500">{{ $message }}</p>
            @enderror

        </div>

        {{-- ── Sección: Horarios semanales ─────────────────────────────────── --}}
        <div class="tc-card p-6" id="horario-editor">

            <h2 class="text-sm font-bold uppercase tracking-wide mb-2"
                style="color: var(--color-primary-700);">
                Horarios de apertura
            </h2>

            <p class="text-xs text-gray-400 mb-5">
                Activa el día para indicar que está abierto y define su horario.
                Deja desactivado si permanece cerrado.
            </p>

            @php
                $diasConfig = [
                    'lunes'     => 'Lunes',
                    'martes'    => 'Martes',
                    'miercoles' => 'Miércoles',
                    'jueves'    => 'Jueves',
                    'viernes'   => 'Viernes',
                    'sabado'    => 'Sábado',
                    'domingo'   => 'Domingo',
                ];

                /*
                 * El nuevo formato de horarios es:
                 *
                 * [
                 *     [
                 *         'dia' => 'lunes',
                 *         'hora_inicio' => '06:00',
                 *         'hora_fin' => '17:30'
                 *     ],
                 *     ...
                 * ]
                 *
                 * Para facilitar la edición del formulario, convertimos
                 * temporalmente el array recibido por old() a un mapa por día.
                 */
                $horariosOld = old('horarios', []);

                $horariosPorDia = [];

                if (is_array($horariosOld)) {
                    foreach ($horariosOld as $horario) {
                        if (
                            is_array($horario) &&
                            !empty($horario['dia'])
                        ) {
                            $horariosPorDia[$horario['dia']] = $horario;
                        }
                    }
                }
            @endphp

            <div class="space-y-3">

                @foreach($diasConfig as $clave => $nombre)

                    @php
                        $horarioDia = $horariosPorDia[$clave] ?? null;

                        $abierto = is_array($horarioDia);

                        $horaInicio = $horarioDia['hora_inicio'] ?? '09:00';
                        $horaFin    = $horarioDia['hora_fin'] ?? '18:00';

                        /*
                         * El índice que utilizará el formulario para este día.
                         * Se basa en el orden de $diasConfig.
                         */
                        $indiceHorario = array_search($clave, array_keys($diasConfig), true);
                    @endphp

                    <div class="flex items-center gap-4 py-3 border-b border-gray-100 last:border-0">

                        {{-- Toggle abierto/cerrado --}}
                        <label class="tc-toggle shrink-0"
                               title="{{ $abierto ? 'Cerrar' : 'Abrir' }} {{ $nombre }}">

                            <input type="checkbox"
                                   id="toggle-{{ $clave }}"
                                   data-dia="{{ $clave }}"
                                   data-index="{{ $indiceHorario }}"
                                   {{ $abierto ? 'checked' : '' }}
                                   onchange="toggleDia('{{ $clave }}', {{ $indiceHorario }}, this.checked)">

                            <span class="tc-toggle-track"></span>
                            <span class="tc-toggle-thumb"></span>

                        </label>

                        {{-- Nombre del día --}}
                        <span class="w-24 text-sm font-semibold text-gray-700 shrink-0">
                            {{ $nombre }}
                        </span>

                        {{-- Inputs de horario --}}
                        <div id="horario-inputs-{{ $clave }}"
                             class="flex items-center gap-3 flex-1 {{ $abierto ? '' : 'hidden' }}">

                            {{-- Día --}}
                            <input type="hidden"
                                   id="dia-{{ $clave }}"
                                   name="{{ $abierto ? "horarios[{$indiceHorario}][dia]" : '' }}"
                                   value="{{ $clave }}">

                            <div class="flex items-center gap-2">

                                <label for="hora-inicio-{{ $clave }}"
                                       class="text-xs text-gray-400 shrink-0">
                                    Desde
                                </label>

                                <input type="time"
                                       id="hora-inicio-{{ $clave }}"
                                       name="{{ $abierto ? "horarios[{$indiceHorario}][hora_inicio]" : '' }}"
                                       value="{{ $horaInicio }}"
                                       class="tc-input w-32 text-sm">

                            </div>

                            <span class="text-gray-300">→</span>

                            <div class="flex items-center gap-2">

                                <label for="hora-fin-{{ $clave }}"
                                       class="text-xs text-gray-400 shrink-0">
                                    Hasta
                                </label>

                                <input type="time"
                                       id="hora-fin-{{ $clave }}"
                                       name="{{ $abierto ? "horarios[{$indiceHorario}][hora_fin]" : '' }}"
                                       value="{{ $horaFin }}"
                                       class="tc-input w-32 text-sm">

                            </div>

                        </div>

                        {{-- Etiqueta "Cerrado" --}}
                        <div id="horario-closed-{{ $clave }}"
                             class="text-xs text-gray-400 {{ $abierto ? 'hidden' : '' }}">
                            Cerrado
                        </div>

                        @error("horarios.{$indiceHorario}.dia")
                            <p class="text-xs text-red-500">
                                {{ $message }}
                            </p>
                        @enderror

                        @error("horarios.{$indiceHorario}.hora_inicio")
                            <p class="text-xs text-red-500">
                                {{ $message }}
                            </p>
                        @enderror

                        @error("horarios.{$indiceHorario}.hora_fin")
                            <p class="text-xs text-red-500">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                @endforeach

            </div>

        </div>

        {{-- ── Sección: Imágenes ────────────────────────────────────────────── --}}
        <div class="tc-card p-6">

            <h2 class="text-sm font-bold uppercase tracking-wide mb-5"
                style="color: var(--color-primary-700);">
                Imágenes
            </h2>

            <div class="space-y-4">

                {{-- Imagen de portada --}}
                <div>

                    <label for="imagen_portada"
                           class="block text-sm font-semibold mb-1.5 text-gray-700">
                        Imagen de portada
                    </label>

                    <input type="file"
                           id="imagen_portada"
                           name="imagen_portada"
                           accept="image/jpg,image/jpeg,image/png,image/webp"
                           class="tc-input text-sm file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700">

                    <p class="text-xs text-gray-400 mt-1">
                        JPG, PNG o WebP. Máx 4 MB.
                    </p>

                    @error('imagen_portada')
                        <p class="mt-1 text-xs text-red-500">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

                {{-- Galería --}}
                <div>

                    <label for="galeria"
                           class="block text-sm font-semibold mb-1.5 text-gray-700">
                        Galería de imágenes adicional
                    </label>

                    <input type="file"
                           id="galeria"
                           name="galeria[]"
                           accept="image/jpg,image/jpeg,image/png,image/webp"
                           multiple
                           class="tc-input text-sm file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700">

                    <p class="text-xs text-gray-400 mt-1">
                        Hasta 10 imágenes. JPG, PNG o WebP. Máx 4 MB c/u.
                    </p>

                    @error('galeria')
                        <p class="mt-1 text-xs text-red-500">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

            </div>

        </div>

        {{-- ── Botones de acción ────────────────────────────────────────────── --}}
        <div class="flex items-center justify-end gap-3">

            <a href="{{ route('operador.atractivos.index') }}"
               class="tc-btn-outline">
                Cancelar
            </a>

            <button type="submit"
                    class="tc-btn-primary">

                <svg class="w-4 h-4"
                     fill="none"
                     stroke="currentColor"
                     viewBox="0 0 24 24">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M5 13l4 4L19 7"/>

                </svg>

                Crear Atractivo

            </button>

        </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
/**
 * Editor semanal de horarios.
 *
 * El formulario utiliza ahora la estructura:
 *
 * horarios[0][dia]
 * horarios[0][hora_inicio]
 * horarios[0][hora_fin]
 *
 * horarios[1][dia]
 * horarios[1][hora_inicio]
 * horarios[1][hora_fin]
 *
 * etc.
 *
 * Los días desactivados no envían ningún elemento al servidor.
 */
function toggleDia(dia, indice, abierto) {

    const inputsDiv = document.getElementById('horario-inputs-' + dia);
    const closedDiv = document.getElementById('horario-closed-' + dia);

    const inputDia = document.getElementById('dia-' + dia);
    const inputInicio = document.getElementById('hora-inicio-' + dia);
    const inputFin = document.getElementById('hora-fin-' + dia);

    if (abierto) {

        inputsDiv.classList.remove('hidden');
        closedDiv.classList.add('hidden');

        inputDia.name =
            'horarios[' + indice + '][dia]';

        inputInicio.name =
            'horarios[' + indice + '][hora_inicio]';

        inputFin.name =
            'horarios[' + indice + '][hora_fin]';

    } else {

        inputsDiv.classList.add('hidden');
        closedDiv.classList.remove('hidden');

        /*
         * Quitamos los atributos name para que Laravel
         * no reciba el día cerrado.
         */
        inputDia.removeAttribute('name');
        inputInicio.removeAttribute('name');
        inputFin.removeAttribute('name');
    }
}


/**
 * Inicializa el estado de los días al cargar la página.
 */
document.addEventListener('DOMContentLoaded', function () {

    const dias = [
        'lunes',
        'martes',
        'miercoles',
        'jueves',
        'viernes',
        'sabado',
        'domingo'
    ];

    dias.forEach(function (dia) {

        const toggle = document.getElementById('toggle-' + dia);

        if (!toggle) {
            return;
        }

        const indice = toggle.dataset.index;

        toggleDia(
            dia,
            indice,
            toggle.checked
        );
    });


    // Preview de galería
    const galeriaInput = document.querySelector(
        'input[name="galeria[]"]'
    );

    if (galeriaInput) {

        galeriaInput.addEventListener('change', function (e) {

            let previewContainer =
                document.getElementById('preview-galeria');

            if (!previewContainer) {

                previewContainer =
                    document.createElement('div');

                previewContainer.id =
                    'preview-galeria';

                previewContainer.className =
                    'mt-3 flex gap-2 overflow-x-auto pb-2 scrollbar-hide';

                this.parentNode.appendChild(
                    previewContainer
                );
            }

            previewContainer.innerHTML = '';

            Array.from(e.target.files)
                .slice(0, 10)
                .forEach(function (file) {

                    if (!file.type.match('image.*')) {
                        return;
                    }

                    const reader =
                        new FileReader();

                    reader.onload = function (e) {

                        const imgWrap =
                            document.createElement('div');

                        imgWrap.className =
                            'shrink-0 w-24 h-24 rounded-lg overflow-hidden border border-gray-200';

                        imgWrap.style.boxShadow =
                            '0 2px 4px rgba(0,0,0,0.05)';

                        const img =
                            document.createElement('img');

                        img.src =
                            e.target.result;

                        img.className =
                            'w-full h-full object-cover';

                        imgWrap.appendChild(img);

                        previewContainer.appendChild(
                            imgWrap
                        );
                    };

                    reader.readAsDataURL(file);
                });
        });
    }

});


// Preview de portada
function previewPortada(input) {

    if (input.files && input.files[0]) {

        const reader =
            new FileReader();

        reader.onload = function (e) {

            const previewArea =
                document.getElementById(
                    'preview-portada-area'
                );

            if (previewArea) {

                previewArea.innerHTML =
                    '<img src="' +
                    e.target.result +
                    '" class="w-full h-full object-cover" alt="Portada">';
            }
        };

        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
