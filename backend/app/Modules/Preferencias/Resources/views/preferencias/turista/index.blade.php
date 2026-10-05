@extends('layouts.app')

@section('title', 'Mis preferencias')

@section('header')
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-gray-900">
            Mis preferencias
        </h1>

        <p class="mt-1 text-sm text-gray-500">
            Consulta y administra tus preferencias de viaje.
        </p>
    </div>
@endsection

@section('content')

    @if($preferencia)

        <div class="space-y-6">

            {{-- Encabezado de la preferencia --}}
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                <div>
                    <h2 class="text-lg font-semibold text-gray-900">
                        Preferencias de viaje
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Esta información será utilizada para generar recomendaciones personalizadas.
                    </p>
                </div>

                <a
                    href="{{ route('preferencias.edit', $preferencia->id) }}"
                    class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-gray-800"
                >
                    Editar preferencias
                </a>

            </div>


            {{-- Información general --}}
            <div class="grid gap-6 lg:grid-cols-2">

                {{-- Presupuesto --}}
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">

                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900">
                                Presupuesto
                            </h3>

                            <p class="mt-1 text-xs text-gray-500">
                                Rango disponible para el viaje
                            </p>
                        </div>

                        <div class="rounded-lg bg-gray-100 px-3 py-2 text-sm font-semibold text-gray-700">
                            S/
                        </div>
                    </div>

                    <div class="mt-5">
                        <p class="text-2xl font-bold text-gray-900">
                            S/ {{ number_format($preferencia->presupuesto_max, 2) }}
                        </p>

                        @if($preferencia->presupuesto_min !== null)
                            <p class="mt-1 text-sm text-gray-500">
                                Desde S/ {{ number_format($preferencia->presupuesto_min, 2) }}
                            </p>
                        @else
                            <p class="mt-1 text-sm text-gray-500">
                                Sin presupuesto mínimo establecido
                            </p>
                        @endif
                    </div>

                </div>


                {{-- Duración --}}
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">

                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900">
                                Duración
                            </h3>

                            <p class="mt-1 text-xs text-gray-500">
                                Tiempo disponible para viajar
                            </p>
                        </div>

                        <div class="rounded-lg bg-gray-100 px-3 py-2 text-sm font-semibold text-gray-700">
                            Días
                        </div>
                    </div>

                    <div class="mt-5">
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $preferencia->dias_disponibles }}
                            {{ $preferencia->dias_disponibles === 1 ? 'día' : 'días' }}
                        </p>
                    </div>

                </div>

            </div>


            {{-- Ritmo y horario --}}
            <div class="grid gap-6 lg:grid-cols-2">

                {{-- Ritmo --}}
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">

                    <h3 class="text-sm font-semibold text-gray-900">
                        Ritmo del viaje
                    </h3>

                    <p class="mt-1 text-xs text-gray-500">
                        Intensidad preferida durante tus actividades
                    </p>

                    <div class="mt-5">
                        @php
                            $ritmoLabel = match($preferencia->ritmo) {
                                'relajado' => 'Relajado',
                                'moderado' => 'Moderado',
                                'intenso' => 'Intenso',
                                default => ucfirst($preferencia->ritmo),
                            };
                        @endphp

                        <span class="inline-flex rounded-full px-3 py-1.5 text-sm font-medium
                            {{ match($preferencia->ritmo) {
                                'relajado' => 'bg-green-100 text-green-700',
                                'moderado' => 'bg-yellow-100 text-yellow-700',
                                'intenso' => 'bg-red-100 text-red-700',
                                default => 'bg-gray-100 text-gray-700',
                            } }}"
                        >
                            {{ $ritmoLabel }}
                        </span>
                    </div>

                </div>


                {{-- Horario --}}
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">

                    <h3 class="text-sm font-semibold text-gray-900">
                        Horario diario
                    </h3>

                    <p class="mt-1 text-xs text-gray-500">
                        Horario preferido para iniciar y finalizar el día
                    </p>

                    <div class="mt-5 flex items-center gap-3">

                        <div>
                            <p class="text-xs text-gray-500">
                                Inicio
                            </p>

                            <p class="text-lg font-semibold text-gray-900">
                                {{ $preferencia->hora_inicio_dia
                                    ? substr($preferencia->hora_inicio_dia, 0, 5)
                                    : 'No definido' }}
                            </p>
                        </div>

                        <span class="text-gray-400">
                            →
                        </span>

                        <div>
                            <p class="text-xs text-gray-500">
                                Fin
                            </p>

                            <p class="text-lg font-semibold text-gray-900">
                                {{ $preferencia->hora_fin_dia
                                    ? substr($preferencia->hora_fin_dia, 0, 5)
                                    : 'No definido' }}
                            </p>
                        </div>

                    </div>

                </div>

            </div>


            {{-- Categorías --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">

                <div>
                    <h3 class="text-sm font-semibold text-gray-900">
                        Categorías de interés
                    </h3>

                    <p class="mt-1 text-xs text-gray-500">
                        Tipos de experiencias que deseas encontrar.
                    </p>
                </div>

                @if($preferencia->categorias->isNotEmpty())

                    <div class="mt-5 flex flex-wrap gap-2">

                        @foreach($preferencia->categorias as $categoria)

                            <span class="rounded-full bg-gray-100 px-3 py-1.5 text-sm font-medium text-gray-700">
                                {{ $categoria->nombre }}
                            </span>

                        @endforeach

                    </div>

                @else

                    <p class="mt-5 text-sm text-gray-500">
                        No has seleccionado categorías de interés.
                    </p>

                @endif

            </div>


            {{-- Estado --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">

                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">
                            Estado de preferencias
                        </h3>

                        <p class="mt-1 text-xs text-gray-500">
                            Indica si estas preferencias se encuentran actualmente activas.
                        </p>
                    </div>

                    @if($preferencia->vigente)

                        <span class="inline-flex w-fit rounded-full bg-green-100 px-3 py-1.5 text-sm font-medium text-green-700">
                            Vigente
                        </span>

                    @else

                        <span class="inline-flex w-fit rounded-full bg-gray-100 px-3 py-1.5 text-sm font-medium text-gray-600">
                            No vigente
                        </span>

                    @endif

                </div>

            </div>


            {{-- Acciones --}}
            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">

                <a
                    href="{{ route('preferencias.index') }}"
                    class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                >
                    Volver
                </a>

                <a
                    href="{{ route('preferencias.edit', $preferencia->id) }}"
                    class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-gray-800"
                >
                    Editar preferencias
                </a>

            </div>

        </div>

    @else

        {{-- Estado sin preferencias --}}
        <div class="rounded-xl border border-dashed border-gray-300 bg-white px-6 py-12 text-center shadow-sm">

            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-100">
                <span class="text-lg text-gray-500">
                    +
                </span>
            </div>

            <h2 class="mt-4 text-lg font-semibold text-gray-900">
                Aún no tienes preferencias registradas
            </h2>

            <p class="mx-auto mt-2 max-w-md text-sm text-gray-500">
                Registra tus preferencias de viaje para que la plataforma pueda
                ofrecerte recomendaciones más personalizadas.
            </p>

            <div class="mt-6">
                <a
                    href="{{ route('preferencias.create') }}"
                    class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-gray-800"
                >
                    Registrar mis preferencias
                </a>
            </div>

        </div>

    @endif

@endsection