@extends('layouts.auth')

@section('content')
<main class="relative mx-auto flex min-h-screen w-full max-w-6xl flex-col px-4 py-5 sm:px-6 sm:py-7">
    <nav class="flex items-center justify-between gap-4" aria-label="Navegación de recuperación">
        <a href="{{ url('/') }}" class="inline-flex items-center gap-2.5 text-sm font-bold text-[#00626A]"><span class="grid h-9 w-9 place-items-center rounded-xl bg-[#00626A] text-white"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 17 8 11l4 4 5-8 4 10M4 21h16"/></svg></span> TourismCloud AI</a>
        <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-600 hover:text-[#00626A]"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 18-6-6 6-6"/></svg> Inicio</a>
    </nav>
    <div class="flex flex-1 items-center justify-center py-8 sm:py-12">
        <section class="w-full max-w-[29rem] rounded-3xl border border-slate-200 bg-white p-6 shadow-[0_16px_50px_rgba(16,47,46,.07)] sm:p-9" aria-labelledby="recovery-title">
            <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-[#00626A] hover:underline"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 18-6-6 6-6"/></svg> Volver al acceso</a>
            <div class="mt-6 flex items-center gap-2 text-xs font-semibold text-slate-500" aria-label="Paso 1 de 2"><span class="grid h-6 w-6 place-items-center rounded-full bg-[#00626A] text-white">1</span><span>Tu correo</span><span class="mx-1 h-px w-6 bg-slate-200"></span><span class="grid h-6 w-6 place-items-center rounded-full bg-slate-100">2</span><span>Nueva contraseña</span></div>
            <h1 id="recovery-title" class="mt-5 font-display text-3xl font-bold tracking-tight text-slate-900">Recuperar acceso</h1>
            <p class="mt-2 text-sm leading-6 text-slate-500">Escribe el correo de tu cuenta. Te enviaremos un código de seis dígitos para cambiar tu contraseña.</p>

            @if(session('error'))
                <div role="alert" class="mt-5 rounded-xl border border-red-100 bg-red-50 p-3 text-sm text-red-700">{{ session('error') }}</div>
            @endif

            <form action="{{ route('password.email') }}" method="POST" class="mt-6 space-y-5">
                @csrf
                <div>
                    <label for="recovery-email" class="mb-2 block text-sm font-semibold text-slate-700">Correo electrónico</label>
                    <input id="recovery-email" type="email" name="email" autocomplete="email" value="{{ old('email') }}" required autofocus placeholder="ejemplo@correo.com" class="tc-input h-12 w-full">
                    @error('email') <p role="alert" class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="tc-btn tc-btn-primary min-h-12 w-full justify-center">Enviar código <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/></svg></button>
            </form>
            <p class="mt-5 text-xs leading-5 text-slate-500">Después de enviarlo, revisa también la carpeta de correo no deseado.</p>
        </section>
    </div>
</main>
@endsection
