@php($registro = $actividad ?? $ruta ?? null)
<div class="border-t pt-6 space-y-4">
    <h2 class="text-lg font-bold">Fuentes y publicación</h2>
    <p class="text-sm text-gray-600">Los datos se publican después de la revisión del administrador. Deja vacíos los datos que no conoces.</p>
    <div class="grid sm:grid-cols-2 gap-4">
        @foreach(['fuente_url' => 'Fuente del lugar o actividad *', 'sitio_web_oficial' => 'Web oficial del responsable', 'url_reserva_oficial' => 'Enlace oficial para consultar o reservar', 'imagen_portada' => 'URL de una fotografía real', 'imagen_fuente_url' => 'Página de origen de la fotografía'] as $campo => $etiqueta)
            <label class="block text-sm">{{ $etiqueta }}
                <input type="url" name="{{ $campo }}" value="{{ old($campo, $registro?->$campo) }}" @required($campo === 'fuente_url') maxlength="2048" class="tc-input mt-1">
            </label>
        @endforeach
        @foreach(['imagen_autor' => 'Autor de la fotografía', 'imagen_licencia' => 'Licencia o autorización de uso'] as $campo => $etiqueta)
            <label class="block text-sm">{{ $etiqueta }}
                <input type="text" name="{{ $campo }}" value="{{ old($campo, $registro?->$campo) }}" maxlength="{{ $campo === 'imagen_autor' ? 150 : 100 }}" class="tc-input mt-1">
            </label>
        @endforeach
        <label class="block text-sm">Estado
            <select name="estado" class="tc-select mt-1">
                <option value="activo" @selected(old('estado', $registro?->estado ?? 'activo') === 'activo')>Activo (visible después de aprobación)</option>
                <option value="inactivo" @selected(old('estado', $registro?->estado) === 'inactivo')>Inactivo / borrador</option>
            </select>
        </label>
    </div>
    <p class="text-xs text-gray-500">Una fotografía necesita fuente, autor y licencia. Las tarifas necesitan una fuente y una comprobación de los últimos siete días. La disponibilidad se confirma en la web del responsable.</p>
</div>
