@extends('layouts.app')

@section('title', 'Preferencias')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Preferencias de Turistas</h1>
            <p class="text-sm text-gray-500 mt-1">Gestiona los presupuestos, días y gustos turísticos configurados.</p>
        </div>

        <a href="{{ route('preferencias.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#00626A] text-white text-sm font-medium rounded-xl hover:bg-[#004e55] shadow-sm transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nueva Preferencia
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        @if ($preferencias->isEmpty())
            <div class="px-6 py-16 text-center">
                <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-gray-50 text-gray-400">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                    </svg>
                </div>
                <h2 class="text-base font-semibold text-gray-900">No hay preferencias registradas</h2>
                <p class="mt-1 text-sm text-gray-500 max-w-sm mx-auto">Aún ningún turista ha configurado sus parámetros de viaje.</p>
                <div class="mt-6">
                    <a href="{{ route('preferencias.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#00626A] text-white text-sm font-medium rounded-xl hover:bg-[#004e55] shadow-sm transition-colors">
                        Registrar Primera Preferencia
                    </a>
                </div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50/75">
                        <tr>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Turista</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Presupuesto Máx.</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Duración</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Horario</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Categorías</th>
                            <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach ($preferencias as $preferencia)
                            <tr class="hover:bg-gray-50/60 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-xs font-bold text-gray-400">#{{ $preferencia->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">
                                        {{ $preferencia->turista->nombre ?? 'Sin turista' }} {{ $preferencia->turista->apellido ?? '' }}
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        {{ $preferencia->turista->email ?? '' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    S/ {{ number_format($preferencia->presupuesto_max, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $preferencia->dias_disponibles }} {{ $preferencia->dias_disponibles == 1 ? 'día' : 'días' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                    @if($preferencia->hora_inicio_preferida && $preferencia->hora_fin_preferida)
                                        {{ substr($preferencia->hora_inicio_preferida, 0, 5) }} - {{ substr($preferencia->hora_fin_preferida, 0, 5) }}
                                    @else
                                        <span class="text-gray-400">Flexible</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1.5 max-w-xs">
                                        @forelse($preferencia->categorias as $cat)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-[#00626A]/10 text-[#00626A]">
                                                {{ $cat->nombre }}
                                            </span>
                                        @empty
                                            <span class="text-xs text-gray-400">Ninguna</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('preferencias.show', $preferencia->id) }}" class="p-1.5 rounded-lg text-gray-500 hover:text-[#00626A] hover:bg-gray-100 transition-colors" title="Ver detalle">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>
                                        <a href="{{ route('preferencias.edit', $preferencia->id) }}" class="p-1.5 rounded-lg text-gray-500 hover:text-[#00626A] hover:bg-gray-100 transition-colors" title="Editar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        </a>
                                        <form action="{{ route('preferencias.destroy', $preferencia->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar esta preferencia?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors" title="Eliminar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
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