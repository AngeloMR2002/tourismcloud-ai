@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('admin.destinos.index') }}" class="p-2 bg-white border border-gray-200 rounded-lg text-gray-500 hover:text-[#00626A] hover:bg-gray-50 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Editar Destino</h1>
            <p class="text-sm text-gray-500">Actualiza la información de {{ $destino->nombre }}.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
        <form action="{{ route('admin.destinos.update', $destino) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nombre -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Destino <span class="text-red-500">*</span></label>
                    <input type="text" name="nombre" value="{{ old('nombre', $destino->nombre) }}" required
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#00626A]/20 focus:border-[#00626A] transition-colors">
                    @error('nombre') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- País -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">País <span class="text-red-500">*</span></label>
                    <input type="text" name="pais" value="{{ old('pais', $destino->pais) }}" required
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#00626A]/20 focus:border-[#00626A] transition-colors">
                    @error('pais') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Región / Departamento -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Región / Departamento</label>
                    <input type="text" name="region" value="{{ old('region', $destino->region) }}"
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#00626A]/20 focus:border-[#00626A] transition-colors">
                </div>

                <!-- Ciudad -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ciudad</label>
                    <input type="text" name="ciudad" value="{{ old('ciudad', $destino->ciudad) }}"
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#00626A]/20 focus:border-[#00626A] transition-colors">
                </div>

                <!-- Operador Turístico -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Operador Turístico Asignado</label>
                    <select name="operador_id" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#00626A]/20 focus:border-[#00626A] bg-white transition-colors">
                        <option value="">-- Sin asignar --</option>
                        @foreach($operadores as $ope)
                            <option value="{{ $ope->id }}" {{ old('operador_id', $destino->operador_id) == $ope->id ? 'selected' : '' }}>
                                {{ $ope->nombre }} {{ $ope->apellido }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Coordenadas -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Latitud</label>
                    <input type="number" step="any" name="latitud" value="{{ old('latitud', $destino->latitud) }}"
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#00626A]/20 focus:border-[#00626A] transition-colors">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Longitud</label>
                    <input type="number" step="any" name="longitud" value="{{ old('longitud', $destino->longitud) }}"
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#00626A]/20 focus:border-[#00626A] transition-colors">
                </div>

                <!-- Estado -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select name="estado" class="w-full md:w-1/2 px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#00626A]/20 focus:border-[#00626A] bg-white transition-colors">
                        <option value="activo" {{ old('estado', $destino->estado) == 'activo' ? 'selected' : '' }}>Activo (Visible)</option>
                        <option value="inactivo" {{ old('estado', $destino->estado) == 'inactivo' ? 'selected' : '' }}>Inactivo (Oculto)</option>
                    </select>
                </div>

                <!-- Descripción -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <textarea name="descripcion" rows="4" 
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#00626A]/20 focus:border-[#00626A] transition-colors">{{ old('descripcion', $destino->descripcion) }}</textarea>
                </div>
            </div>

            <div class="pt-4 border-t border-gray-100 flex justify-end gap-3">
                <a href="{{ route('admin.destinos.index') }}" class="px-5 py-2.5 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="px-5 py-2.5 bg-[#00626A] rounded-lg text-sm font-medium text-white hover:bg-[#004e55] shadow-sm transition-colors">
                    Actualizar Destino
                </button>
            </div>
        </form>
    </div>
</div>
@endsection