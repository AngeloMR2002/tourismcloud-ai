@extends('actividades::layouts.app')
@section('content')
<div class="tc-travel-page tc-travel-detail-page max-w-5xl mx-auto px-4 sm:px-6 py-8 space-y-6">
    <a href="{{ route('catalogo.actividades.index') }}" class="text-sm text-primary-700 underline">← Volver a las actividades</a>
    <section class="tc-detail-summary bg-white border rounded-2xl p-6 sm:p-8 space-y-4">
        <div class="flex flex-wrap gap-2"><span class="tc-chip">{{ ucfirst($actividad->categoria) }}</span><span class="tc-badge-tipo">{{ $actividad->tipo_registro }}</span><span class="tc-badge-activo">Fuente revisada</span></div>
        <h1 class="text-3xl font-extrabold">{{ $actividad->nombre }}</h1>
        <p class="text-sm text-gray-600">{{ $actividad->destino->nombre }}@if($actividad->atractivo) · {{ $actividad->atractivo->nombre }}@endif</p>
        <div class="bg-teal-50 rounded-xl p-4"><p class="text-xl font-bold text-primary-700">{{ $actividad->precio_formateado }}</p><p class="text-xs text-gray-600 mt-1">No es una cotización ni una confirmación de disponibilidad.</p></div>
        <dl class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
            <div><dt class="text-gray-500">Duración</dt><dd class="font-semibold">{{ $actividad->duracion_formateada }}</dd></div>
            <div><dt class="text-gray-500">Horario publicado</dt><dd class="font-semibold">{{ $actividad->horario_inicio && $actividad->horario_fin ? substr($actividad->horario_inicio,0,5).' – '.substr($actividad->horario_fin,0,5) : 'Por confirmar' }}</dd></div>
            <div><dt class="text-gray-500">Capacidad publicada</dt><dd class="font-semibold">{{ $actividad->cupo_maximo !== null ? $actividad->cupo_maximo.' personas (no son cupos disponibles)' : 'Por confirmar' }}</dd></div>
            <div><dt class="text-gray-500">Dificultad</dt><dd class="font-semibold">{{ $actividad->nivel_dificultad ? ucfirst($actividad->nivel_dificultad) : 'Por confirmar' }}</dd></div>
            <div class="sm:col-span-2"><dt class="text-gray-500">Días de operación publicados</dt><dd>{{ $actividad->dias_operacion ? implode(', ', $actividad->dias_operacion) : 'Por confirmar' }}</dd></div>
        </dl>
        @if($actividad->tipo_registro === 'evento')
            <p class="text-sm bg-amber-50 rounded-lg p-3">Evento: {{ $actividad->evento_inicio?->format('d/m/Y H:i') ?? 'Por confirmar' }} → {{ $actividad->evento_fin?->format('d/m/Y H:i') ?? 'Por confirmar' }} (hora de Perú).</p>
        @endif
    </section>
    <div class="tc-detail-layout">
    <div class="space-y-6">
    @include('actividades::catalogo.fotografia', ['registro'=>$actividad])
    <section class="bg-white border rounded-xl p-6 space-y-4">
        <h2 class="text-xl font-bold">Sobre esta visita o actividad</h2>
        <p class="text-sm text-gray-700 whitespace-pre-line">{{ $actividad->descripcion }}</p>
        @foreach(['incluye'=>'Servicios incluidos publicados','no_incluye'=>'Servicios no incluidos publicados','requisitos'=>'Requisitos publicados','punto_encuentro'=>'Punto de encuentro publicado'] as $campo=>$texto)
            @if($actividad->$campo)<div><h3 class="font-semibold text-sm">{{ $texto }}</h3><p class="mt-1 text-sm whitespace-pre-line">{{ $actividad->$campo }}</p></div>@endif
        @endforeach
        @if($actividad->latitud !== null && $actividad->longitud !== null)
            <p class="text-sm">Coordenadas: {{ $actividad->latitud }}, {{ $actividad->longitud }}.
                <a href="https://www.openstreetmap.org/?mlat={{ $actividad->latitud }}&amp;mlon={{ $actividad->longitud }}#map=16/{{ $actividad->latitud }}/{{ $actividad->longitud }}" target="_blank" rel="noopener noreferrer" class="underline text-primary-700">Ver mapa ↗</a>
            </p>
        @endif
    </section>
    </div>
    <aside aria-label="Fuentes de la actividad">
        @include('actividades::catalogo.fuentes', ['registro'=>$actividad])
    </aside>
    </div>
</div>
@endsection
