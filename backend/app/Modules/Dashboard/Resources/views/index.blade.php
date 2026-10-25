@extends('layouts.app', ['title' => __('dashboard.page_title')])

@section('content')
<main class="tc-workspace mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    <header class="tc-page-heading flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-semibold text-primary-700">{{ $esAdministrador ? __('dashboard.administration') : __('dashboard.my_space') }}</p>
            <h1 class="mt-1 text-3xl font-bold tracking-tight text-slate-900">{{ $esAdministrador ? __('dashboard.admin_heading') : __('dashboard.traveler_heading') }}</h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">{{ $esAdministrador ? __('dashboard.admin_intro') : __('dashboard.traveler_intro') }}</p>
        </div>
        @if($esAdministrador)
            <a href="{{ route('admin.usuarios.index') }}" class="tc-btn-primary justify-center">{{ __('dashboard.manage_users') }}</a>
        @else
            <a href="{{ route('itinerarios.create') }}" class="tc-btn-primary justify-center"><span aria-hidden="true">+</span> {{ __('dashboard.new_itinerary') }}</a>
        @endif
    </header>

    <section class="mt-7 grid gap-4 sm:grid-cols-2 {{ $esAdministrador ? 'lg:grid-cols-3' : '' }}" aria-label="{{ __('dashboard.current_summary') }}">
        <article class="tc-card p-5">
            <p class="text-sm text-slate-500">{{ $esAdministrador ? __('dashboard.registered_itineraries') : __('dashboard.my_itineraries') }}</p>
            <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($totalItinerarios) }}</p>
            <p class="mt-2 text-xs text-slate-500">{{ $esAdministrador ? __('dashboard.platform_plans') : __('dashboard.saved_plans') }}</p>
        </article>
        <article class="tc-card p-5">
            <p class="text-sm text-slate-500">{{ __('dashboard.active_destinations') }}</p>
            <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($totalDestinos) }}</p>
            <p class="mt-2 text-xs text-slate-500">{{ __('dashboard.available_destinations') }}</p>
        </article>
        @if($esAdministrador)
            <article class="tc-card p-5">
                <p class="text-sm text-slate-500">{{ __('dashboard.registered_users') }}</p>
                <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($totalUsuarios) }}</p>
                <p class="mt-2 text-xs text-slate-500">{{ __('dashboard.platform_accounts') }}</p>
            </article>
        @endif
    </section>

    @if(! $esAdministrador)
        <section class="tc-card mt-6 p-5 sm:p-6" aria-labelledby="empezar-heading">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div><h2 id="empezar-heading" class="text-lg font-bold text-slate-900">{{ __('dashboard.start_with_destination') }}</h2><p class="mt-1 text-sm text-slate-500">{{ __('dashboard.start_intro') }}</p></div>
                <a href="{{ route('catalogo.atractivos.index') }}" class="shrink-0 text-sm font-semibold text-primary-700 hover:underline">{{ __('dashboard.explore_first') }} →</a>
            </div>
            <form method="GET" action="{{ route('itinerarios.create') }}" class="mt-5 grid gap-4 sm:grid-cols-[minmax(0,1fr)_8rem_auto] sm:items-end">
                <div>
                    <label for="destino-dashboard" class="mb-1.5 block text-sm font-semibold text-slate-700">{{ __('dashboard.destination') }}</label>
                    <select id="destino-dashboard" name="destino_id" class="tc-select" required>
                        <option value="">{{ __('dashboard.select_destination') }}</option>
                        @foreach($destinosActivos as $destino)<option value="{{ $destino->id }}">{{ $destino->nombre }}</option>@endforeach
                    </select>
                </div>
                <div><label for="dias-dashboard" class="mb-1.5 block text-sm font-semibold text-slate-700">{{ __('dashboard.days') }}</label><input id="dias-dashboard" name="dias" type="number" min="1" max="60" value="2" required class="tc-input"></div>
                <button type="submit" class="tc-btn-primary justify-center">{{ __('dashboard.continue') }} →</button>
            </form>
        </section>

        <nav class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4" aria-label="{{ __('dashboard.planning_steps') }}">
            <a href="{{ route('preferencias.turista', auth()->id()) }}" class="tc-card group flex gap-4 p-5 hover:border-primary-300">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary-50 text-sm font-bold text-primary-700">1</span>
                <div><h2 class="font-semibold text-slate-900">{{ __('dashboard.your_preferences') }}</h2><p class="mt-1 text-sm leading-5 text-slate-500">{{ __('dashboard.preferences_intro') }}</p><span class="mt-3 inline-block text-sm font-semibold text-primary-700">{{ __('dashboard.review_preferences') }} →</span></div>
            </a>
            <a href="{{ route('itinerarios.index') }}" class="tc-card group flex gap-4 p-5 hover:border-primary-300">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary-50 text-sm font-bold text-primary-700">2</span>
                <div><h2 class="font-semibold text-slate-900">{{ __('dashboard.your_itineraries') }}</h2><p class="mt-1 text-sm leading-5 text-slate-500">{{ __('dashboard.itineraries_intro') }}</p><span class="mt-3 inline-block text-sm font-semibold text-primary-700">{{ __('dashboard.view_itineraries') }} →</span></div>
            </a>
            <a href="{{ route('agenda.index') }}" class="tc-card group flex gap-4 p-5 hover:border-primary-300">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary-50 text-sm font-bold text-primary-700">3</span>
                <div><h2 class="font-semibold text-slate-900">{{ __('dashboard.your_schedule') }}</h2><p class="mt-1 text-sm leading-5 text-slate-500">{{ __('dashboard.schedule_intro') }}</p><span class="mt-3 inline-block text-sm font-semibold text-primary-700">{{ __('dashboard.view_schedule') }} →</span></div>
            </a>
            <a href="{{ route('recomendaciones.index') }}" class="tc-card group flex gap-4 p-5 hover:border-primary-300">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary-50 text-sm font-bold text-primary-700">4</span>
                <div><h2 class="font-semibold text-slate-900">{{ __('dashboard.your_recommendations') }}</h2><p class="mt-1 text-sm leading-5 text-slate-500">{{ __('dashboard.recommendations_intro') }}</p><span class="mt-3 inline-block text-sm font-semibold text-primary-700">{{ __('dashboard.view_recommendations') }} →</span></div>
            </a>
        </nav>
    @else
        <nav class="mt-6 flex flex-wrap gap-3" aria-label="{{ __('dashboard.platform_management') }}"><a href="{{ route('admin.usuarios.index') }}" class="tc-btn-outline">{{ __('dashboard.users') }}</a><a href="{{ route('admin.destinos.index') }}" class="tc-btn-outline">{{ __('dashboard.destinations') }}</a></nav>
    @endif

    <section class="tc-card mt-7 overflow-hidden" aria-labelledby="recientes-heading">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 sm:px-6">
            <h2 id="recientes-heading" class="text-lg font-bold text-slate-900">{{ $esAdministrador ? __('dashboard.recent_itineraries') : __('dashboard.your_recent_itineraries') }}</h2>
            @if(! $esAdministrador)<a href="{{ route('itinerarios.index') }}" class="text-sm font-semibold text-primary-700 hover:underline">{{ __('dashboard.view_all') }} →</a>@endif
        </div>
        @if($itinerariosRecientes->isEmpty())
            <div class="px-5 py-10 text-center">
                <h3 class="font-semibold text-slate-900">{{ $esAdministrador ? __('dashboard.no_registered_itineraries') : __('dashboard.no_personal_itineraries') }}</h3>
                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">{{ $esAdministrador ? __('dashboard.no_registered_intro') : __('dashboard.no_personal_intro') }}</p>
                @if(! $esAdministrador)<a href="{{ route('itinerarios.create') }}" class="tc-btn-primary mt-5">{{ __('dashboard.create_first_itinerary') }}</a>@endif
            </div>
        @else
            <div class="divide-y divide-slate-100">
                @foreach($itinerariosRecientes as $itinerario)
                    <article class="flex flex-col gap-3 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                        <div class="min-w-0"><h3 class="font-semibold text-slate-900">{{ $itinerario->nombre }}</h3><p class="mt-1 text-sm text-slate-500">{{ $itinerario->destino?->nombre ?? __('dashboard.destination_unavailable') }} · {{ $itinerario->dias }} {{ trans_choice('dashboard.day_count', $itinerario->dias) }} · {{ $itinerario->created_at?->format('d/m/Y') }}</p></div>
                        <div class="flex items-center gap-4"><span class="tc-chip">{{ __('dashboard.status.'.$itinerario->estado) }}</span>@if(! $esAdministrador)<a href="{{ route('itinerarios.show', $itinerario) }}" class="shrink-0 text-sm font-semibold text-primary-700 hover:underline">{{ __('dashboard.view_plan') }} →</a>@endif</div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
</main>
@endsection
