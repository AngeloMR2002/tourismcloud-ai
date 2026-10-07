@extends('actividades::layouts.app')
@section('content')
<div class="tc-travel-page">
    @php
        $conFoto = fn ($actividad) => $actividad->imagen_portada && $actividad->imagen_fuente_url && $actividad->imagen_autor && $actividad->imagen_licencia;
        $portada = $actividades->getCollection()->first(fn ($actividad) => $actividad->categoria === 'cultural' && $conFoto($actividad))
            ?? $actividades->getCollection()->first($conFoto);
        $destinoSeleccionado = $destinos->first(fn ($destino) => (string) $destino->id === (string) ($filtros['destino_id'] ?? ''));
    @endphp
    <section class="tc-travel-hero {{ $portada ? 'has-photo' : '' }}">
        @if($portada)
            <img class="tc-travel-hero-image" src="{{ $portada->imagen_portada }}" alt="{{ $portada->nombre }}" fetchpriority="high" referrerpolicy="no-referrer" onerror="this.hidden=true">
        @endif
        <div class="tc-travel-hero-inner">
            <p class="tc-travel-breadcrumb">Perú <span aria-hidden="true">/</span> Actividades y experiencias</p>
            <h1>{{ $destinoSeleccionado?->nombre ?? 'Descubre algo extraordinario.' }}</h1>
            <p class="tc-travel-hero-lead">Visitas, cultura y naturaleza. Encuentra tu próxima experiencia con información que puedes comprobar.</p>
            <div class="tc-travel-hero-facts"><span><strong>{{ $actividades->total() }}</strong> actividades en esta búsqueda</span><span><strong>Fuentes reales</strong> a un clic</span><span><strong>Consulta directa</strong> con el responsable</span></div>
            <a href="#explorar-actividades" class="tc-travel-hero-cta">Explorar actividades <span aria-hidden="true">↓</span></a>
        </div>
        @if($portada)
            <p class="tc-travel-hero-credit">Foto: {{ $portada->imagen_autor }} · <a href="{{ $portada->imagen_fuente_url }}" target="_blank" rel="noopener noreferrer">{{ $portada->imagen_licencia }} / fuente ↗</a></p>
        @endif
    </section>
    <div class="tc-travel-container">
    <nav class="tc-travel-tabs" aria-label="Explorar P3"><a href="{{ route('catalogo.actividades.index') }}" aria-current="page">Actividades y experiencias</a><a href="{{ route('catalogo.rutas.index') }}" >Rutas y circuitos</a></nav>
    @if($destinos->isNotEmpty())
        <section class="tc-travel-destinations" aria-labelledby="destinos-p3">
            <div class="tc-travel-section-heading"><div><p class="tc-eyebrow">UN LUGAR PARA EMPEZAR</p><h2 id="destinos-p3">¿Dónde te gustaría explorar?</h2></div><span>Destinos del catálogo</span></div>
            <div class="tc-travel-destination-grid">
                @foreach($destinos as $destino)
                    @php $fotoDestino = $actividades->getCollection()->first(fn ($actividad) => $actividad->destino_id === $destino->id && $conFoto($actividad)); @endphp
                    <article class="tc-travel-destination">
                        <a class="tc-travel-destination-cover" href="{{ route('catalogo.actividades.index', array_merge($filtros, ['destino_id' => $destino->id])) }}">
                            @if($fotoDestino)<img src="{{ $fotoDestino->imagen_portada }}" alt="" loading="lazy" referrerpolicy="no-referrer" onerror="this.hidden=true">@endif
                            <span><small>Perú · {{ $destino->region }}</small><strong>{{ $destino->nombre }}</strong><em>Explorar actividades ↗</em></span>
                        </a>
                        @if($fotoDestino)<p class="tc-travel-destination-credit">Foto: {{ $fotoDestino->imagen_autor }} · <a href="{{ $fotoDestino->imagen_fuente_url }}" target="_blank" rel="noopener noreferrer">{{ $fotoDestino->imagen_licencia }} ↗</a></p>@endif
                    </article>
                @endforeach
            </div>
        </section>
    @endif
    <div class="tc-catalog-layout" id="explorar-actividades">
    <aside class="tc-filter-panel" aria-label="Filtros de actividades">
    <details open data-responsive-filters>
    <summary class="tc-filter-heading"><span><span class="tc-eyebrow">AFINA TU BÚSQUEDA</span><span class="tc-filter-title">Encuentra tu experiencia</span></span><span aria-hidden="true">☷</span></summary>
    <form method="GET" action="{{ route('catalogo.actividades.index') }}" class="tc-filters grid gap-4">
        <label class="text-sm">Buscar<input name="buscar" value="{{ $filtros['buscar'] ?? '' }}" placeholder="Lugar o actividad" class="tc-input mt-1"></label>
        <label class="text-sm">Destino<select name="destino_id" class="tc-select mt-1"><option value="">Todos</option>@foreach($destinos as $destino)<option value="{{ $destino->id }}" @selected((string)($filtros['destino_id'] ?? '') === (string)$destino->id)>{{ $destino->nombre }}</option>@endforeach</select></label>
        <label class="text-sm">Categoría<select name="categoria" class="tc-select mt-1"><option value="">Todas</option>@foreach(['aventura','cultural','gastronomica','ecoturismo','relax','deportiva'] as $categoria)<option value="{{ $categoria }}" @selected(($filtros['categoria'] ?? '') === $categoria)>{{ ucfirst($categoria) }}</option>@endforeach</select></label>
        <label class="text-sm">Precio máximo confirmado (S/)<input type="number" name="precio_max" value="{{ $filtros['precio_max'] ?? '' }}" min="0" step="0.01" class="tc-input mt-1"></label>
        <label class="text-sm">Duración máxima conocida (min)<input type="number" name="duracion_max" value="{{ $filtros['duracion_max'] ?? '' }}" min="0" class="tc-input mt-1"></label>
        <label class="text-sm">Dificultad<select name="nivel_dificultad" class="tc-select mt-1"><option value="">Todas / sin especificar</option>@foreach(['baja','media','alta'] as $nivel)<option value="{{ $nivel }}" @selected(($filtros['nivel_dificultad'] ?? '') === $nivel)>{{ ucfirst($nivel) }}</option>@endforeach</select></label>
        <label class="text-sm">Orden<select name="orden" class="tc-select mt-1">@foreach(['recientes'=>'Recientes','nombre_asc'=>'Nombre','precio_asc'=>'Precio ascendente','precio_desc'=>'Precio descendente','duracion_asc'=>'Duración'] as $valor=>$texto)<option value="{{ $valor }}" @selected(($filtros['orden'] ?? 'recientes') === $valor)>{{ $texto }}</option>@endforeach</select></label>
        <div class="flex items-end gap-3"><button class="tc-btn-primary">Filtrar</button><a href="{{ route('catalogo.actividades.index') }}" class="text-sm underline">Limpiar</a></div>
        <p class="tc-filter-note">Un filtro de presupuesto excluye costos desconocidos, de referencia o desactualizados. Un filtro de duración excluye tiempos sin comprobar.</p>
    </form>
    </details>
    </aside>
    <section class="tc-results" aria-label="Resultados de la búsqueda">
    @if($errors->any())<p role="alert" class="tc-alert-error">{{ $errors->first() }}</p>@endif
    <div class="tc-results-heading"><div><p class="tc-eyebrow">ELIGE TU PRÓXIMA VISITA</p><h2>Actividades para tu próxima visita</h2></div><span class="tc-result-count">{{ $actividades->total() }} actividades con fuente revisada</span></div>
    <div class="tc-results-grid">
        @forelse($actividades as $actividad)
            <article class="tc-card tc-experience-card overflow-hidden flex flex-col">
                @include('actividades::catalogo.fotografia', ['registro'=>$actividad, 'compacta'=>true])
                <div class="p-5 flex flex-col flex-1 gap-3">
                    <div class="flex flex-wrap gap-2"><span class="tc-chip">{{ ucfirst($actividad->categoria) }}</span><span class="tc-badge-tipo">{{ $actividad->tipo_registro }}</span></div>
                    <h2 class="text-lg font-bold"><a href="{{ route('catalogo.actividades.show', $actividad->id) }}" class="hover:text-teal-700">{{ $actividad->nombre }}</a></h2>
                    <p class="text-xs text-gray-500">{{ $actividad->destino->nombre }}</p>
                    <p class="tc-card-description flex-1">{{ Illuminate\Support\Str::substr($actividad->descripcion, 0, 155) }}{{ mb_strlen($actividad->descripcion) > 155 ? '…' : '' }}</p>
                    <div class="border-t pt-3 flex flex-wrap justify-between gap-2 text-sm"><strong class="text-primary-700">{{ $actividad->precio_formateado }}</strong><span>{{ $actividad->duracion_formateada }}</span></div>
                    <a href="{{ route('catalogo.actividades.show', $actividad->id) }}" class="tc-btn-outline justify-center">Ver detalles y fuente</a>
                </div>
            </article>
        @empty
            <div class="tc-empty-state"><h2 class="text-lg font-bold">No hay opciones que cumplan estos filtros</h2><p class="text-sm text-gray-600 mt-2">Prueba otro destino o retira las restricciones. Los datos desconocidos no se inventan para completar resultados.</p></div>
        @endforelse
    </div>
    <div class="mt-6">{{ $actividades->links() }}</div>
    </section>
    </div>
    <section class="tc-travel-assurance" aria-label="Cómo explorar con confianza">
        <div><span aria-hidden="true">✓</span><h2>Información con respaldo</h2><p>Consulta la fuente y la fecha de revisión de cada experiencia.</p></div>
        <div><span aria-hidden="true">↗</span><h2>Contacto directo</h2><p>Confirma las condiciones en la web del responsable, sin pagos en TourismCloud.</p></div>
        <div><span aria-hidden="true">◎</span><h2>Decide con claridad</h2><p>Un dato por confirmar nunca se presenta como gratuito o disponible.</p></div>
    </section>
    <section class="tc-travel-faq" aria-labelledby="preguntas-actividades">
        <div class="tc-travel-section-heading"><div><p class="tc-eyebrow">ANTES DE SALIR</p><h2 id="preguntas-actividades">Preguntas frecuentes</h2></div></div>
        <details><summary>¿Cómo elijo una actividad?</summary><p>Filtra por destino, categoría, presupuesto o duración y abre una ficha para conocer su información y fuente. Si aplicas presupuesto o duración, las opciones sin esos datos comprobados quedan fuera de los resultados.</p></details>
        <details><summary>¿Los precios y horarios están confirmados?</summary><p>Se muestran solo los datos documentados. “Por confirmar” significa que no contamos con información suficiente. Confirma el precio final, los horarios y la disponibilidad con el responsable antes de viajar.</p></details>
        <details><summary>¿Puedo reservar en TourismCloud?</summary><p>No gestionamos reservas ni pagos. Cuando existe un enlace oficial, puedes consultar directamente con el responsable. Un enlace al inventario turístico es una fuente de información, no un canal de reserva.</p></details>
        <details><summary>¿De dónde vienen las fotos y la información?</summary><p>Las fichas incluyen enlaces a sus fuentes. Las fotos verificadas muestran autor y licencia; cuando no hay una imagen verificable, no la sustituimos por una foto de otro lugar.</p></details>
    </section>
    </div>
</div>
@endsection
