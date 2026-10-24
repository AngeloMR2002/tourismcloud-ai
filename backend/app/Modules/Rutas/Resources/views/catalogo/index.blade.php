@extends('layouts.app', ['title' => __('catalog.routes_title')])

@section('content')
    @php
        $destinoSeleccionado = ! empty($filtros['destino_id'])
            ? $destinos->firstWhere('id', (int) $filtros['destino_id'])
            : null;
        $chipsFiltros = [];
        if (filled($filtros['costo_min'] ?? null) || filled($filtros['costo_max'] ?? null) || filled($filtros['duracion_min'] ?? null)) { $chipsFiltros[] = __('catalog.active_short'); }

        if ($destinoSeleccionado) {
            $chipsFiltros[] = __('catalog.destination_filter', ['name' => $destinoSeleccionado->nombre]);
        }
        if (! empty($filtros['duracion_max'])) {
            $dMax = (int) $filtros['duracion_max'];
            $durTexto = $dMax >= 1440 && $dMax % 1440 === 0
                ? ($dMax / 1440).' d.'
                : ($dMax % 60 === 0 ? ($dMax / 60).' h.' : $dMax.' min');
            $chipsFiltros[] = __('catalog.up_to_duration', ['duration' => $durTexto, 'minutes' => $durTexto]);
        }
        if (isset($filtros['distancia_max']) && $filtros['distancia_max'] !== '') {
            $chipsFiltros[] = __('catalog.up_to_distance', ['distance' => $filtros['distancia_max']]);
        }
        if (! empty($filtros['buscar'])) {
            $chipsFiltros[] = '“'.$filtros['buscar'].'”';
        }
    @endphp

    <div class="tc-catalog-shell max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        <header class="tc-page-heading" aria-labelledby="rutas-titulo">
            <p class="tc-home-eyebrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><circle cx="5" cy="5" r="2" stroke-width="1.8"/><circle cx="19" cy="19" r="2" stroke-width="1.8"/><path d="M7 5h6a4 4 0 0 1 0 8h-2a3 3 0 0 0 0 6h6" stroke-width="1.8"/></svg><span>{{ __('catalog.routes_eyebrow') }}</span></p>
            <h1 id="rutas-titulo">{{ __('catalog.routes_title') }}</h1>
            <p>{{ __('catalog.routes_intro') }}</p>
        </header>

        <details class="tc-filter-panel mt-5 p-4 lg:hidden" aria-label="{{ __('catalog.route_filters') }}">
            <summary class="tc-filter-summary">
                <span class="inline-flex items-center gap-2 font-bold text-gray-800"><svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 4h18M6 12h12m-9 8h6"/></svg>{{ __('catalog.adjust_filters') }}</span>
                @if(! empty($chipsFiltros))<span class="tc-filter-chip">{{ __('catalog.active_filters', ['count' => count($chipsFiltros)]) }}</span>@endif
            </summary>
            <form method="GET" action="{{ route('catalogo.rutas.index') }}" class="mt-5 grid gap-4">
                @if(isset($filtros['buscar']) && $filtros['buscar'] !== '')<input type="hidden" name="buscar" value="{{ $filtros['buscar'] }}">@endif
                @if(isset($filtros['por_pagina']) && $filtros['por_pagina'] !== '')<input type="hidden" name="por_pagina" value="{{ $filtros['por_pagina'] }}">@endif
                <div>
                    <label for="ruta-destino-movil" class="block text-xs font-bold mb-2 text-primary-700">{{ __('catalog.destination') }}</label>
                    <select id="ruta-destino-movil" name="destino_id" class="tc-select text-sm">
                        <option value="">{{ __('catalog.all_destinations') }}</option>
                        @foreach($destinos as $destino)<option value="{{ $destino->id }}" @selected((string) ($filtros['destino_id'] ?? '') === (string) $destino->id)>{{ $destino->nombre }}</option>@endforeach
                    </select>
                </div>
                <div class="grid gap-3">
                    <x-catalog-range name="costo" :label="__('favorites.entry_price')" :values="$filtros" :money="true" :ceiling="2000" />
                    <x-catalog-range name="duracion" :label="__('favorites.duration')" :values="$filtros" />
                    <div><label for="ruta-distancia-max-movil" class="block text-xs font-bold mb-2 text-primary-700">{{ __('catalog.max_distance_short') }}</label><input id="ruta-distancia-max-movil" type="number" name="distancia_max" min="0" max="99999999.99" step="0.01" value="{{ $filtros['distancia_max'] ?? '' }}" class="tc-input text-sm" placeholder="15"></div>
                </div>
                <button type="submit" class="tc-btn-primary w-full justify-center">{{ __('catalog.apply_filters') }}</button>
            </form>
        </details>

        <div class="mt-6 flex flex-col gap-6 lg:flex-row lg:items-start">
            <aside class="hidden w-60 shrink-0 lg:block" aria-label="{{ __('catalog.route_filters') }}">
                <form method="GET" action="{{ route('catalogo.rutas.index') }}" class="tc-filter-panel tc-filter-panel--desktop p-5">
                    <div class="flex items-center justify-between gap-3 pb-4 border-b border-gray-100"><h2 class="font-bold text-gray-900">{{ __('catalog.filters') }}</h2><svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 4h18M6 12h12m-9 8h6"/></svg></div>
                    @if(isset($filtros['buscar']) && $filtros['buscar'] !== '')<input type="hidden" name="buscar" value="{{ $filtros['buscar'] }}">@endif
                    @if(isset($filtros['por_pagina']) && $filtros['por_pagina'] !== '')<input type="hidden" name="por_pagina" value="{{ $filtros['por_pagina'] }}">@endif
                    <div class="mt-5"><label for="ruta-destino" class="block text-xs font-bold mb-2 text-primary-700">{{ __('catalog.destination') }}</label><select id="ruta-destino" name="destino_id" class="tc-select text-sm"><option value="">{{ __('catalog.all_destinations') }}</option>@foreach($destinos as $destino)<option value="{{ $destino->id }}" @selected((string) ($filtros['destino_id'] ?? '') === (string) $destino->id)>{{ $destino->nombre }}</option>@endforeach</select></div>
                    <x-catalog-range name="costo" :label="__('favorites.entry_price')" :values="$filtros" :money="true" :ceiling="2000" />
                    <x-catalog-range name="duracion" :label="__('favorites.duration')" :values="$filtros" />
                    <div class="mt-5"><label for="ruta-distancia-max" class="block text-xs font-bold mb-2 text-primary-700">{{ __('catalog.max_distance') }}</label><input id="ruta-distancia-max" type="number" name="distancia_max" min="0" max="99999999.99" step="0.01" value="{{ $filtros['distancia_max'] ?? '' }}" class="tc-input text-sm" placeholder="{{ __('catalog.example_value', ['value' => 15]) }}"></div>
                    <button type="submit" class="tc-btn-primary mt-6 w-full justify-center">{{ __('catalog.apply_filters') }}</button>
                    <a href="{{ route('catalogo.rutas.index') }}" class="mt-3 block w-full rounded-lg border border-gray-200 py-2 text-center text-sm font-semibold text-gray-600 transition hover:bg-gray-50">{{ __('catalog.clear_filters') }}</a>
                </form>
            </aside>

            <section class="min-w-0 flex-1" aria-label="{{ __('catalog.route_results') }}">
                <form method="GET" action="{{ route('catalogo.rutas.index') }}" class="mb-5 flex flex-col gap-2 sm:flex-row">
                    @foreach(['destino_id', 'costo_min', 'costo_max', 'duracion_min', 'duracion_max', 'distancia_max', 'por_pagina'] as $campo)
                        @if(isset($filtros[$campo]) && $filtros[$campo] !== '')<input type="hidden" name="{{ $campo }}" value="{{ $filtros[$campo] }}">@endif
                    @endforeach
                    <div class="tc-search-input-wrap"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35m1.35-5.15a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z"/></svg><input type="search" name="buscar" maxlength="100" value="{{ $filtros['buscar'] ?? '' }}" class="tc-input" aria-label="{{ __('catalog.search_routes') }}" placeholder="{{ __('catalog.search_name_description') }}"></div>
                    <button type="submit" class="tc-btn-primary justify-center">{{ __('catalog.search') }}</button>
                </form>

                @if($errors->any())<div class="tc-alert-error mb-5" role="alert">{{ $errors->first() }}</div>@endif

                <div class="tc-result-toolbar">
                    <div><p class="font-bold text-gray-900">{{ $rutas->total() }} {{ $rutas->total() === 1 ? __('catalog.result') : __('catalog.results') }}</p><p class="mt-1 text-xs sm:text-sm text-gray-500">{{ __('catalog.routes_disclaimer') }}</p></div>
                    @if(! empty($chipsFiltros))<div class="flex flex-wrap items-center gap-2">@foreach($chipsFiltros as $chip)<span class="tc-filter-chip">{{ $chip }}</span>@endforeach<a href="{{ route('catalogo.rutas.index') }}" class="text-xs font-bold text-primary-700 hover:underline">{{ __('catalog.clear') }}</a></div>@endif
                </div>

                @if($rutas->isEmpty())
                    <div class="tc-empty-state p-8 sm:p-12 text-center">
                        <div class="w-14 h-14 mx-auto rounded-2xl flex items-center justify-center bg-white text-primary-600"><svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M9 20 3 17V4l6 3 6-3 6 3v13l-6-3-6 3ZM9 7v13m6-16v13"/></svg></div>
                        @if(! empty($chipsFiltros))
                            <h2 class="mt-4 text-xl font-bold text-gray-900">{{ __('catalog.no_matches') }}</h2>
                            <p class="mt-2 text-sm text-gray-500">{{ __('catalog.route_no_matches_hint') }}</p>
                            <a href="{{ route('catalogo.rutas.index') }}" class="tc-btn-outline mt-5">{{ __('catalog.clear_filters') }}</a>
                        @else
                            <h2 class="mt-4 text-xl font-bold text-gray-900">{{ __('catalog.no_routes') }}</h2>
                            <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-gray-500">{{ __('catalog.no_routes_hint') }}</p>
                            <a href="{{ route('catalogo.atractivos.index') }}" class="tc-btn-outline mt-5">{{ __('catalog.explore_attractions') }}</a>
                        @endif
                    </div>
                @else
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
                        @foreach($rutas as $ruta)
                        <x-catalog-card :item="$ruta" type="ruta" />
                    @endforeach
                    </div>
                    <div class="mt-8">{{ $rutas->links() }}</div>
                @endif
            </section>
        </div>
    </div>
@endsection
