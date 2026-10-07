@extends('actividades::layouts.app')
@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-2">Importar lugares reales</h1>
    <p class="text-sm text-gray-600 mb-6">Los nombres y coordenadas llegan desde OpenStreetMap. Revisa cada fuente antes de crear actividades. La importación no aporta tarifas ni cupos.</p>
    <form method="POST" action="{{ route('operador.importaciones.store') }}" class="bg-white border rounded-xl p-5 flex flex-wrap items-end gap-4 mb-8">
        @csrf
        <label>Destino
            <select name="destino_id" required class="tc-select mt-1">
                @foreach($destinos as $destino)
                    <option value="{{ $destino->id }}">{{ $destino->nombre }}</option>
                @endforeach
            </select>
        </label>
        <button class="tc-btn-primary">Importar desde OpenStreetMap</button>
        <span class="text-xs text-gray-500">Caché de 24 horas para ahorrar consultas.</span>
    </form>
    <div class="space-y-4">
        @forelse($lugares as $lugar)
            <article class="bg-white border rounded-xl p-5">
                <div class="flex flex-wrap justify-between gap-3">
                    <div>
                        <h2 class="font-bold">{{ $lugar->nombre }}</h2>
                        <p class="text-sm text-gray-600">{{ $lugar->destino?->nombre }} · {{ $lugar->latitud !== null && $lugar->longitud !== null ? $lugar->latitud.', '.$lugar->longitud : 'Coordenadas por confirmar' }}</p>
                        <p class="text-xs mt-2">Estado: {{ $lugar->estado_verificacion }} · {{ $lugar->fecha_importacion ? 'Importado: '.$lugar->fecha_importacion->format('d/m/Y H:i') : 'Carga inicial desde fuente oficial' }}</p>
                    </div>
                    <a href="{{ $lugar->fuente_url }}" target="_blank" rel="noopener noreferrer" class="tc-btn-outline">Comprobar fuente del lugar ↗</a>
                </div>
                @if($lugar->estado_verificacion !== 'aprobado' || ! $lugar->fecha_verificacion || $lugar->fecha_verificacion->lt(now()->subDays(config('tourism.verification_days'))))
                    <form method="POST" action="{{ route('operador.importaciones.aprobar', $lugar->id) }}" class="mt-4 flex flex-wrap gap-3 items-center">
                        @csrf
                        <label class="text-sm"><input type="checkbox" name="confirmado" value="1" required> Revisé el nombre, ubicación y fuente.</label>
                        <button class="tc-btn-primary">Aprobar lugar</button>
                    </form>
                @else
                    <a href="{{ route('operador.actividades.create', ['atractivo_id' => $lugar->id, 'destino_id' => $lugar->destino_id]) }}" class="tc-btn-primary mt-4">Crear actividad para este lugar</a>
                @endif
            </article>
        @empty
            <p class="bg-white border rounded-xl p-8 text-gray-600">Todavía no hay lugares importados. Selecciona un destino y pulsa Importar.</p>
        @endforelse
    </div>
    <div class="mt-6">{{ $lugares->links() }}</div>
    <p class="text-xs text-gray-500 mt-6">Datos © <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener noreferrer">OpenStreetMap contributors · ODbL</a>.</p>
</div>
@endsection
