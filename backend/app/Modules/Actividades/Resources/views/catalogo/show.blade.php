@extends('layouts.app', ['title' => $actividad->nombre])

@section('content')
    @php
        $portadaUrl = $actividad->portada_url;
        $minutos = $actividad->duracion_estimada_min;
        $horas = $minutos ? intdiv($minutos, 60) : 0;
        $restoMinutos = $minutos ? $minutos % 60 : 0;
        $duracion = $minutos
            ? (($horas > 0 ? $horas.' h' : '').($horas > 0 && $restoMinutos > 0 ? ' ' : '').($restoMinutos > 0 ? $restoMinutos.' min' : ''))
            : 'Por confirmar';
        $esFotoPlazaDemo = $actividad->imagen_portada === 'actividades-rutas-demo/cusco-plaza.jpg';
        $esFotoSacsayhuamanDemo = $actividad->imagen_portada === 'actividades-rutas-demo/cusco-sacsayhuaman.jpg';
        $tieneCoordenadas = $actividad->ubicacion_url !== null;
        $googleMapsApiKey = trim((string) config('services.google_maps.key', ''));
    @endphp

    <div class="tc-catalog-shell max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        <nav class="mb-5 flex flex-wrap items-center gap-2 text-sm" aria-label="Migas de pan">
            <a href="{{ route('catalogo.actividades.index') }}" class="font-semibold text-primary-700 hover:underline">Actividades</a>
            <span class="text-gray-400" aria-hidden="true">/</span>
            <span class="truncate text-gray-500">{{ $actividad->nombre }}</span>
        </nav>

        <header class="tc-page-heading" aria-labelledby="actividad-titulo">
            <p class="text-xs font-semibold uppercase tracking-widest text-primary-700">{{ $actividad->destino->nombre }}</p>
            <h1 id="actividad-titulo" class="mt-2 text-3xl font-bold tracking-tight text-gray-900">{{ $actividad->nombre }}</h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-gray-600">Consulta la descripción, el costo referencial y la ubicación de esta actividad.</p>
        </header>
        @if($portadaUrl)
            <figure class="mt-5 overflow-hidden rounded-2xl bg-gray-100">
                <img src="{{ $portadaUrl }}" alt="{{ $actividad->nombre }}" class="max-h-80 w-full object-cover">
            </figure>
        @endif

        @if($portadaUrl && $esFotoPlazaDemo)
            <p class="mt-2 text-xs text-gray-500">Foto de contexto: <a href="https://commons.wikimedia.org/wiki/File:The_Plaza_de_Armas_of_Cusco.jpg" target="_blank" rel="noopener noreferrer" class="underline">Entropy1963</a>, dominio público.</p>
        @elseif($portadaUrl && $esFotoSacsayhuamanDemo)
            <p class="mt-2 text-xs text-gray-500">Foto de contexto: <a href="https://commons.wikimedia.org/wiki/File:Sacsayhuaman_in_Cusco.jpg" target="_blank" rel="noopener noreferrer" class="underline">Filipe Fortes</a>, <a href="https://creativecommons.org/licenses/by-sa/2.0/" target="_blank" rel="license noopener noreferrer" class="underline">CC BY-SA 2.0</a>.</p>
        @endif

        <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1fr)_19rem]">
            <div class="min-w-0 space-y-6">
                <section class="tc-card p-5 sm:p-7">
                    <p class="text-xs font-bold uppercase tracking-[0.13em] text-primary-600">Sobre esta experiencia</p>
                    <h2 class="mt-2 text-2xl font-bold text-gray-900">Información para planificar tu visita</h2>
                    @if($actividad->descripcion)
                        <p class="mt-4 whitespace-pre-line leading-7 text-gray-600">{{ $actividad->descripcion }}</p>
                    @else
                        <p class="mt-4 leading-7 text-gray-500">El operador aún no registró una descripción detallada para esta actividad.</p>
                    @endif
                </section>

                @if($tieneCoordenadas)
                    <section id="ubicacion" class="overflow-hidden rounded-2xl border border-[#00626A]/10 bg-white shadow-[0_10px_28px_rgba(5,64,72,.06)]">
                        <div class="flex flex-col gap-5 p-5 sm:flex-row sm:items-center sm:justify-between sm:p-6">
                            <div class="flex items-start gap-3">
                                <span class="tc-fact__icon shrink-0" aria-hidden="true"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 21s7-5.2 7-12a7 7 0 1 0-14 0c0 6.8 7 12 7 12Z"/><circle cx="12" cy="9" r="2.2"/></svg></span>
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-[.13em] text-primary-600">Punto de encuentro</p>
                                    <h2 class="mt-1 font-bold text-gray-900">Ubicación de la actividad</h2>
                                    <p class="mt-1 text-sm leading-6 text-gray-500">El operador publicó este punto como referencia para iniciar o realizar la experiencia.</p>
                                </div>
                            </div>
                            <a href="{{ $actividad->ubicacion_url }}" target="_blank" rel="noopener noreferrer" class="tc-btn-outline justify-center shrink-0">Abrir en Google Maps <span aria-hidden="true">↗</span></a>
                        </div>
                        <div class="relative h-72 border-t border-slate-100 bg-[#e6f4f5]">
                            <div id="actividad-public-map" class="h-full w-full" aria-label="Mapa del punto de encuentro de {{ $actividad->nombre }}"></div>
                            <div id="actividad-public-map-status" class="absolute inset-0 flex items-center justify-center bg-[#e6f4f5] p-6 text-center text-sm leading-6 text-[#08565D]" @if($googleMapsApiKey) aria-live="polite" @endif>
                                @if($googleMapsApiKey)
                                    Cargando la ubicación de la actividad…
                                @else
                                    <div class="max-w-md">
                                        <strong class="block text-base text-[#054048]">La vista interactiva aún no está configurada.</strong>
                                        <p class="mt-2">Puedes abrir este punto directamente en Google Maps.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </section>
                @endif
            </div>

            <aside class="space-y-4 xl:sticky xl:top-24 xl:self-start">
                <section class="tc-card p-5">
                    <p class="text-xs font-bold uppercase tracking-[0.13em] text-primary-700">Datos referenciales</p>
                    <div class="tc-detail-facts mt-4 grid-cols-1 sm:grid-cols-2 xl:grid-cols-1">
                        <div class="tc-fact">
                            <span class="tc-fact__icon" aria-hidden="true"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6v6l4 2m5-2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg></span>
                            <p class="tc-fact__label">Duración estimada</p>
                            <p class="tc-fact__value">{{ $duracion }}</p>
                        </div>
                        <div class="tc-fact">
                            <span class="tc-fact__icon" aria-hidden="true"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c1.657 0 3 .895 3 2s-1.343 2-3 2-3 .895-3 2 1.343 2 3 2m0-8c-1.11 0-2.08.402-2.599 1M12 8V6m0 6v2m0 4v-2m0 2c1.11 0 2.08-.402 2.599-1M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg></span>
                            <p class="tc-fact__label">Costo referencial</p>
                            <p class="tc-fact__value">{{ $actividad->costo > 0 ? 'S/ '.number_format((float) $actividad->costo, 2) : 'Gratuita' }}</p>
                        </div>
                    </div>
                    <div class="tc-note mt-5 rounded-r-lg p-3 text-xs leading-5">La información es referencial. Confirma disponibilidad, horarios y precio final directamente con el proveedor.</div>
                </section>
                <a href="{{ route('catalogo.actividades.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-primary-700 hover:underline"><span aria-hidden="true">←</span> Seguir explorando actividades</a>
            </aside>
        </div>
    </div>
@endsection

@if($tieneCoordenadas)
    @push('scripts')
        <script>
            (function () {
                var apiKey = @json($googleMapsApiKey);
                var mapCanvas = document.getElementById('actividad-public-map');
                var mapStatus = document.getElementById('actividad-public-map-status');
                var position = {
                    lat: Number(@json((float) $actividad->latitud)),
                    lng: Number(@json((float) $actividad->longitud))
                };

                if (!mapCanvas || !mapStatus || !apiKey) {
                    return;
                }

                function showFallback(message) {
                    mapStatus.textContent = message;
                    mapStatus.classList.remove('hidden');
                }

                function initializeMap() {
                    if (!window.google || !window.google.maps) {
                        showFallback('No se pudo cargar la vista interactiva. Puedes abrir esta ubicación en Google Maps.');
                        return;
                    }

                    var map = new google.maps.Map(mapCanvas, {
                        center: position,
                        zoom: 15,
                        disableDefaultUI: true,
                        zoomControl: true,
                        gestureHandling: 'cooperative',
                        styles: [
                            { featureType: 'poi', elementType: 'labels', stylers: [{ visibility: 'off' }] },
                            { featureType: 'transit', elementType: 'labels', stylers: [{ visibility: 'off' }] },
                            { featureType: 'water', elementType: 'geometry', stylers: [{ color: '#d9ebed' }] },
                            { featureType: 'landscape', elementType: 'geometry', stylers: [{ color: '#f7faf9' }] }
                        ]
                    });

                    new google.maps.Marker({
                        position: position,
                        map: map,
                        title: @json($actividad->nombre)
                    });

                    mapStatus.classList.add('hidden');
                }

                if (window.google && window.google.maps) {
                    initializeMap();
                    return;
                }

                var mapsScript = document.createElement('script');
                mapsScript.src = 'https://maps.googleapis.com/maps/api/js?key=' + encodeURIComponent(apiKey) + '&v=weekly';
                mapsScript.async = true;
                mapsScript.defer = true;
                mapsScript.onload = initializeMap;
                mapsScript.onerror = function () {
                    showFallback('No se pudo cargar la vista interactiva. Puedes abrir esta ubicación en Google Maps.');
                };
                document.head.appendChild(mapsScript);
            })();
        </script>
    @endpush
@endif
