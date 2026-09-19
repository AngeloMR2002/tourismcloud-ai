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

    {{-- ══════════════════════════════════════════════════════
         NAVBAR PRINCIPAL
    ═══════════════════════════════════════════════════════ --}}
    <header style="background: linear-gradient(135deg, var(--color-primary-700) 0%, var(--color-primary-500) 100%);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                {{-- Logo / Brand --}}
                <a href="/" class="flex items-center gap-2.5 group">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                         style="background: rgba(255,255,255,0.2);">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
                        </svg>
                    </div>
                    <span style="font-family: var(--font-display); font-weight: 800; font-size: 1.125rem; color: white; letter-spacing: -0.01em;">
                        TourismCloud <span style="opacity: 0.85;">AI</span>
                    </span>
                </a>

                {{-- Nav links --}}
                <nav class="hidden md:flex items-center gap-6">
                    <a href="{{ route('catalogo.atractivos.index') }}"
                       class="text-sm font-medium transition-colors"
                       style="color: rgba(255,255,255,0.85);"
                       onmouseover="this.style.color='white'" onmouseout="this.style.color='rgba(255,255,255,0.85)'">
                        Atractivos
                    </a>
                    <a href="{{ route('catalogo.establecimientos.index') }}"
                       class="text-sm font-medium transition-colors"
                       style="color: rgba(255,255,255,0.85);"
                       onmouseover="this.style.color='white'" onmouseout="this.style.color='rgba(255,255,255,0.85)'">
                        Establecimientos
                    </a>
                </nav>

                {{-- Panel links --}}
                <div class="flex items-center gap-2">
                    <a href="{{ route('operador.atractivos.index') }}"
                       class="hidden sm:inline-flex text-xs font-medium px-3 py-1.5 rounded-lg transition-colors"
                       style="background: rgba(255,255,255,0.15); color: white;"
                       onmouseover="this.style.background='rgba(255,255,255,0.25)'"
                       onmouseout="this.style.background='rgba(255,255,255,0.15)'">
                        Panel Operador
                    </a>
                    <a href="{{ route('proveedor.establecimientos.index') }}"
                       class="hidden sm:inline-flex text-xs font-medium px-3 py-1.5 rounded-lg transition-colors"
                       style="background: rgba(255,255,255,0.15); color: white;"
                       onmouseover="this.style.background='rgba(255,255,255,0.25)'"
                       onmouseout="this.style.background='rgba(255,255,255,0.15)'">
                        Panel Proveedor
                    </a>
                </div>
            </div>
        </div>
    </header>

    {{-- ══════════════════════════════════════════════════════
         MENSAJES FLASH
    ═══════════════════════════════════════════════════════ --}}
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

    {{-- ══════════════════════════════════════════════════════
         CONTENIDO PRINCIPAL
    ═══════════════════════════════════════════════════════ --}}
    <main class="flex-1">
        @yield('content')
    </main>

    {{-- ══════════════════════════════════════════════════════
         FOOTER
    ═══════════════════════════════════════════════════════ --}}
    <footer style="background: var(--color-primary-900); color: rgba(255,255,255,0.6);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-sm">
                    © {{ date('Y') }} TourismCloud AI. Todos los derechos reservados.
                </p>
                <div class="flex gap-4 text-sm">
                    <a href="{{ route('catalogo.atractivos.index') }}"
                       class="hover:text-white transition-colors">Atractivos</a>
                    <a href="{{ route('catalogo.establecimientos.index') }}"
                       class="hover:text-white transition-colors">Establecimientos</a>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
