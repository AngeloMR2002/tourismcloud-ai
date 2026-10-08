@extends('layouts.app')

@section('title', $atractivo->nombre)

@section('content')

{{-- ══ Carrusel de galería ══════════════════════════════════════════════ --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
    @include('components.galeria-carousel', [
        'imagenes'      => $atractivo->imagenes,
        'imagenPortada' => $atractivo->imagen_portada,
        'nombreEntidad' => $atractivo->nombre,
        'carouselId'    => 'atractivo-' . $atractivo->id,
    ])

    {{-- Placeholder si no hay imágenes --}}
    @if(!$atractivo->imagen_portada && $atractivo->imagenes->isEmpty())
        <div class="w-full rounded-xl flex items-center justify-center"
             style="height: 280px; background: var(--color-teal-light);">
            <svg class="w-16 h-16" style="color: var(--color-primary-300);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
    @endif
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-1.5 text-sm text-gray-400 mb-6">
        <a href="{{ route('catalogo.atractivos.index') }}" class="hover:text-gray-600 transition-colors">Atractivos</a>
        <span>›</span>
        @if($atractivo->destino)
            <span>{{ $atractivo->destino->nombre }}</span>
            <span>›</span>
        @endif
        <span class="font-medium" style="color: var(--color-primary-700);">{{ $atractivo->nombre }}</span>
    </nav>

    {{-- ══ Layout 2 columnas ══════════════════════════════════════════════ --}}
    <div class="flex gap-8 items-start">

        {{-- ══ COLUMNA IZQUIERDA (flex-1) ══════════════════════════════════ --}}
        <div class="flex-1 min-w-0 space-y-5">

            {{-- Chips + Título + Lugar · Duración --}}
            <div>
                @if($atractivo->categorias->isNotEmpty())
                    <div class="flex flex-wrap gap-2 mb-3">
                        @foreach($atractivo->categorias as $cat)
                            <span class="tc-chip">{{ $cat->nombre }}</span>
                        @endforeach
                    </div>
                @endif

                <h1 style="font-family: var(--font-display); font-size: 1.875rem; font-weight: 800; color: #1a2232; line-height: 1.2;">
                    {{ $atractivo->nombre }}
                </h1>

                <div class="flex items-center gap-2 mt-1.5 text-sm text-gray-400">
                    @if($atractivo->destino)<span>{{ $atractivo->destino->nombre }}</span>@endif
                    @if($atractivo->duracion_estimada_min)
                        @php $h = intdiv($atractivo->duracion_estimada_min, 60); $m = $atractivo->duracion_estimada_min % 60; @endphp
                        @if($atractivo->destino)<span>·</span>@endif
                        <span>{{ $h > 0 ? "{$h}h " : '' }}{{ $m > 0 ? "{$m}min" : '' }}</span>
                    @endif
                </div>
            </div>

            {{-- Descripción --}}
            @if($atractivo->descripcion)
                <div class="tc-card p-5">
                    <h2 class="text-xs font-bold uppercase tracking-wide mb-3" style="color: var(--color-primary-700);">
                        Descripción
                    </h2>
                    <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">{{ $atractivo->descripcion }}</p>
                </div>
            @endif

            {{-- ── Horario de atención — tabla 4 columnas ────────────────── --}}
            <div class="tc-card p-5">
                <h2 class="text-xs font-bold uppercase tracking-wide mb-4" style="color: var(--color-primary-700);">
                    Horario de atención
                </h2>
                @php
                    $dias = [
                        'lunes'     => 'Lunes',
                        'martes'    => 'Martes',
                        'miercoles' => 'Miércoles',
                        'jueves'    => 'Jueves',
                        'viernes'   => 'Viernes',
                        'sabado'    => 'Sábado',
                        'domingo'   => 'Domingo',
                    ];
                    $horariosPorDia = $atractivo->horarios->keyBy('dia');
                @endphp
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr style="border-bottom: 2px solid #f0f0f0;">
                                <th class="text-left font-bold pb-2.5 pr-4" style="color: #6b7280; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em; width: 28%;">Día</th>
                                <th class="text-left font-bold pb-2.5 pr-4" style="color: #6b7280; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em; width: 25%;">Hora de entrada</th>
                                <th class="text-left font-bold pb-2.5 pr-4" style="color: #6b7280; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em; width: 25%;">Hora de salida</th>
                                <th class="text-left font-bold pb-2.5"     style="color: #6b7280; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em; width: 22%;">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dias as $clave => $nombre)
                                @php $horario = $horariosPorDia->get($clave); @endphp
                                <tr class="border-b border-gray-50 last:border-0"
                                    style="{{ $horario ? 'background: transparent;' : '' }}">
                                    <td class="py-3 pr-4 font-semibold" style="color: #374151;">
                                        {{ $nombre }}
                                    </td>
                                    @if($horario)
                                        <td class="py-3 pr-4">
                                            <span class="inline-flex items-center gap-1.5 text-sm font-medium px-2.5 py-1 rounded-md"
                                                  style="background: var(--color-teal-light); color: var(--color-primary-700);">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <circle cx="12" cy="12" r="10"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2"/>
                                                </svg>
                                                {{ substr((string) $horario->hora_inicio, 0, 5) }}
                                            </span>
                                        </td>
                                        <td class="py-3 pr-4">
                                            <span class="inline-flex items-center gap-1.5 text-sm font-medium px-2.5 py-1 rounded-md"
                                                  style="background: #fff7ed; color: #9a3412;">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <circle cx="12" cy="12" r="10"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2"/>
                                                </svg>
                                                {{ substr((string) $horario->hora_fin, 0, 5) }}
                                            </span>
                                        </td>
                                        <td class="py-3">
                                            <span class="inline-flex items-center gap-1 text-xs font-bold px-2.5 py-1 rounded-full"
                                                  style="background: #dcfce7; color: #15803d;">
                                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block"></span>
                                                Abierto
                                            </span>
                                        </td>
                                    @else
                                        <td class="py-3 pr-4 text-gray-400 text-sm">—</td>
                                        <td class="py-3 pr-4 text-gray-400 text-sm">—</td>
                                        <td class="py-3">
                                            <span class="inline-flex items-center gap-1 text-xs font-bold px-2.5 py-1 rounded-full"
                                                  style="background: #f3f4f6; color: #9ca3af;">
                                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400 inline-block"></span>
                                                Cerrado
                                            </span>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Ubicación --}}
            @if($atractivo->latitud && $atractivo->longitud)
                <div class="tc-card p-5">
                    <h2 class="text-xs font-bold uppercase tracking-wide mb-3" style="color: var(--color-primary-700);">
                        Ubicación
                    </h2>
                    <div class="tc-map-placeholder rounded-xl flex flex-col items-center justify-center gap-3"
                         style="height: 180px;">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             style="color: var(--color-primary-600);">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <p class="text-sm font-medium" style="color: var(--color-primary-700);">
                            {{ number_format($atractivo->latitud, 6) }}, {{ number_format($atractivo->longitud, 6) }}
                        </p>
                        <a href="https://maps.google.com/?q={{ $atractivo->latitud }},{{ $atractivo->longitud }}"
                           target="_blank" rel="noopener" class="tc-btn-outline text-xs">
                            Ver en Google Maps ↗
                        </a>
                    </div>
                </div>
            @endif
        </div>

        {{-- ══ COLUMNA DERECHA (w-72) ══════════════════════════════════════ --}}
        <div class="w-72 shrink-0 space-y-4">

            {{-- Costo de entrada --}}
            <div class="tc-card p-5">
                <p class="text-xs font-bold uppercase tracking-wide mb-1" style="color: var(--color-primary-700);">
                    Costo de entrada
                </p>
                <p style="font-family: var(--font-display); font-size: 2.25rem; font-weight: 800; color: #1a2232; line-height: 1.1;" class="mb-0.5">
                    @if($atractivo->costo_entrada > 0)
                        S/ {{ number_format($atractivo->costo_entrada, 2) }}
                    @else
                        <span style="color: #16a34a;">Gratuito</span>
                    @endif
                </p>
                @if($atractivo->costo_entrada > 0)
                    <p class="text-xs text-gray-400">por persona</p>
                @endif
            </div>

            {{-- Reseñas --}}
            <div class="tc-card p-5">
                <div class="flex items-center gap-2 mb-1">
                    <svg class="w-5 h-5" style="color: #f59e0b;" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    <span class="font-bold text-lg" style="color: #1a2232;">5.00</span>
                    <span class="text-sm text-gray-400">· 0 reseñas</span>
                </div>
                <div class="h-px mb-4" style="background: #f0f0f0;"></div>
                <p class="text-xs font-bold uppercase tracking-wide mb-3" style="color: var(--color-primary-700);">
                    Valoración general
                </p>
                <div class="space-y-2 mb-4">
                    @foreach([5,4,3,2,1] as $estrella)
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-400 w-2 shrink-0">{{ $estrella }}</span>
                            <div class="flex-1 h-1.5 rounded-full" style="background: #e5e7eb;">
                                <div class="h-full rounded-full" style="background: #f59e0b; width: 0%;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <p class="text-xs text-center text-gray-400">Sé el primero en dejar una reseña.</p>
            </div>

            <a href="{{ route('catalogo.atractivos.index') }}"
               class="tc-btn-outline w-full justify-center text-sm">
                ← Volver al catálogo
            </a>
        </div>
    </div>
</div>
@endsection
