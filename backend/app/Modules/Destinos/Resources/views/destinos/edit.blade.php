@extends('layouts.app')

@section('content')
<div class="tc-workspace max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <div class="tc-page-heading">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.destinos.index') }}" class="p-2 bg-white border border-gray-200 rounded-lg text-gray-500 hover:text-[#16734f] hover:bg-gray-50 transition-colors" aria-label="{{ __('admin_destinations.back_to_destinations') }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ __('admin_destinations.edit_destination') }}</h1>
                <p class="text-sm text-gray-500">{{ __('admin_destinations.update_intro', ['name' => $destino->nombre]) }}</p>
            </div>
        </div>
    </div>

    <div class="tc-card border border-gray-100 p-6 sm:p-8">
        <form action="{{ route('admin.destinos.update', $destino) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nombre -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin_destinations.destination_name') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="nombre" value="{{ old('nombre', $destino->nombre) }}" required
                        class="tc-input">
                    @error('nombre') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- País -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin_destinations.country') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="pais" value="{{ old('pais', $destino->pais) }}" required
                        class="tc-input">
                    @error('pais') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Región / Departamento -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin_destinations.region') }}</label>
                    <input type="text" name="region" value="{{ old('region', $destino->region) }}"
                        class="tc-input">
                </div>

                <!-- Ciudad -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin_destinations.city') }}</label>
                    <input type="text" name="ciudad" value="{{ old('ciudad', $destino->ciudad) }}"
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#16734f]/20 focus:border-[#16734f] transition-colors">
                </div>

                <!-- Operador Turístico -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin_destinations.tour_operator_assigned') }}</label>
                    <select name="operador_id" class="tc-select">
                        <option value="">-- {{ __('admin_destinations.unassigned') }} --</option>
                        @foreach($operadores as $ope)
                            <option value="{{ $ope->id }}" {{ old('operador_id', $destino->operador_id) == $ope->id ? 'selected' : '' }}>
                                {{ $ope->nombre }} {{ $ope->apellido }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Coordenadas -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin_destinations.latitude') }}</label>
                    <input type="number" step="any" name="latitud" value="{{ old('latitud', $destino->latitud) }}"
                        class="tc-input">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin_destinations.longitude') }}</label>
                    <input type="number" step="any" name="longitud" value="{{ old('longitud', $destino->longitud) }}"
                        class="tc-input">
                </div>

                <!-- Estado -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin_destinations.status') }}</label>
                    <select name="estado" class="tc-select md:w-1/2">
                        <option value="activo" {{ old('estado', $destino->estado) == 'activo' ? 'selected' : '' }}>{{ __('admin_destinations.active_visible') }}</option>
                        <option value="inactivo" {{ old('estado', $destino->estado) == 'inactivo' ? 'selected' : '' }}>{{ __('admin_destinations.inactive_hidden') }}</option>
                    </select>
                </div>

                <!-- Descripción -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin_destinations.description') }}</label>
                    <textarea name="descripcion" rows="4" 
                        class="tc-input">{{ old('descripcion', $destino->descripcion) }}</textarea>
                </div>
            </div>

            <div class="pt-4 border-t border-gray-100 flex justify-end gap-3">
                <a href="{{ route('admin.destinos.index') }}" class="tc-btn-outline">
                    {{ __('admin_destinations.cancel') }}
                </a>
                <button type="submit" class="tc-btn-primary">
                    {{ __('admin_destinations.update_destination') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
