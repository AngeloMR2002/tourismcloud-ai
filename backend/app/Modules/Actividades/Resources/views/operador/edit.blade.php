@extends('layouts.app', ['title' => 'Editar actividad'])

@section('content')
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
        <div class="mb-7">
            <a href="{{ route('operador.actividades.index') }}" class="text-sm font-semibold hover:underline" style="color: var(--color-primary-600);">← Mis actividades</a>
            <h1 class="mt-3 text-3xl font-extrabold tracking-tight" style="color: var(--color-primary-700);">Editar actividad</h1>
            <p class="mt-2 text-sm text-gray-500">Actualiza la información que verán los turistas.</p>
        </div>

        <form method="POST" action="{{ route('operador.actividades.update', $actividad) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            @include('actividades::operador._form', ['actividad' => $actividad, 'destinos' => $destinos, 'submitLabel' => 'Guardar cambios'])
        </form>
    </main>
@endsection
