@extends('layouts.app', ['title' => $ruta->nombre])

@section('content')
    @php
        $portadaUrl = $ruta->portada_url;
        $minutos = $ruta->duracion_estimada_min;
        $horas = $minutos ? intdiv($minutos, 60) : 0;
        $restoMinutos = $minutos ? $minutos % 60 : 0;
        $duracion = $minutos
            ? (($horas > 0 ? $horas.' h' : '').($horas > 0 && $restoMinutos > 0 ? ' ' : '').($restoMinutos > 0 ? $restoMinutos.' min' : ''))
            : 'Por confirmar';
        $paradasMapa = $ruta->paradas->map(function ($parada) {
            return [
                'orden' => (int) $parada->orden,
                'nombre' => $parada->atractivo?->nombre ?? 'Parada',
                'latitud' => $parada->atractivo?->latitud,
                'longitud' => $parada->atractivo?->longitud,
            ];
        })->values()->all();
        $googleMapsApiKey = trim((string) config('services.google_maps.key', ''));
    @endphp

    <div class="tc-catalog-shell max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        <nav class="mb-5 flex flex-wrap items-center gap-2 text-sm" aria-label="Migas de pan">
            <a href="{{ route('catalogo.rutas.index') }}" class="font-semibold text-primary-700 hover:underline">Rutas</a>
            <span class="text-gray-400" aria-hidden="true">/</span>
            <span class="truncate text-gray-500">{{ $ruta->nombre }}</span>
        </nav>

        <header class="tc-page-heading" aria-labelledby="ruta-titulo">
            <p class="text-xs font-semibold uppercase tracking-widest text-primary-700">{{ $ruta->destino->nombre }}</p>
            <h1 id="ruta-titulo" class="mt-2 text-3xl font-bold tracking-tight text-gray-900">{{ $ruta->nombre }}</h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-gray-600">Consulta las paradas en orden, la duración y la distancia de este recorrido.</p>
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
                    <p class="text-xs font-bold uppercase tracking-[0.13em] text-primary-600">Sobre el recorrido</p>
                    <h2 class="mt-2 text-2xl font-bold text-gray-900">Un trayecto, varias paradas</h2>
                    <p class="mt-4 whitespace-pre-line leading-7 text-gray-600">{{ $ruta->descripcion ?: 'El operador todavía no ha agregado una descripción detallada para este recorrido.' }}</p>
                </section>

                <section class="tc-card p-5 sm:p-7" aria-labelledby="paradas-titulo">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.13em] text-primary-600">Itinerario de la ruta</p>
                            <h2 id="paradas-titulo" class="mt-2 text-2xl font-bold text-gray-900">Paradas en orden</h2>
                        </div>
                        <span class="tc-filter-chip">{{ $ruta->paradas->count() }} {{ $ruta->paradas->count() === 1 ? 'parada' : 'paradas' }}</span>
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
                                    <p class="tc-route-stop__entry mt-2"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c1.657 0 3 .895 3 2s-1.343 2-3 2-3 .895-3 2 1.343 2 3 2m0-8c-1.11 0-2.08.402-2.599 1M12 8V6m0 6v2m0 4v-2m0 2c1.11 0 2.08-.402 2.599-1M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>Entrada referencial: S/ {{ $parada->atractivo->costo_entrada }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                </section>

                @include('rutas::partials.mapa-paradas', [
                    'mapId' => 'mapa-ruta-publica',
                    'mapEyebrow' => 'Ubicación de las paradas',
                    'mapTitle' => 'Mapa del recorrido',
                    'mapDescription' => 'Cada marcador corresponde a un atractivo de la ruta y conserva el orden publicado por el operador.',
                    'mapLabel' => 'Mapa con las paradas de ' . $ruta->nombre,
                ])
            </div>

            <aside class="space-y-4 xl:sticky xl:top-24 xl:self-start">
                <section class="tc-card p-5">
                    <p class="text-xs font-bold uppercase tracking-[0.13em] text-primary-700">Datos referenciales</p>
                    <div class="tc-detail-facts mt-4 grid-cols-1 sm:grid-cols-3 xl:grid-cols-1">
                        <div class="tc-fact"><span class="tc-fact__icon" aria-hidden="true"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12h18M7 8l-4 4 4 4m10-8 4 4-4 4"/></svg></span><p class="tc-fact__label">Distancia</p><p class="tc-fact__value">{{ $ruta->distancia_km !== null ? $ruta->distancia_km.' km' : 'No indicada' }}</p></div>
                        <div class="tc-fact"><span class="tc-fact__icon" aria-hidden="true"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6v6l4 2m5-2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg></span><p class="tc-fact__label">Duración</p><p class="tc-fact__value">{{ $duracion }}</p></div>
                        <div class="tc-fact"><span class="tc-fact__icon" aria-hidden="true"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="5" cy="5" r="1.5"/><circle cx="19" cy="19" r="1.5"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6.5 5h5a3.5 3.5 0 0 1 0 7h-1a3.5 3.5 0 0 0 0 7h7"/></svg></span><p class="tc-fact__label">Paradas</p><p class="tc-fact__value">{{ $ruta->paradas->count() }}</p></div>
                    </div>
                </section>
                <section class="tc-card p-5">
                    <p class="text-xs font-bold uppercase tracking-[0.13em] text-primary-700">Entradas de la ruta</p>
                    <p class="mt-2 text-3xl font-extrabold text-primary-700">S/ {{ $ruta->costo_entradas_referencial }}</p>
                    <p class="mt-3 text-sm leading-6 text-gray-500">Suma de los importes registrados para los atractivos. No incluye transporte, guía, comida ni otras actividades.</p>
                </section>
                <div class="tc-note rounded-r-lg p-3 text-xs leading-5">La distancia y duración fueron registradas manualmente por el operador. No representan navegación, tráfico ni una cotización.</div>
                <a href="{{ route('catalogo.rutas.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-primary-700 hover:underline"><span aria-hidden="true">←</span> Seguir explorando rutas</a>
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
