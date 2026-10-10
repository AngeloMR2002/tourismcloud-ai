@extends('layouts.auth')

@section('content')
<div class="h-screen flex flex-col md:flex-row w-full overflow-hidden bg-white">
    
    <!-- Columna Izquierda: Formulario (Con scroll oculto) -->
    <section class="flex-1 flex items-center justify-center p-8 lg:p-16 overflow-y-auto [&::-webkit-scrollbar]:hidden [-ms-overflow-style:'none'] [scrollbar-width:none]">
        <div class="w-full max-w-md">
            <div class="flex flex-col gap-6">
                <!-- Título -->
                <div>
                    <h1 class="text-4xl md:text-5xl font-semibold leading-tight text-gray-900">Bienvenido</h1>
                    <p class="text-gray-500 mt-2">Accede a tu cuenta y planifica tu próximo viaje.</p>
                </div>

                @if(session('exito'))
                    <div class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm font-medium rounded-2xl flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('exito') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="p-4 bg-red-50 border border-red-100 text-red-700 text-sm font-medium rounded-2xl flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <!-- Formulario -->
                <form method="POST" action="{{ route('login.post') }}" class="space-y-4 mt-2">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label class="text-sm font-medium text-gray-500 mb-1 block">Correo Electrónico</label>
                        <div class="rounded-2xl border border-gray-200 bg-gray-50/50 transition-colors focus-within:border-[#00626A] focus-within:bg-[#00626A]/5">
                            <input name="email" type="email" value="{{ old('email') }}" placeholder="ejemplo@correo.com" required autofocus
                                class="w-full bg-transparent text-sm p-4 rounded-2xl focus:outline-none focus:ring-0 border-none">
                        </div>
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Password (con Alpine.js) -->
                    <div x-data="{ showPassword: false }">
                        <label class="text-sm font-medium text-gray-500 mb-1 block">Contraseña</label>
                        <div class="rounded-2xl border border-gray-200 bg-gray-50/50 transition-colors focus-within:border-[#00626A] focus-within:bg-[#00626A]/5 relative">
                            <input name="password" x-bind:type="showPassword ? 'text' : 'password'" placeholder="Ingresa tu contraseña" required
                                class="w-full bg-transparent text-sm p-4 pr-12 rounded-2xl focus:outline-none focus:ring-0 border-none">
                            
                            <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-gray-600">
                                <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg x-show="showPassword" style="display: none;" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" y1="2" x2="22" y2="22"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Recordarme y Olvidé contraseña -->
                    <div class="flex items-center justify-between text-sm mt-2">
                        <label class="flex items-center gap-2 cursor-pointer text-gray-600 hover:text-gray-900 transition-colors">
                            <input type="checkbox" name="remember" class="rounded border-gray-300 text-[#00626A] focus:ring-[#00626A]">
                            <span>Mantener sesión iniciada</span>
                        </label>
                        <a href="{{ route('password.request') }}" class="font-medium text-[#00626A] hover:underline">¿Olvidaste tu contraseña?</a>
                    </div>

                    <!-- Botón Ingresar -->
                    <button type="submit" class="w-full rounded-2xl bg-[#00626A] py-4 font-medium text-white hover:bg-[#004e55] transition-colors shadow-lg shadow-[#00626A]/30 mt-4">
                        Iniciar Sesión
                    </button>
                </form>

                <!-- Separador -->
                <div class="relative flex items-center justify-center mt-2">
                    <span class="w-full border-t border-gray-200"></span>
                    <span class="px-4 text-sm text-gray-400 bg-white absolute">O continúa con</span>
                </div>

                <!-- Botón Google -->
                <button type="button" class="w-full flex items-center justify-center gap-3 border border-gray-200 rounded-2xl py-4 hover:bg-gray-50 transition-colors text-gray-700 font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 48 48">
                        <path fill="#FFC107" d="M43.611 20.083H42V20H24v8h11.303c-1.649 4.657-6.08 8-11.303 8-6.627 0-12-5.373-12-12s12-5.373 12-12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 12.955 4 4 12.955 4 24s8.955 20 20 20 20-8.955 20-20c0-2.641-.21-5.236-.611-7.743z" />
                        <path fill="#FF3D00" d="M6.306 14.691l6.571 4.819C14.655 15.108 18.961 12 24 12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 16.318 4 9.656 8.337 6.306 14.691z" />
                        <path fill="#4CAF50" d="M24 44c5.166 0 9.86-1.977 13.409-5.192l-6.19-5.238C29.211 35.091 26.715 36 24 36c-5.202 0-9.619-3.317-11.283-7.946l-6.522 5.025C9.505 39.556 16.227 44 24 44z" />
                        <path fill="#1976D2" d="M43.611 20.083H42V20H24v8h11.303c-.792 2.237-2.231 4.166-4.087 5.571l6.19 5.238C42.022 35.026 44 30.038 44 24c0-2.641-.21-5.236-.611-7.743z" />
                    </svg>
                    Ingresar con Google
                </button>

                <p class="text-center text-sm text-gray-500 mt-2">
                    ¿Eres nuevo en la plataforma? <a href="{{ route('register') }}" class="font-semibold text-[#00626A] hover:underline">Crear cuenta</a>
                </p>
            </div>
        </div>
    </section>

    <!-- Columna Derecha: Imagen Hero -->
    <section class="hidden md:block flex-1 relative p-4 bg-white">
        <div class="absolute inset-4 rounded-3xl bg-cover bg-center shadow-2xl" 
             style="background-image: url('https://images.unsplash.com/photo-1526392060635-9d6019884377?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80');">
            <div class="absolute inset-0 bg-black/20 rounded-3xl"></div>
            
            <div class="absolute bottom-8 left-8 right-8 flex gap-4">
                <div class="flex items-start gap-3 rounded-3xl bg-white/20 backdrop-blur-md border border-white/30 p-5 max-w-sm text-white shadow-xl">
                    <div class="h-10 w-10 rounded-full bg-[#00626A] flex items-center justify-center font-bold text-lg shrink-0">AI</div>
                    <div class="text-sm leading-snug">
                        <p class="font-medium text-white">TourismCloud IA</p>
                        <p class="text-white/80 mt-1">Genera itinerarios inteligentes y optimiza tu tiempo de viaje con un solo clic.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>
@endsection