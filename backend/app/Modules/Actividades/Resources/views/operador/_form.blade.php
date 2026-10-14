@php
    $portadaUrl = $actividad?->portada_url;
@endphp

@if($errors->any())
    <div class="tc-alert-error" role="alert">
        Revisa los campos marcados antes de guardar la actividad.
    </div>
@endif

<section class="tc-card p-5 sm:p-6">
    <h2 class="text-base font-bold" style="color: var(--color-primary-700);">Información principal</h2>
    <div class="grid grid-cols-1 gap-5 mt-5 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <label for="destino_id" class="block text-sm font-semibold text-gray-700 mb-1.5">Destino <span class="text-red-600">*</span></label>
            <select id="destino_id" name="destino_id" class="tc-select @error('destino_id') border-red-400 @enderror" required>
                <option value="">Selecciona un destino</option>
                @foreach($destinos as $destino)
                    <option value="{{ $destino->id }}" @selected((string) old('destino_id', $actividad?->destino_id) === (string) $destino->id)>
                        {{ $destino->nombre }}{{ $destino->estado !== 'activo' ? ' (inactivo; solo se puede conservar)' : '' }}
                    </option>
                @endforeach
            </select>
            @error('destino_id')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="sm:col-span-2">
            <label for="nombre" class="block text-sm font-semibold text-gray-700 mb-1.5">Nombre de la actividad <span class="text-red-600">*</span></label>
            <input id="nombre" name="nombre" value="{{ old('nombre', $actividad?->nombre) }}" maxlength="150" class="tc-input @error('nombre') border-red-400 @enderror" required>
            @error('nombre')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="sm:col-span-2">
            <label for="descripcion" class="block text-sm font-semibold text-gray-700 mb-1.5">Descripción</label>
            <textarea id="descripcion" name="descripcion" rows="5" maxlength="5000" class="tc-input @error('descripcion') border-red-400 @enderror" placeholder="Explica qué incluye la experiencia y qué debe considerar el turista.">{{ old('descripcion', $actividad?->descripcion) }}</textarea>
            @error('descripcion')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="costo" class="block text-sm font-semibold text-gray-700 mb-1.5">Costo estimado (S/) <span class="text-red-600">*</span></label>
            <input id="costo" type="number" name="costo" min="0" max="99999999.99" step="0.01" value="{{ old('costo', $actividad?->costo ?? '0.00') }}" class="tc-input @error('costo') border-red-400 @enderror" required>
            <p class="mt-1 text-xs text-gray-400">Usa 0 si es gratuita.</p>
            @error('costo')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="duracion_estimada_min" class="block text-sm font-semibold text-gray-700 mb-1.5">Duración estimada (minutos)</label>
            <input id="duracion_estimada_min" type="number" name="duracion_estimada_min" min="1" max="10080" value="{{ old('duracion_estimada_min', $actividad?->duracion_estimada_min) }}" class="tc-input @error('duracion_estimada_min') border-red-400 @enderror" placeholder="Ej. 120">
            @error('duracion_estimada_min')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="latitud" class="block text-sm font-semibold text-gray-700 mb-1.5">Latitud</label>
            <input id="latitud" type="number" name="latitud" step="0.0000001" min="-90" max="90" value="{{ old('latitud', $actividad?->latitud) }}" class="tc-input @error('latitud') border-red-400 @enderror" placeholder="Ej. -9.085">
            @error('latitud')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
            <p class="mt-1 text-xs text-gray-400">Regístrala junto con la longitud para habilitar el mapa.</p>
        </div>

        <div>
            <label for="longitud" class="block text-sm font-semibold text-gray-700 mb-1.5">Longitud</label>
            <input id="longitud" type="number" name="longitud" step="0.0000001" min="-180" max="180" value="{{ old('longitud', $actividad?->longitud) }}" class="tc-input @error('longitud') border-red-400 @enderror" placeholder="Ej. -78.565">
            @error('longitud')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="estado" class="block text-sm font-semibold text-gray-700 mb-1.5">Estado <span class="text-red-600">*</span></label>
            <select id="estado" name="estado" class="tc-select @error('estado') border-red-400 @enderror" required>
                <option value="activo" @selected(old('estado', $actividad?->estado ?? 'activo') === 'activo')>Activo</option>
                <option value="inactivo" @selected(old('estado', $actividad?->estado) === 'inactivo')>Inactivo</option>
            </select>
            @error('estado')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>
</section>

<section class="tc-card p-5 sm:p-6">
    <h2 class="text-base font-bold" style="color: var(--color-primary-700);">Imagen de portada</h2>
    <p class="mt-1 text-sm text-gray-500">Una imagen ayuda a los turistas a reconocer la actividad.</p>

    <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-[13rem_minmax(0,1fr)] sm:items-start">
        <div class="rounded-xl overflow-hidden aspect-[4/3] flex items-center justify-center" style="background: var(--color-teal-light); color: var(--color-primary-500);">
            @if($portadaUrl)
                <img id="portada-preview" src="{{ $portadaUrl }}" alt="Portada actual" class="w-full h-full object-cover">
            @else
                <img id="portada-preview" src="" alt="Vista previa de portada" class="w-full h-full object-cover hidden">
                <span id="portada-placeholder" class="text-sm text-center px-4">Sin imagen de portada</span>
            @endif
        </div>
        <div>
            <label for="imagen_portada" class="block text-sm font-semibold text-gray-700 mb-1.5">Seleccionar imagen</label>
            <input id="imagen_portada" type="file" name="imagen_portada" accept="image/jpeg,image/png,image/webp" class="tc-input @error('imagen_portada') border-red-400 @enderror">
            <p class="mt-1 text-xs text-gray-400">JPG, PNG o WebP. Tamaño máximo: 5 MB.</p>
            @error('imagen_portada')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror

            @if($portadaUrl)
                <label class="inline-flex items-center gap-2 mt-4 text-sm text-gray-600 cursor-pointer">
                    <input type="hidden" name="quitar_portada" value="0">
                    <input type="checkbox" name="quitar_portada" value="1" @checked(old('quitar_portada')) class="rounded border-gray-300" style="accent-color: var(--color-primary-500);">
                    Quitar la imagen actual
                </label>
            @endif
        </div>
    </div>
</section>

<div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end pb-4">
    <a href="{{ route('operador.actividades.index') }}" class="tc-btn-outline justify-center">Cancelar</a>
    <button type="submit" class="tc-btn-primary justify-center">{{ $submitLabel }}</button>
</div>

@push('scripts')
    <script>
        document.getElementById('imagen_portada')?.addEventListener('change', function (event) {
            const file = event.target.files?.[0];

            if (!file) {
                return;
            }

            const preview = document.getElementById('portada-preview');
            const placeholder = document.getElementById('portada-placeholder');
            preview.src = URL.createObjectURL(file);
            preview.classList.remove('hidden');
            placeholder?.classList.add('hidden');
        });
    </script>
@endpush
