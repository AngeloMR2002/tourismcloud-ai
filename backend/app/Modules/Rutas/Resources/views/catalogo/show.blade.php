@extends('layouts.app', ['title' => $ruta->nombre])

@section('content')
    @php
        $portadaUrl = $ruta->portada_url;
        $minutos = $ruta->duracion_estimada_min;
        $horas = $minutos ? intdiv($minutos, 60) : 0;
        $restoMinutos = $minutos ? $minutos % 60 : 0;
        $duracion = $minutos
            ? (($horas > 0 ? $horas.' h' : '').($horas > 0 && $restoMinutos > 0 ? ' ' : '').($restoMinutos > 0 ? $restoMinutos.' min' : ''))
            : __('catalog.duration_unknown');
        $paradasMapa = $ruta->paradas->map(function ($parada) {
            return [
                'orden' => (int) $parada->orden,
                'nombre' => $parada->atractivo?->nombre ?? __('catalog.stop'),
                'latitud' => $parada->atractivo?->latitud,
                'longitud' => $parada->atractivo?->longitud,
            ];
        })->values()->all();
        $googleMapsApiKey = trim((string) config('services.google_maps.key', ''));
    @endphp

    <div class="tc-catalog-shell max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        <nav class="mb-5 flex flex-wrap items-center gap-2 text-sm" aria-label="{{ __('catalog.breadcrumbs') }}">
            <a href="{{ route('catalogo.rutas.index') }}" class="font-semibold text-primary-700 hover:underline">{{ __('catalog.routes_title') }}</a>
            <span class="text-gray-400" aria-hidden="true">/</span>
            <span class="truncate text-gray-500">{{ $ruta->nombre }}</span>
        </nav>
        <div class="flex justify-end mb-3"><x-favorite-button type="ruta" :item="$ruta" :inline="true" /></div>

        <header class="tc-page-heading" aria-labelledby="ruta-titulo">
            <p class="text-xs font-semibold uppercase tracking-widest text-primary-700">{{ $ruta->destino->nombre }}</p>
            <h1 id="ruta-titulo" class="mt-2 text-3xl font-bold tracking-tight text-gray-900">{{ $ruta->nombre }}</h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-gray-600">{{ __('catalog.route_intro') }}</p>
        </header>
        @if($portadaUrl)
            <figure class="mt-5 overflow-hidden rounded-2xl bg-gray-100">
                <img src="{{ $portadaUrl }}" alt="{{ $ruta->nombre }}" class="max-h-80 w-full object-cover">
            </figure>
        @endif
        @include('rutas::partials.credito', ['ruta' => $ruta])

        <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1fr)_19rem]">
            <div class="min-w-0 space-y-6">
                <section class="tc-card p-5 sm:p-7">
                    <p class="text-xs font-bold uppercase tracking-[0.13em] text-primary-600">{{ __('catalog.about_route') }}</p>
                    <h2 class="mt-2 text-2xl font-bold text-gray-900">{{ __('catalog.route_section_title') }}</h2>
                    <p class="mt-4 whitespace-pre-line leading-7 text-gray-600">{{ $ruta->descripcion ?: __('catalog.route_no_description') }}</p>
                </section>

                <section class="tc-card p-5 sm:p-7" aria-labelledby="paradas-titulo">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.13em] text-primary-600">{{ __('catalog.route_itinerary') }}</p>
                            <h2 id="paradas-titulo" class="mt-2 text-2xl font-bold text-gray-900">{{ __('catalog.ordered_stops') }}</h2>
                        </div>
                        <span class="tc-filter-chip">{{ $ruta->paradas->count() }} {{ $ruta->paradas->count() === 1 ? __('catalog.stop') : __('catalog.stops') }}</span>
                    </div>
                    <ol class="tc-route-timeline">
                        @foreach($ruta->paradas as $parada)
                            <li class="tc-route-stop">
                                <span class="tc-route-stop__number">{{ $loop->iteration }}</span>
                                <div class="tc-route-stop__content">
                                    <a href="{{ route('catalogo.atractivos.show', $parada->atractivo) }}" class="text-lg font-bold leading-tight text-primary-700 hover:underline break-words">{{ $parada->atractivo->nombre }}</a>
                                    @if($parada->atractivo->descripcion)
                                        <p class="mt-1.5 text-sm leading-6 text-gray-500 break-words">{{ Illuminate\Support\Str::limit($parada->atractivo->descripcion, 180) }}</p>
                                    @endif
                                    <p class="tc-route-stop__entry mt-2"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c1.657 0 3 .895 3 2s-1.343 2-3 2-3 .895-3 2 1.343 2 3 2m0-8c-1.11 0-2.08.402-2.599 1M12 8V6m0 6v2m0 4v-2m0 2c1.11 0 2.08-.402 2.599-1M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>{{ __('catalog.reference_entry') }} S/ {{ number_format((float) $parada->atractivo->costo_entrada, 2) }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                </section>

                @include('rutas::partials.mapa-paradas', [
                    'mapId' => 'mapa-ruta-publica',
                    'mapEyebrow' => __('catalog.stops_location'),
                    'mapTitle' => __('catalog.route_map'),
                    'mapDescription' => __('catalog.route_map_description'),
                    'mapLabel' => __('catalog.route_map_label', ['name' => $ruta->nombre]),
                ])
            </div>

            <aside class="space-y-4 xl:sticky xl:top-24 xl:self-start">
                <section class="tc-card p-5">
                    <p class="text-xs font-bold uppercase tracking-[0.13em] text-primary-700">{{ __('catalog.reference_details') }}</p>
                    <div class="tc-detail-facts mt-4 grid-cols-1 sm:grid-cols-3 xl:grid-cols-1">
                        <div class="tc-fact"><span class="tc-fact__icon" aria-hidden="true"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12h18M7 8l-4 4 4 4m10-8 4 4-4 4"/></svg></span><p class="tc-fact__label">{{ __('catalog.distance') }}</p><p class="tc-fact__value">{{ $ruta->distancia_km !== null ? $ruta->distancia_km.' km' : __('catalog.not_indicated') }}</p></div>
                        <div class="tc-fact"><span class="tc-fact__icon" aria-hidden="true"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6v6l4 2m5-2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg></span><p class="tc-fact__label">{{ __('catalog.duration') }}</p><p class="tc-fact__value">{{ $duracion }}</p></div>
                        <div class="tc-fact"><span class="tc-fact__icon" aria-hidden="true"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="5" cy="5" r="1.5"/><circle cx="19" cy="19" r="1.5"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6.5 5h5a3.5 3.5 0 0 1 0 7h-1a3.5 3.5 0 0 0 0 7h7"/></svg></span><p class="tc-fact__label">{{ __('catalog.stops') }}</p><p class="tc-fact__value">{{ $ruta->paradas->count() }}</p></div>
                    </div>
                </section>
                <section class="tc-card p-5">
                    <p class="text-xs font-bold uppercase tracking-[0.13em] text-primary-700">{{ __('catalog.route_entries') }}</p>
                    <p class="mt-2 text-3xl font-extrabold text-primary-700">S/ {{ number_format((float) $ruta->costo_entradas_referencial, 2) }}</p>
                    <p class="mt-3 text-sm leading-6 text-gray-500">{{ __('catalog.route_entries_note') }}</p>
                </section>
                <div class="tc-note rounded-r-lg p-3 text-xs leading-5">{{ __('catalog.route_manual_note') }}</div>
                <a href="{{ route('catalogo.rutas.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-primary-700 hover:underline"><span aria-hidden="true">←</span> {{ __('catalog.continue_routes') }}</a>
            </aside>
        </div>
    </div>
@endsection

@push('scripts')
@include('rutas::partials.mapa-paradas-scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const mapaRuta = window.TourismCloudRouteStopsMap.create({
            mapId: 'mapa-ruta-publica',
            apiKey: @json($googleMapsApiKey),
        });

        mapaRuta.actualizar({{ Illuminate\Support\Js::from($paradasMapa) }});
    });
</script>
@endpush
