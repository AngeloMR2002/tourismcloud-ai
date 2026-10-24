@extends('layouts.app')

@section('title', __('travel_preferences.my_preferences'))

@section('content')
<div class="tc-workspace max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <header class="tc-page-heading">
        <div>
            <p class="text-xs uppercase tracking-wider font-semibold text-neutral-400">{{ __('travel_preferences.tourist_profile') }}</p>
            <h1 class="text-2xl font-bold text-gray-900 mt-1">{{ __('travel_preferences.my_travel_preferences') }}</h1>
        </div>
        @if($preferencia)
            <a href="{{ route('preferencias.turista.edit', ['turistaId' => auth()->id(), 'preferenciaId' => $preferencia->id]) }}" class="tc-btn-primary inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                {{ __('travel_preferences.edit_preferences') }}
            </a>
        @endif
    </header>

    @if($preferencia)
        <div class="space-y-6">
            {{-- Parámetros Principales --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="tc-card bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('travel_preferences.max_budget') }}</p>
                    <p class="text-2xl font-black text-[#16734f] mt-2">S/ {{ number_format($preferencia->presupuesto_max, 2) }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ __('travel_preferences.travel_limit') }}</p>
                </div>

                <div class="tc-card bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('travel_preferences.days_available') }}</p>
                    <p class="text-2xl font-black text-gray-900 mt-2">
                        {{ $preferencia->dias_disponibles }} <span class="text-sm font-normal text-gray-500">{{ $preferencia->dias_disponibles == 1 ? __('travel_preferences.day') : __('travel_preferences.days') }}</span>
                    </p>
                    <p class="text-xs text-gray-400 mt-1">{{ __('travel_preferences.typical_stay') }}</p>
                </div>

                <div class="tc-card bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('travel_preferences.usual_schedule') }}</p>
                    <p class="text-lg font-bold text-gray-900 mt-2">
                        @if($preferencia->hora_inicio_preferida && $preferencia->hora_fin_preferida)
                            {{ substr($preferencia->hora_inicio_preferida, 0, 5) }} - {{ substr($preferencia->hora_fin_preferida, 0, 5) }}
                        @else
                            <span class="text-gray-400 text-sm font-normal">{{ __('travel_preferences.flexible') }}</span>
                        @endif
                    </p>
                    <p class="text-xs text-gray-400 mt-1">{{ __('travel_preferences.activity_hours') }}</p>
                </div>
            </div>

            {{-- Categorías de Interés --}}
            <div class="tc-card bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-base font-semibold text-gray-900 mb-2">{{ __('travel_preferences.your_categories') }}</h2>
                <p class="text-xs text-gray-500 mb-4">{{ __('travel_preferences.categories_ai_hint') }}</p>

                @if($preferencia->categorias->isNotEmpty())
                    <div class="flex flex-wrap gap-2">
                        @foreach($preferencia->categorias as $cat)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-sm font-semibold bg-[#16734f]/10 text-[#16734f] border border-[#16734f]/20">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                {{ $cat->nombre }}
                            </span>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-400">{{ __('travel_preferences.no_favorite_categories') }}</p>
                @endif
            </div>

            <div class="text-right">
                <a href="{{ route('preferencias.turista.edit', ['turistaId' => auth()->id(), 'preferenciaId' => $preferencia->id]) }}" class="tc-btn-primary inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold">
                    {{ __('travel_preferences.update_my_preferences') }}
                </a>
            </div>
        </div>
    @else
        <div class="tc-card bg-white rounded-2xl shadow-sm border border-gray-100 px-6 py-16 text-center">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-[#16734f]/10 text-[#16734f]">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                </svg>
            </div>
            <h2 class="text-lg font-bold text-gray-900">{{ __('travel_preferences.no_preferences') }}</h2>
            <p class="mx-auto mt-2 max-w-md text-sm text-gray-500">
                {{ __('travel_preferences.no_preferences_hint') }}
            </p>
            <div class="mt-6">
                <a href="{{ route('preferencias.create') }}" class="tc-btn-primary inline-flex items-center gap-2 px-6 py-2.5 text-sm font-semibold">
                    {{ __('travel_preferences.register_my_preferences') }}
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
