@extends('layouts.app')

@section('title', $establecimiento->nombre)

@section('content')
@php
    $tipoEstablecimiento = [
        'hotel'       => __('place.lodging'),
        'restaurante' => __('place.gastronomy'),
        'transporte'  => __('place.mobility'),
        'agencia'     => __('place.agency'),
        'otro'        => __('place.tourist_service'),
    ][$establecimiento->tipo] ?? ucfirst($establecimiento->tipo);

    $rangoPrecio = [
        'Bajo'   => ['etiqueta' => __('place.price_low'), 'simbolo' => '$', 'rango' => 'S/ 20 - S/ 80'],
        'Medio'  => ['etiqueta' => __('place.price_mid'), 'simbolo' => '$$', 'rango' => 'S/ 80 - S/ 200'],
        'Alto'   => ['etiqueta' => __('place.price_high'), 'simbolo' => '$$$', 'rango' => 'S/ 200 - S/ 500'],
        'Lujoso' => ['etiqueta' => __('place.price_luxury'), 'simbolo' => '$$$$', 'rango' => 'S/ 500 - S/ 2,000'],
    ][$establecimiento->rango_precio] ?? ($establecimiento->rango_precio ? [
        'etiqueta' => $establecimiento->rango_precio,
        'simbolo' => '$$',
        'rango' => __('place.ask_place'),
    ] : null);

    $horarios = $establecimiento->horarios;
    $galeria = $establecimiento->imagenes;
    $tieneCoordenadas = $establecimiento->latitud !== null && $establecimiento->longitud !== null;
@endphp

<div class="tc-catalog-shell max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
    <nav class="mb-5 flex flex-wrap items-center gap-2 text-sm" aria-label="{{ __('catalog.breadcrumbs') }}">
        <a href="{{ route('catalogo.establecimientos.index') }}" class="font-semibold text-primary-700 hover:underline">{{ __('place.places') }}</a>
        <span class="text-gray-400" aria-hidden="true">/</span>
        <span class="text-gray-500">{{ $establecimiento->nombre }}</span>
    </nav>
        <div class="flex justify-end mb-3"><x-favorite-button type="establecimiento" :item="$establecimiento" :inline="true" /></div>
    <header class="tc-page-heading" aria-labelledby="establecimiento-titulo">
        <p class="text-xs font-semibold uppercase tracking-widest text-primary-700">{{ $tipoEstablecimiento }}@if($establecimiento->destino) · {{ $establecimiento->destino->nombre }}@endif</p>
        <h1 id="establecimiento-titulo" class="mt-2 text-3xl font-bold tracking-tight text-gray-900">{{ $establecimiento->nombre }}</h1>
        @if($establecimiento->direccion)<p class="mt-2 text-sm leading-6 text-gray-600">{{ $establecimiento->direccion }}</p>@endif
        <div class="mt-4 flex flex-wrap gap-3">
            <a href="#horarios" class="text-sm font-semibold text-primary-700 hover:underline">{{ __('place.view_hours') }}</a>
            <a href="#ubicacion" class="text-sm font-semibold text-primary-700 hover:underline">{{ __('place.view_location') }}</a>
            <a href="#resenas" class="text-sm font-semibold text-primary-700 hover:underline">{{ __('place.view_reviews') }}</a>
        </div>
    </header>
    @if($establecimiento->imagen_portada)
        <figure class="mt-5 overflow-hidden rounded-2xl bg-gray-100">
            <img src="{{ asset('storage/'.$establecimiento->imagen_portada) }}" alt="{{ $establecimiento->nombre }}" class="max-h-80 w-full object-cover">
        </figure>
    @endif

    <div class="mt-6 grid gap-6 lg:grid-cols-[minmax(0,1fr)_19rem] lg:items-start">
        <div class="min-w-0 space-y-7">
            <section class="rounded-[1.6rem] border border-[#dce8e5] bg-white p-6 shadow-[0_18px_42px_-34px_rgba(8,75,80,0.55)] sm:p-8">
                <div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">
                    <div class="max-w-2xl">
                        <p class="text-[0.68rem] font-extrabold uppercase tracking-[0.17em] text-[#4d8584]">{{ __('attraction.about_place') }}</p>
                        <h2 class="mt-2 text-2xl font-extrabold tracking-[-0.035em] text-[#173f43]" style="font-family: var(--font-display);">{{ __('place.plan_visit_info') }}</h2>
                    </div>
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-[#eaf5f3] text-[#16734f]" aria-hidden="true">
                        @if($establecimiento->tipo === 'restaurante')
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M8 3v8m3-8v8m-6-8v5a3 3 0 0 0 6 0V3m4 0v18m0-9h4l-4-9"/></svg>
                        @elseif($establecimiento->tipo === 'transporte')
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M5 17h14M6 17V8a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v9M8 17v2m8-2v2M8 10h8"/></svg>
                        @elseif($establecimiento->tipo === 'agencia')
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M3 21h18M4 10h16M5 10l7-6 7 6M6 10v9m4-9v9m4-9v9m4-9v9"/></svg>
                        @else
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M4 21V6a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v15M9 21v-5h6v5M8 8h.01M12 8h.01M16 8h.01M8 12h.01M12 12h.01M16 12h.01"/></svg>
                        @endif
                    </span>
                </div>

                @if(filled($establecimiento->descripcion))
                    <p class="mt-6 whitespace-pre-line text-[0.98rem] leading-8 text-[#557375]">{{ $establecimiento->descripcion }}</p>
                @else
                    <p class="mt-6 text-[0.98rem] leading-8 text-[#6f8687]">{{ __('place.no_description') }}</p>
                @endif

                @if($establecimiento->categorias->isNotEmpty())
                    <div class="mt-7 border-t border-[#e4efec] pt-5">
                        <p class="mb-3 text-[0.67rem] font-extrabold uppercase tracking-[0.14em] text-[#497e80]">{{ __('place.registered_categories') }}</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($establecimiento->categorias as $categoria)
                                <span class="rounded-full border border-[#cde3df] bg-[#f0f8f6] px-3 py-1.5 text-xs font-bold text-[#246a6d]">{{ $categoria->nombre }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </section>

            <section id="galeria" class="tc-card p-6 sm:p-8">
                <p class="text-xs font-bold uppercase tracking-widest text-primary-700">{{ __('attraction.gallery') }}</p>
                <h2 class="mt-1 text-2xl font-bold text-gray-900">{{ __('place.see_space') }}</h2>
                <div class="mt-5">
                    @include('components.galeria-carousel', [
                        'imagenes' => $galeria,
                        'imagenPortada' => $establecimiento->imagen_portada,
                        'nombreEntidad' => $establecimiento->nombre,
                        'carouselId' => 'est-' . $establecimiento->id,
                    ])
                    @if(!$establecimiento->imagen_portada && $galeria->isEmpty())
                        <div class="tc-cover-fallback min-h-64 rounded-2xl" role="img" aria-label="{{ __('attraction.no_images_for', ['name' => $establecimiento->nombre]) }}">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 21V6a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v15M9 21v-5h6v5"/></svg>
                        </div>
                    @endif
                </div>
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
                                @php $horario = $establecimiento->horarios[$clave] ?? null; @endphp
                                <tr class="border-b border-gray-50 last:border-0">
                                    <td class="py-3 pr-4 font-semibold" style="color: #374151;">{{ $nombre }}</td>
                                    @if($horario)
                                        <td class="py-3 pr-4">
                                            <span class="inline-flex items-center gap-1.5 text-sm font-medium px-2.5 py-1 rounded-md"
                                                  style="background: var(--color-amber-soft); color: var(--color-tertiary-500);">
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
                                            <span class="inline-flex items-center gap-1 text-xs font-bold px-2.5 py-1 rounded-full"
                                                  style="background: #dcfce7; color: #15803d;">
                                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block"></span>
                                                {{ __('attraction.open') }}
                                            </span>
                                        </td>
                                    @else
                                        <td class="py-3 pr-4 text-gray-400">—</td>
                                        <td class="py-3 pr-4 text-gray-400">—</td>
                                        <td class="py-3">
                                            <span class="inline-flex items-center gap-1 text-xs font-bold px-2.5 py-1 rounded-full"
                                                  style="background: #f3f4f6; color: #9ca3af;">
                                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400 inline-block"></span>
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

            <section id="ubicacion" class="overflow-hidden rounded-[1.6rem] border border-[#dce8e5] bg-white shadow-[0_18px_42px_-34px_rgba(8,75,80,0.55)] scroll-mt-24">
                <div class="flex flex-col gap-4 border-b border-[#e2eeeb] p-6 sm:flex-row sm:items-center sm:justify-between sm:p-8">
                    <div>
                        <p class="text-[0.68rem] font-extrabold uppercase tracking-[0.17em] text-[#4d8584]">{{ __('attraction.location') }}</p>
                        <h2 class="mt-1 text-2xl font-extrabold tracking-[-0.035em] text-[#173f43]" style="font-family: var(--font-display);">{{ __('place.find_place') }}</h2>
                        @if($establecimiento->direccion)
                            <p class="mt-2 text-sm text-[#678183]">{{ $establecimiento->direccion }}</p>
                        @endif
                    </div>
                    @if($tieneCoordenadas)
                        <a href="https://maps.google.com/?q={{ $establecimiento->latitud }},{{ $establecimiento->longitud }}" target="_blank" rel="noopener" class="inline-flex w-fit items-center gap-2 rounded-xl border border-[#bcd8d4] bg-white px-4 py-3 text-sm font-bold text-[#075d64] transition hover:border-[#16734f] hover:bg-[#eef8f6]">
                            {{ __('place.open_maps') }}
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 3h7v7m0-7-9 9M5 7v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-5"/></svg>
                        </a>
                    @endif
                </div>

                @if($tieneCoordenadas)
                    <div class="relative h-[22rem] bg-[#e7f1ef] sm:h-[28rem]">
                        <div id="interactive_map_establecimiento" class="h-full w-full"></div>
                        <div class="pointer-events-none absolute bottom-4 left-4 rounded-xl bg-white/95 px-3 py-2 text-xs font-bold text-[#35676a] shadow-lg backdrop-blur-sm">{{ __('attraction.registered_location') }}</div>
                    </div>
                @else
                    <div class="relative flex min-h-64 items-center justify-center overflow-hidden bg-[#eaf4f2] px-6 text-center">
                        <svg viewBox="0 0 540 260" class="absolute inset-0 h-full w-full text-[#16734f]/10" fill="none" aria-hidden="true"><path d="M0 210c84-67 164-54 246 20 77-112 168-110 294-36" stroke="currentColor" stroke-width="3"/><path d="M135 260V130l48-36 48 36v130M328 260V88l62-42 62 42v172" stroke="currentColor" stroke-width="3"/></svg>
                        <div class="relative max-w-sm">
                            <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-[#16734f] shadow-sm"><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 21s7-5.1 7-12a7 7 0 1 0-14 0c0 6.9 7 12 7 12Z"/><circle cx="12" cy="9" r="2.2" stroke-width="1.8"/></svg></span>
                            <p class="mt-4 text-sm font-bold text-[#2c6064]">{{ __('attraction.location_unavailable') }}</p>
                            <p class="mt-2 text-sm leading-6 text-[#6a8586]">{{ __('place.location_hint') }}</p>
                        </div>
                    </div>
                @endif
            </section>
            <section id="resenas" class="scroll-mt-24">
                @include('valoraciones::components.form', ['modelo' => $establecimiento, 'tipo' => 'establecimiento'])
            </section>
        </div>

        <aside class="space-y-4 lg:sticky lg:top-24">
            <section class="overflow-hidden rounded-[1.5rem] border border-[#dbe9e5] bg-white shadow-[0_18px_42px_-34px_rgba(8,75,80,0.6)]">
                <div class="border-b border-gray-100 px-5 py-4">
                    <h2 class="font-bold text-gray-900">{{ __('place.place_facts') }}</h2>
                </div>

                <dl class="divide-y divide-[#e7efed] px-5">
                    <div class="flex items-start gap-3 py-4">
                        <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-[#eaf5f3] text-[#16734f]"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 21V6a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v15M9 21v-5h6v5M8 8h.01M12 8h.01M16 8h.01M8 12h.01M12 12h.01M16 12h.01"/></svg></span>
                        <div>
                            <dt class="text-[0.65rem] font-extrabold uppercase tracking-[0.12em] text-[#779091]">{{ __('place.service') }}</dt>
                            <dd class="mt-1 text-sm font-bold text-[#2a5558]">{{ $tipoEstablecimiento }}</dd>
                        </div>
                    </div>
                    @if($establecimiento->destino)
                        <div class="flex items-start gap-3 py-4">
                            <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-[#fff3dd] text-[#a76d1f]"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 21s7-5.1 7-12a7 7 0 1 0-14 0c0 6.9 7 12 7 12Z"/><circle cx="12" cy="9" r="2.2" stroke-width="1.8"/></svg></span>
                            <div>
                                <dt class="text-[0.65rem] font-extrabold uppercase tracking-[0.12em] text-[#779091]">{{ __('attraction.destination') }}</dt>
                                <dd class="mt-1 text-sm font-bold text-[#2a5558]">{{ $establecimiento->destino->nombre }}</dd>
                            </div>
                        </div>
                    @endif
                    @if($rangoPrecio)
                        <div class="flex items-start gap-3 py-4">
                            <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-[#fff3dd] text-[#a76d1f]"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6v12m4-9.5A4 4 0 0 0 12 6c-2.21 0-4 1.12-4 2.5S9.79 11 12 11s4 1.12 4 2.5S14.21 16 12 16a4 4 0 0 1-4-2.5"/></svg></span>
                            <div>
                                <dt class="text-[0.65rem] font-extrabold uppercase tracking-[0.12em] text-[#779091]">{{ __('place.price_level') }}</dt>
                                <dd class="mt-1 text-sm font-bold text-[#2a5558]">{{ $rangoPrecio['etiqueta'] }} <span class="text-[#b57925]">{{ $rangoPrecio['simbolo'] }}</span></dd>
                                <dd class="mt-2 text-xs leading-5 text-gray-500">{{ __('place.approx_price_per_person') }} {{ $rangoPrecio['rango'] }}</dd>
                            </div>
                        </div>
                    @endif
                    <div class="flex items-start gap-3 py-4">
                        <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-[#edf5ff] text-[#3f719a]"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="16" rx="2" stroke-width="1.8"/><circle cx="8" cy="9" r="1.2" fill="currentColor"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m21 15-4-4L7 20"/></svg></span>
                        <div>
                            <dt class="text-[0.65rem] font-extrabold uppercase tracking-[0.12em] text-[#779091]">{{ __('attraction.images') }}</dt>
                            <dd class="mt-1 text-sm font-bold text-[#2a5558]">{{ $galeria->count() + ($establecimiento->imagen_portada ? 1 : 0) }} {{ ($galeria->count() + ($establecimiento->imagen_portada ? 1 : 0)) === 1 ? __('place.registered_one') : __('place.registered_many') }}</dd>
                        </div>
                    </div>
                </dl>
            </section>

            <section class="rounded-[1.5rem] border border-[#cfe3df] bg-[#eff8f6] p-5">
                <div class="flex gap-3">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-[#16734f] shadow-sm"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg></span>
                    <p class="text-sm leading-6 text-[#497173]">{{ __('place.provider_disclaimer') }}</p>
                </div>
            </section>
            <x-agregar-itinerario tipo="establecimiento" :item="$establecimiento" />

            @include('valoraciones::components.sidebar', ['modelo' => $establecimiento, 'tipo' => 'establecimiento'])

            <a href="{{ route('catalogo.establecimientos.index') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-[#b7d4d0] bg-white px-4 py-3 text-sm font-bold text-[#176168] transition hover:border-[#16734f] hover:bg-[#f0f9f7]">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 18-6-6 6-6"/></svg>
                {{ __('attraction.back_to_catalog') }}
            </a>
        </aside>
    </div>
</div>

@if($tieneCoordenadas)
    @push('scripts')
    <script>
        (function () {
            const target = document.getElementById('interactive_map_establecimiento');
            if (!target) return;
            const apiKey = @json(config('services.google_maps.key'));
            const point = {
                lat: {{ json_encode((float) $establecimiento->latitud) }},
                lng: {{ json_encode((float) $establecimiento->longitud) }}
            };
            const name = @json($establecimiento->nombre);
            const detail = @json($establecimiento->direccion ?? __('place.no_specific_address'));

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
                        zoom: 16,
                        disableDefaultUI: true,
                        zoomControl: true,
                        mapId: 'DEMO_MAP_ID',
                        gestureHandling: 'cooperative'
                    });
                    const markerContent = document.createElement('div');
                    markerContent.innerHTML = '<div class="flex cursor-pointer items-center justify-center rounded-full border-2 border-white bg-gray-900 p-2.5 text-white shadow-lg"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 21V6a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v15M9 21v-5h6v5"/></svg></div>';
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
