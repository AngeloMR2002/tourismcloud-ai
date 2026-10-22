@extends('layouts.app', ['title' => __('operator.my_activities')])

@section('content')
    <main class="tc-workspace max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
        <div class="tc-page-heading">
            <div>
                <span class="tc-chip">{{ __('operator.operator_panel') }}</span>
                <h1>{{ __('operator.my_activities') }}</h1>
                <p>{{ __('operator.activities_intro') }}</p>
            </div>
            <div class="tc-operator-heading__actions">
                <a href="{{ route('operador.rutas.index') }}" class="tc-btn-outline justify-center">{{ __('operator.view_my_routes') }}</a>
                <a href="{{ route('operador.actividades.create') }}" class="tc-btn-primary justify-center">+ {{ __('operator.new_activity') }}</a>
            </div>
        </div>

        <form method="GET" action="{{ route('operador.actividades.index') }}" class="tc-operator-toolbar mb-6">
            <div class="tc-operator-toolbar__fields">
                <div>
                    <label for="buscar" class="block text-xs font-semibold text-gray-600 mb-1.5">{{ __('operator.search') }}</label>
                    <input id="buscar" name="buscar" value="{{ $filtros['buscar'] ?? '' }}" class="tc-input" placeholder="{{ __('operator.name_or_description') }}">
                </div>
                <div>
                    <label for="destino_id" class="block text-xs font-semibold text-gray-600 mb-1.5">{{ __('operator.destination') }}</label>
                    <select id="destino_id" name="destino_id" class="tc-select">
                        <option value="">{{ __('operator.all_my_destinations') }}</option>
                        @foreach($destinos as $destino)
                            <option value="{{ $destino->id }}" @selected((string) ($filtros['destino_id'] ?? '') === (string) $destino->id)>
                                {{ $destino->nombre }}{{ $destino->estado !== 'activo' ? ' ('.__('operator.inactive').')' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="estado" class="block text-xs font-semibold text-gray-600 mb-1.5">{{ __('operator.status') }}</label>
                    <select id="estado" name="estado" class="tc-select">
                        <option value="">{{ __('operator.all_statuses') }}</option>
                        <option value="activo" @selected(($filtros['estado'] ?? '') === 'activo')>{{ __('operator.active_status') }}</option>
                        <option value="inactivo" @selected(($filtros['estado'] ?? '') === 'inactivo')>{{ __('operator.inactive_status') }}</option>
                    </select>
                </div>
            </div>
            <div class="tc-operator-toolbar__actions">
                <button type="submit" class="tc-btn-primary">{{ __('operator.filter') }}</button>
                <a href="{{ route('operador.actividades.index') }}" class="tc-btn-outline">{{ __('operator.clear') }}</a>
            </div>
        </form>

        @if($actividades->isEmpty())
            <div class="tc-card p-8 sm:p-12 text-center">
                <h2 class="text-lg font-bold text-gray-800">{{ __('operator.no_activities') }}</h2>
                <p class="mt-2 text-sm text-gray-500">{{ __('operator.no_activities_hint') }}</p>
                <div class="mt-5 flex flex-wrap justify-center gap-3">
                    <a href="{{ route('operador.actividades.create') }}" class="tc-btn-primary">{{ __('operator.create_first_activity') }}</a>
                    <a href="{{ route('operador.rutas.index') }}" class="tc-btn-outline">{{ __('operator.manage_routes') }}</a>
                </div>
            </div>
        @else
            <div class="tc-operator-summary">
                <span class="tc-operator-summary__item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><circle cx="12" cy="12" r="8" stroke-width="1.7"/><path d="M12 7v5l3 2" stroke-width="1.7" stroke-linecap="round"/></svg>{{ trans_choice('operator.activities_count', $actividades->total()) }}</span>
                <span class="tc-operator-summary__item">{{ __('operator.review_before_publish') }}</span>
            </div>
            <div class="tc-card overflow-hidden">
                <div class="hidden md:block overflow-x-auto">
                    <table class="tc-table tc-operator-table min-w-full">
                        <thead>
                            <tr>
                                <th class="text-left">{{ __('operator.activity') }}</th>
                                <th class="text-left">{{ __('operator.destination') }}</th>
                                <th class="text-left">{{ __('operator.cost') }}</th>
                                <th class="text-left">{{ __('operator.duration') }}</th>
                                <th class="text-left">{{ __('operator.status') }}</th>
                                <th class="text-right">{{ __('operator.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($actividades as $actividad)
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-3">
                                            @if($actividad->portada_url)
                                                <img src="{{ $actividad->portada_url }}" alt="" class="w-10 h-10 rounded-lg object-cover shrink-0">
                                            @else
                                                <div class="w-10 h-10 rounded-lg shrink-0 flex items-center justify-center" style="background: var(--color-teal-light); color: var(--color-primary-500);">✦</div>
                                            @endif
                                            <span class="font-semibold text-gray-800">{{ $actividad->nombre }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $actividad->destino->nombre }}</td>
                                    <td>{{ $actividad->costo > 0 ? 'S/ '.number_format((float) $actividad->costo, 2) : __('operator.free') }}</td>
                                    <td>{{ $actividad->duracion_estimada_min ? $actividad->duracion_estimada_min.' min' : '—' }}</td>
                                    <td class="text-center">
                                        <form method="POST" action="{{ route('operador.actividades.toggle-estado', $actividad) }}" id="tog-actividad-{{ $actividad->id }}">
                                            @csrf
                                            @method('PATCH')
                                            <label class="tc-toggle" title="{{ $actividad->estado === 'activo' ? __('operator.disable') : __('operator.enable') }} {{ __('operator.activity') }}">
                                                <input type="checkbox" {{ $actividad->estado === 'activo' ? 'checked' : '' }}
                                                       aria-label="{{ $actividad->estado === 'activo' ? __('operator.disable') : __('operator.enable') }} {{ $actividad->nombre }}"
                                                       onchange="document.getElementById('tog-actividad-{{ $actividad->id }}').submit()">
                                                <span class="tc-toggle-track"></span>
                                                <span class="tc-toggle-thumb"></span>
                                            </label>
                                        </form>
                                    </td>
                                    <td>
                                        <div class="flex items-center justify-end gap-1">
                                            <a href="{{ route('catalogo.actividades.show', $actividad) }}" target="_blank" rel="noopener" title="{{ __('operator.view_in_catalog') }}" aria-label="{{ __('operator.view_named_in_catalog', ['name' => $actividad->nombre]) }}" class="tc-icon-action tc-icon-action--view">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z" stroke-width="1.8" stroke-linejoin="round"/><circle cx="12" cy="12" r="2.6" stroke-width="1.8"/></svg>
                                            </a>
                                            <a href="{{ route('operador.actividades.edit', $actividad) }}" title="{{ __('operator.edit') }}" aria-label="{{ __('operator.edit_named', ['name' => $actividad->nombre]) }}" class="tc-icon-action tc-icon-action--edit">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="m14.5 5.5 4 4M5 19l3.1-.7L19 7.4a2.8 2.8 0 0 0-4-4L4.1 14.3 3.5 17.5 5 19Z" stroke-width="1.8" stroke-linejoin="round" stroke-linecap="round"/></svg>
                                            </a>
                                            <form method="POST" action="{{ route('operador.actividades.destroy', $actividad) }}" data-confirm-message="{{ __('operator.delete_activity_confirm') }}" onsubmit="return confirm(this.dataset.confirmMessage);">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="{{ __('operator.delete') }}" aria-label="{{ __('operator.delete_named', ['name' => $actividad->nombre]) }}" class="tc-icon-action tc-icon-action--danger">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M5 7h14m-9 4v5m4-5v5M9 7V4h6v3m-8 0 .8 12h8.4L17 7" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="grid gap-3 p-3 md:hidden">
                    @foreach($actividades as $actividad)
                        <article class="tc-operator-card">
                            <div class="flex items-start gap-3">
                                <div class="tc-operator-card__media {{ $actividad->portada_url ? '' : 'tc-operator-card__media--fallback' }}">
                                    @if($actividad->portada_url)
                                        <img src="{{ $actividad->portada_url }}" alt="">
                                    @else
                                        <span aria-hidden="true">✦</span>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="tc-operator-card__destination">{{ $actividad->destino->nombre }}</p>
                                    <h2 class="tc-operator-card__title mt-1">{{ $actividad->nombre }}</h2>
                                    <p class="mt-2 text-xs text-gray-500">{{ $actividad->costo > 0 ? 'S/ '.number_format((float) $actividad->costo, 2) : __('operator.free') }} · {{ $actividad->duracion_estimada_min ? $actividad->duracion_estimada_min.' min' : __('operator.duration_unknown') }}</p>
                                </div>
                                <form method="POST" action="{{ route('operador.actividades.toggle-estado', $actividad) }}" id="tog-actividad-mobile-{{ $actividad->id }}">
                                    @csrf
                                    @method('PATCH')
                                    <label class="tc-toggle" title="{{ $actividad->estado === 'activo' ? __('operator.disable') : __('operator.enable') }} {{ __('operator.activity') }}">
                                        <input type="checkbox" {{ $actividad->estado === 'activo' ? 'checked' : '' }}
                                               aria-label="{{ $actividad->estado === 'activo' ? __('operator.disable') : __('operator.enable') }} {{ $actividad->nombre }}"
                                               onchange="document.getElementById('tog-actividad-mobile-{{ $actividad->id }}').submit()">
                                        <span class="tc-toggle-track"></span>
                                        <span class="tc-toggle-thumb"></span>
                                    </label>
                                </form>
                            </div>
                            @if($actividad->descripcion)
                                <p class="tc-operator-card__copy mt-3">{{ $actividad->descripcion }}</p>
                            @endif
                            <div class="tc-operator-card__actions justify-end">
                                <a href="{{ route('catalogo.actividades.show', $actividad) }}" target="_blank" rel="noopener" title="{{ __('operator.view_in_catalog') }}" aria-label="{{ __('operator.view_named_in_catalog', ['name' => $actividad->nombre]) }}" class="tc-icon-action tc-icon-action--view">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z" stroke-width="1.8" stroke-linejoin="round"/><circle cx="12" cy="12" r="2.6" stroke-width="1.8"/></svg>
                                </a>
                                <a href="{{ route('operador.actividades.edit', $actividad) }}" title="{{ __('operator.edit') }}" aria-label="{{ __('operator.edit_named', ['name' => $actividad->nombre]) }}" class="tc-icon-action tc-icon-action--edit">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="m14.5 5.5 4 4M5 19l3.1-.7L19 7.4a2.8 2.8 0 0 0-4-4L4.1 14.3 3.5 17.5 5 19Z" stroke-width="1.8" stroke-linejoin="round" stroke-linecap="round"/></svg>
                                </a>
                                <form method="POST" action="{{ route('operador.actividades.destroy', $actividad) }}" data-confirm-message="{{ __('operator.delete_activity_confirm') }}" onsubmit="return confirm(this.dataset.confirmMessage);">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="{{ __('operator.delete') }}" aria-label="{{ __('operator.delete_named', ['name' => $actividad->nombre]) }}" class="tc-icon-action tc-icon-action--danger">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M5 7h14m-9 4v5m4-5v5M9 7V4h6v3m-8 0 .8 12h8.4L17 7" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </button>
                                </form>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>

            <div class="mt-8">{{ $actividades->links() }}</div>
        @endif
    </main>
@endsection
