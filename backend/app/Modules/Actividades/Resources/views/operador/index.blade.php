@extends('layouts.app', ['title' => 'Mis actividades'])

@section('content')
    <main class="tc-workspace max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
        <div class="tc-page-heading">
            <div>
                <span class="tc-chip">Panel de operador</span>
                <h1>Mis actividades</h1>
                <p>Registra experiencias, controla su publicación y mantén sus datos listos para el catálogo turístico.</p>
            </div>
            <div class="tc-operator-heading__actions">
                <a href="{{ route('operador.rutas.index') }}" class="tc-btn-outline justify-center">Ver mis rutas</a>
                <a href="{{ route('operador.actividades.create') }}" class="tc-btn-primary justify-center">+ Nueva actividad</a>
            </div>
        </div>

        <form method="GET" action="{{ route('operador.actividades.index') }}" class="tc-operator-toolbar mb-6">
            <div class="tc-operator-toolbar__fields">
                <div>
                    <label for="buscar" class="block text-xs font-semibold text-gray-600 mb-1.5">Buscar</label>
                    <input id="buscar" name="buscar" value="{{ $filtros['buscar'] ?? '' }}" class="tc-input" placeholder="Nombre o descripción">
                </div>
                <div>
                    <label for="destino_id" class="block text-xs font-semibold text-gray-600 mb-1.5">Destino</label>
                    <select id="destino_id" name="destino_id" class="tc-select">
                        <option value="">Todos mis destinos</option>
                        @foreach($destinos as $destino)
                            <option value="{{ $destino->id }}" @selected((string) ($filtros['destino_id'] ?? '') === (string) $destino->id)>
                                {{ $destino->nombre }}{{ $destino->estado !== 'activo' ? ' (inactivo)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="estado" class="block text-xs font-semibold text-gray-600 mb-1.5">Estado</label>
                    <select id="estado" name="estado" class="tc-select">
                        <option value="">Todos los estados</option>
                        <option value="activo" @selected(($filtros['estado'] ?? '') === 'activo')>Activo</option>
                        <option value="inactivo" @selected(($filtros['estado'] ?? '') === 'inactivo')>Inactivo</option>
                    </select>
                </div>
            </div>
            <div class="tc-operator-toolbar__actions">
                <button type="submit" class="tc-btn-primary">Filtrar</button>
                <a href="{{ route('operador.actividades.index') }}" class="tc-btn-outline">Limpiar</a>
            </div>
        </form>

        @if($actividades->isEmpty())
            <div class="tc-card p-8 sm:p-12 text-center">
                <h2 class="text-lg font-bold text-gray-800">Aún no tienes actividades registradas</h2>
                <p class="mt-2 text-sm text-gray-500">Crea una actividad para que pueda aparecer en el catálogo turístico.</p>
                <div class="mt-5 flex flex-wrap justify-center gap-3">
                    <a href="{{ route('operador.actividades.create') }}" class="tc-btn-primary">Crear primera actividad</a>
                    <a href="{{ route('operador.rutas.index') }}" class="tc-btn-outline">Administrar rutas</a>
                </div>
            </div>
        @else
            <div class="tc-operator-summary">
                <span class="tc-operator-summary__item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><circle cx="12" cy="12" r="8" stroke-width="1.7"/><path d="M12 7v5l3 2" stroke-width="1.7" stroke-linecap="round"/></svg>{{ $actividades->total() }} actividades</span>
                <span class="tc-operator-summary__item">Revisa estado, costo y duración antes de publicar.</span>
            </div>
            <div class="tc-card overflow-hidden">
                <div class="hidden md:block overflow-x-auto">
                    <table class="tc-table tc-operator-table min-w-full">
                        <thead>
                            <tr>
                                <th class="text-left">Actividad</th>
                                <th class="text-left">Destino</th>
                                <th class="text-left">Costo</th>
                                <th class="text-left">Duración</th>
                                <th class="text-left">Estado</th>
                                <th class="text-right">Acciones</th>
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
                                    <td>{{ $actividad->costo > 0 ? 'S/ '.number_format((float) $actividad->costo, 2) : 'Gratuita' }}</td>
                                    <td>{{ $actividad->duracion_estimada_min ? $actividad->duracion_estimada_min.' min' : '—' }}</td>
                                    <td class="text-center">
                                        <form method="POST" action="{{ route('operador.actividades.toggle-estado', $actividad) }}" id="tog-actividad-{{ $actividad->id }}">
                                            @csrf
                                            @method('PATCH')
                                            <label class="tc-toggle" title="{{ $actividad->estado === 'activo' ? 'Desactivar' : 'Activar' }} actividad">
                                                <input type="checkbox" {{ $actividad->estado === 'activo' ? 'checked' : '' }}
                                                       aria-label="{{ $actividad->estado === 'activo' ? 'Desactivar' : 'Activar' }} {{ $actividad->nombre }}"
                                                       onchange="document.getElementById('tog-actividad-{{ $actividad->id }}').submit()">
                                                <span class="tc-toggle-track"></span>
                                                <span class="tc-toggle-thumb"></span>
                                            </label>
                                        </form>
                                    </td>
                                    <td>
                                        <div class="flex items-center justify-end gap-1">
                                            <a href="{{ route('catalogo.actividades.show', $actividad) }}" target="_blank" rel="noopener" title="Ver en catálogo" aria-label="Ver {{ $actividad->nombre }} en catálogo" class="tc-icon-action tc-icon-action--view">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z" stroke-width="1.8" stroke-linejoin="round"/><circle cx="12" cy="12" r="2.6" stroke-width="1.8"/></svg>
                                            </a>
                                            <a href="{{ route('operador.actividades.edit', $actividad) }}" title="Editar" aria-label="Editar {{ $actividad->nombre }}" class="tc-icon-action tc-icon-action--edit">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="m14.5 5.5 4 4M5 19l3.1-.7L19 7.4a2.8 2.8 0 0 0-4-4L4.1 14.3 3.5 17.5 5 19Z" stroke-width="1.8" stroke-linejoin="round" stroke-linecap="round"/></svg>
                                            </a>
                                            <form method="POST" action="{{ route('operador.actividades.destroy', $actividad) }}" onsubmit="return confirm('¿Eliminar esta actividad?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Eliminar" aria-label="Eliminar {{ $actividad->nombre }}" class="tc-icon-action tc-icon-action--danger">
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
                                    <p class="mt-2 text-xs text-gray-500">{{ $actividad->costo > 0 ? 'S/ '.number_format((float) $actividad->costo, 2) : 'Gratuita' }} · {{ $actividad->duracion_estimada_min ? $actividad->duracion_estimada_min.' min' : 'Duración por definir' }}</p>
                                </div>
                                <form method="POST" action="{{ route('operador.actividades.toggle-estado', $actividad) }}" id="tog-actividad-mobile-{{ $actividad->id }}">
                                    @csrf
                                    @method('PATCH')
                                    <label class="tc-toggle" title="{{ $actividad->estado === 'activo' ? 'Desactivar' : 'Activar' }} actividad">
                                        <input type="checkbox" {{ $actividad->estado === 'activo' ? 'checked' : '' }}
                                               aria-label="{{ $actividad->estado === 'activo' ? 'Desactivar' : 'Activar' }} {{ $actividad->nombre }}"
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
                                <a href="{{ route('catalogo.actividades.show', $actividad) }}" target="_blank" rel="noopener" title="Ver en catálogo" aria-label="Ver {{ $actividad->nombre }} en catálogo" class="tc-icon-action tc-icon-action--view">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z" stroke-width="1.8" stroke-linejoin="round"/><circle cx="12" cy="12" r="2.6" stroke-width="1.8"/></svg>
                                </a>
                                <a href="{{ route('operador.actividades.edit', $actividad) }}" title="Editar" aria-label="Editar {{ $actividad->nombre }}" class="tc-icon-action tc-icon-action--edit">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="m14.5 5.5 4 4M5 19l3.1-.7L19 7.4a2.8 2.8 0 0 0-4-4L4.1 14.3 3.5 17.5 5 19Z" stroke-width="1.8" stroke-linejoin="round" stroke-linecap="round"/></svg>
                                </a>
                                <form method="POST" action="{{ route('operador.actividades.destroy', $actividad) }}" onsubmit="return confirm('¿Eliminar esta actividad?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Eliminar" aria-label="Eliminar {{ $actividad->nombre }}" class="tc-icon-action tc-icon-action--danger">
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
