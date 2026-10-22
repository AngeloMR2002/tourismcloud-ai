@extends('layouts.app', ['title' => __('modules.my_itineraries')])

@section('content')
@php
    $hayFiltros = request()->filled('buscar') || request()->filled('destino_id');
@endphp
<main class="tc-workspace mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    <header class="tc-page-heading flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
        <div><p class="text-sm font-semibold text-primary-700">{{ __('modules.my_planning') }}</p><h1 class="mt-1 text-3xl font-bold tracking-tight text-slate-900">{{ __('modules.my_itineraries') }}</h1><p class="mt-2 text-sm leading-6 text-slate-500">{{ __('modules.itineraries_intro') }}</p></div>
        <div class="flex flex-wrap gap-3"><a class="tc-btn-outline justify-center" href="{{ route('agenda.index') }}">{{ __('modules.view_agenda') }}</a><a class="tc-btn-primary justify-center" href="{{ route('itinerarios.create') }}"><span aria-hidden="true">+</span> {{ __('modules.new_itinerary') }}</a></div>
    </header>

    <form method="GET" action="{{ route('itinerarios.index') }}" class="tc-card mt-7 grid gap-4 p-5 sm:grid-cols-[minmax(0,1fr)_14rem_auto] sm:items-end" aria-label="{{ __('modules.filter_itineraries') }}">
        <div><label for="buscar" class="mb-1.5 block text-sm font-semibold text-slate-700">{{ __('modules.search_plan') }}</label><input id="buscar" class="tc-input" name="buscar" value="{{ request('buscar') }}" placeholder="{{ __('modules.name_or_description') }}"></div>
        <div><label for="destino_id" class="mb-1.5 block text-sm font-semibold text-slate-700">{{ __('modules.destination') }}</label><select id="destino_id" class="tc-select" name="destino_id"><option value="">{{ __('modules.all_destinations') }}</option>@foreach($destinos as $destino)<option value="{{ $destino->id }}" @selected((string) request('destino_id') === (string) $destino->id)>{{ $destino->nombre }}</option>@endforeach</select></div>
        <div class="flex gap-2"><button class="tc-btn-primary justify-center" type="submit">{{ __('modules.search') }}</button>@if($hayFiltros)<a class="tc-btn-outline justify-center" href="{{ route('itinerarios.index') }}">{{ __('modules.clear') }}</a>@endif</div>
    </form>

    <p class="mt-6 text-sm text-slate-500">{{ trans_choice('modules.itinerary_count', $itinerarios->total(), ['count' => $itinerarios->total()]) }}{{ $hayFiltros ? __('modules.with_filters') : ($itinerarios->total() === 1 ? __('modules.saved_singular') : __('modules.saved_plural')) }}</p>

    @if($itinerarios->isEmpty())
        <section class="tc-card mt-4 px-6 py-12 text-center">
            <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-primary-50 text-primary-700"><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 5h16M7 3v4m10-4v4M5 9h14v11H5zM8 13h3m2 0h3m-8 3h3"/></svg></span>
            <h2 class="mt-4 text-lg font-semibold text-slate-900">{{ $hayFiltros ? __('modules.no_filtered_plans') : __('modules.first_trip_starts') }}</h2>
            <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">{{ $hayFiltros ? __('modules.try_another_plan') : __('modules.choose_destination_plan') }}</p>
            @if($hayFiltros)<a class="tc-btn-outline mt-5" href="{{ route('itinerarios.index') }}">{{ __('modules.view_all_itineraries') }}</a>@else<a class="tc-btn-primary mt-5" href="{{ route('itinerarios.create') }}">{{ __('modules.create_first_itinerary') }}</a>@endif
        </section>
    @else
        <div class="mt-4 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            @foreach($itinerarios as $itinerario)
                @php
                    $estado = ['generado' => [__('modules.status_generated'), '#daf2e6', '#105c3e'], 'guardado' => [__('modules.status_saved'), '#fdf6ec', '#85480C'], 'activo' => [__('modules.status_active'), '#dcfce7', '#15803d'], 'finalizado' => [__('modules.status_finished'), '#f3f4f6', '#4b5563'], 'cancelado' => [__('modules.status_cancelled'), '#fef2f2', '#b91c1c']][$itinerario->estado] ?? [ucfirst($itinerario->estado), '#f3f4f6', '#4b5563'];
                @endphp
                <article class="tc-card flex min-w-0 flex-col p-5">
                    <div class="flex items-center justify-between gap-3"><p class="text-sm font-semibold text-primary-700">{{ $itinerario->destino?->nombre ?? __('modules.destination_unavailable') }}</p><span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold" style="background: {{ $estado[1] }}; color: {{ $estado[2] }};">{{ $estado[0] }}</span></div>
                    <h2 class="mt-4 text-lg font-bold leading-6 text-slate-900"><a href="{{ route('itinerarios.show', $itinerario) }}" class="hover:text-primary-700">{{ $itinerario->nombre }}</a></h2>
                    <p class="mt-2 flex-1 text-sm leading-6 text-slate-500">{{ \Illuminate\Support\Str::limit($itinerario->descripcion ?: __('modules.no_description'), 130) }}</p>
                    <dl class="mt-5 grid grid-cols-2 gap-3 border-t border-slate-100 pt-4 text-sm">
                        <div><dt class="text-xs text-slate-500">{{ __('modules.duration') }}</dt><dd class="mt-1 font-semibold text-slate-800">{{ $itinerario->dias }} {{ $itinerario->dias === 1 ? __('modules.day') : __('modules.days') }}</dd></div>
                        <div><dt class="text-xs text-slate-500">{{ __('modules.reference_budget') }}</dt><dd class="mt-1 font-semibold text-slate-800">{{ $itinerario->presupuesto_total ? 'S/ '.number_format((float) $itinerario->presupuesto_total, 2) : __('modules.to_define') }}</dd></div>
                    </dl>
                    <a class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-primary-700 hover:underline" href="{{ route('itinerarios.show', $itinerario) }}">{{ __('modules.open_itinerary') }} <span aria-hidden="true">→</span></a>
                </article>
            @endforeach
        </div>
        <div class="mt-7">{{ $itinerarios->links() }}</div>
    @endif
</main>
@endsection
