@extends('layouts.panel')

@section('title', 'Mis Atractivos')

@section('panel-rol-label', 'Operador')

@section('panel-sidebar-links')
    <a href="{{ route('operador.atractivos.index') }}?_operador_id_test={{ request('_operador_id_test', 1) }}"
       class="group/sidebar flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 mb-0.5 text-white shadow-sm"
       style="background: var(--color-primary-500);">
        <div class="shrink-0 transition-transform duration-150 group-hover/sidebar:scale-110">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
            </svg>
        </div>
        <span class="whitespace-pre group-hover/sidebar:translate-x-1 transition-transform duration-150">Mis Atractivos</span>
    </a>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-6">
        <div>
            <h1 style="font-family: var(--font-display); font-size: 1.75rem; font-weight: 800; color: #1a2232;">
                Mis Atractivos
            </h1>
            <p class="text-sm text-gray-500 mt-0.5">Gestiona los atractivos turísticos que has registrado en la plataforma</p>
        </div>
        <a href="{{ route('operador.atractivos.create') }}?_operador_id_test={{ request('_operador_id_test', 1) }}"
           class="tc-btn-primary shrink-0">
            + Nuevo Atractivo
        </a>
    </div>

    {{-- Barra de filtros inline (Diseño actualizado según wireframe) --}}
    <div class="mb-6">
        <form method="GET" action="{{ route('operador.atractivos.index') }}"
              class="flex flex-col sm:flex-row items-center justify-between gap-4 p-2 bg-white rounded-xl border border-gray-200"
              style="box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <input type="hidden" name="_operador_id_test" value="{{ request('_operador_id_test', 1) }}">

            {{-- Buscar (Izquierda) --}}
            <div class="flex items-center gap-2 px-3 w-full sm:w-80">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="busqueda" value="{{ request('busqueda') }}"
                       placeholder="Buscar atractivo..."
                       class="flex-1 text-sm outline-none bg-transparent py-1.5 text-gray-700 placeholder-gray-400"
                       onkeypress="if(event.key === 'Enter') this.form.submit();">
            </div>

            {{-- Selects (Derecha) --}}
            <div class="flex items-center gap-3 w-full sm:w-auto px-1 sm:px-0">
                {{-- Estado --}}
                <select name="estado" class="tc-select text-sm h-9 py-1 pr-8 pl-3" style="min-width: 160px;" onchange="this.form.submit()">
                    <option value="">Todos los estados</option>
                    <option value="activo"   @selected(request('estado') === 'activo')>Activos</option>
                    <option value="inactivo" @selected(request('estado') === 'inactivo')>Inactivos</option>
                </select>

                {{-- Destino --}}
                <select name="destino_id" class="tc-select text-sm h-9 py-1 pr-8 pl-3" style="min-width: 170px;" onchange="this.form.submit()">
                    <option value="">Todos los destinos</option>
                    @foreach($destinos as $d)
                        <option value="{{ $d->id }}" @selected(request('destino_id') == $d->id)>{{ $d->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    {{-- Tabla --}}
    <div class="tc-card overflow-hidden">
        @if($atractivos->isEmpty())
            <div class="text-center py-16">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
                <p class="text-gray-500 font-medium mb-3">No tienes atractivos registrados aún.</p>
                <a href="{{ route('operador.atractivos.create') }}?_operador_id_test={{ request('_operador_id_test', 1) }}"
                   class="tc-btn-primary text-sm">+ Nuevo Atractivo</a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="tc-table">
                    <thead>
                        <tr>
                            <th>Atractivo</th>
                            <th>Destino</th>
                            <th>Categoría</th>
                            <th class="text-right">Costo</th>
                            <th class="text-center">Duración</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($atractivos as $at)
                            <tr>
                                {{-- Atractivo --}}
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg overflow-hidden shrink-0"
                                             style="background: var(--color-teal-light);">
                                            @if($at->imagen_portada)
                                                <img src="{{ asset('storage/' . $at->imagen_portada) }}"
                                                     class="w-full h-full object-cover" alt="{{ $at->nombre }}">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <svg class="w-5 h-5" style="color: var(--color-primary-400);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <span class="font-semibold text-gray-800 text-sm">{{ $at->nombre }}</span>
                                    </div>
                                </td>

                                {{-- Destino --}}
                                <td class="text-sm text-gray-500">
                                    {{ $at->destino?->nombre ?? '—' }}
                                </td>

                                {{-- Categoría --}}
                                <td>
                                    <div class="flex flex-wrap gap-1">
                                        @forelse($at->categorias->take(2) as $cat)
                                            <span class="tc-chip">{{ $cat->nombre }}</span>
                                        @empty
                                            <span class="text-gray-400 text-xs">—</span>
                                        @endforelse
                                    </div>
                                </td>

                                {{-- Costo --}}
                                <td class="text-right text-sm font-semibold" style="color: #1a2232;">
                                    @if($at->costo_entrada > 0)
                                        S/ {{ number_format($at->costo_entrada, 2) }}
                                    @else
                                        <span class="text-green-600 font-medium text-xs">Gratuito</span>
                                    @endif
                                </td>

                                {{-- Duración --}}
                                <td class="text-center text-sm text-gray-500">
                                    @if($at->duracion_estimada_min)
                                        @php $h = intdiv($at->duracion_estimada_min, 60); $m = $at->duracion_estimada_min % 60; @endphp
                                        {{ $h > 0 ? "{$h}h " : '' }}{{ $m > 0 ? "{$m}m" : '' }}
                                    @else
                                        —
                                    @endif
                                </td>

                                {{-- Toggle estado --}}
                                <td class="text-center">
                                    <form method="POST"
                                          action="{{ route('operador.atractivos.toggle-estado', $at) }}"
                                          id="tog-at-{{ $at->id }}">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="_operador_id_test" value="{{ request('_operador_id_test', 1) }}">
                                        <label class="tc-toggle">
                                            <input type="checkbox"
                                                   {{ $at->estado === 'activo' ? 'checked' : '' }}
                                                   onchange="document.getElementById('tog-at-{{ $at->id }}').submit()">
                                            <span class="tc-toggle-track"></span>
                                            <span class="tc-toggle-thumb"></span>
                                        </label>
                                    </form>
                                </td>

                                {{-- Acciones --}}
                                <td>
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a href="{{ route('catalogo.atractivos.show', $at) }}" target="_blank"
                                           title="Ver en catálogo"
                                           class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                        <a href="{{ route('operador.atractivos.edit', $at) }}?_operador_id_test={{ request('_operador_id_test', 1) }}"
                                           title="Editar"
                                           class="p-1.5 rounded-lg transition-colors"
                                           style="color: var(--color-primary-600);"
                                           onmouseover="this.style.background='var(--color-teal-light)'"
                                           onmouseout="this.style.background='transparent'">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <form method="POST" action="{{ route('operador.atractivos.destroy', $at) }}"
                                              onsubmit="return confirm('¿Eliminar «{{ addslashes($at->nombre) }}»?')">
                                            @csrf @method('DELETE')
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
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $atractivos->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
