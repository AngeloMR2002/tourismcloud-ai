<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $metaDescription ?? 'TourismCloud AI — Descubre los mejores atractivos y establecimientos turísticos.' }}">
    <title>{{ $title ?? 'TourismCloud AI' }} | TourismCloud AI</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex flex-col">

    {{-- ══════════════════════════════════════════════════════════════
         OVERLAY (backdrop oscuro con desenfoque — no mueve el contenido, lo opaca)
    ══════════════════════════════════════════════════════════════ --}}
    <div id="sidebar-overlay"
         class="fixed inset-0 z-40 bg-black/50 backdrop-blur-sm transition-opacity duration-300 opacity-0 pointer-events-none"
         onclick="closeSidebar()"></div>

    {{-- ══════════════════════════════════════════════════════════════
         SIDEBAR DRAWER (estilo Aceternity UI Sidebar — 300px)
    ══════════════════════════════════════════════════════════════ --}}
    <aside id="sidebar-drawer"
           class="fixed inset-y-0 left-0 z-50 w-[300px] max-w-[85vw] flex flex-col bg-white transition-transform duration-300 ease-in-out -translate-x-full border-r border-neutral-200"
           style="box-shadow: 4px 0 24px rgba(0,0,0,0.12);">

        {{-- Encabezado del Sidebar: Marca + Botón Cerrar (X) --}}
        <div class="flex items-center justify-between px-5 h-16 shrink-0 border-b border-neutral-100">
            <a href="/" class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center shadow-sm"
                     style="background: var(--color-primary-500);">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
                    </svg>
                </div>
                <span style="font-family: var(--font-display); font-weight: 800; font-size: 1.05rem; color: var(--color-primary-700); letter-spacing: -0.01em;">
                    TurismCloudIA
                </span>
            </a>
            {{-- Botón X para cerrar --}}
            <button onclick="closeSidebar()"
                    class="p-2 rounded-lg text-neutral-500 hover:text-neutral-800 hover:bg-neutral-100 transition-colors"
                    aria-label="Cerrar menú">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Navegación del Sidebar (Links interactivos con animación micro-translate) --}}
        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">

            {{-- Sección: Explorar --}}
            <div class="px-3 pt-2 pb-1.5">
                <span class="text-[0.65rem] font-bold uppercase tracking-widest text-neutral-400">Explorar</span>
            </div>

            {{-- Enlace: Inicio --}}
            <a href="/"
               class="group/sidebar flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                      {{ request()->is('/') ? 'text-white shadow-sm' : 'text-neutral-700 hover:text-[#00626A] hover:bg-neutral-100' }}"
               style="{{ request()->is('/') ? 'background: var(--color-primary-500); color: white;' : '' }}">
                <div class="shrink-0 transition-transform duration-150 group-hover/sidebar:scale-110">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
                <span class="whitespace-pre group-hover/sidebar:translate-x-1 transition-transform duration-150">Inicio</span>
            </a>

            {{-- Enlace: Atractivos --}}
            <a href="{{ route('catalogo.atractivos.index') }}"
               class="group/sidebar flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                      {{ request()->routeIs('catalogo.atractivos.*') ? 'text-white shadow-sm' : 'text-neutral-700 hover:text-[#00626A] hover:bg-neutral-100' }}"
               style="{{ request()->routeIs('catalogo.atractivos.*') ? 'background: var(--color-primary-500); color: white;' : '' }}">
                <div class="shrink-0 transition-transform duration-150 group-hover/sidebar:scale-110">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                </div>
                <span class="whitespace-pre group-hover/sidebar:translate-x-1 transition-transform duration-150">Atractivos</span>
            </a>

            {{-- Enlace: Establecimientos --}}
            <a href="{{ route('catalogo.establecimientos.index') }}"
               class="group/sidebar flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                      {{ request()->routeIs('catalogo.establecimientos.*') ? 'text-white shadow-sm' : 'text-neutral-700 hover:text-[#00626A] hover:bg-neutral-100' }}"
               style="{{ request()->routeIs('catalogo.establecimientos.*') ? 'background: var(--color-primary-500); color: white;' : '' }}">
                <div class="shrink-0 transition-transform duration-150 group-hover/sidebar:scale-110">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <span class="whitespace-pre group-hover/sidebar:translate-x-1 transition-transform duration-150">Establecimientos</span>
            </a>

            {{-- Separador --}}
            <div class="my-3 mx-2 border-t border-neutral-100"></div>

            {{-- Sección: Gestión --}}
            <div class="px-3 pt-1 pb-1.5">
                <span class="text-[0.65rem] font-bold uppercase tracking-widest text-neutral-400">Gestión</span>
            </div>


            {{-- Enlace: Preferencias --}}
            <a href="{{ route('preferencias.index') }}"
            class="group/sidebar flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                    {{ request()->routeIs('preferencias.*') ? 'text-white shadow-sm' : 'text-neutral-700 hover:text-[#00626A] hover:bg-neutral-100' }}"
            style="{{ request()->routeIs('preferencias.*') ? 'background: var(--color-primary-500); color: white;' : '' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="whitespace-pre group-hover/sidebar:translate-x-1 transition-transform duration-150">Preferencias</span>
            </a>


            <a href="{{ route('login') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-neutral-700 hover:bg-neutral-100">↗ Iniciar sesión</a>
            <a href="{{ route('principal.usuarios') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-neutral-700 hover:bg-neutral-100">♧ Usuarios <span class="ml-auto text-xs text-neutral-400">Demo</span></a>
            <a href="{{ route('principal.destinos') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-neutral-700 hover:bg-neutral-100">◇ Destinos <span class="ml-auto text-xs text-neutral-400">Demo</span></a>
            {{-- Enlace: Panel Operador --}}
            <a href="{{ route('operador.atractivos.index') }}?_operador_id_test=1"
               class="group/sidebar flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                      {{ request()->routeIs('operador.*') ? 'text-white shadow-sm' : 'text-neutral-700 hover:text-[#00626A] hover:bg-neutral-100' }}"
               style="{{ request()->routeIs('operador.*') ? 'background: var(--color-primary-600); color: white;' : '' }}">
                <div class="shrink-0 transition-transform duration-150 group-hover/sidebar:scale-110">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <span class="whitespace-pre group-hover/sidebar:translate-x-1 transition-transform duration-150">Panel Operador</span>
                <span class="ml-auto text-[0.6rem] font-bold px-2 py-0.5 rounded-md"
                      style="background: var(--color-teal-light); color: var(--color-primary-700);">OPE</span>
            </a>

            {{-- Enlace: Panel Proveedor --}}
            <a href="{{ route('proveedor.establecimientos.index') }}?_proveedor_id_test=2"
               class="group/sidebar flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                      {{ request()->routeIs('proveedor.*') ? 'text-white shadow-sm' : 'text-neutral-700 hover:text-[#00626A] hover:bg-neutral-100' }}"
               style="{{ request()->routeIs('proveedor.*') ? 'background: var(--color-primary-600); color: white;' : '' }}">
                <div class="shrink-0 transition-transform duration-150 group-hover/sidebar:scale-110">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                    </svg>
                </div>
                <span class="whitespace-pre group-hover/sidebar:translate-x-1 transition-transform duration-150">Panel Proveedor</span>
                <span class="ml-auto text-[0.6rem] font-bold px-2 py-0.5 rounded-md"
                      style="background: var(--color-amber-soft); color: var(--color-tertiary-700);">PRO</span>
            </a>
        </nav>

        {{-- Pie del drawer minimalista (sin usuario aquí, según requerimiento) --}}
        <div class="px-5 py-3 shrink-0 border-t border-neutral-100 text-center">
            <span class="text-[0.7rem] text-neutral-400 font-medium">TourismCloud AI &copy; {{ date('Y') }}</span>
        </div>
    </aside>

    {{-- ══════════════════════════════════════════════════════════════
         TOPBAR (Header Superior Fijo) — Ocupa TODO el ancho disponible
         El usuario se muestra ÚNICAMENTE aquí en la barra superior.
    ══════════════════════════════════════════════════════════════ --}}
    <header class="sticky top-0 z-30 w-full"
            style="background: linear-gradient(135deg, var(--color-primary-700) 0%, var(--color-primary-500) 100%); box-shadow: 0 2px 12px rgb(0 98 106 / 0.25);">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                {{-- Izquierda: Botón Hamburguesa + Logo --}}
                <div class="flex items-center gap-3">
                    <button onclick="openSidebar()"
                            class="p-2 -ml-2 rounded-lg transition-colors hover:bg-white/15 cursor-pointer text-white"
                            aria-label="Abrir menú">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    <a href="/" class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: rgba(255,255,255,0.2);">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
                            </svg>
                        </div>
                        <span class="hidden sm:inline" style="font-family: var(--font-display); font-weight: 800; font-size: 1.1rem; color: white; letter-spacing: -0.01em;">
                            TurismCloudIA
                        </span>
                    </a>
                </div>

                {{-- Derecha: Usuario (SOLO en el menú superior) --}}
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2.5 py-1.5 px-3 rounded-full bg-white/10 hover:bg-white/15 border border-white/20 transition-colors cursor-default">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 bg-white/20 border border-white/40">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div class="flex flex-col text-left leading-tight pr-1">
                            <span class="text-xs font-semibold text-white">Usuario</span>
                            <span class="text-[0.65rem] text-white/75 font-medium">Turista</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </header>

    {{-- Flash messages --}}
        {{-- Encabezado de la vista --}}
    @hasSection('header')
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
            @yield('header')
        </div>
    @endif

    {{-- Mensajes de éxito --}}
    @if(session('success') || session('exito'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <div class="tc-alert-exito" role="alert">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ session('success') ?? session('exito') }}
            </div>
        </div>
    @endif

    {{-- Mensajes de error --}}
    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <div class="tc-alert-error" role="alert">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('error') }}
            </div>
        </div>
    @endif

    {{-- Errores de validación --}}
    @if($errors->any())
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <div class="tc-alert-error" role="alert">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
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

    {{-- ══ SIDEBAR DRAWER — JavaScript (vanilla) ══════════════════ --}}
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

        // Cerrar con ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeSidebar();
        });
    })();
    </script>

    {{-- ══ BOTÓN FLOTANTE TEMPORAL — DEV ONLY ══════════════════════════
         Eliminar cuando el módulo de auth esté integrado.
         Permite navegar al panel de Operador y Proveedor sin login.
    ══════════════════════════════════════════════════════════════════ --}}
    <div id="dev-panel-dock" style="position: fixed; bottom: 1.25rem; right: 1.25rem; z-index: 9999; display: flex; flex-direction: column; align-items: flex-end; gap: 0.5rem;">
        <div id="dev-panel-links"
             style="display: none; flex-direction: column; gap: 0.5rem; align-items: flex-end; margin-bottom: 0.25rem;">
            <a href="{{ route('operador.atractivos.index') }}?_operador_id_test=1"
               style="background: var(--color-primary-600); color: white; font-size: 0.78rem; font-weight: 700; padding: 0.5rem 0.875rem; border-radius: 0.625rem; text-decoration: none; box-shadow: 0 4px 12px rgba(0,98,106,0.4); white-space: nowrap; display: flex; align-items: center; gap: 0.375rem;">
                🗺️ Panel Operador
            </a>
            <a href="{{ route('proveedor.establecimientos.index') }}?_proveedor_id_test=2"
               style="background: var(--color-tertiary-500); color: white; font-size: 0.78rem; font-weight: 700; padding: 0.5rem 0.875rem; border-radius: 0.625rem; text-decoration: none; box-shadow: 0 4px 12px rgba(180,100,0,0.3); white-space: nowrap; display: flex; align-items: center; gap: 0.375rem;">
                🏢 Panel Proveedor
            </a>
        </div>
        <button onclick="var d=document.getElementById('dev-panel-links'); d.style.display=d.style.display==='none'?'flex':'none';"
                title="Acceso rápido a paneles (DEV)"
                style="background: #1a2232; color: white; font-size: 0.7rem; font-weight: 800; padding: 0.4rem 0.65rem; border-radius: 0.5rem; border: none; cursor: pointer; letter-spacing: 0.08em; box-shadow: 0 4px 12px rgba(0,0,0,0.3); display: flex; align-items: center; gap: 0.3rem;">
            <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
            DEV
        </button>
    </div>

    @stack('scripts')

</body>
</html>
