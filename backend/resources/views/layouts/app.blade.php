<!DOCTYPE html>
<html lang="es" class="h-full m-0 p-0">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $metaDescription ?? 'TourismCloud AI — Descubre los mejores atractivos y establecimientos turísticos.' }}">
    <title>{{ $title ?? 'TourismCloud AI' }} | TourismCloud AI</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex flex-col m-0 p-0">

    {{-- OVERLAY (backdrop oscuro con desenfoque) --}}
    <div id="sidebar-overlay"
         class="fixed inset-0 z-40 bg-black/50 backdrop-blur-sm transition-opacity duration-300 opacity-0 pointer-events-none"
         onclick="closeSidebar()"></div>

    {{-- SIDEBAR DRAWER --}}
    <aside id="sidebar-drawer"
           class="fixed inset-y-0 left-0 z-50 w-[300px] max-w-[85vw] flex flex-col bg-white transition-transform duration-300 ease-in-out -translate-x-full border-r border-neutral-200"
           style="box-shadow: 4px 0 24px rgba(0,0,0,0.12);">

        {{-- Encabezado del Sidebar --}}
        <div class="flex items-center justify-between px-5 h-16 shrink-0 border-b border-neutral-100">
            <a href="{{ route('inicio') }}" class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center shadow-sm" style="background: var(--color-primary-500);">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
                    </svg>
                </div>
                <span style="font-family: var(--font-display); font-weight: 800; font-size: 1.05rem; color: var(--color-primary-700); letter-spacing: -0.01em;">
                    TurismCloudIA
                </span>
            </a>
            <button onclick="closeSidebar()" class="p-2 rounded-lg text-neutral-500 hover:text-neutral-800 hover:bg-neutral-100 transition-colors" aria-label="Cerrar menú">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Navegación del Sidebar --}}
        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">

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
                    <a href="{{ route('operador.atractivos.index') }}" class="group/sidebar flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('operador.*') ? 'text-white shadow-sm' : 'text-neutral-700 hover:text-[#00626A] hover:bg-neutral-100' }}" style="{{ request()->routeIs('operador.*') ? 'background: var(--color-primary-600); color: white;' : '' }}">
                        <div class="shrink-0 transition-transform duration-150 group-hover/sidebar:scale-110">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <span class="whitespace-pre group-hover/sidebar:translate-x-1 transition-transform duration-150">Panel Operador</span>
                    </a>
                @endif

                @if(auth()->user()->isProveedor())
                    <a href="{{ route('proveedor.establecimientos.index') }}" class="group/sidebar flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('proveedor.*') ? 'text-white shadow-sm' : 'text-neutral-700 hover:text-[#00626A] hover:bg-neutral-100' }}" style="{{ request()->routeIs('proveedor.*') ? 'background: var(--color-primary-600); color: white;' : '' }}">
                        <div class="shrink-0 transition-transform duration-150 group-hover/sidebar:scale-110">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg>
                        </div>
                        <span class="whitespace-pre group-hover/sidebar:translate-x-1 transition-transform duration-150">Panel Proveedor</span>
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
    <header class="sticky top-0 z-30 w-full" style="background: linear-gradient(135deg, var(--color-primary-700) 0%, var(--color-primary-500) 100%); box-shadow: 0 2px 12px rgb(0 98 106 / 0.25);">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                <div class="flex items-center gap-3">
                    <button onclick="openSidebar()" class="p-2 -ml-2 rounded-lg transition-colors hover:bg-white/15 cursor-pointer text-white" aria-label="Abrir menú">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <a href="{{ route('inicio') }}" class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: rgba(255,255,255,0.2);">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/></svg>
                        </div>
                        <span class="hidden sm:inline" style="font-family: var(--font-display); font-weight: 800; font-size: 1.1rem; color: white; letter-spacing: -0.01em;">
                            TurismCloudIA
                        </span>
                    </a>
                </div>

                @auth
                    <div class="flex items-center gap-3">
                        <div class="text-right hidden md:block">
                            <p class="text-sm font-semibold text-white">
                                {{ auth()->user()->nombre }} {{ auth()->user()->apellido }}
                            </p>
                            <p class="text-xs text-white/80 capitalize">
                                {{ str_replace('_', ' ', auth()->user()->rol) }}
                            </p>
                        </div>
                        <div class="h-10 w-10 rounded-full bg-white text-[#00626A] flex items-center justify-center font-bold">
                            {{ substr(auth()->user()->nombre, 0, 1) }}{{ substr(auth()->user()->apellido, 0, 1) }}
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="ml-2">
                            @csrf
                            <button type="submit" class="p-2 text-white/80 hover:text-white transition-colors" title="Cerrar sesión">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                            </button>
                        </form>
                    </div>
                @else
                    <div class="flex gap-3">
                        <a href="{{ route('login') }}" class="text-sm font-medium text-white/90 hover:text-white px-4 py-2">Ingresar</a>
                        <a href="{{ route('register') }}" class="text-sm font-medium text-[#00626A] bg-white hover:bg-gray-50 px-4 py-2 rounded-lg transition-colors shadow-sm">Crear cuenta</a>
                    </div>
                @endauth
            </div>
        </div>
    </header>

    {{-- Flash messages --}}
    @if(session('exito'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <div class="tc-alert-exito" role="alert">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('exito') }}
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <div class="tc-alert-error" role="alert">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('error') }}
            </div>
        </div>
    @endif

    <main class="flex-1">
        @yield('content')
    </main>

    <footer style="background: var(--color-primary-900); color: rgba(255,255,255,0.55);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                <p class="text-sm">© {{ date('Y') }} TourismCloud AI.</p>
                <div class="flex gap-5 text-sm">
                    <a href="{{ route('catalogo.atractivos.index') }}" class="hover:text-white transition-colors">Atractivos</a>
                    <a href="{{ route('catalogo.establecimientos.index') }}" class="hover:text-white transition-colors">Establecimientos</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
    (function() {
        var overlay = document.getElementById('sidebar-overlay');
        var drawer  = document.getElementById('sidebar-drawer');

        window.openSidebar = function() {
            drawer.classList.remove('-translate-x-full');
            drawer.classList.add('translate-x-0');
            overlay.classList.remove('opacity-0', 'pointer-events-none');
            overlay.classList.add('opacity-100', 'pointer-events-auto');
            document.body.style.overflow = 'hidden';
        };

        window.closeSidebar = function() {
            drawer.classList.remove('translate-x-0');
            drawer.classList.add('-translate-x-full');
            overlay.classList.remove('opacity-100', 'pointer-events-auto');
            overlay.classList.add('opacity-0', 'pointer-events-none');
            document.body.style.overflow = '';
        };

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeSidebar();
        });
    })();
    </script>
    @stack('scripts')
</body>
</html>
