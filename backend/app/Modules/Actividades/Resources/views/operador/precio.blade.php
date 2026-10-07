<fieldset class="border rounded-xl p-5 space-y-4">
    <legend class="font-bold px-2">Precio y evidencia</legend>
    <div class="grid sm:grid-cols-2 gap-4">
        <label class="block text-sm">Tipo de precio
            <select name="tipo_precio" class="tc-select mt-1" data-price-type>
                @foreach(['variable' => 'Por confirmar / consultar', 'fijo' => 'Tarifa publicada (0 solo si el acceso es libre)', 'estimado' => 'Precio de referencia publicado'] as $valor => $texto)
                    <option value="{{ $valor }}" @selected(old('tipo_precio', $registro?->tipo_precio ?? 'variable') === $valor)>{{ $texto }}</option>
                @endforeach
            </select>
        </label>
        <label class="block text-sm">Importe publicado en soles
            <input type="number" name="{{ $campoPrecio }}" value="{{ old($campoPrecio, $registro?->$campoPrecio) }}" min="0" step="0.01" class="tc-input mt-1" data-price-field>
        </label>
        <label class="block text-sm">Fuente de la tarifa
            <input type="url" name="precio_fuente_url" value="{{ old('precio_fuente_url', $registro?->precio_fuente_url) }}" class="tc-input mt-1" data-price-field>
        </label>
        <label class="block text-sm">Fecha de comprobación de la tarifa
            <input type="date" name="precio_verificado_en" value="{{ old('precio_verificado_en', $registro?->precio_verificado_en?->format('Y-m-d')) }}" max="{{ now()->toDateString() }}" class="tc-input mt-1" data-price-field>
        </label>
    </div>
    <p class="text-xs text-gray-600">Dejar un precio vacío significa desconocido. Un cupo máximo no representa disponibilidad en tiempo real.</p>
</fieldset>
@push('scripts')
<script>
    const priceType = document.querySelector('[data-price-type]');
    const togglePrice = () => document.querySelectorAll('[data-price-field]').forEach(input => {
        input.disabled = priceType.value === 'variable';
    });
    priceType.addEventListener('change', togglePrice);
    togglePrice();
</script>
@endpush
