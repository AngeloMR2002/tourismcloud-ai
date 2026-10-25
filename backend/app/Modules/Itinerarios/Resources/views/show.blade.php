@extends('layouts.app', ['title' => $itinerario->nombre])

@section('content')
@php
    $estado = ['generado' => [__('modules.status_generated'), '#daf2e6', '#105c3e'], 'guardado' => [__('modules.status_saved'), '#fdf6ec', '#85480C'], 'activo' => [__('modules.status_active'), '#dcfce7', '#15803d'], 'finalizado' => [__('modules.status_finished'), '#f3f4f6', '#4b5563'], 'cancelado' => [__('modules.status_cancelled'), '#fef2f2', '#b91c1c']][$itinerario->estado] ?? [ucfirst($itinerario->estado), '#f3f4f6', '#4b5563'];
    $primerDiaConFecha = $itinerario->diasItinerario->first(fn ($dia) => $dia->fecha);
@endphp
<main class="tc-workspace mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
    <a href="{{ route('itinerarios.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-primary-700 hover:underline"><span aria-hidden="true">←</span> {{ __('modules.my_itineraries') }}</a>
    <header class="tc-page-heading mt-5 flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0"><p class="text-sm font-semibold text-primary-700">{{ $itinerario->destino?->nombre ?? __('modules.destination_unavailable') }}</p><h1 class="mt-1 break-words text-3xl font-bold tracking-tight text-slate-900">{{ $itinerario->nombre }}</h1><span class="mt-3 inline-flex rounded-full px-3 py-1 text-xs font-semibold" style="background: {{ $estado[1] }}; color: {{ $estado[2] }};">{{ $estado[0] }}</span></div>
        <a href="{{ route('agenda.index') }}" class="tc-btn-outline shrink-0 justify-center">{{ __('modules.view_my_agenda') }}</a>
    </header>

    @if($errors->any())
        <div class="mt-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800" role="alert"><p class="font-semibold">{{ __('modules.review_visit') }}</p><ul class="mt-2 list-inside list-disc space-y-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <dl class="tc-card mt-7 grid grid-cols-2 gap-5 p-5 sm:grid-cols-4 sm:p-6">
        <div><dt class="text-sm text-slate-500">{{ __('modules.destination') }}</dt><dd class="mt-1 font-semibold text-slate-900">{{ $itinerario->destino?->nombre ?? __('modules.not_available') }}</dd></div>
        <div><dt class="text-sm text-slate-500">{{ __('modules.duration') }}</dt><dd class="mt-1 font-semibold text-slate-900">{{ $itinerario->dias }} {{ $itinerario->dias === 1 ? __('modules.day') : __('modules.days') }}</dd></div>
        <div><dt class="text-sm text-slate-500">{{ __('modules.start_date') }}</dt><dd class="mt-1 font-semibold text-slate-900">{{ $primerDiaConFecha?->fecha?->format('d/m/Y') ?? __('modules.to_define') }}</dd></div>
        <div>
            <dt class="text-sm text-slate-500">{{ __('modules.reference_budget') }}</dt>
            <dd class="mt-1 font-semibold text-primary-700">{{ $itinerario->presupuesto_total ? 'S/ '.number_format((float) $itinerario->presupuesto_total, 2) : __('modules.to_define') }}</dd>
            <p class="mt-1 text-xs text-slate-500">{{ __('modules.estimated_spend') }}: S/ {{ number_format($costoTotal, 2) }}@if($itinerario->presupuesto_total) · {{ __('modules.budget_remaining') }}: S/ {{ number_format((float) $itinerario->presupuesto_total - $costoTotal, 2) }}@endif</p>
        </div>
    </dl>

    @if($itinerario->descripcion)
        <section class="mt-6"><h2 class="text-sm font-semibold text-slate-900">{{ __('modules.trip_notes') }}</h2><p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-600">{{ $itinerario->descripcion }}</p></section>
    @endif

    <section class="tc-card mt-7 overflow-hidden" aria-labelledby="dias-heading">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 sm:px-6"><h2 id="dias-heading" class="text-lg font-bold text-slate-900">{{ __('modules.day_by_day') }}</h2><span class="text-sm text-slate-500">{{ $itinerario->dias }} {{ trans_choice('modules.travel_day', $itinerario->dias) }}</span></div>
        @if($itinerario->diasItinerario->isEmpty())
            <div class="px-5 py-8 text-center"><p class="font-semibold text-slate-900">{{ __('modules.itinerary_no_days') }}</p><p class="mt-2 text-sm text-slate-500">{{ __('modules.return_to_plans') }}</p></div>
        @else
            <ol class="divide-y divide-slate-100">
                @foreach($itinerario->diasItinerario as $dia)
                    <li class="flex gap-4 px-5 py-5 sm:px-6">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary-50 text-sm font-bold text-primary-700">{{ $dia->numero_dia }}</span>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-baseline justify-between gap-2"><h3 class="font-semibold text-slate-900">{{ __('modules.day_number', ['number' => $dia->numero_dia]) }}</h3><p class="text-sm text-slate-500">{{ $dia->fecha ? $dia->fecha->format('d/m/Y') : __('modules.date_to_define') }}</p></div>

                            @if($dia->items->isEmpty())
                                <p class="mt-2 text-sm leading-6 text-slate-500">{{ __('modules.day_no_activities') }}</p>
                            @else
                                <ul class="mt-3 space-y-2">
                                    @foreach($dia->items as $item)
                                        @php($entidad = $entidades[$item->entidad_tipo]->get($item->entidad_id))
                                        <li class="rounded-lg bg-slate-50 px-3 py-2 text-sm">
                                            <div class="flex items-baseline justify-between gap-3">
                                                <span class="font-medium text-slate-800">{{ $entidad?->nombre ?? __('modules.itinerary_item_unavailable') }}</span>
                                                <span class="shrink-0 text-slate-500">{{ substr($item->hora_inicio, 0, 5) }}–{{ substr($item->hora_fin, 0, 5) }} · S/ {{ number_format((float) $item->costo_estimado, 2) }}</span>
                                            </div>
                                            <div class="mt-2 flex flex-wrap items-start gap-4">
                                                <details class="flex-1">
                                                    <summary class="cursor-pointer text-xs font-semibold text-primary-700">{{ __('modules.edit') }}</summary>
                                                    <form method="POST" action="{{ route('itinerarios.items.update', [$itinerario, $item]) }}" class="mt-2 grid gap-2 sm:grid-cols-4">
                                                        @csrf
                                                        @method('PUT')
                                                        <select name="itinerario_dia_id" class="tc-select" aria-label="{{ __('modules.travel_day_label') }}">
                                                            @foreach($itinerario->diasItinerario as $opcionDia)<option value="{{ $opcionDia->id }}" @selected($opcionDia->id === $dia->id)>{{ __('modules.day_number', ['number' => $opcionDia->numero_dia]) }}</option>@endforeach
                                                        </select>
                                                        <input type="time" name="hora_inicio" value="{{ substr($item->hora_inicio, 0, 5) }}" required class="tc-input" aria-label="{{ __('modules.start_hour') }}">
                                                        <input type="time" name="hora_fin" value="{{ substr($item->hora_fin, 0, 5) }}" required class="tc-input" aria-label="{{ __('modules.end_hour') }}">
                                                        @if($item->entidad_tipo === 'establecimiento')
                                                            <input type="number" name="costo_estimado" min="0" step="0.01" value="{{ $item->costo_estimado }}" data-tc-money-input class="tc-input" aria-label="{{ __('modules.item_cost_pen') }}">
                                                        @endif
                                                        <button type="submit" class="tc-btn-primary justify-center">{{ __('modules.save') }}</button>
                                                    </form>
                                                </details>
                                                <form method="POST" action="{{ route('itinerarios.items.destroy', [$itinerario, $item]) }}" onsubmit="return confirm(@js(__('modules.remove_visit_confirm')));">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-xs font-semibold text-red-700 hover:underline">{{ __('modules.remove') }}</button>
                                                </form>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif

                            <details class="mt-3">
                                <summary class="cursor-pointer text-sm font-semibold text-primary-700">+ {{ __('modules.add_visit') }}</summary>
                                <form method="POST" action="{{ route('itinerarios.items.store', $itinerario) }}" class="mt-3 grid gap-3 sm:grid-cols-3">
                                    @csrf
                                    <input type="hidden" name="itinerario_dia_id" value="{{ $dia->id }}">
                                    <div class="sm:col-span-3">
                                        <label for="entidad-{{ $dia->id }}" class="mb-1.5 block text-sm font-semibold text-slate-700">{{ __('modules.catalogue_option') }}</label>
                                        <select id="entidad-{{ $dia->id }}" name="entidad" required class="tc-select">
                                            <option value="">{{ __('modules.choose_visit') }}</option>
                                            <optgroup label="{{ __('ui.attractions') }}">@foreach($catalogo['atractivo'] as $opcion)<option value="atractivo:{{ $opcion->id }}">{{ $opcion->nombre }} · S/ {{ number_format((float) $opcion->costo_entrada, 2) }}</option>@endforeach</optgroup>
                                            <optgroup label="{{ __('ui.activities') }}">@foreach($catalogo['actividad'] as $opcion)<option value="actividad:{{ $opcion->id }}">{{ $opcion->nombre }} · S/ {{ number_format((float) $opcion->costo, 2) }}</option>@endforeach</optgroup>
                                            <optgroup label="{{ __('ui.establishments') }}">@foreach($catalogo['establecimiento'] as $opcion)<option value="establecimiento:{{ $opcion->id }}">{{ $opcion->nombre }}</option>@endforeach</optgroup>
                                        </select>
                                    </div>
                                    <div><label for="inicio-{{ $dia->id }}" class="mb-1.5 block text-sm font-semibold text-slate-700">{{ __('modules.start_hour') }}</label><input id="inicio-{{ $dia->id }}" type="time" name="hora_inicio" required class="tc-input"></div>
                                    <div><label for="fin-{{ $dia->id }}" class="mb-1.5 block text-sm font-semibold text-slate-700">{{ __('modules.end_hour') }}</label><input id="fin-{{ $dia->id }}" type="time" name="hora_fin" required class="tc-input"></div>
                                    <div><label for="costo-{{ $dia->id }}" data-tc-money-label class="mb-1.5 block text-sm font-semibold text-slate-700">{{ __('modules.item_cost_pen') }}</label><input id="costo-{{ $dia->id }}" type="number" name="costo_estimado" min="0" step="0.01" data-tc-money-input class="tc-input"></div>
                                    <p class="text-xs text-slate-500 sm:col-span-2">{{ __('modules.item_cost_hint') }}</p>
                                    <button type="submit" class="tc-btn-primary justify-center">{{ __('modules.add_visit') }}</button>
                                </form>
                            </details>
                        </div>
                    </li>
                @endforeach
            </ol>
        @endif
        <div class="flex flex-col gap-3 border-t border-slate-100 bg-primary-50/40 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6"><p class="text-sm text-slate-600">{{ __('modules.catalogue_ideas') }}</p><div class="flex flex-wrap gap-4"><a href="{{ route('catalogo.actividades.index') }}" class="text-sm font-semibold text-primary-700 hover:underline">{{ __('modules.view_activities') }}</a><a href="{{ route('catalogo.rutas.index') }}" class="text-sm font-semibold text-primary-700 hover:underline">{{ __('modules.view_routes') }}</a></div></div>
    </section>

    <div class="mt-6 flex flex-col gap-4 border-t border-slate-200 pt-5 sm:flex-row sm:items-center sm:justify-between">
        <p class="max-w-xl text-sm leading-6 text-slate-500">{{ __('modules.itinerary_disclaimer') }}</p>
        <form method="POST" action="{{ route('itinerarios.destroy', $itinerario) }}" onsubmit="return confirm(@js(__('modules.delete_itinerary_confirm')));">
            @csrf
            @method('DELETE')
            <button class="rounded-lg border border-red-200 px-4 py-2.5 text-sm font-semibold text-red-700 transition hover:bg-red-50" type="submit">{{ __('modules.delete_itinerary') }}</button>
        </form>
    </div>
</main>
@endsection