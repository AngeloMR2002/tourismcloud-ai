@extends('layouts.app')

@section('title', 'Preferencias')

@section('header')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <p class="text-sm text-gray-400 mb-0.5">Viajes personalizados</p>
        <h1 style="font-family: var(--font-display); font-size: 2rem; font-weight: 800; color: #1a2232; line-height: 1.15;">
            Preferencias de turistas
        </h1>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm text-slate-500">Gestiona las preferencias registradas para los turistas.</p>
        </div>

        <a href="{{ route('preferencias.create') }}" class="tc-btn-primary">
            <span class="text-lg">+</span>
            Nueva preferencia
        </a>
    </div>

    <div class="tc-card overflow-hidden">
        @if ($preferencias->isEmpty())
            <div class="px-6 py-12 text-center">
                <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-xl text-slate-500">
                    —
                </div>

                <h2 class="text-lg font-semibold text-slate-900">No existen preferencias registradas</h2>
                <p class="mt-1 text-sm text-slate-500">Registra la primera preferencia para comenzar.</p>

                <div class="mt-6">
                    <a href="{{ route('preferencias.create') }}" class="tc-btn-primary">
                        Registrar preferencia
                    </a>
                </div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="tc-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Turista</th>
                            <th>Presupuesto</th>
                            <th>Días</th>
                            <th>Ritmo</th>
                            <th>Estado</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($preferencias as $preferencia)
                            <tr>
                                <td class="font-semibold text-slate-900">#{{ $preferencia->id }}</td>
                                <td>
                                    <div class="text-sm font-medium text-slate-900">
                                        {{ $preferencia->turista->nombre ?? 'Sin turista' }}
                                    </div>
                                    <div class="text-xs text-slate-500">
                                        {{ $preferencia->turista->email ?? 'Sin email' }}
                                    </div>
                                </td>
                                <td class="text-sm text-slate-700">
                                    <span class="font-medium">S/ {{ number_format($preferencia->presupuesto_min ?? 0, 2) }}</span>
                                    <span class="text-slate-400"> — </span>
                                    <span class="font-medium">S/ {{ number_format($preferencia->presupuesto_max, 2) }}</span>
                                </td>
                                <td class="text-sm text-slate-700">
                                    {{ $preferencia->dias_disponibles }}
                                    {{ $preferencia->dias_disponibles == 1 ? 'día' : 'días' }}
                                </td>
                                <td>
                                    @php
                                        $ritmoClasses = [
                                            'relajado' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
                                            'moderado' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
                                            'intenso' => 'bg-orange-50 text-orange-700 ring-orange-600/20',
                                        ];
                                    @endphp
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset {{ $ritmoClasses[$preferencia->ritmo] ?? 'bg-slate-50 text-slate-600 ring-slate-500/20' }}">
                                        {{ ucfirst($preferencia->ritmo) }}
                                    </span>
                                </td>
                                <td>
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
                                <td class="text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('preferencias.show', $preferencia->id) }}" class="rounded-lg px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-slate-900">
                                            Ver
                                        </a>
                                        <a href="{{ route('preferencias.edit', $preferencia->id) }}" class="rounded-lg px-3 py-2 text-xs font-semibold text-primary-600 transition hover:bg-primary-50 hover:text-primary-700" style="color: var(--color-primary-600);">
                                            Editar
                                        </a>
                                        <form action="{{ route('preferencias.destroy', $preferencia->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-lg px-3 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-50 hover:text-red-700">
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
</div>
@endsection
