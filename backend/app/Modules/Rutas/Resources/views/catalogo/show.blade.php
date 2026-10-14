@extends('layouts.app', ['title' => $ruta->nombre])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <a href="{{ route('catalogo.rutas.index') }}" class="text-sm font-semibold text-primary-700 hover:underline">← Volver a rutas</a>
    <div class="mt-5 grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6 min-w-0">
            <div>
                <div class="h-56 sm:h-80 rounded-xl overflow-hidden">@include('rutas::partials.portada', ['ruta' => $ruta])</div>
                @include('rutas::partials.credito', ['ruta' => $ruta])
            </div>
            <div>
                <p class="font-semibold text-primary-600">{{ $ruta->destino->nombre }}</p>
                <h1 class="mt-2 text-3xl sm:text-4xl font-extrabold text-primary-700 break-words">{{ $ruta->nombre }}</h1>
            </div>
            <section class="tc-card p-5 sm:p-6">
                <h2 class="text-lg font-bold text-primary-700">Sobre el recorrido</h2>
                <p class="mt-3 text-gray-600 leading-relaxed whitespace-pre-line break-words">{{ $ruta->descripcion ?: 'El operador todavía no ha agregado una descripción.' }}</p>
            </section>
            <section class="tc-card p-5 sm:p-6">
                <h2 class="text-lg font-bold text-primary-700">Paradas en orden</h2>
                <ol class="mt-5 space-y-4">
                    @foreach($ruta->paradas as $parada)
                        <li class="flex gap-3 sm:gap-4">
                            <span class="w-8 h-8 shrink-0 rounded-full bg-primary-600 text-white flex items-center justify-center font-bold">{{ $loop->iteration }}</span>
                            <div class="min-w-0 flex-1 border-b border-gray-100 pb-4">
                                <a href="{{ route('catalogo.atractivos.show', $parada->atractivo) }}" class="text-lg font-bold text-primary-700 hover:underline break-words">{{ $parada->atractivo->nombre }} ↗</a>
                                <p class="mt-1 text-sm text-gray-500 break-words">{{ Illuminate\Support\Str::limit($parada->atractivo->descripcion, 180) }}</p>
                                <p class="mt-2 text-sm text-gray-600">Entrada referencial: S/ {{ $parada->atractivo->costo_entrada }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </section>
        </div>
        <aside class="space-y-5">
            <section class="tc-card p-5 sm:p-6">
                <h2 class="text-sm uppercase font-bold tracking-wide text-primary-700">Datos referenciales</h2>
                <dl class="mt-4 space-y-4">
                    <div><dt class="text-sm text-gray-500">Distancia registrada</dt><dd class="mt-1 text-xl font-bold">{{ $ruta->distancia_km !== null ? $ruta->distancia_km.' km' : 'No indicada' }}</dd></div>
                    <div><dt class="text-sm text-gray-500">Duración registrada</dt><dd class="mt-1 text-xl font-bold">{{ $ruta->duracion_estimada_min !== null ? $ruta->duracion_estimada_min.' min' : 'No indicada' }}</dd></div>
                    <div><dt class="text-sm text-gray-500">Paradas</dt><dd class="mt-1 text-xl font-bold">{{ $ruta->paradas->count() }}</dd></div>
                </dl>
                <p class="mt-5 text-xs text-gray-500 leading-relaxed">Distancia y duración introducidas manualmente por el operador. No se calcula la navegación ni se verifica el tráfico.</p>
            </section>
            <section class="tc-card p-5 sm:p-6">
                <h2 class="text-sm uppercase font-bold tracking-wide text-primary-700">Suma referencial de entradas</h2>
                <p class="mt-3 text-3xl font-extrabold text-primary-700">S/ {{ $ruta->costo_entradas_referencial }}</p>
                <p class="mt-3 text-sm text-gray-500 leading-relaxed">Suma de los importes registrados para los atractivos. No incluye transporte, guía, comida ni otras actividades. No es una cotización ni garantiza disponibilidad; verifica las tarifas vigentes y los boletos combinados con cada proveedor.</p>
            </section>
        </aside>
    </div>
</div>
@endsection
