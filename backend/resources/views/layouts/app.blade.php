<!DOCTYPE html>
<html lang="es" class="h-full m-0 p-0">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $metaDescription ?? 'TourismCloud AI — Descubre los mejores atractivos y establecimientos turísticos.' }}">
    <title>@yield('title', $title ?? 'Explorar') | TourismCloud AI</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="tc-app-shell h-full flex flex-col m-0 p-0">

    {{-- OVERLAY (backdrop oscuro con desenfoque) --}}
    <div id="sidebar-overlay"
         class="fixed inset-0 z-40 bg-black/50 backdrop-blur-sm transition-opacity duration-300 opacity-0 pointer-events-none"
         onclick="closeSidebar()"></div>

    {{-- SIDEBAR DRAWER --}}
    <aside id="sidebar-drawer" aria-label="Menú de navegación" aria-hidden="true" inert
           class="tc-sidebar fixed inset-y-0 left-0 z-50 w-[300px] max-w-[85vw] flex flex-col bg-white transition-transform duration-300 ease-in-out -translate-x-full border-r border-neutral-200">

        {{-- Encabezado del Sidebar --}}
        <div class="flex items-center justify-between px-5 h-16 shrink-0 border-b border-neutral-100">
            <a href="{{ route('inicio') }}" class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center shadow-sm" style="background: var(--color-primary-500);">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
                    </svg>
                </div>
                <span style="font-family: var(--font-display); font-weight: 800; font-size: 1.05rem; color: var(--color-primary-700); letter-spacing: -0.01em;">
                    TourismCloud AI
                </span>
            </a>
            <button onclick="closeSidebar()" class="p-2 rounded-lg text-neutral-500 hover:text-neutral-800 hover:bg-neutral-100 transition-colors" aria-label="Cerrar menú">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Navegación del Sidebar --}}
        <nav class="tc-sidebar__nav flex-1 overflow-y-auto py-4 px-3 space-y-1">

            {{-- Sección: Explorar (Público) --}}
            <div class="px-3 pt-2 pb-1.5">
                <span class="text-[0.65rem] font-bold uppercase tracking-widest text-neutral-400">Explorar</span>
            </div>

            <a href="{{ route('inicio') }}" class="group/sidebar flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('inicio') ? 'text-white shadow-sm' : 'text-neutral-700 hover:text-[#00626A] hover:bg-neutral-100' }}" style="{{ request()->routeIs('inicio') ? 'background: var(--color-primary-500); color: white;' : '' }}">
                <div class="shrink-0 transition-transform duration-150 group-hover/sidebar:scale-110">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                </div>
                <span class="whitespace-pre group-hover/sidebar:translate-x-1 transition-transform duration-150">Inicio</span>
            </a>

            <a href="{{ route('catalogo.atractivos.index') }}" class="group/sidebar flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('catalogo.atractivos.*') ? 'text-white shadow-sm' : 'text-neutral-700 hover:text-[#00626A] hover:bg-neutral-100' }}" style="{{ request()->routeIs('catalogo.atractivos.*') ? 'background: var(--color-primary-500); color: white;' : '' }}">
                <div class="shrink-0 transition-transform duration-150 group-hover/sidebar:scale-110">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                </div>
                <span class="whitespace-pre group-hover/sidebar:translate-x-1 transition-transform duration-150">Atractivos</span>
            </a>

            <a href="{{ route('catalogo.establecimientos.index') }}" class="group/sidebar flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('catalogo.establecimientos.*') ? 'text-white shadow-sm' : 'text-neutral-700 hover:text-[#00626A] hover:bg-neutral-100' }}" style="{{ request()->routeIs('catalogo.establecimientos.*') ? 'background: var(--color-primary-500); color: white;' : '' }}">
                <div class="shrink-0 transition-transform duration-150 group-hover/sidebar:scale-110">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <span class="whitespace-pre group-hover/sidebar:translate-x-1 transition-transform duration-150">Establecimientos</span>
            </a>

            @foreach(['actividades' => 'Actividades', 'rutas' => 'Rutas'] as $modulo => $etiqueta)
                <a href="{{ route('catalogo.'.$modulo.'.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('catalogo.'.$modulo.'.*') ? 'text-white shadow-sm' : 'text-neutral-700 hover:bg-neutral-100' }}" style="{{ request()->routeIs('catalogo.'.$modulo.'.*') ? 'background: var(--color-primary-500); color: white;' : '' }}">
                    @if($modulo === 'actividades')
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="4" r="2"/><path d="m10 8 4 2-2 6-5 5m5-5 3 5M10 8l-2 4-3 2m9-4 3 3 3 1"/></svg>
                    @else
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><circle cx="5" cy="5" r="2"/><circle cx="19" cy="19" r="2"/><path d="M7 5h6a4 4 0 0 1 0 8h-2a3 3 0 0 0 0 6h6"/></svg>
                    @endif
                    <span>{{ $etiqueta }}</span>
                </a>
            @endforeach
            <div class="my-3 mx-2 border-t border-neutral-100"></div>

            {{-- Sección: Gestión (Restringida) --}}
            <div class="px-3 pt-1 pb-1.5">
                <span class="text-[0.65rem] font-bold uppercase tracking-widest text-neutral-400">Gestión</span>
            </div>

            @guest
                <a href="{{ route('login') }}" class="group/sidebar flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('login') ? 'text-white shadow-sm' : 'text-neutral-700 hover:text-[#00626A] hover:bg-neutral-100' }}" style="{{ request()->routeIs('login') ? 'background: var(--color-primary-500); color: white;' : '' }}">
                    <div class="shrink-0 transition-transform duration-150 group-hover/sidebar:scale-110">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    </div>
                    <span class="whitespace-pre group-hover/sidebar:translate-x-1 transition-transform duration-150">Iniciar sesión</span>
                </a>
                <a href="{{ route('register') }}" class="group/sidebar flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('register') ? 'text-white shadow-sm' : 'text-neutral-700 hover:text-[#00626A] hover:bg-neutral-100' }}" style="{{ request()->routeIs('register') ? 'background: var(--color-primary-500); color: white;' : '' }}">
                    <div class="shrink-0 transition-transform duration-150 group-hover/sidebar:scale-110">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    </div>
                    <span class="whitespace-pre group-hover/sidebar:translate-x-1 transition-transform duration-150">Crear cuenta</span>
                </a>
            @endguest

            @auth
                @if(auth()->user()->isAdministrador() || auth()->user()->isTurista())
                    @php
                        $rutaModulo = 'dashboard';
                        $etiquetaModulo = 'Dashboard';
                    @endphp
                    <a href="{{ route($rutaModulo) }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-colors {{ request()->routeIs($rutaModulo === 'itinerarios.index' ? 'itinerarios.*' : $rutaModulo) ? 'text-white shadow-sm' : 'text-neutral-700 hover:bg-neutral-100' }}" style="{{ request()->routeIs($rutaModulo === 'itinerarios.index' ? 'itinerarios.*' : $rutaModulo) ? 'background: var(--color-primary-500); color: white;' : '' }}">
                        @if($rutaModulo === 'dashboard')
                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="8" height="8" rx="1"/><rect x="13" y="3" width="8" height="5" rx="1"/><rect x="13" y="10" width="8" height="11" rx="1"/><rect x="3" y="13" width="8" height="8" rx="1"/></svg>
                        @elseif($rutaModulo === 'agenda.index')
                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/></svg>
                        @else
                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 5h18M6 3v4m12-4v4M5 9h14v11H5zM8 13h3m2 0h3m-8 3h3"/></svg>
                        @endif
                        <span>{{ $etiquetaModulo }}</span>
                    </a>
                @endif
                @if(auth()->user()->isTurista())
                    @foreach(['itinerarios.index' => 'Mis itinerarios', 'agenda.index' => 'Agenda'] as $rutaModulo => $etiquetaModulo)
                        <a href="{{ route($rutaModulo) }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-colors {{ request()->routeIs($rutaModulo === 'itinerarios.index' ? 'itinerarios.*' : $rutaModulo) ? 'text-white shadow-sm' : 'text-neutral-700 hover:bg-neutral-100' }}" style="{{ request()->routeIs($rutaModulo === 'itinerarios.index' ? 'itinerarios.*' : $rutaModulo) ? 'background: var(--color-primary-500); color: white;' : '' }}">
                            @if($rutaModulo === 'agenda.index')
                                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/></svg>
                            @else
                                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 5h18M6 3v4m12-4v4M5 9h14v11H5zM8 13h3m2 0h3m-8 3h3"/></svg>
                            @endif
                            <span>{{ $etiquetaModulo }}</span>
                        </a>
                    @endforeach
                @endif
                @if(auth()->user()->isAdministrador())
                    <a href="{{ route('admin.usuarios.index') }}" class="group/sidebar flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.usuarios.*') ? 'text-white shadow-sm' : 'text-neutral-700 hover:text-[#00626A] hover:bg-neutral-100' }}" style="{{ request()->routeIs('admin.usuarios.*') ? 'background: var(--color-primary-600); color: white;' : '' }}">
                        <div class="shrink-0 transition-transform duration-150 group-hover/sidebar:scale-110">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                        <span class="whitespace-pre group-hover/sidebar:translate-x-1 transition-transform duration-150">Usuarios</span>
                    </a>
                    <a href="{{ route('admin.destinos.index') }}" class="group/sidebar flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.destinos.*') ? 'text-white shadow-sm' : 'text-neutral-700 hover:text-[#00626A] hover:bg-neutral-100' }}" style="{{ request()->routeIs('admin.destinos.*') ? 'background: var(--color-primary-600); color: white;' : '' }}">
                        <div class="shrink-0 transition-transform duration-150 group-hover/sidebar:scale-110">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <span class="whitespace-pre group-hover/sidebar:translate-x-1 transition-transform duration-150">Destinos</span>
                    </a>
                    <a href="{{ route('preferencias.index') }}" class="group/sidebar flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('preferencias.index', 'preferencias.create', 'preferencias.show', 'preferencias.edit') ? 'text-white shadow-sm' : 'text-neutral-700 hover:text-[#00626A] hover:bg-neutral-100' }}" style="{{ request()->routeIs('preferencias.index', 'preferencias.create', 'preferencias.show', 'preferencias.edit') ? 'background: var(--color-primary-600); color: white;' : '' }}">
                        <div class="shrink-0 transition-transform duration-150 group-hover/sidebar:scale-110">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                        </div>
                        <span class="whitespace-pre group-hover/sidebar:translate-x-1 transition-transform duration-150">Preferencias</span>
                    </a>
                @endif

                @if(auth()->user()->isOperador())
                    <a href="{{ route('operador.atractivos.index') }}" class="group/sidebar flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('operador.atractivos.*') ? 'text-white shadow-sm' : 'text-neutral-700 hover:text-[#00626A] hover:bg-neutral-100' }}" style="{{ request()->routeIs('operador.atractivos.*') ? 'background: var(--color-primary-600); color: white;' : '' }}">
                        <div class="shrink-0 transition-transform duration-150 group-hover/sidebar:scale-110">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <span class="whitespace-pre group-hover/sidebar:translate-x-1 transition-transform duration-150">Mis Atractivos</span>
                    </a>
                    @foreach(['actividades' => 'Mis actividades', 'rutas' => 'Mis rutas'] as $modulo => $etiqueta)
                        <a href="{{ route('operador.'.$modulo.'.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('operador.'.$modulo.'.*') ? 'text-white shadow-sm' : 'text-neutral-700 hover:bg-neutral-100' }}" style="{{ request()->routeIs('operador.'.$modulo.'.*') ? 'background: var(--color-primary-600); color: white;' : '' }}">
                            @if($modulo === 'actividades')
                                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="4" r="2"/><path d="m10 8 4 2-2 6-5 5m5-5 3 5M10 8l-2 4-3 2m9-4 3 3 3 1"/></svg>
                            @else
                                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><circle cx="5" cy="5" r="2"/><circle cx="19" cy="19" r="2"/><path d="M7 5h6a4 4 0 0 1 0 8h-2a3 3 0 0 0 0 6h6"/></svg>
                            @endif
                            <span>{{ $etiqueta }}</span>
                        </a>
                    @endforeach
                @endif

                @if(auth()->user()->isProveedor())
                    <a href="{{ route('proveedor.establecimientos.index') }}" class="group/sidebar flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('proveedor.*') ? 'text-white shadow-sm' : 'text-neutral-700 hover:text-[#00626A] hover:bg-neutral-100' }}" style="{{ request()->routeIs('proveedor.*') ? 'background: var(--color-primary-600); color: white;' : '' }}">
                        <div class="shrink-0 transition-transform duration-150 group-hover/sidebar:scale-110">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg>
                        </div>
                        <span class="whitespace-pre group-hover/sidebar:translate-x-1 transition-transform duration-150">Mis Establecimientos</span>
                    </a>
                    <a href="{{ route('preferencias.recientes') }}" class="group/sidebar flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('preferencias.recientes') ? 'text-white shadow-sm' : 'text-neutral-700 hover:text-[#00626A] hover:bg-neutral-100' }}" style="{{ request()->routeIs('preferencias.recientes') ? 'background: var(--color-primary-600); color: white;' : '' }}">
                        <div class="shrink-0 transition-transform duration-150 group-hover/sidebar:scale-110">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        </div>
                        <span class="whitespace-pre group-hover/sidebar:translate-x-1 transition-transform duration-150">Tendencias Turistas</span>
                    </a>
                @endif

                @if(auth()->user()->isTurista())
                    <a href="{{ route('preferencias.turista', auth()->id()) }}" class="group/sidebar flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('preferencias.turista*') ? 'text-white shadow-sm' : 'text-neutral-700 hover:text-[#00626A] hover:bg-neutral-100' }}" style="{{ request()->routeIs('preferencias.turista*') ? 'background: var(--color-primary-600); color: white;' : '' }}">
                        <div class="shrink-0 transition-transform duration-150 group-hover/sidebar:scale-110">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        </div>
                        <span class="whitespace-pre group-hover/sidebar:translate-x-1 transition-transform duration-150">Mis Preferencias</span>
                    </a>
                @endif
            @endauth
        </nav>

        <div class="px-5 py-3 shrink-0 border-t border-neutral-100 text-center">
            <span class="text-[0.7rem] text-neutral-400 font-medium">TourismCloud AI &copy; {{ date('Y') }}</span>
        </div>
    </aside>

    {{-- TOPBAR --}}
    <a href="#contenido" class="tc-skip-link">Ir al contenido</a>
    <div id="site-chrome" class="tc-site-chrome tc-site-chrome--home">
    <header class="tc-topbar tc-topbar--home w-full">
        <div class="tc-topbar__inner w-full px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                <div class="flex items-center gap-3">
                    <button onclick="openSidebar()" class="tc-menu-trigger" aria-label="Abrir menú" aria-controls="sidebar-drawer" aria-expanded="false">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <a href="{{ route('inicio') }}" class="tc-brand">
                        <div class="tc-brand__mark">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="m3 18 6-9 4 6 3-4 5 7H3ZM9 9l2-3 3 5" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/></svg>
                        </div>
                        <span>TourismCloud <b>AI</b></span>
                    </a>
                </div>

                <div class="tc-topbar__actions">
                    <div class="tc-header-utilities" role="group" aria-label="Preferencias de la plataforma">
                        {{-- Dropdown Idioma (solo Español / English) --}}
                        <div class="tc-dropdown-wrap" data-dropdown="language">
                            <button type="button" class="tc-utility-control" data-dropdown-trigger aria-haspopup="menu" aria-expanded="false" title="Seleccionar idioma">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><circle cx="12" cy="12" r="8" stroke-width="1.7"/><path d="M4 12h16M12 4c2.1 2.2 3.2 4.9 3.2 8S14.1 17.8 12 20M12 4C9.9 6.2 8.8 8.9 8.8 12s1.1 5.8 3.2 8" stroke-width="1.7" stroke-linecap="round"/></svg>
                                <span data-current-language>Español</span>
                                <svg class="tc-utility-control__chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="m7 10 5 5 5-5" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                            <div class="tc-dropdown-menu" role="menu" hidden>
                                <button type="button" class="tc-dropdown-item" data-language-option="es" role="menuitem" aria-pressed="true">
                                    <span class="tc-dropdown-mark">ES</span>
                                    <span>Español</span>
                                    <svg class="tc-dropdown-check" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                </button>
                                <button type="button" class="tc-dropdown-item" data-language-option="en" role="menuitem" aria-pressed="false">
                                    <span class="tc-dropdown-mark">EN</span>
                                    <span>English</span>
                                    <svg class="tc-dropdown-check" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                </button>
                            </div>
                        </div>

                        {{-- Dropdown Moneda (solo Soles / Dólares) --}}
                        <div class="tc-dropdown-wrap" data-dropdown="currency">
                            <button type="button" class="tc-utility-control" data-dropdown-trigger aria-haspopup="menu" aria-expanded="false" title="Seleccionar moneda">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><circle cx="12" cy="12" r="8" stroke-width="1.7"/><path d="M15.2 8.8c-.7-.6-1.8-.9-3-.9-1.7 0-2.9.8-2.9 2 0 3.1 5.8 1.1 5.8 4.1 0 1.3-1.2 2.1-3 2.1-1.3 0-2.5-.4-3.3-1.1M12 6.6v10.8" stroke-width="1.7" stroke-linecap="round"/></svg>
                                <span data-current-currency>S/ PEN</span>
                                <svg class="tc-utility-control__chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="m7 10 5 5 5-5" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                            <div class="tc-dropdown-menu" role="menu" hidden>
                                <button type="button" class="tc-dropdown-item" data-currency-option="PEN" role="menuitem" aria-pressed="true">
                                    <span class="tc-dropdown-mark">S/</span>
                                    <span>Soles (PEN)</span>
                                    <svg class="tc-dropdown-check" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                </button>
                                <button type="button" class="tc-dropdown-item" data-currency-option="USD" role="menuitem" aria-pressed="false">
                                    <span class="tc-dropdown-mark">$</span>
                                    <span>Dólares (USD)</span>
                                    <svg class="tc-dropdown-check" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                </button>
                            </div>
                        </div>

                        <button type="button" class="tc-utility-control" data-help-open aria-haspopup="dialog" aria-controls="help-dialog">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><circle cx="12" cy="12" r="8" stroke-width="1.7"/><path d="M9.8 9.5a2.4 2.4 0 1 1 4.4 1.4c-.8 1-2.2 1.4-2.2 3M12 16.8h.01" stroke-width="1.7" stroke-linecap="round"/></svg>
                            <span data-tc-i18n="help_label">Ayuda</span>
                        </button>
                    </div>

                    @auth
                        <div class="tc-account flex items-center gap-3">
                            <div class="tc-account__identity text-right hidden md:block">
                                <p class="text-sm font-semibold text-white">
                                    {{ auth()->user()->nombre }} {{ auth()->user()->apellido }}
                                </p>
                                <p class="text-xs text-white/80 capitalize">
                                    {{ str_replace('_', ' ', auth()->user()->rol) }}
                                </p>
                            </div>
                            <button type="button" onclick="openSidebar()" class="tc-account__avatar" aria-label="Abrir mi espacio y opciones de cuenta">
                                {{ substr(auth()->user()->nombre, 0, 1) }}{{ substr(auth()->user()->apellido, 0, 1) }}
                            </button>
                            <form method="POST" action="{{ route('logout') }}" class="ml-2">
                                @csrf
                                <button type="submit" class="p-2 text-white/80 hover:text-white transition-colors" title="Cerrar sesión">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="tc-auth-actions">
                            <a href="{{ route('login') }}" class="tc-login-link">Ingresar</a>
                            <a href="{{ route('register') }}" class="tc-signup-link">Crear cuenta</a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    @auth
        <nav class="tc-context-nav" aria-label="Mi espacio">
            <div class="tc-context-nav__inner">
                <span>Mi espacio</span>
                @if(auth()->user()->isTurista())
                    @foreach(['dashboard' => 'Resumen', 'preferencias.turista' => 'Preferencias', 'itinerarios.index' => 'Itinerarios', 'agenda.index' => 'Agenda'] as $navRoute => $navLabel)
                        <a href="{{ $navRoute === 'preferencias.turista' ? route($navRoute, auth()->id()) : route($navRoute) }}" {{ request()->routeIs(str_replace('.index', '.*', $navRoute).'*') ? 'aria-current=page' : '' }}>{{ $navLabel }}</a>
                    @endforeach
                @elseif(auth()->user()->isOperador())
                    @foreach(['atractivos' => 'Mis atractivos', 'actividades' => 'Mis actividades', 'rutas' => 'Mis rutas'] as $modulo => $navLabel)
                        <a href="{{ route('operador.'.$modulo.'.index') }}" {{ request()->routeIs('operador.'.$modulo.'.*') ? 'aria-current=page' : '' }}>{{ $navLabel }}</a>
                    @endforeach
                @elseif(auth()->user()->isAdministrador())
                    @foreach(['dashboard' => 'Resumen', 'admin.usuarios.index' => 'Usuarios', 'admin.destinos.index' => 'Destinos', 'preferencias.index' => 'Preferencias'] as $navRoute => $navLabel)
                        <a href="{{ route($navRoute) }}" {{ request()->routeIs(str_replace('.index', '.*', $navRoute)) ? 'aria-current=page' : '' }}>{{ $navLabel }}</a>
                    @endforeach
                @elseif(auth()->user()->isProveedor())
                    <a href="{{ route('proveedor.establecimientos.index') }}" {{ request()->routeIs('proveedor.establecimientos.*') ? 'aria-current=page' : '' }}>Mis establecimientos</a>
                    <a href="{{ route('preferencias.recientes') }}" {{ request()->routeIs('preferencias.recientes') ? 'aria-current=page' : '' }}>Preferencias recientes</a>
                @endif
            </div>
        </nav>
    @endauth
    </div>

    {{-- Modal Centrado de Notificación de Éxito --}}
    @if(session('exito'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Operación Exitosa!',
                        text: @json(session('exito')),
                        confirmButtonText: 'Aceptar',
                        confirmButtonColor: '#00626A',
                        timer: 4000,
                        timerProgressBar: true,
                        background: '#ffffff',
                        customClass: {
                            popup: 'rounded-3xl shadow-2xl p-6 font-sans border border-gray-100',
                            confirmButton: 'rounded-xl px-6 py-2.5 font-semibold text-sm shadow-sm'
                        }
                    });
                } else {
                    var m = document.getElementById('modal-notificacion-exito');
                    if (m) m.classList.remove('hidden');
                }
            });
        </script>
        {{-- Fallback nativo centrado con ícono animado tipo check --}}
        <div id="modal-notificacion-exito" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm transition-opacity duration-300">
            <div class="relative bg-white rounded-3xl p-6 sm:p-8 max-w-sm w-full mx-auto text-center shadow-2xl border border-gray-100 animate-in zoom-in-95 duration-200">
                <button type="button" onclick="document.getElementById('modal-notificacion-exito').remove()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 p-1.5 rounded-full hover:bg-gray-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
                <div class="mx-auto mb-4 w-16 h-16 rounded-full bg-emerald-50 flex items-center justify-center border-2 border-emerald-100 text-emerald-600 shadow-inner">
                    <svg class="w-9 h-9 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-1.5">¡Operación Exitosa!</h3>
                <p class="text-sm text-gray-600 mb-6">{{ session('exito') }}</p>
                <button type="button" onclick="document.getElementById('modal-notificacion-exito').remove()" class="tc-btn-primary w-full justify-center py-2.5 rounded-xl font-semibold text-sm">
                    Aceptar
                </button>
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="tc-flash-wrap max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <div class="tc-alert-error" role="alert">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('error') }}
            </div>
        </div>
    @endif

    <main id="contenido" class="tc-main flex-1" tabindex="-1">
        @yield('content')
    </main>

    @php
        $footerPanelUrl = route('register');
        $footerPanelLabel = 'Crear una cuenta';

        if (auth()->check()) {
            if (auth()->user()->isOperador()) {
                $footerPanelUrl = route('operador.actividades.index');
                $footerPanelLabel = 'Mis actividades';
            } elseif (auth()->user()->isProveedor()) {
                $footerPanelUrl = route('proveedor.establecimientos.index');
                $footerPanelLabel = 'Mis establecimientos';
            } else {
                $footerPanelUrl = route('dashboard');
                $footerPanelLabel = 'Abrir mi panel';
            }
        }
    @endphp

    <footer class="tc-footer">
        <div class="tc-footer__inner">
            <div class="tc-footer__intro">
                <a href="{{ route('inicio') }}" class="tc-footer__brand">TourismCloud <b>AI</b></a>
                <p>Una plataforma para descubrir, organizar y consultar experiencias turísticas de forma estructurada.</p>
            </div>
            <div>
                <h2>Explora</h2>
                <a href="{{ route('catalogo.atractivos.index') }}">Atractivos</a>
                <a href="{{ route('catalogo.actividades.index') }}">Actividades</a>
                <a href="{{ route('catalogo.rutas.index') }}">Rutas</a>
                <a href="{{ route('catalogo.establecimientos.index') }}">Establecimientos</a>
            </div>
            <div>
                <h2>Planifica</h2>
                @auth
                    @if(auth()->user()->isTurista())
                        <a href="{{ route('dashboard') }}">Mi resumen</a>
                        <a href="{{ route('itinerarios.index') }}">Mis itinerarios</a>
                        <a href="{{ route('agenda.index') }}">Mi agenda</a>
                    @else
                        <a href="{{ $footerPanelUrl }}">{{ $footerPanelLabel }}</a>
                        <a href="{{ route('inicio') }}#destinos">Destinos disponibles</a>
                    @endif
                @else
                    <a href="{{ route('register') }}">Crear una cuenta</a>
                    <a href="{{ route('login') }}">Ingresar</a>
                    <a href="{{ route('inicio') }}#destinos">Destinos disponibles</a>
                @endauth
            </div>
            <div>
                <h2>Para participar</h2>
                <p>Operadores y proveedores pueden registrar contenido y mantener sus datos actualizados desde su cuenta.</p>
                <a class="tc-footer__cta" href="{{ $footerPanelUrl }}">{{ $footerPanelLabel }} <span aria-hidden="true">→</span></a>
            </div>
        </div>
        <div class="tc-footer__bottom">
            <p>© {{ date('Y') }} TourismCloud AI · Proyecto académico</p>
            <a href="#contenido">Volver arriba <span aria-hidden="true">↑</span></a>
        </div>
    </footer>

    @include('components.preference-dialogs')

    <script>
    (function() {
        var overlay = document.getElementById('sidebar-overlay');
        var drawer  = document.getElementById('sidebar-drawer');
        var trigger = document.querySelector('.tc-menu-trigger');
        var chrome = document.getElementById('site-chrome');
        var previousFocus;
        var lastScrollY = window.scrollY;
        var ticking = false;

        function sincronizarCabecera() {
            var scrollY = Math.max(0, window.scrollY);
            var diferencia = scrollY - lastScrollY;

            chrome.classList.toggle('is-scrolled', scrollY > 8);

            if (drawer.getAttribute('aria-hidden') === 'false' || scrollY < 72) {
                chrome.classList.remove('is-header-hidden');
            } else if (Math.abs(diferencia) >= 10) {
                chrome.classList.toggle('is-header-hidden', diferencia > 0);
            }

            lastScrollY = scrollY;
            ticking = false;
        }

        window.openSidebar = function() {
            previousFocus = document.activeElement;
            chrome.classList.remove('is-header-hidden');
            drawer.inert = false;
            drawer.setAttribute('aria-hidden', 'false');
            trigger.setAttribute('aria-expanded', 'true');
            drawer.classList.remove('-translate-x-full');
            drawer.classList.add('translate-x-0');
            overlay.classList.remove('opacity-0', 'pointer-events-none');
            overlay.classList.add('opacity-100', 'pointer-events-auto');
            document.body.style.overflow = 'hidden';
            drawer.querySelector('button').focus();
        };

        window.closeSidebar = function() {
            if (drawer.getAttribute('aria-hidden') === 'true') return;
            drawer.inert = true;
            drawer.setAttribute('aria-hidden', 'true');
            trigger.setAttribute('aria-expanded', 'false');
            drawer.classList.remove('translate-x-0');
            drawer.classList.add('-translate-x-full');
            overlay.classList.remove('opacity-100', 'pointer-events-auto');
            overlay.classList.add('opacity-0', 'pointer-events-none');
            document.body.style.overflow = '';
            if (previousFocus) previousFocus.focus();
        };

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !document.querySelector('dialog[open]')) closeSidebar();
            if (e.key !== 'Tab' || drawer.inert) return;
            var focusable = drawer.querySelectorAll('a[href], button, input, select, textarea, [tabindex="0"]');
            var first = focusable[0];
            var last = focusable[focusable.length - 1];
            if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
            if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
        });

        window.addEventListener('scroll', function() {
            if (!ticking) {
                ticking = true;
                window.requestAnimationFrame(sincronizarCabecera);
            }
        }, { passive: true });
        window.addEventListener('pageshow', function() {
            chrome.classList.remove('is-header-hidden');
            lastScrollY = window.scrollY;
            sincronizarCabecera();
        });
        chrome.addEventListener('focusin', function() {
            chrome.classList.remove('is-header-hidden');
        });
        sincronizarCabecera();
    })();
    </script>
    @stack('scripts')
</body>
</html>
