@extends('layouts.app')

@section('title', 'Mis preferencias')

@section('header')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <p class="text-sm text-gray-400 mb-0.5">Viajes personalizados</p>
        <h1 style="font-family: var(--font-display); font-size: 2rem; font-weight: 800; color: #1a2232; line-height: 1.15;">
            Mis preferencias
        </h1>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
    @if($preferencia)
        <div class="space-y-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 style="font-family: var(--font-display); font-size: 1.15rem; font-weight: 700; color: #1a2232;">Preferencias de viaje</h2>
                    <p class="mt-1 text-sm text-slate-500">Esta información será utilizada para generar recomendaciones personalizadas.</p>
                </div>
                <a href="{{ route('preferencias.edit', $preferencia->id) }}" class="tc-btn-primary">Editar preferencias</a>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="tc-card p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-slate-900">Presupuesto</h3>
                            <p class="mt-1 text-xs text-slate-500">Rango disponible para el viaje</p>
                        </div>
                        <div class="rounded-lg bg-primary-50 px-3 py-2 text-sm font-semibold text-primary-700">S/</div>
                    </div>
                    <div class="mt-5">
                        <p class="text-2xl font-bold text-slate-900">S/ {{ number_format($preferencia->presupuesto_max, 2) }}</p>
                        @if($preferencia->presupuesto_min !== null)
                            <p class="mt-1 text-sm text-slate-500">Desde S/ {{ number_format($preferencia->presupuesto_min, 2) }}</p>
                        @else
                            <p class="mt-1 text-sm text-slate-500">Sin presupuesto mínimo establecido</p>
                        @endif
                    </div>
                </div>

                <div class="tc-card p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-slate-900">Duración</h3>
                            <p class="mt-1 text-xs text-slate-500">Tiempo disponible para viajar</p>
                        </div>
                        <div class="rounded-lg bg-primary-50 px-3 py-2 text-sm font-semibold text-primary-700">Días</div>
                    </div>
                    <div class="mt-5">
                        <p class="text-2xl font-bold text-slate-900">{{ $preferencia->dias_disponibles }} {{ $preferencia->dias_disponibles === 1 ? 'día' : 'días' }}</p>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="tc-card p-6">
                    <h3 class="text-sm font-semibold text-slate-900">Ritmo del viaje</h3>
                    <p class="mt-1 text-xs text-slate-500">Intensidad preferida durante tus actividades</p>
                    <div class="mt-5">
                        @php
                            $ritmoLabel = match($preferencia->ritmo) {
                                'relajado' => 'Relajado',
                                'moderado' => 'Moderado',
                                'intenso' => 'Intenso',
                                default => ucfirst($preferencia->ritmo),
                            };
                        @endphp
                        <span class="inline-flex rounded-full px-3 py-1.5 text-sm font-medium {{ match($preferencia->ritmo) { 'relajado' => 'bg-emerald-50 text-emerald-700', 'moderado' => 'bg-amber-50 text-amber-700', 'intenso' => 'bg-orange-50 text-orange-700', default => 'bg-slate-100 text-slate-700' } }}">
                            {{ $ritmoLabel }}
                        </span>
                    </div>
                </div>

                <div class="tc-card p-6">
                    <h3 class="text-sm font-semibold text-slate-900">Horario diario</h3>
                    <p class="mt-1 text-xs text-slate-500">Horario preferido para iniciar y finalizar el día</p>
                    <div class="mt-5 flex items-center gap-3">
                        <div>
                            <p class="text-xs text-slate-500">Inicio</p>
                            <p class="text-lg font-semibold text-slate-900">{{ $preferencia->hora_inicio_dia ? substr($preferencia->hora_inicio_dia, 0, 5) : 'No definido' }}</p>
                        </div>
                        <span class="text-slate-400">→</span>
                        <div>
                            <p class="text-xs text-slate-500">Fin</p>
                            <p class="text-lg font-semibold text-slate-900">{{ $preferencia->hora_fin_dia ? substr($preferencia->hora_fin_dia, 0, 5) : 'No definido' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tc-card p-6">
                <h3 class="text-sm font-semibold text-slate-900">Categorías de interés</h3>
                <p class="mt-1 text-xs text-slate-500">Tipos de experiencias que deseas encontrar.</p>

                @if($preferencia->categorias->isNotEmpty())
                    <div class="mt-5 flex flex-wrap gap-2">
                        @foreach($preferencia->categorias as $categoria)
                            <span class="inline-flex rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-sm font-medium text-slate-700">
                                {{ $categoria->nombre }}
                            </span>
                        @endforeach
                    </div>
                @else
                    <div class="mt-5 rounded-lg border border-dashed border-slate-300 bg-slate-50 px-4 py-5 text-sm text-slate-500">
                        No has añadido categorías de interés aún.
                    </div>
                @endif
            </div>

            <div class="tc-card p-6">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900">Estado de preferencias</h3>
                        <p class="mt-1 text-xs text-slate-500">Indica si estas preferencias se encuentran actualmente activas.</p>
                    </div>
                    @if($preferencia->vigente)
                        <span class="inline-flex w-fit rounded-full bg-emerald-50 px-3 py-1.5 text-sm font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                            Vigente
                        </span>
                    @else
                        <span class="inline-flex w-fit rounded-full bg-slate-100 px-3 py-1.5 text-sm font-medium text-slate-600 ring-1 ring-inset ring-slate-500/20">
                            No vigente
                        </span>
                    @endif
                </div>
            </div>

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <a href="{{ route('preferencias.index') }}" class="tc-btn-outline">Volver</a>
                <a href="{{ route('preferencias.edit', $preferencia->id) }}" class="tc-btn-primary">Editar preferencias</a>
            </div>
        </div>
    @else
        <div class="rounded-xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center tc-card">
            <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-xl text-slate-500">+</div>
            <h2 class="text-lg font-semibold text-slate-900">Aún no tienes preferencias registradas</h2>
            <p class="mx-auto mt-2 max-w-md text-sm text-slate-500">Registra tus preferencias de viaje para que la plataforma pueda ofrecerte recomendaciones más personalizadas.</p>
            <div class="mt-6">
                <a href="{{ route('preferencias.create') }}" class="tc-btn-primary">Registrar mis preferencias</a>
            </div>
        </div>
    @endif
</div>
@endsection