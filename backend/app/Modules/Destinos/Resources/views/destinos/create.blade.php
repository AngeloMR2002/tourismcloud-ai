@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('admin.destinos.index') }}" class="p-2 bg-white border border-gray-200 rounded-lg text-gray-500 hover:text-[#00626A] hover:bg-gray-50 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Registrar Nuevo Destino</h1>
            <p class="text-sm text-gray-500">Busca la ciudad y los datos se completarán automáticamente con IA.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
        <form action="{{ route('admin.destinos.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- Buscador Nativo (Tailwind Puro) -->
                <div class="md:col-span-2 relative">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Buscar Destino (Ciudad / Región) <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" id="buscador_nativo" placeholder="Escribe el nombre de la ciudad (Ej: Cusco)" autocomplete="off"
                            class="w-full pl-10 px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#00626A]/20 focus:border-[#00626A] transition-colors shadow-sm">
                    </div>
                    
                    <!-- Lista de sugerencias (oculta por defecto) -->
                    <ul id="sugerencias_lista" class="absolute z-50 w-full bg-white border border-gray-200 rounded-lg shadow-lg mt-1 hidden max-h-60 overflow-y-auto"></ul>
                    
                    <!-- Input real para Laravel -->
                    <input type="hidden" id="nombre_input" name="nombre" value="{{ old('nombre') }}" required>
                    @error('nombre') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- País -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">País <span class="text-red-500">*</span></label>
                    <input type="text" id="pais_input" name="pais" value="{{ old('pais') }}" required readonly
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-500 focus:outline-none cursor-not-allowed placeholder-gray-300" placeholder="Autocompletado">
                </div>

                <!-- Región / Departamento -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Región / Departamento</label>
                    <input type="text" id="region_input" name="region" value="{{ old('region') }}" readonly
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-500 focus:outline-none cursor-not-allowed placeholder-gray-300" placeholder="Autocompletado">
                </div>

                <!-- Ciudad -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ciudad</label>
                    <input type="text" id="ciudad_input" name="ciudad" value="{{ old('ciudad') }}" readonly
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-500 focus:outline-none cursor-not-allowed placeholder-gray-300" placeholder="Autocompletado">
                </div>

                <!-- Operador Turístico -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Operador Turístico Asignado</label>
                    <select name="operador_id" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#00626A]/20 focus:border-[#00626A] bg-white transition-colors">
                        <option value="">-- Sin asignar --</option>
                        <?php foreach($operadores as$ope): ?>
                            <option value="{{ $ope->id }}" {{ old('operador_id') == $ope->id ? 'selected' : '' }}>
                                {{ $ope->nombre }} {{$ope->apellido }}
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Coordenadas -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Latitud</label>
                    <input type="number" step="any" id="lat_input" name="latitud" value="{{ old('latitud') }}" readonly
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-500 focus:outline-none cursor-not-allowed placeholder-gray-300" placeholder="0.000000">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Longitud</label>
                    <input type="number" step="any" id="lng_input" name="longitud" value="{{ old('longitud') }}" readonly
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-500 focus:outline-none cursor-not-allowed placeholder-gray-300" placeholder="0.000000">
                </div>

                <!-- Estado -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select name="estado" class="w-full md:w-1/2 px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#00626A]/20 focus:border-[#00626A] bg-white transition-colors">
                        <option value="activo" {{ old('estado') == 'activo' ? 'selected' : '' }}>Activo (Visible)</option>
                        <option value="inactivo" {{ old('estado') == 'inactivo' ? 'selected' : '' }}>Inactivo (Oculto)</option>
                    </select>
                </div>

                <!-- Descripción -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <textarea name="descripcion" rows="3" placeholder="Añade una descripción..."
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#00626A]/20 focus:border-[#00626A] transition-colors">{{ old('descripcion') }}</textarea>
                </div>
            </div>

            <div class="pt-6 border-t border-gray-100 flex justify-end gap-3">
                <a href="{{ route('admin.destinos.index') }}" class="px-5 py-2.5 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">Cancelar</a>
                <button type="submit" class="px-5 py-2.5 bg-[#00626A] rounded-lg text-sm font-medium text-white hover:bg-[#004e55] shadow-sm transition-colors">Guardar Destino</button>
            </div>
        </form>
    </div>
</div>
@endsection

@stack('scripts')
<!-- Cargador Oficial Moderno (Sin errores de "not async") -->
<script>
  (g=>{var h,a,k,p="The Google Maps JavaScript API",c="google",l="importLibrary",q="__ib__",m=document,b=window;b=b[c]||(b[c]={});var d=b.maps||(b.maps={}),r=new Set,e=new URLSearchParams,u=()=>h||(h=new Promise(async(f,n)=>{await (a=m.createElement("script"));e.set("libraries",[...r]+"");for(k in g)e.set(k.replace(/[A-Z]/g,t=>"_"+t[0].toLowerCase()),g[k]);e.set("callback",c+".maps."+q);a.src=`https://maps.${c}apis.com/maps/api/js?`+e;d[q]=f;a.onerror=()=>h=n(Error(p+" could not load."));a.nonce=m.querySelector("script[nonce]")?.nonce||"";m.head.append(a)}));d[l]?console.warn(p+" only loads once. Ignoring:",g):d[l]=(f,...n)=>r.add(f)&&u().then(()=>d[l](f,...n))})({
    key: "{{ env('GOOGLE_MAPS_API_KEY') }}",
    v: "weekly"
  });
</script>

<script>
    async function initModernAutocomplete() {
        try {
            // Importar exactamente las clases permitidas para cuentas de 2025
            const { Place, AutocompleteSuggestion } = await google.maps.importLibrary("places");

            const inputEl = document.getElementById('buscador_nativo');
            const listEl = document.getElementById('sugerencias_lista');

            // Detectar escritura en el input
            inputEl.addEventListener('input', async function() {
                const query = this.value;
                
                if (query.length < 3) {
                    listEl.innerHTML = '';
                    listEl.classList.add('hidden');
                    return;
                }

                try {
                    // LLamada a la NUEVA API de sugerencias
                    const response = await AutocompleteSuggestion.fetchAutocompleteSuggestions({
                        input: query
                    });

                    const suggestions = response.suggestions;

                    if (!suggestions || suggestions.length === 0) {
                        listEl.classList.add('hidden');
                        return;
                    }

                    listEl.innerHTML = '';
                    
                    // Renderizar nuestra propia lista (Tailwind)
                    suggestions.forEach(suggestion => {
                        // El texto que devuelve Google (Ej: "Cusco, Perú")
                        const textoLugar = suggestion.placePrediction.text.text;
                        const placeId = suggestion.placePrediction.placeId;

                        const li = document.createElement('li');
                        li.className = 'px-4 py-3 hover:bg-gray-50 cursor-pointer text-sm text-gray-700 border-b border-gray-100 last:border-0 flex items-center gap-2';
                        li.innerHTML = `<svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        ${textoLugar}`;
                        
                        // Acción al hacer clic en una sugerencia
                        li.addEventListener('click', async function() {
                            inputEl.value = textoLugar;
                            listEl.classList.add('hidden');
                            
                            // 1. Crear el nuevo objeto Place (Regla de la API 2025)
                            const place = new Place({ id: placeId });
                            
                            // 2. Pedir los detalles exactos que necesitamos
                            await place.fetchFields({
                                fields: ['displayName', 'location', 'addressComponents']
                            });

                            document.getElementById('nombre_input').value = place.displayName;
                            
                            if (place.location) {
                                document.getElementById('lat_input').value = place.location.lat();
                                document.getElementById('lng_input').value = place.location.lng();
                            }

                            let country = '', adminArea = '', locality = '';
                            
                            if (place.addressComponents) {
                                place.addressComponents.forEach(component => {
                                    if (component.types.includes('country')) country = component.longText;
                                    else if (component.types.includes('administrative_area_level_1')) adminArea = component.longText;
                                    else if (component.types.includes('locality')) locality = component.longText;
                                });
                            }

                            document.getElementById('pais_input').value = country;
                            document.getElementById('region_input').value = adminArea;
                            document.getElementById('ciudad_input').value = locality || adminArea;
                        });
                        
                        listEl.appendChild(li);
                    });
                    
                    listEl.classList.remove('hidden');

                } catch (e) {
                    console.error("Error consultando la API:", e);
                }
            });

            // Ocultar lista al hacer clic afuera
            document.addEventListener('click', function(e) {
                if (e.target !== inputEl && e.target !== listEl) {
                    listEl.classList.add('hidden');
                }
            });

        } catch (error) {
            console.error("Error inicializando Google Maps:", error);
        }
    }

    document.getElementById('buscador_nativo')?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') e.preventDefault();
    });

    // Iniciar cuando el DOM esté listo
    document.addEventListener("DOMContentLoaded", initModernAutocomplete);
</script>