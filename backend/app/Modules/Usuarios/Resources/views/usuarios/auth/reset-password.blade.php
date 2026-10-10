@extends('layouts.auth')

@section('content')
<div class="min-h-screen flex items-center justify-center p-4 bg-gray-50">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl overflow-hidden p-8 border border-gray-100">
        
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-50 mb-4">
                <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Código Enviado</h1>
            <p class="text-sm text-gray-500 mt-2">Ingresa el código que enviamos a tu correo y tu nueva contraseña.</p>
        </div>

        @if(session('exito'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm font-medium rounded-xl flex items-center gap-3">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('exito') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-700 text-sm font-medium rounded-xl flex items-center gap-3">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('password.update') }}" method="POST" class="space-y-5">
            @csrf
            
            <!-- Correo (necesario para verificar de quién es el código) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirma tu Correo</label>
                <input type="email" name="email" value="{{ old('email', session('reset_email')) }}" required
                    class="w-full px-5 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00626A]/20 focus:border-[#00626A] focus:bg-white transition-all text-gray-900">
                @error('email') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <!-- Código OTP -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Código de 6 dígitos <span class="text-red-500">*</span></label>
                <input type="text" name="code" required maxlength="6" pattern="\d{6}" placeholder="000000" autofocus
                    class="w-full px-5 py-3 text-center tracking-[0.5em] font-mono text-xl bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00626A]/20 focus:border-[#00626A] focus:bg-white transition-all text-gray-900">
                @error('code') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <!-- Nueva Contraseña -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nueva Contraseña <span class="text-red-500">*</span></label>
                <input type="password" name="password" required minlength="8"
                    class="w-full px-5 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00626A]/20 focus:border-[#00626A] focus:bg-white transition-all text-gray-900">
                @error('password') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <!-- Confirmar Contraseña -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar Contraseña <span class="text-red-500">*</span></label>
                <input type="password" name="password_confirmation" required minlength="8"
                    class="w-full px-5 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00626A]/20 focus:border-[#00626A] focus:bg-white transition-all text-gray-900">
            </div>

            <button type="submit" class="w-full flex items-center justify-center py-3.5 px-4 mt-2 bg-[#00626A] text-white font-semibold rounded-xl hover:bg-[#004e55] shadow-lg shadow-[#00626A]/20 transition-all active:scale-[0.98]">
                Restablecer Contraseña
            </button>
        </form>

        <div class="mt-8 pt-4 border-t border-gray-100 flex items-center justify-between text-sm">
            <a href="{{ route('password.request') }}" class="font-medium text-[#00626A] hover:underline flex items-center gap-1">
                ↺ Reenviar código
            </a>
            <a href="{{ route('login') }}" class="font-medium text-gray-500 hover:text-gray-700">
                Volver al login
            </a>
        </div>
    </div>
</div>
@endsection