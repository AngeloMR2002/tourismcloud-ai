@extends('layouts.app')

@section('title', 'Mis Establecimientos')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- ── Header del panel ────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
                      style="background: var(--color-amber-soft); color: var(--color-tertiary-500);">
                    Panel Proveedor
                </span>
            </div>
            <h1 style="font-family: var(--font-display); font-size: 1.75rem; font-weight: 800; color: #1a2232;">
                Mis Establecimientos
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Gestiona tus hoteles, restaurantes, agencias y más. Activa o desactiva su visibilidad.
            </p>
        </div>
        <a href="{{ route('proveedor.establecimientos.create') }}" class="tc-btn-primary shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo Establecimiento
        </a>
    </div>

    {{-- ── Filtros rápidos ─────────────────────────────────────── --}}
    <div class="tc-card p-4 mb-6">
        <form method="GET" action="{{ route('proveedor.establecimientos.index') }}"
              class="flex flex-col sm:flex-row gap-3 flex-wrap">
            <input type="hidden" name="_proveedor_id_test" value="{{ request('_proveedor_id_test', 2) }}">

            <select name="destino_id" class="tc-select sm:w-44">
                <option value="">Todos los destinos</option>
                @foreach($destinos as $d)
                    <option value="{{ $d->id }}" @selected(request('destino_id') == $d->id)>{{ $d->nombre }}</option>
                @endforeach
            </select>

            <select name="tipo" class="tc-select sm:w-40">
                <option value="">Todos los tipos</option>
                <option value="hotel"       @selected(request('tipo') === 'hotel')>Hotel</option>
                <option value="restaurante" @selected(request('tipo') === 'restaurante')>Restaurante</option>
                <option value="transporte"  @selected(request('tipo') === 'transporte')>Transporte</option>
                <option value="agencia"     @selected(request('tipo') === 'agencia')>Agencia</option>
                <option value="otro"        @selected(request('tipo') === 'otro')>Otro</option>
            </select>

            <select name="rango_precio" class="tc-select sm:w-40">
                <option value="">Cualquier precio</option>
                <option value="bajo"  @selected(request('rango_precio') === 'bajo')>Bajo</option>
                <option value="medio" @selected(request('rango_precio') === 'medio')>Medio</option>
                <option value="alto"  @selected(request('rango_precio') === 'alto')>Alto</option>
                <option value="lujo"  @selected(request('rango_precio') === 'lujo')>Lujo</option>
            </select>

            <div class="relative flex-1 min-w-0">
                <input type="text" name="busqueda" value="{{ request('busqueda') }}"
                       placeholder="Buscar establecimiento..." class="tc-input pl-9">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            <button type="submit" class="tc-btn-primary">Buscar</button>
            <a href="{{ route('proveedor.establecimientos.index') }}" class="tc-btn-outline">Limpiar</a>
        </form>
    </div>

    {{-- ── Tabla de gestión ─────────────────────────────────────── --}}
    <div class="tc-card overflow-hidden">
        @if($establecimientos->isEmpty())
            <div class="text-center py-16">
                <svg class="w-14 h-14 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <p class="text-gray-500 font-medium mb-3">No tienes establecimientos aún.</p>
                <a href="{{ route('proveedor.establecimientos.create') }}" class="tc-btn-primary text-sm">
                    Crear tu primer establecimiento
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="tc-table">
                    <thead>
                        <tr>
                            <th>Establecimiento</th>
                            <th>Destino</th>
                            <th class="text-center">Tipo</th>
                            <th class="text-center">Precio</th>
                            <th>Categorías</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($establecimientos as $est)
                            <tr>
                                {{-- Nombre + thumbnail --}}
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg overflow-hidden shrink-0 flex items-center justify-center text-xl"
                                             style="background: var(--color-amber-soft);">
                                            @if($est->imagen_portada)
                                                <img src="{{ asset('storage/' . $est->imagen_portada) }}"
                                                     alt="{{ $est->nombre }}"
                                                     class="w-full h-full object-cover">
                                            @else
                                                @php $iconos = ['hotel'=>'🏨','restaurante'=>'🍽️','transporte'=>'🚌','agencia'=>'🏢','otro'=>'📋']; @endphp
                                                {{ $iconos[$est->tipo] ?? '🏢' }}
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-800 leading-tight">{{ $est->nombre }}</p>
                                            @if($est->direccion)
                                                <p class="text-xs text-gray-400 truncate max-w-48">{{ $est->direccion }}</p>
                                            @else
                                                <p class="text-xs text-gray-300">ID #{{ $est->id }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Destino --}}
                                <td>
                                    @if($est->destino)
                                        <span class="text-sm text-gray-600">{{ $est->destino->nombre }}</span>
                                    @else
                                        <span class="text-gray-400 text-xs">—</span>
                                    @endif
                                </td>

                                {{-- Tipo --}}
                                <td class="text-center">
                                    <span class="tc-badge-tipo">{{ ucfirst($est->tipo) }}</span>
                                </td>

                                {{-- Rango de precio --}}
                                <td class="text-center">
                                    @if($est->rango_precio)
                                        <span class="tc-badge-precio-{{ $est->rango_precio }}">
                                            {{ ucfirst($est->rango_precio) }}
                                        </span>
                                    @else
                                        <span class="text-gray-300 text-xs">—</span>
                                    @endif
                                </td>

                                {{-- Categorías --}}
                                <td>
                                    <div class="flex flex-wrap gap-1 max-w-xs">
                                        @forelse($est->categorias->take(3) as $cat)
                                            <span class="tc-chip">{{ $cat->nombre }}</span>
                                        @empty
                                            <span class="text-gray-400 text-xs">Sin categorías</span>
                                        @endforelse
                                    </div>
                                </td>

                                {{-- Toggle de estado (NO es soft delete) --}}
                                <td class="text-center">
                                    <form method="POST"
                                          action="{{ route('proveedor.establecimientos.toggle-estado', $est) }}"
                                          id="toggle-est-{{ $est->id }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="_proveedor_id_test" value="{{ request('_proveedor_id_test', 2) }}">
                                        <label class="tc-toggle">
                                            <input type="checkbox"
                                                   {{ $est->estado === 'activo' ? 'checked' : '' }}
                                                   onchange="document.getElementById('toggle-est-{{ $est->id }}').submit()">
                                            <span class="tc-toggle-track"></span>
                                            <span class="tc-toggle-thumb"></span>
                                        </label>
                                    </form>
                                    <p class="text-xs mt-1 {{ $est->estado === 'activo' ? 'text-green-600' : 'text-gray-400' }}">
                                        {{ ucfirst($est->estado) }}
                                    </p>
                                </td>

                                {{-- Acciones --}}
                                <td class="text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('catalogo.establecimientos.show', $est) }}"
                                           target="_blank" title="Ver en catálogo"
                                           class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                        <a href="{{ route('proveedor.establecimientos.edit', $est) }}"
                                           title="Editar"
                                           class="p-1.5 rounded-lg transition-colors"
                                           style="color: var(--color-primary-600);"
                                           onmouseover="this.style.background='var(--color-teal-light)'"
                                           onmouseout="this.style.background='transparent'">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <form method="POST"
                                              action="{{ route('proveedor.establecimientos.destroy', $est) }}"
                                              onsubmit="return confirm('¿Eliminar «{{ addslashes($est->nombre) }}»?')">
                                            @csrf
                                            @method('DELETE')
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
