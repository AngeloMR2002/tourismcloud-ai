@extends('layouts.app')

@section('title', 'Establecimientos')

@section('content')
@php
    $rangoPrecio = ['Bajo' => 'Bajo $', 'Medio' => 'Medio $$', 'Alto' => 'Alto $$$', 'Lujoso' => 'Lujoso $$$$'];
    $tipos = ['hotel' => 'Hotel', 'restaurante' => 'Restaurante', 'transporte' => 'Transporte', 'agencia' => 'Agencia', 'otro' => 'Otro'];
    $filtroActivo = collect($filtros)->only(['destino_id', 'tipo', 'categoria_id', 'rango_precio', 'busqueda'])->contains(fn ($valor) => filled($valor));
@endphp

<div class="tc-catalog-shell max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
    <header class="tc-page-heading" aria-labelledby="establecimientos-titulo">
        <p class="tc-home-eyebrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M4 21V6a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v15M9 21v-5h6v5" stroke-width="1.8"/><circle cx="12" cy="10" r="1.5"/></svg><span>Explora · Dónde alojarte y comer</span></p>
        <h1 id="establecimientos-titulo">Establecimientos</h1>
        <p>Encuentra hospedajes, restaurantes y otros servicios para acompañar tu viaje.</p>
    </header>

    <div class="mt-6 flex flex-col gap-6 lg:flex-row lg:items-start">
        @foreach(['movil', 'escritorio'] as $modo)
            @if($modo === 'movil')
                <details class="tc-filter-panel p-4 lg:hidden" aria-label="Filtros de establecimientos">
                    <summary class="tc-filter-summary"><span class="font-bold text-gray-900">Ajustar filtros</span>@if($filtroActivo)<span class="tc-filter-chip">Activos</span>@endif</summary>
            @else
                <aside class="hidden w-60 shrink-0 lg:block" aria-label="Filtros de establecimientos">
            @endif
                <form method="GET" action="{{ route('catalogo.establecimientos.index') }}" class="{{ $modo === 'movil' ? 'mt-5' : 'tc-filter-panel p-5' }} space-y-5">
                    @if($modo === 'escritorio')<h2 class="border-b border-gray-100 pb-4 font-bold text-gray-900">Filtros</h2>@endif
                    @if(filled($filtros['busqueda'] ?? null))<input type="hidden" name="busqueda" value="{{ $filtros['busqueda'] }}">@endif
                    @if(filled($filtros['por_pagina'] ?? null))<input type="hidden" name="por_pagina" value="{{ $filtros['por_pagina'] }}">@endif
                    <div>
                        <label for="establecimiento-destino-{{ $modo }}" class="mb-2 block text-xs font-bold text-gray-700">Destino</label>
                        <select id="establecimiento-destino-{{ $modo }}" name="destino_id" class="tc-select text-sm">
                            <option value="">Todos los destinos</option>
                            @foreach($destinos as $destino)<option value="{{ $destino->id }}" @selected(($filtros['destino_id'] ?? '') == $destino->id)>{{ $destino->nombre }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label for="establecimiento-tipo-{{ $modo }}" class="mb-2 block text-xs font-bold text-gray-700">Tipo de servicio</label>
                        <select id="establecimiento-tipo-{{ $modo }}" name="tipo" class="tc-select text-sm">
                            <option value="">Todos los tipos</option>
                            @foreach($tipos as $valor => $etiqueta)<option value="{{ $valor }}" @selected(($filtros['tipo'] ?? '') === $valor)>{{ $etiqueta }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label for="establecimiento-categoria-{{ $modo }}" class="mb-2 block text-xs font-bold text-gray-700">Categoría de interés</label>
                        <select id="establecimiento-categoria-{{ $modo }}" name="categoria_id" class="tc-select text-sm">
                            <option value="">Todas las categorías</option>
                            @foreach($categorias as $categoria)<option value="{{ $categoria->id }}" @selected(($filtros['categoria_id'] ?? '') == $categoria->id)>{{ $categoria->nombre }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label for="establecimiento-precio-{{ $modo }}" class="mb-2 block text-xs font-bold text-gray-700">Nivel de precio</label>
                        <select id="establecimiento-precio-{{ $modo }}" name="rango_precio" class="tc-select text-sm">
                            <option value="">Todos los niveles</option>
                            @foreach($rangoPrecio as $valor => $etiqueta)<option value="{{ $valor }}" @selected(($filtros['rango_precio'] ?? '') === $valor)>{{ $etiqueta }}</option>@endforeach
                        </select>
                    </div>
                    <button type="submit" class="tc-btn-primary w-full justify-center">Aplicar filtros</button>
                    <a href="{{ route('catalogo.establecimientos.index') }}" class="block text-center text-sm font-semibold text-gray-600 hover:underline">Limpiar filtros</a>
                </form>
            @if($modo === 'movil')</details>@else</aside>@endif
        @endforeach

        <section class="min-w-0 flex-1" aria-label="Resultados de establecimientos">
            <form method="GET" action="{{ route('catalogo.establecimientos.index') }}" class="mb-5 flex flex-col gap-2 sm:flex-row">
                @foreach(['destino_id', 'tipo', 'categoria_id', 'rango_precio', 'por_pagina'] as $campo)
                    @if(filled($filtros[$campo] ?? null))<input type="hidden" name="{{ $campo }}" value="{{ $filtros[$campo] }}">@endif
                @endforeach
                <div class="tc-search-input-wrap">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35m1.35-5.15a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z"/></svg>
                    <input type="search" name="busqueda" value="{{ $filtros['busqueda'] ?? '' }}" placeholder="Buscar por nombre o descripción" aria-label="Buscar establecimientos" class="tc-input">
                </div>
                <button type="submit" class="tc-btn-primary justify-center">Buscar</button>
            </form>
            @if($errors->any())<div class="tc-alert-error mb-5" role="alert">{{ $errors->first() }}</div>@endif

            <div class="tc-result-toolbar">
                <div>
                    <p class="font-bold text-gray-900">{{ number_format($establecimientos->total()) }} {{ $establecimientos->total() === 1 ? 'resultado' : 'resultados' }}</p>
                    <p class="mt-1 text-xs sm:text-sm text-gray-500">Consulta los servicios y datos de contacto publicados por cada proveedor.</p>
                </div>
                @if($filtroActivo)<a href="{{ route('catalogo.establecimientos.index') }}" class="text-xs font-bold text-primary-700 hover:underline">Limpiar filtros</a>@endif
            </div>

            @if($establecimientos->isEmpty())
                <div class="tc-empty-state p-8 sm:p-12 text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-primary-600"><svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M4 21V6a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v15M9 21v-5h6v5M8 8h.01M12 8h.01M16 8h.01M8 12h.01M12 12h.01M16 12h.01"/></svg></div>
                    @if($filtroActivo)
                        <h2 class="mt-4 text-xl font-bold text-gray-900">No encontramos coincidencias</h2>
                        <p class="mt-2 text-sm leading-6 text-gray-500">Prueba otro destino, tipo de servicio o nivel de precio.</p>
                        <a href="{{ route('catalogo.establecimientos.index') }}" class="tc-btn-outline mt-5">Limpiar filtros</a>
                    @else
                        <h2 class="mt-4 text-xl font-bold text-gray-900">Todavía no hay establecimientos publicados</h2>
                        <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-gray-500">Aquí aparecerán los servicios disponibles cuando los proveedores los publiquen.</p>
                        <a href="{{ route('catalogo.atractivos.index') }}" class="tc-btn-outline mt-5">Explorar atractivos</a>
                    @endif
                </div>
            @else
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
                    @foreach($establecimientos as $est)
                        <a href="{{ route('catalogo.establecimientos.show', $est) }}" class="tc-card tc-card-interactive tc-experience-card focus:outline-none focus-visible:ring-4 focus-visible:ring-primary-200">
                            <div class="tc-experience-card__media">
                                @if($est->imagen_portada)
                                    <img src="{{ asset('storage/'.$est->imagen_portada) }}" alt="{{ $est->nombre }}" loading="lazy">
                                @else
                                    <div class="tc-cover-fallback" aria-hidden="true"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" d="M4 21V6a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v15M9 21v-5h6v5M8 8h.01M12 8h.01M16 8h.01M8 12h.01M12 12h.01M16 12h.01"/></svg></div>
                                @endif
                                <span class="tc-media-badge">{{ $est->destino?->nombre ?? 'Destino por confirmar' }}</span>
                            </div>
                            <div class="tc-experience-card__body">
                                <p class="text-xs font-semibold text-primary-700">{{ $tipos[$est->tipo] ?? ucfirst($est->tipo) }}</p>
                                <h2 class="mt-2 text-xl font-bold leading-tight text-gray-900">{{ $est->nombre }}</h2>
                                @if($est->direccion)<p class="mt-2 text-xs text-gray-500">{{ $est->direccion }}</p>@endif
                                @if($est->total_resenas > 0)
                                    <p class="mt-2 flex items-center gap-1.5 text-sm font-bold text-gray-800" aria-label="Valoración de {{ $est->nombre }}">
                                        <span class="text-amber-400" aria-hidden="true">★</span>
                                        {{ number_format($est->promedio_puntuacion, 1) }}
                                        <span class="text-xs font-normal text-gray-500">({{ $est->total_resenas }} {{ $est->total_resenas === 1 ? 'reseña' : 'reseñas' }})</span>
                                    </p>
                                @endif
                                <p class="mt-2 line-clamp-2 text-sm leading-6 text-gray-500">{{ $est->descripcion ?: 'Consulta los servicios y datos disponibles de este establecimiento.' }}</p>
                                <div class="tc-card-meta">
                                    @if($est->rango_precio)<span class="font-semibold">{{ $rangoPrecio[$est->rango_precio] ?? ucfirst($est->rango_precio) }}</span>@endif
                                    @foreach($est->categorias->take(2) as $categoria)<span>{{ $categoria->nombre }}</span>@endforeach
                                </div>
                                <span class="tc-card-cta">Ver establecimiento <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 18 6-6-6-6"/></svg></span>
                            </div>
                        </a>
                    @endforeach
                </div>
                <div class="mt-8">{{ $establecimientos->appends(request()->query())->links() }}</div>
            @endif
        </section>
    </div>
</div>
@endsection
