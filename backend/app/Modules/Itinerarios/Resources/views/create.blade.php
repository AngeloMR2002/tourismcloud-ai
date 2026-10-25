@extends('layouts.app', ['title' => __('modules.create_itinerary')])

@section('content')
<main class="tc-workspace mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
    <a href="{{ route('itinerarios.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-primary-700 hover:underline"><span aria-hidden="true">←</span> {{ __('modules.my_itineraries') }}</a>
    <header class="tc-page-heading mt-5">
        <p class="text-sm font-semibold text-primary-700">{{ __('modules.new_plan') }}</p>
        <h1 class="mt-1 text-3xl font-bold tracking-tight text-slate-900">{{ __('modules.how_will_trip_be') }}</h1>
        <p class="mt-2 text-sm leading-6 text-slate-500">{{ __('modules.itinerary_form_intro', ['asterisk' => '*']) }}</p>
    </header>

    @if($errors->any())
        <div class="mt-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800" role="alert"><p class="font-semibold">{{ __('modules.review_itinerary') }}</p><ul class="mt-2 list-inside list-disc space-y-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    @if($destinos->isEmpty())
        <div class="mt-5 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm leading-6 text-amber-800" role="status">{{ __('modules.no_active_destinations_before') }} <a href="{{ route('catalogo.atractivos.index') }}" class="font-semibold underline">{{ __('modules.browse_catalogue') }}</a> {{ __('modules.no_active_destinations_after') }}</div>
    @endif

    <form method="POST" action="{{ route('itinerarios.store') }}" class="tc-card mt-6 overflow-hidden">
        @csrf
        <section class="p-5 sm:p-7">
            <div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-50 text-sm font-bold text-primary-700">1</span><h2 class="text-lg font-bold text-slate-900">{{ __('modules.name_and_destination') }}</h2></div>
            <div class="mt-5 grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="nombre" class="mb-1.5 block text-sm font-semibold text-slate-700">{{ __('modules.itinerary_name') }} <span class="text-red-600">*</span></label>
                    <input id="nombre" name="nombre" required maxlength="150" value="{{ old('nombre') }}" class="tc-input" placeholder="{{ __('modules.itinerary_name_example') }}" @error('nombre') aria-invalid="true" aria-describedby="nombre-error" @enderror>
                    @error('nombre')<p id="nombre-error" class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="sm:col-span-2">
                    <label for="destino_id" class="mb-1.5 block text-sm font-semibold text-slate-700">{{ __('modules.destination') }} <span class="text-red-600">*</span></label>
                    <select id="destino_id" name="destino_id" required class="tc-select" @error('destino_id') aria-invalid="true" aria-describedby="destino-error" @enderror>
                        <option value="">{{ __('modules.select_destination') }}</option>
                        @foreach($destinos as $destino)<option value="{{ $destino->id }}" @selected((string) old('destino_id', $destinoInicial) === (string) $destino->id)>{{ $destino->nombre }}</option>@endforeach
                    </select>
                    @error('destino_id')<p id="destino-error" class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        <section class="border-t border-slate-100 p-5 sm:p-7">
            <div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-50 text-sm font-bold text-primary-700">2</span><h2 class="text-lg font-bold text-slate-900">{{ __('modules.time_and_budget') }}</h2></div>
            <div class="mt-5 grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="dias" class="mb-1.5 block text-sm font-semibold text-slate-700">{{ __('modules.days_available') }} <span class="text-red-600">*</span></label>
                    <input id="dias" name="dias" type="number" min="1" max="60" required value="{{ old('dias', $diasIniciales) }}" class="tc-input" @error('dias') aria-invalid="true" aria-describedby="dias-error" @enderror>
                    @error('dias')<p id="dias-error" class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="fecha_inicio" class="mb-1.5 block text-sm font-semibold text-slate-700">{{ __('modules.start_date') }} <span class="text-xs font-normal text-slate-400">{{ __('modules.optional') }}</span></label>
                    <input id="fecha_inicio" name="fecha_inicio" type="date" value="{{ old('fecha_inicio') }}" class="tc-input" @error('fecha_inicio') aria-invalid="true" aria-describedby="fecha-error" @enderror>
                    <p class="mt-1.5 text-xs text-slate-500">{{ __('modules.date_hint') }}</p>
                    @error('fecha_inicio')<p id="fecha-error" class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="sm:col-span-2">
                    <label for="presupuesto_total" data-tc-money-label class="mb-1.5 block text-sm font-semibold text-slate-700">{{ __('modules.total_budget_pen') }} <span class="text-xs font-normal text-slate-400">{{ __('modules.optional') }}</span></label>
                    <input id="presupuesto_total" data-tc-money-input name="presupuesto_total" type="number" min="0" max="99999999" step="0.01" value="{{ old('presupuesto_total') }}" class="tc-input" placeholder="0.00" @error('presupuesto_total') aria-invalid="true" aria-describedby="presupuesto-error" @enderror>
                    <p class="mt-1.5 text-xs text-slate-500">{{ __('modules.budget_hint') }}</p>
                    @error('presupuesto_total')<p id="presupuesto-error" class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="sm:col-span-2">
                    <input type="hidden" name="componer_automaticamente" value="0">
                    <label class="flex items-start gap-2.5 text-sm text-slate-700">
                        <input type="checkbox" name="componer_automaticamente" value="1" @checked(old('componer_automaticamente') === '1') class="mt-0.5 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                        <span>{{ __('modules.auto_compose') }}<span class="mt-0.5 block text-xs text-slate-500">{{ __('modules.auto_compose_hint') }}</span></span>
                    </label>
                </div>
            </div>
        </section>

        <section class="border-t border-slate-100 p-5 sm:p-7">
            <label for="descripcion" class="mb-1.5 block text-sm font-semibold text-slate-700">{{ __('modules.trip_notes') }} <span class="text-xs font-normal text-slate-400">{{ __('modules.optional') }}</span></label>
            <textarea id="descripcion" name="descripcion" rows="3" maxlength="5000" class="tc-input" placeholder="{{ __('modules.trip_notes_placeholder') }}" @error('descripcion') aria-invalid="true" aria-describedby="descripcion-error" @enderror>{{ old('descripcion') }}</textarea>
            @error('descripcion')<p id="descripcion-error" class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
        </section>

        <div class="flex flex-col-reverse gap-3 border-t border-slate-100 bg-slate-50/60 p-5 sm:flex-row sm:items-center sm:justify-between sm:px-7">
            <a class="tc-btn-outline justify-center" href="{{ route('itinerarios.index') }}">{{ __('modules.cancel') }}</a>
            <button class="tc-btn-primary justify-center disabled:cursor-not-allowed disabled:opacity-50" type="submit" @disabled($destinos->isEmpty())>{{ __('modules.save_itinerary') }}</button>
        </div>
    </form>
    <p class="mt-4 text-sm leading-6 text-slate-500">{{ __('modules.itinerary_saved_before') }} <a href="{{ route('agenda.index') }}" class="font-semibold text-primary-700 hover:underline">{{ __('modules.your_agenda') }}</a>.</p>
</main>
@endsection