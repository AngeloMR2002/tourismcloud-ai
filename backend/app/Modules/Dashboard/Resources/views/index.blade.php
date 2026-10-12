@extends('layouts.app', ['title' => 'Dashboard'])

@section('content')
<main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-10 lg:px-8">
    <section class="relative isolate overflow-hidden rounded-3xl px-6 py-9 sm:px-10 sm:py-11"
             style="background: linear-gradient(120deg, var(--color-primary-900), var(--color-primary-600)); box-shadow: var(--shadow-card-hover);">
        <div class="absolute -right-20 -top-24 -z-10 h-72 w-72 rounded-full border-[34px] border-white/10"></div>
        <div class="absolute -bottom-28 right-1/3 -z-10 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>

        <div class="relative grid items-center gap-8 lg:grid-cols-[minmax(0,1fr)_18rem]">
            <div class="max-w-3xl">
                <span class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-1.5 text-xs font-semibold uppercase tracking-wider text-white/90">
                    <span class="h-2 w-2 rounded-full bg-amber-300"></span>
                    {{ $esAdministrador ? 'Vista de administración' : 'Planificación turística personal' }}
                </span>
                <h1 class="mt-5 text-3xl font-extrabold leading-tight tracking-tight text-white sm:text-4xl" style="font-family: var(--font-display);">
                    {{ $esAdministrador ? 'Conoce lo que ocurre en la plataforma.' : 'Diseña tu próximo viaje con calma.' }}
                </h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-white/75 sm:text-base">
                    {{ $esAdministrador ? 'Consulta los indicadores generales y los itinerarios creados recientemente.' : 'Elige un destino y arma una propuesta que puedas guardar y revisar cuando quieras.' }}
                </p>

                @if($esAdministrador)
                    <div class="mt-6 flex flex-wrap gap-3">
                        <a href="{{ route('admin.usuarios.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-white px-4 py-2.5 text-sm font-bold text-primary-800 transition hover:bg-primary-50">
                            Gestionar usuarios <span aria-hidden="true">→</span>
                        </a>
                        <a href="{{ route('admin.destinos.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-white/30 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-white/10">Ver destinos</a>
                    </div>
                @else
                    <form method="GET" action="{{ route('itinerarios.create') }}" class="mt-6 grid max-w-3xl gap-3 rounded-2xl border border-white/20 bg-white/95 p-3 shadow-xl sm:grid-cols-[minmax(0,1fr)_7rem_auto] sm:p-4">
                        <label class="sr-only" for="destino-dashboard">Destino</label>
                        <select id="destino-dashboard" name="destino_id" class="tc-select border-0 bg-transparent font-medium shadow-none focus:shadow-none">
                            <option value="">Elige un destino para comenzar</option>
                            @foreach($destinosActivos as $destino)
                                <option value="{{ $destino->id }}">{{ $destino->nombre }}</option>
                            @endforeach
                        </select>
                        <label class="sr-only" for="dias-dashboard">Días</label>
                        <input id="dias-dashboard" name="dias" type="number" min="1" max="60" value="2" class="tc-input border-0 bg-transparent shadow-none focus:shadow-none" aria-label="Cantidad de días">
                        <button type="submit" class="tc-btn-primary justify-center whitespace-nowrap">Crear itinerario <span aria-hidden="true">→</span></button>
                    </form>
                @endif
            </div>

            <div class="hidden justify-center lg:flex" aria-hidden="true">
                <div class="relative flex h-48 w-48 items-center justify-center rounded-full border border-white/20 bg-white/10 shadow-2xl backdrop-blur-sm">
                    <div class="absolute inset-4 rounded-full border border-dashed border-white/30"></div>
                    <svg class="h-20 w-20 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.3" d="M3.055 11H5a2 2 0 0 1 2 2v1a2 2 0 0 0 2 2 2 2 0 0 1 2 2v2.945M8 3.935V5.5A2.5 2.5 0 0 0 10.5 8h.5a2 2 0 0 1 2 2 2 2 0 1 0 4 0 2 2 0 0 1 2-2h1.064M15 20.488V18a2 2 0 0 1 2-2h3.064M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="m9.5 12 1.8 1.8 3.6-3.6"/>
                    </svg>
                    <span class="absolute right-1 top-8 flex h-10 w-10 items-center justify-center rounded-2xl bg-amber-300 text-primary-900 shadow-lg">✦</span>
                    <span class="absolute bottom-4 left-3 flex h-9 w-9 items-center justify-center rounded-xl bg-white text-primary-600 shadow-lg">⌖</span>
                </div>
            </div>
        </div>
    </section>

    <section class="mt-8" aria-labelledby="metricas-heading">
        <div class="mb-4"><p class="text-xs font-bold uppercase tracking-widest text-primary-600">Resumen</p><h2 id="metricas-heading" class="mt-1 text-2xl font-bold text-slate-900">{{ $esAdministrador ? 'Indicadores de la plataforma' : 'Tu planificación en un vistazo' }}</h2></div>
        <div class="grid grid-cols-1 gap-4 @if($esAdministrador) sm:grid-cols-3 @else sm:grid-cols-2 @endif">
            <article class="tc-card flex items-center gap-4 p-5">
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-primary-50 text-primary-700"><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg></span>
                <div><p class="text-sm text-slate-500">{{ $esAdministrador ? 'Itinerarios' : 'Mis itinerarios' }}</p><p class="mt-1 text-3xl font-bold text-primary-700">{{ number_format($totalItinerarios) }}</p></div>
            </article>
            <article class="tc-card flex items-center gap-4 p-5">
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-tertiary-50 text-tertiary-600"><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 21s7-5.2 7-12a7 7 0 1 0-14 0c0 6.8 7 12 7 12Z"/><circle cx="12" cy="9" r="2.2"/></svg></span>
                <div><p class="text-sm text-slate-500">Destinos activos</p><p class="mt-1 text-3xl font-bold text-primary-700">{{ number_format($totalDestinos) }}</p></div>
            </article>
            @if($esAdministrador)
                <article class="tc-card flex items-center gap-4 p-5">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-secondary-50 text-secondary-600"><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 20v-1.5A3.5 3.5 0 0 0 12.5 15h-5A3.5 3.5 0 0 0 4 18.5V20m11-10a3 3 0 1 0-6 0 3 3 0 0 0 6 0Zm2 5a3 3 0 0 1 3 3V20m-1-10a2.5 2.5 0 1 0-1.6-4.4"/></svg></span>
                    <div><p class="text-sm text-slate-500">Usuarios</p><p class="mt-1 text-3xl font-bold text-primary-700">{{ number_format($totalUsuarios) }}</p></div>
                </article>
            @endif
        </div>
    </section>

    <section class="mt-8 grid gap-6 lg:grid-cols-[minmax(0,1.6fr)_minmax(15rem,0.7fr)]">
        <div class="tc-card overflow-hidden">
            <div class="flex flex-col gap-2 border-b border-slate-100 p-5 sm:flex-row sm:items-center sm:justify-between"><div><p class="text-xs font-bold uppercase tracking-widest text-primary-600">Actividad reciente</p><h2 class="mt-1 text-lg font-bold text-slate-900">{{ $esAdministrador ? 'Itinerarios recientes' : 'Mis itinerarios recientes' }}</h2></div>@if(! $esAdministrador)<a class="text-sm font-semibold text-primary-700 hover:underline" href="{{ route('itinerarios.index') }}">Ver todos →</a>@endif</div>
            @if($itinerariosRecientes->isEmpty())
                <div class="p-7 text-center"><p class="font-semibold text-slate-800">Aún no hay itinerarios para mostrar.</p><p class="mt-1 text-sm text-slate-500">{{ $esAdministrador ? 'Los nuevos planes aparecerán aquí.' : 'Crea uno para empezar a organizar tu viaje.' }}</p>@if(! $esAdministrador)<a href="{{ route('itinerarios.create') }}" class="tc-btn-primary mt-5">Crear itinerario</a>@endif</div>
            @else
                <div class="overflow-x-auto"><table class="tc-table min-w-full"><thead><tr><th>Itinerario</th><th>Destino</th><th>Estado</th><th>Creado</th></tr></thead><tbody>@foreach($itinerariosRecientes as $itinerario)<tr><td class="font-semibold text-slate-800">{{ $itinerario->nombre }}</td><td>{{ $itinerario->destino?->nombre ?? 'Destino no disponible' }}</td><td><span class="tc-chip">{{ ucfirst($itinerario->estado) }}</span></td><td>{{ $itinerario->created_at?->format('d/m/Y') }}</td></tr>@endforeach</tbody></table></div>
            @endif
        </div>

        <aside class="grid gap-4 sm:grid-cols-2 lg:grid-cols-1">
            @if($esAdministrador)
                <a href="{{ route('admin.usuarios.index') }}" class="tc-card group flex flex-col p-5"><span class="flex h-11 w-11 items-center justify-center rounded-xl bg-secondary-50 text-secondary-600"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 20v-1.5A3.5 3.5 0 0 0 12.5 15h-5A3.5 3.5 0 0 0 4 18.5V20m11-10a3 3 0 1 0-6 0 3 3 0 0 0 6 0Z"/></svg></span><h3 class="mt-4 font-bold text-slate-900">Usuarios</h3><p class="mt-1 flex-1 text-sm leading-6 text-slate-500">Administra cuentas y sus roles dentro de la plataforma.</p><span class="mt-4 text-sm font-semibold text-primary-700">Ir a usuarios <span class="transition group-hover:translate-x-1">→</span></span></a>
                <a href="{{ route('admin.destinos.index') }}" class="tc-card group flex flex-col p-5"><span class="flex h-11 w-11 items-center justify-center rounded-xl bg-tertiary-50 text-tertiary-600"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 21s7-5.2 7-12a7 7 0 1 0-14 0c0 6.8 7 12 7 12Z"/><circle cx="12" cy="9" r="2"/></svg></span><h3 class="mt-4 font-bold text-slate-900">Destinos</h3><p class="mt-1 flex-1 text-sm leading-6 text-slate-500">Revisa los destinos activos disponibles en el catálogo.</p><span class="mt-4 text-sm font-semibold text-primary-700">Ver destinos <span class="transition group-hover:translate-x-1">→</span></span></a>
            @else
                <a href="{{ route('itinerarios.index') }}" class="tc-card group flex flex-col p-5"><span class="flex h-11 w-11 items-center justify-center rounded-xl bg-primary-50 text-primary-700"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 5h18M6 3v4m12-4v4M5 9h14v11H5zM8 13h3m2 0h3m-8 3h3"/></svg></span><h3 class="mt-4 font-bold text-slate-900">Mis itinerarios</h3><p class="mt-1 flex-1 text-sm leading-6 text-slate-500">Crea y consulta los planes que has guardado para tus viajes.</p><span class="mt-4 text-sm font-semibold text-primary-700">Ver itinerarios <span class="transition group-hover:translate-x-1">→</span></span></a>
                <a href="{{ route('agenda.index') }}" class="tc-card group flex flex-col p-5"><span class="flex h-11 w-11 items-center justify-center rounded-xl bg-tertiary-50 text-tertiary-600"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="16" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 3v4M8 3v4M3 10h18"/></svg></span><h3 class="mt-4 font-bold text-slate-900">Agenda</h3><p class="mt-1 flex-1 text-sm leading-6 text-slate-500">Mantén a mano los itinerarios que están en planificación.</p><span class="mt-4 text-sm font-semibold text-primary-700">Ver agenda <span class="transition group-hover:translate-x-1">→</span></span></a>
            @endif
        </aside>
    </section>
</main>
@endsection
