@extends('layouts.app')

@section('title', 'Preferencias de viaje recientes')

@section('header')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <p class="text-sm text-gray-400 mb-0.5">Preferencias</p>
        <h1 style="font-family: var(--font-display); font-size: 2rem; font-weight: 800; color: #1a2232; line-height: 1.15;">
            Preferencias de viaje recientes
        </h1>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm text-slate-500">Resumen de las últimas 10 preferencias registradas.</p>
        </div>
        <span class="inline-flex items-center gap-2 rounded-full bg-primary-50 px-4 py-2 text-sm font-medium text-primary-700 ring-1 ring-primary-200">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Últimas 10
        </span>
    </div>

    <section class="mb-6">
        <div class="tc-card overflow-hidden">
            <div class="border-b border-slate-200 px-6 py-5">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V6m0 6v2m0 4v-2m0 2c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">Presupuestos más recientes</h2>
                        <p class="text-sm text-slate-500">Rangos de presupuesto registrados.</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                @if ($presupuestos->isEmpty())
                    <div class="py-8 text-center">
                        <p class="text-sm text-slate-500">No existen presupuestos registrados.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($presupuestos as $presupuesto)
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-5">
                                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Rango de presupuesto</p>
                                <p class="mt-2 text-xl font-bold text-slate-900">
                                    S/ {{ number_format((float) $presupuesto['min'], 2) }}
                                    <span class="font-normal text-slate-400">—</span>
                                    S/ {{ number_format((float) $presupuesto['max'], 2) }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </section>

    <div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
        <section class="tc-card overflow-hidden">
            <div class="border-b border-slate-200 px-6 py-5">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary-50 text-primary-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">Duraciones más recientes</h2>
                        <p class="text-sm text-slate-500">Cantidad de días solicitados.</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                @if ($duraciones->isEmpty())
                    <p class="py-8 text-center text-sm text-slate-500">No existen datos de duración.</p>
                @else
                    <div class="space-y-4">
                        @foreach ($duraciones as $dias => $cantidad)
                            <div>
                                <div class="mb-2 flex items-center justify-between">
                                    <span class="text-sm font-medium text-slate-700">{{ $dias }} {{ $dias == 1 ? 'día' : 'días' }}</span>
                                    <span class="text-sm font-semibold text-slate-900">{{ $cantidad }}</span>
                                </div>
                                <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                                    <div class="h-full rounded-full bg-primary-500" style="width: {{ ($cantidad / max(1, $preferencias->count())) * 100 }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        <section class="tc-card overflow-hidden">
            <div class="border-b border-slate-200 px-6 py-5">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-50 text-violet-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">Ritmos de viaje</h2>
                        <p class="text-sm text-slate-500">Preferencias según ritmo de viaje.</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                @if ($ritmos->isEmpty())
                    <p class="py-8 text-center text-sm text-slate-500">No existen datos de ritmo.</p>
                @else
                    <div class="space-y-4">
                        @foreach ($ritmos as $ritmo => $cantidad)
                            @php
                                $nombreRitmo = ucfirst($ritmo);
                            @endphp
                            <div>
                                <div class="mb-2 flex items-center justify-between">
                                    <span class="text-sm font-medium text-slate-700">{{ $nombreRitmo }}</span>
                                    <span class="text-sm font-semibold text-slate-900">{{ $cantidad }}</span>
                                </div>
                                <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                                    <div class="h-full rounded-full bg-violet-500" style="width: {{ ($cantidad / max(1, $preferencias->count())) * 100 }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    </div>

    <section class="tc-card overflow-hidden">
        <div class="border-b border-slate-200 px-6 py-5">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-700">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M3 11l8.49-8.49a2 2 0 012.83 0L21 9.17a2 2 0 010 2.83L12.5 20.5a2 2 0 01-2.83 0L3 13.83A2 2 0 013 11z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Categorías de interés</h2>
                    <p class="text-sm text-slate-500">Categorías asociadas a las últimas preferencias.</p>
                </div>
            </div>
        </div>

        <div class="p-6">
            @if ($categorias->isEmpty())
                <div class="py-8 text-center">
                    <p class="text-sm text-slate-500">No existen categorías registradas.</p>
                </div>
            @else
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($categorias as $categoria)
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-5">
                            <div class="flex items-center justify-between gap-4">
                                <span class="text-sm font-medium text-slate-700">{{ $categoria['nombre'] }}</span>
                                <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">{{ $categoria['cantidad'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <div class="mt-6 rounded-xl border border-primary-100 bg-primary-50 px-5 py-4">
        <div class="flex gap-3">
            <svg class="mt-0.5 h-5 w-5 shrink-0 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 21a9 9 0 100-18 9 9 0 000 18z" />
            </svg>
            <p class="text-sm leading-6 text-primary-800">
                La información mostrada corresponde únicamente a un resumen
                de las últimas 10 preferencias registradas y no incluye
                datos personales de los turistas.
            </p>
        </div>
    </div>
</div>
@endsection
