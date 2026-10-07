@extends('actividades::layouts.app')
@section('content')
<div class="tc-travel-page">
    <section class="tc-travel-hero tc-travel-route-hero">
        <div class="tc-travel-hero-inner">
            <p class="tc-travel-breadcrumb">Perú <span aria-hidden="true">/</span> Rutas y circuitos</p>
            <h1>La mejor parte del viaje<br>está en el recorrido.</h1>
            <p class="tc-travel-hero-lead">Descubre lugares conectados por una historia. Explora las paradas, revisa las fuentes y elige cómo recorrerlas.</p>
            <div class="tc-travel-hero-facts"><span><strong>{{ $rutas->total() }}</strong> {{ $rutas->total() === 1 ? 'recorrido' : 'recorridos' }} en esta búsqueda</span><span><strong>Paradas ordenadas</strong> lugares con fuente</span><span><strong>Condiciones claras</strong> sin tiempos inventados</span></div>
            <a href="#explorar-rutas" class="tc-travel-hero-cta">Explorar recorridos <span aria-hidden="true">↓</span></a>
        </div>
        <svg class="tc-travel-route-art" viewBox="0 0 360 260" fill="none" aria-hidden="true"><path d="M30 175c45-145 99 70 141-32S287 215 321 64" stroke="#a4d7c9" stroke-width="3" stroke-dasharray="8 8" stroke-linecap="round"/><circle cx="38" cy="167" r="19" fill="#fff"/><circle cx="171" cy="143" r="19" fill="#fff"/><circle cx="321" cy="64" r="19" fill="#fff"/><g fill="#086859" font-size="16" font-family="sans-serif" text-anchor="middle"><text x="38" y="173">1</text><text x="171" y="149">2</text><text x="321" y="70">3</text></g><path d="M20 50h270M70 230h250" stroke="#477c70" stroke-width="1"/><text x="185" y="230" fill="#b4d4c9" font-size="10" font-family="sans-serif" text-anchor="middle">EXPLORA · CONECTA · DESCUBRE</text></svg>
    </section>
    <div class="tc-travel-container">
    <nav class="tc-travel-tabs" aria-label="Explorar P3"><a href="{{ route('catalogo.actividades.index') }}" >Actividades y experiencias</a><a href="{{ route('catalogo.rutas.index') }}" aria-current="page">Rutas y circuitos</a></nav>

    <div class="tc-catalog-layout" id="explorar-rutas">
    <aside class="tc-filter-panel" aria-label="Filtros de rutas">
    <details open data-responsive-filters>
    <summary class="tc-filter-heading"><span><span class="tc-eyebrow">AFINA TU BÚSQUEDA</span><span class="tc-filter-title">Encuentra tu recorrido</span></span><span aria-hidden="true">☷</span></summary>
    <form method="GET" action="{{ route('catalogo.rutas.index') }}" class="tc-filters grid gap-4">
        <label class="text-sm">Buscar<input name="buscar" value="{{ $filtros['buscar'] ?? '' }}" class="tc-input mt-1"></label>
        <label class="text-sm">Destino<select name="destino_id" class="tc-select mt-1"><option value="">Todos</option>@foreach($destinos as $destino)<option value="{{ $destino->id }}" @selected((string)($filtros['destino_id'] ?? '') === (string)$destino->id)>{{ $destino->nombre }}</option>@endforeach</select></label>
        <label class="text-sm">Tipo<select name="tipo_ruta" class="tc-select mt-1"><option value="">Todos</option>@foreach(['circuito'=>'Circuito','lineal'=>'Lineal','tematica'=>'Temática','senderismo'=>'Senderismo'] as $valor=>$texto)<option value="{{ $valor }}" @selected(($filtros['tipo_ruta'] ?? '') === $valor)>{{ $texto }}</option>@endforeach</select></label>
        <label class="text-sm">Transporte<select name="transporte_recomendado" class="tc-select mt-1"><option value="">Todos</option>@foreach(['a_pie'=>'A pie','bicicleta'=>'Bicicleta','automovil'=>'Automóvil','autobus'=>'Autobús','mixto'=>'Mixto'] as $valor=>$texto)<option value="{{ $valor }}" @selected(($filtros['transporte_recomendado'] ?? '') === $valor)>{{ $texto }}</option>@endforeach</select></label>
        <label class="text-sm">Duración máxima conocida (horas)<input type="number" name="duracion_max_horas" value="{{ $filtros['duracion_max_horas'] ?? '' }}" min="0" step="0.1" class="tc-input mt-1"></label>
        <label class="text-sm">Dificultad<select name="nivel_dificultad" class="tc-select mt-1"><option value="">Todas</option>@foreach(['facil'=>'Fácil','moderada'=>'Moderada','dificil'=>'Difícil','experto'=>'Experto'] as $valor=>$texto)<option value="{{ $valor }}" @selected(($filtros['nivel_dificultad'] ?? '') === $valor)>{{ $texto }}</option>@endforeach</select></label>
        <label class="text-sm">Orden<select name="orden" class="tc-select mt-1">@foreach(['recientes'=>'Recientes','nombre_asc'=>'Nombre','duracion_asc'=>'Duración','distancia_asc'=>'Distancia'] as $valor=>$texto)<option value="{{ $valor }}" @selected(($filtros['orden'] ?? 'recientes') === $valor)>{{ $texto }}</option>@endforeach</select></label>
        <div class="flex items-end gap-3"><button class="tc-btn-primary">Filtrar</button><a href="{{ route('catalogo.rutas.index') }}" class="text-sm underline">Limpiar</a></div>
    </form>
    </details>
    </aside>
    <section class="tc-results" aria-label="Resultados de la búsqueda">
    @if($errors->any())<p role="alert" class="tc-alert-error">{{ $errors->first() }}</p>@endif
    <div class="tc-results-heading"><div><p class="tc-eyebrow">ELIGE TU PRÓXIMA VISITA</p><h2>Recorridos para explorar</h2></div><span class="tc-result-count">{{ $rutas->total() }} {{ $rutas->total() === 1 ? 'recorrido' : 'recorridos' }} con fuente revisada</span></div>
    <div class="tc-results-grid tc-route-grid">
        @forelse($rutas as $ruta)
            <article class="tc-card tc-experience-card overflow-hidden flex flex-col">
                @if($ruta->imagen_portada && $ruta->imagen_fuente_url && $ruta->imagen_autor && $ruta->imagen_licencia)
                    @include('actividades::catalogo.fotografia', ['registro'=>$ruta, 'compacta'=>true])
                @else
                    <div class="tc-route-preview">
                        <p class="tc-route-preview-title">PARADAS DEL RECORRIDO</p>
                        <ol aria-label="Paradas de {{ $ruta->nombre }}">
                            @foreach($ruta->puntos->take(3) as $punto)
                                <li><span>{{ $punto->orden }}</span><span>{{ $punto->nombre_parada }}</span></li>
                            @endforeach
                        </ol>
                        <small>{{ $ruta->puntos->count() }} paradas · Esquema del recorrido, no mapa.</small>
                    </div>
                @endif
                <div class="p-5 flex flex-col gap-3 flex-1">
                    <span class="tc-badge-tipo">{{ $ruta->es_propuesta ? 'Propuesta TourismCloud' : 'Recorrido publicado' }}</span>
                    <h2 class="text-lg font-bold"><a href="{{ route('catalogo.rutas.show', $ruta->id) }}" class="hover:text-teal-700">{{ $ruta->nombre }}</a></h2>
                    <p class="text-xs text-gray-500">{{ $ruta->destino->nombre }} · {{ $ruta->puntos->count() }} paradas</p>
                    <p class="tc-card-description">{{ Illuminate\Support\Str::substr($ruta->descripcion, 0, 155) }}{{ mb_strlen($ruta->descripcion) > 155 ? '…' : '' }}</p>
                    <p class="text-sm">{{ $ruta->distancia_km !== null ? $ruta->distancia_km.' km' : 'Distancia por confirmar' }} · {{ $ruta->duracion_estimada_horas !== null ? $ruta->duracion_estimada_horas.' h' : 'Duración por confirmar' }}</p>
                    <p class="text-primary-700 font-bold">{{ $ruta->costo_formateado }}</p>
                    <a href="{{ route('catalogo.rutas.show', $ruta->id) }}" class="tc-btn-outline mt-auto w-full justify-center">Ver recorrido y fuentes</a>
                </div>
            </article>
        @empty
            <p class="tc-empty-state text-gray-600">No hay rutas verificadas que cumplan estos filtros.</p>
        @endforelse
    </div>
    <div class="mt-6">{{ $rutas->links() }}</div>
    </section>
    </div>
    <section class="tc-travel-assurance" aria-label="Cómo explorar con confianza">
        <div><span aria-hidden="true">✓</span><h2>Información con respaldo</h2><p>Consulta la fuente y la fecha de revisión de cada experiencia.</p></div>
        <div><span aria-hidden="true">↗</span><h2>Contacto directo</h2><p>Confirma las condiciones en la web del responsable, sin pagos en TourismCloud.</p></div>
        <div><span aria-hidden="true">◎</span><h2>Decide con claridad</h2><p>Un dato por confirmar nunca se presenta como gratuito o disponible.</p></div>
    </section>
    <section class="tc-travel-faq" aria-labelledby="preguntas-rutas">
        <div class="tc-travel-section-heading"><div><p class="tc-eyebrow">ANTES DE SALIR</p><h2 id="preguntas-rutas">Preguntas frecuentes</h2></div></div>
        <details><summary>¿Cómo funciona una ruta?</summary><p>Elige un recorrido y abre su detalle para ver la secuencia de paradas y las fuentes de cada lugar. Una propuesta TourismCloud es una sugerencia de visita, no un tour contratado.</p></details>
        <details><summary>¿Los precios y horarios están confirmados?</summary><p>Se muestran solo los datos documentados. “Por confirmar” significa que no contamos con información suficiente. Confirma el precio final, los horarios y la disponibilidad con el responsable antes de viajar.</p></details>
        <details><summary>¿Puedo reservar en TourismCloud?</summary><p>No gestionamos reservas ni pagos. Cuando existe un enlace oficial, puedes consultar directamente con el responsable. Un enlace al inventario turístico es una fuente de información, no un canal de reserva.</p></details>
        <details><summary>¿De dónde vienen las fotos y la información?</summary><p>Las fichas incluyen enlaces a sus fuentes. Las fotos verificadas muestran autor y licencia; cuando no hay una imagen verificable, no la sustituimos por una foto de otro lugar.</p></details>
    </section>
    </div>
</div>
@endsection
