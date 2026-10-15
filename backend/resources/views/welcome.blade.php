@extends('layouts.app', ['title' => 'Inicio'])

@section('content')
    @php
        $planUrl = route('register');
        $planLabel = 'Crear mi cuenta';

        if (auth()->check()) {
            if (auth()->user()->isTurista()) {
                $planUrl = route('itinerarios.create');
                $planLabel = 'Crear mi itinerario';
            } elseif (auth()->user()->isOperador()) {
                $planUrl = route('operador.actividades.index');
                $planLabel = 'Gestionar mis actividades';
            } elseif (auth()->user()->isProveedor()) {
                $planUrl = route('proveedor.establecimientos.index');
                $planLabel = 'Gestionar mis establecimientos';
            } else {
                $planUrl = route('dashboard');
                $planLabel = 'Abrir mi panel';
            }
        }

        $heroSlides = [
            [
                'eyebrow' => 'Turismo inteligente para Perú',
                'title' => 'Un viaje que empieza con lo que te inspira.',
                'lead' => 'Encuentra destinos, lugares, actividades y rutas para ordenar cada día de tu próxima aventura.',
                'primary_label' => 'Ver destinos',
                'primary_url' => '#destinos',
                'image' => asset('storage/actividades-rutas-demo/cusco-plaza.jpg'),
                'position' => 'center 62%',
                'credit' => 'Fotografía de Cusco: Entropy1963 · dominio público',
                'credit_url' => 'https://commons.wikimedia.org/wiki/File:The_Plaza_de_Armas_of_Cusco.jpg',
            ],
            [
                'eyebrow' => 'Costa, historia y naturaleza',
                'title' => 'Experiencias para descubrir a tu ritmo.',
                'lead' => 'Revisa actividades publicadas por operadores y elige las que mejor acompañen los días de tu visita.',
                'primary_label' => 'Explorar actividades',
                'primary_url' => route('catalogo.actividades.index'),
                'image' => asset('images/home-hero/paracas-amanecer.png'),
                'position' => 'center center',
                'credit' => 'Imagen conceptual creada para TourismCloud AI',
                'credit_url' => null,
            ],
            [
                'eyebrow' => 'Paradas que cuentan historias',
                'title' => 'Rutas que conectan cada parada.',
                'lead' => 'Consulta recorridos con lugares ordenados y arma una visita con la información disponible en la plataforma.',
                'primary_label' => 'Explorar rutas',
                'primary_url' => route('catalogo.rutas.index'),
                'image' => asset('images/home-hero/andes-ruta.png'),
                'position' => 'center center',
                'credit' => 'Imagen conceptual creada para TourismCloud AI',
                'credit_url' => null,
            ],
            [
                'eyebrow' => 'Planificación inteligente · en evolución',
                'title' => 'Tu próximo viaje, ordenado paso a paso.',
                'lead' => 'Elige un destino, define tus días y registra un presupuesto referencial para guardar tu propio punto de partida.',
                'primary_label' => $planLabel,
                'primary_url' => $planUrl,
                'image' => asset('images/home-hero/arequipa-atardecer.png'),
                'position' => 'center center',
                'credit' => 'Imagen conceptual creada para TourismCloud AI',
                'credit_url' => null,
            ],
        ];
    @endphp

    <div class="tc-home">
        <section class="tc-home-hero" aria-labelledby="inicio-titulo" aria-roledescription="carrusel" aria-label="Escenas destacadas de TourismCloud AI" data-home-carousel>
            <div class="tc-home-hero__slides" aria-hidden="true">
                @foreach($heroSlides as $heroSlide)
                    <div
                        class="tc-home-hero__slide {{ $loop->first ? 'is-active' : '' }}"
                        data-hero-slide
                        data-hero-eyebrow="{{ $heroSlide['eyebrow'] }}"
                        data-hero-title="{{ $heroSlide['title'] }}"
                        data-hero-lead="{{ $heroSlide['lead'] }}"
                        data-hero-primary-label="{{ $heroSlide['primary_label'] }}"
                        data-hero-primary-url="{{ $heroSlide['primary_url'] }}"
                        data-hero-credit="{{ $heroSlide['credit'] }}"
                        data-hero-credit-url="{{ $heroSlide['credit_url'] }}"
                        style="--tc-home-hero-image: url('{{ $heroSlide['image'] }}'); --tc-home-hero-position: {{ $heroSlide['position'] }};"
                    ></div>
                @endforeach
            </div>

            <div class="tc-home-hero__inner">
                <p class="tc-home-eyebrow" data-hero-eyebrow>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M12 21s7-5 7-12a7 7 0 1 0-14 0c0 7 7 12 7 12Z" stroke-width="1.8"/><circle cx="12" cy="9" r="2.2"/></svg>
                    <span>{{ $heroSlides[0]['eyebrow'] }}</span>
                </p>
                <h1 id="inicio-titulo" data-hero-title>{{ $heroSlides[0]['title'] }}</h1>
                <p class="tc-home-hero__lead" data-hero-lead>{{ $heroSlides[0]['lead'] }}</p>

                <form id="busqueda-inicio" action="{{ route('catalogo.atractivos.index') }}" method="GET" class="tc-home-search tc-home-search--hero" aria-label="Buscar en los catálogos">
                    <div>
                        <label for="catalogo-inicio">Quiero explorar</label>
                        <select id="catalogo-inicio" class="tc-select">
                            <option value="{{ route('catalogo.atractivos.index') }}" data-query="busqueda" data-placeholder="Ej. Machu Picchu">Atractivos</option>
                            <option value="{{ route('catalogo.actividades.index') }}" data-query="buscar" data-placeholder="Ej. caminata o taller">Actividades</option>
                            <option value="{{ route('catalogo.rutas.index') }}" data-query="buscar" data-placeholder="Ej. ruta cultural">Rutas</option>
                            <option value="{{ route('catalogo.establecimientos.index') }}" data-query="busqueda" data-placeholder="Ej. hotel o restaurante">Establecimientos</option>
                        </select>
                    </div>
                    <div>
                        <label for="busqueda-inicio-texto">¿Qué tienes en mente?</label>
                        <div class="tc-search-input-wrap">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><circle cx="10.5" cy="10.5" r="6.5" stroke-width="1.7"/><path d="m16 16 5 5" stroke-width="1.7" stroke-linecap="round"/></svg>
                            <input id="busqueda-inicio-texto" name="busqueda" type="search" maxlength="100" placeholder="Ej. Machu Picchu" class="tc-input">
                        </div>
                    </div>
                    <button type="submit" class="tc-btn-primary">Buscar <span aria-hidden="true">→</span></button>
                </form>

                <div class="tc-home-hero__actions">
                    <a href="{{ $heroSlides[0]['primary_url'] }}" class="tc-btn-hero-secondary" data-hero-primary-action><span data-hero-primary-label>{{ $heroSlides[0]['primary_label'] }}</span> <span aria-hidden="true">→</span></a>
                    <a href="{{ route('catalogo.rutas.index') }}" class="tc-hero-text-link">Explorar rutas <span aria-hidden="true">↗</span></a>
                </div>
            </div>

            <div class="tc-home-hero__controls" role="group" aria-label="Controles del carrusel">
                <button type="button" class="tc-home-hero__carousel-button tc-home-hero__carousel-arrow" data-hero-previous aria-label="Mostrar la escena anterior">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="m14 6-6 6 6 6" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <div class="tc-home-hero__dots" role="group" aria-label="Seleccionar escena">
                    @foreach($heroSlides as $heroSlide)
                        <button type="button" class="tc-home-hero__dot {{ $loop->first ? 'is-active' : '' }}" data-hero-dot data-hero-index="{{ $loop->index }}" aria-label="Mostrar escena {{ $loop->iteration }}: {{ $heroSlide['title'] }}" aria-current="{{ $loop->first ? 'true' : 'false' }}" tabindex="{{ $loop->first ? '0' : '-1' }}"><span class="sr-only">Escena {{ $loop->iteration }}</span></button>
                    @endforeach
                </div>
                <button type="button" class="tc-home-hero__carousel-button tc-home-hero__carousel-arrow" data-hero-next aria-label="Mostrar la escena siguiente">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="m10 6 6 6-6 6" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <button type="button" class="tc-home-hero__carousel-button tc-home-hero__pause" data-hero-pause aria-label="Pausar cambio automático" aria-pressed="false">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M9 7v10M15 7v10" stroke-width="1.9" stroke-linecap="round"/></svg>
                    <span class="sr-only" data-hero-pause-label>Pausar cambio automático</span>
                </button>
            </div>

            <ul class="tc-home-hero__benefits" aria-label="Características de la plataforma">
                <li><span aria-hidden="true">✦</span> Información organizada</li>
                <li><span aria-hidden="true">⌁</span> Rutas con paradas</li>
                <li><span aria-hidden="true">◌</span> Planifica por días</li>
                <li><span aria-hidden="true">↗</span> Explora a tu ritmo</li>
            </ul>

            <p class="tc-home-hero__credit"><span data-hero-credit>{{ $heroSlides[0]['credit'] }}</span><a href="{{ $heroSlides[0]['credit_url'] }}" target="_blank" rel="noopener noreferrer" data-hero-credit-link>Ver crédito</a></p>
        </section>

        <div class="tc-home-content">
            <section id="destinos" class="tc-home-section tc-home-section--destinations" aria-labelledby="destinos-titulo">
                <div class="tc-home-section__heading">
                    <div>
                        <p class="tc-section-kicker">Para empezar</p>
                        <h2 id="destinos-titulo">Destinos principales</h2>
                        <p>Conoce los destinos disponibles y empieza a construir tu recorrido.</p>
                    </div>
                    <a href="{{ route('catalogo.atractivos.index') }}" class="tc-section-link">Explorar todos <span aria-hidden="true">→</span></a>
                </div>

                <div class="tc-destinations-grid">
                    @forelse($destinosDestacados as $destino)
                        @php
                            $nombreNormalizado = \Illuminate\Support\Str::lower($destino->nombre);
                            $visualDestino = str_contains($nombreNormalizado, 'cusco')
                                ? 'tc-destination-card--cusco'
                                : (str_contains($nombreNormalizado, 'arequipa') ? 'tc-destination-card--arequipa' : 'tc-destination-card--default');
                            $ubicacionDestino = collect([$destino->ciudad, $destino->region, $destino->pais])->filter()->unique()->implode(', ');
                        @endphp
                        <a href="{{ route('catalogo.atractivos.index', ['destino_id' => $destino->id]) }}" class="tc-destination-card {{ $visualDestino }}">
                            <span class="tc-destination-card__symbol" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 19 9.5 6l3.4 7 2.1-4 6 10H3Z" stroke-width="1.6" stroke-linejoin="round"/><path d="M4 19h16" stroke-width="1.6" stroke-linecap="round"/></svg>
                            </span>
                            <span class="tc-destination-card__content">
                                @if($ubicacionDestino !== '')
                                    <span class="tc-destination-card__location">{{ $ubicacionDestino }}</span>
                                @endif
                                <strong>{{ $destino->nombre }}</strong>
                                <span>{{ $destino->descripcion ? \Illuminate\Support\Str::limit($destino->descripcion, 96) : 'Explora los lugares y servicios registrados para este destino.' }}</span>
                                <em>Conocer destino <b aria-hidden="true">→</b></em>
                            </span>
                        </a>
                    @empty
                        <div class="tc-home-empty tc-home-empty--wide">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="m3 19 6-14 4 9 3-5 5 10H3Z" stroke-width="1.7" stroke-linejoin="round"/></svg>
                            <div><strong>Los destinos aparecerán aquí</strong><span>Cuando se publiquen destinos activos, podrás explorarlos desde esta sección.</span></div>
                        </div>
                    @endforelse
                </div>
            </section>

            <section class="tc-home-section tc-home-section--activities" aria-labelledby="actividades-titulo">
                <div class="tc-home-section__heading">
                    <div>
                        <p class="tc-section-kicker">Ideas para tu visita</p>
                        <h2 id="actividades-titulo">Actividades destacadas</h2>
                        <p>Las publicaciones más recientes de los operadores turísticos activos.</p>
                    </div>
                    <a href="{{ route('catalogo.actividades.index') }}" class="tc-section-link">Ver actividades <span aria-hidden="true">→</span></a>
                </div>

                @if($actividadesDestacadas->isNotEmpty())
                    <div class="tc-featured-activities">
                        @foreach($actividadesDestacadas as $actividad)
                            @php
                                $minutos = $actividad->duracion_estimada_min;
                                $duracion = 'Duración por confirmar';
                                if ($minutos) {
                                    $horas = intdiv((int) $minutos, 60);
                                    $restoMinutos = (int) $minutos % 60;
                                    $duracion = trim(($horas ? $horas.' h' : '').($restoMinutos ? ' '.$restoMinutos.' min' : ''));
                                }
                            @endphp
                            <a href="{{ route('catalogo.actividades.show', $actividad) }}" class="tc-feature-card">
                                <div class="tc-feature-card__media">
                                    @if($actividad->portada_url)
                                        <img src="{{ $actividad->portada_url }}" alt="" loading="lazy">
                                    @else
                                        <div class="tc-feature-card__fallback" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="4" r="2" stroke-width="1.7"/><path d="m10 8 4 2-2 6-5 5m5-5 3 5M10 8l-2 4-3 2m9-4 3 3 3 1" stroke-width="1.7" stroke-linejoin="round" stroke-linecap="round"/></svg>
                                        </div>
                                    @endif
                                    <span>{{ $actividad->destino?->nombre ?? 'Destino' }}</span>
                                </div>
                                <div class="tc-feature-card__body">
                                    <h3>{{ $actividad->nombre }}</h3>
                                    <p>{{ \Illuminate\Support\Str::limit($actividad->descripcion, 110) }}</p>
                                    <div><span>{{ $duracion }}</span><strong>Costo referencial</strong></div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="tc-activities-empty">
                        <div class="tc-activities-empty__icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="4" r="2" stroke-width="1.7"/><path d="m10 8 4 2-2 6-5 5m5-5 3 5M10 8l-2 4-3 2m9-4 3 3 3 1" stroke-width="1.7" stroke-linejoin="round" stroke-linecap="round"/></svg>
                        </div>
                        <div>
                            <p class="tc-section-kicker">Próximamente</p>
                            <h3>Aún no hay actividades publicadas</h3>
                            <p>Esta sección mostrará las actividades activas cuando los operadores las registren. No se muestran datos de demostración como si fueran publicaciones reales.</p>
                        </div>
                        <a href="{{ route('catalogo.actividades.index') }}" class="tc-btn-outline">Ir al catálogo</a>
                    </div>
                @endif
            </section>

            <section id="planifica" class="tc-home-plan" aria-labelledby="planifica-titulo">
                <div>
                    <p class="tc-section-kicker">Planificación inteligente <span class="tc-home-plan__status">en evolución</span></p>
                    <h2 id="planifica-titulo">Planifica tu viaje, paso a paso.</h2>
                    <p>Elige un destino, define tus días y registra un presupuesto referencial. Guarda el plan y consúltalo desde tu agenda.</p>
                    <a href="{{ $planUrl }}" class="tc-btn-primary">{{ $planLabel }} <span aria-hidden="true">→</span></a>
                </div>
                <ol>
                    <li><span>1</span><div><h3>Explora</h3><p>Encuentra lugares, actividades y servicios según tu interés.</p></div></li>
                    <li><span>2</span><div><h3>Ordena</h3><p>Define el destino, tus días disponibles y el presupuesto.</p></div></li>
                    <li><span>3</span><div><h3>Consulta</h3><p>Revisa el plan y sus fechas desde una misma agenda.</p></div></li>
                </ol>
            </section>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        var form = document.getElementById('busqueda-inicio');
        var catalogo = document.getElementById('catalogo-inicio');
        var busqueda = document.getElementById('busqueda-inicio-texto');

        function sincronizarCatalogo() {
            var opcion = catalogo.selectedOptions[0];
            form.action = opcion.value;
            busqueda.name = opcion.dataset.query;
            busqueda.placeholder = opcion.dataset.placeholder;
        }

        catalogo.addEventListener('change', sincronizarCatalogo);
        form.addEventListener('submit', sincronizarCatalogo);
        window.addEventListener('pageshow', sincronizarCatalogo);
        sincronizarCatalogo();
    })();

    (function () {
        var carousel = document.querySelector('[data-home-carousel]');

        if (!carousel) {
            return;
        }

        var slides = Array.prototype.slice.call(carousel.querySelectorAll('[data-hero-slide]'));
        var dots = Array.prototype.slice.call(carousel.querySelectorAll('[data-hero-dot]'));
        var previousButton = carousel.querySelector('[data-hero-previous]');
        var nextButton = carousel.querySelector('[data-hero-next]');
        var pauseButton = carousel.querySelector('[data-hero-pause]');
        var pauseLabel = carousel.querySelector('[data-hero-pause-label]');
        var eyebrow = carousel.querySelector('[data-hero-eyebrow] span');
        var title = carousel.querySelector('h1[data-hero-title]');
        var lead = carousel.querySelector('p[data-hero-lead]');
        var primaryAction = carousel.querySelector('[data-hero-primary-action]');
        var primaryLabel = primaryAction.querySelector('[data-hero-primary-label]');
        var credit = carousel.querySelector('span[data-hero-credit]');
        var creditLink = carousel.querySelector('[data-hero-credit-link]');
        var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
        var activeIndex = 0;
        var timerId;
        var manuallyPaused = false;

        function stopRotation() {
            if (timerId) {
                window.clearInterval(timerId);
                timerId = undefined;
            }
        }

        function startRotation() {
            stopRotation();

            if (!manuallyPaused && !reducedMotion.matches) {
                timerId = window.setInterval(function () {
                    showSlide(activeIndex + 1);
                }, 3500);
            }
        }

        function showSlide(index) {
            activeIndex = (index + slides.length) % slides.length;
            var activeSlide = slides[activeIndex];

            slides.forEach(function (slide, slideIndex) {
                slide.classList.toggle('is-active', slideIndex === activeIndex);
            });

            dots.forEach(function (dot, dotIndex) {
                var isActive = dotIndex === activeIndex;
                dot.classList.toggle('is-active', isActive);
                dot.setAttribute('aria-current', isActive ? 'true' : 'false');
                dot.tabIndex = isActive ? 0 : -1;
            });

            eyebrow.textContent = activeSlide.dataset.heroEyebrow;
            title.textContent = activeSlide.dataset.heroTitle;
            lead.textContent = activeSlide.dataset.heroLead;
            primaryAction.href = activeSlide.dataset.heroPrimaryUrl;
            primaryLabel.textContent = activeSlide.dataset.heroPrimaryLabel;
            credit.textContent = activeSlide.dataset.heroCredit;

            if (activeSlide.dataset.heroCreditUrl) {
                creditLink.href = activeSlide.dataset.heroCreditUrl;
                creditLink.hidden = false;
            } else {
                creditLink.hidden = true;
            }
        }

        function moveTo(index) {
            showSlide(index);
            startRotation();
        }

        previousButton.addEventListener('click', function () {
            moveTo(activeIndex - 1);
        });

        nextButton.addEventListener('click', function () {
            moveTo(activeIndex + 1);
        });

        dots.forEach(function (dot, dotIndex) {
            dot.addEventListener('click', function () {
                moveTo(dotIndex);
            });

            dot.addEventListener('keydown', function (event) {
                if (event.key !== 'ArrowDown' && event.key !== 'ArrowRight' && event.key !== 'ArrowUp' && event.key !== 'ArrowLeft') {
                    return;
                }

                event.preventDefault();
                var direction = event.key === 'ArrowDown' || event.key === 'ArrowRight' ? 1 : -1;
                var nextIndex = (dotIndex + direction + dots.length) % dots.length;
                dots[nextIndex].focus();
                moveTo(nextIndex);
            });
        });

        pauseButton.addEventListener('click', function () {
            manuallyPaused = !manuallyPaused;
            pauseButton.setAttribute('aria-pressed', manuallyPaused ? 'true' : 'false');
            pauseButton.setAttribute('aria-label', manuallyPaused ? 'Reanudar cambio automático' : 'Pausar cambio automático');
            pauseLabel.textContent = manuallyPaused ? 'Reanudar cambio automático' : 'Pausar cambio automático';

            if (manuallyPaused) {
                stopRotation();
            } else {
                startRotation();
            }
        });

        carousel.addEventListener('mouseenter', stopRotation);
        carousel.addEventListener('mouseleave', startRotation);
        carousel.addEventListener('focusin', stopRotation);
        carousel.addEventListener('focusout', function (event) {
            if (!carousel.contains(event.relatedTarget)) {
                startRotation();
            }
        });

        document.addEventListener('visibilitychange', function () {
            if (document.hidden) {
                stopRotation();
            } else {
                startRotation();
            }
        });

        reducedMotion.addEventListener('change', startRotation);
        showSlide(activeIndex);
        startRotation();
    })();
</script>
@endpush
