@extends('layouts.app')

@section('title', 'Tendencias de Preferencias de Turistas')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-xs uppercase tracking-wider font-semibold text-neutral-400">Demanda Turística</p>
            <h1 class="text-2xl font-bold text-gray-900 mt-1">Tendencias de Preferencias Recientes</h1>
            <p class="text-sm text-gray-500 mt-1">Analiza presupuestos, días promedio y actividades más solicitadas por turistas.</p>
        </div>
        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-[#00626A]/10 text-[#00626A] w-fit">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            Últimas 10 Registradas
        </span>
    </div>

    {{-- Presupuestos --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <h2 class="text-base font-semibold text-gray-900 mb-1">Presupuestos Máximos Solicitados</h2>
        <p class="text-xs text-gray-500 mb-4">Rangos de gasto proyectados por los turistas en sus viajes.</p>

        @if ($presupuestos->isEmpty())
            <p class="text-sm text-gray-400 py-4 text-center">No hay datos de presupuestos recientes.</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
                @foreach ($presupuestos as $presupuesto)
                    <div class="p-4 rounded-xl border border-gray-100 bg-gray-50/75">
                        <p class="text-xs text-gray-400 font-medium">Presupuesto Máximo</p>
                        <p class="text-lg font-bold text-[#00626A] mt-1">
                            S/ {{ number_format((float) $presupuesto['max'], 2) }}
                        </p>
                        <p class="text-[10px] text-gray-400 mt-1">
                            {{ $presupuesto['fecha'] ? \Carbon\Carbon::parse($presupuesto['fecha'])->diffForHumans() : '' }}
                        </p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Duraciones --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-semibold text-gray-900 mb-1">Días Disponibles Más Solicitados</h2>
            <p class="text-xs text-gray-500 mb-4">Frecuencia de estadías planeadas por los turistas.</p>

            @if ($duraciones->isEmpty())
                <p class="text-sm text-gray-400 py-4 text-center">No hay datos de duraciones registradas.</p>
            @else
                <div class="space-y-3">
                    @foreach ($duraciones as $dias => $cantidad)
                        <div>
                            <div class="flex items-center justify-between text-sm mb-1">
                                <span class="font-medium text-gray-700">{{ $dias }} {{ $dias == 1 ? 'día' : 'días' }}</span>
                                <span class="font-bold text-gray-900">{{ $cantidad }} turista(s)</span>
                            </div>
                            <div class="h-2 rounded-full bg-gray-100 overflow-hidden">
                                <div class="h-full rounded-full bg-[#00626A]" style="width: {{ ($cantidad / max(1, $preferencias->count())) * 100 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Categorías de Interés --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-semibold text-gray-900 mb-1">Categorías de Mayor Interés</h2>
            <p class="text-xs text-gray-500 mb-4">Actividades y experiencias más demandadas.</p>

            @if ($categorias->isEmpty())
                <p class="text-sm text-gray-400 py-4 text-center">No hay categorías registradas.</p>
            @else
                <div class="space-y-3">
                    @foreach ($categorias as $cat)
                        <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 border border-gray-100">
                            <span class="text-sm font-semibold text-gray-800">{{ $cat['nombre'] }}</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#00626A]/10 text-[#00626A]">
                                {{ $cat['cantidad'] }} interés{{ $cat['cantidad'] == 1 ? '' : 'es' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="rounded-xl border border-[#00626A]/20 bg-[#00626A]/5 p-4 flex items-center gap-3">
        <svg class="w-5 h-5 text-[#00626A] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p class="text-xs text-neutral-600">
            Esta información anónima resume las tendencias recientes para que prestadores de servicios y operadores adapten su oferta a la demanda real de los visitantes.
        </p>
    </div>
</div>
@endsection