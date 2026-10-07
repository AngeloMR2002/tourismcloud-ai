@extends('actividades::layouts.app')
@section('content')
<div class="px-4">
    <div class="tc-login-layout">
        <section class="tc-login-intro">
            <p class="tc-eyebrow">ESPACIO DE GESTIÓN · P3</p>
            <h2>Buenas experiencias.<br>Información bien cuidada.</h2>
            <p>Organiza tus actividades y rutas, documenta sus fuentes y mantén claros los datos que todavía necesitan confirmación.</p>
            <div class="flex flex-wrap gap-2"><span class="tc-chip">Actividades</span><span class="tc-chip">Rutas y paradas</span><span class="tc-chip">Fuentes</span></div>
            <a href="{{ route('catalogo.actividades.index') }}" class="text-sm text-primary-700 underline underline-offset-4 mt-4">← Volver al catálogo público</a>
        </section>
        <section class="tc-login-form">
            <p class="tc-eyebrow mb-3">BIENVENIDO DE NUEVO</p>
            <h1 class="text-2xl font-bold mb-3">Acceso al panel</h1>
            <p class="text-sm text-gray-600 mb-6">Inicia sesión con la cuenta de operador o administrador asignada al proyecto.</p>
            @if($errors->any())
                <p class="tc-alert-error mb-4" role="alert">{{ $errors->first() }}</p>
            @endif
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                <label class="block text-sm font-medium text-gray-700">Correo
                    <input type="email" name="email" value="{{ old('email') }}" autocomplete="username" placeholder="tu@correo.com" required class="tc-input mt-2">
                </label>
                <label class="block text-sm font-medium text-gray-700">Contraseña
                    <input type="password" name="password" autocomplete="current-password" placeholder="Tu contraseña" required class="tc-input mt-2">
                </label>
                <button class="tc-btn-primary w-full justify-center">Iniciar sesión <span aria-hidden="true">→</span></button>
            </form>
            <p class="text-xs text-gray-500 mt-6 border-t border-gray-100 pt-4">Acceso reservado a cuentas autorizadas. Explorar el catálogo no requiere iniciar sesión.</p>
        </section>
    </div>
</div>
@endsection
