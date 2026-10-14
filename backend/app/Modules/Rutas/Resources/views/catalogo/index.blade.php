@extends('layouts.app', ['title' => 'Rutas turísticas'])

@section('content')
    @php
        $destinoSeleccionado = ! empty($filtros['destino_id'])
            ? $destinos->firstWhere('id', (int) $filtros['destino_id'])
            : null;
        $chipsFiltros = [];

        if ($destinoSeleccionado) {
            $chipsFiltros[] = 'Destino: '.$destinoSeleccionado->nombre;
        }
        if (! empty($filtros['duracion_max'])) {
            $chipsFiltros[] = 'Hasta '.$filtros['duracion_max'].' min';
        }
        if (isset($filtros['distancia_max']) && $filtros['distancia_max'] !== '') {
            $chipsFiltros[] = 'Hasta '.$filtros['distancia_max'].' km';
        }
        if (! empty($filtros['buscar'])) {
            $chipsFiltros[] = '“'.$filtros['buscar'].'”';
        }
    @endphp

    <div class="tc-catalog-shell max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        <header class="tc-page-heading" aria-labelledby="rutas-titulo">
            <p class="tc-home-eyebrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><circle cx="5" cy="5" r="2" stroke-width="1.8"/><circle cx="19" cy="19" r="2" stroke-width="1.8"/><path d="M7 5h6a4 4 0 0 1 0 8h-2a3 3 0 0 0 0 6h6" stroke-width="1.8"/></svg><span>Explora · Cómo recorrer</span></p>
            <h1 id="rutas-titulo">Rutas</h1>
            <p>Compara recorridos y consulta sus paradas ordenadas antes de planificar tu visita.</p>
        </header>

        <details class="tc-filter-panel mt-5 p-4 lg:hidden" aria-label="Filtros de rutas">
            <summary class="tc-filter-summary">
                <span class="inline-flex items-center gap-2 font-bold text-gray-800"><svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 4h18M6 12h12m-9 8h6"/></svg>Ajustar filtros</span>
                @if(! empty($chipsFiltros))<span class="tc-filter-chip">{{ count($chipsFiltros) }} activos</span>@endif
            </summary>
            <form method="GET" action="{{ route('catalogo.rutas.index') }}" class="mt-5 grid gap-4">
                @if(isset($filtros['buscar']) && $filtros['buscar'] !== '')<input type="hidden" name="buscar" value="{{ $filtros['buscar'] }}">@endif
                @if(isset($filtros['por_pagina']) && $filtros['por_pagina'] !== '')<input type="hidden" name="por_pagina" value="{{ $filtros['por_pagina'] }}">@endif
                <div>
                    <label for="ruta-destino-movil" class="block text-xs font-bold mb-2 text-primary-700">Destino</label>
                    <select id="ruta-destino-movil" name="destino_id" class="tc-select text-sm">
                        <option value="">Todos los destinos</option>
                        @foreach($destinos as $destino)<option value="{{ $destino->id }}" @selected((string) ($filtros['destino_id'] ?? '') === (string) $destino->id)>{{ $destino->nombre }}</option>@endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label for="ruta-duracion-max-movil" class="block text-xs font-bold mb-2 text-primary-700">Duración máx. (min)</label><input id="ruta-duracion-max-movil" type="number" name="duracion_max" min="1" max="10080" value="{{ $filtros['duracion_max'] ?? '' }}" class="tc-input text-sm" placeholder="240"></div>
                    <div><label for="ruta-distancia-max-movil" class="block text-xs font-bold mb-2 text-primary-700">Distancia máx. (km)</label><input id="ruta-distancia-max-movil" type="number" name="distancia_max" min="0" max="99999999.99" step="0.01" value="{{ $filtros['distancia_max'] ?? '' }}" class="tc-input text-sm" placeholder="15"></div>
                </div>
                <button type="submit" class="tc-btn-primary w-full justify-center">Aplicar filtros</button>
            </form>
        </details>

        <div class="mt-6 flex flex-col gap-6 lg:flex-row lg:items-start">
            <aside class="hidden w-60 shrink-0 lg:block" aria-label="Filtros de rutas">
                <form method="GET" action="{{ route('catalogo.rutas.index') }}" class="tc-filter-panel tc-filter-panel--desktop p-5">
                    <div class="flex items-center justify-between gap-3 pb-4 border-b border-gray-100"><h2 class="font-bold text-gray-900">Filtros</h2><svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 4h18M6 12h12m-9 8h6"/></svg></div>
                    @if(isset($filtros['buscar']) && $filtros['buscar'] !== '')<input type="hidden" name="buscar" value="{{ $filtros['buscar'] }}">@endif
                    @if(isset($filtros['por_pagina']) && $filtros['por_pagina'] !== '')<input type="hidden" name="por_pagina" value="{{ $filtros['por_pagina'] }}">@endif
                    <div class="mt-5"><label for="ruta-destino" class="block text-xs font-bold mb-2 text-primary-700">Destino</label><select id="ruta-destino" name="destino_id" class="tc-select text-sm"><option value="">Todos los destinos</option>@foreach($destinos as $destino)<option value="{{ $destino->id }}" @selected((string) ($filtros['destino_id'] ?? '') === (string) $destino->id)>{{ $destino->nombre }}</option>@endforeach</select></div>
                    <div class="mt-5"><label for="ruta-duracion-max" class="block text-xs font-bold mb-2 text-primary-700">Duración máxima (min)</label><input id="ruta-duracion-max" type="number" name="duracion_max" min="1" max="10080" value="{{ $filtros['duracion_max'] ?? '' }}" class="tc-input text-sm" placeholder="Ej. 240"></div>
                    <div class="mt-5"><label for="ruta-distancia-max" class="block text-xs font-bold mb-2 text-primary-700">Distancia máxima (km)</label><input id="ruta-distancia-max" type="number" name="distancia_max" min="0" max="99999999.99" step="0.01" value="{{ $filtros['distancia_max'] ?? '' }}" class="tc-input text-sm" placeholder="Ej. 15"></div>
                    <button type="submit" class="tc-btn-primary mt-6 w-full justify-center">Aplicar filtros</button>
                    <a href="{{ route('catalogo.rutas.index') }}" class="mt-3 block w-full rounded-lg border border-gray-200 py-2 text-center text-sm font-semibold text-gray-600 transition hover:bg-gray-50">Limpiar filtros</a>
                </form>
            </aside>

            <section class="min-w-0 flex-1" aria-label="Resultados de rutas">
                <form method="GET" action="{{ route('catalogo.rutas.index') }}" class="mb-5 flex flex-col gap-2 sm:flex-row">
                    @foreach(['destino_id', 'duracion_max', 'distancia_max', 'por_pagina'] as $campo)
                        @if(isset($filtros[$campo]) && $filtros[$campo] !== '')<input type="hidden" name="{{ $campo }}" value="{{ $filtros[$campo] }}">@endif
                    @endforeach
                    <div class="tc-search-input-wrap"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35m1.35-5.15a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z"/></svg><input type="search" name="buscar" maxlength="100" value="{{ $filtros['buscar'] ?? '' }}" class="tc-input" aria-label="Buscar rutas" placeholder="Buscar por nombre o descripción"></div>
                    <button type="submit" class="tc-btn-primary justify-center">Buscar</button>
                </form>

                @if($errors->any())<div class="tc-alert-error mb-5" role="alert">{{ $errors->first() }}</div>@endif

                <div class="tc-result-toolbar">
                    <div><p class="font-bold text-gray-900">{{ $rutas->total() }} {{ $rutas->total() === 1 ? 'resultado' : 'resultados' }}</p><p class="mt-1 text-xs sm:text-sm text-gray-500">Distancias y tiempos referenciales registrados por el operador.</p></div>
                    @if(! empty($chipsFiltros))<div class="flex flex-wrap items-center gap-2">@foreach($chipsFiltros as $chip)<span class="tc-filter-chip">{{ $chip }}</span>@endforeach<a href="{{ route('catalogo.rutas.index') }}" class="text-xs font-bold text-primary-700 hover:underline">Limpiar</a></div>@endif
                </div>

                @if($rutas->isEmpty())
                    <div class="tc-empty-state p-8 sm:p-12 text-center">
                        <div class="w-14 h-14 mx-auto rounded-2xl flex items-center justify-center bg-white text-primary-600"><svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M9 20 3 17V4l6 3 6-3 6 3v13l-6-3-6 3ZM9 7v13m6-16v13"/></svg></div>
                        @if(! empty($chipsFiltros))
                            <h2 class="mt-4 text-xl font-bold text-gray-900">No encontramos coincidencias</h2>
                            <p class="mt-2 text-sm text-gray-500">Prueba otro destino o amplía la duración y la distancia.</p>
                            <a href="{{ route('catalogo.rutas.index') }}" class="tc-btn-outline mt-5">Limpiar filtros</a>
                        @else
                            <h2 class="mt-4 text-xl font-bold text-gray-900">Todavía no hay rutas publicadas</h2>
                            <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-gray-500">Aquí aparecerán los recorridos disponibles con sus atractivos y paradas.</p>
                            <a href="{{ route('catalogo.atractivos.index') }}" class="tc-btn-outline mt-5">Explorar atractivos</a>
                        @endif
                    </div>
                @else
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
                        @foreach($rutas as $ruta)
                            @php
                                $portadaUrl = $ruta->portada_url;
                                $minutos = $ruta->duracion_estimada_min;
                                $horas = $minutos ? intdiv($minutos, 60) : 0;
                                $restoMinutos = $minutos ? $minutos % 60 : 0;
                                $duracion = $minutos ? (($horas > 0 ? $horas.' h' : '').($horas > 0 && $restoMinutos > 0 ? ' ' : '').($restoMinutos > 0 ? $restoMinutos.' min' : '')) : 'Por confirmar';
                            @endphp
                            <article class="tc-card tc-card-interactive tc-experience-card">
                                <a href="{{ route('catalogo.rutas.show', $ruta) }}" class="tc-experience-card__media" aria-label="Ver {{ $ruta->nombre }}">
                                    @if($portadaUrl)<img src="{{ $portadaUrl }}" alt="{{ $ruta->nombre }}" loading="lazy">@else<div class="tc-cover-fallback" aria-hidden="true"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="5" cy="5" r="2"/><circle cx="19" cy="19" r="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.35" d="M7 5h6a4 4 0 0 1 0 8h-2a3 3 0 0 0 0 6h6"/></svg></div>@endif
                                    <span class="tc-media-badge">{{ $ruta->destino->nombre }}</span>
                                </a>
                                @include('rutas::partials.credito', ['ruta' => $ruta])
                                <div class="tc-experience-card__body">
                                    <p class="text-[0.68rem] font-bold uppercase tracking-[0.13em] text-primary-600">Recorrido con paradas</p>
                                    <h2 class="mt-2 text-xl font-bold leading-tight text-gray-900"><a href="{{ route('catalogo.rutas.show', $ruta) }}" class="hover:underline">{{ $ruta->nombre }}</a></h2>
                                    @if($ruta->descripcion)<p class="mt-2 text-sm leading-6 text-gray-500 line-clamp-3">{{ $ruta->descripcion }}</p>@else<p class="mt-2 text-sm leading-6 text-gray-400">Un recorrido ordenado con atractivos disponibles.</p>@endif
                                    <div class="tc-card-meta">
                                        <span><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6v6l4 2m5-2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>{{ $duracion }}</span>
                                        <span><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12h18M7 8l-4 4 4 4m10-8 4 4-4 4"/></svg>{{ $ruta->distancia_km !== null ? $ruta->distancia_km.' km' : 'Distancia por confirmar' }}</span>
                                        <span><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><circle cx="5" cy="5" r="1.5"/><circle cx="19" cy="19" r="1.5"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6.5 5h5a3.5 3.5 0 0 1 0 7h-1a3.5 3.5 0 0 0 0 7h7"/></svg>{{ $ruta->paradas_count }} {{ $ruta->paradas_count === 1 ? 'parada' : 'paradas' }}</span>
                                    </div>
                                    <a href="{{ route('catalogo.rutas.show', $ruta) }}" class="tc-card-cta">Ver recorrido <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 18 6-6-6-6"/></svg></a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                    <div class="mt-8">{{ $rutas->links() }}</div>
                @endif
            </section>
        </div>
    </div>
@endsection
