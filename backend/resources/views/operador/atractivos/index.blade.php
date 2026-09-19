@extends('layouts.app')

@section('title', 'Mis Atractivos')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- ── Header del panel ────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
                      style="background: var(--color-teal-light); color: var(--color-primary-700);">
                    Panel Operador
                </span>
            </div>
            <h1 style="font-family: var(--font-display); font-size: 1.75rem; font-weight: 800; color: #1a2232;">
                Mis Atractivos
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Gestiona los atractivos de tus destinos. Activa o desactiva su visibilidad en el catálogo.
            </p>
        </div>
        <a href="{{ route('operador.atractivos.create') }}" class="tc-btn-primary shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo Atractivo
        </a>
    </div>

    {{-- ── Filtros rápidos ─────────────────────────────────────── --}}
    <div class="tc-card p-4 mb-6">
        <form method="GET" action="{{ route('operador.atractivos.index') }}" class="flex flex-col sm:flex-row gap-3">
            {{-- Pasar el ID del operador (provisional) --}}
            <input type="hidden" name="_operador_id_test" value="{{ request('_operador_id_test', 1) }}">

            <select name="destino_id" class="tc-select sm:w-56">
                <option value="">Todos los destinos</option>
                @foreach($destinos as $d)
                    <option value="{{ $d->id }}" @selected(request('destino_id') == $d->id)>{{ $d->nombre }}</option>
                @endforeach
            </select>

            <div class="relative flex-1">
                <input type="text" name="busqueda" value="{{ request('busqueda') }}"
                       placeholder="Buscar atractivo..." class="tc-input pl-9">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            <button type="submit" class="tc-btn-primary">Buscar</button>
            <a href="{{ route('operador.atractivos.index') }}" class="tc-btn-outline">Limpiar</a>
        </form>
    </div>

    {{-- ── Tabla de gestión ─────────────────────────────────────── --}}
    <div class="tc-card overflow-hidden">
        @if($atractivos->isEmpty())
            <div class="text-center py-16">
                <svg class="w-14 h-14 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
                <p class="text-gray-500 font-medium mb-3">No tienes atractivos aún.</p>
                <a href="{{ route('operador.atractivos.create') }}" class="tc-btn-primary text-sm">
                    Crear tu primer atractivo
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="tc-table">
                    <thead>
                        <tr>
                            <th>Atractivo</th>
                            <th>Destino</th>
                            <th>Categorías</th>
                            <th>Costo (S/)</th>
                            <th>Duración</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($atractivos as $atractivo)
                            <tr>
                                {{-- Nombre + portada --}}
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg overflow-hidden shrink-0"
                                             style="background: var(--color-teal-light);">
                                            @if($atractivo->imagen_portada)
                                                <img src="{{ asset('storage/' . $atractivo->imagen_portada) }}"
                                                     alt="{{ $atractivo->nombre }}"
                                                     class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-lg">🗺️</div>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-800 leading-tight">{{ $atractivo->nombre }}</p>
                                            <p class="text-xs text-gray-400">ID #{{ $atractivo->id }}</p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Destino --}}
                                <td>
                                    @if($atractivo->destino)
                                        <span class="text-sm text-gray-600">{{ $atractivo->destino->nombre }}</span>
                                    @else
                                        <span class="text-gray-400 text-xs">Sin destino</span>
                                    @endif
                                </td>

                                {{-- Categorías --}}
                                <td>
                                    <div class="flex flex-wrap gap-1 max-w-xs">
                                        @forelse($atractivo->categorias->take(3) as $cat)
                                            <span class="tc-chip">{{ $cat->nombre }}</span>
                                        @empty
                                            <span class="text-gray-400 text-xs">Sin categorías</span>
                                        @endforelse
                                    </div>
                                </td>

                                {{-- Costo --}}
                                <td>
                                    <span class="font-semibold" style="color: var(--color-primary-700);">
                                        {{ $atractivo->costo_entrada > 0 ? 'S/ ' . number_format($atractivo->costo_entrada, 2) : 'Gratuito' }}
                                    </span>
                                </td>

                                {{-- Duración --}}
                                <td class="text-sm text-gray-500">
                                    @if($atractivo->duracion_estimada_min)
                                        @php $h = intdiv($atractivo->duracion_estimada_min, 60); $m = $atractivo->duracion_estimada_min % 60; @endphp
                                        {{ $h > 0 ? "{$h}h " : '' }}{{ $m > 0 ? "{$m}min" : '' }}
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>

                                {{-- Toggle de estado (ACTIVO/INACTIVO) --}}
                                {{-- No hace soft delete, solo cambia el campo 'estado' --}}
                                <td class="text-center">
                                    <form method="POST"
                                          action="{{ route('operador.atractivos.toggle-estado', $atractivo) }}"
                                          id="toggle-form-{{ $atractivo->id }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="_operador_id_test" value="{{ request('_operador_id_test', 1) }}">

                                        <label class="tc-toggle" title="{{ $atractivo->estado === 'activo' ? 'Desactivar' : 'Activar' }}">
                                            <input type="checkbox"
                                                   {{ $atractivo->estado === 'activo' ? 'checked' : '' }}
                                                   onchange="document.getElementById('toggle-form-{{ $atractivo->id }}').submit()">
                                            <span class="tc-toggle-track"></span>
                                            <span class="tc-toggle-thumb"></span>
                                        </label>
                                    </form>
                                    <p class="text-xs mt-1 {{ $atractivo->estado === 'activo' ? 'text-green-600' : 'text-gray-400' }}">
                                        {{ ucfirst($atractivo->estado) }}
                                    </p>
                                </td>

                                {{-- Acciones --}}
                                <td class="text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('catalogo.atractivos.show', $atractivo) }}"
                                           target="_blank"
                                           title="Ver en catálogo"
                                           class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                        <a href="{{ route('operador.atractivos.edit', $atractivo) }}"
                                           title="Editar"
                                           class="p-1.5 rounded-lg transition-colors"
                                           style="color: var(--color-primary-600);"
                                           onmouseover="this.style.background='var(--color-teal-light)'"
                                           onmouseout="this.style.background='transparent'">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <form method="POST"
                                              action="{{ route('operador.atractivos.destroy', $atractivo) }}"
                                              onsubmit="return confirm('¿Eliminar atractivo «{{ addslashes($atractivo->nombre) }}»? Esta acción es reversible (soft delete).')">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="_operador_id_test" value="{{ request('_operador_id_test', 1) }}">
                                            <button type="submit" title="Eliminar"
                                                    class="p-1.5 rounded-lg text-red-400 hover:text-red-600 hover:bg-red-50 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $atractivos->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
