@php
    $mapId = $mapId ?? 'mapa-ruta-paradas';
    $mapEyebrow = $mapEyebrow ?? 'Vista geográfica';
    $mapTitle = $mapTitle ?? 'Mapa del recorrido';
    $mapDescription = $mapDescription ?? 'Las paradas se ubican con las coordenadas publicadas en sus atractivos.';
    $mapLabel = $mapLabel ?? 'Mapa con las paradas de la ruta';
@endphp

<section class="tc-card p-5 sm:p-7" aria-labelledby="{{ $mapId }}-heading">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.13em] text-primary-600">{{ $mapEyebrow }}</p>
            <h2 id="{{ $mapId }}-heading" class="mt-2 text-2xl font-bold text-gray-900">{{ $mapTitle }}</h2>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-gray-600">{{ $mapDescription }}</p>
        </div>
        <span id="{{ $mapId }}-status" class="rounded-full bg-primary-50 px-3 py-1.5 text-xs font-semibold text-primary-700">Preparando paradas</span>
    </div>

    <div class="relative mt-5 overflow-hidden rounded-2xl border border-primary-100 bg-primary-50/60">
        <div id="{{ $mapId }}" class="hidden h-80 w-full" aria-label="{{ $mapLabel }}"></div>
        <div id="{{ $mapId }}-fallback" class="flex min-h-[20rem] items-center justify-center p-5" role="status" aria-live="polite">
            <div class="max-w-md text-center">
                <svg class="mx-auto h-10 w-10 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M5 5h7a4 4 0 0 1 0 8H9a3 3 0 0 0 0 6h10"/><circle cx="5" cy="5" r="1.5"/><circle cx="19" cy="19" r="1.5"/></svg>
                <p class="mt-3 font-semibold text-slate-800">La vista previa se construye con las paradas de la ruta.</p>
                <p class="mt-1 text-sm leading-6 text-slate-500">Agrega atractivos que tengan coordenadas publicadas para verlos ubicados en el mapa.</p>
            </div>
        </div>
    </div>

    <p class="mt-3 text-xs leading-5 text-slate-500">El mapa respeta el orden de las paradas. La distancia y duración registradas en esta ruta siguen siendo referenciales.</p>
</section>
