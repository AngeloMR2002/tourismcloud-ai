@extends('layouts.app')

@section('title', 'Editar: ' . $establecimiento->nombre)

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- ── Header ──────────────────────────────────────────────── --}}
    <div class="mb-8">
        <a href="{{ route('proveedor.establecimientos.index') }}"
           class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-gray-600 mb-3 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Mis Establecimientos
        </a>
        <h1 style="font-family: var(--font-display); font-size: 1.75rem; font-weight: 800; color: #1a2232;">
            Editar Establecimiento
        </h1>
        <p class="text-gray-400 text-sm mt-1">{{ $establecimiento->nombre }}</p>
    </div>

    @if($errors->any())
        <div class="tc-alert-error mb-6">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <p class="font-semibold">Por favor corrige los siguientes errores:</p>
                <ul class="mt-1 list-disc list-inside text-sm">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        </div>
    @endif

    <form method="POST"
          action="{{ route('proveedor.establecimientos.update', $establecimiento) }}"
          enctype="multipart/form-data"
          class="space-y-6">
        @csrf
        @method('PUT')
        <input type="hidden" name="_proveedor_id_test" value="{{ request('_proveedor_id_test', 2) }}">

        {{-- ── Información básica ──────────────────────────────── --}}
        <div class="tc-card p-6">
            <h2 class="text-sm font-bold uppercase tracking-wide mb-5" style="color: var(--color-primary-700);">
                Información básica
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div class="sm:col-span-2">
                    <label for="destino_id" class="block text-sm font-semibold mb-1.5 text-gray-700">
                        Destino <span class="text-red-500">*</span>
                    </label>
                    <select id="destino_id" name="destino_id" class="tc-select" required>
                        <option value="">Selecciona un destino…</option>
                        @foreach($destinos as $d)
                            <option value="{{ $d->id }}" @selected(old('destino_id', $establecimiento->destino_id) == $d->id)>
                                {{ $d->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('destino_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="tipo" class="block text-sm font-semibold mb-1.5 text-gray-700">
                        Tipo <span class="text-red-500">*</span>
                    </label>
                    <select id="tipo" name="tipo" class="tc-select" required>
                        <option value="hotel"       @selected(old('tipo', $establecimiento->tipo) === 'hotel')>🏨 Hotel</option>
                        <option value="restaurante" @selected(old('tipo', $establecimiento->tipo) === 'restaurante')>🍽️ Restaurante</option>
                        <option value="transporte"  @selected(old('tipo', $establecimiento->tipo) === 'transporte')>🚌 Transporte</option>
                        <option value="agencia"     @selected(old('tipo', $establecimiento->tipo) === 'agencia')>🏢 Agencia</option>
                        <option value="otro"        @selected(old('tipo', $establecimiento->tipo) === 'otro')>📋 Otro</option>
                    </select>
                    @error('tipo') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="rango_precio" class="block text-sm font-semibold mb-1.5 text-gray-700">
                        Rango de precio
                    </label>
                    <select id="rango_precio" name="rango_precio" class="tc-select">
                        <option value="">Sin especificar</option>
                        <option value="bajo"  @selected(old('rango_precio', $establecimiento->rango_precio) === 'bajo')>💚 Bajo</option>
                        <option value="medio" @selected(old('rango_precio', $establecimiento->rango_precio) === 'medio')>💛 Medio</option>
                        <option value="alto"  @selected(old('rango_precio', $establecimiento->rango_precio) === 'alto')>🟠 Alto</option>
                        <option value="lujo"  @selected(old('rango_precio', $establecimiento->rango_precio) === 'lujo')>💜 Lujo</option>
                    </select>
                    @error('rango_precio') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="nombre" class="block text-sm font-semibold mb-1.5 text-gray-700">
                        Nombre <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="nombre" name="nombre"
                           value="{{ old('nombre', $establecimiento->nombre) }}"
                           class="tc-input" maxlength="150" required>
                    @error('nombre') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="descripcion" class="block text-sm font-semibold mb-1.5 text-gray-700">Descripción</label>
                    <textarea id="descripcion" name="descripcion" rows="4"
                              class="tc-input resize-none">{{ old('descripcion', $establecimiento->descripcion) }}</textarea>
                </div>

                <div class="sm:col-span-2">
                    <label for="direccion" class="block text-sm font-semibold mb-1.5 text-gray-700">Dirección</label>
                    <input type="text" id="direccion" name="direccion"
                           value="{{ old('direccion', $establecimiento->direccion) }}"
                           class="tc-input" maxlength="255">
                </div>

                <div>
                    <label for="estado" class="block text-sm font-semibold mb-1.5 text-gray-700">
                        Estado <span class="text-red-500">*</span>
                    </label>
                    <select id="estado" name="estado" class="tc-select" required>
                        <option value="activo"   @selected(old('estado', $establecimiento->estado) === 'activo')>✅ Activo</option>
                        <option value="inactivo" @selected(old('estado', $establecimiento->estado) === 'inactivo')>⏸ Inactivo</option>
                    </select>
                </div>

                <div>
                    <label for="latitud" class="block text-sm font-semibold mb-1.5 text-gray-700">Latitud</label>
                    <input type="number" id="latitud" name="latitud"
                           value="{{ old('latitud', $establecimiento->latitud) }}"
                           step="0.0000001" min="-90" max="90" class="tc-input">
                </div>
                <div>
                    <label for="longitud" class="block text-sm font-semibold mb-1.5 text-gray-700">Longitud</label>
                    <input type="number" id="longitud" name="longitud"
                           value="{{ old('longitud', $establecimiento->longitud) }}"
                           step="0.0000001" min="-180" max="180" class="tc-input">
                </div>
            </div>
        </div>

        {{-- ── Categorías ──────────────────────────────────────── --}}
        <div class="tc-card p-6">
            <h2 class="text-sm font-bold uppercase tracking-wide mb-5" style="color: var(--color-primary-700);">
                Categorías de interés
            </h2>
            @php $categoriasSeleccionadas = old('categorias', $establecimiento->categorias->pluck('id')->toArray()); @endphp
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @foreach($categorias as $cat)
                    <label class="flex items-center gap-2.5 cursor-pointer group">
                        <input type="checkbox" name="categorias[]" value="{{ $cat->id }}"
                               {{ in_array($cat->id, $categoriasSeleccionadas) ? 'checked' : '' }}
                               class="w-4 h-4 rounded"
                               style="accent-color: var(--color-primary-500);">
                        <span class="text-sm text-gray-700">{{ $cat->nombre }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- ── Horarios semanales ──────────────────────────────── --}}
        <div class="tc-card p-6">
            <h2 class="text-sm font-bold uppercase tracking-wide mb-2" style="color: var(--color-primary-700);">
                Horarios de atención
            </h2>
            <p class="text-xs text-gray-400 mb-5">Activa el toggle y define el horario.</p>

            @php
                $diasConfig = ['lunes'=>'Lunes','martes'=>'Martes','miercoles'=>'Miércoles','jueves'=>'Jueves','viernes'=>'Viernes','sabado'=>'Sábado','domingo'=>'Domingo'];
                $horariosActuales = old('horarios', $establecimiento->horarios ?? []);
            @endphp

            <div class="space-y-3">
                @foreach($diasConfig as $clave => $nombre)
                    @php
                        $horarioDia = $horariosActuales[$clave] ?? null;
                        $abierto    = is_array($horarioDia) && isset($horarioDia['abre']);
                        $abre       = $horarioDia['abre']   ?? '09:00';
                        $cierra     = $horarioDia['cierra'] ?? '18:00';
                    @endphp
                    <div class="flex items-center gap-4 py-3 border-b border-gray-100 last:border-0">
                        <label class="tc-toggle shrink-0">
                            <input type="checkbox" id="toggle-{{ $clave }}"
                                   {{ $abierto ? 'checked' : '' }}
                                   onchange="toggleDia('{{ $clave }}', this.checked)">
                            <span class="tc-toggle-track"></span>
                            <span class="tc-toggle-thumb"></span>
                        </label>
                        <span class="w-24 text-sm font-semibold text-gray-700 shrink-0">{{ $nombre }}</span>
                        <div id="horario-inputs-{{ $clave }}" class="flex items-center gap-3 flex-1 {{ $abierto ? '' : 'hidden' }}">
                            <div class="flex items-center gap-2">
                                <label class="text-xs text-gray-400 shrink-0">Abre</label>
                                <input type="time" id="abre-{{ $clave }}"
                                       name="horarios[{{ $clave }}][abre]"
                                       value="{{ $abre }}" class="tc-input w-32 text-sm">
                            </div>
                            <span class="text-gray-300">→</span>
                            <div class="flex items-center gap-2">
                                <label class="text-xs text-gray-400 shrink-0">Cierra</label>
                                <input type="time" id="cierra-{{ $clave }}"
                                       name="horarios[{{ $clave }}][cierra]"
                                       value="{{ $cierra }}" class="tc-input w-32 text-sm">
                            </div>
                        </div>
                        <div id="horario-closed-{{ $clave }}" class="text-xs text-gray-400 {{ $abierto ? 'hidden' : '' }}">
                            Cerrado
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ── Imágenes ─────────────────────────────────────────── --}}
        <div class="tc-card p-6">
            <h2 class="text-sm font-bold uppercase tracking-wide mb-5" style="color: var(--color-primary-700);">
                Imágenes
            </h2>

            @if($establecimiento->imagen_portada)
                <div class="mb-4">
                    <p class="text-xs font-semibold text-gray-500 mb-2">Portada actual:</p>
                    <img src="{{ asset('storage/' . $establecimiento->imagen_portada) }}"
                         alt="{{ $establecimiento->nombre }}"
                         class="w-32 h-24 object-cover rounded-lg border border-gray-200">
                </div>
            @endif

            <div class="space-y-4">
                <div>
                    <label for="imagen_portada" class="block text-sm font-semibold mb-1.5 text-gray-700">
                        {{ $establecimiento->imagen_portada ? 'Reemplazar portada' : 'Imagen de portada' }}
                    </label>
                    <input type="file" id="imagen_portada" name="imagen_portada"
                           accept="image/jpg,image/jpeg,image/png,image/webp"
                           class="tc-input text-sm file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-amber-50 file:text-amber-700">
                    <p class="text-xs text-gray-400 mt-1">JPG, PNG o WebP. Máx 4 MB.</p>
                    @error('imagen_portada') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                @if($establecimiento->imagenes->isNotEmpty())
                    <div>
                        <p class="text-xs font-semibold text-gray-500 mb-2">
                            Galería actual ({{ $establecimiento->imagenes->count() }} foto(s)):
                        </p>
                        <div class="tc-gallery-grid">
                            @foreach($establecimiento->imagenes as $img)
                                <div class="tc-gallery-item">
                                    <img src="{{ asset('storage/' . $img->url) }}" alt="{{ $img->alt_text }}">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div>
                    <label for="galeria" class="block text-sm font-semibold mb-1.5 text-gray-700">
                        Agregar imágenes a la galería
                    </label>
                    <input type="file" id="galeria" name="galeria[]"
                           accept="image/jpg,image/jpeg,image/png,image/webp"
                           multiple
                           class="tc-input text-sm file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-amber-50 file:text-amber-700">
                    <p class="text-xs text-gray-400 mt-1">Las nuevas imágenes se agregan a las existentes.</p>
                </div>
            </div>
        </div>

        {{-- ── Botones ──────────────────────────────────────────── --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('proveedor.establecimientos.index') }}" class="tc-btn-outline">Cancelar</a>
            <button type="submit" class="tc-btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Guardar Cambios
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function toggleDia(dia, abierto) {
    const inputsDiv   = document.getElementById('horario-inputs-' + dia);
    const closedDiv   = document.getElementById('horario-closed-' + dia);
    const inputAbre   = document.getElementById('abre-' + dia);
    const inputCierra = document.getElementById('cierra-' + dia);
    if (abierto) {
        inputsDiv.classList.remove('hidden');
        closedDiv.classList.add('hidden');
        inputAbre.name   = 'horarios[' + dia + '][abre]';
        inputCierra.name = 'horarios[' + dia + '][cierra]';
    } else {
        inputsDiv.classList.add('hidden');
        closedDiv.classList.remove('hidden');
        inputAbre.removeAttribute('name');
        inputCierra.removeAttribute('name');
    }
}
document.addEventListener('DOMContentLoaded', function () {
    ['lunes','martes','miercoles','jueves','viernes','sabado','domingo'].forEach(function(dia) {
        const t = document.getElementById('toggle-' + dia);
        if (t && !t.checked) toggleDia(dia, false);
    });
});
</script>
@endpush
