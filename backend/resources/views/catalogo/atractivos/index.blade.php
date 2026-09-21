@extends('layouts.app')

@section('title', 'Atractivos Turisticos')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- ── Hero --}}
    <div class="mb-6">
        <p class="text-sm text-gray-400 mb-0.5">Descubre el Perú</p>
        <h1 style="font-family: var(--font-display); font-size: 2rem; font-weight: 800; color: #1a2232; line-height: 1.15;">
            Atractivos Turísticos
        </h1>
    </div>

    {{-- ── Layout: sidebar izquierdo + contenido derecho --}}
    <div class="flex gap-6 items-start">

        {{-- ════════════════════════════════
             SIDEBAR DE FILTROS
        ════════════════════════════════ --}}
        <aside class="w-64 shrink-0">
            <form method="GET" action="{{ route('catalogo.atractivos.index') }}" id="form-filtros-atractivos">
                <div class="tc-card p-5">
                    <h2 style="font-family: var(--font-display); font-size: 1.05rem; font-weight: 700; color: #1a2232;" class="mb-5">
                        Filtros
                    </h2>

                    {{-- Destinos --}}
                    <div class="mb-5">
                        <label class="block text-xs font-bold mb-2" style="color: var(--color-primary-700);">Destinos</label>
                        <select name="destino_id" class="tc-select text-sm"
                                onchange="document.getElementById('form-filtros-atractivos').submit()">
                            <option value="">Todos los destinos</option>
                            @foreach($destinos as $d)
                                <option value="{{ $d->id }}" @selected(($filtros['destino_id'] ?? '') == $d->id)>
                                    {{ $d->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Categorías como chips seleccionables --}}
                    <div class="mb-5">
                        <label class="block text-xs font-bold mb-2" style="color: var(--color-primary-700);">Categoría</label>
                        <div class="flex flex-wrap gap-2">
                            @foreach($categorias as $cat)
                                @php $seleccionada = ($filtros['categoria_id'] ?? '') == $cat->id; @endphp
                                <button type="submit" name="categoria_id" value="{{ $cat->id }}"
                                        class="text-xs font-medium px-3 py-1.5 rounded-full border transition-all"
                                        style="{{ $seleccionada
                                            ? 'background: var(--color-primary-500); color: white; border-color: var(--color-primary-500);'
                                            : 'background: white; color: #374151; border-color: #d1d5db;' }}">
                                    {{ $cat->nombre }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Costo mínimo (slider) --}}
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="text-xs font-bold" style="color: var(--color-primary-700);">Costo mínimo</label>
                            <span class="text-xs font-semibold" style="color: var(--color-primary-600);">
                                S/ <span id="label-costo-min">{{ $filtros['costo_min'] ?? 0 }}</span>
                            </span>
                        </div>
                        <input type="range" id="costo_min" name="costo_min"
                               min="0" max="1000" step="10"
                               value="{{ $filtros['costo_min'] ?? 0 }}"
                               class="tc-range w-full h-1.5 rounded-full appearance-none cursor-pointer"
                               oninput="document.getElementById('label-costo-min').textContent = this.value; updateRangeStyle(this, 'min')">
                    </div>

                    {{-- Costo máximo (slider) --}}
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="text-xs font-bold" style="color: var(--color-primary-700);">Costo máximo</label>
                            <span class="text-xs font-semibold" style="color: var(--color-primary-600);">
                                S/ <span id="label-costo-max">{{ $filtros['costo_max'] ?? 1000 }}</span>
                            </span>
                        </div>
                        <input type="range" id="costo_max" name="costo_max"
                               min="0" max="1000" step="10"
                               value="{{ $filtros['costo_max'] ?? 1000 }}"
                               class="tc-range w-full h-1.5 rounded-full appearance-none cursor-pointer"
                               oninput="document.getElementById('label-costo-max').textContent = this.value; updateRangeStyle(this, 'max')">
                    </div>

                    {{-- Limpiar filtros --}}
                    <a href="{{ route('catalogo.atractivos.index') }}"
                       class="block w-full text-center text-sm font-semibold py-2 rounded-lg border transition-colors"
                       style="border-color: #d1d5db; color: #6b7280;"
                       onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='transparent'">
                        Limpiar filtros
                    </a>
                </div>

                {{-- Aplicar sliders al hacer submit --}}
                <button type="submit" class="sr-only">Aplicar</button>
            </form>
        </aside>

        {{-- ════════════════════════════════
             CONTENIDO DERECHO
        ════════════════════════════════ --}}
        <div class="flex-1 min-w-0">

            {{-- Barra de búsqueda --}}
            <form method="GET" action="{{ route('catalogo.atractivos.index') }}" class="flex gap-2 mb-6">
                {{-- Preservar filtros activos en la búsqueda --}}
                @if(!empty($filtros['destino_id']))
                    <input type="hidden" name="destino_id" value="{{ $filtros['destino_id'] }}">
                @endif
                @if(!empty($filtros['categoria_id']))
                    <input type="hidden" name="categoria_id" value="{{ $filtros['categoria_id'] }}">
                @endif
                @if(!empty($filtros['costo_min']))
                    <input type="hidden" name="costo_min" value="{{ $filtros['costo_min'] }}">
                @endif
                @if(!empty($filtros['costo_max']))
                    <input type="hidden" name="costo_max" value="{{ $filtros['costo_max'] }}">
                @endif

                <input type="text" name="busqueda"
                       value="{{ $filtros['busqueda'] ?? '' }}"
                       placeholder="Buscar atractivos..."
                       class="tc-input flex-1">
                <button type="submit" class="tc-btn-primary px-5">
                    Buscar
                </button>
            </form>

            {{-- Grid de resultados --}}
            @if($atractivos->isEmpty())
                <div class="text-center py-20">
                    <svg class="w-14 h-14 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                    <p class="text-gray-500 font-medium">No se encontraron atractivos.</p>
                    <a href="{{ route('catalogo.atractivos.index') }}" class="mt-3 inline-block tc-btn-outline text-sm">
                        Ver todos
                    </a>
                </div>
            @else
                {{-- 2 columnas --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    @foreach($atractivos as $atractivo)
                        <a href="{{ route('catalogo.atractivos.show', $atractivo) }}"
                           class="tc-card overflow-hidden group flex flex-col">

                            {{-- Imagen --}}
                            <div class="relative overflow-hidden" style="aspect-ratio: 16/10; background: var(--color-teal-light);">
                                @if($atractivo->imagen_portada)
                                    <img src="{{ asset('storage/' . $atractivo->imagen_portada) }}"
                                         alt="{{ $atractivo->nombre }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <svg class="w-10 h-10" style="color: var(--color-primary-300);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </div>
                                @endif

                                {{-- Costo badge --}}
                                <span class="absolute top-2.5 right-2.5 text-xs font-bold px-2.5 py-1 rounded-full"
                                      style="background: rgba(255,255,255,0.92); color: var(--color-primary-700);">
                                    @if($atractivo->costo_entrada > 0)
                                        S/ {{ number_format($atractivo->costo_entrada, 0) }}
                                    @else
                                        Gratuito
                                    @endif
                                </span>
                            </div>

                            {{-- Info tarjeta --}}
                            <div class="p-4 flex flex-col flex-1">
                                <h3 style="font-family: var(--font-display); font-weight: 700; font-size: 1rem; color: #1a2232;" class="mb-1.5 leading-snug">
                                    {{ $atractivo->nombre }}
                                </h3>

                                {{-- Separador --}}
                                <div class="h-px mb-2" style="background: #f0f0f0;"></div>

                                {{-- Categorías --}}
                                @if($atractivo->categorias->isNotEmpty())
                                    <p class="text-xs font-medium mb-1" style="color: var(--color-primary-600);">
                                        {{ $atractivo->categorias->pluck('nombre')->take(2)->join(' · ') }}
                                    </p>
                                @endif

                                {{-- Lugar · Duración --}}
                                <div class="flex items-center gap-1.5 text-xs text-gray-400 mt-auto flex-wrap">
                                    @if($atractivo->destino)
                                        <span>{{ $atractivo->destino->nombre }}</span>
                                    @endif
                                    @if($atractivo->duracion_estimada_min)
                                        @php $h = intdiv($atractivo->duracion_estimada_min, 60); $m = $atractivo->duracion_estimada_min % 60; @endphp
                                        @if($atractivo->destino)<span>·</span>@endif
                                        <span>{{ $h > 0 ? "{$h}h " : '' }}{{ $m > 0 ? "{$m}min" : '' }}</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                {{-- Paginación --}}
                <div class="mt-8">
                    {{ $atractivos->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
// Aplicar sliders al soltar
['costo_min', 'costo_max'].forEach(function(id) {
    var el = document.getElementById(id);
    if (el) {
        el.addEventListener('change', function() {
            document.getElementById('form-filtros-atractivos').submit();
        });
    }
});
</script>
@endpush
@push('scripts')
<style>
.tc-range {
    -webkit-appearance: none;
    appearance: none;
    background: #e5e7eb;
    outline: none;
}
.tc-range::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: var(--color-primary-600);
    cursor: pointer;
    box-shadow: 0 1px 3px rgba(0,0,0,0.3);
    transition: transform 0.1s;
}
.tc-range::-webkit-slider-thumb:hover {
    transform: scale(1.15);
}
</style>
<script>
function updateRangeStyle(el, type) {
    var min = parseFloat(el.min) || 0;
    var max = parseFloat(el.max) || 100;
    var val = parseFloat(el.value);
    var pct = ((val - min) / (max - min)) * 100;
    
    // Para 'min': pinta el track a la DERECHA del thumb (desde el pct hasta 100%)
    // Para 'max': pinta el track a la IZQUIERDA del thumb (desde 0% hasta el pct)
    if (type === 'min') {
        el.style.background = 'linear-gradient(to right, #e5e7eb ' + pct + '%, var(--color-primary-500) ' + pct + '%)';
    } else {
        el.style.background = 'linear-gradient(to right, var(--color-primary-500) ' + pct + '%, #e5e7eb ' + pct + '%)';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    var cMin = document.getElementById('costo_min');
    var cMax = document.getElementById('costo_max');
    if(cMin) updateRangeStyle(cMin, 'min');
    if(cMax) updateRangeStyle(cMax, 'max');
});
</script>
@endpush
@endsection
