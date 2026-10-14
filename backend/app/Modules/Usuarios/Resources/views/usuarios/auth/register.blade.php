@extends('layouts.auth')

@section('content')
<main class="relative mx-auto flex min-h-screen w-full max-w-6xl flex-col px-4 py-5 sm:px-6 sm:py-7">
    <nav class="flex items-center justify-between gap-4" aria-label="Navegación de registro">
        <a href="{{ url('/') }}" class="inline-flex items-center gap-2.5 text-sm font-bold text-[#00626A]"><span class="grid h-9 w-9 place-items-center rounded-xl bg-[#00626A] text-white"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 17 8 11l4 4 5-8 4 10M4 21h16"/></svg></span> TourismCloud AI</a>
        <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-600 hover:text-[#00626A]"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 18-6-6 6-6"/></svg> Inicio</a>
    </nav>

    <div class="flex flex-1 items-center justify-center py-8 sm:py-12">
        <section class="w-full max-w-[32rem] rounded-3xl border border-slate-200 bg-white p-6 shadow-[0_16px_50px_rgba(16,47,46,.07)] sm:p-9" aria-labelledby="register-title">
            <p class="text-xs font-bold uppercase tracking-[.14em] text-[#9A442D]">Empieza tu viaje</p>
            <h1 id="register-title" class="mt-2 font-display text-3xl font-bold tracking-tight text-slate-900">Crear cuenta</h1>
            <p class="mt-2 text-sm leading-6 text-slate-500">Regístrate como turista para organizar tus planes.</p>

            <form method="POST" action="{{ route('register.post') }}" class="mt-6 space-y-4">
                @csrf
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="register-nombre" class="mb-2 block text-sm font-semibold text-slate-700">Nombre</label>
                        <input id="register-nombre" name="nombre" type="text" autocomplete="given-name" value="{{ old('nombre') }}" required autofocus class="tc-input h-12 w-full">
                        @error('nombre') <p role="alert" class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="register-apellido" class="mb-2 block text-sm font-semibold text-slate-700">Apellido</label>
                        <input id="register-apellido" name="apellido" type="text" autocomplete="family-name" value="{{ old('apellido') }}" required class="tc-input h-12 w-full">
                        @error('apellido') <p role="alert" class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div>
                    <label for="register-email" class="mb-2 block text-sm font-semibold text-slate-700">Correo electrónico</label>
                    <input id="register-email" name="email" type="email" autocomplete="email" value="{{ old('email') }}" required placeholder="ejemplo@correo.com" class="tc-input h-12 w-full">
                    @error('email') <p role="alert" class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div x-data="{ show: false }">
                    <label for="register-password" class="mb-2 block text-sm font-semibold text-slate-700">Contraseña</label>
                    <div class="relative">
                        <input id="register-password" name="password" type="password" x-bind:type="show ? 'text' : 'password'" autocomplete="new-password" required minlength="8" placeholder="Mínimo 8 caracteres" class="tc-input h-12 w-full" style="padding-right: 3rem;">
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 grid w-12 place-items-center rounded-r-xl text-slate-500 hover:text-[#00626A]" x-bind:aria-label="show ? 'Ocultar contraseña' : 'Mostrar contraseña'" x-bind:aria-pressed="show">
                            <svg x-show="!show" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2 12s3.5-6.5 10-6.5S22 12 22 12s-3.5 6.5-10 6.5S2 12 2 12Z"/><circle cx="12" cy="12" r="2.5"/></svg>
                            <svg x-show="show" style="display: none;" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m3 3 18 18M9.9 5.7A10.2 10.2 0 0 1 12 5.5C18.5 5.5 22 12 22 12a18.2 18.2 0 0 1-3.4 4.2M6.2 6.2C3.5 8.1 2 12 2 12s3.5 6.5 10 6.5a9.7 9.7 0 0 0 4.1-.9"/></svg>
                        </button>
                    </div>
                    @error('password') <p role="alert" class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="register-password-confirmation" class="mb-2 block text-sm font-semibold text-slate-700">Confirmar contraseña</label>
                    <input id="register-password-confirmation" name="password_confirmation" type="password" autocomplete="new-password" required minlength="8" placeholder="Repite tu contraseña" class="tc-input h-12 w-full">
                    @error('password_confirmation') <p role="alert" class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="tc-btn tc-btn-primary min-h-12 w-full justify-center">Crear mi cuenta <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/></svg></button>
            </form>

            <p class="mt-6 border-t border-slate-100 pt-5 text-center text-sm text-slate-600">¿Ya tienes una cuenta? <a href="{{ route('login') }}" class="font-semibold text-[#00626A] hover:underline">Iniciar sesión</a></p>
        </section>
    </div>
</main>
@endsection
