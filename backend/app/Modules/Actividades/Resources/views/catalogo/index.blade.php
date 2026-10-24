@extends('layouts.app', ['title' => __('catalog.activities_title')])

@section('content')
    @php
        $destinoSeleccionado = ! empty($filtros['destino_id'])
            ? $destinos->firstWhere('id', (int) $filtros['destino_id'])
            : null;
        $chipsFiltros = [];
        if (filled($filtros['costo_min'] ?? null) || filled($filtros['duracion_min'] ?? null)) { $chipsFiltros[] = __('catalog.active_short'); }

        if ($destinoSeleccionado) {
            $chipsFiltros[] = __('catalog.destination_filter', ['name' => $destinoSeleccionado->nombre]);
        }
        if (isset($filtros['costo_max']) && $filtros['costo_max'] !== '') {
            $chipsFiltros[] = __('catalog.up_to_money', ['amount' => number_format((float) $filtros['costo_max'], 2)]);
        }
        if (! empty($filtros['duracion_max'])) {
            $dMax = (int) $filtros['duracion_max'];
            $durTexto = $dMax >= 1440 && $dMax % 1440 === 0
                ? ($dMax / 1440).' d.'
                : ($dMax % 60 === 0 ? ($dMax / 60).' h.' : $dMax.' min');
            $chipsFiltros[] = __('catalog.up_to_duration', ['duration' => $durTexto, 'minutes' => $durTexto]);
        }
        if (! empty($filtros['buscar'])) {
            $chipsFiltros[] = '“'.$filtros['buscar'].'”';
        }
    @endphp

    <div class="tc-catalog-shell max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        <header class="tc-page-heading" aria-labelledby="actividades-titulo">
            <p class="tc-home-eyebrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><circle cx="12" cy="4" r="2" stroke-width="1.8"/><path d="m10 8 4 2-2 6-5 5m5-5 3 3 3 1" stroke-width="1.8" stroke-linecap="round"/></svg><span>{{ __('catalog.activities_eyebrow') }}</span></p>
            <h1 id="actividades-titulo">{{ __('catalog.activities_title') }}</h1>
            <p>{{ __('catalog.activities_intro') }}</p>
        </header>

        <details class="tc-filter-panel mt-5 p-4 lg:hidden" aria-label="{{ __('catalog.activity_filters') }}">
            <summary class="tc-filter-summary">
                <span class="inline-flex items-center gap-2 font-bold text-gray-800">
                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 4h18M6 12h12m-9 8h6"/></svg>
                    {{ __('catalog.adjust_filters') }}
                </span>
                @if(! empty($chipsFiltros))
                    <span class="tc-filter-chip">{{ __('catalog.active_filters', ['count' => count($chipsFiltros)]) }}</span>
                @endif
            </summary>
            <form method="GET" action="{{ route('catalogo.actividades.index') }}" class="mt-5 grid gap-4">
                @if(isset($filtros['buscar']) && $filtros['buscar'] !== '')
                    <input type="hidden" name="buscar" value="{{ $filtros['buscar'] }}">
                @endif
                <div>
                    <label for="actividad-destino-movil" class="block text-xs font-bold mb-2 text-primary-700">{{ __('catalog.destination') }}</label>
                    <select id="actividad-destino-movil" name="destino_id" class="tc-select text-sm">
                        <option value="">{{ __('catalog.all_destinations') }}</option>
                        @foreach($destinos as $destino)
                            <option value="{{ $destino->id }}" @selected((string) ($filtros['destino_id'] ?? '') === (string) $destino->id)>{{ $destino->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <x-catalog-range name="costo" :label="__('favorites.price')" :values="$filtros" :money="true" :ceiling="2000" />
                    <x-catalog-range name="duracion" :label="__('favorites.duration')" :values="$filtros" />
                <button type="submit" class="tc-btn-primary w-full justify-center">{{ __('catalog.apply_filters') }}</button>
            </form>
        </details>

        <div class="mt-6 flex flex-col gap-6 lg:flex-row lg:items-start">
            <aside class="hidden w-60 shrink-0 lg:block" aria-label="{{ __('catalog.activity_filters') }}">
                <form method="GET" action="{{ route('catalogo.actividades.index') }}" class="tc-filter-panel tc-filter-panel--desktop p-5">
                    <div class="flex items-center justify-between gap-3 pb-4 border-b border-gray-100">
                        <h2 class="font-bold text-gray-900">{{ __('catalog.filters') }}</h2>
                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 4h18M6 12h12m-9 8h6"/></svg>
                    </div>
                    @if(isset($filtros['buscar']) && $filtros['buscar'] !== '')
                        <input type="hidden" name="buscar" value="{{ $filtros['buscar'] }}">
                    @endif
                    <div class="mt-5">
                        <label for="actividad-destino" class="block text-xs font-bold mb-2 text-primary-700">{{ __('catalog.destination') }}</label>
                        <select id="actividad-destino" name="destino_id" class="tc-select text-sm">
                            <option value="">{{ __('catalog.all_destinations') }}</option>
                            @foreach($destinos as $destino)
                                <option value="{{ $destino->id }}" @selected((string) ($filtros['destino_id'] ?? '') === (string) $destino->id)>{{ $destino->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <x-catalog-range name="costo" :label="__('favorites.price')" :values="$filtros" :money="true" :ceiling="2000" />
                    <x-catalog-range name="duracion" :label="__('favorites.duration')" :values="$filtros" />
                    <button type="submit" class="tc-btn-primary mt-6 w-full justify-center">{{ __('catalog.apply_filters') }}</button>
                    <a href="{{ route('catalogo.actividades.index') }}" class="mt-3 block w-full rounded-lg border border-gray-200 py-2 text-center text-sm font-semibold text-gray-600 transition hover:bg-gray-50">{{ __('catalog.clear_filters') }}</a>
                </form>
            </aside>

            <section class="min-w-0 flex-1" aria-label="{{ __('catalog.activity_results') }}">
                <form method="GET" action="{{ route('catalogo.actividades.index') }}" class="mb-5 flex flex-col gap-2 sm:flex-row">
                    @foreach(['destino_id', 'costo_min', 'costo_max', 'duracion_min', 'duracion_max'] as $campo)
                        @if(isset($filtros[$campo]) && $filtros[$campo] !== '')
                            <input type="hidden" name="{{ $campo }}" value="{{ $filtros[$campo] }}">
                        @endif
                    @endforeach
                    <div class="tc-search-input-wrap">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35m1.35-5.15a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z"/></svg>
                        <input type="search" name="buscar" value="{{ $filtros['buscar'] ?? '' }}" class="tc-input" maxlength="100" aria-label="{{ __('catalog.search_activities') }}" placeholder="{{ __('catalog.search_name_description') }}">
                    </div>
                    <button type="submit" class="tc-btn-primary justify-center">{{ __('catalog.search') }}</button>
                </form>

                @if($errors->any())
                    <div class="tc-alert-error mb-5" role="alert">{{ $errors->first() }}</div>
                @endif

                <div class="tc-result-toolbar">
                    <div>
                        <p class="font-bold text-gray-900">{{ $actividades->total() }} {{ $actividades->total() === 1 ? __('catalog.result') : __('catalog.results') }}</p>
                        <p class="mt-1 text-xs sm:text-sm text-gray-500">{{ __('catalog.activities_disclaimer') }}</p>
                    </div>
                    @if(! empty($chipsFiltros))
                        <div class="flex flex-wrap items-center gap-2">
                            @foreach($chipsFiltros as $chip)
                                <span class="tc-filter-chip">{{ $chip }}</span>
                            @endforeach
                            <a href="{{ route('catalogo.actividades.index') }}" class="text-xs font-bold text-primary-700 hover:underline">{{ __('catalog.clear') }}</a>
                        </div>
                    @endif
                </div>

                @if($actividades->isEmpty())
                    <div class="tc-empty-state p-8 sm:p-12 text-center">
                        <div class="w-14 h-14 mx-auto rounded-2xl flex items-center justify-center bg-white text-primary-600 shadow-sm">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="m4 15 4-4 3 3 5-6 4 5M5 19h14"/></svg>
                        </div>
                        @if(! empty($chipsFiltros))
                            <h2 class="mt-4 text-xl font-bold text-gray-900">{{ __('catalog.no_matches') }}</h2>
                            <p class="mt-2 text-sm text-gray-500">{{ __('catalog.activity_no_matches_hint') }}</p>
                            <a href="{{ route('catalogo.actividades.index') }}" class="tc-btn-outline mt-5">{{ __('catalog.clear_filters') }}</a>
                        @else
                            <h2 class="mt-4 text-xl font-bold text-gray-900">{{ __('catalog.no_activities') }}</h2>
                            <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-gray-500">{{ __('catalog.no_activities_hint') }}</p>
                            <a href="{{ route('catalogo.atractivos.index') }}" class="tc-btn-outline mt-5">{{ __('catalog.explore_attractions') }}</a>
                        @endif
                    </div>
                @else
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
                        @foreach($actividades as $actividad)
                        <x-catalog-card :item="$actividad" type="actividad" />
                    @endforeach
                    </div>
                    <div class="mt-8">{{ $actividades->links() }}</div>
                @endif
            </section>
        </div>
    </div>
@endsection
