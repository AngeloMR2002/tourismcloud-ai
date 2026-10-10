@extends('layouts.panel')

@section('title', 'Mis Establecimientos')

@section('panel-rol-label', 'Proveedor')

@section('panel-sidebar-links')
    <a href="{{ route('proveedor.establecimientos.index') }}?_proveedor_id_test={{ request('_proveedor_id_test', 2) }}"
       class="group/sidebar flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 mb-0.5 text-white shadow-sm"
       style="background: var(--color-primary-500);">
        <div class="shrink-0 transition-transform duration-150 group-hover/sidebar:scale-110">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
        </div>
        <span class="whitespace-pre group-hover/sidebar:translate-x-1 transition-transform duration-150">Mis Establecimientos</span>
    </a>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-6">
        <div>
            <h1 style="font-family: var(--font-display); font-size: 1.75rem; font-weight: 800; color: #1a2232;">
                Mis Establecimientos
            </h1>
            <p class="text-sm text-gray-500 mt-0.5">Gestiona hoteles, restaurantes, agencias y más</p>
        </div>
        <a href="{{ route('proveedor.establecimientos.create') }}?_proveedor_id_test={{ request('_proveedor_id_test', 2) }}"
           class="tc-btn-primary shrink-0">
            + Nuevo Establecimiento
        </a>
    </div>

    {{-- Barra de filtros inline (Diseño actualizado según wireframe) --}}
    <div class="mb-6">
        <form method="GET" action="{{ route('proveedor.establecimientos.index') }}"
              class="flex flex-col sm:flex-row items-center justify-between gap-4 p-2 bg-white rounded-xl border border-gray-200"
              style="box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <input type="hidden" name="_proveedor_id_test" value="{{ request('_proveedor_id_test', 2) }}">

            {{-- Buscar (Izquierda) --}}
            <div class="flex items-center gap-2 px-3 w-full sm:w-80">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="busqueda" value="{{ request('busqueda') }}"
                       placeholder="Buscar establecimiento..."
                       class="flex-1 text-sm outline-none bg-transparent py-1.5 text-gray-700 placeholder-gray-400"
                       onkeypress="if(event.key === 'Enter') this.form.submit();">
            </div>

            {{-- Selects (Derecha) --}}
            <div class="flex items-center gap-3 w-full sm:w-auto px-1 sm:px-0">
                {{-- Tipo --}}
                <select name="tipo" class="tc-select text-sm h-9 py-1 pr-8 pl-3" style="min-width: 150px;" onchange="this.form.submit()">
                    <option value="">Todos los tipos</option>
                    <option value="hotel"       @selected(request('tipo') === 'hotel')>Hotel</option>
                    <option value="restaurante" @selected(request('tipo') === 'restaurante')>Restaurante</option>
                    <option value="transporte"  @selected(request('tipo') === 'transporte')>Transporte</option>
                    <option value="agencia"     @selected(request('tipo') === 'agencia')>Agencia</option>
                    <option value="otro"        @selected(request('tipo') === 'otro')>Otro</option>
                </select>

                {{-- Rango de precio --}}
                <select name="rango_precio" class="tc-select text-sm h-9 py-1 pr-8 pl-3" style="min-width: 160px;" onchange="this.form.submit()">
                    <option value="">Todos los precios</option>
                    <option value="bajo"  @selected(request('rango_precio') === 'bajo')>Bajo</option>
                    <option value="medio" @selected(request('rango_precio') === 'medio')>Medio</option>
                    <option value="alto"  @selected(request('rango_precio') === 'alto')>Alto</option>
                    <option value="lujo"  @selected(request('rango_precio') === 'lujo')>Lujo</option>
                </select>

                {{-- Destino --}}
                <select name="destino_id" class="tc-select text-sm h-9 py-1 pr-8 pl-3" style="min-width: 170px;" onchange="this.form.submit()">
                    <option value="">Todos los destinos</option>
                    @foreach($destinos as $d)
                        <option value="{{ $d->id }}" @selected(request('destino_id') == $d->id)>{{ $d->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    {{-- Tabla --}}
    <div class="tc-card overflow-hidden">
        @if($establecimientos->isEmpty())
            <div class="text-center py-16">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <p class="text-gray-500 font-medium mb-3">No tienes establecimientos registrados aún.</p>
                <a href="{{ route('proveedor.establecimientos.create') }}?_proveedor_id_test={{ request('_proveedor_id_test', 2) }}"
                   class="tc-btn-primary text-sm">+ Nuevo Establecimiento</a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="tc-table">
                    <thead>
                        <tr>
                            <th>Establecimiento</th>
                            <th class="text-center">Tipo</th>
                            <th>Destino</th>
                            <th class="text-center">Precio</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($establecimientos as $est)
                            <tr>
                                {{-- Establecimiento --}}
                                <td>
                                    <div class="flex items-center gap-3">
                                        @php $iconos = ['hotel'=>'🏨','restaurante'=>'🍽️','transporte'=>'🚌','agencia'=>'🏢','otro'=>'📋']; @endphp
                                        <div class="w-10 h-10 rounded-lg overflow-hidden shrink-0 flex items-center justify-center text-lg"
                                             style="background: var(--color-amber-soft);">
                                            @if($est->imagen_portada)
                                                <img src="{{ asset('storage/' . $est->imagen_portada) }}"
                                                     class="w-full h-full object-cover" alt="{{ $est->nombre }}">
                                            @else
                                                {{ $iconos[$est->tipo] ?? '🏢' }}
                                            @endif
                                        </div>
                                        <span class="font-semibold text-gray-800 text-sm">{{ $est->nombre }}</span>
                                    </div>
                                </td>

                                {{-- Tipo --}}
                                <td class="text-center">
                                    <span class="tc-badge-tipo">{{ ucfirst($est->tipo) }}</span>
                                </td>

                                {{-- Destino --}}
                                <td class="text-sm text-gray-500">{{ $est->destino?->nombre ?? '—' }}</td>

                                {{-- Precio --}}
                                <td class="text-center">
                                    @if($est->rango_precio)
                                        <span class="tc-badge-precio-{{ $est->rango_precio }}">{{ ucfirst($est->rango_precio) }}</span>
                                    @else
                                        <span class="text-gray-400 text-xs">—</span>
                                    @endif
                                </td>

                                {{-- Toggle estado --}}
                                <td class="text-center">
                                    <form method="POST"
                                          action="{{ route('proveedor.establecimientos.toggle-estado', $est) }}"
                                          id="tog-est-{{ $est->id }}">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="_proveedor_id_test" value="{{ request('_proveedor_id_test', 2) }}">
                                        <label class="tc-toggle">
                                            <input type="checkbox"
                                                   {{ $est->estado === 'activo' ? 'checked' : '' }}
                                                   onchange="document.getElementById('tog-est-{{ $est->id }}').submit()">
                                            <span class="tc-toggle-track"></span>
                                            <span class="tc-toggle-thumb"></span>
                                        </label>
                                    </form>
                                </td>

                                {{-- Acciones --}}
                                <td>
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a href="{{ route('catalogo.establecimientos.show', $est) }}" target="_blank"
                                           title="Ver en catálogo"
                                           class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                        <a href="{{ route('proveedor.establecimientos.edit', $est) }}?_proveedor_id_test={{ request('_proveedor_id_test', 2) }}"
                                           title="Editar"
                                           class="p-1.5 rounded-lg transition-colors"
                                           style="color: var(--color-primary-600);"
                                           onmouseover="this.style.background='var(--color-teal-light)'"
                                           onmouseout="this.style.background='transparent'">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <form method="POST" action="{{ route('proveedor.establecimientos.destroy', $est) }}"
                                              onsubmit="return confirm('¿Eliminar «{{ addslashes($est->nombre) }}»?')">
                                            @csrf @method('DELETE')
                                            <input type="hidden" name="_proveedor_id_test" value="{{ request('_proveedor_id_test', 2) }}">
                                            <button type="submit" title="Eliminar"
                                                    class="p-1.5 rounded-lg text-red-400 hover:text-red-600 hover:bg-red-50 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $establecimientos->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
