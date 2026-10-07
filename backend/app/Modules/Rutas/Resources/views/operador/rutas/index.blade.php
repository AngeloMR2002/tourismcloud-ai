@extends('actividades::layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Encabezado del Panel --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold uppercase tracking-wider px-2.5 py-1 rounded bg-teal-100 text-teal-800">
                    Panel Operador Turístico
                </span>
                <span class="text-xs text-gray-500">• Módulo P3</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 mt-1 font-display">
                Gestión de Rutas & Circuitos
            </h1>
            <p class="text-xs sm:text-sm text-gray-500">
                Diseña itinerarios conectados paso a paso, gestiona paradas secuenciales y optimiza traslados.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('operador.actividades.index') }}" class="tc-btn-outline text-xs">
                Ver Mis Actividades
            </a>
            <a href="{{ route('operador.rutas.create') }}" class="tc-btn-primary text-xs shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nueva Ruta / Circuito
            </a>
        </div>
    </div>

    {{-- Filtro Rápido --}}
    <div class="bg-white rounded-xl p-4 mb-6 shadow-sm border border-gray-100 flex flex-wrap items-center justify-between gap-4">
        <form method="GET" action="{{ route('operador.rutas.index') }}" class="flex flex-wrap items-center gap-3 flex-1">
            <input type="text" name="buscar" value="{{ $filtros['buscar'] ?? '' }}"
                   placeholder="Buscar por nombre..."
                   class="tc-input text-xs py-2 w-48 sm:w-64">

            <select name="destino_id" class="tc-select text-xs py-2 w-44">
                <option value="">Todos los destinos</option>
                @foreach($destinos as $destino)
                    <option value="{{ $destino->id }}" {{ ($filtros['destino_id'] ?? '') == $destino->id ? 'selected' : '' }}>
                        {{ $destino->nombre }}
                    </option>
                @endforeach
            </select>

            <select name="estado" class="tc-select text-xs py-2 w-36">
                <option value="">Cualquier estado</option>
                <option value="activo" {{ ($filtros['estado'] ?? '') === 'activo' ? 'selected' : '' }}>Solo Activas</option>
                <option value="inactivo" {{ ($filtros['estado'] ?? '') === 'inactivo' ? 'selected' : '' }}>Solo Inactivas</option>
            </select>

            <button type="submit" class="tc-btn-outline text-xs py-2">
                Filtrar
            </button>
            @if(!empty(array_filter($filtros ?? [])))
                <a href="{{ route('operador.rutas.index') }}" class="text-xs text-gray-500 hover:text-gray-700">Limpiar</a>
            @endif
        </form>

        <span class="text-xs font-semibold text-gray-500">
            Total: <strong>{{ $rutas->total() }}</strong> circuitos
        </span>
    </div>

    {{-- Tabla de Gestión --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        @if($rutas->isEmpty())
            <div class="p-12 text-center">
                <div class="w-12 h-12 rounded-full bg-teal-50 text-teal-600 mx-auto flex items-center justify-center mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                </div>
                <h3 class="text-base font-bold text-gray-800 mb-1">No tienes rutas registradas</h3>
                <p class="text-xs text-gray-500 mb-4">Diseña un nuevo circuito con paradas conectadas paso a paso.</p>
                <a href="{{ route('operador.rutas.create') }}" class="tc-btn-primary text-xs">
                    Crear primera ruta
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="tc-table">
                    <thead>
                        <tr>
                            <th>Nombre del Circuito</th>
                            <th>Destino</th>
                            <th>Tipo / Transporte</th>
                            <th>Distancia & Tiempo</th>
                            <th>Paradas</th>
                            <th>Dificultad</th>
                            <th>Estado</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rutas as $ruta)
                            <tr>
                                <td>
                                    <div class="font-bold text-gray-900 text-sm hover:text-teal-700">
                                        <a href="{{ route('catalogo.rutas.show', $ruta->id) }}" target="_blank">
                                            {{ $ruta->nombre }}
                                        </a>
                                    </div>
                                    <span class="text-[11px] text-gray-500">
                                        {{ Str::substr($ruta->descripcion, 0, 60) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-xs font-semibold text-gray-800">{{ $ruta->destino->nombre ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <span class="tc-chip block w-fit mb-0.5">{{ ucfirst($ruta->tipo_ruta) }}</span>
                                    <span class="text-[11px] text-gray-500">{{ $ruta->transporte_texto }}</span>
                                </td>
                                <td>
                                    <div class="text-xs font-semibold text-gray-900">
                                        {{ $ruta->distancia_km !== null ? $ruta->distancia_km.' km' : 'Por confirmar' }}
                                    </div>
                                    <span class="text-[11px] text-gray-500">{{ $ruta->duracion_estimada_horas !== null ? $ruta->duracion_estimada_horas.' horas' : 'Duración por confirmar' }} · Revisión: {{ $ruta->estado_verificacion }}</span>
                                </td>
                                <td>
                                    <span class="inline-flex items-center gap-1 text-xs font-bold px-2 py-0.5 bg-teal-50 text-teal-800 rounded border border-teal-200">
                                        {{ $ruta->puntos->count() }} paradas
                                    </span>
                                </td>
                                <td>
                                    <span class="text-[11px] font-bold px-2 py-0.5 rounded-full border {{ $ruta->badge_dificultad_color }}">
                                        {{ $ruta->nivel_dificultad ? ucfirst($ruta->nivel_dificultad) : 'Por confirmar' }}
                                    </span>
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('operador.rutas.toggle-estado', $ruta->id) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                title="Haz clic para cambiar estado"
                                                class="cursor-pointer transition-opacity hover:opacity-80 {{ $ruta->estado === 'activo' ? 'tc-badge-activo' : 'tc-badge-inactivo' }}">
                                            ● {{ ucfirst($ruta->estado) }}
                                        </button>
                                    </form>
                                </td>
                                <td class="text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        {{-- Ver en catálogo --}}
                                        <a href="{{ route('catalogo.rutas.show', $ruta->id) }}"
                                           target="_blank"
                                           class="p-1.5 text-gray-400 hover:text-teal-600 rounded" title="Ver en catálogo">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>

                                        {{-- Editar --}}
                                        <a href="{{ route('operador.rutas.edit', $ruta->id) }}"
                                           class="p-1.5 text-gray-500 hover:text-blue-600 rounded" title="Editar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>

                                        {{-- Eliminar --}}
                                        <form method="POST" action="{{ route('operador.rutas.destroy', $ruta->id) }}"
                                              onsubmit="return confirm('¿Estás seguro de eliminar esta ruta turística y sus paradas?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-gray-400 hover:text-rose-600 rounded" title="Eliminar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-gray-100">
                {{ $rutas->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
