@props(['item', 'type'])
@php
    $plural = ['atractivo' => 'atractivos', 'actividad' => 'actividades', 'ruta' => 'rutas', 'establecimiento' => 'establecimientos'][$type];
    $url = route('catalogo.'.$plural.'.show', $item);
    $image = in_array($type, ['actividad', 'ruta']) ? $item->portada_url : ($item->imagen_portada ? asset('storage/'.$item->imagen_portada) : null);
    $minutes = (int) ($item->duracion_estimada_min ?? 0);
    $duration = trim(($minutes >= 60 ? intdiv($minutes, 60).' h ' : '').($minutes % 60 ? ($minutes % 60).' min' : ''));
    $price = match ($type) {
        'atractivo' => $item->costo_entrada,
        'actividad' => $item->costo,
        'ruta' => $item->costo_entradas_referencial,
        default => null,
    };
    $reviewCount = in_array($type, ['atractivo', 'establecimiento']) ? $item->total_resenas : 0;
    $priceLevels = ['Bajo' => __('catalog.price_low'), 'Medio' => __('catalog.price_mid'), 'Alto' => __('catalog.price_high'), 'Lujoso' => __('catalog.price_luxury')];
    $placeTypes = ['hotel' => __('catalog.hotel'), 'restaurante' => __('catalog.restaurant'), 'transporte' => __('catalog.transport'), 'agencia' => __('catalog.agency'), 'otro' => __('catalog.other')];
@endphp
<article {{ $attributes->class(['tc-discovery-card']) }}>
    <a href="{{ $url }}" class="tc-discovery-card__photo" tabindex="-1" aria-hidden="true">
        @if($image)<img src="{{ $image }}" alt="" loading="lazy" width="640" height="480">
        @else<div class="tc-cover-fallback"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="m3 19 6-10 4 5 3-7 5 12H3Z" stroke-width="1.5"/></svg></div>@endif
    </a>
    <x-favorite-button :type="$type" :item="$item" />
    <div class="tc-discovery-card__body">
        <p class="tc-discovery-card__category">{{ $item->destino?->nombre }} · {{ __('favorites.'.$type) }}</p>
        <h2><a href="{{ $url }}" class="tc-discovery-card__link">{{ $item->nombre }}</a></h2>
        @if($item->descripcion)<p class="tc-discovery-card__description">{{ $item->descripcion }}</p>@endif
        <div class="tc-discovery-card__meta">
            @if($minutes)<span>{{ $duration }}</span>@endif
            @if($type === 'ruta')
                <span>{{ $item->paradas_count ?? $item->paradas->count() }} {{ __('catalog.stops') }}</span>
                @if($item->distancia_km)<span>{{ number_format((float) $item->distancia_km, 1) }} km</span>@endif
            @endif
            @if($type === 'establecimiento')<span>{{ $placeTypes[$item->tipo] ?? $item->tipo }}</span>@endif
        </div>
        <div class="tc-discovery-card__bottom">
            @if($reviewCount > 0)
                <span class="tc-discovery-card__rating">
                    <span aria-hidden="true">★</span> {{ number_format($item->promedio_puntuacion, 1) }}
                    <span class="tc-discovery-card__count">({{ $reviewCount }})<span class="sr-only"> {{ __('catalog.reviews') }}</span></span>
                </span>
            @endif
            <div class="tc-discovery-card__price">
                @if($type === 'ruta')<small>{{ __('favorites.entry_note') }}</small>@endif
                @if($price !== null)
                    <strong @class(['tc-discovery-card__free' => (float) $price <= 0])>{{ (float) $price > 0 ? 'S/ '.number_format((float) $price, 2) : __('catalog.free') }}</strong>
                @elseif($type === 'establecimiento' && $item->rango_precio)
                    <small>{{ __('catalog.price_level') }}</small><strong class="tc-discovery-card__level">{{ $priceLevels[$item->rango_precio] ?? $item->rango_precio }}</strong>
                @else<small>{{ __('favorites.price_pending') }}</small>@endif
            </div>
        </div>
    </div>
</article>
