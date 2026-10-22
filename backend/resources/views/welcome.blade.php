@extends('layouts.app', ['title' => __('home.page_title')])

@section('content')
    @php
        $planUrl = route('register');
        $planLabel = __('home.create_account');

        if (auth()->check()) {
            if (auth()->user()->isTurista()) {
                $planUrl = route('itinerarios.create');
                $planLabel = __('home.create_itinerary');
            } elseif (auth()->user()->isOperador()) {
                $planUrl = route('operador.actividades.index');
                $planLabel = __('home.manage_activities');
            } elseif (auth()->user()->isProveedor()) {
                $planUrl = route('proveedor.establecimientos.index');
                $planLabel = __('home.manage_places');
            } else {
                $planUrl = route('dashboard');
                $planLabel = __('home.open_dashboard');
            }
        }

        $heroSlides = [
            [
                'eyebrow' => __('home.hero_1_eyebrow'),
                'title' => __('home.hero_1_title'),
                'lead' => __('home.hero_1_lead'),
                'primary_label' => __('home.view_destinations'),
                'primary_url' => '#destinos',
                'image' => asset('storage/actividades-rutas-demo/cusco-plaza.jpg'),
                'position' => 'center 62%',
                'credit' => __('home.cusco_credit'),
                'credit_url' => 'https://commons.wikimedia.org/wiki/File:The_Plaza_de_Armas_of_Cusco.jpg',
            ],
            [
                'eyebrow' => __('home.hero_2_eyebrow'),
                'title' => __('home.hero_2_title'),
                'lead' => __('home.hero_2_lead'),
                'primary_label' => __('home.explore_activities'),
                'primary_url' => route('catalogo.actividades.index'),
                'image' => asset('images/home-hero/paracas-amanecer.png'),
                'position' => 'center center',
                'credit' => __('home.concept_credit'),
                'credit_url' => null,
            ],
            [
                'eyebrow' => __('home.hero_3_eyebrow'),
                'title' => __('home.hero_3_title'),
                'lead' => __('home.hero_3_lead'),
                'primary_label' => __('home.explore_routes'),
                'primary_url' => route('catalogo.rutas.index'),
                'image' => asset('images/home-hero/andes-ruta.png'),
                'position' => 'center center',
                'credit' => __('home.concept_credit'),
                'credit_url' => null,
            ],
            [
                'eyebrow' => __('home.hero_4_eyebrow'),
                'title' => __('home.hero_4_title'),
                'lead' => __('home.hero_4_lead'),
                'primary_label' => $planLabel,
                'primary_url' => $planUrl,
                'image' => asset('images/home-hero/arequipa-atardecer.png'),
                'position' => 'center center',
                'credit' => __('home.concept_credit'),
                'credit_url' => null,
            ],
        ];
    @endphp

    <div class="tc-home">
        <section class="tc-home-hero" aria-labelledby="inicio-titulo" aria-roledescription="{{ __('home.carousel') }}" aria-label="{{ __('home.featured_scenes') }}" data-home-carousel>
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
                <div class="tc-home-hero__text-slot">
                    <h1 id="inicio-titulo" class="tc-home-hero__title" data-hero-title>{{ $heroSlides[0]['title'] }}</h1>
                    @foreach($heroSlides as $heroSlide)
                        <div class="tc-home-hero__title tc-home-hero__sizer" aria-hidden="true">{{ $heroSlide['title'] }}</div>
                    @endforeach
                </div>
                <div class="tc-home-hero__text-slot">
                    <p class="tc-home-hero__lead" data-hero-lead>{{ $heroSlides[0]['lead'] }}</p>
                    @foreach($heroSlides as $heroSlide)
                        <div class="tc-home-hero__lead tc-home-hero__sizer" aria-hidden="true">{{ $heroSlide['lead'] }}</div>
                    @endforeach
                </div>

                <form id="busqueda-inicio" action="{{ route('catalogo.atractivos.index') }}" method="GET" class="tc-home-search tc-home-search--hero" aria-label="{{ __('home.search_catalogs') }}">
                    <div>
                        <label for="catalogo-inicio">{{ __('home.want_to_explore') }}</label>
                        <select id="catalogo-inicio" class="tc-select">
                            <option value="{{ route('catalogo.atractivos.index') }}" data-query="busqueda" data-placeholder="{{ __('home.example_attraction') }}">{{ __('home.attractions') }}</option>
                            <option value="{{ route('catalogo.actividades.index') }}" data-query="buscar" data-placeholder="{{ __('home.example_activity') }}">{{ __('home.activities') }}</option>
                            <option value="{{ route('catalogo.rutas.index') }}" data-query="buscar" data-placeholder="{{ __('home.example_route') }}">{{ __('home.routes') }}</option>
                            <option value="{{ route('catalogo.establecimientos.index') }}" data-query="busqueda" data-placeholder="{{ __('home.example_place') }}">{{ __('home.places') }}</option>
                        </select>
                    </div>
                    <div>
                        <label for="busqueda-inicio-texto">{{ __('home.what_in_mind') }}</label>
                        <div class="tc-search-input-wrap">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><circle cx="10.5" cy="10.5" r="6.5" stroke-width="1.7"/><path d="m16 16 5 5" stroke-width="1.7" stroke-linecap="round"/></svg>
                            <input id="busqueda-inicio-texto" name="busqueda" type="search" maxlength="100" placeholder="{{ __('home.example_attraction') }}" class="tc-input">
                        </div>
                    </div>
                    <button type="submit" class="tc-btn-primary">{{ __('home.search') }} <span aria-hidden="true">→</span></button>
                </form>

                <div class="tc-home-hero__actions">
                    <a href="{{ $heroSlides[0]['primary_url'] }}" class="tc-btn-hero-secondary" data-hero-primary-action><span data-hero-primary-label>{{ $heroSlides[0]['primary_label'] }}</span> <span aria-hidden="true">→</span></a>
                    <a href="{{ route('catalogo.rutas.index') }}" class="tc-hero-text-link">{{ __('home.explore_routes') }} <span aria-hidden="true">↗</span></a>
                </div>
            </div>

            <ul class="tc-home-hero__benefits" aria-label="{{ __('home.platform_features') }}">
                <li><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="5" y="4" width="14" height="17" rx="2"/><path d="M9 4V2h6v2M9 10h6M9 14h6M9 18h3"/></svg> {{ __('home.organized_info') }}</li>
                <li><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="6" cy="5" r="3"/><circle cx="18" cy="19" r="3"/><path d="M9 5h7a4 4 0 0 1 0 8H8a3 3 0 0 0 0 6h7"/></svg> {{ __('home.routes_with_stops') }}</li>
                <li><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M7 2v6M17 2v6M3 11h18m-13 5 3 3 5-5"/></svg> {{ __('home.plan_by_days') }}</li>
                <li><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="m16 8-2.5 5.5L8 16l2.5-5.5L16 8Z"/></svg> {{ __('home.explore_at_pace') }}</li>
            </ul>

            <p class="tc-home-hero__credit"><span data-hero-credit>{{ $heroSlides[0]['credit'] }}</span><a href="{{ $heroSlides[0]['credit_url'] }}" target="_blank" rel="noopener noreferrer" data-hero-credit-link>{{ __('home.view_credit') }}</a></p>
        </section>

        <div class="tc-home-content">
            <section id="destinos" class="tc-home-section tc-home-section--destinations" aria-labelledby="destinos-titulo">
                <div class="tc-home-section__heading">
                    <div>
                        <p class="tc-section-kicker">{{ __('home.destinations_kicker') }}</p>
                        <h2 id="destinos-titulo">{{ __('home.destinations_title') }}</h2>
                        <p>{{ __('home.destinations_intro') }}</p>
                    </div>
                    <a href="{{ route('catalogo.atractivos.index') }}" class="tc-section-link">{{ __('home.explore_all') }} <span aria-hidden="true">→</span></a>
                </div>

                <div class="tc-destinations-grid">
                    @foreach($ciudadesDestacadas as $ciudad)
                        <a href="{{ route('catalogo.atractivos.index', $ciudad['destino_id'] ? ['destino_id' => $ciudad['destino_id']] : []) }}" class="tc-destination-card" aria-label="{{ __('home.explore_city', ['city' => $ciudad['nombre']]) }}" style="--tc-destination-image: url('{{ asset($ciudad['imagen']) }}')">
                            <span class="tc-destination-card__content">
                                <span class="tc-destination-card__location">{{ $ciudad['region'] }}</span>
                                <strong>{{ $ciudad['nombre'] }}</strong>
                                <span>{{ $ciudad['resumen'] }}</span>
                            </span>
                        </a>
                    @endforeach
                </div>
                <p class="tc-destination-credits">{{ __('home.destination_photo_credit') }} <a href="https://commons.wikimedia.org/wiki/File:Plaza_de_Armas,_Lima,_Peru.jpg">Lima · Allan Grey (CC BY-SA 2.0)</a> · <a href="https://commons.wikimedia.org/wiki/File:Panoramica_de_la_ciudad_de_Puno.jpg">Puno · De los Ordoño et al. (CC BY 3.0)</a> · <a href="https://commons.wikimedia.org/wiki/File:Trujillo_Per%C3%BA_Plaza_de_Armas.jpg">Trujillo · Alfredobi ({{ __('home.public_domain') }})</a> · <a href="https://commons.wikimedia.org/wiki/File:Plaza_de_Armas_en_Iquitos.jpg">Iquitos · LLs (CC BY-SA 4.0)</a>.</p>
            </section>

            <section class="tc-home-section tc-home-section--activities" aria-labelledby="actividades-titulo">
                <div class="tc-home-section__heading">
                    <div>
                        <p class="tc-section-kicker">{{ __('home.activities_kicker') }}</p>
                        <h2 id="actividades-titulo">{{ __('home.featured_activities') }}</h2>
                        <p>{{ __('home.activities_intro') }}</p>
                    </div>
                    <a href="{{ route('catalogo.actividades.index') }}" class="tc-section-link">{{ __('home.view_activities') }} <span aria-hidden="true">→</span></a>
                </div>

                @if($actividadesDestacadas->isNotEmpty())
                    <div class="tc-featured-activities">
                        @foreach($actividadesDestacadas as $actividad)
                        <x-catalog-card :item="$actividad" type="actividad" />
                    @endforeach
                    </div>
                    @if(app()->environment('local'))
                        <p class="tc-destination-credits">{{ __('home.demo_photo_credit') }} <a href="https://commons.wikimedia.org/wiki/File:Traditionnal_peruvian_weaving_-_Cuzco_-_Peru.jpg">{{ __('home.weaving') }} · Pethrus (CC BY-SA 4.0)</a> · <a href="https://commons.wikimedia.org/wiki/File:Street_of_cusco.jpg">{{ __('home.cusco_street') }} · Thomas Quine (CC BY 2.0)</a> · <a href="https://commons.wikimedia.org/wiki/File:Ceviche_Peruvian_dish.jpg">{{ __('home.ceviche') }} · CEllen (CC BY-SA 4.0)</a> · <a href="https://commons.wikimedia.org/wiki/File:Vista_panor%C3%A1mica_de_la_ciudad_del_Cusco_desde_el_S.A._de_MachuAnden.jpg">{{ __('home.panorama') }} · NatalyVeraA (CC BY-SA 4.0)</a>. {{ __('home.cropped_for_cards') }}</p>
                    @endif
                @else
                    <div class="tc-activities-empty">
                        <div class="tc-activities-empty__icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="4" r="2" stroke-width="1.7"/><path d="m10 8 4 2-2 6-5 5m5-5 3 5M10 8l-2 4-3 2m9-4 3 3 3 1" stroke-width="1.7" stroke-linejoin="round" stroke-linecap="round"/></svg>
                        </div>
                        <div>
                            <p class="tc-section-kicker">{{ __('home.coming_soon') }}</p>
                            <h3>{{ __('home.no_activities_title') }}</h3>
                            <p>{{ __('home.no_activities_body') }}</p>
                        </div>
                        <a href="{{ route('catalogo.actividades.index') }}" class="tc-btn-outline">{{ __('home.go_to_catalog') }}</a>
                    </div>
                @endif
            </section>

            <section id="planifica" class="tc-home-plan" aria-labelledby="planifica-titulo">
                <div>
                    <p class="tc-section-kicker">{{ __('home.smart_planning') }} <span class="tc-home-plan__status">{{ __('home.evolving') }}</span></p>
                    <h2 id="planifica-titulo">{{ __('home.plan_title') }}</h2>
                    <p>{{ __('home.plan_intro') }}</p>
                    <a href="{{ $planUrl }}" class="tc-btn-primary">{{ $planLabel }} <span aria-hidden="true">→</span></a>
                </div>
                <ol>
                    <li><span>1</span><div><h3>{{ __('home.step_explore') }}</h3><p>{{ __('home.step_explore_body') }}</p></div></li>
                    <li><span>2</span><div><h3>{{ __('home.step_organize') }}</h3><p>{{ __('home.step_organize_body') }}</p></div></li>
                    <li><span>3</span><div><h3>{{ __('home.step_review') }}</h3><p>{{ __('home.step_review_body') }}</p></div></li>
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

        function stopRotation() {
            if (timerId) {
                window.clearInterval(timerId);
                timerId = undefined;
            }
        }

        function startRotation() {
            stopRotation();

            if (!reducedMotion.matches && !document.hidden) {
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
