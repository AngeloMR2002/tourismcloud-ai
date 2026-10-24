@extends('layouts.app')

@section('title', __('catalog.places_title'))

@section('content')
@php
    $rangoPrecio = ['Bajo' => __('catalog.price_low').' $', 'Medio' => __('catalog.price_mid').' $$', 'Alto' => __('catalog.price_high').' $$$', 'Lujoso' => __('catalog.price_luxury').' $$$$'];
    $tipos = ['hotel' => __('catalog.hotel'), 'restaurante' => __('catalog.restaurant'), 'transporte' => __('catalog.transport'), 'agencia' => __('catalog.agency'), 'otro' => __('catalog.other')];
    $filtroActivo = collect($filtros)->only(['destino_id', 'tipo', 'categoria_id', 'rango_precio', 'busqueda'])->contains(fn ($valor) => filled($valor));
@endphp

<div class="tc-catalog-shell max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
    <header class="tc-page-heading" aria-labelledby="establecimientos-titulo">
        <p class="tc-home-eyebrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M4 21V6a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v15M9 21v-5h6v5" stroke-width="1.8"/><circle cx="12" cy="10" r="1.5"/></svg><span>{{ __('catalog.places_eyebrow') }}</span></p>
        <h1 id="establecimientos-titulo">{{ __('catalog.places_heading') }}</h1>
        <p>{{ __('catalog.places_intro') }}</p>
    </header>

    <div class="mt-6 flex flex-col gap-6 lg:flex-row lg:items-start">
        @foreach(['movil', 'escritorio'] as $modo)
            @if($modo === 'movil')
                <details class="tc-filter-panel p-4 lg:hidden" aria-label="{{ __('catalog.places_filters') }}">
                    <summary class="tc-filter-summary"><span class="font-bold text-gray-900">{{ __('catalog.adjust_filters') }}</span>@if($filtroActivo)<span class="tc-filter-chip">{{ __('catalog.active_short') }}</span>@endif</summary>
            @else
                <aside class="hidden w-60 shrink-0 lg:block" aria-label="{{ __('catalog.places_filters') }}">
            @endif
                <form method="GET" action="{{ route('catalogo.establecimientos.index') }}" class="{{ $modo === 'movil' ? 'mt-5' : 'tc-filter-panel p-5' }} space-y-5">
                    @if($modo === 'escritorio')<h2 class="border-b border-gray-100 pb-4 font-bold text-gray-900">{{ __('catalog.filters') }}</h2>@endif
                    @if(filled($filtros['busqueda'] ?? null))<input type="hidden" name="busqueda" value="{{ $filtros['busqueda'] }}">@endif
                    @if(filled($filtros['por_pagina'] ?? null))<input type="hidden" name="por_pagina" value="{{ $filtros['por_pagina'] }}">@endif
                    <div>
                        <label for="establecimiento-destino-{{ $modo }}" class="mb-2 block text-xs font-bold text-gray-700">{{ __('catalog.destination') }}</label>
                        <select id="establecimiento-destino-{{ $modo }}" name="destino_id" class="tc-select text-sm">
                            <option value="">{{ __('catalog.all_destinations') }}</option>
                            @foreach($destinos as $destino)<option value="{{ $destino->id }}" @selected(($filtros['destino_id'] ?? '') == $destino->id)>{{ $destino->nombre }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label for="establecimiento-tipo-{{ $modo }}" class="mb-2 block text-xs font-bold text-gray-700">{{ __('catalog.service_type') }}</label>
                        <select id="establecimiento-tipo-{{ $modo }}" name="tipo" class="tc-select text-sm">
                            <option value="">{{ __('catalog.all_types') }}</option>
                            @foreach($tipos as $valor => $etiqueta)<option value="{{ $valor }}" @selected(($filtros['tipo'] ?? '') === $valor)>{{ $etiqueta }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label for="establecimiento-categoria-{{ $modo }}" class="mb-2 block text-xs font-bold text-gray-700">{{ __('catalog.interest_category') }}</label>
                        <select id="establecimiento-categoria-{{ $modo }}" name="categoria_id" class="tc-select text-sm">
                            <option value="">{{ __('catalog.all_categories') }}</option>
                            @foreach($categorias as $categoria)<option value="{{ $categoria->id }}" @selected(($filtros['categoria_id'] ?? '') == $categoria->id)>{{ $categoria->nombre }}</option>@endforeach
                        </select>
                    </div>
                    <fieldset class="tc-price-options">
                        <legend class="mb-3 font-bold">{{ __('catalog.price_level') }}</legend>
                        <label><input type="radio" name="rango_precio" value="" @checked(empty($filtros['rango_precio']))>{{ __('catalog.all_levels') }}</label>
                        @foreach($rangoPrecio as $valor => $etiqueta)
                            <label><input type="radio" name="rango_precio" value="{{ $valor }}" @checked(($filtros['rango_precio'] ?? '') === $valor)>{{ $etiqueta }}</label>
                        @endforeach
                    </fieldset>
                    <button type="submit" class="tc-btn-primary w-full justify-center">{{ __('catalog.apply_filters') }}</button>
                    <a href="{{ route('catalogo.establecimientos.index') }}" class="block text-center text-sm font-semibold text-gray-600 hover:underline">{{ __('catalog.clear_filters') }}</a>
                </form>
            @if($modo === 'movil')</details>@else</aside>@endif
        @endforeach

        <section class="min-w-0 flex-1" aria-label="{{ __('catalog.places_results') }}">
            <form method="GET" action="{{ route('catalogo.establecimientos.index') }}" class="mb-5 flex flex-col gap-2 sm:flex-row">
                @foreach(['destino_id', 'tipo', 'categoria_id', 'rango_precio', 'por_pagina'] as $campo)
                    @if(filled($filtros[$campo] ?? null))<input type="hidden" name="{{ $campo }}" value="{{ $filtros[$campo] }}">@endif
                @endforeach
                <div class="tc-search-input-wrap">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35m1.35-5.15a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z"/></svg>
                    <input type="search" name="busqueda" value="{{ $filtros['busqueda'] ?? '' }}" placeholder="{{ __('catalog.search_name_description') }}" aria-label="{{ __('catalog.search_places') }}" class="tc-input">
                </div>
                <button type="submit" class="tc-btn-primary justify-center">{{ __('catalog.search') }}</button>
            </form>
            @if($errors->any())<div class="tc-alert-error mb-5" role="alert">{{ $errors->first() }}</div>@endif

            <div class="tc-result-toolbar">
                <div>
                    <p class="font-bold text-gray-900">{{ number_format($establecimientos->total()) }} {{ $establecimientos->total() === 1 ? __('catalog.result') : __('catalog.results') }}</p>
                    <p class="mt-1 text-xs sm:text-sm text-gray-500">{{ __('catalog.places_disclaimer') }}</p>
                </div>
                @if($filtroActivo)<a href="{{ route('catalogo.establecimientos.index') }}" class="text-xs font-bold text-primary-700 hover:underline">{{ __('catalog.clear_filters') }}</a>@endif
            </div>

            @if($establecimientos->isEmpty())
                <div class="tc-empty-state p-8 sm:p-12 text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-primary-600"><svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M4 21V6a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v15M9 21v-5h6v5M8 8h.01M12 8h.01M16 8h.01M8 12h.01M12 12h.01M16 12h.01"/></svg></div>
                    @if($filtroActivo)
                        <h2 class="mt-4 text-xl font-bold text-gray-900">{{ __('catalog.no_matches') }}</h2>
                        <p class="mt-2 text-sm leading-6 text-gray-500">{{ __('catalog.places_no_matches_hint') }}</p>
                        <a href="{{ route('catalogo.establecimientos.index') }}" class="tc-btn-outline mt-5">{{ __('catalog.clear_filters') }}</a>
                    @else
                        <h2 class="mt-4 text-xl font-bold text-gray-900">{{ __('catalog.no_places') }}</h2>
                        <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-gray-500">{{ __('catalog.no_places_hint') }}</p>
                        <a href="{{ route('catalogo.atractivos.index') }}" class="tc-btn-outline mt-5">{{ __('catalog.explore_attractions') }}</a>
                    @endif
                </div>
            @else
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
                    @foreach($establecimientos as $est)
                        <x-catalog-card :item="$est" type="establecimiento" />
                    @endforeach
                </div>
                <div class="mt-8">{{ $establecimientos->appends(request()->query())->links() }}</div>
            @endif
        </section>
    </div>
</div>
@endsection
