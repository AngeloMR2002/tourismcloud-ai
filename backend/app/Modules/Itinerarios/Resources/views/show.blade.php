@extends('layouts.app', ['title' => $itinerario->nombre])

@section('content')
@php
    $estado = ['generado' => ['Generado', '#e6f4f5', '#08565D'], 'guardado' => ['Guardado', '#fdf6ec', '#85480C'], 'activo' => ['Activo', '#dcfce7', '#15803d'], 'finalizado' => ['Finalizado', '#f3f4f6', '#4b5563'], 'cancelado' => ['Cancelado', '#fef2f2', '#b91c1c']][$itinerario->estado] ?? [ucfirst($itinerario->estado), '#f3f4f6', '#4b5563'];
    $primerDiaConFecha = $itinerario->diasItinerario->first(fn ($dia) => $dia->fecha);
@endphp
<main class="mx-auto max-w-6xl px-4 py-8 sm:px-6 sm:py-10 lg:px-8">
    <a href="{{ route('itinerarios.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-primary-700 hover:underline"><span aria-hidden="true">←</span> Mis itinerarios</a>

    <section class="relative mt-5 overflow-hidden rounded-3xl px-6 py-8 sm:px-8 sm:py-10" style="background: linear-gradient(120deg, var(--color-primary-900), var(--color-primary-600));">
        <div class="absolute -right-12 -top-16 h-44 w-44 rounded-full border-[26px] border-white/10"></div>
        <div class="relative max-w-3xl"><p class="text-xs font-bold uppercase tracking-widest text-white/65">{{ $itinerario->destino?->nombre ?? 'Destino no disponible' }}</p><div class="mt-3 flex flex-wrap items-center gap-3"><h1 class="text-3xl font-extrabold text-white sm:text-4xl">{{ $itinerario->nombre }}</h1><span class="rounded-full px-3 py-1 text-xs font-bold" style="background: {{ $estado[1] }}; color: {{ $estado[2] }};">{{ $estado[0] }}</span></div><p class="mt-4 max-w-2xl whitespace-pre-line text-sm leading-6 text-white/75">{{ $itinerario->descripcion ?: 'Aún no agregaste una descripción para este itinerario.' }}</p></div>
    </section>

    <section class="mt-6 grid gap-6 lg:grid-cols-[minmax(0,1fr)_17rem]">
        <div class="space-y-6">
            <section class="tc-card p-5 sm:p-6"><div class="flex items-center justify-between gap-4"><div><p class="text-xs font-bold uppercase tracking-widest text-primary-600">Plan del viaje</p><h2 class="mt-1 text-xl font-bold text-slate-900">Días del itinerario</h2></div><span class="tc-chip">{{ $itinerario->dias }} {{ $itinerario->dias === 1 ? 'día' : 'días' }}</span></div>
                <ol class="mt-5 space-y-3">@foreach($itinerario->diasItinerario as $dia)<li class="flex gap-4 rounded-2xl border border-slate-100 bg-slate-50 p-4"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white font-bold text-primary-700 shadow-sm">{{ $dia->numero_dia }}</span><div><h3 class="font-semibold text-slate-900">Día {{ $dia->numero_dia }}</h3><p class="mt-1 text-sm text-slate-500">{{ $dia->fecha ? $dia->fecha->format('d/m/Y') : 'Fecha por definir' }}</p><p class="mt-2 text-xs leading-5 text-slate-500">Las actividades de este día aún no se han asociado al itinerario.</p></div></li>@endforeach</ol>
            </section>

            <section class="rounded-2xl border border-tertiary-100 bg-tertiary-50 p-5"><div class="flex gap-3"><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-tertiary-600 shadow-sm"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v4m0 4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg></span><div><h2 class="font-bold text-tertiary-800">Plan referencial</h2><p class="mt-1 text-sm leading-6 text-tertiary-700">El presupuesto y la duración sirven como guía de tu planificación; no constituyen una cotización ni una reserva.</p></div></div></section>
        </div>

        <aside class="space-y-5"><section class="tc-card p-5"><h2 class="font-bold text-slate-900">Resumen</h2><dl class="mt-4 space-y-4 text-sm"><div class="flex items-start justify-between gap-3"><dt class="text-slate-500">Destino</dt><dd class="text-right font-semibold text-slate-800">{{ $itinerario->destino?->nombre ?? 'No disponible' }}</dd></div><div class="flex items-start justify-between gap-3"><dt class="text-slate-500">Duración</dt><dd class="text-right font-semibold text-slate-800">{{ $itinerario->dias }} {{ $itinerario->dias === 1 ? 'día' : 'días' }}</dd></div><div class="flex items-start justify-between gap-3"><dt class="text-slate-500">Inicio</dt><dd class="text-right font-semibold text-slate-800">{{ $primerDiaConFecha?->fecha?->format('d/m/Y') ?? 'Por definir' }}</dd></div><div class="flex items-start justify-between gap-3 border-t border-slate-100 pt-4"><dt class="text-slate-500">Presupuesto</dt><dd class="text-right font-semibold text-primary-700">{{ $itinerario->presupuesto_total ? 'S/ '.number_format((float) $itinerario->presupuesto_total, 2) : 'Por definir' }}</dd></div></dl></section>
            <a href="{{ route('agenda.index') }}" class="tc-btn-outline w-full justify-center">Ver en mi agenda</a>
            <form method="POST" action="{{ route('itinerarios.destroy', $itinerario) }}" onsubmit="return confirm('¿Eliminar este itinerario? Esta acción no se puede deshacer.');">@csrf @method('DELETE')<button class="w-full rounded-lg border border-red-200 px-4 py-2.5 text-sm font-semibold text-red-700 transition hover:bg-red-50" type="submit">Eliminar itinerario</button></form>
        </aside>
    </section>
</main>
@endsection
