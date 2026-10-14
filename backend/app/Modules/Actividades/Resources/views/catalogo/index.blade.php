@extends('layouts.app', ['title' => 'Actividades turísticas'])

@section('content')
    @php
        $destinoSeleccionado = ! empty($filtros['destino_id'])
            ? $destinos->firstWhere('id', (int) $filtros['destino_id'])
            : null;
        $chipsFiltros = [];

        if ($destinoSeleccionado) {
            $chipsFiltros[] = 'Destino: '.$destinoSeleccionado->nombre;
        }
        if (isset($filtros['costo_max']) && $filtros['costo_max'] !== '') {
            $chipsFiltros[] = 'Hasta S/ '.number_format((float) $filtros['costo_max'], 2);
        }
        if (! empty($filtros['duracion_max'])) {
            $chipsFiltros[] = 'Hasta '.$filtros['duracion_max'].' min';
        }
        if (! empty($filtros['buscar'])) {
            $chipsFiltros[] = '“'.$filtros['buscar'].'”';
        }
    @endphp

    <div class="tc-catalog-shell max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        <header class="tc-page-heading" aria-labelledby="actividades-titulo">
            <p class="tc-home-eyebrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><circle cx="12" cy="4" r="2" stroke-width="1.8"/><path d="m10 8 4 2-2 6-5 5m5-5 3 5M10 8l-2 4-3 2m9-4 3 3 3 1" stroke-width="1.8" stroke-linecap="round"/></svg><span>Explora · Qué hacer</span></p>
            <h1 id="actividades-titulo">Actividades</h1>
            <p>Encuentra experiencias según tu destino, presupuesto y tiempo disponible.</p>
        </header>

        <details class="tc-filter-panel mt-5 p-4 lg:hidden" aria-label="Filtros de actividades">
            <summary class="tc-filter-summary">
                <span class="inline-flex items-center gap-2 font-bold text-gray-800">
                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 4h18M6 12h12m-9 8h6"/></svg>
                    Ajustar filtros
                </span>
                @if(! empty($chipsFiltros))
                    <span class="tc-filter-chip">{{ count($chipsFiltros) }} activos</span>
                @endif
            </summary>
            <form method="GET" action="{{ route('catalogo.actividades.index') }}" class="mt-5 grid gap-4">
                @if(isset($filtros['buscar']) && $filtros['buscar'] !== '')
                    <input type="hidden" name="buscar" value="{{ $filtros['buscar'] }}">
                @endif
                <div>
                    <label for="actividad-destino-movil" class="block text-xs font-bold mb-2 text-primary-700">Destino</label>
                    <select id="actividad-destino-movil" name="destino_id" class="tc-select text-sm">
                        <option value="">Todos los destinos</option>
                        @foreach($destinos as $destino)
                            <option value="{{ $destino->id }}" @selected((string) ($filtros['destino_id'] ?? '') === (string) $destino->id)>{{ $destino->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="actividad-costo-max-movil" class="block text-xs font-bold mb-2 text-primary-700">Costo máx. (S/)</label>
                        <input id="actividad-costo-max-movil" type="number" name="costo_max" min="0" step="0.01" value="{{ $filtros['costo_max'] ?? '' }}" class="tc-input text-sm" placeholder="100">
                    </div>
                    <div>
                        <label for="actividad-duracion-max-movil" class="block text-xs font-bold mb-2 text-primary-700">Duración máx. (min)</label>
                        <input id="actividad-duracion-max-movil" type="number" name="duracion_max" min="1" max="10080" value="{{ $filtros['duracion_max'] ?? '' }}" class="tc-input text-sm" placeholder="180">
                    </div>
                </div>
                <button type="submit" class="tc-btn-primary w-full justify-center">Aplicar filtros</button>
            </form>
        </details>

        <div class="mt-6 flex flex-col gap-6 lg:flex-row lg:items-start">
            <aside class="hidden w-60 shrink-0 lg:block" aria-label="Filtros de actividades">
                <form method="GET" action="{{ route('catalogo.actividades.index') }}" class="tc-filter-panel tc-filter-panel--desktop p-5">
                    <div class="flex items-center justify-between gap-3 pb-4 border-b border-gray-100">
                        <h2 class="font-bold text-gray-900">Filtros</h2>
                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 4h18M6 12h12m-9 8h6"/></svg>
                    </div>
                    @if(isset($filtros['buscar']) && $filtros['buscar'] !== '')
                        <input type="hidden" name="buscar" value="{{ $filtros['buscar'] }}">
                    @endif
                    <div class="mt-5">
                        <label for="actividad-destino" class="block text-xs font-bold mb-2 text-primary-700">Destino</label>
                        <select id="actividad-destino" name="destino_id" class="tc-select text-sm">
                            <option value="">Todos los destinos</option>
                            @foreach($destinos as $destino)
                                <option value="{{ $destino->id }}" @selected((string) ($filtros['destino_id'] ?? '') === (string) $destino->id)>{{ $destino->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mt-5">
                        <label for="actividad-costo-max" class="block text-xs font-bold mb-2 text-primary-700">Costo máximo (S/)</label>
                        <input id="actividad-costo-max" type="number" name="costo_max" min="0" step="0.01" value="{{ $filtros['costo_max'] ?? '' }}" class="tc-input text-sm" placeholder="Ej. 100">
                    </div>
                    <div class="mt-5">
                        <label for="actividad-duracion-max" class="block text-xs font-bold mb-2 text-primary-700">Duración máxima (min)</label>
                        <input id="actividad-duracion-max" type="number" name="duracion_max" min="1" max="10080" value="{{ $filtros['duracion_max'] ?? '' }}" class="tc-input text-sm" placeholder="Ej. 180">
                    </div>
                    <button type="submit" class="tc-btn-primary mt-6 w-full justify-center">Aplicar filtros</button>
                    <a href="{{ route('catalogo.actividades.index') }}" class="mt-3 block w-full rounded-lg border border-gray-200 py-2 text-center text-sm font-semibold text-gray-600 transition hover:bg-gray-50">Limpiar filtros</a>
                </form>
            </aside>

            <section class="min-w-0 flex-1" aria-label="Resultados de actividades">
                <form method="GET" action="{{ route('catalogo.actividades.index') }}" class="mb-5 flex flex-col gap-2 sm:flex-row">
                    @foreach(['destino_id', 'costo_max', 'duracion_max'] as $campo)
                        @if(isset($filtros[$campo]) && $filtros[$campo] !== '')
                            <input type="hidden" name="{{ $campo }}" value="{{ $filtros[$campo] }}">
                        @endif
                    @endforeach
                    <div class="tc-search-input-wrap">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35m1.35-5.15a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z"/></svg>
                        <input type="search" name="buscar" value="{{ $filtros['buscar'] ?? '' }}" class="tc-input" maxlength="100" aria-label="Buscar actividades" placeholder="Buscar por nombre o descripción">
                    </div>
                    <button type="submit" class="tc-btn-primary justify-center">Buscar</button>
                </form>

                @if($errors->any())
                    <div class="tc-alert-error mb-5" role="alert">{{ $errors->first() }}</div>
                @endif

                <div class="tc-result-toolbar">
                    <div>
                        <p class="font-bold text-gray-900">{{ $actividades->total() }} {{ $actividades->total() === 1 ? 'resultado' : 'resultados' }}</p>
                        <p class="mt-1 text-xs sm:text-sm text-gray-500">Los costos y tiempos son referenciales y pueden variar según el proveedor.</p>
                    </div>
                    @if(! empty($chipsFiltros))
                        <div class="flex flex-wrap items-center gap-2">
                            @foreach($chipsFiltros as $chip)
                                <span class="tc-filter-chip">{{ $chip }}</span>
                            @endforeach
                            <a href="{{ route('catalogo.actividades.index') }}" class="text-xs font-bold text-primary-700 hover:underline">Limpiar</a>
                        </div>
                    @endif
                </div>

                @if($actividades->isEmpty())
                    <div class="tc-empty-state p-8 sm:p-12 text-center">
                        <div class="w-14 h-14 mx-auto rounded-2xl flex items-center justify-center bg-white text-primary-600 shadow-sm">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="m4 15 4-4 3 3 5-6 4 5M5 19h14"/></svg>
                        </div>
                        @if(! empty($chipsFiltros))
                            <h2 class="mt-4 text-xl font-bold text-gray-900">No encontramos coincidencias</h2>
                            <p class="mt-2 text-sm text-gray-500">Prueba otro destino o amplía el costo y la duración.</p>
                            <a href="{{ route('catalogo.actividades.index') }}" class="tc-btn-outline mt-5">Limpiar filtros</a>
                        @else
                            <h2 class="mt-4 text-xl font-bold text-gray-900">Todavía no hay actividades publicadas</h2>
                            <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-gray-500">Aquí aparecerán las experiencias disponibles cuando los operadores las publiquen.</p>
                            <a href="{{ route('catalogo.atractivos.index') }}" class="tc-btn-outline mt-5">Explorar atractivos</a>
                        @endif
                    </div>
                @else
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
                        @foreach($actividades as $actividad)
                            @php
                                $portadaUrl = $actividad->portada_url;
                                $minutos = $actividad->duracion_estimada_min;
                                $horas = $minutos ? intdiv($minutos, 60) : 0;
                                $restoMinutos = $minutos ? $minutos % 60 : 0;
                                $duracion = $minutos
                                    ? (($horas > 0 ? $horas.' h' : '').($horas > 0 && $restoMinutos > 0 ? ' ' : '').($restoMinutos > 0 ? $restoMinutos.' min' : ''))
                                    : 'Por confirmar';
                                $esFotoPlazaDemo = $actividad->imagen_portada === 'actividades-rutas-demo/cusco-plaza.jpg';
                                $esFotoSacsayhuamanDemo = $actividad->imagen_portada === 'actividades-rutas-demo/cusco-sacsayhuaman.jpg';
                            @endphp
                            <article class="tc-card tc-card-interactive tc-experience-card">
                                <a href="{{ route('catalogo.actividades.show', $actividad) }}" class="tc-experience-card__media" aria-label="Ver {{ $actividad->nombre }}">
                                    @if($portadaUrl)
                                        <img src="{{ $portadaUrl }}" alt="{{ $actividad->nombre }}" loading="lazy">
                                    @else
                                        <div class="tc-cover-fallback" aria-hidden="true">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.35" d="M12 2a4 4 0 0 1 4 4v1.1a7 7 0 0 1 4.9 6.65V16a2 2 0 0 1-2 2H5.1a2 2 0 0 1-2-2v-2.25A7 7 0 0 1 8 7.1V6a4 4 0 0 1 4-4Z M9 22h6"/></svg>
                                        </div>
                                    @endif
                                    <span class="tc-media-badge">{{ $actividad->destino->nombre }}</span>
                                </a>
                                @if($portadaUrl && $esFotoPlazaDemo)
                                    <p class="px-5 py-2 text-[0.65rem] text-gray-500 border-b border-gray-100">Foto de contexto: <a href="https://commons.wikimedia.org/wiki/File:The_Plaza_de_Armas_of_Cusco.jpg" target="_blank" rel="noopener noreferrer" class="underline">Entropy1963</a>, dominio público.</p>
                                @elseif($portadaUrl && $esFotoSacsayhuamanDemo)
                                    <p class="px-5 py-2 text-[0.65rem] text-gray-500 border-b border-gray-100">Foto de contexto: <a href="https://commons.wikimedia.org/wiki/File:Sacsayhuaman_in_Cusco.jpg" target="_blank" rel="noopener noreferrer" class="underline">Filipe Fortes</a>, <a href="https://creativecommons.org/licenses/by-sa/2.0/" target="_blank" rel="license noopener noreferrer" class="underline">CC BY-SA 2.0</a>.</p>
                                @endif
                                <div class="tc-experience-card__body">
                                    <div class="flex items-start justify-between gap-3">
                                        <p class="text-[0.68rem] font-bold uppercase tracking-[0.13em] text-primary-600">Experiencia local</p>
                                        @if($actividad->ubicacion_url)
                                            <a href="{{ $actividad->ubicacion_url }}" target="_blank" rel="noopener noreferrer" class="shrink-0 text-xs font-bold text-primary-700 hover:underline">Mapa ↗</a>
                                        @endif
                                    </div>
                                    <h2 class="mt-2 text-xl font-bold leading-tight text-gray-900"><a href="{{ route('catalogo.actividades.show', $actividad) }}" class="hover:underline">{{ $actividad->nombre }}</a></h2>
                                    @if($actividad->descripcion)
                                        <p class="mt-2 text-sm leading-6 text-gray-500 line-clamp-3">{{ $actividad->descripcion }}</p>
                                    @else
                                        <p class="mt-2 text-sm leading-6 text-gray-400">Información referencial registrada por el operador.</p>
                                    @endif
                                    <div class="tc-card-meta">
                                        <span>
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6v6l4 2m5-2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                            {{ $duracion }}
                                        </span>
                                        <span class="font-bold text-primary-700">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c1.657 0 3 .895 3 2s-1.343 2-3 2-3 .895-3 2 1.343 2 3 2m0-8c-1.11 0-2.08.402-2.599 1M12 8V6m0 6v2m0 4v-2m0 2c1.11 0 2.08-.402 2.599-1M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                            {{ $actividad->costo > 0 ? 'S/ '.number_format((float) $actividad->costo, 2) : 'Gratuita' }}
                                        </span>
                                    </div>
                                    <a href="{{ route('catalogo.actividades.show', $actividad) }}" class="tc-card-cta">Ver experiencia <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 18 6-6-6-6"/></svg></a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                    <div class="mt-8">{{ $actividades->links() }}</div>
                @endif
            </section>
        </div>
    </div>
@endsection
