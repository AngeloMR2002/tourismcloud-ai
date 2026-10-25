@extends('layouts.app')

@section('title', $atractivo->nombre)

@section('content')
@php
    $imagenHero = $atractivo->imagen_portada ?: $atractivo->imagenes->first()?->url;
    $tieneCoordenadas = $atractivo->latitud !== null && $atractivo->longitud !== null;
    $horarios = $atractivo->horarios;
    $duracion = null;
    if ($atractivo->duracion_estimada_min) {
        $horas = intdiv($atractivo->duracion_estimada_min, 60);
        $minutos = $atractivo->duracion_estimada_min % 60;
        $duracion = ($horas ? $horas . ' h' : '') . ($horas && $minutos ? ' ' : '') . ($minutos ? $minutos . ' min' : '');
    }
@endphp

<div class="tc-catalog-shell max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
    <nav aria-label="{{ __('catalog.breadcrumbs') }}" class="mb-4 flex flex-wrap items-center gap-2 text-xs font-semibold text-slate-500">
        <a href="{{ route('inicio') }}" class="transition hover:text-[#16734f]">{{ __('ui.home') }}</a>
        <svg class="h-3.5 w-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 18 6-6-6-6"/></svg>
        <a href="{{ route('catalogo.atractivos.index') }}" class="transition hover:text-[#16734f]">{{ __('catalog.attractions_heading') }}</a>
        @if($atractivo->destino)
            <svg class="h-3.5 w-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 18 6-6-6-6"/></svg>
            <span>{{ $atractivo->destino->nombre }}</span>
        @endif
    </nav>
        <div class="flex justify-end mb-3"><x-favorite-button type="atractivo" :item="$atractivo" :inline="true" /></div>

    <header class="tc-page-heading" aria-labelledby="atractivo-titulo">
        <p class="text-xs font-semibold uppercase tracking-widest text-primary-700">{{ $atractivo->destino?->nombre ?? __('attraction.tourist_attraction') }}</p>
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

    <section aria-label="{{ __('attraction.main_facts') }}" class="tc-detail-facts mt-5">
        <div class="tc-fact">
            <span class="tc-fact__icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21s7-4.35 7-11.3A7 7 0 0 0 5 9.7C5 16.65 12 21 12 21Z"/><circle cx="12" cy="10" r="2.25"/></svg></span>
            <p class="tc-fact__label">{{ __('attraction.destination') }}</p>
            <p class="tc-fact__value">{{ $atractivo->destino?->nombre ?? __('attraction.not_indicated') }}</p>
        </div>
        <div class="tc-fact">
            <span class="tc-fact__icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="8.5"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 7v5l3 1.75"/></svg></span>
            <p class="tc-fact__label">{{ __('attraction.estimated_time') }}</p>
            <p class="tc-fact__value">{{ $duracion ?: __('attraction.not_indicated') }}</p>
        </div>
        <div class="tc-fact">
            <span class="tc-fact__icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.119-3 2.5S10.343 13 12 13s3 1.119 3 2.5S13.657 18 12 18m0-10V6m0 12v-2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg></span>
            <p class="tc-fact__label">{{ __('attraction.admission') }}</p>
            <p class="tc-fact__value">{{ $atractivo->costo_entrada > 0 ? 'S/ ' . number_format($atractivo->costo_entrada, 2) : __('attraction.free_entry') }}</p>
        </div>
        <div class="tc-fact">
            <span class="tc-fact__icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="8.5" cy="10" r="1.5"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m4 17 5-5 3.5 3 2-2 5 4"/></svg></span>
            <p class="tc-fact__label">{{ __('attraction.gallery') }}</p>
            <p class="tc-fact__value">{{ $atractivo->imagenes->count() + ($atractivo->imagen_portada ? 1 : 0) }} {{ ($atractivo->imagenes->count() + ($atractivo->imagen_portada ? 1 : 0)) === 1 ? __('attraction.image') : __('attraction.images') }}</p>
        </div>
    </section>

    <div class="mt-10 grid items-start gap-8 xl:grid-cols-[minmax(0,1fr)_20rem]">
        <div class="min-w-0 space-y-8">
            <section id="descripcion" class="rounded-2xl border border-[#16734f]/10 bg-white p-6 shadow-[0_10px_28px_rgba(5,64,72,.06)] sm:p-8">
                <div class="flex items-start gap-4">
                    <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-[#daf2e6] text-[#16734f]">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v11.494m-9-5.747h18"/></svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[.14em] text-[#1b8059]">{{ __('attraction.about_place') }}</p>
                        <h2 class="mt-1 text-2xl font-extrabold tracking-[-.03em] text-slate-900" style="font-family: var(--font-display);">{{ __('attraction.visit_at_your_pace') }}</h2>
                    </div>
                </div>
                @if($atractivo->descripcion)
                    <p class="mt-6 whitespace-pre-line text-[.95rem] leading-8 text-slate-600">{{ $atractivo->descripcion }}</p>
                @else
                    <p class="mt-6 rounded-xl bg-slate-50 p-4 text-sm leading-6 text-slate-500">{{ __('attraction.no_description') }}</p>
                @endif
            </section>

            {{-- ── Horario de atención — tabla 4 columnas ────────────────── --}}
            <section id="horarios" class="tc-card scroll-mt-24 p-6 sm:p-8">
                <h2 class="text-xs font-bold uppercase tracking-wide mb-4" style="color: var(--color-primary-700);">
                    {{ __('attraction.opening_hours') }}
                </h2>
                @php
                    $dias = __('attraction.weekdays');
                @endphp
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr style="border-bottom: 2px solid #f0f0f0;">
                                <th class="text-left font-bold pb-2.5 pr-4" style="color: #6b7280; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em; width: 28%;">{{ __('attraction.day') }}</th>
                                <th class="text-left font-bold pb-2.5 pr-4" style="color: #6b7280; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em; width: 25%;">{{ __('attraction.opens') }}</th>
                                <th class="text-left font-bold pb-2.5 pr-4" style="color: #6b7280; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em; width: 25%;">{{ __('attraction.closes') }}</th>
                                <th class="text-left font-bold pb-2.5"     style="color: #6b7280; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em; width: 22%;">{{ __('attraction.status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dias as $clave => $nombre)
                                @php
                                    $diaKey = strtolower(str_replace(['á','é','í','ó','ú'], ['a','e','i','o','u'], $clave));
                                    $horario = $atractivo->horarios[$diaKey] ?? $atractivo->horarios[$clave] ?? null;
                                    $abierto = !empty($horario) && !empty($horario['abre']) && !empty($horario['cierra']);
                                @endphp
                                <tr class="border-b border-gray-100 last:border-0 {{ $abierto ? '' : 'bg-slate-50/60' }}">
                                    <td class="py-3 pr-4 font-semibold {{ $abierto ? 'text-gray-800' : 'text-gray-500' }}">
                                        {{ $nombre }}
                                    </td>
                                    @if($abierto)
                                        <td class="py-3 pr-4">
                                            <span class="inline-flex items-center gap-1.5 text-sm font-medium px-2.5 py-1 rounded-md"
                                                  style="background: var(--color-teal-light); color: var(--color-primary-700);">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <circle cx="12" cy="12" r="10"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2"/>
                                                </svg>
                                                {{ $horario['abre'] ?? '—' }}
                                            </span>
                                        </td>
                                        <td class="py-3 pr-4">
                                            <span class="inline-flex items-center gap-1.5 text-sm font-medium px-2.5 py-1 rounded-md"
                                                  style="background: #fff7ed; color: #9a3412;">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <circle cx="12" cy="12" r="10"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2"/>
                                                </svg>
                                                {{ $horario['cierra'] ?? '—' }}
                                            </span>
                                        </td>
                                        <td class="py-3">
                                            <span class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1 rounded-full"
                                                  style="background: #dcfce7; color: #15803d;">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>
                                                {{ __('attraction.open') }}
                                            </span>
                                        </td>
                                    @else
                                        <td class="py-3 pr-4">
                                            <span class="inline-flex items-center text-xs font-medium px-2.5 py-1 rounded-md bg-gray-100 text-gray-500">
                                                {{ __('attraction.no_service') }}
                                            </span>
                                        </td>
                                        <td class="py-3 pr-4">
                                            <span class="inline-flex items-center text-xs font-medium px-2.5 py-1 rounded-md bg-gray-100 text-gray-500">
                                                {{ __('attraction.no_service') }}
                                            </span>
                                        </td>
                                        <td class="py-3">
                                            <span class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1 rounded-full bg-rose-50 text-rose-700 border border-rose-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500 inline-block"></span>
                                                {{ __('attraction.closed') }}
                                            </span>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            <section id="galeria" class="rounded-2xl border border-[#16734f]/10 bg-white p-6 shadow-[0_10px_28px_rgba(5,64,72,.06)] sm:p-8">
                <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-end">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[.14em] text-[#1b8059]">{{ __('attraction.gallery') }}</p>
                        <h2 class="mt-1 text-2xl font-extrabold tracking-[-.03em] text-slate-900" style="font-family: var(--font-display);">{{ __('attraction.see_attraction') }}</h2>
                    </div>
                    @if($atractivo->imagenes->isNotEmpty() || $atractivo->imagen_portada)
                        <span class="text-sm font-medium text-slate-500">{{ __('attraction.swipe_images') }}</span>
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
                        <div class="tc-cover-fallback min-h-[17rem] rounded-2xl" role="img" aria-label="{{ __('attraction.no_images_for', ['name' => $atractivo->nombre]) }}">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="8.5" cy="10" r="1.5"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.35" d="m4 17 5-5 3.5 3 2-2 5 4"/></svg>
                        </div>
                    @endif
                </div>
            </section>

            <section id="ubicacion" class="overflow-hidden rounded-2xl border border-[#16734f]/10 bg-white shadow-[0_10px_28px_rgba(5,64,72,.06)]">
                <div class="flex flex-col gap-5 p-6 sm:flex-row sm:items-center sm:justify-between sm:p-8">
                    <div class="flex items-start gap-4">
                        <div class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-[#daf2e6] text-[#16734f]">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21s7-4.35 7-11.3A7 7 0 0 0 5 9.7C5 16.65 12 21 12 21Z"/><circle cx="12" cy="10" r="2.25"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[.14em] text-[#1b8059]">{{ __('attraction.location') }}</p>
                            <h2 class="mt-1 text-xl font-extrabold tracking-[-.025em] text-slate-900" style="font-family: var(--font-display);">{{ $atractivo->destino?->nombre ?? __('attraction.attraction_point') }}</h2>
                            @if($tieneCoordenadas)
                                <p class="mt-1 text-sm text-slate-500">{{ __('attraction.published_coordinates', ['lat' => $atractivo->latitud, 'lng' => $atractivo->longitud]) }}</p>
                            @else
                                <p class="mt-1 text-sm text-slate-500">{{ __('attraction.no_coordinates') }}</p>
                            @endif
                        </div>
                    </div>
                    @if($tieneCoordenadas)
                        <a href="https://maps.google.com/?q={{ $atractivo->latitud }},{{ $atractivo->longitud }}" target="_blank" rel="noopener noreferrer" class="tc-btn-outline w-full justify-center sm:w-auto">
                            {{ __('attraction.open_location') }}
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5h6m0 0v6m0-6-8 8"/></svg>
                        </a>
                    @endif
                </div>
                @if($tieneCoordenadas)
                    <div class="relative h-[22rem] sm:h-[28rem] border-t border-slate-100 bg-[#daf2e6]">
                        <div id="interactive_map_atractivo" class="h-full w-full" aria-label="{{ __('attraction.location_map_for', ['name' => $atractivo->nombre]) }}"></div>
                        <div class="pointer-events-none absolute bottom-4 left-4 rounded-xl bg-white/95 px-3 py-2 text-xs font-bold text-[#35676a] shadow-lg backdrop-blur-sm">{{ __('attraction.registered_location') }}</div>
                        <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-[#124e37]/10 to-transparent"></div>
                    </div>
                @else
                    <div class="relative flex min-h-64 items-center justify-center overflow-hidden border-t border-slate-100 bg-[#eaf4f2] px-6 text-center">
                        <svg viewBox="0 0 540 260" class="absolute inset-0 h-full w-full text-[#16734f]/10" fill="none" aria-hidden="true"><path d="M0 210c84-67 164-54 246 20 77-112 168-110 294-36" stroke="currentColor" stroke-width="3"/><path d="M135 260V130l48-36 48 36v130M328 260V88l62-42 62 42v172" stroke="currentColor" stroke-width="3"/></svg>
                        <div class="relative max-w-sm py-10">
                            <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-[#16734f] shadow-sm"><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 21s7-5.1 7-12a7 7 0 1 0-14 0c0 6.9 7 12 7 12Z"/><circle cx="12" cy="9" r="2.2" stroke-width="1.8"/></svg></span>
                            <p class="mt-4 text-sm font-bold text-[#2c6064]">{{ __('attraction.location_unavailable') }}</p>
                            <p class="mt-2 text-sm leading-6 text-[#6a8586]">{{ __('attraction.location_hint') }}</p>
                        </div>
                    </div>
                @endif
            </section>
            <section id="resenas" class="scroll-mt-24">
                @include('valoraciones::components.form', ['modelo' => $atractivo, 'tipo' => 'atractivo'])
            </section>
        </div>

        <aside class="space-y-4 xl:sticky xl:top-[5.5rem]">
            {{-- Costo de entrada --}}
            <div class="tc-card p-5">
                <p class="text-xs font-bold uppercase tracking-wide mb-1" style="color: var(--color-primary-700);">
                    {{ __('attraction.admission_cost') }}
                </p>
                <p style="font-family: var(--font-display); font-size: 2.25rem; font-weight: 800; color: #1a2232; line-height: 1.1;" class="mb-0.5">
                    @if($atractivo->costo_entrada > 0)
                        S/ {{ number_format($atractivo->costo_entrada, 2) }}
                    @else
                        <span style="color: #16a34a;">{{ __('attraction.free') }}</span>
                    @endif
                </p>
                @if($atractivo->costo_entrada > 0)
                    <p class="text-xs text-gray-400">{{ __('attraction.per_person') }}</p>
                @endif
            </div>

            <div class="rounded-2xl border border-[#16734f]/10 bg-white p-5 shadow-[0_10px_28px_rgba(5,64,72,.06)]">
                <p class="text-xs font-bold uppercase tracking-[.14em] text-[#1b8059]">{{ __('attraction.on_this_page') }}</p>
                <nav aria-label="{{ __('attraction.attraction_contents') }}" class="mt-3 space-y-1">
                    @foreach(['descripcion' => __('attraction.description'), 'horarios' => __('attraction.hours'), 'galeria' => __('attraction.gallery'), 'ubicacion' => __('attraction.location'), 'resenas' => __('attraction.reviews')] as $ancla => $etiqueta)
                        <a href="#{{ $ancla }}" class="flex items-center justify-between rounded-lg px-3 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-[#daf2e6] hover:text-[#16734f]">
                            {{ $etiqueta }}
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 18 6-6-6-6"/></svg>
                        </a>
                    @endforeach
                </nav>
                <div class="mt-5 border-t border-slate-100 pt-5">
                    <p class="text-xs leading-5 text-slate-500">{{ __('attraction.operator_disclaimer') }}</p>
                </div>
            </div>
            <x-agregar-itinerario tipo="atractivo" :item="$atractivo" />
            @include('valoraciones::components.sidebar', ['modelo' => $atractivo, 'tipo' => 'atractivo'])

            <a href="{{ route('catalogo.atractivos.index') }}" class="tc-btn-outline mt-4 w-full justify-center text-sm">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19 8 12l7-7"/></svg>
                {{ __('attraction.back_to_catalog') }}
            </a>
        </aside>
    </div>
</div>

@if($tieneCoordenadas)
    @push('scripts')
    <script>
        (function () {
            const target = document.getElementById('interactive_map_atractivo');
            if (!target) return;
            const apiKey = @json(config('services.google_maps.key'));
            const point = {
                lat: {{ json_encode((float) $atractivo->latitud) }},
                lng: {{ json_encode((float) $atractivo->longitud) }}
            };
            const name = @json($atractivo->nombre);
            const detail = point.lat + '°, ' + point.lng + '°';

            function showMapLink() {
                const link = document.createElement('a');
                link.href = 'https://maps.google.com/?q=' + point.lat + ',' + point.lng;
                link.target = '_blank';
                link.rel = 'noopener noreferrer';
                link.className = 'flex h-full items-center justify-center bg-[#daf2e6] p-6 text-center text-sm font-semibold text-[#105c3e]';
                link.textContent = @json(__('attraction.open_published_point'));
                target.replaceChildren(link);
            }

            if (!apiKey) {
                showMapLink();
                return;
            }

    (g=>{var h,a,k,p="The Google Maps JavaScript API",c="google",l="importLibrary",q="__ib__",m=document,b=window;b=b[c]||(b[c]={});var d=b.maps||(b.maps={}),r=new Set,e=new URLSearchParams,u=()=>h||(h=new Promise(async(f,n)=>{await (a=m.createElement("script"));e.set("libraries",[...r]+"");for(k in g)e.set(k.replace(/[A-Z]/g,t=>"_"+t[0].toLowerCase()),g[k]);e.set("callback",c+".maps."+q);a.src=`https://maps.${c}apis.com/maps/api/js?`+e;d[q]=f;a.onerror=()=>h=n(Error(p+" could not load."));a.nonce=m.querySelector("script[nonce]")?.nonce||"";m.head.append(a)}));d[l]?console.warn(p+" only loads once. Ignoring:",g):d[l]=(f,...n)=>r.add(f)&&u().then(()=>d[l](f,...n))})({
        key: apiKey,
        v: "weekly",
        language: @json(app()->getLocale())
    });


            async function initializeMap() {
                try {
                    const { Map, InfoWindow } = await google.maps.importLibrary('maps');
                    const { AdvancedMarkerElement } = await google.maps.importLibrary('marker');
                    const map = new Map(target, {
                        center: point,
                        zoom: 15,
                        disableDefaultUI: true,
                        zoomControl: true,
                        mapId: 'DEMO_MAP_ID',
                        gestureHandling: 'cooperative'
                    });
                    const markerContent = document.createElement('div');
                    markerContent.innerHTML = '<div class="flex cursor-pointer items-center justify-center rounded-full border-2 border-white bg-gray-900 p-2.5 text-white shadow-lg"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2Z"/><path d="M9 22V12h6v10"/></svg></div>';
                    const marker = new AdvancedMarkerElement({
                        map: map,
                        position: point,
                        content: markerContent,
                        title: name
                    });
                    const popup = document.createElement('div');
                    popup.className = 'max-w-[200px] p-1 font-sans';
                    const title = document.createElement('h3');
                    title.className = 'mb-1 text-sm font-bold leading-tight text-gray-900';
                    title.textContent = name;
                    const description = document.createElement('p');
                    description.className = 'text-xs text-gray-500';
                    description.textContent = detail;
                    popup.append(title, description);
                    const infoWindow = new InfoWindow({ content: popup, headerDisabled: true });
                    marker.addListener('click', function () {
                        infoWindow.open({ anchor: marker, map: map });
                    });
                } catch (error) {
                    showMapLink();
                }
            }

            initializeMap();
        })();
    </script>
    @endpush
@endif
@endsection
