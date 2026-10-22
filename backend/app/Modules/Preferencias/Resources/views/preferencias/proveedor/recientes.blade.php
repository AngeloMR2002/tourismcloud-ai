@extends('layouts.app')

@section('title', __('travel_preferences.trends_title'))

@section('content')
<div class="tc-workspace max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <header class="tc-page-heading">
        <div>
            <p class="text-xs uppercase tracking-wider font-semibold text-neutral-400">{{ __('travel_preferences.tourism_demand') }}</p>
            <h1 class="text-2xl font-bold text-gray-900 mt-1">{{ __('travel_preferences.recent_trends') }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('travel_preferences.recent_trends_hint') }}</p>
        </div>
        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-[#16734f]/10 text-[#16734f] w-fit">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            {{ __('travel_preferences.latest_ten') }}
        </span>
    </header>

    {{-- Presupuestos --}}
    <div class="tc-card bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <h2 class="text-base font-semibold text-gray-900 mb-1">{{ __('travel_preferences.requested_budgets') }}</h2>
        <p class="text-xs text-gray-500 mb-4">{{ __('travel_preferences.requested_budgets_hint') }}</p>

        @if ($presupuestos->isEmpty())
            <p class="text-sm text-gray-400 py-4 text-center">{{ __('travel_preferences.no_recent_budgets') }}</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
                @foreach ($presupuestos as $presupuesto)
                    <div class="p-4 rounded-xl border border-gray-100 bg-gray-50/75">
                        <p class="text-xs text-gray-400 font-medium">{{ __('travel_preferences.max_budget') }}</p>
                        <p class="text-lg font-bold text-[#16734f] mt-1">
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
        <div class="tc-card bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-semibold text-gray-900 mb-1">{{ __('travel_preferences.requested_days') }}</h2>
            <p class="text-xs text-gray-500 mb-4">{{ __('travel_preferences.requested_days_hint') }}</p>

            @if ($duraciones->isEmpty())
                <p class="text-sm text-gray-400 py-4 text-center">{{ __('travel_preferences.no_durations') }}</p>
            @else
                <div class="space-y-3">
                    @foreach ($duraciones as $dias => $cantidad)
                        <div>
                            <div class="flex items-center justify-between text-sm mb-1">
                                <span class="font-medium text-gray-700">{{ $dias }} {{ __($dias == 1 ? 'travel_preferences.day' : 'travel_preferences.days') }}</span>
                                <span class="font-bold text-gray-900">{{ trans_choice('travel_preferences.tourists_count', $cantidad, ['count' => $cantidad]) }}</span>
                            </div>
                            <div class="h-2 rounded-full bg-gray-100 overflow-hidden">
                                <div class="h-full rounded-full bg-[#16734f]" style="width: {{ ($cantidad / max(1, $preferencias->count())) * 100 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Categorías de Interés --}}
        <div class="tc-card bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-semibold text-gray-900 mb-1">{{ __('travel_preferences.top_categories') }}</h2>
            <p class="text-xs text-gray-500 mb-4">{{ __('travel_preferences.top_categories_hint') }}</p>

            @if ($categorias->isEmpty())
                <p class="text-sm text-gray-400 py-4 text-center">{{ __('travel_preferences.no_registered_categories') }}</p>
            @else
                <div class="space-y-3">
                    @foreach ($categorias as $cat)
                        <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 border border-gray-100">
                            <span class="text-sm font-semibold text-gray-800">{{ $cat['nombre'] }}</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#16734f]/10 text-[#16734f]">
                                {{ $cat['cantidad'] }} {{ __($cat['cantidad'] == 1 ? 'travel_preferences.interest' : 'travel_preferences.interests') }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="rounded-xl border border-[#16734f]/20 bg-[#16734f]/5 p-4 flex items-center gap-3">
        <svg class="w-5 h-5 text-[#16734f] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p class="text-xs text-neutral-600">
            {{ __('travel_preferences.trends_info') }}
        </p>
    </div>
</div>
@endsection
