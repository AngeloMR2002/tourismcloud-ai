@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Encabezado --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Gestión de Usuarios</h1>
            <p class="text-sm text-gray-500 mt-1">Administra el acceso de operadores y turistas.</p>
        </div>

        <a href="{{ route('admin.usuarios.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#00626A] text-white text-sm font-medium rounded-xl hover:bg-[#004e55] shadow-sm transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo Usuario
        </a>
    </div>

    {{-- Filtros de búsqueda --}}
    <form method="GET" action="{{ route('admin.usuarios.index') }}"
          class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6 grid grid-cols-1 md:grid-cols-5 gap-3">

        <input
            type="text"
            name="q"
            value="{{ request('q') }}"
            placeholder="Buscar por nombre o correo"
            class="md:col-span-2 px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#00626A]/20 focus:border-[#00626A]"
        >

        <select name="rol"
                class="px-4 py-2.5 rounded-lg border border-gray-300 bg-white focus:ring-2 focus:ring-[#00626A]/20 focus:border-[#00626A]">
            <option value="">Todos los roles</option>
            <option value="administrador" @selected(request('rol') === 'administrador')>Administrador</option>
            <option value="operador_turistico" @selected(request('rol') === 'operador_turistico')>Operador</option>
            <option value="proveedor" @selected(request('rol') === 'proveedor')>Proveedor</option>
            <option value="turista" @selected(request('rol') === 'turista')>Turista</option>
        </select>

        <select name="estado"
                class="px-4 py-2.5 rounded-lg border border-gray-300 bg-white focus:ring-2 focus:ring-[#00626A]/20 focus:border-[#00626A]">
            <option value="">Todos los estados</option>
            <option value="activo" @selected(request('estado') === 'activo')>Activo</option>
            <option value="inactivo" @selected(request('estado') === 'inactivo')>Inactivo</option>
        </select>

        <div class="flex gap-2">
            <button type="submit"
                    class="flex-1 px-4 py-2.5 bg-[#00626A] text-white text-sm font-medium rounded-lg hover:bg-[#004e55] transition-colors">
                Filtrar
            </button>

            <a href="{{ route('admin.usuarios.index') }}"
               class="px-4 py-2.5 bg-white border border-gray-300 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                Limpiar
            </a>
        </div>
    </form>

    {{-- Tabla de usuarios --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Usuario</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Contacto</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Rol</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Acciones</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">

                    @forelse($usuarios as $user)
                        <tr class="hover:bg-gray-50/50 transition-colors">

                            {{-- Usuario --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-[#00626A]/10 text-[#00626A] flex items-center justify-center font-bold text-sm">
                                        {{ substr($user->nombre, 0, 1) }}{{ substr($user->apellido, 0, 1) }}
                                    </div>

                                    <div>
                                        <div class="font-medium text-gray-900">
                                            {{ $user->nombre }} {{ $user->apellido }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            ID: {{ $user->id }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Contacto y fecha de registro --}}
                            <td class="px-6 py-4">
                                <div class="text-xs text-gray-400">
                                    Registrado: {{ $user->created_at?->format('d/m/Y') }}
                                </div>
                                <div class="text-sm text-gray-600">
                                    {{ $user->email }}
                                </div>
                            </td>

                            {{-- Rol --}}
                            <td class="px-6 py-4">
                                @if($user->rol === 'administrador' || $user->rol === 'admin')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-purple-50 text-purple-700 border border-purple-100">
                                        Administrador
                                    </span>
                                @elseif($user->rol === 'operador_turistico')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                        Operador
                                    </span>
                                @elseif($user->rol === 'proveedor')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-100">
                                        Proveedor
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                                        Turista
                                    </span>
                                @endif
                            </td>

                            {{-- Estado --}}
                            <td class="px-6 py-4">
                                @if($user->estado === 'activo')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium text-emerald-700 bg-emerald-50 border border-emerald-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Activo
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium text-red-700 bg-red-50 border border-red-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                        Inactivo
                                    </span>
                                @endif
                            </td>

                            {{-- Acciones --}}
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">

                                    {{-- Ver detalle --}}
                                    <a href="{{ route('admin.usuarios.show', $user->id) }}"
                                       title="Ver detalle"
                                       aria-label="Ver detalle de usuario"
                                       class="p-2 text-gray-400 hover:text-[#00626A] hover:bg-[#00626A]/10 rounded-lg transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>

                                    {{-- Editar --}}
                                    <a href="{{ route('admin.usuarios.edit', $user->id) }}"
                                       title="Editar usuario"
                                       aria-label="Editar usuario"
                                       class="p-2 text-gray-400 hover:text-[#00626A] hover:bg-[#00626A]/10 rounded-lg transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>

                                    {{-- Eliminar --}}
                                    <form action="{{ route('admin.usuarios.destroy', $user->id) }}"
                                          method="POST"
                                          class="inline-block"
                                          onsubmit="return confirm('¿Seguro que deseas eliminar este usuario?');">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                title="Eliminar usuario"
                                                aria-label="Eliminar usuario"
                                                class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">
                                No se encontraron usuarios con esos filtros.
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $usuarios->links() }}
        </div>
    </div>
</div>
@endsection