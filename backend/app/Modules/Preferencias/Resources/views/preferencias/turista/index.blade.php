@extends('layouts.app')

@section('title', 'Mis Preferencias')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <p class="text-xs uppercase tracking-wider font-semibold text-neutral-400">Perfil Turista</p>
            <h1 class="text-2xl font-bold text-gray-900 mt-1">Mis Preferencias de Viaje</h1>
        </div>
        @if($preferencia)
            <a href="{{ route('preferencias.turista.edit', ['turistaId' => auth()->id(), 'preferenciaId' => $preferencia->id]) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-[#00626A] text-white text-sm font-semibold rounded-xl hover:bg-[#004e55] shadow-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                Editar Preferencias
            </a>
        @endif
    </div>

    @if($preferencia)
        <div class="space-y-6">
            {{-- Parámetros Principales --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Presupuesto Máximo</p>
                    <p class="text-2xl font-black text-[#00626A] mt-2">S/ {{ number_format($preferencia->presupuesto_max, 2) }}</p>
                    <p class="text-xs text-gray-400 mt-1">Límite para tus viajes</p>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Días Disponibles</p>
                    <p class="text-2xl font-black text-gray-900 mt-2">
                        {{ $preferencia->dias_disponibles }} <span class="text-sm font-normal text-gray-500">{{ $preferencia->dias_disponibles == 1 ? 'día' : 'días' }}</span>
                    </p>
                    <p class="text-xs text-gray-400 mt-1">Duración típica de estadía</p>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Horario Habitual</p>
                    <p class="text-lg font-bold text-gray-900 mt-2">
                        @if($preferencia->hora_inicio_preferida && $preferencia->hora_fin_preferida)
                            {{ substr($preferencia->hora_inicio_preferida, 0, 5) }} - {{ substr($preferencia->hora_fin_preferida, 0, 5) }}
                        @else
                            <span class="text-gray-400 text-sm font-normal">Flexible</span>
                        @endif
                    </p>
                    <p class="text-xs text-gray-400 mt-1">Horas de actividad</p>
                </div>
            </div>

            {{-- Categorías de Interés --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-base font-semibold text-gray-900 mb-2">Tus Categorías de Interés</h2>
                <p class="text-xs text-gray-500 mb-4">Estas categorías son tomadas en cuenta por el generador de itinerarios con IA.</p>

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
                    <p class="text-sm text-gray-400">Aún no has seleccionado categorías de interés favoritas.</p>
                @endif
            </div>

            <div class="text-right">
                <a href="{{ route('preferencias.turista.edit', ['turistaId' => auth()->id(), 'preferenciaId' => $preferencia->id]) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#00626A] text-white text-sm font-semibold rounded-xl hover:bg-[#004e55] shadow-sm transition-colors">
                    Actualizar Mis Preferencias
                </a>
            </div>
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 px-6 py-16 text-center">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-[#00626A]/10 text-[#00626A]">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                </svg>
            </div>
            <h2 class="text-lg font-bold text-gray-900">Aún no tienes preferencias registradas</h2>
            <p class="mx-auto mt-2 max-w-md text-sm text-gray-500">
                Registra tu presupuesto, días disponibles y categorías preferidas para que la plataforma y la IA puedan ofrecerte recomendaciones a tu medida.
            </p>
            <div class="mt-6">
                <a href="{{ route('preferencias.create') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#00626A] text-white text-sm font-semibold rounded-xl hover:bg-[#004e55] shadow-sm transition-colors">
                    Registrar Mis Preferencias
                </a>
            </div>
        </div>
    @endif
</div>
@endsection