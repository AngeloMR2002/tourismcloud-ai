@extends('layouts.app', ['title' => 'Crear itinerario'])

@section('content')
<main class="mx-auto max-w-5xl px-4 py-8 sm:px-6 sm:py-10 lg:px-8">
    <a href="{{ route('itinerarios.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-primary-700 hover:underline">
        <span aria-hidden="true">←</span> Mis itinerarios
    </a>

    <div class="mt-5 grid gap-6 lg:grid-cols-[minmax(0,1fr)_17rem]">
        <section>
            <span class="tc-chip">Planificación turística</span>
            <h1 class="mt-3 text-3xl font-extrabold text-primary-700">Crea un nuevo itinerario</h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">Define el destino y los datos iniciales. Podrás usar este plan como base para organizar tu viaje.</p>

            <form method="POST" action="{{ route('itinerarios.store') }}" class="mt-6 space-y-6">
                @csrf
                <section class="tc-card p-5 sm:p-6">
                    <div class="flex items-start gap-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary-50 text-primary-700"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 21s7-5.2 7-12a7 7 0 1 0-14 0c0 6.8 7 12 7 12Z"/><circle cx="12" cy="9" r="2.2"/></svg></span>
                        <div><h2 class="font-bold text-slate-900">Datos principales</h2><p class="mt-1 text-sm text-slate-500">El nombre y el destino identifican tu plan.</p></div>
                    </div>
                    <div class="mt-5 grid gap-5 sm:grid-cols-2">
                        <div class="sm:col-span-2"><label for="nombre" class="mb-1.5 block text-sm font-semibold text-slate-700">Nombre del itinerario <span class="text-red-600">*</span></label><input id="nombre" name="nombre" required maxlength="150" value="{{ old('nombre') }}" class="tc-input" placeholder="Ej.: Escapada cultural por Cusco">@error('nombre')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror</div>
                        <div><label for="destino_id" class="mb-1.5 block text-sm font-semibold text-slate-700">Destino <span class="text-red-600">*</span></label><select id="destino_id" name="destino_id" required class="tc-select"><option value="">Selecciona un destino</option>@foreach($destinos as $destino)<option value="{{ $destino->id }}" @selected((string) old('destino_id', $destinoInicial) === (string) $destino->id)>{{ $destino->nombre }}</option>@endforeach</select>@error('destino_id')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror</div>
                        <div><label for="fecha_inicio" class="mb-1.5 block text-sm font-semibold text-slate-700">Fecha de inicio <span class="text-xs font-normal text-slate-400">(opcional)</span></label><input id="fecha_inicio" name="fecha_inicio" type="date" value="{{ old('fecha_inicio') }}" class="tc-input">@error('fecha_inicio')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror</div>
                        <div class="sm:col-span-2"><label for="descripcion" class="mb-1.5 block text-sm font-semibold text-slate-700">Descripción <span class="text-xs font-normal text-slate-400">(opcional)</span></label><textarea id="descripcion" name="descripcion" rows="4" maxlength="5000" class="tc-input" placeholder="Cuenta brevemente qué esperas de este viaje.">{{ old('descripcion') }}</textarea>@error('descripcion')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror</div>
                    </div>
                </section>

                <section class="tc-card p-5 sm:p-6">
                    <div class="flex items-start gap-3"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-tertiary-50 text-tertiary-600"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="16" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 3v4M8 3v4M3 10h18"/></svg></span><div><h2 class="font-bold text-slate-900">Duración y presupuesto</h2><p class="mt-1 text-sm text-slate-500">Son valores referenciales para orientar tu planificación.</p></div></div>
                    <div class="mt-5 grid gap-5 sm:grid-cols-2">
                        <div><label for="dias" class="mb-1.5 block text-sm font-semibold text-slate-700">Días <span class="text-red-600">*</span></label><input id="dias" name="dias" type="number" min="1" max="60" required value="{{ old('dias', $diasIniciales) }}" class="tc-input">@error('dias')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror</div>
                        <div><label for="presupuesto_total" class="mb-1.5 block text-sm font-semibold text-slate-700">Presupuesto total (S/) <span class="text-xs font-normal text-slate-400">(opcional)</span></label><input id="presupuesto_total" name="presupuesto_total" type="number" min="0" max="99999999" step="0.01" value="{{ old('presupuesto_total') }}" class="tc-input" placeholder="0.00">@error('presupuesto_total')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror</div>
                    </div>
                </section>

                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center"><a class="tc-btn-outline justify-center" href="{{ route('itinerarios.index') }}">Cancelar</a><button class="tc-btn-primary justify-center" type="submit">Guardar itinerario <span aria-hidden="true">→</span></button></div>
            </form>
        </section>

        <aside class="tc-card h-fit p-5">
            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-primary-50 text-primary-700"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v18m-7-9h14"/></svg></span>
            <h2 class="mt-4 font-bold text-slate-900">¿Qué se guardará?</h2>
            <ul class="mt-3 space-y-3 text-sm leading-5 text-slate-600"><li class="flex gap-2"><span class="font-bold text-primary-600">✓</span><span>Un plan asociado únicamente a tu cuenta.</span></li><li class="flex gap-2"><span class="font-bold text-primary-600">✓</span><span>Los días de viaje para que puedas consultarlos en tu agenda.</span></li><li class="flex gap-2"><span class="font-bold text-primary-600">✓</span><span>La fecha de inicio, solo si la indicas.</span></li></ul>
        </aside>
    </div>
</main>
@endsection
