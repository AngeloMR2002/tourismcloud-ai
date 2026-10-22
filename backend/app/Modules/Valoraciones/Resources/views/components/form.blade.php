@php
    /** @var \Illuminate\Database\Eloquent\Model $modelo */
    /** @var string $tipo */
    $miValoracion = $modelo->mi_valoracion;
@endphp

@auth
    @if($miValoracion)
        {{-- Estado: El usuario ya comentó. El formulario principal no duplica edición, solo informa --}}
        <div class="mt-8 bg-white rounded-3xl p-6 sm:p-8 border border-gray-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] w-full">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-5">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-teal-50 text-[#16734f] flex items-center justify-center shrink-0 shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-base font-bold text-gray-900">{{ __('reviews.already_reviewed') }}</h3>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                ★ {{ $miValoracion->puntuacion }}.0
                            </span>
                        </div>
                        <p class="text-xs sm:text-sm text-gray-500 mt-1">
                            {{ __('reviews.already_reviewed_hint') }}
                        </p>
                    </div>
                </div>
                <button type="button" 
                        onclick="abrirModalEdicionResena({{ $miValoracion->puntuacion }}, {{ json_encode($miValoracion->comentario ?? '') }})"
                        class="tc-btn-primary px-5 py-2.5 text-xs font-semibold shrink-0 whitespace-nowrap inline-flex items-center gap-1.5 shadow-sm hover:shadow transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                    {{ __('reviews.edit_my_review') }}
                </button>
            </div>
        </div>
    @else
        {{-- Formulario exclusivo de CREACIÓN de nueva reseña --}}
        <div class="mt-8 bg-white rounded-3xl p-6 sm:p-8 border border-gray-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] w-full">
            <div class="flex items-center justify-between pb-4 mb-6 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-[#16734f]/10 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-[#16734f]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">
                            {{ __('reviews.leave_review') }}
                        </h2>
                        <p class="text-xs text-gray-500 mt-0.5">
                            {{ __('reviews.share_experience') }}
                        </p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('valoraciones.store') }}" class="space-y-6">
                @csrf
                <input type="hidden" name="valorable_type" value="{{ $tipo }}">
                <input type="hidden" name="valorable_id" value="{{ $modelo->id }}">
                <input type="hidden" name="puntuacion" id="create_puntuacion_input_{{ $tipo }}_{{ $modelo->id }}" 
                       value="{{ old('puntuacion', 5) }}">

                {{-- 1. Selector interactivo de estrellas --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-800 mb-2">
                        {{ __('reviews.overall_rating') }}
                    </label>
                    <div class="flex items-center gap-2 flex-wrap" id="star-picker-create-{{ $tipo }}-{{ $modelo->id }}">
                        <div class="flex items-center gap-1 bg-amber-50/50 p-2 rounded-2xl border border-amber-100/80">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button"
                                        class="star-create-btn transition-transform hover:scale-125 focus:outline-none p-1 cursor-pointer"
                                        data-val="{{ $i }}"
                                        data-target="create_puntuacion_input_{{ $tipo }}_{{ $modelo->id }}"
                                        data-container="star-picker-create-{{ $tipo }}-{{ $modelo->id }}"
                                        title="{{ $i }} {{ $i === 1 ? __('reviews.star') : __('reviews.stars') }}">
                                    <svg class="w-7 h-7 sm:w-8 sm:h-8 star-create-svg transition-colors {{ $i <= 5 ? 'text-amber-400' : 'text-gray-300' }}"
                                         fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </button>
                            @endfor
                        </div>
                        <span id="star-create-text-{{ $tipo }}-{{ $modelo->id }}" class="text-sm font-bold text-gray-700 ml-2 whitespace-nowrap">
                            {{ __('reviews.stars_out_of_five', ['count' => 5]) }}
                        </span>
                    </div>
                    @error('puntuacion')
                        <p class="text-xs text-red-500 mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 2. Textarea de opinión --}}
                <div>
                    <label for="create_comentario_{{ $tipo }}_{{ $modelo->id }}" class="block text-sm font-semibold text-gray-800 mb-2">
                        {{ __('reviews.comment_optional') }} <span class="text-xs font-normal text-gray-400">{{ __('reviews.optional') }}</span>:
                    </label>
                    <textarea id="create_comentario_{{ $tipo }}_{{ $modelo->id }}"
                              name="comentario"
                              rows="4"
                              class="w-full rounded-2xl border border-gray-200 p-4 text-sm text-gray-800 placeholder-gray-400 focus:border-[#16734f] focus:ring-2 focus:ring-[#16734f]/20 transition outline-none resize-none"
                              placeholder="{{ __('reviews.comment_placeholder') }}">{{ old('comentario') }}</textarea>
                    @error('comentario')
                        <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 3. Botón de publicación --}}
                <div class="flex items-center justify-end pt-1">
                    <button type="submit" class="tc-btn-primary px-7 py-3 text-sm font-semibold inline-flex items-center gap-2 rounded-xl shadow-sm hover:shadow transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        {{ __('reviews.publish') }}
                    </button>
                </div>
            </form>
        </div>
    @endif
@else
    {{-- Estado Invitado: Invitar a iniciar sesión --}}
    <div class="mt-8 bg-white rounded-3xl p-6 sm:p-8 border border-gray-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] w-full">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-6 py-2">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-teal-50 text-[#16734f] flex items-center justify-center shrink-0 shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">{{ __('reviews.visited_place') }}</h3>
                    <p class="text-xs sm:text-sm text-gray-500 mt-1 max-w-xl">
                        {{ __('reviews.login_hint') }}
                    </p>
                </div>
            </div>
            <a href="{{ route('login') }}" class="tc-btn-primary px-6 py-2.5 text-sm font-semibold shrink-0 whitespace-nowrap inline-flex items-center gap-2 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                {{ __('reviews.login_to_review') }}
            </a>
        </div>
    </div>
@endauth

{{-- Script de interactividad de estrellas para el formulario de creación --}}
<script>
    (function() {
        const starsOutOfFive = @json(__('reviews.stars_out_of_five'));
        function initCreateStarPickers() {
            document.querySelectorAll('.star-create-btn').forEach(function(button) {
                var containerId = button.getAttribute('data-container');
                var targetId    = button.getAttribute('data-target');
                var value       = parseInt(button.getAttribute('data-val'), 10);

                var container = document.getElementById(containerId);
                var input     = document.getElementById(targetId);
                if (!container || !input) return;

                var textLabel = document.getElementById('star-create-text-' + targetId.replace('create_puntuacion_input_', ''));

                function updateStars(activeVal) {
                    var buttons = container.querySelectorAll('.star-create-btn');
                    buttons.forEach(function(btn) {
                        var bVal = parseInt(btn.getAttribute('data-val'), 10);
                        var svg  = btn.querySelector('.star-create-svg');
                        if (bVal <= activeVal) {
                            svg.classList.remove('text-gray-300');
                            svg.classList.add('text-amber-400');
                        } else {
                            svg.classList.remove('text-amber-400');
                            svg.classList.add('text-gray-300');
                        }
                    });
                    if (textLabel) {
                        textLabel.textContent = starsOutOfFive.replace(':count', String(activeVal));
                    }
                }

                button.addEventListener('mouseenter', function() { updateStars(value); });
                button.addEventListener('mouseleave', function() { updateStars(parseInt(input.value || 5, 10)); });
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    input.value = value;
                    updateStars(value);
                });
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initCreateStarPickers);
        } else {
            initCreateStarPickers();
        }
    })();
</script>
