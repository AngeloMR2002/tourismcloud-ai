@extends('layouts.app')

@section('title', 'Establecimientos')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Hero --}}
    <div class="mb-6">
        <p class="text-sm text-gray-400 mb-0.5">Hospedaje, gastronomía y más</p>
        <h1 style="font-family: var(--font-display); font-size: 2rem; font-weight: 800; color: #1a2232; line-height: 1.15;">
            Establecimientos
        </h1>
    </div>

    {{-- Layout sidebar + contenido --}}
    <div class="flex gap-6 items-start">

        {{-- ════════════════════════════════
             SIDEBAR DE FILTROS
        ════════════════════════════════ --}}
        <aside class="w-64 shrink-0">
            <form method="GET" action="{{ route('catalogo.establecimientos.index') }}" id="form-filtros-est">
                <div class="tc-card p-5">
                    <h2 style="font-family: var(--font-display); font-size: 1.05rem; font-weight: 700; color: #1a2232;" class="mb-5">
                        Filtros
                    </h2>

                    {{-- Destinos --}}
                    <div class="mb-4">
                        <label class="block text-xs font-bold mb-2" style="color: var(--color-primary-700);">Destinos</label>
                        <select name="destino_id" class="tc-select text-sm"
                                onchange="document.getElementById('form-filtros-est').submit()">
                            <option value="">Todos los destinos</option>
                            @foreach($destinos as $d)
                                <option value="{{ $d->id }}" @selected(($filtros['destino_id'] ?? '') == $d->id)>
                                    {{ $d->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Tipo --}}
                    <div class="mb-4">
                        <label class="block text-xs font-bold mb-2" style="color: var(--color-primary-700);">Tipo</label>
                        <select name="tipo" class="tc-select text-sm"
                                onchange="document.getElementById('form-filtros-est').submit()">
                            <option value="">Todos los Tipos</option>
                            <option value="hotel"       @selected(($filtros['tipo'] ?? '') === 'hotel')>Hotel</option>
                            <option value="restaurante" @selected(($filtros['tipo'] ?? '') === 'restaurante')>Restaurante</option>
                            <option value="transporte"  @selected(($filtros['tipo'] ?? '') === 'transporte')>Transporte</option>
                            <option value="agencia"     @selected(($filtros['tipo'] ?? '') === 'agencia')>Agencia</option>
                            <option value="otro"        @selected(($filtros['tipo'] ?? '') === 'otro')>Otro</option>
                        </select>
                    </div>

                    {{-- Categorías como chips --}}
                    <div class="mb-4">
                        <label class="block text-xs font-bold mb-2" style="color: var(--color-primary-700);">Categoría</label>
                        <div class="flex flex-wrap gap-2">
                            @foreach($categorias as $cat)
                                @php $seleccionada = ($filtros['categoria_id'] ?? '') == $cat->id; @endphp
                                <button type="submit" name="categoria_id" value="{{ $cat->id }}"
                                        class="text-xs font-medium px-3 py-1.5 rounded-full border transition-all"
                                        style="{{ $seleccionada
                                            ? 'background: var(--color-primary-500); color: white; border-color: var(--color-primary-500);'
                                            : 'background: white; color: #374151; border-color: #d1d5db;' }}">
                                    {{ $cat->nombre }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Rango de precio — botones tipo toggle 2x2 --}}
                    <div class="mb-6">
                        <label class="block text-xs font-bold mb-2" style="color: var(--color-primary-700);">Rango de precio</label>
                        <div class="grid grid-cols-2 gap-2">
                            @php
                                $rangos = [
                                    'bajo'  => 'Bajo $',
                                    'medio' => 'Medio $$',
                                    'alto'  => 'Alto $$$',
                                    'lujo'  => 'Lujoso $$$$',
                                ];
                            @endphp
                            @foreach($rangos as $valor => $etiqueta)
                                @php $activo = ($filtros['rango_precio'] ?? '') === $valor; @endphp
                                <button type="submit" name="rango_precio" value="{{ $valor }}"
                                        class="text-xs font-semibold py-2 px-2 rounded-lg border transition-all text-center"
                                        style="{{ $activo
                                            ? 'background: var(--color-primary-500); color: white; border-color: var(--color-primary-500);'
                                            : 'background: white; color: #374151; border-color: #d1d5db;' }}">
                                    {{ $etiqueta }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Limpiar filtros --}}
                    <a href="{{ route('catalogo.establecimientos.index') }}"
                       class="block w-full text-center text-sm font-semibold py-2 rounded-lg border transition-colors"
                       style="border-color: #d1d5db; color: #6b7280;"
                       onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='transparent'">
                        Limpiar filtros
                    </a>
                </div>
            </form>
        </aside>

        {{-- ════════════════════════════════
             CONTENIDO DERECHO
        ════════════════════════════════ --}}
        <div class="flex-1 min-w-0">

            {{-- Barra de búsqueda --}}
            <form method="GET" action="{{ route('catalogo.establecimientos.index') }}" class="flex gap-2 mb-6">
                @if(!empty($filtros['destino_id']))
                    <input type="hidden" name="destino_id" value="{{ $filtros['destino_id'] }}">
                @endif
                @if(!empty($filtros['tipo']))
                    <input type="hidden" name="tipo" value="{{ $filtros['tipo'] }}">
                @endif
                @if(!empty($filtros['categoria_id']))
                    <input type="hidden" name="categoria_id" value="{{ $filtros['categoria_id'] }}">
                @endif
                @if(!empty($filtros['rango_precio']))
                    <input type="hidden" name="rango_precio" value="{{ $filtros['rango_precio'] }}">
                @endif

                <input type="text" name="busqueda"
                       value="{{ $filtros['busqueda'] ?? '' }}"
                       placeholder="Buscar establecimientos..."
                       class="tc-input flex-1">
                <button type="submit" class="tc-btn-primary px-5">
                    Buscar
                </button>
            </form>

            {{-- Grid 2 columnas --}}
            @if($establecimientos->isEmpty())
                <div class="text-center py-20">
                    <svg class="w-14 h-14 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <p class="text-gray-500 font-medium">No se encontraron establecimientos.</p>
                    <a href="{{ route('catalogo.establecimientos.index') }}" class="mt-3 inline-block tc-btn-outline text-sm">
                        Ver todos
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    @foreach($establecimientos as $est)
                        <a href="{{ route('catalogo.establecimientos.show', $est) }}"
                           class="tc-card overflow-hidden group flex flex-col">

                            {{-- Imagen --}}
                            <div class="relative overflow-hidden" style="aspect-ratio: 16/10; background: var(--color-amber-soft);">
                                @if($est->imagen_portada)
                                    <img src="{{ asset('storage/' . $est->imagen_portada) }}"
                                         alt="{{ $est->nombre }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    @php $iconos = ['hotel'=>'🏨','restaurante'=>'🍽️','transporte'=>'🚌','agencia'=>'🏢','otro'=>'📋']; @endphp
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <span class="text-4xl">{{ $iconos[$est->tipo] ?? '🏢' }}</span>
                                    </div>
                                @endif

                                {{-- Tipo badge --}}
                                <span class="absolute top-2.5 left-2.5 text-xs font-bold px-2.5 py-1 rounded-full"
                                      style="background: rgba(255,255,255,0.92); color: var(--color-tertiary-500); text-transform: uppercase; letter-spacing: 0.03em;">
                                    {{ ucfirst($est->tipo) }}
                                </span>
                            </div>

                            {{-- Info tarjeta --}}
                            <div class="p-4 flex flex-col flex-1">
                                <h3 style="font-family: var(--font-display); font-weight: 700; font-size: 1rem; color: #1a2232;" class="mb-1.5 leading-snug">
                                    {{ $est->nombre }}
                                </h3>

                                {{-- Badges tipo + precio --}}
                                <div class="flex items-center gap-2 mb-1.5">
                                    @if($est->rango_precio)
                                        <span class="tc-badge-precio-{{ $est->rango_precio }}">
                                            {{ ucfirst($est->rango_precio) }}
                                        </span>
                                    @endif
                                    @if($est->categorias->isNotEmpty())
                                        <span class="tc-chip">{{ $est->categorias->first()->nombre }}</span>
                                    @endif
                                </div>

                                {{-- Separador --}}
                                <div class="h-px mb-2" style="background: #f0f0f0;"></div>

                                {{-- Categoría (etiqueta) --}}
                                @if($est->categorias->isNotEmpty())
                                    <p class="text-xs font-medium mb-1" style="color: var(--color-primary-600);">
                                        {{ $est->categorias->pluck('nombre')->take(2)->join(' · ') }}
                                    </p>
                                @endif

                                {{-- Lugar · Valoración --}}
                                <div class="flex items-center gap-1.5 text-xs text-gray-400 mt-auto flex-wrap">
                                    @if($est->destino)
                                        <span>{{ $est->destino->nombre }}</span>
                                    @endif
                                    @if($est->direccion && $est->destino)
                                        <span>·</span>
                                        <span class="truncate max-w-32">{{ $est->direccion }}</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                {{-- Paginación --}}
                <div class="mt-8">
                    {{ $establecimientos->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
