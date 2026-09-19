@extends('layouts.app')

@section('title', $establecimiento->nombre)

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- ── Breadcrumb ───────────────────────────────────────────── --}}
    <nav class="flex items-center gap-2 text-sm text-gray-400 mb-6">
        <a href="{{ route('catalogo.establecimientos.index') }}" class="hover:text-gray-600 transition-colors">Establecimientos</a>
        <span>/</span>
        @if($establecimiento->destino)
            <span>{{ $establecimiento->destino->nombre }}</span>
            <span>/</span>
        @endif
        <span class="font-medium" style="color: var(--color-primary-700);">{{ $establecimiento->nombre }}</span>
    </nav>

    {{-- ── Imagen portada + galería ─────────────────────────────── --}}
    @if($establecimiento->imagen_portada || $establecimiento->imagenes->isNotEmpty())
        <div class="mb-8 rounded-xl overflow-hidden" style="background: var(--color-amber-soft);">
            @if($establecimiento->imagen_portada)
                <img src="{{ asset('storage/' . $establecimiento->imagen_portada) }}"
                     alt="{{ $establecimiento->nombre }}"
                     class="w-full object-cover"
                     style="max-height: 420px;">
            @endif
            @if($establecimiento->imagenes->isNotEmpty())
                <div class="p-4 tc-gallery-grid">
                    @foreach($establecimiento->imagenes as $imagen)
                        <div class="tc-gallery-item">
                            <img src="{{ asset('storage/' . $imagen->url) }}"
                                 alt="{{ $imagen->alt_text ?? $establecimiento->nombre }}"
                                 loading="lazy">
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @else
        <div class="mb-8 rounded-xl flex items-center justify-center" style="height: 300px; background: var(--color-amber-soft);">
            @php
                $iconos = ['hotel'=>'🏨','restaurante'=>'🍽️','transporte'=>'🚌','agencia'=>'🏢','otro'=>'📋'];
            @endphp
            <span class="text-7xl">{{ $iconos[$establecimiento->tipo] ?? '🏢' }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- ── Columna principal ─────────────────────────────────── --}}
        <div class="lg:col-span-2">

            {{-- Título --}}
            <div class="mb-6">
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <span class="tc-badge-tipo">{{ ucfirst($establecimiento->tipo) }}</span>
                    @if($establecimiento->rango_precio)
                        <span class="tc-badge-precio-{{ $establecimiento->rango_precio }}">
                            {{ ucfirst($establecimiento->rango_precio) }}
                        </span>
                    @endif
                </div>
                <h1 style="font-family: var(--font-display); font-size: 1.875rem; font-weight: 800; color: #1a2232; line-height: 1.2;">
                    {{ $establecimiento->nombre }}
                </h1>
                @if($establecimiento->destino)
                    <p class="mt-1 text-sm" style="color: var(--color-primary-600);">
                        📍 {{ $establecimiento->destino->nombre }}
                    </p>
                @endif
                @if($establecimiento->direccion)
                    <p class="text-sm text-gray-500 mt-1">🗺️ {{ $establecimiento->direccion }}</p>
                @endif
                @if($establecimiento->categorias->isNotEmpty())
                    <div class="flex flex-wrap gap-2 mt-3">
                        @foreach($establecimiento->categorias as $cat)
                            <span class="tc-chip">{{ $cat->nombre }}</span>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Descripción --}}
            @if($establecimiento->descripcion)
                <div class="tc-card p-6 mb-6">
                    <h2 class="text-sm font-bold uppercase tracking-wide mb-3" style="color: var(--color-primary-700);">
                        Descripción
                    </h2>
                    <p class="text-gray-600 leading-relaxed whitespace-pre-line">{{ $establecimiento->descripcion }}</p>
                </div>
            @endif

            {{-- ── Horarios semanales ─────────────────────────────── --}}
            @if($establecimiento->horarios)
                <div class="tc-card p-6 mb-6">
                    <h2 class="text-sm font-bold uppercase tracking-wide mb-4" style="color: var(--color-primary-700);">
                        Horarios de atención
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
                                    @php $horario = $establecimiento->horarios[$clave] ?? null; @endphp
                                    <tr>
                                        <td class="py-2.5 font-medium text-gray-700">{{ $nombre }}</td>
                                        @if($horario)
                                            <td class="py-2.5 text-gray-600">{{ $horario['abre'] ?? '—' }}</td>
                                            <td class="py-2.5 text-gray-600">{{ $horario['cierra'] ?? '—' }}</td>
                                            <td class="py-2.5"><span class="tc-badge-activo">Abierto</span></td>
                                        @else
                                            <td class="py-2.5 text-gray-400">—</td>
                                            <td class="py-2.5 text-gray-400">—</td>
                                            <td class="py-2.5"><span class="tc-badge-inactivo">Cerrado</span></td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- Mapa --}}
            @if($establecimiento->latitud && $establecimiento->longitud)
                <div class="tc-card p-6">
                    <h2 class="text-sm font-bold uppercase tracking-wide mb-4" style="color: var(--color-primary-700);">
                        Ubicación
                    </h2>
                    <div class="tc-map-placeholder h-56">
                        <div class="text-center">
                            <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <p class="text-sm">{{ $establecimiento->latitud }}, {{ $establecimiento->longitud }}</p>
                            @if($establecimiento->direccion)
                                <p class="text-xs text-gray-500 mt-1">{{ $establecimiento->direccion }}</p>
                            @endif
                            <a href="https://maps.google.com/?q={{ $establecimiento->latitud }},{{ $establecimiento->longitud }}"
                               target="_blank" rel="noopener"
                               class="mt-2 tc-btn-outline text-xs inline-flex">
                                Ver en Google Maps ↗
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- ── Sidebar ───────────────────────────────────────────── --}}
        <div class="space-y-4">

            {{-- Tipo --}}
            <div class="tc-card p-5">
                <p class="text-xs font-bold uppercase tracking-wide mb-2" style="color: var(--color-primary-700);">Tipo</p>
                <span class="tc-badge-tipo text-sm">{{ ucfirst($establecimiento->tipo) }}</span>
            </div>

            {{-- Rango de precio --}}
            @if($establecimiento->rango_precio)
                <div class="tc-card p-5">
                    <p class="text-xs font-bold uppercase tracking-wide mb-2" style="color: var(--color-primary-700);">Rango de precio</p>
                    <span class="tc-badge-precio-{{ $establecimiento->rango_precio }} text-sm font-semibold">
                        {{ ucfirst($establecimiento->rango_precio) }}
                    </span>
                </div>
            @endif

            {{-- Proveedor --}}
            @if($establecimiento->proveedor)
                <div class="tc-card p-5">
                    <p class="text-xs font-bold uppercase tracking-wide mb-1" style="color: var(--color-primary-700);">Proveedor</p>
                    <p class="text-sm font-medium text-gray-700">{{ $establecimiento->proveedor->nombre }}</p>
                </div>
            @endif

            {{-- Volver --}}
            <a href="{{ route('catalogo.establecimientos.index') }}" class="tc-btn-outline w-full justify-center text-sm">
                ← Volver al catálogo
            </a>
        </div>
    </div>
</div>
@endsection
