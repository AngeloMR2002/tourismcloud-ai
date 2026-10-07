<section class="tc-source-panel bg-white border rounded-xl p-5 space-y-3">
    <div class="flex items-center gap-3"><span class="tc-source-icon" aria-hidden="true">✓</span><div><p class="tc-eyebrow">TRANSPARENCIA</p><h2 class="font-bold">Fuente y comprobación</h2></div></div>
    <p class="text-sm text-gray-600">Información del lugar revisada: {{ $registro->fecha_verificacion?->format('d/m/Y') ?? 'Sin comprobar' }}. Esta fecha no garantiza precio, horario o cupos actuales.</p>
    <div class="flex flex-wrap gap-3">
        <a href="{{ $registro->fuente_url }}" target="_blank" rel="noopener noreferrer" class="tc-btn-outline">Consultar fuente <span aria-hidden="true">↗</span></a>
        @if($registro->sitio_web_oficial)<a href="{{ $registro->sitio_web_oficial }}" target="_blank" rel="noopener noreferrer" class="tc-btn-outline">Web oficial ↗</a>@endif
        @if($registro->url_reserva_oficial)<a href="{{ $registro->url_reserva_oficial }}" target="_blank" rel="noopener noreferrer" class="tc-btn-primary">Consultar con el responsable ↗</a>@endif
    </div>
    @if($registro->precio_fuente_url)
        <p class="text-sm">Tarifa comprobada: {{ $registro->precio_verificado_en?->format('d/m/Y') ?? 'Sin comprobar' }}.
            <a href="{{ $registro->precio_fuente_url }}" target="_blank" rel="noopener noreferrer" class="underline text-teal-800">Fuente de la tarifa ↗</a>
        </p>
    @endif
    <p class="tc-disclaimer">TourismCloud no confirma reservas ni disponibilidad. Si un costo es desconocido, no se considera cero ni se incluye como precio confirmado en un presupuesto.</p>
</section>
