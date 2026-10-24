@extends('layouts.app')

@section('content')
<div class="tc-workspace max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="tc-page-heading">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.usuarios.index') }}" class="p-2 bg-white border border-gray-200 rounded-lg text-gray-500 hover:text-[#16734f] hover:bg-gray-50 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ __('admin_users.register_title') }}</h1>
                <p class="text-sm text-gray-500">{{ __('admin_users.register_subtitle') }}</p>
            </div>
        </div>
    </div>

    <div class="tc-card border border-gray-100 p-6 sm:p-8">
        <form action="{{ route('admin.usuarios.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nombre -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin_users.first_name') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="nombre" value="{{ old('nombre') }}" required
                        class="tc-input">
                    @error('nombre') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Apellido -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin_users.last_name') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="apellido" value="{{ old('apellido') }}" required
                        class="tc-input">
                    @error('apellido') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin_users.email') }} <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="tc-input">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Contraseña -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin_users.password') }} <span class="text-red-500">*</span></label>
                    <input type="password" name="password" required minlength="8"
                        class="tc-input">
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Rol -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin_users.system_role') }} <span class="text-red-500">*</span></label>
                    <select name="rol" required class="tc-select">
                        <option value="turista" {{ old('rol') == 'turista' ? 'selected' : '' }}>{{ __('admin_users.roles.turista') }}</option>
                        <option value="operador_turistico" {{ old('rol') == 'operador_turistico' ? 'selected' : '' }}>{{ __('admin_users.roles.operador_turistico') }}</option>
                        <option value="proveedor" {{ old('rol') == 'proveedor' ? 'selected' : '' }}>{{ __('admin_users.roles.proveedor') }}</option>
                        <option value="administrador" {{ old('rol') == 'administrador' || old('rol') == 'admin' ? 'selected' : '' }}>{{ __('admin_users.roles.administrador') }}</option>
                    </select>
                </div>

                <!-- Estado -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin_users.status') }}</label>
                    <select name="estado" class="tc-select md:w-1/2">
                        <option value="activo" {{ old('estado') == 'activo' ? 'selected' : '' }}>{{ __('admin_users.statuses.activo') }}</option>
                        <option value="inactivo" {{ old('estado') == 'inactivo' ? 'selected' : '' }}>{{ __('admin_users.statuses.inactivo') }}</option>
                    </select>
                </div>
            </div>

            <div class="pt-6 border-t border-gray-100 flex justify-end gap-3">
                <a href="{{ route('admin.usuarios.index') }}" class="tc-btn-outline">{{ __('admin_users.cancel') }}</a>
                <button type="submit" class="tc-btn-primary">{{ __('admin_users.save_user') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
