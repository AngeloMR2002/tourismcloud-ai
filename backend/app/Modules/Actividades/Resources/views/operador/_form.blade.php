@php
    $portadaUrl = $actividad?->portada_url;
    $googleMapsApiKey = trim((string) config('services.google_maps.key', ''));
    $latitudInicial = old('latitud', $actividad?->latitud);
    $longitudInicial = old('longitud', $actividad?->longitud);
@endphp

@if($errors->any())
    <div class="tc-alert-error" role="alert">
        {{ __('operator.review_marked_fields') }}
    </div>
@endif

<section class="tc-card tc-form-section p-5 sm:p-6">
    <div class="tc-form-section__heading">
        <div>
            <p class="tc-form-section__eyebrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M5 20V4h10l4 4v12H5Z" stroke-width="1.7" stroke-linejoin="round"/><path d="M15 4v4h4M8 13h8M8 17h5" stroke-width="1.7" stroke-linecap="round"/></svg>{{ __('operator.activity') }}</p>
            <h2>{{ __('operator.experience_information') }}</h2>
            <p>{{ __('operator.experience_information_hint') }}</p>
        </div>
        <span class="tc-step-badge">{{ __('operator.step_one_of_two') }}</span>
    </div>
    <div class="tc-form-grid tc-form-grid--two">
        <div class="tc-form-field tc-form-field--wide">
            <label for="destino_id" class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('operator.destination') }} <span class="text-red-600">*</span></label>
            <select id="destino_id" name="destino_id" class="tc-select @error('destino_id') border-red-400 @enderror" required>
                <option value="">{{ __('operator.select_destination') }}</option>
                @foreach($destinos as $destino)
                    <option value="{{ $destino->id }}" @selected((string) old('destino_id', $actividad?->destino_id) === (string) $destino->id)>
                        {{ $destino->nombre }}{{ $destino->estado !== 'activo' ? ' ('.__('operator.inactive_keep_only').')' : '' }}
                    </option>
                @endforeach
            </select>
            @error('destino_id')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="tc-form-field tc-form-field--wide">
            <label for="nombre" class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('operator.activity_name') }} <span class="text-red-600">*</span></label>
            <input id="nombre" name="nombre" value="{{ old('nombre', $actividad?->nombre) }}" maxlength="150" placeholder="{{ __('operator.activity_name_example') }}" class="tc-input @error('nombre') border-red-400 @enderror" required>
            @error('nombre')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="tc-form-field tc-form-field--wide">
            <label for="descripcion" class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('operator.description') }}</label>
            <textarea id="descripcion" name="descripcion" rows="5" maxlength="5000" class="tc-input @error('descripcion') border-red-400 @enderror" placeholder="{{ __('operator.activity_description_hint') }}">{{ old('descripcion', $actividad?->descripcion) }}</textarea>
            @error('descripcion')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
        </div>

        <fieldset class="tc-form-field tc-form-field--wide">
            <legend class="mb-2 block text-sm font-semibold text-gray-700">{{ __('operator.activity_categories') }}</legend>
            <p class="mb-3 text-xs text-gray-500">{{ __('operator.activity_categories_hint') }}</p>
            @if($categorias->isEmpty())
                <p class="text-sm text-gray-500">{{ __('operator.no_activity_categories') }}</p>
            @else
                <div class="grid gap-2 sm:grid-cols-2">
                    @foreach($categorias as $categoria)
                        <label class="flex items-center gap-2 rounded-xl border border-gray-200 px-3 py-2 text-sm text-gray-700">
                            <input
                                type="checkbox"
                                name="categorias[]"
                                value="{{ $categoria->id }}"
                                @checked(in_array((string) $categoria->id, array_map('strval', old('categorias', $actividad?->categorias->modelKeys() ?? [])), true))
                                class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                            >
                            <span>{{ $categoria->nombre }}</span>
                        </label>
                    @endforeach
                </div>
            @endif
            @error('categorias')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
            @error('categorias.*')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
        </fieldset>

        <div class="tc-form-field">
            <label for="costo" data-tc-money-label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('operator.estimated_cost_pen') }} <span class="text-red-600">*</span></label>
            <input id="costo" data-tc-money-input type="number" name="costo" min="0" max="99999999.99" step="0.01" value="{{ old('costo', $actividad?->costo ?? '0.00') }}" class="tc-input @error('costo') border-red-400 @enderror" required>
            <p class="tc-form-field__hint">{{ __('operator.free_cost_hint') }}</p>
            @error('costo')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="tc-form-field">
            <label for="duracion_estimada_min" class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('operator.estimated_duration_minutes') }}</label>
            <input id="duracion_estimada_min" type="number" name="duracion_estimada_min" min="1" max="14400" value="{{ old('duracion_estimada_min', $actividad?->duracion_estimada_min) }}" class="tc-input @error('duracion_estimada_min') border-red-400 @enderror" placeholder="{{ __('operator.example_duration') }}">
            @error('duracion_estimada_min')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="tc-form-field tc-form-field--wide">
            <div class="tc-form-location">
                <span class="tc-form-location__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 20s6-5.2 6-10a6 6 0 1 0-12 0c0 4.8 6 10 6 10Z" stroke-width="1.7" stroke-linejoin="round"/><circle cx="12" cy="10" r="2" stroke-width="1.7"/></svg></span>
                <div>
                    <strong>{{ __('operator.meeting_place') }}</strong>
                    <p>{{ __('operator.meeting_place_hint') }}</p>
                </div>
            </div>
        </div>

        <div class="tc-form-field tc-form-field--wide">
            <div class="relative overflow-hidden rounded-2xl border border-[#16734f]/10 bg-[#daf2e6] shadow-sm">
                <div class="absolute left-4 right-4 top-4 z-10 sm:right-auto sm:w-[25rem]">
                    <label for="actividad-map-search" class="sr-only">{{ __('operator.search_meeting_place') }}</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-[#16734f]" aria-hidden="true">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="11" cy="11" r="6.5" stroke-width="1.8"/><path d="m16 16 4 4" stroke-width="1.8" stroke-linecap="round"/></svg>
                        </span>
                        <input id="actividad-map-search" type="search" autocomplete="off" placeholder="{{ __('operator.search_meeting_placeholder') }}" @disabled(! $googleMapsApiKey) class="tc-input w-full bg-white/95 py-3 pl-11 pr-4 shadow-lg backdrop-blur-sm disabled:cursor-not-allowed disabled:opacity-75">
                    </div>
                    <ul id="actividad-map-suggestions" class="mt-2 hidden max-h-60 overflow-y-auto rounded-xl border border-slate-100 bg-white shadow-xl" aria-label="{{ __('operator.place_suggestions') }}"></ul>
                </div>
                <div id="actividad-form-map" class="h-[21rem] w-full sm:h-[25rem]" aria-label="{{ __('operator.activity_location_map') }}"></div>
                <div id="actividad-map-status" class="absolute inset-0 flex items-center justify-center bg-[#daf2e6] p-6 text-center text-sm leading-6 text-[#105c3e]" @if($googleMapsApiKey) aria-live="polite" @endif>
                    @if($googleMapsApiKey)
                        {{ __('operator.loading_location_picker') }}
                    @else
                        <div class="max-w-md">
                            <strong class="block text-base text-[#124e37]">{{ __('operator.map_picker_not_configured') }}</strong>
                            <p class="mt-2">{{ __('operator.manual_coordinates_hint') }}</p>
                        </div>
                    @endif
                </div>
            </div>
            <p class="tc-form-field__hint mt-2">{{ __('operator.coordinates_optional_hint') }}</p>
        </div>

        <div class="tc-form-field">
            <label for="latitud" class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('operator.latitude') }}</label>
            <input id="latitud" type="number" name="latitud" step="0.0000001" min="-90" max="90" value="{{ $latitudInicial }}" class="tc-input @error('latitud') border-red-400 @enderror" placeholder="{{ __('catalog.example_value', ['value' => '-9.085']) }}">
            @error('latitud')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
            <p class="tc-form-field__hint">{{ __('operator.latitude_hint') }}</p>
        </div>

        <div class="tc-form-field">
            <label for="longitud" class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('operator.longitude') }}</label>
            <input id="longitud" type="number" name="longitud" step="0.0000001" min="-180" max="180" value="{{ $longitudInicial }}" class="tc-input @error('longitud') border-red-400 @enderror" placeholder="{{ __('catalog.example_value', ['value' => '-78.565']) }}">
            @error('longitud')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="tc-form-field tc-form-field--wide">
            <label for="estado" class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('operator.status') }} <span class="text-red-600">*</span></label>
            <select id="estado" name="estado" class="tc-select @error('estado') border-red-400 @enderror" required>
                <option value="activo" @selected(old('estado', $actividad?->estado ?? 'activo') === 'activo')>{{ __('operator.active_status') }}</option>
                <option value="inactivo" @selected(old('estado', $actividad?->estado) === 'inactivo')>{{ __('operator.inactive_status') }}</option>
            </select>
            @error('estado')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>
</section>

<section class="tc-card tc-form-section p-5 sm:p-6">
    <div class="tc-form-section__heading">
        <div>
            <p class="tc-form-section__eyebrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><rect x="4" y="5" width="16" height="14" rx="2" stroke-width="1.7"/><circle cx="9" cy="10" r="1.4" stroke-width="1.7"/><path d="m6.5 17 4.2-4.2 2.8 2.7 2-2 2 3.5" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>{{ __('operator.cover') }}</p>
            <h2>{{ __('operator.main_image') }}</h2>
            <p>{{ __('operator.cover_hint') }}</p>
        </div>
        <span class="tc-step-badge tc-step-badge--warm">{{ __('operator.step_two_of_two') }}</span>
    </div>

    <div class="tc-form-cover">
        <div class="tc-form-cover__preview">
            @if($portadaUrl)
                <img id="portada-preview" src="{{ $portadaUrl }}" alt="{{ __('operator.current_cover') }}" class="w-full h-full object-cover">
            @else
                <img id="portada-preview" src="" alt="{{ __('operator.cover_preview') }}" class="w-full h-full object-cover hidden">
                <span id="portada-placeholder" class="text-sm text-center px-4">{{ __('operator.no_cover') }}</span>
            @endif
        </div>
        <div class="tc-form-field">
            <label for="imagen_portada" class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('operator.select_image') }}</label>
            <input id="imagen_portada" type="file" name="imagen_portada" accept="image/jpeg,image/png,image/webp" class="tc-input @error('imagen_portada') border-red-400 @enderror">
            <p class="mt-1 text-xs text-gray-400">{{ __('operator.image_limit') }}</p>
            @error('imagen_portada')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror

            @if($portadaUrl)
                <label class="inline-flex items-center gap-2 mt-4 text-sm text-gray-600 cursor-pointer">
                    <input type="hidden" name="quitar_portada" value="0">
                    <input type="checkbox" name="quitar_portada" value="1" @checked(old('quitar_portada')) class="rounded border-gray-300" style="accent-color: var(--color-primary-500);">
                    {{ __('operator.remove_current_image') }}
                </label>
            @endif
        </div>
    </div>
</section>

<div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end pb-4">
    <a href="{{ route('operador.actividades.index') }}" class="tc-btn-outline justify-center">{{ __('operator.cancel') }}</a>
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
            var copy = @json(trans('operator'));
            var locale = @json(app()->getLocale());
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
                    mapsScript.src = 'https://maps.googleapis.com/maps/api/js?key=' + encodeURIComponent(apiKey) + '&v=weekly&language=' + encodeURIComponent(locale);
                    mapsScript.async = true;
                    mapsScript.defer = true;
                    mapsScript.onload = function () {
                        if (window.google && window.google.maps && typeof window.google.maps.importLibrary === 'function') {
                            resolve();
                            return;
                        }

                        reject(new Error(copy.google_maps_import_error));
                    };
                    mapsScript.onerror = function () {
                        reject(new Error(copy.google_maps_download_error));
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
                searchInput.placeholder = copy.search_unavailable;
                hideSuggestions();
            }

            function addPlaceSuggestion(prediction, Place) {
                if (!suggestionsList || !prediction) {
                    return;
                }

                var label = prediction.text?.text || prediction.mainText?.text || copy.suggested_place;
                var item = document.createElement('li');
                var button = document.createElement('button');
                var icon = document.createElement('span');
                var text = document.createElement('span');

                button.type = 'button';
                button.className = 'flex w-full items-center gap-2 border-b border-slate-100 px-4 py-3 text-left text-sm text-slate-700 transition last:border-b-0 hover:bg-[#daf2e6] hover:text-[#124e37]';
                icon.className = 'shrink-0 text-[#16734f]';
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
                            language: locale,
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
                        title: copy.activity_meeting_marker
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
                    showMapStatus(copy.google_maps_load_error);
                    setSearchUnavailable();
                }
            }

            initializeMap();
        })();
    </script>
@endpush
