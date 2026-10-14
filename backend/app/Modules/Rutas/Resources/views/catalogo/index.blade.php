@extends('layouts.app', ['title' => 'Rutas turísticas'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between mb-7">
        <div>
            <span class="tc-chip">Descubre el recorrido</span>
            <h1 class="mt-3 text-3xl sm:text-4xl font-extrabold text-primary-700">Rutas turísticas</h1>
            <p class="mt-2 text-sm sm:text-base text-gray-500">Explora recorridos con atractivos disponibles y paradas ordenadas.</p>
        </div>
        <p class="text-sm text-gray-500">{{ $rutas->total() }} {{ $rutas->total() === 1 ? 'ruta disponible' : 'rutas disponibles' }}</p>
    </div>
    @if($errors->any())
        <div class="tc-alert-error mb-4" role="alert">{{ $errors->first() }}</div>
    @endif
    <div class="flex flex-col gap-6 lg:flex-row lg:items-start">
        <aside class="w-full shrink-0 lg:w-64" aria-label="Filtros de rutas">
            <form method="GET" action="{{ route('catalogo.rutas.index') }}" class="tc-card p-5">
                <h2 class="mb-5 text-lg font-bold" style="font-family: var(--font-display); color: #1a2232;">Filtros</h2>
                @if(isset($filtros['buscar']) && $filtros['buscar'] !== '')
                    <input type="hidden" name="buscar" value="{{ $filtros['buscar'] }}">
                @endif
                <div class="mb-5">
                    <label for="ruta-destino" class="block text-xs font-bold mb-2" style="color: var(--color-primary-700);">Destinos</label>
                    <select id="ruta-destino" name="destino_id" class="tc-select text-sm" onchange="this.form.requestSubmit()">
                        <option value="">Todos los destinos</option>
                        @foreach($destinos as $destino)
                            <option value="{{ $destino->id }}" @selected((string) ($filtros['destino_id'] ?? '') === (string) $destino->id)>{{ $destino->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-5">
                    <label for="ruta-duracion-max" class="block text-xs font-bold mb-2" style="color: var(--color-primary-700);">Duración máxima (min)</label>
                    <input id="ruta-duracion-max" type="number" name="duracion_max" min="1" max="10080" value="{{ $filtros['duracion_max'] ?? '' }}" class="tc-input text-sm" placeholder="Ej. 240">
                </div>
                <div class="mb-6">
                    <label for="ruta-distancia-max" class="block text-xs font-bold mb-2" style="color: var(--color-primary-700);">Distancia máxima (km)</label>
                    <input id="ruta-distancia-max" type="number" name="distancia_max" min="0" max="99999999.99" step="0.01" value="{{ $filtros['distancia_max'] ?? '' }}" class="tc-input text-sm" placeholder="Ej. 15">
                </div>
                <button type="submit" class="tc-btn-primary w-full justify-center">Aplicar filtros</button>
                <a href="{{ route('catalogo.rutas.index') }}" class="mt-3 block w-full rounded-lg border py-2 text-center text-sm font-semibold text-gray-500 hover:bg-gray-50">Limpiar filtros</a>
            </form>
        </aside>
        <div class="min-w-0 flex-1">
            <form method="GET" action="{{ route('catalogo.rutas.index') }}" class="mb-6 flex gap-2">
                @foreach(['destino_id', 'duracion_max', 'distancia_max'] as $campo)
                    @if(isset($filtros[$campo]) && $filtros[$campo] !== '')
                        <input type="hidden" name="{{ $campo }}" value="{{ $filtros[$campo] }}">
                    @endif
                @endforeach
                <input name="buscar" maxlength="100" value="{{ $filtros['buscar'] ?? '' }}" class="tc-input min-w-0 flex-1" aria-label="Buscar rutas" placeholder="Buscar rutas...">
                <button type="submit" class="tc-btn-primary">Buscar</button>
            </form>
            <p class="text-xs text-gray-500 mb-5">Distancias y tiempos referenciales registrados por el operador. No son cálculos automáticos ni cotizaciones.</p>
    @if($rutas->isEmpty())
        <div class="tc-card p-8 text-center">
            <h2 class="text-lg font-bold text-gray-800">No encontramos rutas con esos filtros</h2>
            <p class="mt-2 text-sm text-gray-500">Prueba otro destino o amplía la duración y la distancia.</p>
            <a href="{{ route('catalogo.rutas.index') }}" class="tc-btn-outline mt-5">Ver todas las rutas</a>
        </div>
    @else
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            @foreach($rutas as $ruta)
                <article class="tc-card overflow-hidden flex flex-col">
                    <a href="{{ route('catalogo.rutas.show', $ruta) }}" class="block h-48 overflow-hidden" aria-label="Ver {{ $ruta->nombre }}">
                        @include('rutas::partials.portada', ['ruta' => $ruta])
                    </a>
                    @include('rutas::partials.credito', ['ruta' => $ruta])
                    <div class="p-5 flex-1 flex flex-col">
                        <p class="text-xs font-semibold uppercase tracking-wide text-primary-600">{{ $ruta->destino->nombre }}</p>
                        <h2 class="mt-2 text-xl font-bold text-gray-900"><a href="{{ route('catalogo.rutas.show', $ruta) }}" class="hover:underline">{{ $ruta->nombre }}</a></h2>
                        <p class="mt-2 text-sm text-gray-500 line-clamp-3">{{ $ruta->descripcion }}</p>
                        <div class="mt-5 pt-4 border-t border-gray-100 flex flex-wrap gap-x-4 gap-y-2 text-sm text-gray-600">
                            <span>{{ $ruta->paradas_count }} paradas</span>
                            <span>{{ $ruta->distancia_km !== null ? $ruta->distancia_km.' km' : 'Distancia no indicada' }}</span>
                            <span>{{ $ruta->duracion_estimada_min !== null ? $ruta->duracion_estimada_min.' min' : 'Duración no indicada' }}</span>
                        </div>
                        <a href="{{ route('catalogo.rutas.show', $ruta) }}" class="mt-4 font-semibold text-primary-700 hover:underline">Ver recorrido →</a>
                    </div>
                </article>
            @endforeach
        </div>
        <div class="mt-8">{{ $rutas->links() }}</div>
    @endif
        </div>
    </div>
</div>
@endsection
