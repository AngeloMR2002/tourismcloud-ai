@extends('layouts.app', ['title' => 'Dashboard'])

@section('content')
<main class="tc-workspace mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    <header class="tc-page-heading flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-semibold text-primary-700">{{ $esAdministrador ? 'Administración' : 'Mi espacio' }}</p>
            <h1 class="mt-1 text-3xl font-bold tracking-tight text-slate-900">{{ $esAdministrador ? 'Resumen de la plataforma' : 'Organiza tu próximo viaje' }}</h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">{{ $esAdministrador ? 'Consulta la actividad registrada y accede a la gestión de usuarios y destinos.' : 'Guarda tus preferencias, crea un plan y consulta sus días en la agenda.' }}</p>
        </div>
        @if($esAdministrador)
            <a href="{{ route('admin.usuarios.index') }}" class="tc-btn-primary justify-center">Gestionar usuarios</a>
        @else
            <a href="{{ route('itinerarios.create') }}" class="tc-btn-primary justify-center"><span aria-hidden="true">+</span> Nuevo itinerario</a>
        @endif
    </header>

    <section class="mt-7 grid gap-4 sm:grid-cols-2 {{ $esAdministrador ? 'lg:grid-cols-3' : '' }}" aria-label="Resumen actual">
        <article class="tc-card p-5">
            <p class="text-sm text-slate-500">{{ $esAdministrador ? 'Itinerarios registrados' : 'Mis itinerarios' }}</p>
            <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($totalItinerarios) }}</p>
            <p class="mt-2 text-xs text-slate-500">{{ $esAdministrador ? 'Planes creados en la plataforma' : 'Planes guardados en tu cuenta' }}</p>
        </article>
        <article class="tc-card p-5">
            <p class="text-sm text-slate-500">Destinos activos</p>
            <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($totalDestinos) }}</p>
            <p class="mt-2 text-xs text-slate-500">Disponibles para explorar y planificar</p>
        </article>
        @if($esAdministrador)
            <article class="tc-card p-5">
                <p class="text-sm text-slate-500">Usuarios registrados</p>
                <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($totalUsuarios) }}</p>
                <p class="mt-2 text-xs text-slate-500">Cuentas de la plataforma</p>
            </article>
        @endif
    </section>

    @if(! $esAdministrador)
        <section class="tc-card mt-6 p-5 sm:p-6" aria-labelledby="empezar-heading">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div><h2 id="empezar-heading" class="text-lg font-bold text-slate-900">Empieza con un destino</h2><p class="mt-1 text-sm text-slate-500">Elige dónde ir y cuántos días tienes. Completarás los datos en el siguiente paso.</p></div>
                <a href="{{ route('catalogo.atractivos.index') }}" class="shrink-0 text-sm font-semibold text-primary-700 hover:underline">Explorar primero →</a>
            </div>
            <form method="GET" action="{{ route('itinerarios.create') }}" class="mt-5 grid gap-4 sm:grid-cols-[minmax(0,1fr)_8rem_auto] sm:items-end">
                <div>
                    <label for="destino-dashboard" class="mb-1.5 block text-sm font-semibold text-slate-700">Destino</label>
                    <select id="destino-dashboard" name="destino_id" class="tc-select" required>
                        <option value="">Selecciona un destino</option>
                        @foreach($destinosActivos as $destino)<option value="{{ $destino->id }}">{{ $destino->nombre }}</option>@endforeach
                    </select>
                </div>
                <div><label for="dias-dashboard" class="mb-1.5 block text-sm font-semibold text-slate-700">Días</label><input id="dias-dashboard" name="dias" type="number" min="1" max="60" value="2" required class="tc-input"></div>
                <button type="submit" class="tc-btn-primary justify-center">Continuar →</button>
            </form>
        </section>

        <nav class="mt-6 grid gap-4 md:grid-cols-3" aria-label="Pasos para planificar">
            <a href="{{ route('preferencias.turista', auth()->id()) }}" class="tc-card group flex gap-4 p-5 hover:border-primary-300">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary-50 text-sm font-bold text-primary-700">1</span>
                <div><h2 class="font-semibold text-slate-900">Tus preferencias</h2><p class="mt-1 text-sm leading-5 text-slate-500">Define los intereses que quieres tener en cuenta.</p><span class="mt-3 inline-block text-sm font-semibold text-primary-700">Revisar preferencias →</span></div>
            </a>
            <a href="{{ route('itinerarios.index') }}" class="tc-card group flex gap-4 p-5 hover:border-primary-300">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary-50 text-sm font-bold text-primary-700">2</span>
                <div><h2 class="font-semibold text-slate-900">Tus itinerarios</h2><p class="mt-1 text-sm leading-5 text-slate-500">Consulta el destino, duración y presupuesto de tus planes.</p><span class="mt-3 inline-block text-sm font-semibold text-primary-700">Ver itinerarios →</span></div>
            </a>
            <a href="{{ route('agenda.index') }}" class="tc-card group flex gap-4 p-5 hover:border-primary-300">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary-50 text-sm font-bold text-primary-700">3</span>
                <div><h2 class="font-semibold text-slate-900">Tu agenda</h2><p class="mt-1 text-sm leading-5 text-slate-500">Revisa los planes en preparación y sus fechas.</p><span class="mt-3 inline-block text-sm font-semibold text-primary-700">Consultar agenda →</span></div>
            </a>
        </nav>
    @else
        <nav class="mt-6 flex flex-wrap gap-3" aria-label="Gestión de plataforma"><a href="{{ route('admin.usuarios.index') }}" class="tc-btn-outline">Usuarios</a><a href="{{ route('admin.destinos.index') }}" class="tc-btn-outline">Destinos</a></nav>
    @endif

    <section class="tc-card mt-7 overflow-hidden" aria-labelledby="recientes-heading">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 sm:px-6">
            <h2 id="recientes-heading" class="text-lg font-bold text-slate-900">{{ $esAdministrador ? 'Itinerarios recientes' : 'Tus últimos itinerarios' }}</h2>
            @if(! $esAdministrador)<a href="{{ route('itinerarios.index') }}" class="text-sm font-semibold text-primary-700 hover:underline">Ver todos →</a>@endif
        </div>
        @if($itinerariosRecientes->isEmpty())
            <div class="px-5 py-10 text-center">
                <h3 class="font-semibold text-slate-900">{{ $esAdministrador ? 'Aún no hay itinerarios registrados' : 'Todavía no has creado un itinerario' }}</h3>
                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">{{ $esAdministrador ? 'Los planes que se creen en la plataforma aparecerán aquí.' : 'Elige un destino y guarda los datos de tu primer viaje.' }}</p>
                @if(! $esAdministrador)<a href="{{ route('itinerarios.create') }}" class="tc-btn-primary mt-5">Crear mi primer itinerario</a>@endif
            </div>
        @else
            <div class="divide-y divide-slate-100">
                @foreach($itinerariosRecientes as $itinerario)
                    <article class="flex flex-col gap-3 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                        <div class="min-w-0"><h3 class="font-semibold text-slate-900">{{ $itinerario->nombre }}</h3><p class="mt-1 text-sm text-slate-500">{{ $itinerario->destino?->nombre ?? 'Destino no disponible' }} · {{ $itinerario->dias }} {{ $itinerario->dias === 1 ? 'día' : 'días' }} · {{ $itinerario->created_at?->format('d/m/Y') }}</p></div>
                        <div class="flex items-center gap-4"><span class="tc-chip">{{ ucfirst($itinerario->estado) }}</span>@if(! $esAdministrador)<a href="{{ route('itinerarios.show', $itinerario) }}" class="shrink-0 text-sm font-semibold text-primary-700 hover:underline">Ver plan →</a>@endif</div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
</main>
@endsection
