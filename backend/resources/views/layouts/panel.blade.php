<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel') | TourismCloud AI</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex flex-col" style="background: #f8fafb;">

    {{-- NAVBAR DEL PANEL --}}
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

                {{-- Links del panel (inyectados por cada vista) --}}
                <nav class="hidden md:flex items-center gap-8">
                    @yield('panel-nav-links')
                    <span class="text-sm font-medium cursor-not-allowed" style="color: rgba(255,255,255,0.5);">
                        Estadísticas
                    </span>
                </nav>

                {{-- Rol del usuario --}}
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center" style="background: rgba(255,255,255,0.2); border: 2px solid rgba(255,255,255,0.45);">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <span class="hidden sm:inline text-sm font-semibold" style="color: rgba(255,255,255,0.95);">
                        @yield('panel-rol-label', 'Panel')
                    </span>
                </div>
            </div>
        </div>
    </header>

    {{-- Flash messages --}}
    @if(session('exito'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <div class="tc-alert-exito">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ session('exito') }}
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <div class="tc-alert-error">
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

    @stack('scripts')
</body>
</html>
