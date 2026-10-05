@extends('layouts.app')

@section('title', 'Preferencias')

@section('header')

<div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900">
            Preferencias de turistas
        </h1>

        <p class="mt-1 text-sm text-slate-500">
            Gestiona las preferencias registradas para los turistas.
        </p>
    </div>

    <a
        href="{{ route('preferencias.create') }}"
        class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
    >
        <span class="mr-2 text-lg">+</span>
        Nueva preferencia
    </a>

</div>

@endsection

@section('content')

<div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

    @if ($preferencias->isEmpty())

        <div class="px-6 py-12 text-center">

            <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-xl text-slate-500">
                —
            </div>

            <h2 class="text-lg font-semibold text-slate-900">
                No existen preferencias registradas
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Registra la primera preferencia para comenzar.
            </p>

            <div class="mt-6">

                <a
                    href="{{ route('preferencias.create') }}"
                    class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
                >
                    Registrar preferencia
                </a>

            </div>

        </div>

    @else

        <div class="overflow-x-auto">

            <table class="min-w-full divide-y divide-slate-200">

                <thead class="bg-slate-50">

                    <tr>

                        <th
                            scope="col"
                            class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500"
                        >
                            ID
                        </th>

                        <th
                            scope="col"
                            class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500"
                        >
                            Turista
                        </th>

                        <th
                            scope="col"
                            class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500"
                        >
                            Presupuesto
                        </th>

                        <th
                            scope="col"
                            class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500"
                        >
                            Días
                        </th>

                        <th
                            scope="col"
                            class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500"
                        >
                            Ritmo
                        </th>

                        <th
                            scope="col"
                            class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500"
                        >
                            Estado
                        </th>

                        <th
                            scope="col"
                            class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500"
                        >
                            Acciones
                        </th>

                    </tr>

                </thead>

                <tbody class="divide-y divide-slate-100 bg-white">

                    @foreach ($preferencias as $preferencia)

                        <tr class="transition hover:bg-slate-50">

                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-slate-900">
                                #{{ $preferencia->id }}
                            </td>

                            <td class="px-6 py-4">

                                <div class="text-sm font-medium text-slate-900">
                                    {{ $preferencia->turista->nombre ?? 'Sin turista' }}
                                </div>

                                <div class="text-xs text-slate-500">
                                    {{ $preferencia->turista->email ?? 'Sin email' }}
                                </div>

                            </td>

                            <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-700">

                                <span class="font-medium">
                                    S/ {{ number_format($preferencia->presupuesto_min ?? 0, 2) }}
                                </span>

                                <span class="text-slate-400">
                                    -
                                </span>

                                <span class="font-medium">
                                    S/ {{ number_format($preferencia->presupuesto_max, 2) }}
                                </span>

                            </td>

                            <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-700">

                                {{ $preferencia->dias_disponibles }}

                                {{ $preferencia->dias_disponibles == 1 ? 'día' : 'días' }}

                            </td>

                            <td class="whitespace-nowrap px-6 py-4">

                                @php
                                    $ritmoClasses = [
                                        'relajado' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
                                        'moderado' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
                                        'intenso' => 'bg-orange-50 text-orange-700 ring-orange-600/20',
                                    ];
                                @endphp

                                <span
                                    class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset {{ $ritmoClasses[$preferencia->ritmo] ?? 'bg-slate-50 text-slate-600 ring-slate-500/20' }}"
                                >
                                    {{ ucfirst($preferencia->ritmo) }}
                                </span>

                            </td>

                            <td class="whitespace-nowrap px-6 py-4">

                                @if ($preferencia->vigente)

                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                                        Vigente
                                    </span>

                                @else

                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600 ring-1 ring-inset ring-slate-500/20">
                                        No vigente
                                    </span>

                                @endif

                            </td>

                            <td class="whitespace-nowrap px-6 py-4 text-right">

                                <div class="flex items-center justify-end gap-2">

                                    <a
                                        href="{{ route('preferencias.show', $preferencia->id) }}"
                                        class="rounded-lg px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-slate-900"
                                    >
                                        Ver
                                    </a>

                                    <a
                                        href="{{ route('preferencias.edit', $preferencia->id) }}"
                                        class="rounded-lg px-3 py-2 text-xs font-semibold text-blue-600 transition hover:bg-blue-50 hover:text-blue-700"
                                    >
                                        Editar
                                    </a>

                                    <form
                                        action="{{ route('preferencias.destroy', $preferencia->id) }}"
                                        method="POST"
                                    >

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="rounded-lg px-3 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-50 hover:text-red-700"
                                        >
                                            Eliminar
                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    @endif

</div>

@endsection
