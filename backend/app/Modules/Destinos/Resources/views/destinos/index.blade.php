@extends('layouts.app')

@section('content')
<div class="tc-workspace max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Encabezado -->
    <div class="tc-page-heading flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('admin_destinations.management') }}</h1>
            <p class="mt-1 text-sm text-gray-500">{{ __('admin_destinations.management_intro') }}</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('admin.destinos.create') }}" class="tc-btn-primary shrink-0">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ __('admin_destinations.new_destination') }}
            </a>
        </div>
    </div>

    <!-- Tabla -->
    <div class="tc-card border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="tc-table min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('admin_destinations.destination') }}</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('admin_destinations.location') }}</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('admin_destinations.assigned_operator') }}</th>
                        <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('admin_destinations.status') }}</th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('admin_destinations.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($destinos as $destino)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $destino->nombre }}</div>
                                <div class="text-sm text-gray-500 truncate max-w-[200px]">{{ $destino->descripcion ?? __('admin_destinations.no_description') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $destino->ciudad ?? 'N/A' }}, {{ $destino->region ?? 'N/A' }}</div>
                                <div class="text-sm text-gray-500">{{ $destino->pais }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($destino->operador)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $destino->operador->nombre }} {{ $destino->operador->apellido }}
                                    </span>
                                @else
                                    <span class="text-sm text-gray-400 italic">{{ __('admin_destinations.unassigned') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $destino->estado === 'activo' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $destino->estado === 'activo' ? 'bg-green-500' : 'bg-gray-500' }}"></span>
                                    {{ $destino->estado === 'activo' ? __('admin_destinations.active') : __('admin_destinations.inactive') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('admin.destinos.edit', $destino) }}" class="text-gray-400 hover:text-[#16734f] transition-colors" title="{{ __('admin_destinations.edit') }}" aria-label="{{ __('admin_destinations.edit') }} {{ $destino->nombre }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form action="{{ route('admin.destinos.toggle-estado', $destino) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-gray-400 hover:text-orange-500 transition-colors" title="{{ $destino->estado === 'activo' ? __('admin_destinations.disable') : __('admin_destinations.enable') }}" aria-label="{{ $destino->estado === 'activo' ? __('admin_destinations.disable') : __('admin_destinations.enable') }} {{ $destino->nombre }}">
                                            @if($destino->estado === 'activo')
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                            @else
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            @endif
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/></svg>
                                {{ __('admin_destinations.no_destinations') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        @if($destinos->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                {{ $destinos->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
