@extends('layouts.app', ['title' => 'Nueva actividad'])

@section('content')
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
        <div class="mb-7">
            <a href="{{ route('operador.actividades.index') }}" class="text-sm font-semibold hover:underline" style="color: var(--color-primary-600);">← Mis actividades</a>
            <h1 class="mt-3 text-3xl font-extrabold tracking-tight" style="color: var(--color-primary-700);">Nueva actividad</h1>
            <p class="mt-2 text-sm text-gray-500">Registra una experiencia disponible en uno de tus destinos activos.</p>
        </div>

        <form method="POST" action="{{ route('operador.actividades.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @include('actividades::operador._form', ['actividad' => null, 'destinos' => $destinos, 'submitLabel' => 'Crear actividad'])
        </form>
    </main>
@endsection
