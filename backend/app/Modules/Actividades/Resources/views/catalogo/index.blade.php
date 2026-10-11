@extends('layouts.app', ['title' => 'Actividades turísticas'])

@section('content')
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between mb-7">
            <div>
                <span class="tc-chip">Explora a tu ritmo</span>
                <h1 class="mt-3 text-3xl sm:text-4xl font-extrabold tracking-tight" style="color: var(--color-primary-700);">
                    Actividades turísticas
                </h1>
                <p class="mt-2 text-sm sm:text-base text-gray-500 max-w-2xl">
                    Encuentra experiencias activas según el destino, tu presupuesto y el tiempo disponible.
                </p>
            </div>
            <p class="text-sm text-gray-500 shrink-0">
                {{ $actividades->total() }} {{ $actividades->total() === 1 ? 'actividad disponible' : 'actividades disponibles' }}
            </p>
        </div>

        <div class="flex flex-col gap-6 lg:flex-row lg:items-start">
            <aside class="w-full shrink-0 lg:w-64" aria-label="Filtros de actividades">
                <form method="GET" action="{{ route('catalogo.actividades.index') }}" class="tc-card p-5">
                    <h2 class="mb-5 text-lg font-bold" style="font-family: var(--font-display); color: #1a2232;">Filtros</h2>
                    @if(isset($filtros['buscar']) && $filtros['buscar'] !== '')
                        <input type="hidden" name="buscar" value="{{ $filtros['buscar'] }}">
                    @endif
                    <div class="mb-5">
                        <label for="actividad-destino" class="block text-xs font-bold mb-2" style="color: var(--color-primary-700);">Destinos</label>
                        <select id="actividad-destino" name="destino_id" class="tc-select text-sm" onchange="this.form.requestSubmit()">
                            <option value="">Todos los destinos</option>
                            @foreach($destinos as $destino)
                                <option value="{{ $destino->id }}" @selected((string) ($filtros['destino_id'] ?? '') === (string) $destino->id)>{{ $destino->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-5">
                        <label for="actividad-costo-max" class="block text-xs font-bold mb-2" style="color: var(--color-primary-700);">Costo máximo (S/)</label>
                        <input id="actividad-costo-max" type="number" name="costo_max" min="0" step="0.01" value="{{ $filtros['costo_max'] ?? '' }}" class="tc-input text-sm" placeholder="Ej. 100">
                    </div>
                    <div class="mb-6">
                        <label for="actividad-duracion-max" class="block text-xs font-bold mb-2" style="color: var(--color-primary-700);">Duración máxima (min)</label>
                        <input id="actividad-duracion-max" type="number" name="duracion_max" min="1" max="10080" value="{{ $filtros['duracion_max'] ?? '' }}" class="tc-input text-sm" placeholder="Ej. 180">
                    </div>
                    <button type="submit" class="tc-btn-primary w-full justify-center">Aplicar filtros</button>
                    <a href="{{ route('catalogo.actividades.index') }}" class="mt-3 block w-full rounded-lg border py-2 text-center text-sm font-semibold text-gray-500 hover:bg-gray-50">Limpiar filtros</a>
                </form>
            </aside>
            <div class="min-w-0 flex-1">
                <form method="GET" action="{{ route('catalogo.actividades.index') }}" class="mb-6 flex gap-2">
                    @foreach(['destino_id', 'costo_max', 'duracion_max'] as $campo)
                        @if(isset($filtros[$campo]) && $filtros[$campo] !== '')
                            <input type="hidden" name="{{ $campo }}" value="{{ $filtros[$campo] }}">
                        @endif
                    @endforeach
                    <input name="buscar" value="{{ $filtros['buscar'] ?? '' }}" class="tc-input min-w-0 flex-1" maxlength="100" aria-label="Buscar actividades" placeholder="Buscar actividades...">
                    <button type="submit" class="tc-btn-primary">Buscar</button>
                </form>
                @if($errors->any())
                    <div class="tc-alert-error mb-5" role="alert">{{ $errors->first() }}</div>
                @endif

        <p class="mb-5 text-xs sm:text-sm text-gray-500">
            Los costos mostrados son estimados y pueden variar según la fecha, disponibilidad y condiciones del proveedor.
        </p>

        @if($actividades->isEmpty())
            <div class="tc-card p-8 sm:p-12 text-center">
                <div class="w-14 h-14 mx-auto rounded-full flex items-center justify-center" style="background: var(--color-teal-light); color: var(--color-primary-500);">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M8 12h8m-4-4v8m8 3a2 2 0 01-2 2H6a2 2 0 01-2-2V5a2 2 0 012-2h7l5 5v11z" />
                    </svg>
                </div>
                <h2 class="mt-4 text-lg font-bold text-gray-800">No encontramos actividades con esos filtros</h2>
                <p class="mt-2 text-sm text-gray-500">Prueba con otro destino o elimina algunos filtros.</p>
                <a href="{{ route('catalogo.actividades.index') }}" class="tc-btn-outline mt-5">Ver todas las actividades</a>
            </div>
        @else
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                @foreach($actividades as $actividad)
                    @php
                        $portadaUrl = $actividad->portada_url;
                        $minutos = $actividad->duracion_estimada_min;
                        $horas = $minutos ? intdiv($minutos, 60) : 0;
                        $restoMinutos = $minutos ? $minutos % 60 : 0;
                        $esFotoPlazaDemo = $actividad->imagen_portada === 'actividades-rutas-demo/cusco-plaza.jpg';
                        $esFotoSacsayhuamanDemo = $actividad->imagen_portada === 'actividades-rutas-demo/cusco-sacsayhuaman.jpg';
                    @endphp
                    <article class="tc-card overflow-hidden flex flex-col">
                        <a href="{{ route('catalogo.actividades.show', $actividad) }}" class="block h-48 overflow-hidden" aria-label="Ver {{ $actividad->nombre }}">
                            @if($portadaUrl)
                                <img src="{{ $portadaUrl }}" alt="{{ $actividad->nombre }}" class="w-full h-full object-cover transition-transform duration-300 hover:scale-105">
                            @else
                                <div class="w-full h-full flex items-center justify-center" style="background: linear-gradient(135deg, var(--color-teal-light), #c8e8eb); color: var(--color-primary-500);">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 2a4 4 0 014 4v1.1a7 7 0 014.9 6.65V16a2 2 0 01-2 2H5.1a2 2 0 01-2-2v-2.25A7 7 0 018 7.1V6a4 4 0 014-4zM9 22h6" />
                                    </svg>
                                </div>
                            @endif
                        </a>
                        @if($portadaUrl && $esFotoPlazaDemo)
                            <p class="px-4 py-1.5 text-[0.65rem] text-gray-500 border-b border-gray-100">
                                Foto de contexto: <a href="https://commons.wikimedia.org/wiki/File:The_Plaza_de_Armas_of_Cusco.jpg" target="_blank" rel="noopener noreferrer" class="underline">Entropy1963</a>, dominio público.
                            </p>
                        @elseif($portadaUrl && $esFotoSacsayhuamanDemo)
                            <p class="px-4 py-1.5 text-[0.65rem] text-gray-500 border-b border-gray-100">
                                Foto de contexto: <a href="https://commons.wikimedia.org/wiki/File:Sacsayhuaman_in_Cusco.jpg" target="_blank" rel="noopener noreferrer" class="underline">Filipe Fortes</a>, <a href="https://creativecommons.org/licenses/by-sa/2.0/" target="_blank" rel="license noopener noreferrer" class="underline">CC BY-SA 2.0</a>.
                            </p>
                        @endif
                        <div class="p-5 flex-1 flex flex-col">
                            <div class="flex items-start justify-between gap-3">
                                <p class="text-xs font-semibold uppercase tracking-wide" style="color: var(--color-primary-600);">
                                    {{ $actividad->destino->nombre }}
                                </p>
                                @if($actividad->ubicacion_url)
                                    <a href="{{ $actividad->ubicacion_url }}" target="_blank" rel="noopener noreferrer" class="shrink-0 text-xs font-semibold hover:underline" style="color: var(--color-primary-500);">Ver mapa ↗</a>
                                @endif
                            </div>
                            <h2 class="mt-2 text-xl font-bold text-gray-900 leading-tight">
                                <a href="{{ route('catalogo.actividades.show', $actividad) }}" class="hover:underline">{{ $actividad->nombre }}</a>
                            </h2>
                            @if($actividad->descripcion)
                                <p class="mt-2 text-sm text-gray-500 leading-relaxed line-clamp-3">{{ $actividad->descripcion }}</p>
                            @endif
                            <div class="mt-5 pt-4 border-t border-gray-100 flex flex-wrap gap-x-5 gap-y-2 text-sm">
                                <span class="font-bold" style="color: {{ $actividad->costo > 0 ? 'var(--color-primary-700)' : '#15803d' }};">
                                    {{ $actividad->costo > 0 ? 'S/ '.number_format((float) $actividad->costo, 2) : 'Gratuita' }}
                                </span>
                                @if($minutos)
                                    <span class="text-gray-500">
                                        {{ $horas > 0 ? $horas.' h' : '' }}{{ $horas > 0 && $restoMinutos > 0 ? ' ' : '' }}{{ $restoMinutos > 0 ? $restoMinutos.' min' : '' }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $actividades->links() }}
            </div>
        @endif
            </div>
        </div>
    </main>
@endsection
