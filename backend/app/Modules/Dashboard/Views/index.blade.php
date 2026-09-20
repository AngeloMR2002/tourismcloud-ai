@extends('layouts.app')

@section('content')

<section class="relative bg-slate-900 min-h-[520px] flex items-center justify-center overflow-hidden">

    <!-- imagen de fondo  -->

    <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?q=80&w=1920&auto=format&fit=crop" 
         alt="Turismo Hero" 
         class="absolute inset-0 w-full h-full object-cover object-center opacity-40">
    <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent"></div>

    <div class="relative max-w-5xl mx-auto px-6 py-20 text-center md:text-left w-full">
        <span class="text-xs md:text-sm font-semibold tracking-widest uppercase text-purple-300 bg-purple-900/50 px-3 py-1 rounded-full border border-purple-400/30">
            Agencia de Viajes y Paquetes Turísticos
        </span>
        
        <h1 class="text-4xl md:text-6xl font-black text-white mt-4 mb-4 tracking-tight leading-tight">
            Encuentra tu <br class="hidden md:block"/>
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-200 via-purple-300 to-indigo-200">próximo viaje</span>
        </h1>
        
        <p class="text-slate-300 text-base md:text-lg max-w-xl mb-8 leading-relaxed">
            Te llevamos a donde quieras ir. Recorre el país y el mundo entero de la forma más sencilla. ¡Vamos todos a viajar!
        </p>

        <!-- formulario/barra de Búsqueda de Viajes -->
        <div class="bg-white/95 backdrop-blur p-4 md:p-6 rounded-2xl shadow-2xl max-w-4xl border border-white/20">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-center">
                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Destino</label>
                    <div class="flex items-center gap-2 border-b border-slate-200 py-1">
                        <i data-lucide="map-pin" class="w-4 h-4 text-purple-600"></i>
                        <input type="text" placeholder="¿A dónde quieres ir?" class="w-full text-sm bg-transparent focus:outline-none text-slate-800 font-medium">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Fecha de Salida</label>
                    <div class="flex items-center gap-2 border-b border-slate-200 py-1">
                        <i data-lucide="calendar" class="w-4 h-4 text-purple-600"></i>
                        <input type="date" class="w-full text-sm bg-transparent focus:outline-none text-slate-800 font-medium">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Pasajeros</label>
                    <div class="flex items-center gap-2 border-b border-slate-200 py-1">
                        <i data-lucide="users" class="w-4 h-4 text-purple-600"></i>
                        <select class="w-full text-sm bg-transparent focus:outline-none text-slate-800 font-medium">
                            <option>1 Adulto</option>
                            <option>2 Adultos</option>
                            <option>Familia (4+)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <a href="/itinerarios" class="w-full py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-xl font-semibold text-sm transition-all shadow-lg shadow-purple-300 flex items-center justify-center gap-2">
                        <i data-lucide="search" class="w-4 h-4"></i> Buscar viajes
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- modulos base-->
<section class="max-w-7xl mx-auto px-6 py-12">
    <h2 class="text-2xl font-bold text-slate-900 mb-6">Módulos del Sistema</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <div class="p-6 bg-white rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow">
            <div class="p-3 bg-purple-50 text-purple-600 rounded-xl w-fit mb-4">
                <i data-lucide="map" class="w-6 h-6"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-900 mb-1">Módulo Itinerarios</h3>
            <p class="text-slate-500 text-sm mb-4">Gestión de rutas turísticas, lugares de interés y paquetes personalizados.</p>
            <a href="/itinerarios" class="inline-flex items-center gap-1 text-sm font-semibold text-purple-600 hover:text-purple-700">
                Ver itinerarios <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>

        <div class="p-6 bg-white rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow">
            <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl w-fit mb-4">
                <i data-lucide="calendar" class="w-6 h-6"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-900 mb-1">Módulo Agenda</h3>
            <p class="text-slate-500 text-sm mb-4">Programación de actividades, calendario de salidas y control de cupos.</p>
            <a href="/agenda" class="inline-flex items-center gap-1 text-sm font-semibold text-purple-600 hover:text-purple-700">
                Ver agenda <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>

    </div>
</section>
@endsection