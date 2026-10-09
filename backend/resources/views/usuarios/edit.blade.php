@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('admin.usuarios.index') }}" class="p-2 bg-white border border-gray-200 rounded-lg text-gray-500 hover:text-[#00626A] hover:bg-gray-50 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Editar Usuario</h1>
            <p class="text-sm text-gray-500">Actualizando datos de {{ $usuario->nombre }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
        <form action="{{ route('admin.usuarios.update', $usuario->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nombre -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre <span class="text-red-500">*</span></label>
                    <input type="text" name="nombre" value="{{ old('nombre', $usuario->nombre) }}" required
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#00626A]/20 focus:border-[#00626A] transition-colors">
                    @error('nombre') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Apellido -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Apellido <span class="text-red-500">*</span></label>
                    <input type="text" name="apellido" value="{{ old('apellido', $usuario->apellido) }}" required
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#00626A]/20 focus:border-[#00626A] transition-colors">
                    @error('apellido') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $usuario->email) }}" required
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#00626A]/20 focus:border-[#00626A] transition-colors">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Contraseña (Opcional) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nueva Contraseña</label>
                    <input type="password" name="password" placeholder="Dejar en blanco para no cambiar" minlength="8"
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#00626A]/20 focus:border-[#00626A] transition-colors placeholder-gray-400">
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Teléfono -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                    <input type="text" name="telefono" value="{{ old('telefono', $usuario->telefono) }}"
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#00626A]/20 focus:border-[#00626A] transition-colors">
                </div>

                <!-- Rol -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rol del Sistema <span class="text-red-500">*</span></label>
                    <select name="rol" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#00626A]/20 focus:border-[#00626A] bg-white transition-colors">
                        <option value="turista" {{ old('rol', $usuario->rol) == 'turista' ? 'selected' : '' }}>Turista</option>
                        <option value="operador_turistico" {{ old('rol', $usuario->rol) == 'operador_turistico' ? 'selected' : '' }}>Operador Turístico</option>
                        <option value="proveedor" {{ old('rol', $usuario->rol) == 'proveedor' ? 'selected' : '' }}>Proveedor</option>
                        <option value="administrador" {{ in_array(old('rol', $usuario->rol), ['administrador', 'admin']) ? 'selected' : '' }}>Administrador</option>
                    </select>
                </div>

                <!-- Estado -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select name="estado" class="w-full md:w-1/2 px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#00626A]/20 focus:border-[#00626A] bg-white transition-colors">
                        <option value="activo" {{ old('estado', $usuario->estado) == 'activo' ? 'selected' : '' }}>Activo</option>
                        <option value="inactivo" {{ old('estado', $usuario->estado) == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>
            </div>

            <div class="pt-6 border-t border-gray-100 flex justify-end gap-3">
                <a href="{{ route('admin.usuarios.index') }}" class="px-5 py-2.5 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">Cancelar</a>
                <button type="submit" class="px-5 py-2.5 bg-[#00626A] rounded-lg text-sm font-medium text-white hover:bg-[#004e55] shadow-sm transition-colors">Actualizar Usuario</button>
            </div>
        </form>
    </div>
</div>
@endsection