@extends('layouts.auth')

@section('content')
<div class="min-h-screen flex items-center justify-center p-4 bg-gray-50">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl overflow-hidden p-8 border border-gray-100">
        
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-[#00626A]/10 mb-4">
                <svg class="w-8 h-8 text-[#00626A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Recuperar Acceso</h1>
            <p class="text-sm text-gray-500 mt-2">Ingresa tu correo electrónico y te enviaremos un código de seguridad de 6 dígitos.</p>
        </div>

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-700 text-sm font-medium rounded-xl flex items-center gap-3">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Correo Electrónico</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00626A]/20 focus:border-[#00626A] focus:bg-white transition-all text-gray-900" 
                    placeholder="ejemplo@correo.com">
                @error('email') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="w-full flex items-center justify-center py-3.5 px-4 bg-[#00626A] text-white font-semibold rounded-xl hover:bg-[#004e55] shadow-lg shadow-[#00626A]/20 transition-all active:scale-[0.98]">
                Enviar Código de Verificación
            </button>
        </form>

        <div class="mt-8 text-center">
            <a href="{{ route('login') }}" class="text-sm font-medium text-gray-500 hover:text-[#00626A] transition-colors flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Volver al inicio de sesión
            </a>
        </div>
    </div>
</div>
@endsection