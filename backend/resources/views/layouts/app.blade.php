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

    {{-- NAVBAR --}}
    <header style="background: linear-gradient(135deg, var(--color-primary-700) 0%, var(--color-primary-500) 100%); box-shadow: 0 2px 12px rgb(0 98 106 / 0.25);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                {{-- Logo --}}
                <a href="/" class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: rgba(255,255,255,0.2);">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
                        </svg>
                    </div>
                    <span style="font-family: var(--font-display); font-weight: 800; font-size: 1.1rem; color: white; letter-spacing: -0.01em;">
                        TurismCloudIA
                    </span>
                </a>

                {{-- Links centrales --}}
                <nav class="hidden md:flex items-center gap-8">
                    <a href="{{ route('catalogo.atractivos.index') }}"
                       class="text-sm font-semibold pb-0.5 transition-all"
                       style="color: white; border-bottom: 2px solid {{ request()->routeIs('catalogo.atractivos.*') ? 'rgba(255,255,255,0.9)' : 'transparent' }};">
                        Atractivos
                    </a>
                    <a href="{{ route('catalogo.establecimientos.index') }}"
                       class="text-sm font-semibold pb-0.5 transition-all"
                       style="color: {{ request()->routeIs('catalogo.establecimientos.*') ? 'white' : 'rgba(255,255,255,0.8)' }}; border-bottom: 2px solid {{ request()->routeIs('catalogo.establecimientos.*') ? 'rgba(255,255,255,0.9)' : 'transparent' }};">
                        Establecimientos
                    </a>
                </nav>

                {{-- Avatar + Usuario --}}
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center" style="background: rgba(255,255,255,0.2); border: 2px solid rgba(255,255,255,0.45);">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <span class="hidden sm:inline text-sm font-medium" style="color: rgba(255,255,255,0.9);">Usuario</span>
                </div>
            </div>
        </div>
    </header>

    {{-- Flash messages --}}
    @if(session('exito'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <div class="tc-alert-exito" role="alert">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ session('exito') }}
            </div>
        </div>
    @endif
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
