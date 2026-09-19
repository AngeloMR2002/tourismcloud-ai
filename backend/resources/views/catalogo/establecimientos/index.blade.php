@extends('layouts.app')

@section('title', 'Catálogo de Establecimientos')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- ── Hero header ─────────────────────────────────────────── --}}
    <div class="mb-8">
        <h1 style="font-family: var(--font-display); font-size: 2rem; font-weight: 800; color: var(--color-primary-700); line-height: 1.2;">
            Establecimientos
        </h1>
        <p class="mt-2 text-gray-500 text-base">
            Hoteles, restaurantes, transporte y más para tu viaje.
        </p>
    </div>

    {{-- ── Panel de filtros ─────────────────────────────────────── --}}
    <div class="tc-card p-5 mb-8">
        <form method="GET" action="{{ route('catalogo.establecimientos.index') }}" id="filtros-form">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                {{-- Búsqueda --}}
                <div class="lg:col-span-2">
                    <label for="busqueda" class="block text-xs font-semibold mb-1" style="color: var(--color-primary-700);">
                        Buscar
                    </label>
                    <div class="relative">
                        <input type="text" id="busqueda" name="busqueda"
                               value="{{ $filtros['busqueda'] ?? '' }}"
                               placeholder="Nombre, descripción o dirección..."
                               class="tc-input pl-9">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>

                {{-- Destino --}}
                <div>
                    <label for="destino_id" class="block text-xs font-semibold mb-1" style="color: var(--color-primary-700);">
                        Destino
                    </label>
                    <select id="destino_id" name="destino_id" class="tc-select">
                        <option value="">Todos los destinos</option>
                        @foreach($destinos as $destino)
                            <option value="{{ $destino->id }}" @selected(($filtros['destino_id'] ?? '') == $destino->id)>
                                {{ $destino->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Tipo --}}
                <div>
                    <label for="tipo" class="block text-xs font-semibold mb-1" style="color: var(--color-primary-700);">
                        Tipo
                    </label>
                    <select id="tipo" name="tipo" class="tc-select">
                        <option value="">Todos los tipos</option>
                        <option value="hotel"        @selected(($filtros['tipo'] ?? '') === 'hotel')>🏨 Hotel</option>
                        <option value="restaurante"  @selected(($filtros['tipo'] ?? '') === 'restaurante')>🍽️ Restaurante</option>
                        <option value="transporte"   @selected(($filtros['tipo'] ?? '') === 'transporte')>🚌 Transporte</option>
                        <option value="agencia"      @selected(($filtros['tipo'] ?? '') === 'agencia')>🏢 Agencia</option>
                        <option value="otro"         @selected(($filtros['tipo'] ?? '') === 'otro')>📋 Otro</option>
                    </select>
                </div>

                {{-- Rango de precio --}}
                <div>
                    <label for="rango_precio" class="block text-xs font-semibold mb-1" style="color: var(--color-primary-700);">
                        Rango de precio
                    </label>
                    <select id="rango_precio" name="rango_precio" class="tc-select">
                        <option value="">Cualquier rango</option>
                        <option value="bajo"  @selected(($filtros['rango_precio'] ?? '') === 'bajo')>💚 Bajo</option>
                        <option value="medio" @selected(($filtros['rango_precio'] ?? '') === 'medio')>💛 Medio</option>
                        <option value="alto"  @selected(($filtros['rango_precio'] ?? '') === 'alto')>🟠 Alto</option>
                        <option value="lujo"  @selected(($filtros['rango_precio'] ?? '') === 'lujo')>💜 Lujo</option>
                    </select>
                </div>

                {{-- Categoría --}}
                <div>
                    <label for="categoria_id" class="block text-xs font-semibold mb-1" style="color: var(--color-primary-700);">
                        Categoría
                    </label>
                    <select id="categoria_id" name="categoria_id" class="tc-select">
                        <option value="">Todas las categorías</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}" @selected(($filtros['categoria_id'] ?? '') == $cat->id)>
                                {{ $cat->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Botones --}}
                <div class="flex items-end gap-2 sm:col-span-2">
                    <button type="submit" class="tc-btn-primary flex-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                        </svg>
                        Filtrar
                    </button>
                    <a href="{{ route('catalogo.establecimientos.index') }}" class="tc-btn-outline">
                        Limpiar
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- ── Resultados ───────────────────────────────────────────── --}}
    @if($establecimientos->isEmpty())
        <div class="text-center py-20">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <p class="text-gray-500 font-medium">No se encontraron establecimientos con los filtros aplicados.</p>
            <a href="{{ route('catalogo.establecimientos.index') }}" class="mt-4 inline-block tc-btn-outline text-sm">
                Ver todos los establecimientos
            </a>
        </div>
    @else
        <p class="text-sm text-gray-400 mb-4">
            {{ $establecimientos->total() }} resultado(s) encontrado(s)
        </p>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($establecimientos as $est)
                <a href="{{ route('catalogo.establecimientos.show', $est) }}"
                   class="tc-card overflow-hidden group flex flex-col">

                    {{-- Imagen de portada --}}
                    <div class="aspect-video overflow-hidden relative" style="background: var(--color-amber-soft);">
                        @if($est->imagen_portada)
                            <img src="{{ asset('storage/' . $est->imagen_portada) }}"
                                 alt="{{ $est->nombre }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="absolute inset-0 flex items-center justify-center">
                                @php
                                    $iconos = [
                                        'hotel'       => '🏨',
                                        'restaurante' => '🍽️',
                                        'transporte'  => '🚌',
                                        'agencia'     => '🏢',
                                        'otro'        => '📋',
                                    ];
                                @endphp
                                <span class="text-5xl">{{ $iconos[$est->tipo] ?? '🏢' }}</span>
                            </div>
                        @endif

                        {{-- Badge de tipo --}}
                        <span class="absolute top-3 left-3 tc-badge-tipo">
                            {{ ucfirst($est->tipo) }}
                        </span>

                        {{-- Badge de rango de precio --}}
                        @if($est->rango_precio)
                            <span class="absolute top-3 right-3 tc-badge-precio-{{ $est->rango_precio }}">
                                {{ ucfirst($est->rango_precio) }}
                            </span>
                        @endif
                    </div>

                    {{-- Contenido de la tarjeta --}}
                    <div class="p-5 flex flex-col flex-1">
                        <h3 style="font-family: var(--font-display); font-weight: 700; font-size: 1.05rem; color: #1a2232;" class="mb-2 leading-snug">
                            {{ $est->nombre }}
                        </h3>

                        @if($est->destino)
                            <p class="text-xs mb-2" style="color: var(--color-primary-600);">
                                📍 {{ $est->destino->nombre }}
                            </p>
                        @endif

                        @if($est->descripcion)
                            <p class="text-sm text-gray-500 line-clamp-2 mb-3 flex-1">
                                {{ $est->descripcion }}
                            </p>
                        @endif

                        {{-- Chips de categorías --}}
                        @if($est->categorias->isNotEmpty())
                            <div class="flex flex-wrap gap-1.5 mb-3">
                                @foreach($est->categorias->take(3) as $cat)
                                    <span class="tc-chip">{{ $cat->nombre }}</span>
                                @endforeach
                            </div>
                        @endif

                        {{-- CTA --}}
                        <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
                            <span class="text-xs font-semibold" style="color: var(--color-primary-600);">
                                Ver detalle →
                            </span>
                            @if($est->imagenes_count > 0)
                                <span class="flex items-center gap-1 text-xs text-gray-400">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $est->imagenes_count }}
                                </span>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- Paginación --}}
        <div class="mt-10 flex justify-center">
            {{ $establecimientos->links() }}
        </div>
    @endif
</div>
@endsection
