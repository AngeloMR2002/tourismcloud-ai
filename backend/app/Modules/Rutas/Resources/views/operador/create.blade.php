@extends('layouts.app', ['title' => 'Nueva ruta'])

@section('content')
<div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <a href="{{ route('operador.rutas.index') }}" class="text-sm font-semibold text-primary-700 hover:text-primary-900">← Volver a mis rutas</a>
            <h1 class="mt-2 text-2xl font-extrabold tracking-tight text-slate-900">Crear ruta turística</h1>
            <p class="mt-1 text-sm text-slate-500">Organiza una secuencia de atractivos para orientar futuros itinerarios.</p>
        </div>
    </div>

    @include('rutas::operador._form')
</div>
@endsection
