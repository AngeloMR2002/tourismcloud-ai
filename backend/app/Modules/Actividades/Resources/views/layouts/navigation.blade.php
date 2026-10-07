<a href="{{ route('catalogo.actividades.index') }}" class="tc-nav-link {{ request()->routeIs('catalogo.actividades.*') ? 'is-active' : '' }}" @if(request()->routeIs('catalogo.actividades.*')) aria-current="page" @endif>Actividades</a>
<a href="{{ route('catalogo.rutas.index') }}" class="tc-nav-link {{ request()->routeIs('catalogo.rutas.*') ? 'is-active' : '' }}" @if(request()->routeIs('catalogo.rutas.*')) aria-current="page" @endif>Rutas y circuitos</a>
@can('gestionar-catalogo')
<a href="{{ route('operador.actividades.index') }}" class="tc-nav-link {{ request()->routeIs('operador.actividades.*') ? 'is-active' : '' }}">Gestionar actividades</a>
<a href="{{ route('operador.rutas.index') }}" class="tc-nav-link {{ request()->routeIs('operador.rutas.*') ? 'is-active' : '' }}">Gestionar rutas</a>
@endcan
@can('revisar-catalogo')
<a href="{{ route('operador.importaciones.index') }}" class="tc-nav-link {{ request()->routeIs('operador.importaciones.*') ? 'is-active' : '' }}">Importar lugares</a>
<a href="{{ route('operador.revision.index') }}" class="tc-nav-link {{ request()->routeIs('operador.revision.*') ? 'is-active' : '' }}">Revisar</a>
@endcan
@auth
<form method="POST" action="{{ route('logout') }}">@csrf<button class="tc-btn-outline">Salir</button></form>
@else
<a href="{{ route('login') }}" class="tc-btn-outline tc-access-link">Acceso de gestión <span aria-hidden="true">↗</span></a>
@endauth
