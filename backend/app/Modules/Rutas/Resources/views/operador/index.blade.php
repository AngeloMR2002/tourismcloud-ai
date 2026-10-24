@extends('layouts.app', ['title' => __('operator_routes.my_routes')])

@section('content')
<div class="tc-workspace max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
    <div class="tc-page-heading">
        <div>
            <span class="tc-chip">{{ __('operator_routes.operator_panel') }}</span>
            <h1>{{ __('operator_routes.my_routes') }}</h1>
            <p>{{ __('operator_routes.intro') }}</p>
        </div>
        <div class="tc-operator-heading__actions">
            <a href="{{ route('operador.actividades.index') }}" class="tc-btn-outline justify-center">{{ __('operator_routes.view_activities') }}</a>
            <a href="{{ route('operador.rutas.create') }}" class="tc-btn-primary justify-center">+ {{ __('operator_routes.new_route') }}</a>
        </div>
    </div>
    @if($errors->any())
        <div class="tc-alert-error mb-4" role="alert">{{ $errors->first() }}</div>
    @endif
    <form method="GET" action="{{ route('operador.rutas.index') }}" class="tc-operator-toolbar mb-6">
        <div class="tc-operator-toolbar__fields">
            <div>
                <label for="buscar" class="block text-xs font-semibold text-gray-600 mb-1.5">{{ __('operator_routes.search') }}</label>
                <input id="buscar" name="buscar" maxlength="100" value="{{ $filtros['buscar'] ?? '' }}" class="tc-input" placeholder="{{ __('operator_routes.name_or_description') }}">
            </div>
            <div>
                <label for="destino_id" class="block text-xs font-semibold text-gray-600 mb-1.5">{{ __('operator_routes.destination') }}</label>
                <select id="destino_id" name="destino_id" class="tc-select">
                    <option value="">{{ __('operator_routes.all_destinations') }}</option>
                    @foreach($destinos as $destino)
                        <option value="{{ $destino->id }}" @selected((string) ($filtros['destino_id'] ?? '') === (string) $destino->id)>{{ $destino->nombre }}{{ $destino->estado !== 'activo' ? ' ('.__('operator_routes.inactive').')' : '' }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="estado" class="block text-xs font-semibold text-gray-600 mb-1.5">{{ __('operator_routes.status') }}</label>
                <select id="estado" name="estado" class="tc-select">
                    <option value="">{{ __('operator_routes.all_statuses') }}</option>
                    <option value="activo" @selected(($filtros['estado'] ?? '') === 'activo')>{{ __('operator_routes.active') }}</option>
                    <option value="inactivo" @selected(($filtros['estado'] ?? '') === 'inactivo')>{{ __('operator_routes.inactive_status') }}</option>
                </select>
            </div>
        </div>
        <div class="tc-operator-toolbar__actions">
            <button type="submit" class="tc-btn-primary">{{ __('operator_routes.filter') }}</button>
            <a href="{{ route('operador.rutas.index') }}" class="tc-btn-outline">{{ __('operator_routes.clear') }}</a>
        </div>
    </form>
    @if($rutas->isEmpty())
        <div class="tc-card p-8 sm:p-12 text-center">
            <h2 class="text-lg font-bold text-gray-800">{{ __('operator_routes.no_routes') }}</h2>
            <p class="mt-2 text-sm text-gray-500">{{ __('operator_routes.no_routes_hint') }}</p>
            <div class="mt-5 flex flex-wrap justify-center gap-3">
                <a href="{{ route('operador.rutas.create') }}" class="tc-btn-primary">{{ __('operator_routes.create_route') }}</a>
                <a href="{{ route('operador.atractivos.index') }}" class="tc-btn-outline">{{ __('operator_routes.review_attractions') }}</a>
            </div>
        </div>
    @else
        <div class="tc-operator-summary">
            <span class="tc-operator-summary__item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M5 5h7a4 4 0 0 1 0 8H9a3 3 0 0 0 0 6h10" stroke-width="1.7" stroke-linecap="round"/><circle cx="5" cy="5" r="1.5" stroke-width="1.7"/><circle cx="19" cy="19" r="1.5" stroke-width="1.7"/></svg>{{ trans_choice('operator_routes.route_count', $rutas->total()) }}</span>
            <span class="tc-operator-summary__item">{{ __('operator_routes.minimum_stops_hint') }}</span>
        </div>
        <div class="grid gap-4 md:grid-cols-2">
            @foreach($rutas as $ruta)
                <article class="tc-operator-card">
                    <div class="flex gap-3 items-start">
                        <div class="tc-operator-card__media">@include('rutas::partials.portada', ['ruta' => $ruta])</div>
                        <div class="min-w-0 flex-1">
                            <p class="tc-operator-card__destination">{{ $ruta->destino->nombre }}</p>
                            <h2 class="tc-operator-card__title mt-1 break-words">{{ $ruta->nombre }}</h2>
                            <form method="POST" action="{{ route('operador.rutas.toggle-estado', $ruta) }}" id="tog-ruta-{{ $ruta->id }}">
                                @csrf
                                @method('PATCH')
                                <label class="tc-toggle" title="{{ $ruta->estado === 'activo' ? __('operator_routes.disable') : __('operator_routes.enable') }} {{ __('operator_routes.route') }}">
                                    <input type="checkbox" {{ $ruta->estado === 'activo' ? 'checked' : '' }}
                                           aria-label="{{ $ruta->estado === 'activo' ? __('operator_routes.disable') : __('operator_routes.enable') }} {{ $ruta->nombre }}"
                                           onchange="document.getElementById('tog-ruta-{{ $ruta->id }}').submit()">
                                    <span class="tc-toggle-track"></span>
                                    <span class="tc-toggle-thumb"></span>
                                </label>
                            </form>
                        </div>
                    </div>
                    @include('rutas::partials.credito', ['ruta' => $ruta])
                    @if($ruta->descripcion)
                        <p class="tc-operator-card__copy mt-3">{{ $ruta->descripcion }}</p>
                    @endif
                    <p class="mt-3 text-xs text-gray-500">{{ __('operator_routes.stops_count', ['count' => $ruta->paradas_count]) }} · {{ $ruta->distancia_km !== null ? $ruta->distancia_km.' km' : __('operator_routes.distance_unset') }} · {{ $ruta->duracion_estimada_min !== null ? $ruta->duracion_estimada_min.' min' : __('operator_routes.duration_unset') }}</p>
                    <p class="mt-2 text-[0.68rem] leading-5 text-gray-500">{{ __('operator_routes.publication_hint') }}</p>
                    <div class="tc-operator-card__actions justify-end">
                        <a href="{{ route('catalogo.rutas.show', $ruta) }}" target="_blank" rel="noopener" title="{{ __('operator_routes.view_in_catalog') }}" aria-label="{{ __('operator_routes.view_named_in_catalog', ['name' => $ruta->nombre]) }}" class="tc-icon-action tc-icon-action--view">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z" stroke-width="1.8" stroke-linejoin="round"/><circle cx="12" cy="12" r="2.6" stroke-width="1.8"/></svg>
                        </a>
                        <a href="{{ route('operador.rutas.edit', $ruta) }}" title="{{ __('operator_routes.edit') }}" aria-label="{{ __('operator_routes.edit_named', ['name' => $ruta->nombre]) }}" class="tc-icon-action tc-icon-action--edit">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="m14.5 5.5 4 4M5 19l3.1-.7L19 7.4a2.8 2.8 0 0 0-4-4L4.1 14.3 3.5 17.5 5 19Z" stroke-width="1.8" stroke-linejoin="round" stroke-linecap="round"/></svg>
                        </a>
                        <form method="POST" action="{{ route('operador.rutas.destroy', $ruta) }}" data-confirm-message="{{ __('operator_routes.delete_confirm') }}" onsubmit="return confirm(this.dataset.confirmMessage);">
                            @csrf @method('DELETE')
                            <button type="submit" title="{{ __('operator_routes.delete') }}" aria-label="{{ __('operator_routes.delete_named', ['name' => $ruta->nombre]) }}" class="tc-icon-action tc-icon-action--danger">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M5 7h14m-9 4v5m4-5v5M9 7V4h6v3m-8 0 .8 12h8.4L17 7" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </form>
                    </div>
                </article>
            @endforeach
        </div>
        <div class="mt-8">{{ $rutas->links() }}</div>
    @endif
</div>
@endsection
