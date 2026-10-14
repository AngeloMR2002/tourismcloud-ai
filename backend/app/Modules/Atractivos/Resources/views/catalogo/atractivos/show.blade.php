@extends('layouts.app')

@section('title', $atractivo->nombre)

@section('content')
@php
    $imagenHero = $atractivo->imagen_portada ?: $atractivo->imagenes->first()?->url;
    $tieneCoordenadas = $atractivo->latitud !== null && $atractivo->longitud !== null;
    $horarios = $atractivo->horarios;
    $dias = [
        'lunes' => 'Lunes', 'martes' => 'Martes', 'miercoles' => 'Miércoles', 'jueves' => 'Jueves',
        'viernes' => 'Viernes', 'sabado' => 'Sábado', 'domingo' => 'Domingo',
    ];
    $duracion = null;
    if ($atractivo->duracion_estimada_min) {
        $horas = intdiv($atractivo->duracion_estimada_min, 60);
        $minutos = $atractivo->duracion_estimada_min % 60;
        $duracion = ($horas ? $horas . ' h' : '') . ($horas && $minutos ? ' ' : '') . ($minutos ? $minutos . ' min' : '');
    }
@endphp

<div class="tc-catalog-shell max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
    <nav aria-label="Migas de pan" class="mb-4 flex flex-wrap items-center gap-2 text-xs font-semibold text-slate-500">
        <a href="{{ route('inicio') }}" class="transition hover:text-[#00626A]">Inicio</a>
        <svg class="h-3.5 w-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 18 6-6-6-6"/></svg>
        <a href="{{ route('catalogo.atractivos.index') }}" class="transition hover:text-[#00626A]">Atractivos</a>
        @if($atractivo->destino)
            <svg class="h-3.5 w-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 18 6-6-6-6"/></svg>
            <span>{{ $atractivo->destino->nombre }}</span>
        @endif
    </nav>

    <header class="tc-page-heading" aria-labelledby="atractivo-titulo">
        <p class="text-xs font-semibold uppercase tracking-widest text-primary-700">{{ $atractivo->destino?->nombre ?? 'Atractivo turístico' }}</p>
        <h1 id="atractivo-titulo" class="mt-2 text-3xl font-bold tracking-tight text-gray-900">{{ $atractivo->nombre }}</h1>
        @if($atractivo->categorias->isNotEmpty())
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach($atractivo->categorias as $categoria)<span class="tc-filter-chip">{{ $categoria->nombre }}</span>@endforeach
            </div>
        @endif
    </header>
    @if($imagenHero)
        <figure class="mt-5 overflow-hidden rounded-2xl bg-gray-100">
            <img src="{{ asset('storage/'.$imagenHero) }}" alt="{{ $atractivo->nombre }}" class="max-h-80 w-full object-cover">
        </figure>
    @endif

    <section aria-label="Datos principales" class="tc-detail-facts mt-5">
        <div class="tc-fact">
            <span class="tc-fact__icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21s7-4.35 7-11.3A7 7 0 0 0 5 9.7C5 16.65 12 21 12 21Z"/><circle cx="12" cy="10" r="2.25"/></svg></span>
            <p class="tc-fact__label">Destino</p>
            <p class="tc-fact__value">{{ $atractivo->destino?->nombre ?? 'No indicado' }}</p>
        </div>
        <div class="tc-fact">
            <span class="tc-fact__icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="8.5"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 7v5l3 1.75"/></svg></span>
            <p class="tc-fact__label">Tiempo estimado</p>
            <p class="tc-fact__value">{{ $duracion ?: 'No indicado' }}</p>
        </div>
        <div class="tc-fact">
            <span class="tc-fact__icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.119-3 2.5S10.343 13 12 13s3 1.119 3 2.5S13.657 18 12 18m0-10V6m0 12v-2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg></span>
            <p class="tc-fact__label">Entrada</p>
            <p class="tc-fact__value">{{ $atractivo->costo_entrada > 0 ? 'S/ ' . number_format($atractivo->costo_entrada, 2) : 'Libre' }}</p>
        </div>
        <div class="tc-fact">
            <span class="tc-fact__icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="8.5" cy="10" r="1.5"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m4 17 5-5 3.5 3 2-2 5 4"/></svg></span>
            <p class="tc-fact__label">Galería</p>
            <p class="tc-fact__value">{{ $atractivo->imagenes->count() + ($atractivo->imagen_portada ? 1 : 0) }} {{ ($atractivo->imagenes->count() + ($atractivo->imagen_portada ? 1 : 0)) === 1 ? 'imagen' : 'imágenes' }}</p>
        </div>
    </section>

    <div class="mt-10 grid items-start gap-8 xl:grid-cols-[minmax(0,1fr)_20rem]">
        <div class="min-w-0 space-y-8">
            <section id="descripcion" class="rounded-2xl border border-[#00626A]/10 bg-white p-6 shadow-[0_10px_28px_rgba(5,64,72,.06)] sm:p-8">
                <div class="flex items-start gap-4">
                    <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-[#e6f4f5] text-[#00626A]">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v11.494m-9-5.747h18"/></svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[.14em] text-[#0E7C86]">Sobre este lugar</p>
                        <h2 class="mt-1 text-2xl font-extrabold tracking-[-.03em] text-slate-900" style="font-family: var(--font-display);">Una visita a tu ritmo</h2>
                    </div>
                </div>
                @if($atractivo->descripcion)
                    <p class="mt-6 whitespace-pre-line text-[.95rem] leading-8 text-slate-600">{{ $atractivo->descripcion }}</p>
                @else
                    <p class="mt-6 rounded-xl bg-slate-50 p-4 text-sm leading-6 text-slate-500">El operador aún no añadió una descripción para este atractivo.</p>
                @endif
            </section>

            <section id="horarios" class="rounded-2xl border border-[#00626A]/10 bg-white p-6 shadow-[0_10px_28px_rgba(5,64,72,.06)] sm:p-8">
                <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[.14em] text-[#0E7C86]">Antes de ir</p>
                        <h2 class="mt-1 text-2xl font-extrabold tracking-[-.03em] text-slate-900" style="font-family: var(--font-display);">Horario publicado</h2>
                    </div>
                    <span class="inline-flex w-fit items-center gap-2 rounded-full bg-[#fff4e6] px-3 py-1.5 text-xs font-bold text-[#85480C]">
                        <span class="h-1.5 w-1.5 rounded-full bg-[#D88422]"></span>
                        Confirma antes de viajar
                    </span>
                </div>
                <div class="mt-6 overflow-hidden rounded-xl border border-slate-100">
                    <div class="grid grid-cols-[1fr_auto] bg-slate-50 px-4 py-3 text-[.68rem] font-bold uppercase tracking-[.12em] text-slate-500 sm:grid-cols-[1fr_1fr_1fr]">
                        <span>Día</span><span class="text-right sm:text-left">Atención</span><span class="hidden sm:block">Estado</span>
                    </div>
                    @foreach($dias as $clave => $nombre)
                        @php($horario = $horarios[$clave] ?? null)
                        <div class="grid grid-cols-[1fr_auto] items-center border-t border-slate-100 px-4 py-3.5 text-sm sm:grid-cols-[1fr_1fr_1fr]">
                            <span class="font-semibold text-slate-800">{{ $nombre }}</span>
                            @if($horario)
                                <span class="text-right font-medium text-slate-600 sm:text-left">{{ $horario['abre'] ?? '—' }} – {{ $horario['cierra'] ?? '—' }}</span>
                                <span class="hidden sm:inline-flex sm:w-fit sm:items-center sm:gap-1.5 sm:rounded-full sm:bg-emerald-50 sm:px-2.5 sm:py-1 sm:text-xs sm:font-bold sm:text-emerald-700"><span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>Publicado</span>
                            @else
                                <span class="text-right text-slate-400 sm:text-left">No indicado</span>
                                <span class="hidden sm:block text-xs font-semibold text-slate-400">Sin horario</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>

            <section id="galeria" class="rounded-2xl border border-[#00626A]/10 bg-white p-6 shadow-[0_10px_28px_rgba(5,64,72,.06)] sm:p-8">
                <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-end">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[.14em] text-[#0E7C86]">Galería</p>
                        <h2 class="mt-1 text-2xl font-extrabold tracking-[-.03em] text-slate-900" style="font-family: var(--font-display);">Mira el atractivo</h2>
                    </div>
                    @if($atractivo->imagenes->isNotEmpty() || $atractivo->imagen_portada)
                        <span class="text-sm font-medium text-slate-500">Desliza para explorar las imágenes</span>
                    @endif
                </div>
                <div class="mt-6">
                    @include('components.galeria-carousel', [
                        'imagenes' => $atractivo->imagenes,
                        'imagenPortada' => $atractivo->imagen_portada,
                        'nombreEntidad' => $atractivo->nombre,
                        'carouselId' => 'atractivo-' . $atractivo->id,
                    ])
                    @if(!$atractivo->imagen_portada && $atractivo->imagenes->isEmpty())
                        <div class="tc-cover-fallback min-h-[17rem] rounded-2xl" role="img" aria-label="Sin imágenes disponibles de {{ $atractivo->nombre }}">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="8.5" cy="10" r="1.5"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.35" d="m4 17 5-5 3.5 3 2-2 5 4"/></svg>
                        </div>
                    @endif
                </div>
            </section>

            <section id="ubicacion" class="overflow-hidden rounded-2xl border border-[#00626A]/10 bg-white shadow-[0_10px_28px_rgba(5,64,72,.06)]">
                <div class="flex flex-col gap-5 p-6 sm:flex-row sm:items-center sm:justify-between sm:p-8">
                    <div class="flex items-start gap-4">
                        <div class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-[#e6f4f5] text-[#00626A]">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21s7-4.35 7-11.3A7 7 0 0 0 5 9.7C5 16.65 12 21 12 21Z"/><circle cx="12" cy="10" r="2.25"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[.14em] text-[#0E7C86]">Ubicación</p>
                            <h2 class="mt-1 text-xl font-extrabold tracking-[-.025em] text-slate-900" style="font-family: var(--font-display);">{{ $atractivo->destino?->nombre ?? 'Punto del atractivo' }}</h2>
                            @if($tieneCoordenadas)
                                <p class="mt-1 text-sm text-slate-500">Coordenadas publicadas: {{ $atractivo->latitud }}, {{ $atractivo->longitud }}</p>
                            @else
                                <p class="mt-1 text-sm text-slate-500">El operador aún no publicó coordenadas para este atractivo.</p>
                            @endif
                        </div>
                    </div>
                    @if($tieneCoordenadas)
                        <a href="https://maps.google.com/?q={{ $atractivo->latitud }},{{ $atractivo->longitud }}" target="_blank" rel="noopener noreferrer" class="tc-btn-outline w-full justify-center sm:w-auto">
                            Abrir ubicación
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5h6m0 0v6m0-6-8 8"/></svg>
                        </a>
                    @endif
                </div>
                @if($tieneCoordenadas)
                    <div class="relative h-72 border-t border-slate-100 bg-[#e6f4f5]">
                        <div id="interactive_map_atractivo" class="h-full w-full" aria-label="Mapa de ubicación de {{ $atractivo->nombre }}"></div>
                        <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-[#054048]/10 to-transparent"></div>
                    </div>
                @endif
            </section>
        </div>

        <aside class="xl:sticky xl:top-[5.5rem]">
            <div class="rounded-2xl border border-[#00626A]/10 bg-white p-5 shadow-[0_10px_28px_rgba(5,64,72,.06)]">
                <p class="text-xs font-bold uppercase tracking-[.14em] text-[#0E7C86]">En esta ficha</p>
                <nav aria-label="Contenido del atractivo" class="mt-3 space-y-1">
                    @foreach(['descripcion' => 'Descripción', 'horarios' => 'Horario', 'galeria' => 'Galería', 'ubicacion' => 'Ubicación'] as $ancla => $etiqueta)
                        <a href="#{{ $ancla }}" class="flex items-center justify-between rounded-lg px-3 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-[#e6f4f5] hover:text-[#00626A]">
                            {{ $etiqueta }}
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 18 6-6-6-6"/></svg>
                        </a>
                    @endforeach
                </nav>
                <div class="mt-5 border-t border-slate-100 pt-5">
                    <p class="text-xs leading-5 text-slate-500">La información proviene del operador turístico y puede cambiar. Verifícala antes de tu visita.</p>
                </div>
            </div>
            <a href="{{ route('catalogo.atractivos.index') }}" class="tc-btn-outline mt-4 w-full justify-center text-sm">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19 8 12l7-7"/></svg>
                Volver al catálogo
            </a>
        </aside>
    </div>
</div>

@if($tieneCoordenadas)
    @push('scripts')
    <script>
        (function () {
            var target = document.getElementById('interactive_map_atractivo');
            if (!target) return;

            var latitude = {{ $atractivo->latitud }};
            var longitude = {{ $atractivo->longitud }};
            var apiKey = @json(env('GOOGLE_MAPS_API_KEY'));

            if (!apiKey) {
                target.innerHTML = '<a href="https://maps.google.com/?q=' + latitude + ',' + longitude + '" target="_blank" rel="noopener noreferrer" class="flex h-full items-center justify-center bg-[#e6f4f5] p-6 text-center text-sm font-semibold text-[#08565D] transition hover:bg-[#c0e4e7]">Abrir el punto publicado en Google Maps</a>';
                return;
            }

            var script = document.createElement('script');
            script.src = 'https://maps.googleapis.com/maps/api/js?key=' + encodeURIComponent(apiKey) + '&callback=initAtractivoMap';
            script.async = true;
            script.defer = true;
            script.onerror = function () {
                target.innerHTML = '<a href="https://maps.google.com/?q=' + latitude + ',' + longitude + '" target="_blank" rel="noopener noreferrer" class="flex h-full items-center justify-center bg-[#e6f4f5] p-6 text-center text-sm font-semibold text-[#08565D] transition hover:bg-[#c0e4e7]">Abrir el punto publicado en Google Maps</a>';
            };
            window.initAtractivoMap = function () {
                var point = { lat: Number(latitude), lng: Number(longitude) };
                var map = new google.maps.Map(target, {
                    center: point,
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
                new google.maps.Marker({ position: point, map: map, title: @json($atractivo->nombre) });
            };
            document.head.appendChild(script);
        })();
    </script>
    @endpush
@endif
@endsection
