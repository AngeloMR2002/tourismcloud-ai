@extends('layouts.app')

@section('content')
<div class="tc-workspace max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="tc-page-heading flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.usuarios.index') }}" class="p-2 bg-white border border-gray-200 rounded-lg text-gray-500 hover:text-[#00626A] hover:bg-gray-50 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Detalle del usuario</h1>
        </div>
        <div class="flex items-center gap-2">
            @if($usuario->id !== auth()->id())
                <form method="POST" action="{{ route('admin.usuarios.toggle-estado', $usuario) }}"
                      onsubmit="return confirm('{{ $usuario->estado === 'activo' ? '¿Desactivar' : '¿Activar' }} a este usuario?');">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="{{ $usuario->estado === 'activo' ? 'tc-btn-danger' : 'tc-btn-outline' }}">
                        {{ $usuario->estado === 'activo' ? 'Desactivar' : 'Activar' }}
                    </button>
                </form>
            @endif
            <a href="{{ route('admin.usuarios.edit', $usuario->id) }}" class="tc-btn-primary">Editar</a>
        </div>
    </div>

    <div class="tc-card border border-gray-100 p-6 sm:p-8">
        <div class="flex items-center gap-4 pb-6 border-b border-gray-100">
            <div class="w-16 h-16 rounded-full bg-[#00626A]/10 text-[#00626A] flex items-center justify-center font-bold text-xl">
                {{ substr($usuario->nombre, 0, 1) }}{{ substr($usuario->apellido, 0, 1) }}
            </div>
            <div>
                <div class="text-lg font-semibold text-gray-900">{{ $usuario->nombre }} {{ $usuario->apellido }}</div>
                <div class="text-sm text-gray-500">{{ $usuario->email }}</div>
            </div>
        </div>

        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-6 text-sm">
            <div>
                <dt class="text-gray-500">Rol</dt>
                <dd class="mt-1 font-medium text-gray-900">{{ $usuario->rolEtiqueta() }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Estado</dt>
                <dd class="mt-1 font-medium {{ $usuario->estado === 'activo' ? 'text-emerald-700' : 'text-red-700' }}">{{ ucfirst($usuario->estado) }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Organización</dt>
                <dd class="mt-1 font-medium text-gray-900">{{ $organizacion ?? 'Sin organización' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">ID</dt>
                <dd class="mt-1 font-medium text-gray-900">{{ $usuario->id }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Fecha de registro</dt>
                <dd class="mt-1 font-medium text-gray-900">{{ $usuario->created_at?->format('d/m/Y H:i') }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Última actualización</dt>
                <dd class="mt-1 font-medium text-gray-900">{{ $usuario->updated_at?->format('d/m/Y H:i') }}</dd>
            </div>
        </dl>

        <p class="mt-8 text-xs text-gray-400">Por seguridad, la contraseña nunca se muestra. Cada usuario gestiona su propia contraseña mediante el flujo de recuperación de cuenta.</p>
    </div>
</div>
@endsection
