@extends('actividades::layouts.app')
@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
    <div class="bg-white border rounded-2xl p-6 sm:p-8">
        <h1 class="text-2xl font-bold mb-3">Editar ruta</h1>
        <p class="text-sm text-gray-600 mb-6">Registra datos reales y sus fuentes. Guardar cambios devuelve el registro a revisión.</p>
        <form method="POST" action="{{ route('operador.rutas.update', $ruta->id) }}" class="space-y-6">
            @csrf
            @method('PUT')
            @include('rutas::operador.rutas.form')
            <div class="flex justify-end gap-3 border-t pt-5">
                <a href="{{ route('operador.rutas.index') }}" class="tc-btn-outline">Cancelar</a>
                <button class="tc-btn-primary">Guardar para revisión</button>
            </div>
        </form>
    </div>
</div>
@endsection
