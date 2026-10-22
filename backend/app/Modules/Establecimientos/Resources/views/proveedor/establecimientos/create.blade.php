@extends('layouts.app')

@section('title', __('provider_places.register_place'))

@section('panel-rol-label', __('provider_places.provider'))

@section('panel-sidebar-links')
    <a href="{{ route('proveedor.establecimientos.index') }}"
       class="group/sidebar flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 mb-0.5 text-white shadow-sm"
       style="background: var(--color-primary-500);">
        <div class="shrink-0 transition-transform duration-150 group-hover/sidebar:scale-110">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
        </div>
        <span class="whitespace-pre group-hover/sidebar:translate-x-1 transition-transform duration-150">{{ __('provider_places.my_places') }}</span>
    </a>
@endsection

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Back + Header --}}
    <div class="mb-6">
        <a href="{{ route('proveedor.establecimientos.index') }}"
           class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-gray-600 mb-3 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            {{ __('provider_places.my_places') }}
        </a>
        <h1 style="font-family: var(--font-display); font-size: 1.625rem; font-weight: 800; color: #1a2232;">
            {{ __('provider_places.register_place') }}
        </h1>
        <p class="text-sm text-gray-400 mt-0.5">{{ __('provider_places.register_intro') }}</p>
    </div>

    @if($errors->any())
        <div class="tc-alert-error mb-5">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <p class="font-semibold">{{ __('provider_places.fix_errors') }}</p>
                <ul class="mt-1 list-disc list-inside text-sm">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('proveedor.establecimientos.store') }}"
          enctype="multipart/form-data" class="space-y-5" id="form-est-create">
        @csrf

        {{-- ── 1. Tipo de establecimiento ── --}}
        <div class="tc-card p-6">
            <h2 class="text-sm font-bold uppercase tracking-wide mb-1" style="color: var(--color-primary-700);">
                {{ __('provider_places.place_type') }}
            </h2>
            <p class="text-xs text-gray-400 mb-4">{{ __('provider_places.choose_type_hint') }}</p>

            @php
                $tiposDisp = [
                    'restaurante' => ['ico' => '🍽️', 'label' => __('provider_places.type_restaurante')],
                    'hotel'       => ['ico' => '🏨', 'label' => __('provider_places.type_hotel')],
                    'transporte'  => ['ico' => '🚌', 'label' => __('provider_places.type_transporte')],
                    'agencia'     => ['ico' => '🏢', 'label' => __('provider_places.type_agencia')],
                    'otro'        => ['ico' => '📋', 'label' => __('provider_places.type_otro')],
                ];
                $tipoOld = old('tipo', '');
            @endphp
            <div class="flex flex-wrap gap-3">
                @foreach($tiposDisp as $val => $t)
                    <label class="cursor-pointer">
                        <input type="radio" name="tipo" value="{{ $val }}"
                               {{ $tipoOld === $val ? 'checked' : '' }} required
                                class="sr-only peer"
                                onchange="selectTipo('{{ $val }}')">
                        <span id="tipo-btn-{{ $val }}"
                              class="flex items-center gap-2 px-4 py-2.5 rounded-xl border-2 font-semibold text-sm transition-all cursor-pointer select-none"
                              style="{{ $tipoOld === $val
                                 ? 'background: var(--color-primary-500); color: white; border-color: var(--color-primary-500);'
                                 : 'background: white; color: #374151; border-color: #d1d5db;' }}">
                            <span>{{ $t['ico'] }}</span>
                            {{ $t['label'] }}
                        </span>
                    </label>
                @endforeach
            </div>
            @error('tipo')<p class="mt-2 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        {{-- ── 2. Información básica ── --}}
        <div class="tc-card p-6">
            <h2 class="text-sm font-bold uppercase tracking-wide mb-5" style="color: var(--color-primary-700);">
                {{ __('provider_places.basic_info') }}
            </h2>
            <div class="space-y-4">
                {{-- Nombre --}}
                <div>
                    <label class="block text-sm font-semibold mb-1.5 text-gray-700">{{ __('provider_places.place_name') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="nombre" value="{{ old('nombre') }}"
                           placeholder="{{ __('provider_places.name_example') }}" class="tc-input" maxlength="150" required>
                    @error('nombre')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                {{-- Destino + Rango de precio --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1.5 text-gray-700">{{ __('provider_places.destination') }} <span class="text-red-500">*</span></label>
                        <select name="destino_id" class="tc-select" required>
                            <option value="">{{ __('provider_places.select_destination') }}</option>
                            @foreach($destinos as $d)
                                <option value="{{ $d->id }}" @selected(old('destino_id') == $d->id)>{{ $d->nombre }}</option>
                            @endforeach
                        </select>
                        @error('destino_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1.5 text-gray-700">{{ __('provider_places.price_range') }}</label>
                        @php
                            $rangos = [
                                'Bajo'   => __('provider_places.price_low'),
                                'Medio'  => __('provider_places.price_mid'),
                                'Alto'   => __('provider_places.price_high'),
                                'Lujoso' => __('provider_places.price_luxury'),
                            ];
                            $rpOld  = old('rango_precio', '');
                        @endphp
                        <div class="grid grid-cols-2 gap-1.5">
                            @foreach($rangos as $rv => $rl)
                                <label class="cursor-pointer">
                                    <input type="radio" name="rango_precio" value="{{ $rv }}"
                                           {{ $rpOld === $rv ? 'checked' : '' }} class="sr-only"
                                           onchange="selectRango('{{ $rv }}')">
                                    <span id="rango-btn-{{ $rv }}"
                                          class="block text-xs font-semibold py-1.5 px-1 rounded-lg border text-center transition-all cursor-pointer"
                                          style="{{ $rpOld === $rv
                                             ? 'background: var(--color-primary-500); color: white; border-color: var(--color-primary-500);'
                                             : 'background: white; color: #374151; border-color: #d1d5db;' }}">
                                        {{ $rl }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Estado --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1.5 text-gray-700">{{ __('provider_places.status') }} <span class="text-red-500">*</span></label>
                        <select name="estado" class="tc-select" required>
                            <option value="activo"   @selected(old('estado', 'activo') === 'activo')>{{ __('provider_places.active') }}</option>
                            <option value="inactivo" @selected(old('estado') === 'inactivo')>{{ __('provider_places.inactive') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1.5 text-gray-700">{{ __('provider_places.address') }}</label>
                        <input type="text" name="direccion" value="{{ old('direccion') }}"
                               placeholder="{{ __('provider_places.address_example') }}" class="tc-input" maxlength="255">
                    </div>
                </div>

                {{-- Descripción --}}
                <div>
                    <label class="block text-sm font-semibold mb-1.5 text-gray-700">{{ __('provider_places.description') }}</label>
                    <textarea name="descripcion" rows="4" placeholder="{{ __('provider_places.description_placeholder') }}" class="tc-input resize-none">{{ old('descripcion') }}</textarea>
                </div>
            </div>
        </div>

        {{-- ── 3. Categoría de Interés ── --}}
        <div class="tc-card p-6">
            <h2 class="text-sm font-bold uppercase tracking-wide mb-1" style="color: var(--color-primary-700);">
                {{ __('provider_places.interest_category') }}
            </h2>
            <p class="text-xs text-gray-400 mb-4">{{ __('provider_places.select_all') }}</p>
            <div class="flex flex-wrap gap-2">
                @foreach($categorias as $cat)
                    @php $checked = in_array($cat->id, old('categorias', [])); @endphp
                    <label class="cursor-pointer select-none">
                        <input type="checkbox" name="categorias[]" value="{{ $cat->id }}"
                               {{ $checked ? 'checked' : '' }} class="sr-only"
                               onchange="toggleChipEstCat(this, '{{ $cat->id }}')">
                        <span class="inline-block text-sm font-medium px-4 py-1.5 rounded-full border transition-all"
                              style="{{ $checked
                                ? 'background-color: var(--color-primary-500, #16734f); color: #ffffff; border-color: var(--color-primary-500, #16734f);'
                                : 'background-color: #ffffff; color: #374151; border-color: #d1d5db;' }}"
                              id="chip-estcat-{{ $cat->id }}">
                            {{ $cat->nombre }}
                        </span>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- ── 4. Ubicación geográfica (MAPA INTERACTIVO) ── --}}
        <div class="tc-card p-6">
            <h2 class="text-sm font-bold uppercase tracking-wide mb-1" style="color: var(--color-primary-700);">
                {{ __('provider_places.exact_location') }} <span class="text-red-500">*</span>
            </h2>
            <p class="text-xs text-gray-400 mb-4">{{ __('provider_places.map_hint') }}</p>
            
            <div class="space-y-4">
                <!-- Contenedor del Mapa -->
                <div class="relative w-full h-[450px] rounded-2xl overflow-hidden border border-gray-200 shadow-sm mb-4">
                    
                    <!-- Buscador Flotante sobre el Mapa -->
                    <div class="absolute top-4 left-4 z-10 w-11/12 max-w-sm">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-[#16734f]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input type="text" id="map_search" placeholder="{{ __('provider_places.map_search_example') }}" autocomplete="off"
                                class="w-full pl-11 px-4 py-3.5 bg-white/95 backdrop-blur-md border border-gray-100 rounded-xl shadow-lg focus:ring-2 focus:ring-[#16734f]/50 focus:border-[#16734f] transition-all text-sm text-gray-800 placeholder-gray-400">
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
                        <label class="block text-xs font-semibold text-gray-500 mb-1">{{ __('provider_places.selected_latitude') }}</label>
                        <input type="number" step="any" id="latitud_input" name="latitud" value="{{ old('latitud', '') }}" readonly required
                            class="w-full px-4 py-2 rounded-lg border-none bg-transparent text-gray-800 focus:outline-none font-mono text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">{{ __('provider_places.selected_longitude') }}</label>
                        <input type="number" step="any" id="longitud_input" name="longitud" value="{{ old('longitud', '') }}" readonly required
                            class="w-full px-4 py-2 rounded-lg border-none bg-transparent text-gray-800 focus:outline-none font-mono text-sm">
                    </div>
                </div>
                @error('latitud') <p class="text-red-500 text-xs mt-1 font-medium">{{ __('provider_places.choose_map_location') }}</p> @enderror
            </div>
        </div>

        {{-- ── 5. Horario semanal de atención ── --}}
        <div class="tc-card p-6">
            <h2 class="text-sm font-bold uppercase tracking-wide mb-1" style="color: var(--color-primary-700);">
                {{ __('provider_places.weekly_hours') }}
            </h2>
            <p class="text-xs text-gray-400 mb-4">{{ __('provider_places.weekly_hours_hint') }}</p>

            @php
                $diasConf = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];
                $horariosOld = old('horarios', []);
            @endphp
            <div class="space-y-2">
                @foreach($diasConf as $k)
                    @php
                        $hor    = $horariosOld[$k] ?? null;
                        $open   = old() ? (is_array($hor) && isset($hor['abre'])) : true;
                        $abre   = $hor['abre']   ?? '09:00';
                        $cierra = $hor['cierra']  ?? '22:00';
                    @endphp
                    <div class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors"
                         id="est-row-{{ $k }}"
                         style="{{ $open ? 'background: var(--color-amber-soft);' : 'background: #f3f4f6;' }}">
                        <label class="tc-toggle shrink-0">
                            <input type="checkbox" id="est-tog-{{ $k }}" {{ $open ? 'checked' : '' }}
                                   onchange="toggleEstHorario('{{ $k }}', this.checked)">
                            <span class="tc-toggle-track"></span>
                            <span class="tc-toggle-thumb"></span>
                        </label>
                        <span class="w-24 text-sm font-semibold shrink-0"
                              style="color: {{ $open ? 'var(--color-tertiary-500)' : '#9ca3af' }};" id="est-label-{{ $k }}">
                            {{ __('provider_places.day_'.$k) }}
                        </span>
                        <div id="est-times-{{ $k }}" class="flex items-center gap-3 flex-1 {{ $open ? '' : 'hidden' }}"
                             style="{{ $open ? 'display: flex;' : 'display: none;' }}">
                            <div class="flex items-center gap-1.5 shrink-0">
                                <span class="text-xs text-gray-500">{{ __('provider_places.opens') }}</span>
                                <input type="time" id="est-abre-{{ $k }}" name="horarios[{{ $k }}][abre]"
                                       value="{{ $abre }}" class="tc-input text-sm" style="width: 110px;">
                            </div>
                            <span class="text-gray-300 text-sm shrink-0">→</span>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <span class="text-xs text-gray-500">{{ __('provider_places.closes') }}</span>
                                <input type="time" id="est-cierra-{{ $k }}" name="horarios[{{ $k }}][cierra]"
                                       value="{{ $cierra }}" class="tc-input text-sm" style="width: 110px;">
                            </div>
                        </div>
                        <span id="est-closed-{{ $k }}" class="{{ $open ? 'hidden' : '' }} text-xs text-gray-400 flex-1"
                              style="{{ $open ? 'display: none;' : '' }}">{{ __('provider_places.closed') }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ── 6. Imágenes del establecimiento ── --}}
        <div class="tc-card p-6">
            <h2 class="text-sm font-bold uppercase tracking-wide mb-5" style="color: var(--color-primary-700);">
                {{ __('provider_places.place_images') }}
            </h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold mb-1.5 text-gray-700">{{ __('provider_places.cover_image') }} <span class="text-red-500">*</span></label>
                    <div id="est-preview-portada-area" class="rounded-xl overflow-hidden mb-2" style="background: var(--color-amber-soft); height: 130px; display: flex; align-items: center; justify-content: center;">
                        <div class="text-center">
                            <svg class="w-8 h-8 mx-auto mb-1" style="color: var(--color-tertiary-400);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-xs" style="color: var(--color-tertiary-500);">{{ __('provider_places.cover_preview') }}</p>
                        </div>
                    </div>
                    <input type="file" name="imagen_portada" accept="image/jpeg,image/png,image/webp,image/jpg,.jpg,.jpeg,.png,.webp,.jfif" required class="tc-input text-sm file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-amber-50 file:text-amber-700"
                           onchange="estPreviewPortada(this)">
                    <p class="text-xs text-gray-400 mt-1">{{ __('provider_places.cover_create_hint') }}</p>
                    @error('imagen_portada')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5 text-gray-700">{{ __('provider_places.additional_gallery') }}</label>
                    <input type="file" name="galeria[]" accept="image/jpeg,image/png,image/webp,image/jpg,.jpg,.jpeg,.png,.webp,.jfif" multiple required
                           class="tc-input text-sm file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-amber-50 file:text-amber-700">
                    <p class="text-xs text-gray-400 mt-1">{{ __('provider_places.gallery_create_hint') }}</p>
                </div>
            </div>
        </div>

        {{-- Botones --}}
        <div class="flex items-center justify-end gap-3 pb-4">
            <a href="{{ route('proveedor.establecimientos.index') }}"
               class="tc-btn-outline">{{ __('provider_places.cancel') }}</a>
            <button type="submit" class="tc-btn-primary">
                + {{ __('provider_places.register_place') }}
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // ══════════════════════════════════════════════════════════════
    // CARGAR GOOGLE MAPS API
    // ══════════════════════════════════════════════════════════════
    (g=>{var h,a,k,p="The Google Maps JavaScript API",c="google",l="importLibrary",q="__ib__",m=document,b=window;b=b[c]||(b[c]={});var d=b.maps||(b.maps={}),r=new Set,e=new URLSearchParams,u=()=>h||(h=new Promise(async(f,n)=>{await (a=m.createElement("script"));e.set("libraries",[...r]+"");for(k in g)e.set(k.replace(/[A-Z]/g,t=>"_"+t[0].toLowerCase()),g[k]);e.set("callback",c+".maps."+q);a.src=`https://maps.${c}apis.com/maps/api/js?`+e;d[q]=f;a.onerror=()=>h=n(Error(p+" could not load."));a.nonce=m.querySelector("script[nonce]")?.nonce||"";m.head.append(a)}));d[l]?console.warn(p+" only loads once. Ignoring:",g):d[l]=(f,...n)=>r.add(f)&&u().then(()=>d[l](f,...n))})({
        key: @json(config('services.google_maps.key')),
        v: "weekly",
        language: @json(app()->getLocale())
    });

    async function initInteractiveFormMap() {
        try {
            if (typeof google === 'undefined' || !google.maps || !google.maps.importLibrary) return;
            const { Map } = await google.maps.importLibrary("maps");
            const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");
            const { Place, AutocompleteSuggestion } = await google.maps.importLibrary("places");

            const latInput = document.getElementById('latitud_input');
            const lngInput = document.getElementById('longitud_input');
            const searchInput = document.getElementById('map_search');
            const suggestionsList = document.getElementById('map_suggestions');

            if (!latInput || !lngInput || !document.getElementById("interactive_form_map")) return;

        // Posición Inicial (Perú por defecto)
        let initialLat = parseFloat(latInput.value) || -9.1900;
        let initialLng = parseFloat(lngInput.value) || -75.0152;
        let initialZoom = latInput.value ? 16 : 5;

        const map = new Map(document.getElementById("interactive_form_map"), {
            center: { lat: initialLat, lng: initialLng },
            zoom: initialZoom,
            mapId: "DEMO_MAP_ID", // Obligatorio para AdvancedMarkers
            disableDefaultUI: false,
            streetViewControl: false,
            mapTypeControl: false
        });

        let marker = null;

        function updateMarkerPosition(lat, lng) {
            if (!marker) {
                marker = new AdvancedMarkerElement({
                    map: map,
                    position: { lat, lng },
                    gmpDraggable: true,
                    title: @json(__('provider_places.marker_drag_hint'))
                });
                
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

        if (latInput.value && lngInput.value) {
            updateMarkerPosition(initialLat, initialLng);
        }

        map.addListener('click', (event) => {
            const lat = event.latLng.lat();
            const lng = event.latLng.lng();
            updateMarkerPosition(lat, lng);
        });

        if (searchInput && suggestionsList) {
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
                    
                    suggestions.forEach(suggestion => {
                        const text = suggestion.placePrediction.text.text;
                        const placeId = suggestion.placePrediction.placeId;

                        const li = document.createElement('li');
                        li.className = 'px-4 py-3 hover:bg-gray-50 cursor-pointer text-sm text-gray-700 border-b border-gray-100 last:border-0 flex items-center gap-2';
                        li.innerHTML = `<svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg> ${text}`;
                        
                        li.addEventListener('click', async function() {
                            searchInput.value = text;
                            suggestionsList.classList.add('hidden');
                            
                            const place = new Place({ id: placeId });
                            await place.fetchFields({ fields: ['location'] });
                            
                            if (place.location) {
                                const lat = place.location.lat();
                                const lng = place.location.lng();
                                
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

            document.addEventListener('click', (e) => {
                if (e.target !== searchInput && e.target !== suggestionsList) {
                    suggestionsList.classList.add('hidden');
                }
            });
            
            searchInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') e.preventDefault();
            });
        }
        } catch (e) {
            console.warn("Interactive map could not load:", e);
        }
    }

    // ══════════════════════════════════════════════════════════════
    // OTRAS FUNCIONALIDADES (TIPOS, RANGOS, HORARIOS, PREVIEWS)
    // ══════════════════════════════════════════════════════════════
    var tiposDisponibles = ['restaurante','hotel','transporte','agencia','otro'];
    var rangosDisponibles = ['Bajo', 'Medio', 'Alto', 'Lujoso'];

    function selectTipo(val) {
        tiposDisponibles.forEach(function(t) {
            var btn = document.getElementById('tipo-btn-' + t);
            if (!btn) return;
            if (t === val) {
                btn.style.background   = 'var(--color-primary-500)';
                btn.style.color        = 'white';
                btn.style.borderColor  = 'var(--color-primary-500)';
            } else {
                btn.style.background   = 'white';
                btn.style.color        = '#374151';
                btn.style.borderColor  = '#d1d5db';
            }
        });
    }

    function selectRango(val) {
        rangosDisponibles.forEach(function(r) {
            var btn = document.getElementById('rango-btn-' + r);
            if (!btn) return;
            if (r === val) {
                btn.style.background   = 'var(--color-primary-500)';
                btn.style.color        = 'white';
                btn.style.borderColor  = 'var(--color-primary-500)';
            } else {
                btn.style.background   = 'white';
                btn.style.color        = '#374151';
                btn.style.borderColor  = '#d1d5db';
            }
        });
    }

    function toggleEstHorario(dia, open) {
        var row    = document.getElementById('est-row-' + dia);
        var label  = document.getElementById('est-label-' + dia);
        var times  = document.getElementById('est-times-' + dia);
        var closed = document.getElementById('est-closed-' + dia);
        var abre   = document.getElementById('est-abre-' + dia);
        var cierra = document.getElementById('est-cierra-' + dia);

        if (!row) return;

        row.style.background = open ? 'var(--color-amber-soft)' : '#f3f4f6';
        label.style.color    = open ? 'var(--color-tertiary-500)' : '#9ca3af';

        if (open) {
            times.classList.remove('hidden'); times.classList.add('flex');
            times.style.display = 'flex';
            closed.classList.add('hidden');
            closed.style.display = 'none';
            abre.name   = 'horarios[' + dia + '][abre]';
            cierra.name = 'horarios[' + dia + '][cierra]';
            abre.disabled = false;
            cierra.disabled = false;
        } else {
            times.classList.add('hidden'); times.classList.remove('flex');
            times.style.display = 'none';
            closed.classList.remove('hidden');
            closed.style.display = '';
            abre.removeAttribute('name');
            cierra.removeAttribute('name');
            abre.disabled = true;
            cierra.disabled = true;
        }
    }

    // ── Chips categoría (Interés) ──
    function toggleChipEstCat(cb, id) {
        var span = document.getElementById('chip-estcat-' + id);
        if (!span && cb && cb.closest) {
            span = cb.closest('label') ? cb.closest('label').querySelector('span') : null;
        }
        if (!span) return;
        if (cb.checked) {
            span.style.backgroundColor = 'var(--color-primary-500, #16734f)';
            span.style.color           = '#ffffff';
            span.style.borderColor     = 'var(--color-primary-500, #16734f)';
        } else {
            span.style.backgroundColor = '#ffffff';
            span.style.color           = '#374151';
            span.style.borderColor     = '#d1d5db';
        }
    }
    window.toggleChipEstCat = toggleChipEstCat;

    // Delegación global inmediata (no depende de DOMContentLoaded ni Maps)
    document.addEventListener('change', function(e) {
        if (e.target && e.target.name === 'categorias[]') {
            toggleChipEstCat(e.target, e.target.value);
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar Mapa de manera protegida
        try {
            initInteractiveFormMap();
        } catch(err) {
            console.warn("Map init:", err);
        }

        // Inicializar horarios
        ['lunes','martes','miercoles','jueves','viernes','sabado','domingo'].forEach(function(d) {
            var t = document.getElementById('est-tog-' + d);
            if (t) toggleEstHorario(d, t.checked);
        });

        // Asegurar estado visual de chips de categoría
        document.querySelectorAll('input[type="checkbox"][name="categorias[]"]').forEach(function(cb) {
            toggleChipEstCat(cb, cb.value);
        });
    });

    // Preview de galería (múltiples imágenes)
    var inputGaleria = document.querySelector('input[name="galeria[]"]');
    if (inputGaleria) {
        inputGaleria.addEventListener('change', function(e) {
            var previewContainer = document.getElementById('est-preview-galeria');
            if (!previewContainer) {
                previewContainer = document.createElement('div');
                previewContainer.id = 'est-preview-galeria';
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
    }

    // Preview de portada
    function estPreviewPortada(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var previewArea = document.getElementById('est-preview-portada-area');
                if(previewArea) {
                    previewArea.innerHTML = '<img src="' + e.target.result + '" class="w-full h-full object-cover" alt="Portada">';
                }
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
