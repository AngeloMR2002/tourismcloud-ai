@extends('layouts.app', ['title' => $itinerario->nombre])

@section('content')
@php
    $estado = ['generado' => ['Generado', '#e6f4f5', '#08565D'], 'guardado' => ['Guardado', '#fdf6ec', '#85480C'], 'activo' => ['Activo', '#dcfce7', '#15803d'], 'finalizado' => ['Finalizado', '#f3f4f6', '#4b5563'], 'cancelado' => ['Cancelado', '#fef2f2', '#b91c1c']][$itinerario->estado] ?? [ucfirst($itinerario->estado), '#f3f4f6', '#4b5563'];
    $primerDiaConFecha = $itinerario->diasItinerario->first(fn ($dia) => $dia->fecha);
@endphp
<main class="tc-workspace mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
    <a href="{{ route('itinerarios.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-primary-700 hover:underline"><span aria-hidden="true">←</span> Mis itinerarios</a>
    <header class="tc-page-heading mt-5 flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0"><p class="text-sm font-semibold text-primary-700">{{ $itinerario->destino?->nombre ?? 'Destino no disponible' }}</p><h1 class="mt-1 break-words text-3xl font-bold tracking-tight text-slate-900">{{ $itinerario->nombre }}</h1><span class="mt-3 inline-flex rounded-full px-3 py-1 text-xs font-semibold" style="background: {{ $estado[1] }}; color: {{ $estado[2] }};">{{ $estado[0] }}</span></div>
        <a href="{{ route('agenda.index') }}" class="tc-btn-outline shrink-0 justify-center">Ver mi agenda</a>
    </header>

    <dl class="tc-card mt-7 grid grid-cols-2 gap-5 p-5 sm:grid-cols-4 sm:p-6">
        <div><dt class="text-sm text-slate-500">Destino</dt><dd class="mt-1 font-semibold text-slate-900">{{ $itinerario->destino?->nombre ?? 'No disponible' }}</dd></div>
        <div><dt class="text-sm text-slate-500">Duración</dt><dd class="mt-1 font-semibold text-slate-900">{{ $itinerario->dias }} {{ $itinerario->dias === 1 ? 'día' : 'días' }}</dd></div>
        <div><dt class="text-sm text-slate-500">Fecha de inicio</dt><dd class="mt-1 font-semibold text-slate-900">{{ $primerDiaConFecha?->fecha?->format('d/m/Y') ?? 'Por definir' }}</dd></div>
        <div><dt class="text-sm text-slate-500">Presupuesto referencial</dt><dd class="mt-1 font-semibold text-primary-700">{{ $itinerario->presupuesto_total ? 'S/ '.number_format((float) $itinerario->presupuesto_total, 2) : 'Por definir' }}</dd></div>
    </dl>

    @if($itinerario->descripcion)
        <section class="mt-6"><h2 class="text-sm font-semibold text-slate-900">Notas del viaje</h2><p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-600">{{ $itinerario->descripcion }}</p></section>
    @endif

    <section class="tc-card mt-7 overflow-hidden" aria-labelledby="dias-heading">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 sm:px-6"><h2 id="dias-heading" class="text-lg font-bold text-slate-900">Día a día</h2><span class="text-sm text-slate-500">{{ $itinerario->dias }} {{ $itinerario->dias === 1 ? 'día de viaje' : 'días de viaje' }}</span></div>
        @if($itinerario->diasItinerario->isEmpty())
            <div class="px-5 py-8 text-center"><p class="font-semibold text-slate-900">Este itinerario no tiene días registrados</p><p class="mt-2 text-sm text-slate-500">Puedes volver al listado para consultar tus otros planes.</p></div>
        @else
            <ol class="divide-y divide-slate-100">
                @foreach($itinerario->diasItinerario as $dia)
                    <li class="flex gap-4 px-5 py-5 sm:px-6">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary-50 text-sm font-bold text-primary-700">{{ $dia->numero_dia }}</span>
                        <div class="min-w-0 flex-1"><div class="flex flex-wrap items-baseline justify-between gap-2"><h3 class="font-semibold text-slate-900">Día {{ $dia->numero_dia }}</h3><p class="text-sm text-slate-500">{{ $dia->fecha ? $dia->fecha->format('d/m/Y') : 'Fecha por definir' }}</p></div><p class="mt-2 text-sm leading-6 text-slate-500">Este día aún no muestra actividades.</p></div>
                    </li>
                @endforeach
            </ol>
        @endif
        <div class="flex flex-col gap-3 border-t border-slate-100 bg-primary-50/40 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6"><p class="text-sm text-slate-600">Encuentra ideas para tu viaje en los catálogos.</p><div class="flex flex-wrap gap-4"><a href="{{ route('catalogo.actividades.index') }}" class="text-sm font-semibold text-primary-700 hover:underline">Ver actividades →</a><a href="{{ route('catalogo.rutas.index') }}" class="text-sm font-semibold text-primary-700 hover:underline">Ver rutas →</a></div></div>
    </section>

    <div class="mt-6 flex flex-col gap-4 border-t border-slate-200 pt-5 sm:flex-row sm:items-center sm:justify-between">
        <p class="max-w-xl text-sm leading-6 text-slate-500">Los datos de este plan son referenciales. El itinerario no representa una cotización ni una reserva.</p>
        <form method="POST" action="{{ route('itinerarios.destroy', $itinerario) }}" onsubmit="return confirm('¿Eliminar este itinerario? Esta acción no se puede deshacer.');">
            @csrf
            @method('DELETE')
            <button class="rounded-lg border border-red-200 px-4 py-2.5 text-sm font-semibold text-red-700 transition hover:bg-red-50" type="submit">Eliminar itinerario</button>
        </form>
    </div>
</main>
@endsection
