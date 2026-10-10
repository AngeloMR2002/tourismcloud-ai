@extends('layouts.app')

@php
    $esTurista = $esTurista ?? false;
@endphp

@section('title', 'Editar Preferencia')

@section('header')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <p class="text-sm text-gray-400 mb-0.5">Viajes personalizados</p>
        <h1 style="font-family: var(--font-display); font-size: 2rem; font-weight: 800; color: #1a2232; line-height: 1.15;">
            Editar preferencia
        </h1>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
    <form action="{{ $esTurista ? route('preferencias.turista.update', ['turistaId' => $preferencia->turista_id, 'preferenciaId' => $preferencia->id]) : route('preferencias.update', $preferencia->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <section class="tc-card p-0 overflow-hidden">
            <div class="border-b border-slate-200 px-6 py-5">
                <h2 style="font-family: var(--font-display); font-size: 1.05rem; font-weight: 700; color: #1a2232;">Información del turista</h2>
                <p class="mt-1 text-sm text-slate-500">
                    @if($esTurista)
                        Tu información asociada a estas preferencias.
                    @else
                        Turista asociado a esta preferencia.
                    @endif
                </p>
            </div>

            <div class="p-6">
                @if(!$esTurista)
                    <label for="turista_id" class="block text-xs font-bold mb-2" style="color: var(--color-primary-700);">Turista</label>
                    <select name="turista_id" id="turista_id" required class="tc-select">
                        @if ($preferencia->turista)
                            <option value="{{ $preferencia->turista->id }}" selected>
                                {{ $preferencia->turista->nombre }} - {{ $preferencia->turista->email }}
                            </option>
                        @else
                            <option value="">Sin turista asociado</option>
                        @endif
                    </select>

                    @error('turista_id')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                @else
                    <p class="text-sm font-medium text-slate-700">Turista</p>
                    @if($preferencia->turista)
                        <div class="mt-2 rounded-lg border border-slate-200 bg-slate-50 px-4 py-3">
                            <p class="text-sm font-semibold text-slate-900">{{ $preferencia->turista->nombre }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $preferencia->turista->email }}</p>
                        </div>
                    @else
                        <div class="mt-2 rounded-lg border border-red-200 bg-red-50 px-4 py-3">
                            <p class="text-sm text-red-600">No se encontró información del turista.</p>
                        </div>
                    @endif
                @endif
            </div>
        </section>

        <section class="tc-card p-0 overflow-hidden">
            <div class="border-b border-slate-200 px-6 py-5">
                <h2 style="font-family: var(--font-display); font-size: 1.05rem; font-weight: 700; color: #1a2232;">Presupuesto y duración</h2>
                <p class="mt-1 text-sm text-slate-500">Actualiza el presupuesto y el tiempo disponible para el viaje.</p>
            </div>

            <div class="grid gap-6 p-6 md:grid-cols-3">
                <div>
                    <label for="presupuesto_min" class="block text-xs font-bold mb-2" style="color: var(--color-primary-700);">Presupuesto mínimo</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-sm text-slate-500">S/</span>
                        <input type="number" name="presupuesto_min" id="presupuesto_min" step="0.01" min="0" value="{{ old('presupuesto_min', $preferencia->presupuesto_min) }}" placeholder="0.00" class="tc-input pl-9">
                    </div>
                    @error('presupuesto_min')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="presupuesto_max" class="block text-xs font-bold mb-2" style="color: var(--color-primary-700);">Presupuesto máximo</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-sm text-slate-500">S/</span>
                        <input type="number" name="presupuesto_max" id="presupuesto_max" step="0.01" min="0" value="{{ old('presupuesto_max', $preferencia->presupuesto_max) }}" placeholder="0.00" required class="tc-input pl-9">
                    </div>
                    @error('presupuesto_max')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="dias_disponibles" class="block text-xs font-bold mb-2" style="color: var(--color-primary-700);">Días disponibles</label>
                    <div class="relative">
                        <input type="number" name="dias_disponibles" id="dias_disponibles" min="1" value="{{ old('dias_disponibles', $preferencia->dias_disponibles) }}" placeholder="Ej. 5" required class="tc-input pr-16">
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-xs text-slate-400">días</span>
                    </div>
                    @error('dias_disponibles')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>

        <section class="tc-card p-0 overflow-hidden">
            <div class="border-b border-slate-200 px-6 py-5">
                <h2 style="font-family: var(--font-display); font-size: 1.05rem; font-weight: 700; color: #1a2232;">Ritmo y horario</h2>
                <p class="mt-1 text-sm text-slate-500">Actualiza la intensidad y el horario habitual del día.</p>
            </div>

            <div class="grid gap-6 p-6 md:grid-cols-3">
                <div>
                    <label for="ritmo" class="block text-xs font-bold mb-2" style="color: var(--color-primary-700);">Ritmo del viaje</label>
                    <select name="ritmo" id="ritmo" required class="tc-select">
                        <option value="relajado" {{ old('ritmo', $preferencia->ritmo) === 'relajado' ? 'selected' : '' }}>Relajado</option>
                        <option value="moderado" {{ old('ritmo', $preferencia->ritmo) === 'moderado' ? 'selected' : '' }}>Moderado</option>
                        <option value="intenso" {{ old('ritmo', $preferencia->ritmo) === 'intenso' ? 'selected' : '' }}>Intenso</option>
                    </select>
                    @error('ritmo')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="hora_inicio_dia" class="block text-xs font-bold mb-2" style="color: var(--color-primary-700);">Hora de inicio</label>
                    <input type="time" name="hora_inicio_dia" id="hora_inicio_dia" value="{{ old('hora_inicio_dia', $preferencia->hora_inicio_dia ? substr($preferencia->hora_inicio_dia, 0, 5) : '') }}" class="tc-input">
                    @error('hora_inicio_dia')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="hora_fin_dia" class="block text-xs font-bold mb-2" style="color: var(--color-primary-700);">Hora de fin</label>
                    <input type="time" name="hora_fin_dia" id="hora_fin_dia" value="{{ old('hora_fin_dia', $preferencia->hora_fin_dia ? substr($preferencia->hora_fin_dia, 0, 5) : '') }}" class="tc-input">
                    @error('hora_fin_dia')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>

        <section class="tc-card p-0 overflow-hidden">
            <div class="border-b border-slate-200 px-6 py-5">
                <h2 style="font-family: var(--font-display); font-size: 1.05rem; font-weight: 700; color: #1a2232;">Categorías de interés</h2>
                <p class="mt-1 text-sm text-slate-500">Actualiza las categorías asociadas a esta preferencia.</p>
            </div>

            <div class="p-6">
                @forelse ($categorias as $categoria)
                    <label class="mb-3 flex cursor-pointer items-center rounded-lg border border-slate-200 p-3 transition hover:border-primary-300 hover:bg-primary-50/40">
                        <input type="checkbox" name="categorias[]" value="{{ $categoria->id }}" {{ in_array($categoria->id, old('categorias', $preferencia->categorias->pluck('id')->toArray())) ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                        <span class="ml-3 text-sm font-medium text-slate-700">{{ $categoria->nombre }}</span>
                    </label>
                @empty
                    <div class="rounded-lg border border-dashed border-slate-300 bg-slate-50 px-4 py-6 text-center">
                        <p class="text-sm text-slate-500">No existen categorías de interés registradas.</p>
                    </div>
                @endforelse

                @error('categorias')
                    <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </section>

        <section class="tc-card p-6">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-sm font-semibold text-slate-900">Preferencia vigente</h2>
                    <p class="mt-1 text-sm text-slate-500">Indica si esta preferencia se encuentra actualmente activa.</p>
                </div>

                <label class="relative inline-flex cursor-pointer items-center">
                    <input type="checkbox" name="vigente" value="1" {{ old('vigente', $preferencia->vigente) ? 'checked' : '' }} class="peer sr-only">
                    <div class="h-6 w-11 rounded-full bg-slate-300 transition peer-checked:bg-primary-500 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary-500/30 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-slate-300 after:bg-white after:transition-all peer-checked:after:translate-x-full peer-checked:after:border-white"></div>
                </label>
            </div>
        </section>

        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <a href="{{ $esTurista ? route('preferencias.turista', ['turistaId' => $preferencia->turista_id]) : route('preferencias.index') }}" class="tc-btn-outline">Cancelar</a>
            <button type="submit" class="tc-btn-primary">Guardar cambios</button>
        </div>
    </form>
</div>
@endsection
