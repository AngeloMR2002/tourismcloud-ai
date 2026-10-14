@php
    $esEdicion = isset($ruta);
    $destinoFormulario = old('destino_id', $esEdicion ? $ruta->destino_id : ($destinoSeleccionado ?? ''));
    $paradasIniciales = old('paradas', $esEdicion ? $ruta->paradas->pluck('atractivo_id')->all() : []);
    $accion = $esEdicion ? route('operador.rutas.update', $ruta) : route('operador.rutas.store');
    $googleMapsApiKey = trim((string) config('services.google_maps.key', ''));
@endphp

<form method="POST" action="{{ $accion }}" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if($esEdicion)
        @method('PUT')
    @endif
    @if($errors->any())
        <div class="tc-alert-error" role="alert">
            <ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif
    @if($destinos->isEmpty())
        <div class="tc-alert-error" role="alert">No tienes destinos activos asignados. Solicita la asignación de un destino antes de crear una ruta.</div>
    @endif

    <section class="tc-card tc-form-section p-5 sm:p-6">
        <div class="tc-form-section__heading">
            <div>
                <p class="tc-form-section__eyebrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M5 5h7a4 4 0 0 1 0 8H9a3 3 0 0 0 0 6h10" stroke-width="1.7" stroke-linecap="round"/><circle cx="5" cy="5" r="1.5" stroke-width="1.7"/><circle cx="19" cy="19" r="1.5" stroke-width="1.7"/></svg>Ruta</p>
                <h2>Información del recorrido</h2>
                <p>Define el destino, la distancia y una duración referencial antes de organizar las paradas.</p>
            </div>
            <span class="tc-step-badge">Paso 1 de 4</span>
        </div>

        <div class="tc-form-grid tc-form-grid--two">
            <div class="tc-form-field">
                <label for="destino_id" class="mb-1.5 block text-sm font-semibold text-slate-700">Destino <span class="text-red-500">*</span></label>
                <select id="destino_id" name="destino_id" class="tc-select w-full" required>
                    <option value="">Selecciona un destino</option>
                    @foreach($destinos as $destino)
                        @php
                            $esDestinoActual = $esEdicion && (int) $ruta->destino_id === (int) $destino->id;
                            $esInactivoNoActual = $destino->estado !== 'activo' && ! $esDestinoActual;
                        @endphp
                        <option value="{{ $destino->id }}"
                            @selected((string) $destinoFormulario === (string) $destino->id)
                            @disabled($esInactivoNoActual)>
                            {{ $destino->nombre }}@if($destino->estado !== 'activo') (inactivo)@endif
                        </option>
                    @endforeach
                </select>
                @error('destino_id')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="tc-form-field">
                <label for="estado" class="mb-1.5 block text-sm font-semibold text-slate-700">Estado <span class="text-red-500">*</span></label>
                <select id="estado" name="estado" class="tc-select w-full" required>
                    <option value="activo" @selected(old('estado', $esEdicion ? $ruta->estado : 'activo') === 'activo')>Activo</option>
                    <option value="inactivo" @selected(old('estado', $esEdicion ? $ruta->estado : 'activo') === 'inactivo')>Inactivo</option>
                </select>
                @error('estado')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="tc-form-field tc-form-field--wide">
                <label for="nombre" class="mb-1.5 block text-sm font-semibold text-slate-700">Nombre de la ruta <span class="text-red-500">*</span></label>
                <input id="nombre" name="nombre" type="text" maxlength="150" class="tc-input w-full"
                    value="{{ old('nombre', $esEdicion ? $ruta->nombre : '') }}" placeholder="Ej. Ruta histórica por el centro" required>
                @error('nombre')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="tc-form-field tc-form-field--wide">
                <label for="descripcion" class="mb-1.5 block text-sm font-semibold text-slate-700">Descripción</label>
                <textarea id="descripcion" name="descripcion" rows="4" maxlength="5000" class="tc-input w-full resize-y"
                    placeholder="Describe la experiencia y lo que el visitante encontrará en el recorrido.">{{ old('descripcion', $esEdicion ? $ruta->descripcion : '') }}</textarea>
                @error('descripcion')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="tc-form-field">
                <label for="distancia_km" class="mb-1.5 block text-sm font-semibold text-slate-700">Distancia estimada (km)</label>
                <input id="distancia_km" name="distancia_km" type="number" min="0" step="0.01" class="tc-input w-full"
                    value="{{ old('distancia_km', $esEdicion ? $ruta->distancia_km : '') }}" placeholder="Ej. 8.50">
                @error('distancia_km')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="tc-form-field">
                <label for="duracion_estimada_min" class="mb-1.5 block text-sm font-semibold text-slate-700">Duración estimada (minutos)</label>
                <input id="duracion_estimada_min" name="duracion_estimada_min" type="number" min="1" max="10080" class="tc-input w-full"
                    value="{{ old('duracion_estimada_min', $esEdicion ? $ruta->duracion_estimada_min : '') }}" placeholder="Ej. 180">
                @error('duracion_estimada_min')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>
    </section>

    <section class="tc-card tc-form-section p-5 sm:p-6">
        <div class="tc-form-section__heading">
            <div>
                <p class="tc-form-section__eyebrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M6 4h12v5H6zM6 15h12v5H6zM12 9v6" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>Paradas</p>
                <h2>Paradas del recorrido <span class="text-red-500">*</span></h2>
                <p>Agrega al menos dos atractivos activos del destino elegido. Usa Subir y Bajar para definir su orden.</p>
            </div>
            <span class="tc-step-badge tc-step-badge--warm">Paso 2 de 4</span>
        </div>

        <div class="mt-5 grid gap-5 lg:grid-cols-2">
            <div class="tc-route-stop-panel rounded-2xl border p-4">
                <div class="flex items-center justify-between gap-3">
                    <h3 class="font-semibold text-slate-800">Atractivos disponibles</h3>
                    <span id="contador-disponibles" class="text-xs font-medium text-slate-500"></span>
                </div>
                <p class="mt-1 text-xs leading-5 text-slate-500">Solo se muestran atractivos activos de los destinos que gestionas.</p>
                <div id="atractivos-disponibles" class="mt-3 max-h-80 space-y-2 overflow-y-auto pr-1" aria-live="polite"></div>
            </div>

            <div class="tc-route-stop-panel tc-route-stop-panel--selected rounded-2xl border p-4">
                <div class="flex items-center justify-between gap-3">
                    <h3 class="font-semibold text-slate-800">Orden de la ruta</h3>
                    <span id="contador-paradas" class="rounded-full bg-white px-2.5 py-1 text-xs font-semibold text-primary-700 shadow-sm">0 paradas</span>
                </div>
                <p class="mt-1 text-xs leading-5 text-slate-500">El primer atractivo será la primera parada del recorrido.</p>
                <div id="paradas-seleccionadas" class="mt-3 space-y-2" aria-live="polite"></div>
            </div>
        </div>
        @error('paradas')<p class="mt-3 text-sm font-medium text-red-600">{{ $message }}</p>@enderror
        <noscript><p class="mt-3 text-sm text-red-700">Activa JavaScript para seleccionar y ordenar las paradas.</p></noscript>
    </section>

    @include('rutas::partials.mapa-paradas', [
        'mapId' => 'mapa-ruta-formulario',
        'mapEyebrow' => 'Vista previa geográfica',
        'mapTitle' => 'Recorrido según tus paradas',
        'mapDescription' => 'El mapa toma las coordenadas de los atractivos seleccionados y mantiene el orden que definas arriba.',
        'mapLabel' => 'Mapa de previsualización de las paradas de la ruta',
    ])

    <section class="tc-card tc-form-section p-5 sm:p-6">
        <div class="tc-form-section__heading">
            <div>
                <p class="tc-form-section__eyebrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><rect x="4" y="5" width="16" height="14" rx="2" stroke-width="1.7"/><circle cx="9" cy="10" r="1.4" stroke-width="1.7"/><path d="m6.5 17 4.2-4.2 2.8 2.7 2-2 2 3.5" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>Portada</p>
                <h2>Imagen principal</h2>
                <p>Opcional. Formatos JPG, PNG o WebP; máximo 5 MB.</p>
            </div>
            <span class="tc-step-badge tc-step-badge--warm">Paso 4 de 4</span>
        </div>

        <div class="tc-form-cover">
            <div class="tc-form-cover__preview">
                @if($esEdicion && $ruta->portada_url)
                    <img id="ruta-portada-preview" src="{{ $ruta->portada_url }}" alt="Portada actual de {{ $ruta->nombre }}" class="h-full w-full object-cover">
                @else
                    <img id="ruta-portada-preview" src="" alt="Vista previa de portada" class="h-full w-full object-cover hidden">
                    <span id="ruta-portada-placeholder">Sin portada</span>
                @endif
            </div>
            <div class="tc-form-field space-y-3">
                <label for="imagen_portada" class="block text-sm font-semibold text-slate-700">Seleccionar imagen</label>
                <input id="imagen_portada" name="imagen_portada" type="file" accept="image/jpeg,image/png,image/webp" class="tc-input w-full">
                @error('imagen_portada')<p class="text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                @if($esEdicion && $ruta->imagen_portada)
                    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                        <input type="hidden" name="quitar_portada" value="0">
                        <input type="checkbox" name="quitar_portada" value="1" @checked(old('quitar_portada')) class="rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                        Quitar mi imagen de portada
                    </label>
                @endif
            </div>
        </div>
    </section>

    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
        <a href="{{ route('operador.rutas.index') }}" class="tc-btn-outline text-center">Cancelar</a>
        <button type="submit" class="tc-btn-primary" @disabled($destinos->isEmpty())>{{ $esEdicion ? 'Guardar cambios' : 'Crear ruta' }}</button>
    </div>
</form>

@push('scripts')
@include('rutas::partials.mapa-paradas-scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const atractivosPorDestino = {{ Illuminate\Support\Js::from($atractivosPorDestino) }};
    const paradasIniciales = {{ Illuminate\Support\Js::from(array_values(array_map('intval', (array) $paradasIniciales))) }};
    const destinoInput = document.getElementById('destino_id');
    const disponibles = document.getElementById('atractivos-disponibles');
    const seleccionadas = document.getElementById('paradas-seleccionadas');
    const contadorDisponibles = document.getElementById('contador-disponibles');
    const contadorParadas = document.getElementById('contador-paradas');
    let paradas = [...new Set(paradasIniciales)];
    const mapaRuta = window.TourismCloudRouteStopsMap.create({
        mapId: 'mapa-ruta-formulario',
        apiKey: @json($googleMapsApiKey),
    });

    function atractivosDelDestino() {
        return atractivosPorDestino[String(destinoInput.value)] || [];
    }

    function crearBoton(texto, etiqueta, accion, deshabilitado = false) {
        const boton = document.createElement('button');
        boton.type = 'button';
        boton.className = 'tc-route-stop-action px-2.5 py-1.5';
        boton.textContent = texto;
        boton.setAttribute('aria-label', etiqueta);
        boton.disabled = deshabilitado;
        boton.addEventListener('click', accion);
        return boton;
    }

    function datosDeParada(id) {
        return atractivosDelDestino().find(function (atractivo) {
            return Number(atractivo.id) === Number(id);
        });
    }

    function paradasParaMapa() {
        return paradas.map(function (id, indice) {
            const atractivo = datosDeParada(id);

            return {
                orden: indice + 1,
                nombre: atractivo ? atractivo.nombre : 'Parada ' + (indice + 1),
                latitud: atractivo ? atractivo.latitud : null,
                longitud: atractivo ? atractivo.longitud : null,
            };
        });
    }

    function renderDisponibles() {
        const atractivos = atractivosDelDestino();
        const seleccionadasIds = new Set(paradas.map(Number));
        const disponiblesAtractivos = atractivos.filter(function (atractivo) {
            return !seleccionadasIds.has(Number(atractivo.id));
        });

        disponibles.replaceChildren();
        contadorDisponibles.textContent = destinoInput.value ? `${disponiblesAtractivos.length} disponibles` : '';

        if (!destinoInput.value) {
            const mensaje = document.createElement('p');
            mensaje.className = 'rounded-xl bg-white px-3 py-4 text-sm text-slate-500';
            mensaje.textContent = 'Selecciona un destino para ver sus atractivos activos.';
            disponibles.appendChild(mensaje);
            return;
        }

        if (disponiblesAtractivos.length === 0) {
            const mensaje = document.createElement('p');
            mensaje.className = 'rounded-xl bg-white px-3 py-4 text-sm text-slate-500';
            mensaje.textContent = 'No hay más atractivos activos disponibles para este destino.';
            disponibles.appendChild(mensaje);
            return;
        }

        disponiblesAtractivos.forEach(function (atractivo) {
            const fila = document.createElement('div');
            fila.className = 'tc-route-stop-row flex items-center justify-between gap-3 rounded-xl bg-white p-3';
            const detalle = document.createElement('div');
            const nombre = document.createElement('p');
            nombre.className = 'text-sm font-semibold text-slate-800';
            nombre.textContent = atractivo.nombre;
            const meta = document.createElement('p');
            meta.className = 'mt-0.5 text-xs text-slate-500';
            meta.textContent = `Entrada referencial: S/ ${atractivo.costo} · ${atractivo.duracion ? `${atractivo.duracion} min` : 'duración no indicada'}`;
            detalle.append(nombre, meta);
            fila.append(
                detalle,
                crearBoton('Agregar', `Agregar ${atractivo.nombre} como parada`, function () {
                    paradas.push(Number(atractivo.id));
                    renderTodo();
                })
            );
            disponibles.appendChild(fila);
        });
    }

    function renderParadas() {
        seleccionadas.replaceChildren();
        contadorParadas.textContent = `${paradas.length} ${paradas.length === 1 ? 'parada' : 'paradas'}`;

        if (paradas.length === 0) {
            const mensaje = document.createElement('p');
            mensaje.className = 'rounded-xl border border-dashed border-primary-200 bg-white/70 px-3 py-5 text-center text-sm text-slate-500';
            mensaje.textContent = 'Aún no agregaste paradas.';
            seleccionadas.appendChild(mensaje);
            return;
        }

        paradas.forEach(function (id, indice) {
            const atractivo = datosDeParada(id);
            const fila = document.createElement('div');
            fila.className = 'tc-route-stop-row flex flex-wrap items-center gap-3 rounded-xl bg-white p-3';

            const orden = document.createElement('span');
            orden.className = 'flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-primary-600 text-xs font-bold text-white';
            orden.textContent = String(indice + 1);

            const detalle = document.createElement('div');
            detalle.className = 'min-w-0 flex-1';
            const nombre = document.createElement('p');
            nombre.className = 'truncate text-sm font-semibold text-slate-800';
            nombre.textContent = atractivo ? atractivo.nombre : 'Atractivo no disponible: reemplázalo o elimínalo';
            detalle.appendChild(nombre);

            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'paradas[]';
            input.value = String(id);

            const acciones = document.createElement('div');
            acciones.className = 'flex w-full sm:w-auto shrink-0 flex-wrap items-center gap-1';
            acciones.append(
                crearBoton('Subir', `Subir ${atractivo ? atractivo.nombre : 'parada'}`, function () {
                    [paradas[indice - 1], paradas[indice]] = [paradas[indice], paradas[indice - 1]];
                    renderTodo();
                }, indice === 0),
                crearBoton('Bajar', `Bajar ${atractivo ? atractivo.nombre : 'parada'}`, function () {
                    [paradas[indice + 1], paradas[indice]] = [paradas[indice], paradas[indice + 1]];
                    renderTodo();
                }, indice === paradas.length - 1),
                crearBoton('Quitar', `Quitar ${atractivo ? atractivo.nombre : 'parada'}`, function () {
                    paradas.splice(indice, 1);
                    renderTodo();
                })
            );

            fila.append(orden, detalle, input, acciones);
            seleccionadas.appendChild(fila);
        });
    }

    function renderTodo() {
        renderDisponibles();
        renderParadas();
        mapaRuta.actualizar(paradasParaMapa());
    }

    destinoInput.addEventListener('change', function () {
        paradas = [];
        renderTodo();
    });

    renderTodo();
    let vistaPreviaUrl;
    document.getElementById('imagen_portada').addEventListener('change', function (event) {
        const archivo = event.target.files?.[0];
        if (!archivo) { return; }
        if (vistaPreviaUrl) { URL.revokeObjectURL(vistaPreviaUrl); }
        vistaPreviaUrl = URL.createObjectURL(archivo);
        const preview = document.getElementById('ruta-portada-preview');
        preview.src = vistaPreviaUrl;
        preview.classList.remove('hidden');
        document.getElementById('ruta-portada-placeholder')?.classList.add('hidden');
    });
});
</script>
@endpush
