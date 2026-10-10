@extends('layouts.app')

@section('title', 'Nuevo Atractivo')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- ── Header ──────────────────────────────────────────────── --}}
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

    {{-- ── Errores de validación ────────────────────────────────── --}}
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

        {{-- ── Sección: Información básica ────────────────────────── --}}
        <div class="tc-card p-6">
            <h2 class="text-sm font-bold uppercase tracking-wide mb-5" style="color: var(--color-primary-700);">
                Información básica
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                {{-- Destino --}}
                <div class="sm:col-span-2">
                    <label for="destino_id" class="block text-sm font-semibold mb-1.5 text-gray-700">
                        Destino <span class="text-red-500">*</span>
                    </label>
                    <select id="destino_id" name="destino_id" class="tc-select" required>
                        <option value="">Selecciona un destino…</option>
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

                {{-- Descripción --}}
                <div class="sm:col-span-2">
                    <label for="descripcion" class="block text-sm font-semibold mb-1.5 text-gray-700">
                        Descripción
                    </label>
                    <textarea id="descripcion" name="descripcion" rows="4"
                              placeholder="Describe el atractivo…"
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

                {{-- Duración estimada --}}
                <div>
                    <label for="duracion_estimada_min" class="block text-sm font-semibold mb-1.5 text-gray-700">
                        Duración estimada (minutos)
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
                        <option value="activo"   @selected(old('estado', 'activo') === 'activo')>Activo</option>
                        <option value="inactivo" @selected(old('estado') === 'inactivo')>Inactivo</option>
                    </select>
                    @error('estado')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 📍 MAPA INTERACTIVO PARA SELECCIÓN DE COORDENADAS ════════════════════════ --}}
                <div class="sm:col-span-2 mt-4">
                    <label class="block text-sm font-semibold mb-1.5 text-gray-700">Ubicación Exacta en el Mapa <span class="text-red-500">*</span></label>
                    <p class="text-xs text-gray-400 mb-4">Busca la dirección o arrastra el marcador rojo para fijar las coordenadas automáticamente.</p>
                    
                    <!-- Contenedor del Mapa -->
                    <div class="relative w-full h-[450px] rounded-2xl overflow-hidden border border-gray-200 shadow-sm mb-4">
                        
                        <!-- Buscador Flotante sobre el Mapa -->
                        <div class="absolute top-4 left-4 z-10 w-11/12 max-w-sm">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-[#00626A]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </div>
                                <input type="text" id="map_search" placeholder="Ej. Plaza de Armas de Cusco..." autocomplete="off"
                                    class="w-full pl-11 px-4 py-3.5 bg-white/95 backdrop-blur-md border border-gray-100 rounded-xl shadow-lg focus:ring-2 focus:ring-[#00626A]/50 focus:border-[#00626A] transition-all text-sm text-gray-800 placeholder-gray-400">
                            </div>
                            <!-- Lista de sugerencias de Google -->
                            <ul id="map_suggestions" class="absolute w-full bg-white rounded-xl shadow-xl mt-2 hidden max-h-60 overflow-y-auto border border-gray-100"></ul>
                        </div>
                        
                        <!-- Lienzo de Google Maps -->
                        <div id="interactive_form_map" class="w-full h-full bg-gray-50"></div>
                    </div>

                    <!-- Inputs de Coordenadas (Solo Lectura) -->
                    <div class="grid grid-cols-2 gap-4 bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Latitud Seleccionada</label>
                            <input type="number" step="any" id="latitud_input" name="latitud" value="{{ old('latitud', '') }}" readonly required
                                class="w-full px-4 py-2 rounded-lg border-none bg-transparent text-gray-800 focus:outline-none font-mono text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Longitud Seleccionada</label>
                            <input type="number" step="any" id="longitud_input" name="longitud" value="{{ old('longitud', '') }}" readonly required
                                class="w-full px-4 py-2 rounded-lg border-none bg-transparent text-gray-800 focus:outline-none font-mono text-sm">
                        </div>
                    </div>
                    @error('latitud') <p class="text-red-500 text-xs mt-1 font-medium">Debes seleccionar una ubicación en el mapa.</p> @enderror
                </div>
            </div>
        </div>

        {{-- ── Sección: Categorías ─────────────────────────────────── --}}
        <div class="tc-card p-6">
            <h2 class="text-sm font-bold uppercase tracking-wide mb-5" style="color: var(--color-primary-700);">
                Categorías de interés
            </h2>
            @if($categorias->isEmpty())
                <p class="text-sm text-gray-400">No hay categorías disponibles aún.</p>
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

        {{-- ── Sección: Horarios semanales ────────────────────────── --}}
        <div class="tc-card p-6" id="horario-editor">
            <h2 class="text-sm font-bold uppercase tracking-wide mb-2" style="color: var(--color-primary-700);">
                Horarios de apertura
            </h2>
            <p class="text-xs text-gray-400 mb-5">
                Activa el toggle del día para indicar que está abierto y luego define el horario.
                Deja desactivado si ese día permanece cerrado.
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

                        {{-- Nombre del día --}}
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
                            <span class="text-gray-300">→</span>
                            <div class="flex items-center gap-2">
                                <label class="text-xs text-gray-400 shrink-0">Cierra</label>
                                <input type="time" id="cierra-{{ $clave }}"
                                       name="horarios[{{ $clave }}][cierra]"
                                       value="{{ $cierra }}"
                                       class="tc-input w-32 text-sm">
                            </div>
                        </div>

                        {{-- Etiqueta "Cerrado" cuando el día está desactivado --}}
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

        {{-- ── Sección: Imágenes ───────────────────────────────────── --}}
        <div class="tc-card p-6">
            <h2 class="text-sm font-bold uppercase tracking-wide mb-5" style="color: var(--color-primary-700);">
                Imágenes
            </h2>
            <div class="space-y-4">

                {{-- Imagen de portada --}}
                <div>
                    <label for="imagen_portada" class="block text-sm font-semibold mb-1.5 text-gray-700">
                        Imagen de portada
                    </label>
                    <input type="file" id="imagen_portada" name="imagen_portada"
                           accept="image/jpeg,image/png,image/webp,image/jpg,.jpg,.jpeg,.png,.webp,.jfif"
                           class="tc-input text-sm file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700">
                    <p class="text-xs text-gray-400 mt-1">JPG, PNG o WebP. Máx 4 MB.</p>
                    @error('imagen_portada') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Galería adicional --}}
                <div>
                    <label for="galeria" class="block text-sm font-semibold mb-1.5 text-gray-700">
                        Galería de imágenes adicional
                    </label>
                    <input type="file" id="galeria" name="galeria[]"
                           accept="image/jpeg,image/png,image/webp,image/jpg,.jpg,.jpeg,.png,.webp,.jfif"
                           multiple
                           class="tc-input text-sm file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700">
                    <p class="text-xs text-gray-400 mt-1">Hasta 10 imágenes. JPG, PNG o WebP. Máx 4 MB c/u.</p>
                    @error('galeria') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- ── Botones de acción ───────────────────────────────────── --}}
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
    (g=>{var h,a,k,p="The Google Maps JavaScript API",c="google",l="importLibrary",q="__ib__",m=document,b=window;b=b[c]||(b[c]={});var d=b.maps||(b.maps={}),r=new Set,e=new URLSearchParams,u=()=>h||(h=new Promise(async(f,n)=>{await (a=m.createElement("script"));e.set("libraries",[...r]+"");for(k in g)e.set(k.replace(/[A-Z]/g,t=>"_"+t[0].toLowerCase()),g[k]);e.set("callback",c+".maps."+q);a.src=`https://maps.${c}apis.com/maps/api/js?`+e;d[q]=f;a.onerror=()=>h=n(Error(p+" could not load."));a.nonce=m.querySelector("script[nonce]")?.nonce||"";m.head.append(a)}));d[l]?console.warn(p+" only loads once. Ignoring:",g):d[l]=(f,...n)=>r.add(f)&&u().then(()=>d[l](f,...n))})({
        key: "{{ env('GOOGLE_MAPS_API_KEY') }}",
        v: "weekly"
    });

    async function initInteractiveFormMap() {
        const { Map } = await google.maps.importLibrary("maps");
        const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");
        const { Place, AutocompleteSuggestion } = await google.maps.importLibrary("places");

        const latInput = document.getElementById('latitud_input');
        const lngInput = document.getElementById('longitud_input');
        const searchInput = document.getElementById('map_search');
        const suggestionsList = document.getElementById('map_suggestions');

        // Posición Inicial (Usa las de BD si existen (edit), o Lima/Perú por defecto)
        let initialLat = parseFloat(latInput.value) || -9.1900;
        let initialLng = parseFloat(lngInput.value) || -75.0152;
        let initialZoom = latInput.value ? 16 : 5; // Zoom alto si estamos editando, bajo si es nuevo

        const map = new Map(document.getElementById("interactive_form_map"), {
            center: { lat: initialLat, lng: initialLng },
            zoom: initialZoom,
            mapId: "DEMO_MAP_ID", // Obligatorio para AdvancedMarkers
            disableDefaultUI: false,
            streetViewControl: false, // Quitamos el muñequito
            mapTypeControl: false // Quitamos selector Satélite/Mapa
        });

        let marker = null;

        // Función para mover el PIN y actualizar los inputs
        function updateMarkerPosition(lat, lng) {
            if (!marker) {
                marker = new AdvancedMarkerElement({
                    map: map,
                    position: { lat, lng },
                    gmpDraggable: true, // ¡Permite arrastrar el pin libremente!
                    title: "Arrastra para ajustar la ubicación"
                });
                
                // Escuchar el final del arrastre (Drop)
                marker.addListener('dragend', (event) => {
                    latInput.value = event.latLng.lat().toFixed(6);
                    lngInput.value = event.latLng.lng().toFixed(6);
                });
            } else {
                marker.position = { lat, lng };
            }
            
            latInput.value = lat.toFixed(6);
            lngInput.value = lng.toFixed(6);
        }

        // Si estamos en EDITAR y ya hay coordenadas, dibujar el pin de inmediato
        if (latInput.value && lngInput.value) {
            updateMarkerPosition(initialLat, initialLng);
        }

        // Evento 1: Clic en cualquier parte del mapa
        map.addListener('click', (event) => {
            const lat = event.latLng.lat();
            const lng = event.latLng.lng();
            updateMarkerPosition(lat, lng);
        });

        // Evento 2: Buscador inteligente de ubicaciones (Autocomplete 2025)
        searchInput.addEventListener('input', async function() {
            const query = this.value;
            if (query.length < 3) {
                suggestionsList.innerHTML = '';
                suggestionsList.classList.add('hidden');
                return;
            }

            try {
                const response = await AutocompleteSuggestion.fetchAutocompleteSuggestions({ input: query });
                const suggestions = response.suggestions;

                if (!suggestions || suggestions.length === 0) {
                    suggestionsList.classList.add('hidden');
                    return;
                }

                suggestionsList.innerHTML = '';
                
                // Pintar resultados
                suggestions.forEach(suggestion => {
                    const text = suggestion.placePrediction.text.text;
                    const placeId = suggestion.placePrediction.placeId;

                    const li = document.createElement('li');
                    li.className = 'px-4 py-3 hover:bg-gray-50 cursor-pointer text-sm text-gray-700 border-b border-gray-100 last:border-0 flex items-center gap-2';
                    li.innerHTML = `<svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg> ${text}`;
                    
                    // Al hacer clic en un resultado
                    li.addEventListener('click', async function() {
                        searchInput.value = text;
                        suggestionsList.classList.add('hidden');
                        
                        // Traer coordenadas
                        const place = new Place({ id: placeId });
                        await place.fetchFields({ fields: ['location'] });
                        
                        if (place.location) {
                            const lat = place.location.lat();
                            const lng = place.location.lng();
                            
                            // "Volar" hacia el lugar y clavar el pin
                            map.panTo({ lat, lng });
                            map.setZoom(17);
                            updateMarkerPosition(lat, lng);
                        }
                    });
                    suggestionsList.appendChild(li);
                });
                suggestionsList.classList.remove('hidden');
            } catch (e) {
                console.error("Error consultando Google Places:", e);
            }
        });

        // Ocultar buscador si hacen clic fuera
        document.addEventListener('click', (e) => {
            if (e.target !== searchInput && e.target !== suggestionsList) {
                suggestionsList.classList.add('hidden');
            }
        });
        
        // Evitar que el 'Enter' mande el formulario general por error
        searchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') e.preventDefault();
        });
    }

    document.addEventListener("DOMContentLoaded", initInteractiveFormMap);
</script>
@endpush

