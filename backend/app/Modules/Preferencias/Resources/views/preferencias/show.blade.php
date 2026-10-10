@extends('layouts.app')

@section('title', 'Detalle de Preferencia')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <p class="text-xs uppercase tracking-wider font-semibold text-neutral-400">Preferencias de Viaje</p>
            <h1 class="text-2xl font-bold text-gray-900 mt-1">Detalle de Preferencia #{{ $preferencia->id }}</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ auth()->user()->isTurista() ? route('preferencias.turista', auth()->id()) : route('preferencias.index') }}" class="px-4 py-2 border border-gray-200 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors shadow-sm">
                Volver
            </a>
            <a href="{{ route('preferencias.edit', $preferencia->id) }}" class="px-4 py-2 bg-[#00626A] text-white rounded-xl text-sm font-semibold hover:bg-[#004e55] transition-colors shadow-sm">
                Editar
            </a>
        </div>
    </div>

    <div class="space-y-6">
        {{-- Turista --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-semibold text-gray-900 mb-4">Turista</h2>
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-[#00626A]/10 text-[#00626A] flex items-center justify-center font-bold text-base">
                    {{ substr($preferencia->turista->nombre ?? 'T', 0, 1) }}{{ substr($preferencia->turista->apellido ?? '', 0, 1) }}
                </div>
                <div>
                    <p class="text-base font-bold text-gray-900">{{ $preferencia->turista->nombre ?? 'Sin turista' }} {{ $preferencia->turista->apellido ?? '' }}</p>
                    <p class="text-sm text-gray-500">{{ $preferencia->turista->email ?? '' }}</p>
                </div>
            </div>
        </div>

        {{-- Parámetros Principales --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Presupuesto Máximo</p>
                <p class="text-2xl font-black text-[#00626A] mt-2">S/ {{ number_format($preferencia->presupuesto_max, 2) }}</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Días Disponibles</p>
                <p class="text-2xl font-black text-gray-900 mt-2">
                    {{ $preferencia->dias_disponibles }} <span class="text-sm font-normal text-gray-500">{{ $preferencia->dias_disponibles == 1 ? 'día' : 'días' }}</span>
                </p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Horario Habitual</p>
                <p class="text-lg font-bold text-gray-900 mt-2">
                    @if($preferencia->hora_inicio_preferida && $preferencia->hora_fin_preferida)
                        {{ substr($preferencia->hora_inicio_preferida, 0, 5) }} - {{ substr($preferencia->hora_fin_preferida, 0, 5) }}
                    @else
                        <span class="text-gray-400 text-sm font-normal">No especificado</span>
                    @endif
                </p>
            </div>
        </div>

        {{-- Categorías de Interés --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-semibold text-gray-900 mb-3">Categorías de Interés Favoritas</h2>
            @if($preferencia->categorias->isNotEmpty())
                <div class="flex flex-wrap gap-2">
                    @foreach($preferencia->categorias as $cat)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-sm font-semibold bg-[#00626A]/10 text-[#00626A] border border-[#00626A]/20">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            {{ $cat->nombre }}
                        </span>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-400">No hay categorías seleccionadas.</p>
            @endif
        </div>

        <div class="flex items-center justify-between pt-2">
            <span class="text-xs text-gray-400">Registrado el {{ $preferencia->created_at?->format('d/m/Y H:i') }}</span>
            <form action="{{ route('preferencias.destroy', $preferencia->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar esta preferencia?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 text-xs font-medium text-red-600 hover:text-red-700 hover:bg-red-50 rounded-xl transition-colors">
                    Eliminar Preferencia
                </button>
            </form>
        </div>
    </div>
</div>
@endsection