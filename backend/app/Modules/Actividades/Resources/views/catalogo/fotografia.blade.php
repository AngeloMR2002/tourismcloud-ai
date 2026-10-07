@if($registro->imagen_portada && $registro->imagen_fuente_url && $registro->imagen_autor && $registro->imagen_licencia)
    <figure class="tc-photo {{ ($compacta ?? false) ? 'tc-photo-compact' : 'tc-photo-detail' }}">
        <img src="{{ $registro->imagen_portada }}" alt="{{ $registro->nombre }}" loading="lazy" referrerpolicy="no-referrer" onerror="this.hidden=true; this.nextElementSibling.hidden=false">
        <p hidden class="tc-photo-unavailable">La fotografía no está disponible. Consulta su fuente.</p>
        <figcaption>Foto: {{ $registro->imagen_autor }} · <a href="{{ $registro->imagen_fuente_url }}" target="_blank" rel="noopener noreferrer">{{ $registro->imagen_licencia }} / fuente ↗</a></figcaption>
    </figure>
@else
    <div class="tc-photo-placeholder {{ ($compacta ?? false) ? '' : 'tc-photo-placeholder-detail' }}">
        <svg viewBox="0 0 64 64" fill="none" aria-hidden="true"><rect x="8" y="12" width="48" height="40" rx="7" stroke="currentColor" stroke-width="1.5"/><circle cx="43" cy="24" r="4" stroke="currentColor" stroke-width="1.5"/><path d="m10 44 14-17 11 13 7-8 12 13" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg>
        <span>Sin fotografía verificada</span>
        <small>Preferimos mostrarte información real.</small>
    </div>
@endif
