@php
    /** @var \Illuminate\Database\Eloquent\Model $modelo */
    /** @var string $tipo */
    $promedio     = $modelo->promedio_puntuacion;
    $total        = $modelo->total_resenas;
    $distribucion = $modelo->distribucion_estrellas;
    $todasValoraciones = $modelo->valoraciones;

    // ── 3. Ordenamiento: Fijar la reseña propia en el primer lugar ──
    $authId   = Auth::id();
    $miResena = ($authId && $todasValoraciones) ? $todasValoraciones->firstWhere('usuario_id', $authId) : null;

    if ($miResena) {
        $otrasResenas = $todasValoraciones->reject(fn($r) => $r->usuario_id === $authId);
        $valoraciones = collect([$miResena])->concat($otrasResenas);
    } else {
        $valoraciones = $todasValoraciones;
    }
@endphp

<div class="tc-card p-5">

    {{-- ── 1. Resumen Superior (Flat, Sin Cajas Anidadas) ─────────── --}}
    <div>
        {{-- Cabecera de la sección --}}
        <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-600">
                Opiniones y Valoraciones
            </h3>
            <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-teal-50 text-teal-700 whitespace-nowrap">
                {{ $total }} {{ $total === 1 ? 'reseña' : 'reseñas' }}
            </span>
        </div>

        {{-- Promedio general y estrellas --}}
        <div class="flex items-center gap-3 mb-4">
            <span class="text-3xl font-extrabold tracking-tight text-gray-900 leading-none" style="font-family: var(--font-display);">
                {{ number_format($promedio, 1) }}
            </span>
            <div class="space-y-0.5">
                <div class="flex items-center gap-0.5 text-amber-400">
                    @for($i = 1; $i <= 5; $i++)
                        <svg class="w-3.5 h-3.5 {{ $i <= round($promedio) ? 'text-amber-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endfor
                </div>
                <p class="text-[11px] text-gray-500 whitespace-nowrap">
                    de 5.0 ({{ $total }} {{ $total === 1 ? 'calificación' : 'calificaciones' }})
                </p>
            </div>
        </div>

        {{-- 5 Barras de progreso de distribución (sin desbordamiento de texto) --}}
        <div class="space-y-1.5 mb-5">
            @foreach($distribucion as $estrella => $data)
                <div class="flex items-center gap-1.5 text-xs">
                    <span class="w-3 font-semibold text-gray-600 shrink-0 text-right whitespace-nowrap">
                        {{ $estrella }}
                    </span>
                    <svg class="w-3 h-3 text-amber-400 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    <div class="flex-1 h-2 rounded-full bg-gray-100 overflow-hidden min-w-[50px]">
                        <div class="h-full rounded-full transition-all duration-500 bg-amber-400"
                             style="width: {{ $data['porcentaje'] }}%;"></div>
                    </div>
                    <span class="w-7 text-[11px] text-gray-400 text-right shrink-0 whitespace-nowrap">
                        {{ $data['cantidad'] }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Divisor sutil --}}
    <div class="border-b border-gray-100 mb-4"></div>

    {{-- ── 2. Feed Vertical de Reseñas de la Comunidad ────────────── --}}
    <div>
        <div class="flex items-center justify-between mb-3">
            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-600">
                Reseñas de la Comunidad
            </h4>
            <span class="text-[11px] text-gray-400 font-medium">
                {{ $valoraciones->count() }}
            </span>
        </div>

        @if($valoraciones->isEmpty())
            <div class="py-6 text-center text-gray-400">
                <svg class="w-8 h-8 mx-auto mb-2 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <p class="text-xs">Aún no hay reseñas registradas.</p>
            </div>
        @else
            <div class="space-y-4 max-h-[560px] overflow-y-auto pr-1 scrollbar-thin">
                @foreach($valoraciones as $resena)
                    @php
                        $esPropia   = ($authId && $resena->usuario_id === $authId);
                        $user       = $resena->usuario;
                        $iniciales  = $user ? strtoupper(substr($user->nombre ?? 'T', 0, 1) . substr($user->apellido ?? '', 0, 1)) : 'U';
                    @endphp
                    <div class="pb-3 border-b border-gray-100 last:border-0 last:pb-0 {{ $esPropia ? 'p-3 bg-teal-50/30 rounded-2xl border border-teal-100/90 shadow-xs' : '' }}">
                        {{-- Cabecera del comentario --}}
                        <div class="flex items-start justify-between gap-2 mb-1.5">
                            <div class="flex items-center gap-2 min-w-0">
                                {{-- Avatar circular con iniciales --}}
                                <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold text-white shrink-0"
                                     style="background: linear-gradient(135deg, var(--color-primary-600) 0%, var(--color-primary-800) 100%);">
                                    {{ $iniciales }}
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span class="text-xs font-bold text-gray-800 leading-tight truncate">
                                            {{ $user ? "{$user->nombre} {$user->apellido}" : 'Usuario Turista' }}
                                        </span>

                                        {{-- Badge si es la reseña propia --}}
                                        @if($esPropia)
                                            <span class="inline-flex items-center gap-0.5 px-1.5 py-0.2 rounded-full text-[9px] font-bold bg-[#00626A] text-white shrink-0">
                                                Tu reseña
                                            </span>
                                        @endif

                                        {{-- Badge Verde: Visita Verificada --}}
                                        @if($resena->visita_verificada)
                                            <span class="inline-flex items-center gap-0.5 px-1.5 py-0.2 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0" title="Reseña verificada por visita o reserva confirmada">
                                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                Verificada
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Botones de Acción (Editar y Eliminar exclusivamente para la reseña propia) --}}
                            @auth
                                <div class="flex items-center gap-1 shrink-0">
                                    @if($esPropia)
                                        {{-- Botón Editar --}}
                                        <button type="button" 
                                                onclick="abrirModalEdicionResena({{ $resena->puntuacion }}, {{ json_encode($resena->comentario ?? '') }})"
                                                class="inline-flex items-center gap-0.5 text-[11px] font-semibold text-[#00626A] hover:text-[#00474d] bg-white hover:bg-teal-50 border border-teal-200 px-2 py-0.5 rounded-lg shadow-xs transition"
                                                title="Editar mi reseña">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                            </svg>
                                            Editar
                                        </button>

                                        {{-- Botón Eliminar --}}
                                        <form method="POST" action="{{ route('valoraciones.destroy', $resena) }}" onsubmit="event.preventDefault(); confirmarEliminarResena(this);">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="inline-flex items-center gap-0.5 text-[11px] font-semibold text-rose-600 hover:text-rose-700 bg-white hover:bg-rose-50 border border-rose-200 px-2 py-0.5 rounded-lg shadow-xs transition"
                                                    title="Eliminar mi reseña">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                Eliminar
                                            </button>
                                        </form>
                                    @elseif(Auth::user()->isAdmin())
                                        {{-- Admin puede moderar / eliminar reseñas de terceros --}}
                                        <form method="POST" action="{{ route('valoraciones.destroy', $resena) }}" onsubmit="event.preventDefault(); confirmarEliminarResena(this);">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-gray-300 hover:text-red-500 transition-colors p-0.5" title="Eliminar como administrador">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endauth
                        </div>

                        {{-- Estrellas y Fecha --}}
                        <div class="flex items-center gap-2 mb-1.5">
                            <div class="flex items-center text-amber-400">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-3 h-3 {{ $i <= $resena->puntuacion ? 'text-amber-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                            <span class="text-[10px] text-gray-400 font-medium whitespace-nowrap">
                                {{ $resena->created_at->diffForHumans() }}
                            </span>
                        </div>

                        {{-- Comentario --}}
                        @if($resena->comentario)
                            <p class="text-xs text-gray-600 leading-relaxed break-words">
                                {{ $resena->comentario }}
                            </p>
                        @else
                            <p class="text-[11px] text-gray-400 italic">
                                Calificación sin comentario.
                            </p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

{{-- ── MODAL CENTRADO DE EDICIÓN DE RESEÑA (OVERLAY) ──────────────── --}}
@auth
<div id="modal-editar-resena"
     class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm transition-opacity duration-300"
     onclick="if(event.target === this) cerrarModalEdicionResena();">
    <div class="relative bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full mx-auto shadow-2xl border border-gray-100 animate-in zoom-in-95 duration-200">
        
        {{-- Botón Cerrar (X) --}}
        <button type="button" 
                onclick="cerrarModalEdicionResena()" 
                class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 p-2 rounded-full hover:bg-gray-100 transition"
                title="Cerrar ventana">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        {{-- Encabezado del modal --}}
        <div class="flex items-center gap-3.5 pb-4 mb-5 border-b border-gray-100">
            <div class="w-11 h-11 rounded-2xl bg-teal-50 text-[#00626A] flex items-center justify-center shrink-0 shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-900">
                    Editar tu valoración
                </h3>
                <p class="text-xs text-gray-500">
                    Modifica tu puntuación y comparte tus comentarios actualizados.
                </p>
            </div>
        </div>

        {{-- Formulario de actualización --}}
        <form method="POST" action="{{ route('valoraciones.store') }}" class="space-y-5">
            @csrf
            <input type="hidden" name="valorable_type" value="{{ $tipo }}">
            <input type="hidden" name="valorable_id" value="{{ $modelo->id }}">
            <input type="hidden" name="puntuacion" id="modal_edit_puntuacion" value="5">

            {{-- Selector interactivo de estrellas en Modal --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-2">
                    Tu calificación:
                </label>
                <div class="flex items-center gap-2 flex-wrap" id="modal-star-picker">
                    <div class="flex items-center gap-1 bg-amber-50/50 p-2 rounded-2xl border border-amber-100">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button"
                                    class="modal-star-btn transition-transform hover:scale-125 focus:outline-none p-1 cursor-pointer"
                                    data-val="{{ $i }}"
                                    title="{{ $i }} {{ $i === 1 ? 'estrella' : 'estrellas' }}">
                                <svg class="w-7 h-7 modal-star-svg transition-colors text-amber-400"
                                     fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </button>
                        @endfor
                    </div>
                    <span id="modal-star-text" class="text-sm font-bold text-gray-800 ml-2 whitespace-nowrap">
                        5 de 5 estrellas
                    </span>
                </div>
            </div>

            {{-- Textarea de reseña en Modal --}}
            <div>
                <label for="modal_edit_comentario" class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-2">
                    Tu comentario o recomendación:
                </label>
                <textarea id="modal_edit_comentario"
                          name="comentario"
                          rows="4"
                          class="w-full rounded-2xl border border-gray-200 p-3.5 text-sm text-gray-800 placeholder-gray-400 focus:border-[#00626A] focus:ring-2 focus:ring-[#00626A]/20 transition outline-none resize-none"
                          placeholder="Escribe tu opinión actualizada..."></textarea>
            </div>

            {{-- Acciones del Modal --}}
            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" 
                        onclick="cerrarModalEdicionResena()"
                        class="px-5 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition">
                    Cancelar
                </button>
                <button type="submit" 
                        class="tc-btn-primary px-6 py-2.5 text-sm font-semibold inline-flex items-center gap-2 rounded-xl shadow-sm hover:shadow transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Actualizar Reseña
                </button>
            </div>
        </form>
    </div>
</div>
@endauth

{{-- Scripts para Modales y Eliminación con SweetAlert2 --}}
<script>
    // ── Abrir Modal de Edición Pre-llenado ──
    window.abrirModalEdicionResena = function(puntuacion, comentario) {
        var modal = document.getElementById('modal-editar-resena');
        if (!modal) return;

        var inputPuntuacion = document.getElementById('modal_edit_puntuacion');
        var textareaComentario = document.getElementById('modal_edit_comentario');
        var starText = document.getElementById('modal-star-text');

        var val = parseInt(puntuacion || 5, 10);
        if (inputPuntuacion) inputPuntuacion.value = val;
        if (textareaComentario) textareaComentario.value = comentario || '';
        if (starText) starText.textContent = val + ' de 5 estrellas';

        // Actualizar estrellas visuales
        document.querySelectorAll('.modal-star-btn').forEach(function(btn) {
            var bVal = parseInt(btn.getAttribute('data-val'), 10);
            var svg  = btn.querySelector('.modal-star-svg');
            if (svg) {
                if (bVal <= val) {
                    svg.classList.remove('text-gray-300');
                    svg.classList.add('text-amber-400');
                } else {
                    svg.classList.remove('text-amber-400');
                    svg.classList.add('text-gray-300');
                }
            }
        });

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    };

    // ── Cerrar Modal de Edición ──
    window.cerrarModalEdicionResena = function() {
        var modal = document.getElementById('modal-editar-resena');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    };

    // Cerrar con Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            cerrarModalEdicionResena();
        }
    });

    // ── Interactividad de Estrellas en Modal ──
    (function() {
        var container = document.getElementById('modal-star-picker');
        if (!container) return;

        var input = document.getElementById('modal_edit_puntuacion');
        var starText = document.getElementById('modal-star-text');

        function setModalStars(activeVal) {
            container.querySelectorAll('.modal-star-btn').forEach(function(btn) {
                var bVal = parseInt(btn.getAttribute('data-val'), 10);
                var svg  = btn.querySelector('.modal-star-svg');
                if (svg) {
                    if (bVal <= activeVal) {
                        svg.classList.remove('text-gray-300');
                        svg.classList.add('text-amber-400');
                    } else {
                        svg.classList.remove('text-amber-400');
                        svg.classList.add('text-gray-300');
                    }
                }
            });
            if (starText) {
                starText.textContent = activeVal + ' de 5 estrellas';
            }
        }

        container.querySelectorAll('.modal-star-btn').forEach(function(button) {
            var val = parseInt(button.getAttribute('data-val'), 10);
            button.addEventListener('mouseenter', function() { setModalStars(val); });
            button.addEventListener('mouseleave', function() { setModalStars(parseInt(input.value || 5, 10)); });
            button.addEventListener('click', function(e) {
                e.preventDefault();
                input.value = val;
                setModalStars(val);
            });
        });
    })();

    // ── Confirmar Eliminación con SweetAlert2 ──
    window.confirmarEliminarResena = function(form) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: '¿Eliminar tu reseña?',
                text: 'Esta acción no se puede deshacer y tu valoración será removida del cálculo del promedio.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                customClass: {
                    popup: 'rounded-3xl shadow-2xl p-6 font-sans border border-gray-100',
                    confirmButton: 'rounded-xl px-5 py-2.5 font-semibold text-sm shadow-sm',
                    cancelButton: 'rounded-xl px-5 py-2.5 font-semibold text-sm'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
            return false;
        }

        if (confirm('¿Seguro que deseas eliminar tu reseña?')) {
            form.submit();
        }
        return false;
    };
</script>
