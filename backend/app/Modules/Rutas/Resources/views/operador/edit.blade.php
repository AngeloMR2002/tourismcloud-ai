@extends('layouts.app', ['title' => 'Editar ruta'])

@section('content')
<div class="tc-workspace mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="tc-page-heading">
        <div>
            <a href="{{ route('operador.rutas.index') }}" class="text-sm font-semibold text-primary-700 hover:text-primary-900">← Volver a mis rutas</a>
            <h1>Editar ruta</h1>
            <p>Actualiza la información y el orden de las paradas de {{ $ruta->nombre }}.</p>
        </div>
    </div>

    @include('rutas::operador._form')
</div>
@endsection
