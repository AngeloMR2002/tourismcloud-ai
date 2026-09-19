@extends('layouts.app')

@section('title', 'Catálogo de Atractivos')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- ── Hero header ─────────────────────────────────────────── --}}
    <div class="mb-8">
        <h1 style="font-family: var(--font-display); font-size: 2rem; font-weight: 800; color: var(--color-primary-700); line-height: 1.2;">
            Atractivos Turísticos
        </h1>
        <p class="mt-2 text-gray-500 text-base">
            Descubre los lugares más fascinantes del mundo.
        </p>
    </div>

    {{-- ── Panel de filtros ─────────────────────────────────────── --}}
    <div class="tc-card p-5 mb-8">
        <form method="GET" action="{{ route('catalogo.atractivos.index') }}" id="filtros-form">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                {{-- Búsqueda --}}
                <div class="lg:col-span-2">
                    <label for="busqueda" class="block text-xs font-semibold mb-1" style="color: var(--color-primary-700);">
                        Buscar
                    </label>
                    <div class="relative">
                        <input type="text" id="busqueda" name="busqueda"
                               value="{{ $filtros['busqueda'] ?? '' }}"
                               placeholder="Nombre o descripción..."
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

                {{-- Costo mínimo --}}
                <div>
                    <label for="costo_min" class="block text-xs font-semibold mb-1" style="color: var(--color-primary-700);">
                        Costo mínimo (S/)
                    </label>
                    <input type="number" id="costo_min" name="costo_min"
                           value="{{ $filtros['costo_min'] ?? '' }}"
                           placeholder="0" min="0" step="0.01"
                           class="tc-input">
                </div>

                {{-- Costo máximo --}}
                <div>
                    <label for="costo_max" class="block text-xs font-semibold mb-1" style="color: var(--color-primary-700);">
                        Costo máximo (S/)
                    </label>
                    <input type="number" id="costo_max" name="costo_max"
                           value="{{ $filtros['costo_max'] ?? '' }}"
                           placeholder="Sin límite" min="0" step="0.01"
                           class="tc-input">
                </div>

                {{-- Botones --}}
                <div class="flex items-end gap-2 sm:col-span-2">
                    <button type="submit" class="tc-btn-primary flex-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                        </svg>
                        Filtrar
                    </button>
                    <a href="{{ route('catalogo.atractivos.index') }}" class="tc-btn-outline">
                        Limpiar
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- ── Resultados ───────────────────────────────────────────── --}}
    @if($atractivos->isEmpty())
        <div class="text-center py-20">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
            </svg>
            <p class="text-gray-500 font-medium">No se encontraron atractivos con los filtros aplicados.</p>
            <a href="{{ route('catalogo.atractivos.index') }}" class="mt-4 inline-block tc-btn-outline text-sm">
                Ver todos los atractivos
            </a>
        </div>
    @else
        <p class="text-sm text-gray-400 mb-4">
            {{ $atractivos->total() }} resultado(s) encontrado(s)
        </p>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($atractivos as $atractivo)
                <a href="{{ route('catalogo.atractivos.show', $atractivo) }}"
                   class="tc-card overflow-hidden group flex flex-col">

                    {{-- Imagen de portada --}}
                    <div class="aspect-video overflow-hidden relative" style="background: var(--color-teal-light);">
                        @if($atractivo->imagen_portada)
                            <img src="{{ asset('storage/' . $atractivo->imagen_portada) }}"
                                 alt="{{ $atractivo->nombre }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="absolute inset-0 flex items-center justify-center">
                                <svg class="w-12 h-12" style="color: var(--color-primary-300);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                        @endif

                        {{-- Badge de destino --}}
                        @if($atractivo->destino)
                            <span class="absolute top-3 left-3 text-xs font-semibold px-2.5 py-1 rounded-full"
                                  style="background: rgba(0,98,106,0.85); color: white;">
                                {{ $atractivo->destino->nombre }}
                            </span>
                        @endif

                        {{-- Costo de entrada --}}
                        <span class="absolute top-3 right-3 text-xs font-bold px-2.5 py-1 rounded-full"
                              style="background: rgba(255,255,255,0.9); color: var(--color-primary-700);">
                            @if($atractivo->costo_entrada > 0)
                                S/ {{ number_format($atractivo->costo_entrada, 2) }}
                            @else
                                Gratuito
                            @endif
                        </span>
                    </div>

                    {{-- Contenido de la tarjeta --}}
                    <div class="p-5 flex flex-col flex-1">
                        <h3 style="font-family: var(--font-display); font-weight: 700; font-size: 1.05rem; color: #1a2232;" class="mb-2 leading-snug">
                            {{ $atractivo->nombre }}
                        </h3>

                        @if($atractivo->descripcion)
                            <p class="text-sm text-gray-500 line-clamp-2 mb-3 flex-1">
                                {{ $atractivo->descripcion }}
                            </p>
                        @endif

                        {{-- Chips de categorías --}}
                        @if($atractivo->categorias->isNotEmpty())
                            <div class="flex flex-wrap gap-1.5 mb-3">
                                @foreach($atractivo->categorias->take(3) as $cat)
                                    <span class="tc-chip">{{ $cat->nombre }}</span>
                                @endforeach
                            </div>
                        @endif

                        {{-- Duración --}}
                        @if($atractivo->duracion_estimada_min)
                            <div class="flex items-center gap-1.5 text-xs text-gray-400 mt-auto">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                @php
                                    $h = intdiv($atractivo->duracion_estimada_min, 60);
                                    $m = $atractivo->duracion_estimada_min % 60;
                                @endphp
                                Duración: {{ $h > 0 ? "{$h}h " : '' }}{{ $m > 0 ? "{$m}min" : '' }}
                            </div>
                        @endif

                        {{-- CTA --}}
                        <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
                            <span class="text-xs font-semibold" style="color: var(--color-primary-600);">
                                Ver detalle →
                            </span>
                            @if($atractivo->imagenes_count > 0)
                                <span class="flex items-center gap-1 text-xs text-gray-400">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $atractivo->imagenes_count }} foto(s)
                                </span>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- Paginación --}}
        <div class="mt-10 flex justify-center">
            {{ $atractivos->links() }}
        </div>
    @endif
</div>
@endsection
