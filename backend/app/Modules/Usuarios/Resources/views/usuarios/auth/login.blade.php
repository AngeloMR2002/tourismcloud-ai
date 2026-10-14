@extends('layouts.auth')

@section('content')
<main class="relative mx-auto flex min-h-screen w-full max-w-6xl flex-col px-4 py-5 sm:px-6 sm:py-7">
    <nav class="flex items-center justify-between gap-4" aria-label="Navegación de acceso">
        <a href="{{ url('/') }}" class="inline-flex items-center gap-2.5 text-sm font-bold text-[#00626A]">
            <span class="grid h-9 w-9 place-items-center rounded-xl bg-[#00626A] text-white"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 17 8 11l4 4 5-8 4 10M4 21h16"/></svg></span>
            TourismCloud AI
        </a>
        <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-600 hover:text-[#00626A]"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 18-6-6 6-6"/></svg> Inicio</a>
    </nav>

    <div class="flex flex-1 items-center justify-center py-8 sm:py-12">
        <section class="w-full max-w-[29rem] rounded-3xl border border-slate-200 bg-white p-6 shadow-[0_16px_50px_rgba(16,47,46,.07)] sm:p-9" aria-labelledby="login-title">
            <p class="text-xs font-bold uppercase tracking-[.14em] text-[#9A442D]">Tu cuenta</p>
            <h1 id="login-title" class="mt-2 font-display text-3xl font-bold tracking-tight text-slate-900">Iniciar sesión</h1>
            <p class="mt-2 text-sm leading-6 text-slate-500">Continúa con tus planes de viaje.</p>

            @if(session('exito'))
                <div role="status" class="mt-5 rounded-xl border border-emerald-100 bg-emerald-50 p-3 text-sm text-emerald-800">{{ session('exito') }}</div>
            @endif
            @if(session('error'))
                <div role="alert" class="mt-5 rounded-xl border border-red-100 bg-red-50 p-3 text-sm text-red-700">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="mt-6 space-y-5">
                @csrf
                <div>
                    <label for="login-email" class="mb-2 block text-sm font-semibold text-slate-700">Correo electrónico</label>
                    <input id="login-email" name="email" type="email" autocomplete="email" value="{{ old('email') }}" placeholder="ejemplo@correo.com" required autofocus class="tc-input h-12 w-full">
                    @error('email') <p role="alert" class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div x-data="{ showPassword: false }">
                    <div class="mb-2 flex items-center justify-between gap-3">
                        <label for="login-password" class="text-sm font-semibold text-slate-700">Contraseña</label>
                        <a href="{{ route('password.request') }}" class="text-xs font-semibold text-[#00626A] hover:underline">¿La olvidaste?</a>
                    </div>
                    <div class="relative">
                        <input id="login-password" name="password" type="password" x-bind:type="showPassword ? 'text' : 'password'" autocomplete="current-password" placeholder="Tu contraseña" required class="tc-input h-12 w-full" style="padding-right: 3rem;">
                        <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 grid w-12 place-items-center rounded-r-xl text-slate-500 hover:text-[#00626A]" x-bind:aria-label="showPassword ? 'Ocultar contraseña' : 'Mostrar contraseña'" x-bind:aria-pressed="showPassword">
                            <svg x-show="!showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2 12s3.5-6.5 10-6.5S22 12 22 12s-3.5 6.5-10 6.5S2 12 2 12Z"/><circle cx="12" cy="12" r="2.5"/></svg>
                            <svg x-show="showPassword" style="display: none;" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m3 3 18 18M9.9 5.7A10.2 10.2 0 0 1 12 5.5C18.5 5.5 22 12 22 12a18.2 18.2 0 0 1-3.4 4.2M6.2 6.2C3.5 8.1 2 12 2 12s3.5 6.5 10 6.5a9.7 9.7 0 0 0 4.1-.9"/></svg>
                        </button>
                    </div>
                    @error('password') <p role="alert" class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <label class="flex cursor-pointer items-center gap-2.5 text-sm text-slate-600">
                    <input type="checkbox" name="remember" @checked(old('remember')) class="h-4 w-4 rounded border-slate-300 text-[#00626A] focus:ring-[#00626A]">
                    Mantener mi sesión iniciada
                </label>
                <button type="submit" class="tc-btn tc-btn-primary min-h-12 w-full justify-center">Iniciar sesión <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/></svg></button>
            </form>

            <div class="mt-6 border-t border-slate-100 pt-5">
                <button type="button" disabled title="El acceso con Google aún no está disponible" class="flex w-full cursor-not-allowed items-center justify-center gap-2 rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-400">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M21.8 12.2c0-.7-.1-1.4-.2-2.1H12v4h5.5a4.8 4.8 0 0 1-2 3.1v2.6h3.3c1.9-1.8 3-4.4 3-7.6ZM12 22c2.7 0 5-1 6.8-2.2l-3.3-2.6c-.9.6-2 .9-3.5.9a6 6 0 0 1-5.6-4.1H3v2.6A10 10 0 0 0 12 22ZM6.4 14a6 6 0 0 1 0-4V7.4H3a10 10 0 0 0 0 9.2L6.4 14ZM12 6c1.5 0 2.8.5 3.8 1.5l2.9-2.9A9.6 9.6 0 0 0 12 2a10 10 0 0 0-9 5.4L6.4 10A6 6 0 0 1 12 6Z"/></svg>
                    Google <span class="text-xs">· próximamente</span>
                </button>
                <p class="mt-5 text-center text-sm text-slate-600">¿Aún no tienes una cuenta? <a href="{{ route('register') }}" class="font-semibold text-[#00626A] hover:underline">Crear cuenta</a></p>
            </div>
        </section>
    </div>
</main>
@endsection
