@extends('layouts.app')

@section('title', 'Nueva Preferencia')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <p class="text-xs uppercase tracking-wider font-semibold text-neutral-400">Preferencias de Viaje</p>
            <h1 class="text-2xl font-bold text-gray-900 mt-1">Registrar Preferencias</h1>
        </div>
        <a href="{{ auth()->user()->isTurista() ? route('preferencias.turista', auth()->id()) : route('preferencias.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 border border-gray-200 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors shadow-sm">
            Volver
        </a>
    </div>

    <form action="{{ route('preferencias.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- Turista --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-semibold text-gray-900 mb-1">Información del Turista</h2>
            <p class="text-xs text-gray-500 mb-4">Indica a qué usuario turista corresponden estas preferencias.</p>

            @if(auth()->user()->isTurista())
                <input type="hidden" name="turista_id" value="{{ auth()->id() }}">
                <div class="flex items-center gap-3 p-3.5 bg-gray-50 rounded-xl border border-gray-200/60">
                    <div class="w-10 h-10 rounded-full bg-[#00626A]/10 text-[#00626A] flex items-center justify-center font-bold text-sm">
                        {{ substr(auth()->user()->nombre, 0, 1) }}{{ substr(auth()->user()->apellido, 0, 1) }}
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->nombre }} {{ auth()->user()->apellido }}</p>
                        <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                    </div>
                </div>
            @else
                <div>
                    <label for="turista_id" class="block text-xs font-bold text-gray-700 mb-1.5">Turista Asignado *</label>
                    <select name="turista_id" id="turista_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 bg-white focus:ring-2 focus:ring-[#00626A]/20 focus:border-[#00626A] text-sm">
                        <option value="">-- Selecciona un turista --</option>
                        @foreach ($turistas as $turista)
                            <option value="{{ $turista->id }}" {{ old('turista_id') == $turista->id ? 'selected' : '' }}>
                                {{ $turista->nombre }} {{ $turista->apellido }} ({{ $turista->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('turista_id')
                        <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            @endif
        </div>

        {{-- Presupuesto y Tiempo --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-semibold text-gray-900 mb-1">Presupuesto y Duración</h2>
            <p class="text-xs text-gray-500 mb-4">Parámetros clave para el itinerario turístico.</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="presupuesto_max" class="block text-xs font-bold text-gray-700 mb-1.5">Presupuesto Máximo (S/) *</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-sm font-medium text-gray-400">S/</span>
                        <input type="number" step="0.01" min="0" name="presupuesto_max" id="presupuesto_max" value="{{ old('presupuesto_max') }}" placeholder="Ej. 1200.00" required class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-[#00626A]/20 focus:border-[#00626A] text-sm">
                    </div>
                    @error('presupuesto_max')
                        <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="dias_disponibles" class="block text-xs font-bold text-gray-700 mb-1.5">Días Disponibles *</label>
                    <input type="number" min="1" name="dias_disponibles" id="dias_disponibles" value="{{ old('dias_disponibles', 3) }}" placeholder="Ej. 3" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-[#00626A]/20 focus:border-[#00626A] text-sm">
                    @error('dias_disponibles')
                        <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Horarios Preferidos --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-semibold text-gray-900 mb-1">Horario Preferido del Día</h2>
            <p class="text-xs text-gray-500 mb-4">Rango habitual en que prefiere realizar actividades turísticas.</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="hora_inicio_preferida" class="block text-xs font-bold text-gray-700 mb-1.5">Hora de Inicio Habitual</label>
                    <input type="time" name="hora_inicio_preferida" id="hora_inicio_preferida" value="{{ old('hora_inicio_preferida', '08:00') }}" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-[#00626A]/20 focus:border-[#00626A] text-sm">
                    @error('hora_inicio_preferida')
                        <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="hora_fin_preferida" class="block text-xs font-bold text-gray-700 mb-1.5">Hora de Fin Habitual</label>
                    <input type="time" name="hora_fin_preferida" id="hora_fin_preferida" value="{{ old('hora_fin_preferida', '20:00') }}" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-[#00626A]/20 focus:border-[#00626A] text-sm">
                    @error('hora_fin_preferida')
                        <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Categorías de Interés --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-semibold text-gray-900 mb-1">Categorías de Interés</h2>
            <p class="text-xs text-gray-500 mb-4">Selecciona los tipos de actividades y destinos favoritos.</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @forelse ($categorias as $categoria)
                    <label class="flex items-start gap-3 p-3 rounded-xl border border-gray-200 hover:border-[#00626A] hover:bg-[#00626A]/5 cursor-pointer transition-colors">
                        <input type="checkbox" name="categorias[]" value="{{ $categoria->id }}" {{ in_array($categoria->id, old('categorias', [])) ? 'checked' : '' }} class="mt-0.5 rounded border-gray-300 text-[#00626A] focus:ring-[#00626A]">
                        <div>
                            <span class="text-sm font-semibold text-gray-900 block">{{ $categoria->nombre }}</span>
                            @if($categoria->descripcion)
                                <span class="text-xs text-gray-500 line-clamp-1">{{ $categoria->descripcion }}</span>
                            @endif
                        </div>
                    </label>
                @empty
                    <p class="text-xs text-gray-400 col-span-3">No hay categorías de interés registradas.</p>
                @endforelse
            </div>
            @error('categorias')
                <p class="mt-2 text-xs text-red-600 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ auth()->user()->isTurista() ? route('preferencias.turista', auth()->id()) : route('preferencias.index') }}" class="px-5 py-2.5 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-2.5 bg-[#00626A] text-white rounded-xl text-sm font-semibold hover:bg-[#004e55] transition-colors shadow-sm">
                Guardar Preferencias
            </button>
        </div>
    </form>
</div>
@endsection