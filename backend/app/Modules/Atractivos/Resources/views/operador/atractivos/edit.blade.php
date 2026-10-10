@extends('layouts.panel')

@section('title', 'Editar atractivo')

@section('panel-rol-label', 'Operador')

@section('panel-sidebar-links')
    <a href="{{ route('operador.atractivos.index') }}?_operador_id_test={{ request('_operador_id_test', 1) }}"
       class="group/sidebar flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 mb-0.5 text-white shadow-sm"
       style="background: var(--color-primary-500);">
        <div class="shrink-0 transition-transform duration-150 group-hover/sidebar:scale-110">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
            </svg>
        </div>
        <span class="whitespace-pre group-hover/sidebar:translate-x-1 transition-transform duration-150">Mis Atractivos</span>
    </a>
@endsection

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Back + Header --}}
    <div class="mb-6">
        <a href="{{ route('operador.atractivos.index') }}?_operador_id_test={{ request('_operador_id_test', 1) }}"
           class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-gray-600 mb-3 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Mis Atractivos
        </a>
        <h1 style="font-family: var(--font-display); font-size: 1.625rem; font-weight: 800; color: #1a2232;">
            Editar atractivo
        </h1>
        <p class="text-sm text-gray-400 mt-0.5">Actualiza la información del atractivo turístico</p>
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

    <form method="POST" action="{{ route('operador.atractivos.update', $atractivo) }}"
          enctype="multipart/form-data" class="space-y-5">
        @csrf
        @method('PATCH')
        <input type="hidden" name="_operador_id_test" value="{{ request('_operador_id_test', 1) }}">

        {{-- ── 1. Información básica ──────────────────────────────────── --}}
        <div class="tc-card p-6">
            <h2 class="text-sm font-bold uppercase tracking-wide mb-5" style="color: var(--color-primary-700);">
                Información básica
            </h2>
            <div class="space-y-4">
                {{-- Nombre --}}
                <div>
                    <label class="block text-sm font-semibold mb-1.5 text-gray-700">Nombre del atractivo <span class="text-red-500">*</span></label>
                    <input type="text" name="nombre" value="{{ old('nombre', $atractivo->nombre) }}"
                           placeholder="Ej: Machu Picchu" class="tc-input" maxlength="150" required>
                    @error('nombre')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                {{-- Destino + Costo --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1.5 text-gray-700">Destino <span class="text-red-500">*</span></label>
                        <select name="destino_id" class="tc-select" required>
                            <option value="">Seleccionar un destino…</option>
                            @foreach($destinos as $d)
                                <option value="{{ $d->id }}" @selected(old('destino_id', $atractivo->destino_id) == $d->id)>{{ $d->nombre }}</option>
                            @endforeach
                        </select>
                        @error('destino_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1.5 text-gray-700">Costo de Entrada (S/)</label>
                        <input type="number" name="costo_entrada" value="{{ old('costo_entrada', $atractivo->costo_entrada) }}"
                               step="0.01" min="0" placeholder="S/ 0.00" class="tc-input">
                        @error('costo_entrada')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Duración + Estado --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1.5 text-gray-700">Duración estimada de visita</label>
                        <div class="flex gap-2">
                            <input type="number" name="duracion_estimada_min"
                                   value="{{ old('duracion_estimada_min', $atractivo->duracion_estimada_min) }}"
                                   placeholder="120" min="1" class="tc-input flex-1">
                            <span class="inline-flex items-center px-3 py-2 rounded-lg border text-sm text-gray-500"
                                  style="border-color: #d1d5db; background: #f9fafb;">min</span>
                        </div>
                        @error('duracion_estimada_min')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1.5 text-gray-700">Estado <span class="text-red-500">*</span></label>
                        <select name="estado" class="tc-select" required>
                            <option value="activo"   @selected(old('estado', $atractivo->estado) === 'activo')>Activo</option>
                            <option value="inactivo" @selected(old('estado') === 'inactivo')>Inactivo</option>
                        </select>
                    </div>
                </div>

                {{-- Descripción --}}
                <div>
                    <label class="block text-sm font-semibold mb-1.5 text-gray-700">Descripción</label>
                    <textarea name="descripcion" rows="4" placeholder="Describe el atractivo…" class="tc-input resize-none">{{ old('descripcion', $atractivo->descripcion) }}</textarea>
                </div>
            </div>
        </div>

        {{-- ── 2. Categoría de interés ────────────────────────────────── --}}
        <div class="tc-card p-6">
            <h2 class="text-sm font-bold uppercase tracking-wide mb-1" style="color: var(--color-primary-700);">
                Categoría de Interés
            </h2>
            <p class="text-xs text-gray-400 mb-4">Selecciona todas las que apliquen</p>
            <div class="flex flex-wrap gap-2">
                @foreach($categorias as $cat)
                    @php $checked = in_array($cat->id, old('categorias', $atractivo->categorias->pluck('id')->toArray())); @endphp
                    <label class="cursor-pointer">
                        <input type="checkbox" name="categorias[]" value="{{ $cat->id }}"
                               {{ $checked ? 'checked' : '' }} class="sr-only peer">
                        <span class="inline-block text-sm font-medium px-4 py-1.5 rounded-full border transition-all peer-checked:text-white"
                              style="{{ $checked
                                ? 'background: var(--color-primary-500); color: white; border-color: var(--color-primary-500);'
                                : 'background: white; color: #374151; border-color: #d1d5db;' }}"
                              id="chip-cat-{{ $cat->id }}">
                            {{ $cat->nombre }}
                        </span>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- ── 3. Ubicación geográfica ────────────────────────────────── --}}
        <div class="tc-card p-6">
            <h2 class="text-sm font-bold uppercase tracking-wide mb-4" style="color: var(--color-primary-700);">
                Ubicación geográfica
            </h2>
            {{-- Placeholder visual de mapa --}}
            <div class="rounded-xl mb-4 flex flex-col items-center justify-center gap-2"
                 style="height: 180px; background: linear-gradient(135deg, var(--color-teal-light) 0%, #c8e8eb 100%);">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--color-primary-500);">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <p class="text-xs text-gray-500">Ingresa las coordenadas para marcar la ubicación</p>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold mb-1.5 text-gray-600">Latitud</label>
                    <input type="number" name="latitud" value="{{ old('latitud', $atractivo->latitud) }}"
                           step="0.0000001" min="-90" max="90" placeholder="-13.163141" class="tc-input">
                    @error('latitud')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5 text-gray-600">Longitud</label>
                    <input type="number" name="longitud" value="{{ old('longitud', $atractivo->longitud) }}"
                           step="0.0000001" min="-180" max="180" placeholder="-72.544963" class="tc-input">
                    @error('longitud')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- ── 4. Horario semanal de atención ────────────────────────── --}}
        <div class="tc-card p-6">
            <h2 class="text-sm font-bold uppercase tracking-wide mb-1" style="color: var(--color-primary-700);">
                Horario semanal de atención
            </h2>
            <p class="text-xs text-gray-400 mb-4">Activa los días en que el atractivo está disponible para visitar</p>

            @php
                $diasConf = ['lunes'=>'Lunes','martes'=>'Martes','miercoles'=>'Miércoles','jueves'=>'Jueves','viernes'=>'Viernes','sabado'=>'Sábado','domingo'=>'Domingo'];
                $horariosOld = old('horarios', $atractivo->horarios);
            @endphp
            <div class="space-y-2">
                @foreach($diasConf as $k => $nombre)
                    @php
                        $hor    = $horariosOld[$k] ?? null;
                        $open   = is_array($hor) && isset($hor['abre']);
                        $abre   = $hor['abre']   ?? '09:00';
                        $cierra = $hor['cierra']  ?? '18:00';
                    @endphp
                    <div class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors"
                         id="row-{{ $k }}"
                         style="{{ $open ? 'background: var(--color-teal-light);' : 'background: #f3f4f6;' }}">
                        {{-- Toggle --}}
                        <label class="tc-toggle shrink-0">
                            <input type="checkbox" id="tog-{{ $k }}" {{ $open ? 'checked' : '' }}
                                   onchange="toggleHorario('{{ $k }}', this.checked)">
                            <span class="tc-toggle-track"></span>
                            <span class="tc-toggle-thumb"></span>
                        </label>

                        {{-- Nombre del día --}}
                        <span class="w-24 text-sm font-semibold shrink-0"
                              style="color: {{ $open ? 'var(--color-primary-700)' : '#9ca3af' }};" id="label-{{ $k }}">
                            {{ $nombre }}
                        </span>

                        {{-- Inputs de hora (visibles solo cuando abierto) --}}
                        <div id="times-{{ $k }}" class="{{ $open ? 'flex' : 'hidden' }} items-center gap-3 flex-1">
                            <div class="flex items-center gap-1.5">
                                <span class="text-xs text-gray-500">Abre</span>
                                <input type="time" id="abre-{{ $k }}" name="horarios[{{ $k }}][abre]"
                                       value="{{ $abre }}" class="tc-input w-28 text-sm">
                            </div>
                            <span class="text-gray-300 text-sm">→</span>
                            <div class="flex items-center gap-1.5">
                                <span class="text-xs text-gray-500">Cierra</span>
                                <input type="time" id="cierra-{{ $k }}" name="horarios[{{ $k }}][cierra]"
                                       value="{{ $cierra }}" class="tc-input w-28 text-sm">
                            </div>
                        </div>

                        {{-- Label cuando cerrado --}}
                        <span id="closed-{{ $k }}" class="{{ $open ? 'hidden' : '' }} text-xs text-gray-400 flex-1">
                            Cerrado
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ── 5. Imágenes del atractivo ─────────────────────────────── --}}
        <div class="tc-card p-6">
            <h2 class="text-sm font-bold uppercase tracking-wide mb-5" style="color: var(--color-primary-700);">
                Imágenes del atractivo
            </h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold mb-1.5 text-gray-700">Imagen de portada</label>
                    <div id="preview-portada-area" class="rounded-xl overflow-hidden mb-2" style="background: var(--color-teal-light); height: 130px; display: flex; align-items: center; justify-content: center;">
                        @if($atractivo->imagen_portada)
                            <img src="{{ asset('storage/' . $atractivo->imagen_portada) }}" class="w-full h-full object-cover" alt="Portada">
                        @else
                            <div class="text-center">
                                <svg class="w-8 h-8 mx-auto mb-1" style="color: var(--color-primary-400);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="text-xs" style="color: var(--color-primary-500);">Vista previa de portada</p>
                            </div>
                        @endif
                    </div>
                    <input type="file" name="imagen_portada" accept="image/jpeg,image/png,image/webp,image/jpg,.jpg,.jpeg,.png,.webp,.jfif"
                           class="tc-input text-sm file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700"
                           onchange="previewPortada(this)">
                    <p class="text-xs text-gray-400 mt-1">PNG, JPG, WebP · Máx 5 MB · 1200×900px recomendado</p>
                    @error('imagen_portada')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1.5 text-gray-700">Galería de imágenes adicional</label>
                    <input type="file" name="galeria[]" accept="image/jpeg,image/png,image/webp,image/jpg,.jpg,.jpeg,.png,.webp,.jfif" multiple
                           class="tc-input text-sm file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700">
                    <p class="text-xs text-gray-400 mt-1">Puedes agregar nuevas imágenes a la galería (PNG, JPG, WebP · Máx 5 MB c/u).</p>
                    
                    {{-- Contenedor de la galería completa --}}
                    <div class="mt-3 space-y-3">
                        {{-- Imágenes ya guardadas --}}
                        <div id="wrapper-existing-gallery" class="{{ (!isset($atractivo) || $atractivo->imagenes->count() === 0) ? 'hidden' : '' }}">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Imágenes guardadas</p>
                            <div class="flex gap-2.5 overflow-x-auto pb-2 scrollbar-hide" id="existing-gallery">
                                @if(isset($atractivo))
                                    @foreach($atractivo->imagenes as $img)
                                        <div class="shrink-0 w-24 h-24 rounded-lg overflow-hidden border border-gray-200 relative group transition-all" id="img-container-{{ $img->id }}" style="box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                            <img src="{{ asset('storage/' . $img->url) }}" class="w-full h-full object-cover">
                                            <button type="button" onclick="eliminarImagen({{ $img->id }})" 
                                                    class="absolute top-1 right-1 bg-red-500 hover:bg-red-600 text-white rounded-full p-1 opacity-90 group-hover:opacity-100 shadow transition-opacity" title="Eliminar imagen">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        {{-- Nuevas imágenes seleccionadas para agregar --}}
                        <div id="new-uploads-section" class="hidden">
                            <p class="text-xs font-semibold text-teal-700 uppercase tracking-wider mb-2">Nuevas imágenes por agregar</p>
                            <div class="flex gap-2.5 overflow-x-auto pb-2 scrollbar-hide" id="preview-nuevas-imagenes"></div>
                        </div>
                    </div>

                    <!-- Contenedor para IDs de imágenes a eliminar -->
                    <div id="eliminar-inputs"></div>
                    @error('galeria')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Botones --}}
        <div class="flex items-center justify-end gap-3 pb-4">
            <a href="{{ route('operador.atractivos.index') }}?_operador_id_test={{ request('_operador_id_test', 1) }}"
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
function toggleHorario(dia, open) {
    var row    = document.getElementById('row-' + dia);
    var label  = document.getElementById('label-' + dia);
    var times  = document.getElementById('times-' + dia);
    var closed = document.getElementById('closed-' + dia);
    var abre   = document.getElementById('abre-' + dia);
    var cierra = document.getElementById('cierra-' + dia);

    row.style.background = open ? 'var(--color-teal-light)' : '#f3f4f6';
    label.style.color    = open ? 'var(--color-primary-700)' : '#9ca3af';

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

// Preview de portada
function previewPortada(input) {
    // Se podría añadir preview aquí si se desea en el futuro
}

// Inicializar: asegurarse de que los días cerrados no envíen sus inputs
document.addEventListener('DOMContentLoaded', function() {
    ['lunes','martes','miercoles','jueves','viernes','sabado','domingo'].forEach(function(d) {
        var t = document.getElementById('tog-' + d);
        if (t && !t.checked) toggleHorario(d, false);
    });

    // Efecto visual de chips de categoría
    document.querySelectorAll('input[type="checkbox"][name="categorias[]"]').forEach(function(cb) {
        cb.addEventListener('change', function() {
            var span = document.getElementById('chip-cat-' + this.value);
            if (!span) return;
            if (this.checked) {
                span.style.background = 'var(--color-primary-500)';
                span.style.color = 'white';
                span.style.borderColor = 'var(--color-primary-500)';
            } else {
                span.style.background = 'white';
                span.style.color = '#374151';
                span.style.borderColor = '#d1d5db';
            }
        });
    });
});

// Preview de nuevas imágenes adicionales (se agregan visualmente sin borrar las guardadas)
var inputGaleria = document.querySelector('input[name="galeria[]"]');
if (inputGaleria) {
    inputGaleria.addEventListener('change', function(e) {
        var section = document.getElementById('new-uploads-section');
        var previewContainer = document.getElementById('preview-nuevas-imagenes');
        if (!previewContainer || !section) return;

        previewContainer.innerHTML = '';
        var files = Array.from(e.target.files).slice(0, 10);

        if (files.length === 0) {
            section.classList.add('hidden');
            return;
        }

        section.classList.remove('hidden');

        files.forEach(function(file) {
            if (!file.type.match('image.*')) return;
            var reader = new FileReader();
            reader.onload = function(evt) {
                var imgWrap = document.createElement('div');
                imgWrap.className = 'shrink-0 w-24 h-24 rounded-lg overflow-hidden border-2 border-dashed border-teal-500 relative';
                imgWrap.style.boxShadow = '0 2px 4px rgba(0,0,0,0.05)';
                
                var img = document.createElement('img');
                img.src = evt.target.result;
                img.className = 'w-full h-full object-cover';
                
                var badge = document.createElement('span');
                badge.className = 'absolute bottom-1 left-1 bg-teal-600 text-white text-[10px] font-bold px-1.5 py-0.5 rounded shadow';
                badge.textContent = 'Nueva';

                imgWrap.appendChild(img);
                imgWrap.appendChild(badge);
                previewContainer.appendChild(imgWrap);
            };
            reader.readAsDataURL(file);
        });
    });
}

// Preview de portada
function previewPortada(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var previewArea = document.getElementById('preview-portada-area');
            if(previewArea) {
                previewArea.innerHTML = '<img src="' + e.target.result + '" class="w-full h-full object-cover" alt="Portada">';
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Eliminar imagen de galería (vía AJAX inmediato con respaldo de input oculto para submit)
function eliminarImagen(id) {
    if (!confirm('¿Estás seguro de que deseas eliminar esta imagen de la galería?')) {
        return;
    }

    var container = document.getElementById('img-container-' + id);
    if (container) {
        container.style.opacity = '0.3';
        container.style.pointerEvents = 'none';
    }

    // Input de respaldo para que se envíe en el submit si la petición AJAX no se completa
    var input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'eliminar_imagenes[]';
    input.value = id;
    document.getElementById('eliminar-inputs').appendChild(input);

    var urlDelete = '{{ url("operador/atractivos/" . $atractivo->id . "/imagenes") }}/' + id + '?_operador_id_test={{ request("_operador_id_test", 1) }}';
    fetch(urlDelete, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(function(res) {
        if (res.ok) {
            if (container) {
                container.remove();
                var remaining = document.querySelectorAll('#existing-gallery [id^="img-container-"]').length;
                if (remaining === 0) {
                    var wrapper = document.getElementById('wrapper-existing-gallery');
                    if (wrapper) wrapper.classList.add('hidden');
                }
            }
        } else {
            if (container) {
                container.style.display = 'none';
                container.classList.add('deleted-img');
            }
        }
    })
    .catch(function() {
        if (container) {
            container.style.display = 'none';
            container.classList.add('deleted-img');
        }
    });
}
</script>
@endpush
