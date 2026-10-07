<div class="tc-nav-section">
    <p class="tc-nav-section-label">Explorar</p>
    <a href="{{ route('catalogo.actividades.index') }}" class="tc-nav-link tc-nav-link-with-icon {{ request()->routeIs('catalogo.actividades.*') ? 'is-active' : '' }}" @if(request()->routeIs('catalogo.actividades.*')) aria-current="page" @endif><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m3 11 9-7 9 7v9a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z" stroke-linejoin="round"/></svg><span>Inicio y actividades</span></a>
    <a href="{{ route('catalogo.rutas.index') }}" class="tc-nav-link tc-nav-link-with-icon {{ request()->routeIs('catalogo.rutas.*') ? 'is-active' : '' }}" @if(request()->routeIs('catalogo.rutas.*')) aria-current="page" @endif><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M6 19c5-4 7-10 12-14M6 7h.01M18 17h.01" stroke-linecap="round"/><circle cx="6" cy="7" r="2.5"/><circle cx="18" cy="17" r="2.5"/></svg><span>Rutas y circuitos</span></a>
</div>
@can('gestionar-catalogo')
    <div class="tc-nav-divider" role="presentation"></div>
    <div class="tc-nav-section"><p class="tc-nav-section-label">Gestión</p>
        <a href="{{ route('operador.actividades.index') }}" class="tc-nav-link tc-nav-link-with-icon {{ request()->routeIs('operador.actividades.*') ? 'is-active' : '' }}"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="4" y="5" width="16" height="15" rx="2"/><path d="M8 5V3m8 2V3M8 10h8M8 14h5" stroke-linecap="round"/></svg><span>Mis actividades</span><small>OPE</small></a>
        <a href="{{ route('operador.rutas.index') }}" class="tc-nav-link tc-nav-link-with-icon {{ request()->routeIs('operador.rutas.*') ? 'is-active' : '' }}"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M5 4v16M19 4v16M5 7h14M5 17h14M9 7v10m6-10v10" stroke-linecap="round"/></svg><span>Mis rutas</span><small>OPE</small></a>
    </div>
@endcan
@can('revisar-catalogo')
    <div class="tc-nav-divider" role="presentation"></div>
    <div class="tc-nav-section"><p class="tc-nav-section-label">Verificación</p>
        <a href="{{ route('operador.importaciones.index') }}" class="tc-nav-link tc-nav-link-with-icon {{ request()->routeIs('operador.importaciones.*') ? 'is-active' : '' }}"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 3v12m0-12 4 4m-4-4-4 4M5 15v4a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-4" stroke-linecap="round" stroke-linejoin="round"/></svg><span>Importar lugares</span></a>
        <a href="{{ route('operador.revision.index') }}" class="tc-nav-link tc-nav-link-with-icon {{ request()->routeIs('operador.revision.*') ? 'is-active' : '' }}"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m5 12 4 4L19 6" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="9"/></svg><span>Revisar datos</span><small>ADM</small></a>
    </div>
@endcan
<div class="tc-nav-divider" role="presentation"></div>
@auth
    <form method="POST" action="{{ route('logout') }}" class="tc-nav-session">@csrf<button type="submit" class="tc-nav-link tc-nav-link-with-icon"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M10 17l5-5-5-5M15 12H3m10-7h5a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-5" stroke-linecap="round" stroke-linejoin="round"/></svg><span>Cerrar sesión</span></button></form>
@else
    <a href="{{ route('login') }}" class="tc-nav-link tc-nav-link-with-icon tc-nav-login"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M14 8V6a2 2 0 0 0-2-2H5a2 2 0 0 0 2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2-2v-2m-4-4h11m0 0-3-3m3 3-3 3" stroke-linecap="round" stroke-linejoin="round"/></svg><span>Acceso de gestión</span></a>
@endauth
