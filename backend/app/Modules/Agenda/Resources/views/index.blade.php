@extends('layouts.app', ['title' => 'Agenda'])

@section('content')
<main class="tc-workspace mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
    <header class="tc-page-heading flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
        <div><p class="text-sm font-semibold text-primary-700">Mi planificación</p><h1 class="mt-1 text-3xl font-bold tracking-tight text-slate-900">Agenda de itinerarios</h1><p class="mt-2 text-sm leading-6 text-slate-500">Consulta tus itinerarios en preparación y las fechas que les asignaste.</p></div>
        <div class="flex flex-wrap gap-3"><a class="tc-btn-outline justify-center" href="{{ route('itinerarios.index') }}">Mis itinerarios</a><a class="tc-btn-primary justify-center" href="{{ route('itinerarios.create') }}"><span aria-hidden="true">+</span> Crear itinerario</a></div>
    </header>

    <section class="mt-7 grid gap-4 sm:grid-cols-3" aria-label="Resumen de agenda">
        <article class="tc-card p-5"><p class="text-sm text-slate-500">Planes en agenda</p><p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($totalProgramados) }}</p></article>
        <article class="tc-card p-5"><p class="text-sm text-slate-500">Presupuesto referencial total</p><p class="mt-2 text-2xl font-bold tracking-tight text-slate-900">S/ {{ number_format($presupuestoReferencial, 2) }}</p></article>
        <article class="tc-card p-5"><p class="text-sm text-slate-500">Estado de tus planes</p><p class="mt-3 text-sm font-semibold text-slate-800">{{ ($resumenEstados['activo'] ?? 0) }} activos</p><p class="mt-1 text-sm text-slate-500">{{ ($resumenEstados['generado'] ?? 0) + ($resumenEstados['guardado'] ?? 0) }} en preparación</p></article>
    </section>

    <section class="tc-card mt-7 overflow-hidden" aria-labelledby="planes-heading">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 sm:px-6"><h2 id="planes-heading" class="text-lg font-bold text-slate-900">En tu agenda</h2><span class="text-sm text-slate-500">Planes más recientes primero</span></div>
        @if($itinerarios->isEmpty())
            <div class="px-6 py-12 text-center">
                <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-primary-50 text-primary-700"><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 3v4M8 3v4M3 10h18"/></svg></span>
                <h3 class="mt-4 text-lg font-semibold text-slate-900">Aún no hay planes en tu agenda</h3>
                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">Crea un itinerario para organizar los días de tu viaje. Si indicas una fecha de inicio, aparecerá junto al plan.</p>
                <a class="tc-btn-primary mt-5" href="{{ route('itinerarios.create') }}">Crear mi primer itinerario</a>
            </div>
        @else
            <ol class="divide-y divide-slate-100">
                @foreach($itinerarios as $itinerario)
                    @php
                        $primerDiaConFecha = $itinerario->diasItinerario->first(fn ($dia) => $dia->fecha);
                        $estado = ['generado' => ['Generado', '#e6f4f5', '#08565D'], 'guardado' => ['Guardado', '#fdf6ec', '#85480C'], 'activo' => ['Activo', '#dcfce7', '#15803d']][$itinerario->estado] ?? [ucfirst($itinerario->estado), '#f3f4f6', '#4b5563'];
                        $meses = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
                    @endphp
                    <li class="flex gap-4 px-5 py-5 sm:gap-6 sm:px-6">
                        <div class="flex w-14 shrink-0 flex-col items-center rounded-xl border border-primary-100 bg-primary-50 py-2 text-center text-primary-700">
                            @if($primerDiaConFecha)
                                <span class="text-xs font-semibold uppercase">{{ $meses[(int) $primerDiaConFecha->fecha->format('n') - 1] }}</span><span class="text-2xl font-bold leading-7">{{ $primerDiaConFecha->fecha->format('d') }}</span><span class="mt-1 text-[10px]">{{ $primerDiaConFecha->fecha->format('Y') }}</span>
                            @else
                                <svg class="mt-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 3v4M8 3v4M3 10h18"/></svg><span class="mt-2 text-[10px] font-semibold">Sin fecha</span>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center justify-between gap-3"><p class="text-sm font-semibold text-primary-700">{{ $itinerario->destino?->nombre ?? 'Destino no disponible' }}</p><span class="rounded-full px-2.5 py-1 text-xs font-semibold" style="background: {{ $estado[1] }}; color: {{ $estado[2] }};">{{ $estado[0] }}</span></div>
                            <h3 class="mt-1 break-words text-lg font-bold leading-6 text-slate-900"><a href="{{ route('itinerarios.show', $itinerario) }}" class="hover:text-primary-700">{{ $itinerario->nombre }}</a></h3>
                            <p class="mt-2 text-sm leading-6 text-slate-500">{{ $primerDiaConFecha ? 'Inicio: '.$primerDiaConFecha->fecha->format('d/m/Y') : 'Fecha de inicio por definir' }} · {{ $itinerario->dias }} {{ $itinerario->dias === 1 ? 'día' : 'días' }}</p>
                            <a href="{{ route('itinerarios.show', $itinerario) }}" class="mt-3 inline-block text-sm font-semibold text-primary-700 hover:underline">Consultar día a día →</a>
                        </div>
                    </li>
                @endforeach
            </ol>
        @endif
    </section>

    @if(! $itinerarios->isEmpty())<div class="mt-7">{{ $itinerarios->links() }}</div>@endif

    <p class="mt-5 max-w-3xl text-sm leading-6 text-slate-500">Aquí aparecen los planes generados, guardados o activos. Las fechas se muestran cuando las indicas al crear el itinerario; los que aún no tienen fecha también permanecen en tu agenda.</p>
</main>
@endsection
