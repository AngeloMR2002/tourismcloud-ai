@extends('layouts.app', ['title' => 'Crear itinerario'])

@section('content')
<main class="tc-workspace mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
    <a href="{{ route('itinerarios.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-primary-700 hover:underline"><span aria-hidden="true">←</span> Mis itinerarios</a>
    <header class="tc-page-heading mt-5">
        <p class="text-sm font-semibold text-primary-700">Nuevo plan</p>
        <h1 class="mt-1 text-3xl font-bold tracking-tight text-slate-900">¿Cómo será tu viaje?</h1>
        <p class="mt-2 text-sm leading-6 text-slate-500">Guarda los datos iniciales de tu itinerario. Los campos con <span class="text-red-600">*</span> son obligatorios.</p>
    </header>

    @if($errors->any())
        <div class="mt-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800" role="alert"><p class="font-semibold">Revisa los datos del itinerario</p><ul class="mt-2 list-inside list-disc space-y-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    @if($destinos->isEmpty())
        <div class="mt-5 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm leading-6 text-amber-800" role="status">Todavía no hay destinos activos para crear un itinerario. Puedes <a href="{{ route('catalogo.atractivos.index') }}" class="font-semibold underline">consultar el catálogo</a> mientras se publican.</div>
    @endif

    <form method="POST" action="{{ route('itinerarios.store') }}" class="tc-card mt-6 overflow-hidden">
        @csrf
        <section class="p-5 sm:p-7">
            <div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-50 text-sm font-bold text-primary-700">1</span><h2 class="text-lg font-bold text-slate-900">Nombre y destino</h2></div>
            <div class="mt-5 grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="nombre" class="mb-1.5 block text-sm font-semibold text-slate-700">Nombre del itinerario <span class="text-red-600">*</span></label>
                    <input id="nombre" name="nombre" required maxlength="150" value="{{ old('nombre') }}" class="tc-input" placeholder="Ej.: Un fin de semana en Cusco" @error('nombre') aria-invalid="true" aria-describedby="nombre-error" @enderror>
                    @error('nombre')<p id="nombre-error" class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="sm:col-span-2">
                    <label for="destino_id" class="mb-1.5 block text-sm font-semibold text-slate-700">Destino <span class="text-red-600">*</span></label>
                    <select id="destino_id" name="destino_id" required class="tc-select" @error('destino_id') aria-invalid="true" aria-describedby="destino-error" @enderror>
                        <option value="">Selecciona un destino</option>
                        @foreach($destinos as $destino)<option value="{{ $destino->id }}" @selected((string) old('destino_id', $destinoInicial) === (string) $destino->id)>{{ $destino->nombre }}</option>@endforeach
                    </select>
                    @error('destino_id')<p id="destino-error" class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        <section class="border-t border-slate-100 p-5 sm:p-7">
            <div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-50 text-sm font-bold text-primary-700">2</span><h2 class="text-lg font-bold text-slate-900">Tiempo y presupuesto</h2></div>
            <div class="mt-5 grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="dias" class="mb-1.5 block text-sm font-semibold text-slate-700">Días disponibles <span class="text-red-600">*</span></label>
                    <input id="dias" name="dias" type="number" min="1" max="60" required value="{{ old('dias', $diasIniciales) }}" class="tc-input" @error('dias') aria-invalid="true" aria-describedby="dias-error" @enderror>
                    @error('dias')<p id="dias-error" class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="fecha_inicio" class="mb-1.5 block text-sm font-semibold text-slate-700">Fecha de inicio <span class="text-xs font-normal text-slate-400">(opcional)</span></label>
                    <input id="fecha_inicio" name="fecha_inicio" type="date" value="{{ old('fecha_inicio') }}" class="tc-input" @error('fecha_inicio') aria-invalid="true" aria-describedby="fecha-error" @enderror>
                    <p class="mt-1.5 text-xs text-slate-500">Si la indicas, cada día del plan tendrá una fecha.</p>
                    @error('fecha_inicio')<p id="fecha-error" class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="sm:col-span-2">
                    <label for="presupuesto_total" class="mb-1.5 block text-sm font-semibold text-slate-700">Presupuesto total (S/) <span class="text-xs font-normal text-slate-400">(opcional)</span></label>
                    <input id="presupuesto_total" name="presupuesto_total" type="number" min="0" max="99999999" step="0.01" value="{{ old('presupuesto_total') }}" class="tc-input" placeholder="0.00" @error('presupuesto_total') aria-invalid="true" aria-describedby="presupuesto-error" @enderror>
                    <p class="mt-1.5 text-xs text-slate-500">Monto referencial que quieres destinar a este viaje.</p>
                    @error('presupuesto_total')<p id="presupuesto-error" class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        <section class="border-t border-slate-100 p-5 sm:p-7">
            <label for="descripcion" class="mb-1.5 block text-sm font-semibold text-slate-700">Notas del viaje <span class="text-xs font-normal text-slate-400">(opcional)</span></label>
            <textarea id="descripcion" name="descripcion" rows="3" maxlength="5000" class="tc-input" placeholder="Qué te gustaría conocer o tener en cuenta." @error('descripcion') aria-invalid="true" aria-describedby="descripcion-error" @enderror>{{ old('descripcion') }}</textarea>
            @error('descripcion')<p id="descripcion-error" class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
        </section>

        <div class="flex flex-col-reverse gap-3 border-t border-slate-100 bg-slate-50/60 p-5 sm:flex-row sm:items-center sm:justify-between sm:px-7">
            <a class="tc-btn-outline justify-center" href="{{ route('itinerarios.index') }}">Cancelar</a>
            <button class="tc-btn-primary justify-center disabled:cursor-not-allowed disabled:opacity-50" type="submit" @disabled($destinos->isEmpty())>Guardar itinerario →</button>
        </div>
    </form>
    <p class="mt-4 text-sm leading-6 text-slate-500">Se guardarán los datos y los días del plan en tu cuenta. Después podrás consultarlos en <a href="{{ route('agenda.index') }}" class="font-semibold text-primary-700 hover:underline">tu agenda</a>.</p>
</main>
@endsection
