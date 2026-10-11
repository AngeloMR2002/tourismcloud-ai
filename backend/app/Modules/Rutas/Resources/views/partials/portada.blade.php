@php($portadaUrl = $ruta->portada_url)
@if($portadaUrl)
    <img src="{{ $portadaUrl }}" alt="{{ $ruta->nombre }}" class="w-full h-full object-cover">
@else
    <div class="w-full h-full flex flex-col gap-2 items-center justify-center bg-primary-50 text-primary-600">
        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20l-6-3V4l6 3 6-3 6 3v13l-6-3-6 3zm0 0V7m6 10V4"/></svg>
        <span class="text-sm">Recorrido sin fotografía</span>
    </div>
@endif
