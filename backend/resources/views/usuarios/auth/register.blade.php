@extends('layouts.auth')

@section('content')
<div class="h-screen flex flex-col md:flex-row w-full overflow-hidden bg-white">
    
    <!-- Columna Izquierda: Formulario (Con scroll oculto) -->
    <section class="flex-1 flex items-center justify-center p-8 lg:p-12 overflow-y-auto [&::-webkit-scrollbar]:hidden [-ms-overflow-style:'none'] [scrollbar-width:none]">
        <div class="w-full max-w-md">
            <div class="flex flex-col gap-4">
                <div>
                    <h1 class="text-4xl font-semibold leading-tight text-gray-900">Crear cuenta</h1>
                    <p class="text-gray-500 mt-2">Únete a la nueva forma de viajar inteligente.</p>
                </div>

                <form method="POST" action="{{ route('register.post') }}" class="space-y-4 mt-2">
                    @csrf
                    
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Nombre -->
                        <div>
                            <label class="text-xs font-medium text-gray-500 mb-1 block">Nombre</label>
                            <div class="rounded-2xl border border-gray-200 bg-gray-50/50 focus-within:border-[#00626A]">
                                <input name="nombre" type="text" value="{{ old('nombre') }}" required
                                    class="w-full bg-transparent text-sm p-3.5 rounded-2xl focus:outline-none focus:ring-0 border-none">
                            </div>
                            @error('nombre') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Apellido -->
                        <div>
                            <label class="text-xs font-medium text-gray-500 mb-1 block">Apellido</label>
                            <div class="rounded-2xl border border-gray-200 bg-gray-50/50 focus-within:border-[#00626A]">
                                <input name="apellido" type="text" value="{{ old('apellido') }}" required
                                    class="w-full bg-transparent text-sm p-3.5 rounded-2xl focus:outline-none focus:ring-0 border-none">
                            </div>
                            @error('apellido') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="text-xs font-medium text-gray-500 mb-1 block">Correo Electrónico</label>
                        <div class="rounded-2xl border border-gray-200 bg-gray-50/50 focus-within:border-[#00626A]">
                            <input name="email" type="email" value="{{ old('email') }}" required
                                class="w-full bg-transparent text-sm p-3.5 rounded-2xl focus:outline-none focus:ring-0 border-none">
                        </div>
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Password -->
                    <div x-data="{ show: false }">
                        <label class="text-xs font-medium text-gray-500 mb-1 block">Contraseña</label>
                        <div class="rounded-2xl border border-gray-200 bg-gray-50/50 focus-within:border-[#00626A] relative">
                            <input name="password" x-bind:type="show ? 'text' : 'password'" required
                                class="w-full bg-transparent text-sm p-3.5 pr-12 rounded-2xl focus:outline-none focus:ring-0 border-none">
                            <button type="button" @click="show = !show" class="absolute inset-y-0 right-3 flex items-center text-gray-400">
                                <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg x-show="show" style="display: none;" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" y1="2" x2="22" y2="22"/></svg>
                            </button>
                        </div>
                        @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label class="text-xs font-medium text-gray-500 mb-1 block">Confirmar Contraseña</label>
                        <div class="rounded-2xl border border-gray-200 bg-gray-50/50 focus-within:border-[#00626A]">
                            <input name="password_confirmation" type="password" required
                                class="w-full bg-transparent text-sm p-3.5 rounded-2xl focus:outline-none focus:ring-0 border-none">
                        </div>
                    </div>

                    <button type="submit" class="w-full rounded-2xl bg-[#00626A] py-4 font-medium text-white hover:bg-[#004e55] transition-colors shadow-lg shadow-[#00626A]/30 mt-2">
                        Registrarse
                    </button>
                </form>

                <p class="text-center text-sm text-gray-500 mt-2">
                    ¿Ya tienes una cuenta? <a href="{{ route('login') }}" class="font-semibold text-[#00626A] hover:underline">Inicia sesión</a>
                </p>
            </div>
        </div>
    </section>

    <!-- Columna Derecha: Imagen -->
    <section class="hidden md:block flex-1 relative p-4 bg-white">
        <div class="absolute inset-4 rounded-3xl bg-cover bg-center shadow-2xl" 
             style="background-image: url('https://images.unsplash.com/photo-1587595431973-160d0d94add1?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80');">
            <div class="absolute inset-0 bg-black/10 rounded-3xl"></div>
        </div>
    </section>

</div>
@endsection