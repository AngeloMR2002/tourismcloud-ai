@extends('actividades::layouts.app')
@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-2">Revisión del catálogo</h1>
    <p class="text-sm text-gray-600 mb-6">Comprueba la fuente de cada registro y, cuando corresponda, la tarifa y los derechos de la fotografía. Las ediciones vuelven a revisión.</p>
    @if($errors->any())
        <div class="tc-alert-error mb-4" role="alert">{{ $errors->first() }}</div>
    @endif
    @foreach(['actividades' => $actividades, 'rutas' => $rutas] as $tipo => $registros)
        <h2 class="text-xl font-bold mt-8 mb-4">{{ ucfirst($tipo) }}</h2>
        @forelse($registros as $registro)
            <article class="bg-white rounded-xl border p-5 mb-4">
                <h3 class="font-bold">{{ $registro->nombre }}</h3>
                <p class="text-sm text-gray-600 mt-1">{{ $registro->destino?->nombre }} · {{ $registro->estado_verificacion }}</p>
                <p class="text-sm mt-3">{{ $registro->descripcion }}</p>
                <div class="flex flex-wrap gap-3 mt-4">
                    <a href="{{ route('operador.'.$tipo.'.edit', $registro->id) }}" class="tc-btn-outline">Revisar datos y corregir</a>
                    @if($registro->fuente_url)
                        <a href="{{ $registro->fuente_url }}" target="_blank" rel="noopener noreferrer" class="tc-btn-outline">Fuente del registro ↗</a>
                    @endif
                    @if($registro->precio_fuente_url)
                        <a href="{{ $registro->precio_fuente_url }}" target="_blank" rel="noopener noreferrer" class="tc-btn-outline">Fuente de la tarifa ↗</a>
                    @endif
                    @if($registro->imagen_fuente_url)
                        <a href="{{ $registro->imagen_fuente_url }}" target="_blank" rel="noopener noreferrer" class="tc-btn-outline">Origen de la foto ↗</a>
                    @endif
                </div>
                <form method="POST" action="{{ route('operador.revision.aprobar', ['tipo' => $tipo, 'id' => $registro->id]) }}" class="mt-4 flex flex-wrap gap-3 items-center">
                    @csrf
                    <label class="text-sm"><input type="checkbox" name="confirmado" value="1" required> He comprobado las fuentes y los datos publicados.</label>
                    <button class="tc-btn-primary">Aprobar publicación</button>
                </form>
            </article>
        @empty
            <p class="text-gray-600">No hay registros pendientes.</p>
        @endforelse
        {{ $registros->links() }}
    @endforeach
</div>
@endsection
