@extends('layouts.app')

@section('title', 'Detalle de Preferencia')

@section('header')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <p class="text-sm text-gray-400 mb-0.5">Viajes personalizados</p>
        <h1 style="font-family: var(--font-display); font-size: 2rem; font-weight: 800; color: #1a2232; line-height: 1.15;">
            Detalle de preferencia
        </h1>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm text-slate-500">Consulta la información registrada para este turista.</p>
        </div>
        <a href="{{ route('preferencias.edit', $preferencia->id) }}" class="tc-btn-primary">Editar preferencia</a>
    </div>

    <div class="space-y-6">
        <section class="tc-card p-0 overflow-hidden">
            <div class="flex flex-col gap-3 border-b border-slate-200 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 style="font-family: var(--font-display); font-size: 1.05rem; font-weight: 700; color: #1a2232;">Información general</h2>
                    <p class="mt-1 text-sm text-slate-500">Datos principales de la preferencia registrada.</p>
                </div>
                <div>
                    @if ($preferencia->vigente)
                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">Vigente</span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600 ring-1 ring-inset ring-slate-500/20">No vigente</span>
                    @endif
                </div>
            </div>

            <div class="grid gap-6 p-6 sm:grid-cols-2">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-500">ID de preferencia</p>
                    <p class="mt-1 text-sm font-semibold text-slate-900">#{{ $preferencia->id }}</p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Turista</p>
                    @if ($preferencia->turista)
                        <p class="mt-1 text-sm font-semibold text-slate-900">{{ $preferencia->turista->nombre }}</p>
                        <p class="mt-0.5 text-xs text-slate-500">{{ $preferencia->turista->email }}</p>
                    @else
                        <p class="mt-1 text-sm text-slate-500">Sin turista asociado</p>
                    @endif
                </div>
            </div>
        </section>

        <section class="tc-card p-0 overflow-hidden">
            <div class="border-b border-slate-200 px-6 py-5">
                <h2 style="font-family: var(--font-display); font-size: 1.05rem; font-weight: 700; color: #1a2232;">Presupuesto y duración</h2>
                <p class="mt-1 text-sm text-slate-500">Información relacionada con el tiempo y presupuesto disponible.</p>
            </div>

            <div class="grid gap-6 p-6 sm:grid-cols-3">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Presupuesto mínimo</p>
                    <p class="mt-1 text-lg font-semibold text-slate-900">
                        @if ($preferencia->presupuesto_min !== null)
                            S/ {{ number_format($preferencia->presupuesto_min, 2) }}
                        @else
                            <span class="text-sm font-normal text-slate-400">No especificado</span>
                        @endif
                    </p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Presupuesto máximo</p>
                    <p class="mt-1 text-lg font-semibold text-slate-900">S/ {{ number_format($preferencia->presupuesto_max, 2) }}</p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Días disponibles</p>
                    <p class="mt-1 text-lg font-semibold text-slate-900">
                        {{ $preferencia->dias_disponibles }}
                        <span class="text-sm font-normal text-slate-500">{{ $preferencia->dias_disponibles == 1 ? 'día' : 'días' }}</span>
                    </p>
                </div>
            </div>
        </section>

        <section class="tc-card p-0 overflow-hidden">
            <div class="border-b border-slate-200 px-6 py-5">
                <h2 style="font-family: var(--font-display); font-size: 1.05rem; font-weight: 700; color: #1a2232;">Ritmo y horario</h2>
                <p class="mt-1 text-sm text-slate-500">Preferencias relacionadas con la intensidad y el horario del viaje.</p>
            </div>

            <div class="grid gap-6 p-6 sm:grid-cols-3">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Ritmo del viaje</p>
                    @php
                        $ritmoClasses = [
                            'relajado' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
                            'moderado' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
                            'intenso' => 'bg-orange-50 text-orange-700 ring-orange-600/20',
                        ];
                    @endphp
                    <div class="mt-2">
                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-medium ring-1 ring-inset {{ $ritmoClasses[$preferencia->ritmo] ?? 'bg-slate-50 text-slate-600 ring-slate-500/20' }}">
                            {{ ucfirst($preferencia->ritmo) }}
                        </span>
                    </div>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Hora de inicio</p>
                    <p class="mt-1 text-sm font-semibold text-slate-900">
                        @if ($preferencia->hora_inicio_dia)
                            {{ substr($preferencia->hora_inicio_dia, 0, 5) }}
                        @else
                            <span class="font-normal text-slate-400">No especificada</span>
                        @endif
                    </p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Hora de fin</p>
                    <p class="mt-1 text-sm font-semibold text-slate-900">
                        @if ($preferencia->hora_fin_dia)
                            {{ substr($preferencia->hora_fin_dia, 0, 5) }}
                        @else
                            <span class="font-normal text-slate-400">No especificada</span>
                        @endif
                    </p>
                </div>
            </div>
        </section>

        <section class="tc-card p-0 overflow-hidden">
            <div class="border-b border-slate-200 px-6 py-5">
                <h2 style="font-family: var(--font-display); font-size: 1.05rem; font-weight: 700; color: #1a2232;">Categorías de interés</h2>
                <p class="mt-1 text-sm text-slate-500">Categorías asociadas a las preferencias del turista.</p>
            </div>

            <div class="p-6">
                @forelse ($preferencia->categorias as $categoria)
                    <span class="mb-2 mr-2 inline-flex rounded-lg bg-primary-50 px-3 py-2 text-sm font-medium text-primary-700 ring-1 ring-inset ring-primary-600/20">
                        {{ $categoria->nombre }}
                    </span>
                @empty
                    <div class="rounded-lg border border-dashed border-slate-300 bg-slate-50 px-4 py-6 text-center">
                        <p class="text-sm text-slate-500">No se han asociado categorías de interés.</p>
                    </div>
                @endforelse
            </div>
        </section>

        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-between">
            <a href="{{ route('preferencias.index') }}" class="tc-btn-outline">Volver a preferencias</a>

            <div class="flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('preferencias.edit', $preferencia->id) }}" class="tc-btn-primary">Editar preferencia</a>
                <form action="{{ route('preferencias.destroy', $preferencia->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-lg border border-red-200 bg-white px-5 py-2.5 text-sm font-semibold text-red-600 shadow-sm transition hover:bg-red-50 hover:text-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:w-auto">
                        Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection