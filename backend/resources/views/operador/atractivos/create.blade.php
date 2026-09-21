@extends('layouts.app')

@section('title', 'Nuevo Atractivo')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- â”€â”€ Header â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
    <div class="mb-8">
        <a href="{{ route('operador.atractivos.index') }}"
           class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-gray-600 mb-3 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Mis Atractivos
        </a>
        <h1 style="font-family: var(--font-display); font-size: 1.75rem; font-weight: 800; color: #1a2232;">
            Nuevo Atractivo
        </h1>
    </div>

    {{-- â”€â”€ Errores de validaciÃ³n â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
    @if($errors->any())
        <div class="tc-alert-error mb-6">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
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
        <input type="hidden" name="_operador_id_test" value="{{ request('_operador_id_test', 1) }}">

        {{-- â”€â”€ SecciÃ³n: InformaciÃ³n bÃ¡sica â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
        <div class="tc-card p-6">
            <h2 class="text-sm font-bold uppercase tracking-wide mb-5" style="color: var(--color-primary-700);">
                InformaciÃ³n bÃ¡sica
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                {{-- Destino --}}
                <div class="sm:col-span-2">
                    <label for="destino_id" class="block text-sm font-semibold mb-1.5 text-gray-700">
                        Destino <span class="text-red-500">*</span>
                    </label>
                    <select id="destino_id" name="destino_id" class="tc-select" required>
                        <option value="">Selecciona un destinoâ€¦</option>
                        @foreach($destinos as $d)
                            <option value="{{ $d->id }}" @selected(old('destino_id') == $d->id)>
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
                    <label for="nombre" class="block text-sm font-semibold mb-1.5 text-gray-700">
                        Nombre <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="nombre" name="nombre"
                           value="{{ old('nombre') }}"
                           placeholder="Ej: Machu Picchu"
                           class="tc-input" maxlength="150" required>
                    @error('nombre')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- DescripciÃ³n --}}
                <div class="sm:col-span-2">
                    <label for="descripcion" class="block text-sm font-semibold mb-1.5 text-gray-700">
                        DescripciÃ³n
                    </label>
                    <textarea id="descripcion" name="descripcion" rows="4"
                              placeholder="Describe el atractivoâ€¦"
                              class="tc-input resize-none">{{ old('descripcion') }}</textarea>
                    @error('descripcion')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Costo de entrada --}}
                <div>
                    <label for="costo_entrada" class="block text-sm font-semibold mb-1.5 text-gray-700">
                        Costo de entrada (S/) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="costo_entrada" name="costo_entrada"
                           value="{{ old('costo_entrada', 0) }}"
                           min="0" step="0.01"
                           class="tc-input" required>
                    <p class="text-xs text-gray-400 mt-1">Usa 0 si es gratuito.</p>
                    @error('costo_entrada')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- DuraciÃ³n estimada --}}
                <div>
                    <label for="duracion_estimada_min" class="block text-sm font-semibold mb-1.5 text-gray-700">
                        DuraciÃ³n estimada (minutos)
                    </label>
                    <input type="number" id="duracion_estimada_min" name="duracion_estimada_min"
                           value="{{ old('duracion_estimada_min') }}"
                           min="1" max="10080"
                           placeholder="Ej: 240"
                           class="tc-input">
                    @error('duracion_estimada_min')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Estado --}}
                <div>
                    <label for="estado" class="block text-sm font-semibold mb-1.5 text-gray-700">
                        Estado <span class="text-red-500">*</span>
                    </label>
                    <select id="estado" name="estado" class="tc-select" required>
                        <option value="activo"   @selected(old('estado', 'activo') === 'activo')>âœ… Activo</option>
                        <option value="inactivo" @selected(old('estado') === 'inactivo')>â¸ Inactivo</option>
                    </select>
                    @error('estado')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Coordenadas --}}
                <div>
                    <label for="latitud" class="block text-sm font-semibold mb-1.5 text-gray-700">Latitud</label>
                    <input type="number" id="latitud" name="latitud"
                           value="{{ old('latitud') }}"
                           step="0.0000001" min="-90" max="90"
                           placeholder="-13.5319981"
                           class="tc-input">
                    @error('latitud') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="longitud" class="block text-sm font-semibold mb-1.5 text-gray-700">Longitud</label>
                    <input type="number" id="longitud" name="longitud"
                           value="{{ old('longitud') }}"
                           step="0.0000001" min="-180" max="180"
                           placeholder="-71.9674626"
                           class="tc-input">
                    @error('longitud') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- â”€â”€ SecciÃ³n: CategorÃ­as â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
        <div class="tc-card p-6">
            <h2 class="text-sm font-bold uppercase tracking-wide mb-5" style="color: var(--color-primary-700);">
                CategorÃ­as de interÃ©s
            </h2>
            @if($categorias->isEmpty())
                <p class="text-sm text-gray-400">No hay categorÃ­as disponibles aÃºn.</p>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach($categorias as $cat)
                        <label class="flex items-center gap-2.5 cursor-pointer group">
                            <input type="checkbox" name="categorias[]" value="{{ $cat->id }}"
                                   {{ in_array($cat->id, old('categorias', [])) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded"
                                   style="accent-color: var(--color-primary-500);">
                            <span class="text-sm text-gray-700 group-hover:text-gray-900">{{ $cat->nombre }}</span>
                        </label>
                    @endforeach
                </div>
            @endif
            @error('categorias') <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror
        </div>

        {{-- â”€â”€ SecciÃ³n: Horarios semanales â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
        <div class="tc-card p-6" id="horario-editor">
            <h2 class="text-sm font-bold uppercase tracking-wide mb-2" style="color: var(--color-primary-700);">
                Horarios de apertura
            </h2>
            <p class="text-xs text-gray-400 mb-5">
                Activa el toggle del dÃ­a para indicar que estÃ¡ abierto y luego define el horario.
                Deja desactivado si ese dÃ­a permanece cerrado.
            </p>

            @php
                $diasConfig = [
                    'lunes'     => 'Lunes',
                    'martes'    => 'Martes',
                    'miercoles' => 'MiÃ©rcoles',
                    'jueves'    => 'Jueves',
                    'viernes'   => 'Viernes',
                    'sabado'    => 'SÃ¡bado',
                    'domingo'   => 'Domingo',
                ];
                $horariosOld = old('horarios', []);
            @endphp

            <div class="space-y-3">
                @foreach($diasConfig as $clave => $nombre)
                    @php
                        $horarioDia = $horariosOld[$clave] ?? null;
                        $abierto    = is_array($horarioDia) && isset($horarioDia['abre']);
                        $abre       = $horarioDia['abre'] ?? '09:00';
                        $cierra     = $horarioDia['cierra'] ?? '18:00';
                    @endphp
                    <div class="flex items-center gap-4 py-3 border-b border-gray-100 last:border-0">

                        {{-- Toggle abierto/cerrado --}}
                        <label class="tc-toggle shrink-0" title="{{ $abierto ? 'Cerrar' : 'Abrir' }} {{ $nombre }}">
                            <input type="checkbox"
                                   id="toggle-{{ $clave }}"
                                   data-dia="{{ $clave }}"
                                   {{ $abierto ? 'checked' : '' }}
                                   onchange="toggleDia('{{ $clave }}', this.checked)">
                            <span class="tc-toggle-track"></span>
                            <span class="tc-toggle-thumb"></span>
                        </label>

                        {{-- Nombre del dÃ­a --}}
                        <span class="w-24 text-sm font-semibold text-gray-700 shrink-0">{{ $nombre }}</span>

                        {{-- Inputs de hora (visibles solo si abierto) --}}
                        <div id="horario-inputs-{{ $clave }}"
                             class="flex items-center gap-3 flex-1 {{ $abierto ? '' : 'hidden' }}">
                            <div class="flex items-center gap-2">
                                <label class="text-xs text-gray-400 shrink-0">Abre</label>
                                <input type="time" id="abre-{{ $clave }}"
                                       name="horarios[{{ $clave }}][abre]"
                                       value="{{ $abre }}"
                                       class="tc-input w-32 text-sm">
                            </div>
                            <span class="text-gray-300">â†’</span>
                            <div class="flex items-center gap-2">
                                <label class="text-xs text-gray-400 shrink-0">Cierra</label>
                                <input type="time" id="cierra-{{ $clave }}"
                                       name="horarios[{{ $clave }}][cierra]"
                                       value="{{ $cierra }}"
                                       class="tc-input w-32 text-sm">
                            </div>
                        </div>

                        {{-- Etiqueta "Cerrado" cuando el dÃ­a estÃ¡ desactivado --}}
                        <div id="horario-closed-{{ $clave }}"
                             class="text-xs text-gray-400 {{ $abierto ? 'hidden' : '' }}">
                            Cerrado
                        </div>

                        @error("horarios.{$clave}")
                            <p class="text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach
            </div>
        </div>

        {{-- â”€â”€ SecciÃ³n: ImÃ¡genes â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
        <div class="tc-card p-6">
            <h2 class="text-sm font-bold uppercase tracking-wide mb-5" style="color: var(--color-primary-700);">
                ImÃ¡genes
            </h2>
            <div class="space-y-4">

                {{-- Imagen de portada --}}
                <div>
                    <label for="imagen_portada" class="block text-sm font-semibold mb-1.5 text-gray-700">
                        Imagen de portada
                    </label>
                    <input type="file" id="imagen_portada" name="imagen_portada"
                           accept="image/jpg,image/jpeg,image/png,image/webp"
                           class="tc-input text-sm file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700">
                    <p class="text-xs text-gray-400 mt-1">JPG, PNG o WebP. MÃ¡x 4 MB.</p>
                    @error('imagen_portada') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- GalerÃ­a adicional --}}
                <div>
                    <label for="galeria" class="block text-sm font-semibold mb-1.5 text-gray-700">
                        GalerÃ­a de imÃ¡genes adicional
                    </label>
                    <input type="file" id="galeria" name="galeria[]"
                           accept="image/jpg,image/jpeg,image/png,image/webp"
                           multiple
                           class="tc-input text-sm file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700">
                    <p class="text-xs text-gray-400 mt-1">Hasta 10 imÃ¡genes. JPG, PNG o WebP. MÃ¡x 4 MB c/u.</p>
                    @error('galeria') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- â”€â”€ Botones de acciÃ³n â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('operador.atractivos.index') }}" class="tc-btn-outline">
                Cancelar
            </a>
            <button type="submit" class="tc-btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
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
 * Cuando un dÃ­a se desactiva, se eliminan los name attributes de los inputs
 * para que no se envÃ­en al servidor (el backend interpreta ausencia = null = cerrado).
 */
function toggleDia(dia, abierto) {
    const inputsDiv  = document.getElementById('horario-inputs-' + dia);
    const closedDiv  = document.getElementById('horario-closed-' + dia);
    const inputAbre  = document.getElementById('abre-' + dia);
    const inputCierra = document.getElementById('cierra-' + dia);

    if (abierto) {
        inputsDiv.classList.remove('hidden');
        closedDiv.classList.add('hidden');
        inputAbre.name  = 'horarios[' + dia + '][abre]';
        inputCierra.name = 'horarios[' + dia + '][cierra]';
    } else {
        inputsDiv.classList.add('hidden');
        closedDiv.classList.remove('hidden');
        // Quitamos los names para que no se envÃ­en al servidor
        inputAbre.removeAttribute('name');
        inputCierra.removeAttribute('name');
    }
}

// Inicializar estado al cargar la pÃ¡gina
document.addEventListener('DOMContentLoaded', function () {
    ['lunes','martes','miercoles','jueves','viernes','sabado','domingo'].forEach(function(dia) {
        const toggle = document.getElementById('toggle-' + dia);
        if (toggle && !toggle.checked) {
            toggleDia(dia, false);
        }
    });
});
// Preview de galería (múltiples imágenes)
document.querySelector('input[name="galeria[]"]').addEventListener('change', function(e) {
    var previewContainer = document.getElementById('preview-galeria');
    if (!previewContainer) {
        previewContainer = document.createElement('div');
        previewContainer.id = 'preview-galeria';
        previewContainer.className = 'mt-3 flex gap-2 overflow-x-auto pb-2 scrollbar-hide';
        this.parentNode.appendChild(previewContainer);
    }
    previewContainer.innerHTML = ''; 
    Array.from(e.target.files).slice(0, 10).forEach(function(file) {
        if (!file.type.match('image.*')) return;
        var reader = new FileReader();
        reader.onload = function(e) {
            var imgWrap = document.createElement('div');
            imgWrap.className = 'shrink-0 w-24 h-24 rounded-lg overflow-hidden border border-gray-200';
            imgWrap.style.boxShadow = '0 2px 4px rgba(0,0,0,0.05)';
            var img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'w-full h-full object-cover';
            imgWrap.appendChild(img);
            previewContainer.appendChild(imgWrap);
        };
        reader.readAsDataURL(file);
    });
});

// Preview de portada
function previewPortada(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var previewArea = document.getElementById('preview-portada-area');
            if(previewArea) {
                previewArea.innerHTML = '<img src="' + e.target.result + '" class="w-full h-full object-cover" alt="Portada">';
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush

