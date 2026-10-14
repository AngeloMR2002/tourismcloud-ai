@extends('layouts.app')

@section('title', 'Atractivos turísticos')

@section('content')
@php
    $filtroActivo = collect($filtros)->only(['destino_id', 'categoria_id', 'busqueda', 'costo_min', 'costo_max'])->contains(fn ($valor) => filled($valor));
@endphp

<div class="tc-catalog-shell max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
    <header class="tc-page-heading" aria-labelledby="atractivos-titulo">
        <p class="tc-home-eyebrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M12 21s7-5 7-12a7 7 0 1 0-14 0c0 7 7 12 7 12Z" stroke-width="1.8"/><circle cx="12" cy="9" r="2.2"/></svg><span>Explora · Qué visitar</span></p>
        <h1 id="atractivos-titulo">Atractivos</h1>
        <p>Descubre lugares de interés y compara la información disponible para preparar tu visita.</p>
    </header>

    <div class="mt-6 flex flex-col gap-6 lg:flex-row lg:items-start">
        @foreach(['movil', 'escritorio'] as $modo)
            @if($modo === 'movil')
                <details class="tc-filter-panel p-4 lg:hidden" aria-label="Filtros de atractivos">
                    <summary class="tc-filter-summary"><span class="font-bold text-gray-900">Ajustar filtros</span>@if($filtroActivo)<span class="tc-filter-chip">Activos</span>@endif</summary>
            @else
                <aside class="hidden w-60 shrink-0 lg:block" aria-label="Filtros de atractivos">
            @endif
                <form method="GET" action="{{ route('catalogo.atractivos.index') }}" class="{{ $modo === 'movil' ? 'mt-5' : 'tc-filter-panel p-5' }} space-y-5">
                    @if($modo === 'escritorio')<h2 class="border-b border-gray-100 pb-4 font-bold text-gray-900">Filtros</h2>@endif
                    @if(filled($filtros['busqueda'] ?? null))<input type="hidden" name="busqueda" value="{{ $filtros['busqueda'] }}">@endif
                    @if(filled($filtros['por_pagina'] ?? null))<input type="hidden" name="por_pagina" value="{{ $filtros['por_pagina'] }}">@endif
                    <div>
                        <label for="atractivo-destino-{{ $modo }}" class="mb-2 block text-xs font-bold text-gray-700">Destino</label>
                        <select id="atractivo-destino-{{ $modo }}" name="destino_id" class="tc-select text-sm">
                            <option value="">Todos los destinos</option>
                            @foreach($destinos as $destino)<option value="{{ $destino->id }}" @selected(($filtros['destino_id'] ?? '') == $destino->id)>{{ $destino->nombre }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label for="atractivo-categoria-{{ $modo }}" class="mb-2 block text-xs font-bold text-gray-700">Categoría de interés</label>
                        <select id="atractivo-categoria-{{ $modo }}" name="categoria_id" class="tc-select text-sm">
                            <option value="">Todas las categorías</option>
                            @foreach($categorias as $categoria)<option value="{{ $categoria->id }}" @selected(($filtros['categoria_id'] ?? '') == $categoria->id)>{{ $categoria->nombre }}</option>@endforeach
                        </select>
                    </div>
                    <fieldset>
                        <legend class="mb-2 text-xs font-bold text-gray-700">Entrada referencial (S/)</legend>
                        <div class="grid grid-cols-2 gap-3">
                            <div><label for="atractivo-costo-min-{{ $modo }}" class="mb-1 block text-xs text-gray-500">Desde</label><input id="atractivo-costo-min-{{ $modo }}" type="number" name="costo_min" min="0" step="0.01" value="{{ $filtros['costo_min'] ?? '' }}" placeholder="0" class="tc-input text-sm"></div>
                            <div><label for="atractivo-costo-max-{{ $modo }}" class="mb-1 block text-xs text-gray-500">Hasta</label><input id="atractivo-costo-max-{{ $modo }}" type="number" name="costo_max" min="0" step="0.01" value="{{ $filtros['costo_max'] ?? '' }}" placeholder="Sin límite" class="tc-input text-sm"></div>
                        </div>
                    </fieldset>
                    <button type="submit" class="tc-btn-primary w-full justify-center">Aplicar filtros</button>
                    <a href="{{ route('catalogo.atractivos.index') }}" class="block text-center text-sm font-semibold text-gray-600 hover:underline">Limpiar filtros</a>
                </form>
            @if($modo === 'movil')</details>@else</aside>@endif
        @endforeach

        <section class="min-w-0 flex-1" aria-label="Resultados de atractivos">
            <form method="GET" action="{{ route('catalogo.atractivos.index') }}" class="mb-5 flex flex-col gap-2 sm:flex-row">
                @foreach(['destino_id', 'categoria_id', 'costo_min', 'costo_max', 'por_pagina'] as $campo)
                    @if(filled($filtros[$campo] ?? null))<input type="hidden" name="{{ $campo }}" value="{{ $filtros[$campo] }}">@endif
                @endforeach
                <div class="tc-search-input-wrap">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35m1.35-5.15a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z"/></svg>
                    <input type="search" name="busqueda" value="{{ $filtros['busqueda'] ?? '' }}" placeholder="Buscar por nombre o descripción" aria-label="Buscar atractivos" class="tc-input">
                </div>
                <button type="submit" class="tc-btn-primary justify-center">Buscar</button>
            </form>
            @if($errors->any())<div class="tc-alert-error mb-5" role="alert">{{ $errors->first() }}</div>@endif

            <div class="tc-result-toolbar">
                <div>
                    <p class="font-bold text-gray-900">{{ number_format($atractivos->total()) }} {{ $atractivos->total() === 1 ? 'resultado' : 'resultados' }}</p>
                    <p class="mt-1 text-xs sm:text-sm text-gray-500">Los costos de entrada y tiempos de visita son referenciales.</p>
                </div>
                @if($filtroActivo)<a href="{{ route('catalogo.atractivos.index') }}" class="text-xs font-bold text-primary-700 hover:underline">Limpiar filtros</a>@endif
            </div>

            @if($atractivos->isEmpty())
                <div class="tc-empty-state p-8 sm:p-12 text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-primary-600"><svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M12 21s7-4.35 7-11.3A7 7 0 0 0 5 9.7C5 16.65 12 21 12 21Z"/><circle cx="12" cy="10" r="2.25"/></svg></div>
                    @if($filtroActivo)
                        <h2 class="mt-4 text-xl font-bold text-gray-900">No encontramos coincidencias</h2>
                        <p class="mt-2 text-sm leading-6 text-gray-500">Cambia el destino, la categoría o el presupuesto para ampliar tu búsqueda.</p>
                        <a href="{{ route('catalogo.atractivos.index') }}" class="tc-btn-outline mt-5">Limpiar filtros</a>
                    @else
                        <h2 class="mt-4 text-xl font-bold text-gray-900">Todavía no hay atractivos publicados</h2>
                        <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-gray-500">Aquí aparecerán los lugares disponibles cuando los operadores los publiquen.</p>
                        <a href="{{ route('inicio') }}" class="tc-btn-outline mt-5">Volver al inicio</a>
                    @endif
                </div>
            @else
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
                    @foreach($atractivos as $atractivo)
                        @php
                            $duracion = null;
                            if ($atractivo->duracion_estimada_min) {
                                $horas = intdiv($atractivo->duracion_estimada_min, 60);
                                $minutos = $atractivo->duracion_estimada_min % 60;
                                $duracion = ($horas ? $horas.' h' : '').($horas && $minutos ? ' ' : '').($minutos ? $minutos.' min' : '');
                            }
                        @endphp
                        <a href="{{ route('catalogo.atractivos.show', $atractivo) }}" class="tc-card tc-card-interactive tc-experience-card focus:outline-none focus-visible:ring-4 focus-visible:ring-primary-200">
                            <div class="tc-experience-card__media">
                                @if($atractivo->imagen_portada)
                                    <img src="{{ asset('storage/'.$atractivo->imagen_portada) }}" alt="{{ $atractivo->nombre }}" loading="lazy">
                                @else
                                    <div class="tc-cover-fallback" aria-hidden="true"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" d="M12 21s7-4.35 7-11.3A7 7 0 0 0 5 9.7C5 16.65 12 21 12 21Z"/><circle cx="12" cy="10" r="2.25"/></svg></div>
                                @endif
                                <span class="tc-media-badge">{{ $atractivo->destino?->nombre ?? 'Destino por confirmar' }}</span>
                            </div>
                            <div class="tc-experience-card__body">
                                @if($atractivo->categorias->first())<p class="text-xs font-semibold text-primary-700">{{ $atractivo->categorias->first()->nombre }}</p>@endif
                                <h2 class="mt-2 text-xl font-bold leading-tight text-gray-900">{{ $atractivo->nombre }}</h2>
                                <p class="mt-2 line-clamp-2 text-sm leading-6 text-gray-500">{{ $atractivo->descripcion ?: 'Consulta la información disponible para planificar tu visita.' }}</p>
                                <div class="tc-card-meta">
                                    @if($duracion)<span>{{ $duracion }}</span>@endif
                                    <span class="font-bold text-primary-700">{{ $atractivo->costo_entrada > 0 ? 'S/ '.number_format((float) $atractivo->costo_entrada, 2) : 'Entrada libre' }}</span>
                                </div>
                                <span class="tc-card-cta">Ver atractivo <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 18 6-6-6-6"/></svg></span>
                            </div>
                        </a>
                    @endforeach
                </div>
                <div class="mt-8">{{ $atractivos->appends(request()->query())->links() }}</div>
            @endif
        </section>
    </div>
</div>
@endsection
