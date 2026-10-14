@extends('layouts.app', ['title' => 'Mis rutas'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between mb-7">
        <div>
            <span class="tc-chip">Panel de operador</span>
            <h1 class="mt-3 text-3xl font-extrabold text-primary-700">Mis rutas</h1>
            <p class="mt-2 text-sm text-gray-500">Administra recorridos y ordena las paradas de tus destinos.</p>
        </div>
        <a href="{{ route('operador.rutas.create') }}" class="tc-btn-primary justify-center">+ Nueva ruta</a>
    </div>
    @if($errors->any())
        <div class="tc-alert-error mb-4" role="alert">{{ $errors->first() }}</div>
    @endif
    <form method="GET" action="{{ route('operador.rutas.index') }}" class="tc-card p-4 sm:p-5 mb-6">
        <div class="grid gap-3 sm:grid-cols-3">
            <div>
                <label for="buscar" class="block text-xs font-semibold text-gray-600 mb-1.5">Buscar</label>
                <input id="buscar" name="buscar" maxlength="100" value="{{ $filtros['buscar'] ?? '' }}" class="tc-input" placeholder="Nombre o descripción">
            </div>
            <div>
                <label for="destino_id" class="block text-xs font-semibold text-gray-600 mb-1.5">Destino</label>
                <select id="destino_id" name="destino_id" class="tc-select">
                    <option value="">Todos mis destinos</option>
                    @foreach($destinos as $destino)
                        <option value="{{ $destino->id }}" @selected((string) ($filtros['destino_id'] ?? '') === (string) $destino->id)>{{ $destino->nombre }}{{ $destino->estado !== 'activo' ? ' (inactivo)' : '' }}</option>
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
            <a href="{{ route('operador.rutas.index') }}" class="tc-btn-outline">Limpiar</a>
        </div>
    </form>
    @if($rutas->isEmpty())
        <div class="tc-card p-8 text-center">
            <h2 class="text-lg font-bold text-gray-800">No hay rutas para mostrar</h2>
            <p class="mt-2 text-sm text-gray-500">Crea tu primer recorrido o ajusta los filtros.</p>
            <a href="{{ route('operador.rutas.create') }}" class="tc-btn-primary mt-5">Crear ruta</a>
        </div>
    @else
        <div class="grid gap-4 md:grid-cols-2">
            @foreach($rutas as $ruta)
                <article class="tc-card p-5">
                    <div class="flex gap-3 items-start">
                        <div class="w-20 h-20 shrink-0 overflow-hidden rounded-lg">@include('rutas::partials.portada', ['ruta' => $ruta])</div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold text-primary-600">{{ $ruta->destino->nombre }}</p>
                            <h2 class="mt-1 font-bold text-lg break-words">{{ $ruta->nombre }}</h2>
                            <span class="{{ $ruta->estado === 'activo' ? 'tc-badge-activo' : 'tc-badge-inactivo' }}">{{ ucfirst($ruta->estado) }}</span>
                        </div>
                    </div>
                    @include('rutas::partials.credito', ['ruta' => $ruta])
                    <p class="mt-4 text-sm text-gray-500">{{ $ruta->paradas_count }} paradas · {{ $ruta->distancia_km !== null ? $ruta->distancia_km.' km' : 'Sin distancia' }} · {{ $ruta->duracion_estimada_min !== null ? $ruta->duracion_estimada_min.' min' : 'Sin duración' }}</p>
                    <p class="mt-2 text-xs text-gray-500">Solo se publica si la ruta y su destino están activos y todas las paradas siguen disponibles.</p>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <a href="{{ route('operador.rutas.edit', $ruta) }}" class="tc-btn-outline">Editar</a>
                        <form method="POST" action="{{ route('operador.rutas.toggle-estado', $ruta) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="tc-btn-outline">{{ $ruta->estado === 'activo' ? 'Desactivar' : 'Activar' }}</button>
                        </form>
                        <form method="POST" action="{{ route('operador.rutas.destroy', $ruta) }}" onsubmit="return confirm('¿Eliminar definitivamente esta ruta y sus paradas? Esta acción no se puede deshacer.');">
                            @csrf @method('DELETE')
                            <button type="submit" class="tc-btn-outline text-red-700">Eliminar</button>
                        </form>
                    </div>
                </article>
            @endforeach
        </div>
        <div class="mt-8">{{ $rutas->links() }}</div>
    @endif
</div>
@endsection
