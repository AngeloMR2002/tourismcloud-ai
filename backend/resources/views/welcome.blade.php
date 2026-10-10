@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
    <section class="relative isolate overflow-hidden rounded-3xl px-6 py-10 shadow-xl sm:px-10 sm:py-14 lg:px-14"
             style="background: linear-gradient(120deg, var(--color-primary-900), var(--color-primary-600));">
        <div class="absolute -right-16 -top-24 -z-10 h-72 w-72 rounded-full border-[36px] border-white/10"></div>
        <div class="absolute -bottom-36 right-1/4 -z-10 h-72 w-72 rounded-full bg-white/5 blur-2xl"></div>

        <div class="grid items-center gap-10 lg:grid-cols-[1.2fr_0.8fr]">
            <div class="max-w-2xl">
                <span class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-1.5 text-xs font-semibold uppercase tracking-wider text-white/90">
                    <span class="h-2 w-2 rounded-full bg-amber-300"></span>
                    Turismo que conecta
                </span>
                <h1 class="mt-5 max-w-xl text-4xl font-extrabold leading-tight tracking-tight text-white sm:text-5xl"
                    style="font-family: var(--font-display);">
                    Tu próxima gran experiencia empieza aquí.
                </h1>
                <p class="mt-5 max-w-xl text-base leading-7 text-white/75 sm:text-lg">
                    Explora lugares especiales, encuentra dónde hospedarte y organiza tus preferencias para descubrir experiencias a tu medida.
                </p>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('catalogo.atractivos.index') }}"
                       class="inline-flex items-center justify-center gap-2 rounded-lg bg-white px-5 py-3 text-sm font-bold text-primary-800 shadow-sm transition hover:bg-primary-50">
                        Explorar atractivos
                        <span aria-hidden="true">→</span>
                    </a>
                    <a href="{{ route('catalogo.establecimientos.index') }}"
                       class="inline-flex items-center justify-center rounded-lg border border-white/35 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                        Ver establecimientos
                    </a>
                </div>
            </div>

            <div class="hidden justify-center lg:flex" aria-hidden="true">
                <div class="relative flex h-64 w-64 items-center justify-center rounded-full border border-white/20 bg-white/10 shadow-2xl backdrop-blur-sm">
                    <div class="absolute inset-5 rounded-full border border-dashed border-white/30"></div>
                    <div class="absolute inset-12 rounded-full border border-white/20"></div>
                    <svg class="h-28 w-28 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M3.055 11H5a2 2 0 0 1 2 2v1a2 2 0 0 0 2 2 2 2 0 0 1 2 2v2.945M8 3.935V5.5A2.5 2.5 0 0 0 10.5 8h.5a2 2 0 0 1 2 2 2 2 0 1 0 4 0 2 2 0 0 1 2-2h1.064M15 20.488V18a2 2 0 0 1 2-2h3.064M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m9 12 2 2 4-4"/>
                    </svg>
                    <span class="absolute right-5 top-8 flex h-11 w-11 items-center justify-center rounded-2xl bg-amber-300 text-xl text-primary-900 shadow-lg">✦</span>
                    <span class="absolute bottom-7 left-5 flex h-10 w-10 items-center justify-center rounded-xl bg-white text-lg text-primary-600 shadow-lg">⌖</span>
                </div>
            </div>
        </div>
    </section>

    <section class="mt-10" aria-labelledby="explora-heading">
        <div class="mb-5 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-primary-600">Empieza a descubrir</p>
                <h2 id="explora-heading" class="mt-1 text-2xl font-bold text-slate-900">¿Qué te gustaría explorar?</h2>
            </div>
            <p class="max-w-lg text-sm leading-6 text-slate-500">Encuentra opciones para inspirar tu próximo viaje por el Perú.</p>
        </div>

        <div class="grid gap-5 md:grid-cols-3">
            <a href="{{ route('catalogo.atractivos.index') }}" class="group tc-card flex h-full flex-col p-6">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-primary-50 text-primary-700 transition group-hover:bg-primary-100">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 20l-5.447-2.724A1 1 0 0 1 3 16.382V5.618a1 1 0 0 1 1.447-.894L9 7m0 13 6-3m-6 3V7m6 10 4.553 2.276A1 1 0 0 0 21 18.382V7.618a1 1 0 0 0-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                </div>
                <h3 class="mt-5 text-lg font-bold text-slate-900">Atractivos turísticos</h3>
                <p class="mt-2 flex-1 text-sm leading-6 text-slate-500">Descubre paisajes, cultura y lugares únicos para sumar a tu recorrido.</p>
                <span class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-primary-700">Explorar atractivos <span aria-hidden="true" class="transition group-hover:translate-x-1">→</span></span>
            </a>

            <a href="{{ route('catalogo.establecimientos.index') }}" class="group tc-card flex h-full flex-col p-6">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-tertiary-50 text-tertiary-700 transition group-hover:bg-tertiary-100">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 21h18M5 21V7l8-4v18m6 0V11l-6-4M9 9v.01M9 12v.01M9 15v.01M9 18v.01m4-9v.01m0 3v.01m0 3v.01m0 3v.01m4-5v.01m0 3v.01"/>
                    </svg>
                </div>
                <h3 class="mt-5 text-lg font-bold text-slate-900">Establecimientos</h3>
                <p class="mt-2 flex-1 text-sm leading-6 text-slate-500">Encuentra opciones de hospedaje y servicios para hacer tu viaje más cómodo.</p>
                <span class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-primary-700">Ver establecimientos <span aria-hidden="true" class="transition group-hover:translate-x-1">→</span></span>
            </a>

            @php
                $preferenciasUrl = (auth()->check() && auth()->user()->isTurista())
                    ? route('preferencias.turista', auth()->id())
                    : route('preferencias.index');
            @endphp
            <a href="{{ $preferenciasUrl }}" class="group tc-card flex h-full flex-col p-6">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-secondary-50 text-secondary-600 transition group-hover:bg-secondary-100">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c1.657 0 3 .895 3 2s-1.343 2-3 2-3 .895-3 2 1.343 2 3 2m0-8c-1.11 0-2.08.402-2.599 1M12 8V6m0 6v2m0 4v-2m0 2c1.11 0 2.08-.402 2.599-1M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                    </svg>
                </div>
                <h3 class="mt-5 text-lg font-bold text-slate-900">Preferencias de viaje</h3>
                <p class="mt-2 flex-1 text-sm leading-6 text-slate-500">Consulta o registra tus preferencias para orientar la planificación del viaje.</p>
                <span class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-primary-700">Ver preferencias <span aria-hidden="true" class="transition group-hover:translate-x-1">→</span></span>
            </a>
        </div>
    </section>

    <section class="mt-8 overflow-hidden rounded-2xl border border-primary-100 bg-primary-50 p-6 sm:flex sm:items-center sm:justify-between sm:gap-6 sm:px-8">
        <div class="flex items-start gap-4">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white text-primary-700 shadow-sm">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 18h.01M8.2 9a4 4 0 1 1 7.6 1.5c-.9.6-1.8 1.2-1.8 2.5v.5m8-1.5a10 10 0 1 1-20 0 10 10 0 0 1 20 0Z"/>
                </svg>
            </div>
            <div>
                <h2 class="font-bold text-slate-900">¿Ya sabes qué tipo de viaje buscas?</h2>
                <p class="mt-1 text-sm leading-6 text-slate-600">Define tu presupuesto, duración y ritmo para tener tus preferencias a mano.</p>
            </div>
        </div>
        <a href="{{ $preferenciasUrl }}" class="tc-btn-primary mt-5 shrink-0 sm:mt-0">Ir a preferencias <span aria-hidden="true">→</span></a>
    </section>
</div>
@endsection