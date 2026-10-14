@extends('layouts.app', ['title' => $actividad->nombre])

@section('content')
    @php
        $portadaUrl = $actividad->portada_url;
        $minutos = $actividad->duracion_estimada_min;
        $horas = $minutos ? intdiv($minutos, 60) : 0;
        $restoMinutos = $minutos ? $minutos % 60 : 0;
        $esFotoPlazaDemo = $actividad->imagen_portada === 'actividades-rutas-demo/cusco-plaza.jpg';
        $esFotoSacsayhuamanDemo = $actividad->imagen_portada === 'actividades-rutas-demo/cusco-sacsayhuaman.jpg';
    @endphp

    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-7 sm:py-10">
        <a href="{{ route('catalogo.actividades.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold hover:underline" style="color: var(--color-primary-600);">
            <span aria-hidden="true">←</span> Volver a actividades
        </a>

        <div class="mt-5 overflow-hidden rounded-2xl border border-gray-100 shadow-sm h-56 sm:h-80 lg:h-96">
            @if($portadaUrl)
                <img src="{{ $portadaUrl }}" alt="{{ $actividad->nombre }}" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full flex items-center justify-center" style="background: linear-gradient(135deg, var(--color-teal-light), #c8e8eb); color: var(--color-primary-500);">
                    <svg class="w-20 h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.3" d="M12 2a4 4 0 014 4v1.1a7 7 0 014.9 6.65V16a2 2 0 01-2 2H5.1a2 2 0 01-2-2v-2.25A7 7 0 018 7.1V6a4 4 0 014-4zM9 22h6" />
                    </svg>
                </div>
            @endif
        </div>
        @if($portadaUrl && $esFotoPlazaDemo)
            <p class="mt-2 text-xs text-gray-500">
                Foto de contexto: <a href="https://commons.wikimedia.org/wiki/File:The_Plaza_de_Armas_of_Cusco.jpg" target="_blank" rel="noopener noreferrer" class="underline">Entropy1963</a>, dominio público.
            </p>
        @elseif($portadaUrl && $esFotoSacsayhuamanDemo)
            <p class="mt-2 text-xs text-gray-500">
                Foto de contexto: <a href="https://commons.wikimedia.org/wiki/File:Sacsayhuaman_in_Cusco.jpg" target="_blank" rel="noopener noreferrer" class="underline">Filipe Fortes</a>, <a href="https://creativecommons.org/licenses/by-sa/2.0/" target="_blank" rel="license noopener noreferrer" class="underline">CC BY-SA 2.0</a>.
            </p>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_18rem] gap-7 mt-7">
            <section>
                <p class="text-sm font-semibold" style="color: var(--color-primary-600);">{{ $actividad->destino->nombre }}</p>
                <h1 class="mt-1 text-3xl sm:text-4xl font-extrabold tracking-tight text-gray-900">{{ $actividad->nombre }}</h1>

                @if($actividad->descripcion)
                    <div class="tc-card mt-6 p-5 sm:p-6">
                        <h2 class="text-sm font-bold uppercase tracking-wide" style="color: var(--color-primary-700);">Sobre esta actividad</h2>
                        <p class="mt-3 text-gray-600 leading-relaxed whitespace-pre-line">{{ $actividad->descripcion }}</p>
                    </div>
                @endif

                @if($actividad->ubicacion_url)
                    <div class="tc-card mt-5 p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <h2 class="font-bold text-gray-800">Ubicación registrada</h2>
                            <p class="mt-1 text-sm text-gray-500">Consulta el punto de encuentro o referencia en el mapa.</p>
                        </div>
                        <a href="{{ $actividad->ubicacion_url }}" target="_blank" rel="noopener noreferrer" class="tc-btn-outline justify-center shrink-0">Abrir en mapa ↗</a>
                    </div>
                @endif
            </section>

            <aside class="space-y-4">
                <div class="tc-card p-5">
                    <p class="text-xs font-bold uppercase tracking-wide" style="color: var(--color-primary-700);">Costo estimado</p>
                    <p class="mt-2 text-3xl font-extrabold text-gray-900">
                        @if($actividad->costo > 0)
                            S/ {{ number_format((float) $actividad->costo, 2) }}
                        @else
                            <span class="text-green-600">Gratuita</span>
                        @endif
                    </p>
                    <p class="mt-1 text-xs text-gray-400">Verifica el precio final con el proveedor.</p>
                </div>
                <div class="tc-card p-5">
                    <p class="text-xs font-bold uppercase tracking-wide" style="color: var(--color-primary-700);">Duración estimada</p>
                    <p class="mt-2 text-2xl font-extrabold text-gray-900">
                        @if($minutos)
                            {{ $horas > 0 ? $horas.' h' : '' }}{{ $horas > 0 && $restoMinutos > 0 ? ' ' : '' }}{{ $restoMinutos > 0 ? $restoMinutos.' min' : '' }}
                        @else
                            Por confirmar
                        @endif
                    </p>
                </div>
                <div class="rounded-xl p-4 text-sm leading-relaxed" style="background: var(--color-amber-soft); color: var(--color-tertiary-700);">
                    Esta información es referencial. Confirma la disponibilidad antes de realizar tu visita.
                </div>
            </aside>
        </div>
    </main>
@endsection
