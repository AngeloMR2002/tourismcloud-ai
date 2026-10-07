@extends('actividades::layouts.app')
@section('content')
<div class="tc-travel-page tc-travel-detail-page max-w-5xl mx-auto px-4 sm:px-6 py-8 space-y-6">
    <a href="{{ route('catalogo.rutas.index') }}" class="text-sm text-primary-700 underline">← Volver a las rutas</a>
    <section class="tc-detail-summary bg-white border rounded-2xl p-6 sm:p-8 space-y-4">
        <span class="tc-badge-tipo">{{ $ruta->es_propuesta ? 'Propuesta TourismCloud' : 'Recorrido publicado' }}</span>
        <h1 class="text-3xl font-extrabold">{{ $ruta->nombre }}</h1>
        <p class="text-sm text-gray-600">{{ $ruta->destino->nombre }} · {{ $ruta->transporte_texto }}</p>
        @if($ruta->es_propuesta)<p class="text-sm bg-amber-50 border border-amber-200 p-4 rounded-xl">Orden de visita sugerido por TourismCloud entre lugares reales. No es un tour oficial, una reserva ni un paquete contratado. Las condiciones de acceso se consultan en cada lugar.</p>@endif
        <p class="text-sm whitespace-pre-line">{{ $ruta->descripcion }}</p>
        <dl class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
            <div><dt class="text-gray-500">Duración documentada</dt><dd class="font-semibold">{{ $ruta->duracion_estimada_horas !== null ? $ruta->duracion_estimada_horas.' horas' : 'Por confirmar' }}</dd></div>
            <div><dt class="text-gray-500">Distancia documentada</dt><dd class="font-semibold">{{ $ruta->distancia_km !== null ? $ruta->distancia_km.' km' : 'Por confirmar' }}</dd></div>
            <div><dt class="text-gray-500">Dificultad</dt><dd class="font-semibold">{{ $ruta->nivel_dificultad ? ucfirst($ruta->nivel_dificultad) : 'Por confirmar' }}</dd></div>
            <div><dt class="text-gray-500">Costo</dt><dd class="font-semibold text-primary-700">{{ $ruta->costo_formateado }}</dd></div>
        </dl>
    </section>
    <div class="tc-detail-layout">
    <div class="space-y-6">
    @if($ruta->imagen_portada)@include('actividades::catalogo.fotografia', ['registro'=>$ruta])@endif
    <section class="bg-white border rounded-xl p-6">
        <h2 class="text-xl font-bold mb-5">Secuencia de paradas</h2>
        <ol class="space-y-6">
            @foreach($ruta->puntos as $punto)
                <li class="flex gap-4">
                    <span class="shrink-0 w-9 h-9 rounded-full bg-primary-700 text-white flex items-center justify-center font-bold">{{ $punto->orden }}</span>
                    <div class="border-b pb-5 flex-1 space-y-2">
                        <h3 class="font-bold">{{ $punto->nombre_parada }}</h3>
                        @if($punto->descripcion_parada)<p class="text-sm whitespace-pre-line">{{ $punto->descripcion_parada }}</p>@endif
                        <p class="text-xs text-gray-600">Estadía: {{ $punto->tiempo_estadia_min !== null ? $punto->tiempo_estadia_min.' min' : 'por confirmar' }}@if(!$loop->first) · Traslado: {{ $punto->tiempo_traslado_min !== null ? $punto->tiempo_traslado_min.' min' : 'por confirmar' }} · Distancia: {{ $punto->distancia_desde_anterior_km !== null ? $punto->distancia_desde_anterior_km.' km' : 'por confirmar' }}@endif</p>
                        @if($punto->atractivo?->fuente_url)<a href="{{ $punto->atractivo->fuente_url }}" target="_blank" rel="noopener noreferrer" class="text-sm underline text-primary-700">Fuente de este lugar ↗</a>@endif
                    </div>
                </li>
            @endforeach
        </ol>
    </section>
    @if($ruta->recomendaciones)<section class="bg-white border rounded-xl p-6"><h2 class="font-bold">Recomendaciones publicadas</h2><p class="text-sm mt-2 whitespace-pre-line">{{ $ruta->recomendaciones }}</p></section>@endif
    </div>
    <aside aria-label="Fuentes de la ruta">
        @include('actividades::catalogo.fuentes', ['registro'=>$ruta])
    </aside>
    </div>
</div>
@endsection
