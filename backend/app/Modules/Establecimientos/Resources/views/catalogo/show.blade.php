@extends('layouts.app')

@section('title', $establecimiento->nombre)

@section('content')

{{-- ══ Carrusel de galería ══════════════════════════════════════════════ --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
    @include('components.galeria-carousel', [
        'imagenes'      => $establecimiento->imagenes,
        'imagenPortada' => $establecimiento->imagen_portada,
        'nombreEntidad' => $establecimiento->nombre,
        'carouselId'    => 'est-' . $establecimiento->id,
    ])

    @if(!$establecimiento->imagen_portada && $establecimiento->imagenes->isEmpty())
        @php $iconos = ['hotel'=>'🏨','restaurante'=>'🍽️','transporte'=>'🚌','agencia'=>'🏢','otro'=>'📋']; @endphp
        <div class="w-full rounded-xl flex items-center justify-center"
             style="height: 280px; background: var(--color-amber-soft);">
            <span class="text-8xl">{{ $iconos[$establecimiento->tipo] ?? '🏢' }}</span>
        </div>
    @endif
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-1.5 text-sm text-gray-400 mb-6">
        <a href="{{ route('catalogo.establecimientos.index') }}" class="hover:text-gray-600 transition-colors">Establecimientos</a>
        <span>›</span>
        @if($establecimiento->destino)
            <span>{{ $establecimiento->destino->nombre }}</span>
            <span>›</span>
        @endif
        <span class="font-medium" style="color: var(--color-primary-700);">{{ $establecimiento->nombre }}</span>
    </nav>

    <div class="flex flex-col lg:flex-row gap-8 items-start">

        {{-- ══ COLUMNA IZQUIERDA ══════════════════════════════════════════ --}}
        <div class="flex-1 min-w-0 space-y-5">

            {{-- Chip tipo + Título + Lugar --}}
            <div>
                <div class="flex flex-wrap gap-2 mb-3">
                    <span class="tc-badge-tipo">{{ ucfirst($establecimiento->tipo) }}</span>
                </div>
                <h1 style="font-family: var(--font-display); font-size: 1.875rem; font-weight: 800; color: #1a2232; line-height: 1.2;">
                    {{ $establecimiento->nombre }}
                </h1>
                <div class="flex items-center gap-2 mt-1.5 text-sm text-gray-400">
                    @if($establecimiento->destino)<span>{{ $establecimiento->destino->nombre }}</span>@endif
                    @if($establecimiento->direccion)
                        @if($establecimiento->destino)<span>·</span>@endif
                        <span>{{ $establecimiento->direccion }}</span>
                    @endif
                </div>
            </div>

            {{-- Sobre el establecimiento + categorías --}}
            <div class="tc-card p-5">
                <h2 class="text-xs font-bold uppercase tracking-wide mb-3" style="color: var(--color-primary-700);">
                    Sobre el establecimiento
                </h2>
                @if($establecimiento->descripcion)
                    <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line mb-4">
                        {{ $establecimiento->descripcion }}
                    </p>
                @endif
                @if($establecimiento->categorias->isNotEmpty())
                    <div class="flex flex-wrap gap-2 pt-3 border-t border-gray-100">
                        @foreach($establecimiento->categorias as $cat)
                            <span class="tc-chip">{{ $cat->nombre }}</span>
                        @endforeach
                    </div>
                @endif
            </div>

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
                    $horariosPorDia = $establecimiento->horarios->keyBy('dia');
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
                                        <td class="py-3 pr-4 text-gray-400">—</td>
                                        <td class="py-3 pr-4 text-gray-400">—</td>
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
            @if($establecimiento->latitud !== null && $establecimiento->longitud !== null)
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
                            {{ number_format($establecimiento->latitud, 6) }}, {{ number_format($establecimiento->longitud, 6) }}
                        </p>
                        <a href="https://maps.google.com/?q={{ $establecimiento->latitud }},{{ $establecimiento->longitud }}"
                           target="_blank" rel="noopener" class="tc-btn-outline text-xs">
                            Ver en Google Maps ↗
                        </a>
                    </div>
                </div>
            @endif
        </div>

        {{-- ══ COLUMNA DERECHA ════════════════════════════════════════════ --}}
        <div class="w-full lg:w-72 lg:shrink-0 space-y-4">

            {{-- Rango de precio --}}
            @php
                $rangosDisplay = [
                    'bajo'  => ['label' => 'Bajo', 'simbolo' => '$'],
                    'medio' => ['label' => 'Medio', 'simbolo' => '$$'],
                    'alto'  => ['label' => 'Alto', 'simbolo' => '$$$'],
                    'lujo'  => ['label' => 'Lujo', 'simbolo' => '$$$$'],
                ];
                $rp = $rangosDisplay[$establecimiento->rango_precio] ?? null;
            @endphp
            @if($rp)
                <div class="tc-card p-5">
                    <p class="text-xs font-bold uppercase tracking-wide mb-2" style="color: var(--color-primary-700);">
                        Rango de precio
                    </p>
                    <p style="font-family: var(--font-display); font-size: 2.25rem; font-weight: 800; color: #1a2232; line-height: 1.1;" class="mb-1">
                        {{ $rp['label'] }} {{ $rp['simbolo'] }}
                    </p>
                </div>
            @endif

            <a href="{{ route('catalogo.establecimientos.index') }}"
               class="tc-btn-outline w-full justify-center text-sm">
                ← Volver al catálogo
            </a>
        </div>
    </div>
</div>
@endsection
