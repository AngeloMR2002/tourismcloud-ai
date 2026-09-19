@extends('layouts.app')

@section('title', $atractivo->nombre)

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- ── Breadcrumb ───────────────────────────────────────────── --}}
    <nav class="flex items-center gap-2 text-sm text-gray-400 mb-6">
        <a href="{{ route('catalogo.atractivos.index') }}" class="hover:text-gray-600 transition-colors">Atractivos</a>
        <span>/</span>
        @if($atractivo->destino)
            <span>{{ $atractivo->destino->nombre }}</span>
            <span>/</span>
        @endif
        <span class="font-medium" style="color: var(--color-primary-700);">{{ $atractivo->nombre }}</span>
    </nav>

    {{-- ── Imagen de portada + galería ─────────────────────────── --}}
    @if($atractivo->imagen_portada || $atractivo->imagenes->isNotEmpty())
        <div class="mb-8 rounded-xl overflow-hidden" style="background: var(--color-teal-light);">
            {{-- Portada principal --}}
            @if($atractivo->imagen_portada)
                <img src="{{ asset('storage/' . $atractivo->imagen_portada) }}"
                     alt="{{ $atractivo->nombre }}"
                     class="w-full object-cover"
                     style="max-height: 420px;">
            @endif

            {{-- Galería de imágenes adicional --}}
            @if($atractivo->imagenes->isNotEmpty())
                <div class="p-4 tc-gallery-grid">
                    @foreach($atractivo->imagenes as $imagen)
                        <div class="tc-gallery-item">
                            <img src="{{ asset('storage/' . $imagen->url) }}"
                                 alt="{{ $imagen->alt_text ?? $atractivo->nombre }}"
                                 loading="lazy">
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @else
        {{-- Placeholder sin imagen --}}
        <div class="mb-8 rounded-xl flex items-center justify-center" style="height: 300px; background: var(--color-teal-light);">
            <svg class="w-20 h-20" style="color: var(--color-primary-300);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- ── Columna principal ─────────────────────────────────── --}}
        <div class="lg:col-span-2">

            {{-- Título y chips --}}
            <div class="mb-6">
                <h1 style="font-family: var(--font-display); font-size: 1.875rem; font-weight: 800; color: #1a2232; line-height: 1.2;">
                    {{ $atractivo->nombre }}
                </h1>
                @if($atractivo->destino)
                    <p class="mt-1 text-sm" style="color: var(--color-primary-600);">
                        📍 {{ $atractivo->destino->nombre }}
                        @if($atractivo->destino->pais ?? false)
                            — {{ $atractivo->destino->pais }}
                        @endif
                    </p>
                @endif
                @if($atractivo->categorias->isNotEmpty())
                    <div class="flex flex-wrap gap-2 mt-3">
                        @foreach($atractivo->categorias as $cat)
                            <span class="tc-chip">{{ $cat->nombre }}</span>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Descripción --}}
            @if($atractivo->descripcion)
                <div class="tc-card p-6 mb-6">
                    <h2 class="text-sm font-bold uppercase tracking-wide mb-3" style="color: var(--color-primary-700);">
                        Descripción
                    </h2>
                    <p class="text-gray-600 leading-relaxed whitespace-pre-line">{{ $atractivo->descripcion }}</p>
                </div>
            @endif

            {{-- ── Horarios semanales ─────────────────────────────── --}}
            @if($atractivo->horarios)
                <div class="tc-card p-6 mb-6">
                    <h2 class="text-sm font-bold uppercase tracking-wide mb-4" style="color: var(--color-primary-700);">
                        Horarios de apertura
                    </h2>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr>
                                    <th class="text-left pb-2 font-semibold text-gray-500 w-28">Día</th>
                                    <th class="text-left pb-2 font-semibold text-gray-500">Abre</th>
                                    <th class="text-left pb-2 font-semibold text-gray-500">Cierra</th>
                                    <th class="text-left pb-2 font-semibold text-gray-500">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @php
                                    $diasNombres = [
                                        'lunes'     => 'Lunes',
                                        'martes'    => 'Martes',
                                        'miercoles' => 'Miércoles',
                                        'jueves'    => 'Jueves',
                                        'viernes'   => 'Viernes',
                                        'sabado'    => 'Sábado',
                                        'domingo'   => 'Domingo',
                                    ];
                                @endphp
                                @foreach($diasNombres as $clave => $nombre)
                                    @php $horario = $atractivo->horarios[$clave] ?? null; @endphp
                                    <tr>
                                        <td class="py-2.5 font-medium text-gray-700">{{ $nombre }}</td>
                                        @if($horario)
                                            <td class="py-2.5 text-gray-600">{{ $horario['abre'] ?? '—' }}</td>
                                            <td class="py-2.5 text-gray-600">{{ $horario['cierra'] ?? '—' }}</td>
                                            <td class="py-2.5">
                                                <span class="tc-badge-activo">Abierto</span>
                                            </td>
                                        @else
                                            <td class="py-2.5 text-gray-400">—</td>
                                            <td class="py-2.5 text-gray-400">—</td>
                                            <td class="py-2.5">
                                                <span class="tc-badge-inactivo">Cerrado</span>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- ── Mapa ────────────────────────────────────────────── --}}
            @if($atractivo->latitud && $atractivo->longitud)
                <div class="tc-card p-6">
                    <h2 class="text-sm font-bold uppercase tracking-wide mb-4" style="color: var(--color-primary-700);">
                        Ubicación
                    </h2>
                    <div class="tc-map-placeholder h-56" id="mapa-atractivo"
                         data-lat="{{ $atractivo->latitud }}"
                         data-lng="{{ $atractivo->longitud }}"
                         data-nombre="{{ $atractivo->nombre }}">
                        <div class="text-center">
                            <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <p>{{ $atractivo->latitud }}, {{ $atractivo->longitud }}</p>
                            <a href="https://maps.google.com/?q={{ $atractivo->latitud }},{{ $atractivo->longitud }}"
                               target="_blank" rel="noopener"
                               class="mt-2 tc-btn-outline text-xs inline-flex">
                                Ver en Google Maps ↗
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- ── Sidebar de datos rápidos ──────────────────────────── --}}
        <div class="space-y-4">

            {{-- Costo de entrada --}}
            <div class="tc-card p-5">
                <p class="text-xs font-bold uppercase tracking-wide mb-1" style="color: var(--color-primary-700);">
                    Costo de entrada
                </p>
                <p class="text-2xl font-bold" style="font-family: var(--font-display); color: #1a2232;">
                    @if($atractivo->costo_entrada > 0)
                        S/ {{ number_format($atractivo->costo_entrada, 2) }}
                    @else
                        <span style="color: #16a34a;">Gratuito</span>
                    @endif
                </p>
            </div>

            {{-- Duración --}}
            @if($atractivo->duracion_estimada_min)
                <div class="tc-card p-5">
                    <p class="text-xs font-bold uppercase tracking-wide mb-1" style="color: var(--color-primary-700);">
                        Duración estimada
                    </p>
                    @php
                        $h = intdiv($atractivo->duracion_estimada_min, 60);
                        $m = $atractivo->duracion_estimada_min % 60;
                    @endphp
                    <p class="text-xl font-bold" style="font-family: var(--font-display); color: #1a2232;">
                        {{ $h > 0 ? "{$h} h " : '' }}{{ $m > 0 ? "{$m} min" : '' }}
                    </p>
                </div>
            @endif

            {{-- Volver al catálogo --}}
            <a href="{{ route('catalogo.atractivos.index') }}" class="tc-btn-outline w-full justify-center text-sm">
                ← Volver al catálogo
            </a>
        </div>

    </div>
</div>
@endsection
