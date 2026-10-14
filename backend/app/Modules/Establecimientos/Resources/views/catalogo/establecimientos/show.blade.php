@extends('layouts.app')

@section('title', $establecimiento->nombre)

@section('content')
@php
    $tipoEstablecimiento = [
        'hotel'       => 'Hospedaje',
        'restaurante' => 'Gastronomía',
        'transporte'  => 'Movilidad',
        'agencia'     => 'Agencia',
        'otro'        => 'Servicio turístico',
    ][$establecimiento->tipo] ?? ucfirst($establecimiento->tipo);

    $rangoPrecio = [
        'bajo'  => ['etiqueta' => 'Bajo', 'simbolo' => '$'],
        'medio' => ['etiqueta' => 'Medio', 'simbolo' => '$$'],
        'alto'  => ['etiqueta' => 'Alto', 'simbolo' => '$$$'],
        'lujo'  => ['etiqueta' => 'Lujoso', 'simbolo' => '$$$$'],
    ][$establecimiento->rango_precio] ?? null;

    $dias = [
        'lunes'     => 'Lunes',
        'martes'    => 'Martes',
        'miercoles' => 'Miércoles',
        'jueves'    => 'Jueves',
        'viernes'   => 'Viernes',
        'sabado'    => 'Sábado',
        'domingo'   => 'Domingo',
    ];
    $horarios = $establecimiento->horarios;
    $galeria = $establecimiento->imagenes;
    $tieneCoordenadas = $establecimiento->latitud !== null && $establecimiento->longitud !== null;
@endphp

<div class="tc-catalog-shell max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
    <nav class="mb-5 flex flex-wrap items-center gap-2 text-sm" aria-label="Migas de pan">
        <a href="{{ route('catalogo.establecimientos.index') }}" class="font-semibold text-primary-700 hover:underline">Establecimientos</a>
        <span class="text-gray-400" aria-hidden="true">/</span>
        <span class="text-gray-500">{{ $establecimiento->nombre }}</span>
    </nav>
    <header class="tc-page-heading" aria-labelledby="establecimiento-titulo">
        <p class="text-xs font-semibold uppercase tracking-widest text-primary-700">{{ $tipoEstablecimiento }}@if($establecimiento->destino) · {{ $establecimiento->destino->nombre }}@endif</p>
        <h1 id="establecimiento-titulo" class="mt-2 text-3xl font-bold tracking-tight text-gray-900">{{ $establecimiento->nombre }}</h1>
        @if($establecimiento->direccion)<p class="mt-2 text-sm leading-6 text-gray-600">{{ $establecimiento->direccion }}</p>@endif
        <div class="mt-4 flex flex-wrap gap-3">
            <a href="#horarios" class="text-sm font-semibold text-primary-700 hover:underline">Ver horarios</a>
            <a href="#ubicacion" class="text-sm font-semibold text-primary-700 hover:underline">Ver ubicación</a>
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
                        <p class="text-[0.68rem] font-extrabold uppercase tracking-[0.17em] text-[#4d8584]">Sobre este lugar</p>
                        <h2 class="mt-2 text-2xl font-extrabold tracking-[-0.035em] text-[#173f43]" style="font-family: var(--font-display);">Información para planificar tu visita</h2>
                    </div>
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-[#eaf5f3] text-[#00626A]" aria-hidden="true">
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
                    <p class="mt-6 text-[0.98rem] leading-8 text-[#6f8687]">Este establecimiento está registrado en el catálogo turístico. Revisa sus horarios, ubicación y categorías antes de incluirlo en tu recorrido.</p>
                @endif

                @if($establecimiento->categorias->isNotEmpty())
                    <div class="mt-7 border-t border-[#e4efec] pt-5">
                        <p class="mb-3 text-[0.67rem] font-extrabold uppercase tracking-[0.14em] text-[#497e80]">Categorías registradas</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($establecimiento->categorias as $categoria)
                                <span class="rounded-full border border-[#cde3df] bg-[#f0f8f6] px-3 py-1.5 text-xs font-bold text-[#246a6d]">{{ $categoria->nombre }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </section>

            @if($galeria->isNotEmpty())
                <section class="overflow-hidden rounded-[1.6rem] border border-[#dce8e5] bg-white p-6 shadow-[0_18px_42px_-34px_rgba(8,75,80,0.55)] sm:p-8">
                    <div class="mb-5 flex items-end justify-between gap-3">
                        <div>
                            <p class="text-[0.68rem] font-extrabold uppercase tracking-[0.17em] text-[#4d8584]">Galería</p>
                            <h2 class="mt-1 text-2xl font-extrabold tracking-[-0.035em] text-[#173f43]" style="font-family: var(--font-display);">Mira el espacio antes de ir</h2>
                        </div>
                        <span class="rounded-full bg-[#fff3dd] px-3 py-1.5 text-xs font-bold text-[#8d5c18]">{{ $galeria->count() }} {{ $galeria->count() === 1 ? 'imagen' : 'imágenes' }}</span>
                    </div>
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                        @foreach($galeria as $imagen)
                            <a href="{{ asset('storage/' . $imagen->url) }}" target="_blank" rel="noopener" class="group relative aspect-[4/3] overflow-hidden rounded-2xl bg-[#e9f4f2] focus:outline-none focus:ring-4 focus:ring-[#00626A]/15">
                                <img src="{{ asset('storage/' . $imagen->url) }}" alt="{{ $imagen->alt_text ?: $establecimiento->nombre }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                                <span class="absolute inset-0 flex items-center justify-center bg-[#043d42]/0 text-white opacity-0 transition group-hover:bg-[#043d42]/35 group-hover:opacity-100">
                                    <span class="rounded-full bg-white/90 p-2 text-[#07565d]">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 3 6 6m-1.5-4.5L13 11m-2 2-6.5 6.5M5 19h.01"/></svg>
                                    </span>
                                </span>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            <section id="horarios" class="rounded-[1.6rem] border border-[#dce8e5] bg-white p-6 shadow-[0_18px_42px_-34px_rgba(8,75,80,0.55)] sm:p-8 scroll-mt-24">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-[0.68rem] font-extrabold uppercase tracking-[0.17em] text-[#4d8584]">Horarios registrados</p>
                        <h2 class="mt-1 text-2xl font-extrabold tracking-[-0.035em] text-[#173f43]" style="font-family: var(--font-display);">Organiza tu visita</h2>
                    </div>
                    <span class="inline-flex w-fit items-center gap-2 rounded-full bg-[#eaf5f3] px-3 py-1.5 text-xs font-bold text-[#266a6e]">
                        <span class="h-1.5 w-1.5 rounded-full bg-[#2f9f78]"></span>
                        Horario publicado
                    </span>
                </div>

                <div class="mt-6 grid gap-2.5 sm:grid-cols-2">
                    @foreach($dias as $clave => $nombreDia)
                        @php
                            $horario = $horarios[$clave] ?? null;
                        @endphp
                        <div class="flex items-center justify-between gap-3 rounded-2xl border px-4 py-3.5 {{ $horario ? 'border-[#d8e9e5] bg-[#fbfefd]' : 'border-[#edf1f0] bg-[#fafbfb]' }}">
                            <span class="text-sm font-bold {{ $horario ? 'text-[#244e51]' : 'text-[#839596]' }}">{{ $nombreDia }}</span>
                            @if($horario)
                                <span class="inline-flex items-center gap-1.5 rounded-lg bg-[#e8f5f2] px-2.5 py-1.5 text-xs font-bold text-[#17666b]">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" stroke-width="1.8"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 7v5l3 2"/></svg>
                                    {{ $horario['abre'] ?? '—' }} – {{ $horario['cierra'] ?? '—' }}
                                </span>
                            @else
                                <span class="text-xs font-semibold text-[#9aabaa]">No registrado</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>

            <section id="ubicacion" class="overflow-hidden rounded-[1.6rem] border border-[#dce8e5] bg-white shadow-[0_18px_42px_-34px_rgba(8,75,80,0.55)] scroll-mt-24">
                <div class="flex flex-col gap-4 border-b border-[#e2eeeb] p-6 sm:flex-row sm:items-center sm:justify-between sm:p-8">
                    <div>
                        <p class="text-[0.68rem] font-extrabold uppercase tracking-[0.17em] text-[#4d8584]">Ubicación</p>
                        <h2 class="mt-1 text-2xl font-extrabold tracking-[-0.035em] text-[#173f43]" style="font-family: var(--font-display);">Encuentra este establecimiento</h2>
                        @if($establecimiento->direccion)
                            <p class="mt-2 text-sm text-[#678183]">{{ $establecimiento->direccion }}</p>
                        @endif
                    </div>
                    @if($tieneCoordenadas)
                        <a href="https://maps.google.com/?q={{ $establecimiento->latitud }},{{ $establecimiento->longitud }}" target="_blank" rel="noopener" class="inline-flex w-fit items-center gap-2 rounded-xl border border-[#bcd8d4] bg-white px-4 py-3 text-sm font-bold text-[#075d64] transition hover:border-[#00626A] hover:bg-[#eef8f6]">
                            Abrir en Maps
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 3h7v7m0-7-9 9M5 7v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-5"/></svg>
                        </a>
                    @endif
                </div>

                @if($tieneCoordenadas)
                    <div class="relative h-[22rem] bg-[#e7f1ef] sm:h-[28rem]">
                        <div id="interactive_map_establecimiento" class="h-full w-full"></div>
                        <div class="pointer-events-none absolute bottom-4 left-4 rounded-xl bg-white/95 px-3 py-2 text-xs font-bold text-[#35676a] shadow-lg backdrop-blur-sm">Ubicación registrada</div>
                    </div>
                @else
                    <div class="relative flex min-h-64 items-center justify-center overflow-hidden bg-[#eaf4f2] px-6 text-center">
                        <svg viewBox="0 0 540 260" class="absolute inset-0 h-full w-full text-[#00626A]/10" fill="none" aria-hidden="true"><path d="M0 210c84-67 164-54 246 20 77-112 168-110 294-36" stroke="currentColor" stroke-width="3"/><path d="M135 260V130l48-36 48 36v130M328 260V88l62-42 62 42v172" stroke="currentColor" stroke-width="3"/></svg>
                        <div class="relative max-w-sm">
                            <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-[#00626A] shadow-sm"><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 21s7-5.1 7-12a7 7 0 1 0-14 0c0 6.9 7 12 7 12Z"/><circle cx="12" cy="9" r="2.2" stroke-width="1.8"/></svg></span>
                            <p class="mt-4 text-sm font-bold text-[#2c6064]">La ubicación exacta aún no fue registrada.</p>
                            <p class="mt-2 text-sm leading-6 text-[#6a8586]">Consulta la dirección publicada por el proveedor antes de planificar tu desplazamiento.</p>
                        </div>
                    </div>
                @endif
            </section>
        </div>

        <aside class="space-y-4 lg:sticky lg:top-24">
            <section class="overflow-hidden rounded-[1.5rem] border border-[#dbe9e5] bg-white shadow-[0_18px_42px_-34px_rgba(8,75,80,0.6)]">
                <div class="border-b border-gray-100 px-5 py-4">
                    <h2 class="font-bold text-gray-900">Datos del establecimiento</h2>
                </div>

                <dl class="divide-y divide-[#e7efed] px-5">
                    <div class="flex items-start gap-3 py-4">
                        <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-[#eaf5f3] text-[#00626A]"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 21V6a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v15M9 21v-5h6v5M8 8h.01M12 8h.01M16 8h.01M8 12h.01M12 12h.01M16 12h.01"/></svg></span>
                        <div>
                            <dt class="text-[0.65rem] font-extrabold uppercase tracking-[0.12em] text-[#779091]">Servicio</dt>
                            <dd class="mt-1 text-sm font-bold text-[#2a5558]">{{ $tipoEstablecimiento }}</dd>
                        </div>
                    </div>
                    @if($establecimiento->destino)
                        <div class="flex items-start gap-3 py-4">
                            <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-[#fff3dd] text-[#a76d1f]"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 21s7-5.1 7-12a7 7 0 1 0-14 0c0 6.9 7 12 7 12Z"/><circle cx="12" cy="9" r="2.2" stroke-width="1.8"/></svg></span>
                            <div>
                                <dt class="text-[0.65rem] font-extrabold uppercase tracking-[0.12em] text-[#779091]">Destino</dt>
                                <dd class="mt-1 text-sm font-bold text-[#2a5558]">{{ $establecimiento->destino->nombre }}</dd>
                            </div>
                        </div>
                    @endif
                    @if($rangoPrecio)
                        <div class="flex items-start gap-3 py-4">
                            <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-[#fff3dd] text-[#a76d1f]"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6v12m4-9.5A4 4 0 0 0 12 6c-2.21 0-4 1.12-4 2.5S9.79 11 12 11s4 1.12 4 2.5S14.21 16 12 16a4 4 0 0 1-4-2.5"/></svg></span>
                            <div>
                                <dt class="text-[0.65rem] font-extrabold uppercase tracking-[0.12em] text-[#779091]">Nivel de precio</dt>
                                <dd class="mt-1 text-sm font-bold text-[#2a5558]">{{ $rangoPrecio['etiqueta'] }} <span class="text-[#b57925]">{{ $rangoPrecio['simbolo'] }}</span></dd>
                            </div>
                        </div>
                    @endif
                    <div class="flex items-start gap-3 py-4">
                        <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-[#edf5ff] text-[#3f719a]"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="16" rx="2" stroke-width="1.8"/><circle cx="8" cy="9" r="1.2" fill="currentColor"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m21 15-4-4L7 20"/></svg></span>
                        <div>
                            <dt class="text-[0.65rem] font-extrabold uppercase tracking-[0.12em] text-[#779091]">Imágenes</dt>
                            <dd class="mt-1 text-sm font-bold text-[#2a5558]">{{ $galeria->count() + ($establecimiento->imagen_portada ? 1 : 0) }} {{ ($galeria->count() + ($establecimiento->imagen_portada ? 1 : 0)) === 1 ? 'registrada' : 'registradas' }}</dd>
                        </div>
                    </div>
                </dl>
            </section>

            <section class="rounded-[1.5rem] border border-[#cfe3df] bg-[#eff8f6] p-5">
                <div class="flex gap-3">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-[#00626A] shadow-sm"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg></span>
                    <p class="text-sm leading-6 text-[#497173]">Los datos mostrados corresponden a la información registrada por el proveedor.</p>
                </div>
            </section>

            <a href="{{ route('catalogo.establecimientos.index') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-[#b7d4d0] bg-white px-4 py-3 text-sm font-bold text-[#176168] transition hover:border-[#00626A] hover:bg-[#f0f9f7]">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 18-6-6 6-6"/></svg>
                Volver al catálogo
            </a>
        </aside>
    </div>
</div>

@if($tieneCoordenadas)
    @push('scripts')
    <script>
        (function loadGoogleMaps() {
            if (window.google && window.google.maps) {
                initEstablecimientoMap();
                return;
            }

            var script = document.createElement('script');
            script.src = 'https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&v=weekly&libraries=marker';
            script.async = true;
            script.onload = initEstablecimientoMap;
            document.head.appendChild(script);
        })();

        function initEstablecimientoMap() {
            if (!window.google || !window.google.maps) return;

            var latLng = {
                lat: {{ json_encode((float) $establecimiento->latitud) }},
                lng: {{ json_encode((float) $establecimiento->longitud) }}
            };
            var map = new google.maps.Map(document.getElementById('interactive_map_establecimiento'), {
                center: latLng,
                zoom: 16,
                disableDefaultUI: true,
                zoomControl: true,
                gestureHandling: 'cooperative',
                styles: [
                    { featureType: 'poi', elementType: 'labels', stylers: [{ visibility: 'off' }] },
                    { featureType: 'transit', elementType: 'labels', stylers: [{ visibility: 'off' }] },
                    { featureType: 'water', elementType: 'geometry', stylers: [{ color: '#dbecea' }] },
                    { featureType: 'landscape', elementType: 'geometry', stylers: [{ color: '#f4f7f5' }] },
                    { featureType: 'road', elementType: 'geometry', stylers: [{ color: '#ffffff' }] },
                    { featureType: 'road', elementType: 'geometry.stroke', stylers: [{ color: '#e1eae8' }] }
                ]
            });

            new google.maps.Marker({
                map: map,
                position: latLng,
                title: @json($establecimiento->nombre),
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: 9,
                    fillColor: '#00626A',
                    fillOpacity: 1,
                    strokeColor: '#ffffff',
                    strokeWeight: 3
                }
            });
        }
    </script>
    @endpush
@endif
@endsection
