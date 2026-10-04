@extends('layouts.panel')

@section('title', 'Editar establecimiento')

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
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Back + Header --}}
    <div class="mb-6">
        <a href="{{ route('proveedor.establecimientos.index') }}?_proveedor_id_test={{ request('_proveedor_id_test', 2) }}"
           class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-gray-600 mb-3 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Mis Establecimientos
        </a>
        <h1 style="font-family: var(--font-display); font-size: 1.625rem; font-weight: 800; color: #1a2232;">
            Editar establecimiento
        </h1>
        <p class="text-sm text-gray-400 mt-0.5">Actualiza la información del establecimiento</p>
    </div>

    @if($errors->any())
        <div class="tc-alert-error mb-5">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <p class="font-semibold">Corrige los siguientes errores:</p>
                <ul class="mt-1 list-disc list-inside text-sm">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('proveedor.establecimientos.update', $establecimiento) }}"
          enctype="multipart/form-data" class="space-y-5" id="form-est-create">
        @csrf
        @method('PATCH')
        <input type="hidden" name="_proveedor_id_test" value="{{ request('_proveedor_id_test', 2) }}">

        {{-- ── 1. Tipo de establecimiento (primero, como en el wireframe) ── --}}
        <div class="tc-card p-6">
            <h2 class="text-sm font-bold uppercase tracking-wide mb-1" style="color: var(--color-primary-700);">
                Tipo de establecimiento
            </h2>
            <p class="text-xs text-gray-400 mb-4">Selecciona la categoría que mejor describe tu negocio</p>

            @php
                $tiposDisp = [
                    'restaurante' => ['ico' => '🍽️', 'label' => 'Restaurante'],
                    'hotel'       => ['ico' => '🏨', 'label' => 'Hotel'],
                    'transporte'  => ['ico' => '🚌', 'label' => 'Transporte'],
                    'agencia'     => ['ico' => '🏢', 'label' => 'Agencia'],
                    'otro'        => ['ico' => '📋', 'label' => 'Otro'],
                ];
                $tipoOld = old('tipo', $establecimiento->tipo);
            @endphp
            <div class="flex flex-wrap gap-3">
                @foreach($tiposDisp as $val => $t)
                    <label class="cursor-pointer">
                        <input type="radio" name="tipo" value="{{ $val }}"
                               {{ $tipoOld === $val ? 'checked' : '' }} required
                               class="sr-only peer"
                               onchange="selectTipo('{{ $val }}')">
                        <span id="tipo-btn-{{ $val }}"
                              class="flex items-center gap-2 px-4 py-2.5 rounded-xl border-2 font-semibold text-sm transition-all cursor-pointer select-none"
                              style="{{ $tipoOld === $val
                                ? 'background: var(--color-primary-500); color: white; border-color: var(--color-primary-500);'
                                : 'background: white; color: #374151; border-color: #d1d5db;' }}">
                            <span>{{ $t['ico'] }}</span>
                            {{ $t['label'] }}
                        </span>
                    </label>
                @endforeach
            </div>
            @error('tipo')<p class="mt-2 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        {{-- ── 2. Información básica ─────────────────────────────────── --}}
        <div class="tc-card p-6">
            <h2 class="text-sm font-bold uppercase tracking-wide mb-5" style="color: var(--color-primary-700);">
                Información básica
            </h2>
            <div class="space-y-4">
                {{-- Nombre --}}
                <div>
                    <label class="block text-sm font-semibold mb-1.5 text-gray-700">Nombre del establecimiento <span class="text-red-500">*</span></label>
                    <input type="text" name="nombre" value="{{ old('nombre', $establecimiento->nombre) }}"
                           placeholder="Ej: Restaurant La Tradición" class="tc-input" maxlength="150" required>
                    @error('nombre')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                {{-- Destino + Rango de precio --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1.5 text-gray-700">Destino <span class="text-red-500">*</span></label>
                        <select name="destino_id" class="tc-select" required>
                            <option value="">Seleccionar un destino…</option>
                            @foreach($destinos as $d)
                                <option value="{{ $d->id }}" @selected(old('destino_id', $establecimiento->destino_id) == $d->id)>{{ $d->nombre }}</option>
                            @endforeach
                        </select>
                        @error('destino_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1.5 text-gray-700">Rango de precio</label>
                        @php
                            $rangos = ['bajo'=>'Bajo $','medio'=>'Medio $$','alto'=>'Alto $$$','lujo'=>'Lujoso $$$$'];
                            $rpOld  = old('rango_precio', $establecimiento->rango_precio);
                        @endphp
                        <div class="grid grid-cols-2 gap-1.5">
                            @foreach($rangos as $rv => $rl)
                                <label class="cursor-pointer">
                                    <input type="radio" name="rango_precio" value="{{ $rv }}"
                                           {{ $rpOld === $rv ? 'checked' : '' }} class="sr-only"
                                           onchange="selectRango('{{ $rv }}')">
                                    <span id="rango-btn-{{ $rv }}"
                                          class="block text-xs font-semibold py-1.5 px-1 rounded-lg border text-center transition-all cursor-pointer"
                                          style="{{ $rpOld === $rv
                                            ? 'background: var(--color-primary-500); color: white; border-color: var(--color-primary-500);'
                                            : 'background: white; color: #374151; border-color: #d1d5db;' }}">
                                        {{ $rl }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Estado --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1.5 text-gray-700">Estado <span class="text-red-500">*</span></label>
                        <select name="estado" class="tc-select" required>
                            <option value="activo"   @selected(old('estado', $establecimiento->estado) === 'activo')>Activo</option>
                            <option value="inactivo" @selected(old('estado') === 'inactivo')>Inactivo</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1.5 text-gray-700">Dirección</label>
                        <input type="text" name="direccion" value="{{ old('direccion', $establecimiento->direccion) }}"
                               placeholder="Calle, número, referencia…" class="tc-input" maxlength="255">
                    </div>
                </div>

                {{-- Descripción --}}
                <div>
                    <label class="block text-sm font-semibold mb-1.5 text-gray-700">Descripción</label>
                    <textarea name="descripcion" rows="4" placeholder="Describe el establecimiento…" class="tc-input resize-none">{{ old('descripcion', $establecimiento->descripcion) }}</textarea>
                </div>
            </div>
        </div>

        {{-- ── 3. Categoría de Interés ────────────────────────────────── --}}
        <div class="tc-card p-6">
            <h2 class="text-sm font-bold uppercase tracking-wide mb-1" style="color: var(--color-primary-700);">
                Categoría de Interés
            </h2>
            <p class="text-xs text-gray-400 mb-4">Selecciona todas las que apliquen</p>
            <div class="flex flex-wrap gap-2">
                @foreach($categorias as $cat)
                    @php $checked = in_array($cat->id, old('categorias', $establecimiento->categorias->pluck('id')->toArray())); @endphp
                    <label class="cursor-pointer">
                        <input type="checkbox" name="categorias[]" value="{{ $cat->id }}"
                               {{ $checked ? 'checked' : '' }} class="sr-only peer">
                        <span class="inline-block text-sm font-medium px-4 py-1.5 rounded-full border transition-all"
                              style="{{ $checked
                                ? 'background: var(--color-primary-500); color: white; border-color: var(--color-primary-500);'
                                : 'background: white; color: #374151; border-color: #d1d5db;' }}"
                              id="chip-estcat-{{ $cat->id }}">
                            {{ $cat->nombre }}
                        </span>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- ── 4. Ubicación geográfica ─────────────────────────────────── --}}
        <div class="tc-card p-6">
            <h2 class="text-sm font-bold uppercase tracking-wide mb-4" style="color: var(--color-primary-700);">
                Ubicación geográfica
            </h2>
            <div class="rounded-xl mb-4 flex flex-col items-center justify-center gap-2"
                 style="height: 180px; background: linear-gradient(135deg, var(--color-amber-soft) 0%, #fde9b5 100%);">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--color-tertiary-500);">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <p class="text-xs text-gray-500">Ingresa las coordenadas para marcar la ubicación</p>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold mb-1.5 text-gray-600">Latitud</label>
                    <input type="number" name="latitud" value="{{ old('latitud', $establecimiento->latitud) }}"
                           step="0.0000001" min="-90" max="90" placeholder="-13.163141" class="tc-input">
                    @error('latitud')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5 text-gray-600">Longitud</label>
                    <input type="number" name="longitud" value="{{ old('longitud', $establecimiento->longitud) }}"
                           step="0.0000001" min="-180" max="180" placeholder="-72.544963" class="tc-input">
                    @error('longitud')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- ── 5. Horario semanal de atención ────────────────────────── --}}
        <div class="tc-card p-6">
            <h2 class="text-sm font-bold uppercase tracking-wide mb-1" style="color: var(--color-primary-700);">
                Horario semanal de atención
            </h2>
            <p class="text-xs text-gray-400 mb-4">Activa los días en que el establecimiento está disponible para visitar</p>

            @php
                $diasConf = ['lunes'=>'Lunes','martes'=>'Martes','miercoles'=>'Miércoles','jueves'=>'Jueves','viernes'=>'Viernes','sabado'=>'Sábado','domingo'=>'Domingo'];
                $horariosOld = old('horarios', $establecimiento->horarios);
            @endphp
            <div class="space-y-2">
                @foreach($diasConf as $k => $nombre)
                    @php
                        $hor    = $horariosOld[$k] ?? null;
                        $open   = is_array($hor) && isset($hor['abre']);
                        $abre   = $hor['abre']   ?? '09:00';
                        $cierra = $hor['cierra']  ?? '22:00';
                    @endphp
                    <div class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors"
                         id="est-row-{{ $k }}"
                         style="{{ $open ? 'background: var(--color-amber-soft);' : 'background: #f3f4f6;' }}">
                        <label class="tc-toggle shrink-0">
                            <input type="checkbox" id="est-tog-{{ $k }}" {{ $open ? 'checked' : '' }}
                                   onchange="toggleEstHorario('{{ $k }}', this.checked)">
                            <span class="tc-toggle-track"></span>
                            <span class="tc-toggle-thumb"></span>
                        </label>
                        <span class="w-24 text-sm font-semibold shrink-0"
                              style="color: {{ $open ? 'var(--color-tertiary-500)' : '#9ca3af' }};" id="est-label-{{ $k }}">
                            {{ $nombre }}
                        </span>
                        <div id="est-times-{{ $k }}" class="{{ $open ? 'flex' : 'hidden' }} items-center gap-3 flex-1">
                            <div class="flex items-center gap-1.5">
                                <span class="text-xs text-gray-500">Abre</span>
                                <input type="time" id="est-abre-{{ $k }}" name="horarios[{{ $k }}][abre]"
                                       value="{{ $abre }}" class="tc-input w-28 text-sm">
                            </div>
                            <span class="text-gray-300 text-sm">→</span>
                            <div class="flex items-center gap-1.5">
                                <span class="text-xs text-gray-500">Cierra</span>
                                <input type="time" id="est-cierra-{{ $k }}" name="horarios[{{ $k }}][cierra]"
                                       value="{{ $cierra }}" class="tc-input w-28 text-sm">
                            </div>
                        </div>
                        <span id="est-closed-{{ $k }}" class="{{ $open ? 'hidden' : '' }} text-xs text-gray-400 flex-1">Cerrado</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ── 6. Imágenes del establecimiento ──────────────────────── --}}
        <div class="tc-card p-6">
            <h2 class="text-sm font-bold uppercase tracking-wide mb-5" style="color: var(--color-primary-700);">
                Imágenes del establecimiento
            </h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold mb-1.5 text-gray-700">Imagen de portada</label>
                    <div id="est-preview-portada-area" class="rounded-xl overflow-hidden mb-2" style="background: var(--color-amber-soft); height: 130px; display: flex; align-items: center; justify-content: center;">
                        @if($establecimiento->imagen_portada)
                            <img src="{{ asset('storage/' . $establecimiento->imagen_portada) }}" class="w-full h-full object-cover" alt="Portada">
                        @else
                            <div class="text-center">
                                <svg class="w-8 h-8 mx-auto mb-1" style="color: var(--color-tertiary-400);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="text-xs" style="color: var(--color-tertiary-500);">Vista previa de portada</p>
                            </div>
                        @endif
                    </div>
                    <input type="file" name="imagen_portada" accept="image/jpg,image/jpeg,image/png,image/webp"
                           class="tc-input text-sm file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-amber-50 file:text-amber-700"
                           onchange="estPreviewPortada(this)">
                    <p class="text-xs text-gray-400 mt-1">PNG, JPG, WebP · Máx 4 MB · 1200×900px recomendado</p>
                    @error('imagen_portada')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5 text-gray-700">Galería de imágenes adicional <span class="text-red-500">*</span></label>
                    <input type="file" name="galeria[]" accept="image/jpg,image/jpeg,image/png,image/webp" multiple
                           class="tc-input text-sm file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-amber-50 file:text-amber-700">
                    <p class="text-xs text-gray-400 mt-1">Sube al menos 1 imagen si eliminas las actuales. Hasta 10 imágenes · PNG, JPG, WebP · Máx 4 MB c/u</p>
                    <div id="est-preview-galeria">
                        @if(isset($establecimiento) && $establecimiento->imagenes->count() > 0)
                            <div class="mt-3 flex gap-2 overflow-x-auto pb-2 scrollbar-hide" id="est-existing-gallery">
                                @foreach($establecimiento->imagenes as $img)
                                    <div class="shrink-0 w-24 h-24 rounded-lg overflow-hidden border border-gray-200 relative group" id="est-img-container-{{ $img->id }}" style="box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                        <img src="{{ asset('storage/' . $img->url) }}" class="w-full h-full object-cover">
                                        <button type="button" onclick="estEliminarImagen({{ $img->id }})" 
                                                class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity" title="Eliminar imagen">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <!-- Contenedor para IDs de imágenes a eliminar -->
                    <div id="est-eliminar-inputs"></div>
                </div>
            </div>
        </div>

        {{-- Botones --}}
        <div class="flex items-center justify-end gap-3 pb-4">
            <a href="{{ route('proveedor.establecimientos.index') }}?_proveedor_id_test={{ request('_proveedor_id_test', 2) }}"
               class="tc-btn-outline">Cancelar</a>
            <button type="submit" class="tc-btn-primary">
                Guardar Cambios
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
var tiposDisponibles = ['restaurante','hotel','transporte','agencia','otro'];
var rangosDisponibles = ['bajo','medio','alto','lujo'];

function selectTipo(val) {
    tiposDisponibles.forEach(function(t) {
        var btn = document.getElementById('tipo-btn-' + t);
        if (!btn) return;
        if (t === val) {
            btn.style.background   = 'var(--color-primary-500)';
            btn.style.color        = 'white';
            btn.style.borderColor  = 'var(--color-primary-500)';
        } else {
            btn.style.background   = 'white';
            btn.style.color        = '#374151';
            btn.style.borderColor  = '#d1d5db';
        }
    });
}

function selectRango(val) {
    rangosDisponibles.forEach(function(r) {
        var btn = document.getElementById('rango-btn-' + r);
        if (!btn) return;
        if (r === val) {
            btn.style.background   = 'var(--color-primary-500)';
            btn.style.color        = 'white';
            btn.style.borderColor  = 'var(--color-primary-500)';
        } else {
            btn.style.background   = 'white';
            btn.style.color        = '#374151';
            btn.style.borderColor  = '#d1d5db';
        }
    });
}

function toggleEstHorario(dia, open) {
    var row    = document.getElementById('est-row-' + dia);
    var label  = document.getElementById('est-label-' + dia);
    var times  = document.getElementById('est-times-' + dia);
    var closed = document.getElementById('est-closed-' + dia);
    var abre   = document.getElementById('est-abre-' + dia);
    var cierra = document.getElementById('est-cierra-' + dia);

    row.style.background = open ? 'var(--color-amber-soft)' : '#f3f4f6';
    label.style.color    = open ? 'var(--color-tertiary-500)' : '#9ca3af';

    if (open) {
        times.classList.remove('hidden'); times.classList.add('flex');
        closed.classList.add('hidden');
        abre.name   = 'horarios[' + dia + '][abre]';
        cierra.name = 'horarios[' + dia + '][cierra]';
    } else {
        times.classList.add('hidden'); times.classList.remove('flex');
        closed.classList.remove('hidden');
        abre.removeAttribute('name');
        cierra.removeAttribute('name');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar días cerrados
    ['lunes','martes','miercoles','jueves','viernes','sabado','domingo'].forEach(function(d) {
        var t = document.getElementById('est-tog-' + d);
        if (t && !t.checked) toggleEstHorario(d, false);
    });

    // Chips categoría
    document.querySelectorAll('input[type="checkbox"][name="categorias[]"]').forEach(function(cb) {
        cb.addEventListener('change', function() {
            var span = document.getElementById('chip-estcat-' + this.value);
            if (!span) return;
            if (this.checked) {
                span.style.background   = 'var(--color-primary-500)';
                span.style.color        = 'white';
                span.style.borderColor  = 'var(--color-primary-500)';
            } else {
                span.style.background   = 'white';
                span.style.color        = '#374151';
                span.style.borderColor  = '#d1d5db';
            }
        });
    });
});

// Preview de galería (múltiples imágenes)
document.querySelector('input[name="galeria[]"]').addEventListener('change', function(e) {
    var previewContainer = document.getElementById('est-preview-galeria');
    if (!previewContainer) {
        previewContainer = document.createElement('div');
        previewContainer.id = 'est-preview-galeria';
        previewContainer.className = 'mt-3 flex gap-2 overflow-x-auto pb-2 scrollbar-hide';
        this.parentNode.appendChild(previewContainer);
    }
    previewContainer.innerHTML = '';
    
    Array.from(e.target.files).slice(0, 10).forEach(function(file) {
        if (!file.type.match('image.*')) return;
        var reader = new FileReader();
        reader.onload = function(e) {
            var imgWrap = document.createElement('div');
            imgWrap.className = 'shrink-0 w-24 h-24 rounded-lg overflow-hidden border border-gray-200';
            imgWrap.style.boxShadow = '0 2px 4px rgba(0,0,0,0.05)';
            
            var img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'w-full h-full object-cover';
            
            imgWrap.appendChild(img);
            previewContainer.appendChild(imgWrap);
        };
        reader.readAsDataURL(file);
    });
});

// Preview de portada
function estPreviewPortada(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var previewArea = document.getElementById('est-preview-portada-area');
            if(previewArea) {
                previewArea.innerHTML = '<img src="' + e.target.result + '" class="w-full h-full object-cover" alt="Portada">';
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}
// Eliminar imagen de galería
function estEliminarImagen(id) {
    // Ocultar miniatura
    var container = document.getElementById('est-img-container-' + id);
    if(container) {
        container.style.display = 'none';
        container.classList.add('deleted-img');
    }
    // Agregar input oculto para backend
    var input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'eliminar_imagenes[]';
    input.value = id;
    document.getElementById('est-eliminar-inputs').appendChild(input);
}

// Validar en submit que haya al menos 1 imagen
document.querySelector('form').addEventListener('submit', function(e) {
    var existingImgs = document.querySelectorAll('#est-existing-gallery .group:not(.deleted-img)').length;
    var newImgs = document.querySelector('input[name="galeria[]"]').files.length;
    
    if (existingImgs + newImgs === 0) {
        e.preventDefault();
        alert('Debe mantener o subir al menos 1 imagen secundaria en la galería.');
    }
});
</script>
@endpush
