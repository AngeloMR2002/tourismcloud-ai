@extends('layouts.app')

@section('content')
<section class="max-w-7xl mx-auto px-6 py-10">

    <!-- encabezao -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900">Agenda & Programación de Salidas</h1>
            <p class="text-slate-500 text-sm mt-1">Consulta los itinerarios agendados, horarios y estado de reservas para las próximas fechas.</p>
        </div>
        <div class="flex items-center gap-3">
            <button class="px-4 py-2.5 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 rounded-xl text-sm font-semibold transition-all shadow-sm flex items-center gap-2">
                <i data-lucide="filter" class="w-4 h-4 text-purple-600"></i> Filtrar
            </button>
            <button class="px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-sm font-semibold transition-all shadow-md shadow-purple-200 flex items-center gap-2">
                <i data-lucide="calendar-plus" class="w-4 h-4"></i> Agendar Salida
            </button>
        </div>
    </div>

    <!-- contenido principal: próximas salidas , calendario -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- lista de salidas programadas  -->
        <div class="lg:col-span-2 space-y-4">
            <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <i data-lucide="clock" class="w-5 h-5 text-purple-600"></i> Próximos Tour Confirmados
            </h2>

            <!-- Evento 1 -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:border-purple-200 transition-all">
                <div class="flex items-start gap-4">
                    <div class="p-3 bg-purple-50 text-purple-700 rounded-xl font-bold text-center min-w-[64px]">
                        <span class="text-xs uppercase block text-purple-500">OCT</span>
                        <span class="text-2xl font-black">24</span>
                    </div>
                    <div>
                        <span class="text-[11px] font-bold text-purple-600 uppercase tracking-wider bg-purple-50 px-2 py-0.5 rounded-md">Confirmado</span>
                        <h3 class="font-bold text-slate-900 text-base mt-1">Circuito Costa & Playas</h3>
                        <p class="text-slate-500 text-xs flex items-center gap-3 mt-1">
                            <span class="flex items-center gap-1"><i data-lucide="map-pin" class="w-3.5 h-3.5"></i> Salida: Terminal Principal</span>
                            <span class="flex items-center gap-1"><i data-lucide="users" class="w-3.5 h-3.5"></i> 18 / 20 Pasajeros</span>
                        </p>
                    </div>
                </div>
                <div class="flex sm:flex-col items-center sm:items-end justify-between border-t sm:border-0 border-slate-100 pt-3 sm:pt-0">
                    <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full">2 Cupos Libres</span>
                    <a href="#" class="text-xs font-bold text-purple-600 hover:text-purple-700 mt-2">Ver Lista →</a>
                </div>
            </div>

            <!-- Evento 2 -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:border-purple-200 transition-all">
                <div class="flex items-start gap-4">
                    <div class="p-3 bg-slate-100 text-slate-700 rounded-xl font-bold text-center min-w-[64px]">
                        <span class="text-xs uppercase block text-slate-400">NOV</span>
                        <span class="text-2xl font-black">02</span>
                    </div>
                    <div>
                        <span class="text-[11px] font-bold text-amber-600 uppercase tracking-wider bg-amber-50 px-2 py-0.5 rounded-md">En Reserva</span>
                        <h3 class="font-bold text-slate-900 text-base mt-1">Ruta Aventura Andina</h3>
                        <p class="text-slate-500 text-xs flex items-center gap-3 mt-1">
                            <span class="flex items-center gap-1"><i data-lucide="map-pin" class="w-3.5 h-3.5"></i> Salida: Estación Central</span>
                            <span class="flex items-center gap-1"><i data-lucide="users" class="w-3.5 h-3.5"></i> 8 / 15 Pasajeros</span>
                        </p>
                    </div>
                </div>
                <div class="flex sm:flex-col items-center sm:items-end justify-between border-t sm:border-0 border-slate-100 pt-3 sm:pt-0">
                    <span class="text-xs font-semibold text-slate-600 bg-slate-100 px-2.5 py-1 rounded-full">7 Cupos Libres</span>
                    <a href="#" class="text-xs font-bold text-purple-600 hover:text-purple-700 mt-2">Ver Lista →</a>
                </div>
            </div>
        </div>

        <!-- resumen de disponibilidad -->
        <div class="space-y-6">
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
                <h3 class="font-bold text-slate-900 text-base mb-4 flex items-center gap-2">
                    <i data-lucide="calendar" class="w-5 h-5 text-purple-600"></i> Resumen de Estado
                </h3>
                
                <div class="space-y-3">
                    <div class="p-3.5 bg-slate-50 rounded-xl flex items-center justify-between">
                        <span class="text-xs font-medium text-slate-600">Salidas el Mes Actual</span>
                        <span class="text-sm font-bold text-slate-900">12 Salidas</span>
                    </div>
                    <div class="p-3.5 bg-slate-50 rounded-xl flex items-center justify-between">
                        <span class="text-xs font-medium text-slate-600">Total Reservas Conf.</span>
                        <span class="text-sm font-bold text-purple-600">145 Pasajeros</span>
                    </div>
                    <div class="p-3.5 bg-slate-50 rounded-xl flex items-center justify-between">
                        <span class="text-xs font-medium text-slate-600">Porcentaje Ocupación</span>
                        <span class="text-sm font-bold text-emerald-600">88%</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection