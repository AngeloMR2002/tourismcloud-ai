@extends('layouts.app')

@section('content')
<section class="max-w-7xl mx-auto px-6 py-10">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900">Paquetes e Itinerarios</h1>
            <p class="text-slate-500 text-sm mt-1">Explora las rutas turísticas configuradas en la plataforma.</p>
        </div>
        <button class="px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-sm font-medium flex items-center gap-2 w-fit">
            <i data-lucide="plus" class="w-4 h-4"></i> Nuevo Itinerario
        </button>
    </div>

    <!-- tarjetas turísticas -->

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- primera tarjeta -->
        <div class="bg-white rounded-2xl overflow-hidden border border-slate-200/80 shadow-sm hover:shadow-lg transition-all">
            <div class="h-48 bg-slate-200 relative">
                <img src="https://images.unsplash.com/photo-1526772662000-3f88f10405ff?q=80&w=600&auto=format&fit=crop" class="w-full h-full object-cover">
                <span class="absolute top-3 right-3 bg-white/90 backdrop-blur px-3 py-1 rounded-full text-xs font-bold text-purple-700">
                    3 Días / 2 Noches
                </span>
            </div>
            <div class="p-5">
                <h3 class="font-bold text-lg text-slate-900 mb-1">Ruta Aventura Andina</h3>
                <p class="text-slate-500 text-xs mb-4">Recorrido completo por paisajes montañosos y lagunas de alta montaña.</p>
                <div class="flex items-center justify-between border-t border-slate-100 pt-3">
                    <span class="text-xs text-slate-400 font-medium">Estado: <strong class="text-emerald-600">Disponible</strong></span>
                    <a href="#" class="text-xs font-bold text-purple-600 hover:underline">Detalles →</a>
                </div>
            </div>
        </div>

        <!-- segunda carta -->
        <div class="bg-white rounded-2xl overflow-hidden border border-slate-200/80 shadow-sm hover:shadow-lg transition-all">
            <div class="h-48 bg-slate-200 relative">
                <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?q=80&w=600&auto=format&fit=crop" class="w-full h-full object-cover">
                <span class="absolute top-3 right-3 bg-white/90 backdrop-blur px-3 py-1 rounded-full text-xs font-bold text-purple-700">
                    5 Días / 4 Noches
                </span>
            </div>
            <div class="p-5">
                <h3 class="font-bold text-lg text-slate-900 mb-1">Circuito Costa & Playas</h3>
                <p class="text-slate-500 text-xs mb-4">Experiencia de relajo total frente al mar con actividades acuáticas incluidas.</p>
                <div class="flex items-center justify-between border-t border-slate-100 pt-3">
                    <span class="text-xs text-slate-400 font-medium">Estado: <strong class="text-emerald-600">Disponible</strong></span>
                    <a href="#" class="text-xs font-bold text-purple-600 hover:underline">Detalles →</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection