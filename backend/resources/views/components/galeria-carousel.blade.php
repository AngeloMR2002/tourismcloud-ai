{{--
    Partial: galeria-carousel.blade.php
    Fix v2: Los botones prev/next estaban dentro del trackWrap que capturaba
    el puntero (setPointerCapture), impidiendo que los clicks de los botones
    llegaran. Solución: mover los botones FUERA del trackWrap (en el padre
    .tc-carousel) usando position:absolute relativo a .tc-carousel.
    Sin dependencias externas — Pointer Events API (W3C estándar).
--}}

@php
    $slides = [];
    if ($imagenPortada) {
        $slides[] = ['url' => asset('storage/' . $imagenPortada), 'alt' => $nombreEntidad];
    }
    foreach ($imagenes as $img) {
        $slides[] = ['url' => asset('storage/' . $img->url), 'alt' => $img->alt_text ?? $nombreEntidad];
    }
    $haySlides = count($slides) > 0;
    $cid = $carouselId ?? 'main';
@endphp

@if($haySlides)
<div class="tc-carousel" id="carousel-{{ $cid }}" data-total="{{ count($slides) }}" style="position: relative;">

    {{-- Visor principal (solo la pista, SIN los botones dentro) --}}
    <div class="tc-carousel-track-wrap" id="trackwrap-{{ $cid }}">
        <div class="tc-carousel-track" id="track-{{ $cid }}">
            @foreach($slides as $slide)
                <div class="tc-carousel-slide">
                    <img src="{{ $slide['url'] }}" alt="{{ $slide['alt'] }}"
                         class="tc-carousel-img" draggable="false">
                </div>
            @endforeach
        </div>

        {{-- Contador (solo lectura, no captura clicks) --}}
        <div class="tc-carousel-counter" id="counter-{{ $cid }}">
            1 / {{ count($slides) }}
        </div>
    </div>

    {{-- ── Botones FUERA del trackWrap para evitar conflicto con pointer capture ── --}}
    @if(count($slides) > 1)
    <button class="tc-carousel-btn tc-carousel-btn-prev" id="prev-{{ $cid }}" aria-label="Imagen anterior" type="button">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </button>
    <button class="tc-carousel-btn tc-carousel-btn-next" id="next-{{ $cid }}" aria-label="Imagen siguiente" type="button">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </button>

    {{-- Tira de thumbnails --}}
    <div class="tc-carousel-thumbs" id="thumbs-{{ $cid }}">
        @foreach($slides as $i => $slide)
            <button type="button"
                    class="tc-carousel-thumb {{ $i === 0 ? 'is-active' : '' }}"
                    data-index="{{ $i }}"
                    aria-label="Ver imagen {{ $i + 1 }}">
                <img src="{{ $slide['url'] }}" alt="{{ $slide['alt'] }}" draggable="false">
            </button>
        @endforeach
    </div>
    @endif
</div>

@push('scripts')
<script>
(function () {
    var cid      = '{!! $cid !!}';
    var total    = {{ count($slides) }};
    var current  = 0;

    var carousel  = document.getElementById('carousel-' + cid);
    var trackWrap = document.getElementById('trackwrap-' + cid);
    var track     = document.getElementById('track-' + cid);
    var prevBtn   = document.getElementById('prev-' + cid);
    var nextBtn   = document.getElementById('next-' + cid);
    var counter   = document.getElementById('counter-' + cid);
    var thumbsWrap = document.getElementById('thumbs-' + cid);
    var thumbs    = thumbsWrap ? Array.from(thumbsWrap.querySelectorAll('.tc-carousel-thumb')) : [];

    function goTo(n) {
        n = Math.max(0, Math.min(total - 1, n));
        current = n;
        track.style.transform = 'translateX(-' + (current * 100) + '%)';
        if (counter) counter.textContent = (current + 1) + ' / ' + total;
        if (prevBtn) { prevBtn.disabled = current === 0; prevBtn.style.opacity = current === 0 ? '0.35' : '1'; }
        if (nextBtn) { nextBtn.disabled = current === total - 1; nextBtn.style.opacity = current === total - 1 ? '0.35' : '1'; }
        thumbs.forEach(function (t, i) { t.classList.toggle('is-active', i === current); });
        if (thumbsWrap && thumbs[current]) {
            var th = thumbs[current];
            thumbsWrap.scrollTo({ left: th.offsetLeft - (thumbsWrap.offsetWidth / 2) + (th.offsetWidth / 2), behavior: 'smooth' });
        }
    }

    // Botones prev / next (ahora FUERA del trackWrap — no hay conflicto)
    if (prevBtn) prevBtn.addEventListener('click', function () { goTo(current - 1); });
    if (nextBtn) nextBtn.addEventListener('click', function () { goTo(current + 1); });

    // Thumbnails
    thumbs.forEach(function (t) {
        t.addEventListener('click', function () { goTo(parseInt(this.dataset.index, 10)); });
    });

    // Drag / swipe con Pointer Events (solo sobre el trackWrap)
    var startX = 0, startTime = 0, isDragging = false, dragDeltaX = 0;

    trackWrap.addEventListener('pointerdown', function (e) {
        if (e.button !== 0) return;
        isDragging = true;
        startX = e.clientX;
        startTime = Date.now();
        dragDeltaX = 0;
        trackWrap.setPointerCapture(e.pointerId);
        track.style.transition = 'none';
    });

    trackWrap.addEventListener('pointermove', function (e) {
        if (!isDragging) return;
        dragDeltaX = e.clientX - startX;
        track.style.transform = 'translateX(calc(' + -(current * 100) + '% + ' + dragDeltaX + 'px))';
    });

    function endDrag() {
        if (!isDragging) return;
        isDragging = false;
        track.style.transition = '';
        var elapsed = Date.now() - startTime;
        var velocity = Math.abs(dragDeltaX) / Math.max(elapsed, 1);
        var threshold = trackWrap.offsetWidth * 0.25;
        if (dragDeltaX < -threshold || (velocity > 0.5 && dragDeltaX < 0)) { goTo(current + 1); }
        else if (dragDeltaX > threshold || (velocity > 0.5 && dragDeltaX > 0)) { goTo(current - 1); }
        else { goTo(current); }
    }

    trackWrap.addEventListener('pointerup', endDrag);
    trackWrap.addEventListener('pointercancel', function () { isDragging = false; track.style.transition = ''; goTo(current); });

    // Teclado
    carousel.setAttribute('tabindex', '0');
    carousel.addEventListener('keydown', function (e) {
        if (e.key === 'ArrowLeft')  goTo(current - 1);
        if (e.key === 'ArrowRight') goTo(current + 1);
    });

    goTo(0);
})();
</script>
@endpush
@endif
