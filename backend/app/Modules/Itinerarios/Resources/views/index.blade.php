@extends('layouts.app', ['title' => 'Mis itinerarios'])

@section('content')
<main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-10 lg:px-8">
    <section class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
        <div><span class="tc-chip">Planificación turística</span><h1 class="mt-3 text-3xl font-extrabold text-primary-700">Mis itinerarios</h1><p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">Crea, consulta y organiza las propuestas de viaje que pertenecen a tu cuenta.</p></div>
        <a class="tc-btn-primary justify-center" href="{{ route('itinerarios.create') }}"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14m-7-7h14"/></svg> Nuevo itinerario</a>
    </section>

    <form method="GET" class="tc-card mt-7 grid gap-4 p-4 sm:grid-cols-[minmax(0,1fr)_13rem_auto] sm:items-end sm:p-5" aria-label="Filtrar itinerarios">
        <div><label for="buscar" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-primary-700">Buscar</label><input id="buscar" class="tc-input" name="buscar" value="{{ request('buscar') }}" placeholder="Nombre o descripción"></div>
        <div><label for="destino_id" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-primary-700">Destino</label><select id="destino_id" class="tc-select" name="destino_id"><option value="">Todos los destinos</option>@foreach($destinos as $destino)<option value="{{ $destino->id }}" @selected((string) request('destino_id') === (string) $destino->id)>{{ $destino->nombre }}</option>@endforeach</select></div>
        <div class="flex gap-2"><button class="tc-btn-primary justify-center" type="submit">Filtrar</button><a class="tc-btn-outline justify-center" href="{{ route('itinerarios.index') }}">Limpiar</a></div>
    </form>

    @if(session('exito'))<div class="tc-alert-exito mt-5" role="status"><svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 13 4 4L19 7"/></svg>{{ session('exito') }}</div>@endif

    <div class="mt-7 flex items-center justify-between gap-4"><p class="text-sm text-slate-500">{{ $itinerarios->total() }} {{ $itinerarios->total() === 1 ? 'itinerario encontrado' : 'itinerarios encontrados' }}</p><a href="{{ route('agenda.index') }}" class="text-sm font-semibold text-primary-700 hover:underline">Ver mi agenda →</a></div>

    @if($itinerarios->isEmpty())
        <section class="tc-card mt-4 p-8 text-center sm:p-12"><span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-primary-50 text-primary-700"><svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 5h16M7 3v4m10-4v4M5 9h14v11H5zM8 13h3m2 0h3m-8 3h3"/></svg></span><h2 class="mt-5 font-bold text-slate-900">Aún no tienes itinerarios</h2><p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">Crea tu primer plan para organizar el destino, los días y el presupuesto que tienes en mente.</p><a class="tc-btn-primary mt-5" href="{{ route('itinerarios.create') }}">Crear itinerario</a></section>
    @else
        <div class="mt-4 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
            @foreach($itinerarios as $itinerario)
                @php($estado = ['generado' => ['Generado', '#e6f4f5', '#08565D'], 'guardado' => ['Guardado', '#fdf6ec', '#85480C'], 'activo' => ['Activo', '#dcfce7', '#15803d'], 'finalizado' => ['Finalizado', '#f3f4f6', '#4b5563'], 'cancelado' => ['Cancelado', '#fef2f2', '#b91c1c']][$itinerario->estado] ?? [ucfirst($itinerario->estado), '#f3f4f6', '#4b5563'])
                <article class="tc-card group flex min-h-72 flex-col overflow-hidden">
                    <div class="flex items-start justify-between gap-3 border-b border-primary-100 bg-primary-50 px-5 py-4"><div class="flex items-center gap-3"><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-primary-700 shadow-sm"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 21s7-5.2 7-12a7 7 0 1 0-14 0c0 6.8 7 12 7 12Z"/><circle cx="12" cy="9" r="2"/></svg></span><div><p class="text-[0.68rem] font-bold uppercase tracking-widest text-primary-600">Destino</p><p class="mt-0.5 text-sm font-semibold text-slate-800">{{ $itinerario->destino?->nombre ?? 'Destino no disponible' }}</p></div></div><span class="rounded-full px-2.5 py-1 text-xs font-semibold" style="background: {{ $estado[1] }}; color: {{ $estado[2] }};">{{ $estado[0] }}</span></div>
                    <div class="flex flex-1 flex-col p-5"><h2 class="text-lg font-bold text-slate-900">{{ $itinerario->nombre }}</h2><p class="mt-2 flex-1 text-sm leading-6 text-slate-600">{{ $itinerario->descripcion ?: 'Aún no agregaste una descripción para este viaje.' }}</p><dl class="mt-5 grid grid-cols-2 gap-3 border-t border-slate-100 pt-4 text-sm"><div><dt class="text-xs text-slate-500">Duración</dt><dd class="mt-1 font-semibold text-slate-800">{{ $itinerario->dias }} {{ $itinerario->dias === 1 ? 'día' : 'días' }}</dd></div><div><dt class="text-xs text-slate-500">Presupuesto</dt><dd class="mt-1 font-semibold text-slate-800">{{ $itinerario->presupuesto_total ? 'S/ '.number_format((float) $itinerario->presupuesto_total, 2) : 'Por definir' }}</dd></div></dl><a class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-primary-700 hover:underline" href="{{ route('itinerarios.show', $itinerario) }}">Ver detalle <span class="transition group-hover:translate-x-1">→</span></a></div>
                </article>
            @endforeach
        </div>
        <div class="mt-8">{{ $itinerarios->links() }}</div>
    @endif
</main>
@endsection
