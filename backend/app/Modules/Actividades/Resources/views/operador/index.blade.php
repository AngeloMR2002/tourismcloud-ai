@extends('layouts.app', ['title' => 'Mis actividades'])

@section('content')
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between mb-7">
            <div>
                <span class="tc-chip">Panel de operador</span>
                <h1 class="mt-3 text-3xl font-extrabold tracking-tight" style="color: var(--color-primary-700);">Mis actividades</h1>
                <p class="mt-2 text-sm text-gray-500">Administra las experiencias disponibles en tus destinos.</p>
            </div>
            <a href="{{ route('operador.actividades.create') }}" class="tc-btn-primary justify-center">+ Nueva actividad</a>
        </div>

        @if(session('exito'))
            <div class="tc-alert-exito mb-6" role="status">{{ session('exito') }}</div>
        @endif

        <form method="GET" action="{{ route('operador.actividades.index') }}" class="tc-card p-4 sm:p-5 mb-6">
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
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
            <div class="flex flex-wrap gap-3 mt-4">
                <button type="submit" class="tc-btn-primary">Filtrar</button>
                <a href="{{ route('operador.actividades.index') }}" class="tc-btn-outline">Limpiar</a>
            </div>
        </form>

        @if($actividades->isEmpty())
            <div class="tc-card p-8 sm:p-12 text-center">
                <h2 class="text-lg font-bold text-gray-800">Aún no tienes actividades registradas</h2>
                <p class="mt-2 text-sm text-gray-500">Crea una actividad para que pueda aparecer en el catálogo turístico.</p>
                <a href="{{ route('operador.actividades.create') }}" class="tc-btn-primary mt-5">Crear primera actividad</a>
            </div>
        @else
            <div class="tc-card overflow-hidden">
                <div class="hidden md:block overflow-x-auto">
                    <table class="tc-table min-w-full">
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
                                    <td><span class="{{ $actividad->estado === 'activo' ? 'tc-badge-activo' : 'tc-badge-inactivo' }}">{{ ucfirst($actividad->estado) }}</span></td>
                                    <td>
                                        <div class="flex justify-end gap-2">
                                            <form method="POST" action="{{ route('operador.actividades.toggle-estado', $actividad) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-xs font-semibold hover:underline" style="color: var(--color-primary-600);">
                                                    {{ $actividad->estado === 'activo' ? 'Desactivar' : 'Activar' }}
                                                </button>
                                            </form>
                                            <a href="{{ route('operador.actividades.edit', $actividad) }}" class="text-xs font-semibold text-gray-600 hover:underline">Editar</a>
                                            <form method="POST" action="{{ route('operador.actividades.destroy', $actividad) }}" onsubmit="return confirm('¿Eliminar esta actividad?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs font-semibold text-red-600 hover:underline">Eliminar</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="md:hidden divide-y divide-gray-100">
                    @foreach($actividades as $actividad)
                        <article class="p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h2 class="font-bold text-gray-800">{{ $actividad->nombre }}</h2>
                                    <p class="mt-1 text-sm text-gray-500">{{ $actividad->destino->nombre }} · {{ $actividad->costo > 0 ? 'S/ '.number_format((float) $actividad->costo, 2) : 'Gratuita' }}</p>
                                </div>
                                <span class="{{ $actividad->estado === 'activo' ? 'tc-badge-activo' : 'tc-badge-inactivo' }}">{{ ucfirst($actividad->estado) }}</span>
                            </div>
                            <div class="flex flex-wrap gap-x-4 gap-y-2 mt-4 text-sm">
                                <a href="{{ route('operador.actividades.edit', $actividad) }}" class="font-semibold hover:underline" style="color: var(--color-primary-600);">Editar</a>
                                <form method="POST" action="{{ route('operador.actividades.toggle-estado', $actividad) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="font-semibold text-gray-600 hover:underline">{{ $actividad->estado === 'activo' ? 'Desactivar' : 'Activar' }}</button>
                                </form>
                                <form method="POST" action="{{ route('operador.actividades.destroy', $actividad) }}" onsubmit="return confirm('¿Eliminar esta actividad?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="font-semibold text-red-600 hover:underline">Eliminar</button>
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
