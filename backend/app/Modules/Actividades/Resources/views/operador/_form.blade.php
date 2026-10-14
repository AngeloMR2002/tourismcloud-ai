@php
    $portadaUrl = $actividad?->portada_url;
    $googleMapsApiKey = trim((string) config('services.google_maps.key', ''));
    $latitudInicial = old('latitud', $actividad?->latitud);
    $longitudInicial = old('longitud', $actividad?->longitud);
@endphp

@if($errors->any())
    <div class="tc-alert-error" role="alert">
        Revisa los campos marcados antes de guardar la actividad.
    </div>
@endif

<section class="tc-card tc-form-section p-5 sm:p-6">
    <div class="tc-form-section__heading">
        <div>
            <p class="tc-form-section__eyebrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M5 20V4h10l4 4v12H5Z" stroke-width="1.7" stroke-linejoin="round"/><path d="M15 4v4h4M8 13h8M8 17h5" stroke-width="1.7" stroke-linecap="round"/></svg>Actividad</p>
            <h2>Información de la experiencia</h2>
            <p>Completa los datos que el turista verá al comparar actividades dentro de un destino.</p>
        </div>
        <span class="tc-step-badge">Paso 1 de 2</span>
    </div>
    <div class="tc-form-grid tc-form-grid--two">
        <div class="tc-form-field tc-form-field--wide">
            <label for="destino_id" class="block text-sm font-semibold text-gray-700 mb-1.5">Destino <span class="text-red-600">*</span></label>
            <select id="destino_id" name="destino_id" class="tc-select @error('destino_id') border-red-400 @enderror" required>
                <option value="">Selecciona un destino</option>
                @foreach($destinos as $destino)
                    <option value="{{ $destino->id }}" @selected((string) old('destino_id', $actividad?->destino_id) === (string) $destino->id)>
                        {{ $destino->nombre }}{{ $destino->estado !== 'activo' ? ' (inactivo; solo se puede conservar)' : '' }}
                    </option>
                @endforeach
            </select>
            @error('destino_id')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="tc-form-field tc-form-field--wide">
            <label for="nombre" class="block text-sm font-semibold text-gray-700 mb-1.5">Nombre de la actividad <span class="text-red-600">*</span></label>
            <input id="nombre" name="nombre" value="{{ old('nombre', $actividad?->nombre) }}" maxlength="150" placeholder="Ej. Taller de cerámica tradicional" class="tc-input @error('nombre') border-red-400 @enderror" required>
            @error('nombre')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="tc-form-field tc-form-field--wide">
            <label for="descripcion" class="block text-sm font-semibold text-gray-700 mb-1.5">Descripción</label>
            <textarea id="descripcion" name="descripcion" rows="5" maxlength="5000" class="tc-input @error('descripcion') border-red-400 @enderror" placeholder="Explica qué incluye la experiencia y qué debe considerar el turista.">{{ old('descripcion', $actividad?->descripcion) }}</textarea>
            @error('descripcion')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="tc-form-field">
            <label for="costo" class="block text-sm font-semibold text-gray-700 mb-1.5">Costo estimado (S/) <span class="text-red-600">*</span></label>
            <input id="costo" type="number" name="costo" min="0" max="99999999.99" step="0.01" value="{{ old('costo', $actividad?->costo ?? '0.00') }}" class="tc-input @error('costo') border-red-400 @enderror" required>
            <p class="tc-form-field__hint">Usa 0 si la actividad es gratuita.</p>
            @error('costo')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="tc-form-field">
            <label for="duracion_estimada_min" class="block text-sm font-semibold text-gray-700 mb-1.5">Duración estimada (minutos)</label>
            <input id="duracion_estimada_min" type="number" name="duracion_estimada_min" min="1" max="10080" value="{{ old('duracion_estimada_min', $actividad?->duracion_estimada_min) }}" class="tc-input @error('duracion_estimada_min') border-red-400 @enderror" placeholder="Ej. 120">
            @error('duracion_estimada_min')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="tc-form-field tc-form-field--wide">
            <div class="tc-form-location">
                <span class="tc-form-location__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 20s6-5.2 6-10a6 6 0 1 0-12 0c0 4.8 6 10 6 10Z" stroke-width="1.7" stroke-linejoin="round"/><circle cx="12" cy="10" r="2" stroke-width="1.7"/></svg></span>
                <div>
                    <strong>Lugar o punto de encuentro</strong>
                    <p>Busca el lugar donde se realiza la experiencia, haz clic en el mapa o arrastra el marcador. A diferencia de un atractivo, este punto puede ser el inicio de la actividad.</p>
                </div>
            </div>
        </div>

        <div class="tc-form-field tc-form-field--wide">
            <div class="relative overflow-hidden rounded-2xl border border-[#00626A]/10 bg-[#e6f4f5] shadow-sm">
                <div class="absolute left-4 right-4 top-4 z-10 sm:right-auto sm:w-[25rem]">
                    <label for="actividad-map-search" class="sr-only">Buscar lugar o punto de encuentro</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-[#00626A]" aria-hidden="true">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="11" cy="11" r="6.5" stroke-width="1.8"/><path d="m16 16 4 4" stroke-width="1.8" stroke-linecap="round"/></svg>
                        </span>
                        <input id="actividad-map-search" type="search" autocomplete="off" placeholder="Busca un lugar o punto de encuentro…" @disabled(! $googleMapsApiKey) class="tc-input w-full bg-white/95 py-3 pl-11 pr-4 shadow-lg backdrop-blur-sm disabled:cursor-not-allowed disabled:opacity-75">
                    </div>
                    <ul id="actividad-map-suggestions" class="mt-2 hidden max-h-60 overflow-y-auto rounded-xl border border-slate-100 bg-white shadow-xl" aria-label="Sugerencias de lugares"></ul>
                </div>
                <div id="actividad-form-map" class="h-[21rem] w-full sm:h-[25rem]" aria-label="Mapa para seleccionar el lugar de la actividad"></div>
                <div id="actividad-map-status" class="absolute inset-0 flex items-center justify-center bg-[#e6f4f5] p-6 text-center text-sm leading-6 text-[#08565D]" @if($googleMapsApiKey) aria-live="polite" @endif>
                    @if($googleMapsApiKey)
                        Cargando selector de ubicación…
                    @else
                        <div class="max-w-md">
                            <strong class="block text-base text-[#054048]">El selector de Google Maps aún no está configurado.</strong>
                            <p class="mt-2">Puedes registrar las coordenadas manualmente por ahora. Al configurar <code class="rounded bg-white px-1.5 py-0.5 text-xs">GOOGLE_MAPS_API_KEY</code>, aquí aparecerán la búsqueda, el mapa y el marcador arrastrable.</p>
                        </div>
                    @endif
                </div>
            </div>
            <p class="tc-form-field__hint mt-2">Las coordenadas son opcionales, pero deben registrarse juntas para mostrar la ubicación pública.</p>
        </div>

        <div class="tc-form-field">
            <label for="latitud" class="block text-sm font-semibold text-gray-700 mb-1.5">Latitud</label>
            <input id="latitud" type="number" name="latitud" step="0.0000001" min="-90" max="90" value="{{ $latitudInicial }}" class="tc-input @error('latitud') border-red-400 @enderror" placeholder="Ej. -9.085">
            @error('latitud')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
            <p class="tc-form-field__hint">Se completa al elegir una posición en el mapa; también puedes editarla manualmente.</p>
        </div>

        <div class="tc-form-field">
            <label for="longitud" class="block text-sm font-semibold text-gray-700 mb-1.5">Longitud</label>
            <input id="longitud" type="number" name="longitud" step="0.0000001" min="-180" max="180" value="{{ $longitudInicial }}" class="tc-input @error('longitud') border-red-400 @enderror" placeholder="Ej. -78.565">
            @error('longitud')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="tc-form-field tc-form-field--wide">
            <label for="estado" class="block text-sm font-semibold text-gray-700 mb-1.5">Estado <span class="text-red-600">*</span></label>
            <select id="estado" name="estado" class="tc-select @error('estado') border-red-400 @enderror" required>
                <option value="activo" @selected(old('estado', $actividad?->estado ?? 'activo') === 'activo')>Activo</option>
                <option value="inactivo" @selected(old('estado', $actividad?->estado) === 'inactivo')>Inactivo</option>
            </select>
            @error('estado')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>
</section>

<section class="tc-card tc-form-section p-5 sm:p-6">
    <div class="tc-form-section__heading">
        <div>
            <p class="tc-form-section__eyebrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><rect x="4" y="5" width="16" height="14" rx="2" stroke-width="1.7"/><circle cx="9" cy="10" r="1.4" stroke-width="1.7"/><path d="m6.5 17 4.2-4.2 2.8 2.7 2-2 2 3.5" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>Portada</p>
            <h2>Imagen principal</h2>
            <p>Es opcional, pero facilita que el visitante reconozca la actividad antes de abrir el detalle.</p>
        </div>
        <span class="tc-step-badge tc-step-badge--warm">Paso 2 de 2</span>
    </div>

    <div class="tc-form-cover">
        <div class="tc-form-cover__preview">
            @if($portadaUrl)
                <img id="portada-preview" src="{{ $portadaUrl }}" alt="Portada actual" class="w-full h-full object-cover">
            @else
                <img id="portada-preview" src="" alt="Vista previa de portada" class="w-full h-full object-cover hidden">
                <span id="portada-placeholder" class="text-sm text-center px-4">Sin imagen de portada</span>
            @endif
        </div>
        <div class="tc-form-field">
            <label for="imagen_portada" class="block text-sm font-semibold text-gray-700 mb-1.5">Seleccionar imagen</label>
            <input id="imagen_portada" type="file" name="imagen_portada" accept="image/jpeg,image/png,image/webp" class="tc-input @error('imagen_portada') border-red-400 @enderror">
            <p class="mt-1 text-xs text-gray-400">JPG, PNG o WebP. Tamaño máximo: 5 MB.</p>
            @error('imagen_portada')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror

            @if($portadaUrl)
                <label class="inline-flex items-center gap-2 mt-4 text-sm text-gray-600 cursor-pointer">
                    <input type="hidden" name="quitar_portada" value="0">
                    <input type="checkbox" name="quitar_portada" value="1" @checked(old('quitar_portada')) class="rounded border-gray-300" style="accent-color: var(--color-primary-500);">
                    Quitar la imagen actual
                </label>
            @endif
        </div>
    </div>
</section>

<div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end pb-4">
    <a href="{{ route('operador.actividades.index') }}" class="tc-btn-outline justify-center">Cancelar</a>
    <button type="submit" class="tc-btn-primary justify-center">{{ $submitLabel }}</button>
</div>

@push('scripts')
    <script>
        (function () {
            var imageInput = document.getElementById('imagen_portada');

            imageInput?.addEventListener('change', function (event) {
                var file = event.target.files?.[0];

                if (!file) {
                    return;
                }

                var preview = document.getElementById('portada-preview');
                var placeholder = document.getElementById('portada-placeholder');
                preview.src = URL.createObjectURL(file);
                preview.classList.remove('hidden');
                placeholder?.classList.add('hidden');
            });

            var apiKey = @json($googleMapsApiKey);
            var mapCanvas = document.getElementById('actividad-form-map');
            var mapStatus = document.getElementById('actividad-map-status');
            var latInput = document.getElementById('latitud');
            var lngInput = document.getElementById('longitud');
            var searchInput = document.getElementById('actividad-map-search');

            if (!mapCanvas || !latInput || !lngInput || !apiKey) {
                return;
            }

            var map;
            var marker;
            var initialLat = Number.parseFloat(latInput.value);
            var initialLng = Number.parseFloat(lngInput.value);
            var hasInitialPosition = Number.isFinite(initialLat) && Number.isFinite(initialLng);
            var peruCenter = { lat: -9.19, lng: -75.0152 };
            var suggestionsList = document.getElementById('actividad-map-suggestions');
            var searchRequestId = 0;

            function showMapStatus(message) {
                mapStatus.textContent = message;
                mapStatus.classList.remove('hidden');
            }

            function updateCoordinateInputs(position) {
                latInput.value = position.lat.toFixed(7);
                lngInput.value = position.lng.toFixed(7);
            }

            function updateMarker(position, centerMap) {
                if (!map || !marker) {
                    return;
                }

                marker.position = position;
                marker.map = map;
                updateCoordinateInputs(position);

                if (centerMap) {
                    map.panTo(position);
                    map.setZoom(16);
                }
            }

            function coordinatesFromInputs() {
                var latitude = Number.parseFloat(latInput.value);
                var longitude = Number.parseFloat(lngInput.value);

                if (!Number.isFinite(latitude) || !Number.isFinite(longitude)) {
                    return null;
                }

                return { lat: latitude, lng: longitude };
            }

            function loadGoogleMapsSdk() {
                if (window.google && window.google.maps && typeof window.google.maps.importLibrary === 'function') {
                    return Promise.resolve();
                }

                return new Promise(function (resolve, reject) {
                    var mapsScript = document.createElement('script');
                    mapsScript.src = 'https://maps.googleapis.com/maps/api/js?key=' + encodeURIComponent(apiKey) + '&v=weekly';
                    mapsScript.async = true;
                    mapsScript.defer = true;
                    mapsScript.onload = function () {
                        if (window.google && window.google.maps && typeof window.google.maps.importLibrary === 'function') {
                            resolve();
                            return;
                        }

                        reject(new Error('Google Maps no expuso importLibrary.'));
                    };
                    mapsScript.onerror = function () {
                        reject(new Error('No se pudo descargar Google Maps.'));
                    };
                    document.head.appendChild(mapsScript);
                });
            }

            function hideSuggestions() {
                suggestionsList?.classList.add('hidden');
            }

            function setSearchUnavailable() {
                if (!searchInput) {
                    return;
                }

                searchInput.disabled = true;
                searchInput.placeholder = 'Búsqueda no disponible; selecciona el punto en el mapa';
                hideSuggestions();
            }

            function addPlaceSuggestion(prediction, Place) {
                if (!suggestionsList || !prediction) {
                    return;
                }

                var label = prediction.text?.text || prediction.mainText?.text || 'Lugar sugerido';
                var item = document.createElement('li');
                var button = document.createElement('button');
                var icon = document.createElement('span');
                var text = document.createElement('span');

                button.type = 'button';
                button.className = 'flex w-full items-center gap-2 border-b border-slate-100 px-4 py-3 text-left text-sm text-slate-700 transition last:border-b-0 hover:bg-[#e6f4f5] hover:text-[#054048]';
                icon.className = 'shrink-0 text-[#00626A]';
                icon.setAttribute('aria-hidden', 'true');
                icon.textContent = '⌖';
                text.textContent = label;
                button.append(icon, text);

                button.addEventListener('click', async function () {
                    try {
                        var place = typeof prediction.toPlace === 'function'
                            ? prediction.toPlace()
                            : new Place({ id: prediction.placeId });

                        await place.fetchFields({ fields: ['location'] });

                        if (!place.location) {
                            return;
                        }

                        if (searchInput) {
                            searchInput.value = label;
                        }

                        hideSuggestions();
                        mapStatus.classList.add('hidden');
                        updateMarker({
                            lat: place.location.lat(),
                            lng: place.location.lng()
                        }, true);
                    } catch (error) {
                        setSearchUnavailable();
                    }
                });

                item.appendChild(button);
                suggestionsList.appendChild(item);
            }

            function connectPlacesSearch(placesLibrary) {
                if (!searchInput || !suggestionsList || !placesLibrary.AutocompleteSuggestion) {
                    setSearchUnavailable();
                    return;
                }

                var Place = placesLibrary.Place;
                var AutocompleteSuggestion = placesLibrary.AutocompleteSuggestion;

                searchInput.addEventListener('input', async function () {
                    var query = searchInput.value.trim();
                    var requestId = ++searchRequestId;

                    if (query.length < 3) {
                        suggestionsList.replaceChildren();
                        hideSuggestions();
                        return;
                    }

                    try {
                        var response = await AutocompleteSuggestion.fetchAutocompleteSuggestions({
                            input: query,
                            includedRegionCodes: ['pe'],
                            language: 'es',
                            region: 'pe'
                        });

                        if (requestId !== searchRequestId) {
                            return;
                        }

                        suggestionsList.replaceChildren();
                        (response.suggestions || []).forEach(function (suggestion) {
                            addPlaceSuggestion(suggestion.placePrediction, Place);
                        });

                        if (suggestionsList.childElementCount > 0) {
                            suggestionsList.classList.remove('hidden');
                        } else {
                            hideSuggestions();
                        }
                    } catch (error) {
                        if (requestId === searchRequestId) {
                            setSearchUnavailable();
                        }
                    }
                });

                searchInput.addEventListener('keydown', function (event) {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                    }
                });

                document.addEventListener('click', function (event) {
                    if (event.target !== searchInput && !suggestionsList.contains(event.target)) {
                        hideSuggestions();
                    }
                });
            }

            async function initializeMap() {
                try {
                    await loadGoogleMapsSdk();

                    var mapsLibrary = await google.maps.importLibrary('maps');
                    var markerLibrary = await google.maps.importLibrary('marker');
                    var Map = mapsLibrary.Map;
                    var AdvancedMarkerElement = markerLibrary.AdvancedMarkerElement;
                    var startPosition = hasInitialPosition ? { lat: initialLat, lng: initialLng } : peruCenter;

                    map = new Map(mapCanvas, {
                    center: startPosition,
                    zoom: hasInitialPosition ? 16 : 6,
                    mapId: 'DEMO_MAP_ID',
                    streetViewControl: false,
                    mapTypeControl: false,
                    fullscreenControl: false,
                    gestureHandling: 'cooperative'
                    });

                    marker = new AdvancedMarkerElement({
                        map: hasInitialPosition ? map : null,
                        position: hasInitialPosition ? startPosition : null,
                        gmpDraggable: true,
                        title: 'Punto de encuentro de la actividad'
                    });

                    map.addListener('click', function (event) {
                        updateMarker({ lat: event.latLng.lat(), lng: event.latLng.lng() }, false);
                    });

                    marker.addListener('dragend', function (event) {
                        updateMarker({ lat: event.latLng.lat(), lng: event.latLng.lng() }, false);
                    });

                    mapStatus.classList.add('hidden');

                    try {
                        var placesLibrary = await google.maps.importLibrary('places');
                        connectPlacesSearch(placesLibrary);
                    } catch (error) {
                        setSearchUnavailable();
                    }

                    [latInput, lngInput].forEach(function (input) {
                        input.addEventListener('change', function () {
                            var manualPosition = coordinatesFromInputs();

                            if (manualPosition) {
                                mapStatus.classList.add('hidden');
                                updateMarker(manualPosition, true);
                            }
                        });
                    });
                } catch (error) {
                    showMapStatus('No se pudo cargar Google Maps. Puedes registrar las coordenadas manualmente y continuar.');
                    setSearchUnavailable();
                }
            }

            initializeMap();
        })();
    </script>
@endpush
