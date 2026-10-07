<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $metaDescription ?? 'Lugares reales, actividades verificadas y propuestas de recorrido con fuentes consultables.' }}">
    <title>{{ $title ?? 'TourismCloud AI' }} | Catálogo turístico</title>
    @vite(['app/Modules/Actividades/Resources/css/app.css', 'app/Modules/Actividades/Resources/js/app.js'])
</head>
<body class="tc-site min-h-full flex flex-col antialiased bg-surface">
    <a href="#contenido" class="tc-skip-link">Saltar al contenido</a>
    <header class="tc-header tc-team-header">
        <div class="tc-header-inner">
            <div class="flex items-center gap-3">
                <button type="button" class="tc-menu-toggle" data-open-navigation aria-label="Abrir menú" aria-controls="tc-navigation-drawer" aria-expanded="false">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round"/></svg>
                </button>
                <a href="{{ route('catalogo.actividades.index') }}" class="tc-brand" aria-label="TourismCloud AI · Inicio">
                    <span class="tc-brand-symbol" aria-hidden="true"><svg viewBox="0 0 32 32" fill="none"><circle cx="16" cy="16" r="11" stroke="currentColor" stroke-width="1.8"/><path d="M5 16h22M16 5c-9 9-9 13 0 22M16 5c9 9 9 13 0 22" stroke="currentColor" stroke-width="1.6"/></svg></span>
                    <span>TourismCloud<span class="tc-brand-ai">AI</span><small>Explora con información real</small></span>
                </a>
            </div>
            <nav aria-label="Navegación principal" class="tc-nav tc-top-links">@include('actividades::layouts.navigation')</nav>
            <div class="tc-mobile-access">
                @auth
                    <span class="tc-user-label">{{ auth()->user()->name }}</span>
                @else
                    <a href="{{ route('login') }}" class="tc-header-access">Acceso ↗</a>
                @endauth
            </div>
        </div>
    </header>
    <dialog id="tc-navigation-drawer" class="tc-navigation-drawer" aria-labelledby="tc-drawer-title">
        <div class="tc-drawer-heading">
            <span class="tc-drawer-brand">TourismCloud <span>AI</span></span>
            <form method="dialog"><button class="tc-drawer-close" aria-label="Cerrar menú" autofocus>×</button></form>
        </div>
        <div class="tc-drawer-content">
            <p class="tc-eyebrow">TU ESPACIO PARA EXPLORAR</p>
            <h2 id="tc-drawer-title">Descubre tu próximo recorrido</h2>
            <nav aria-label="Menú lateral" class="tc-nav tc-drawer-nav">@include('actividades::layouts.navigation')</nav>
        </div>
        <div class="tc-drawer-footer"><span class="tc-chip">P3 · Actividades + Rutas</span><p>Información real. Fuentes a un clic.</p></div>
    </dialog>
    @foreach(['exito' => 'tc-alert-exito', 'error' => 'tc-alert-error'] as $clave => $clase)
        @if(session($clave))<div class="max-w-7xl w-full mx-auto px-4 pt-4"><p role="alert" class="{{ $clase }}">{{ session($clave) }}</p></div>@endif
    @endforeach
    <main id="contenido" class="tc-content flex-1 pb-12">@yield('content')</main>
    <footer class="tc-footer">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-10">
            <div class="tc-footer-top">
                <div><p class="font-display text-xl font-bold">TourismCloud <span class="text-teal-200">AI</span></p><p class="mt-2 max-w-md text-sm text-teal-100/80">Tu próximo recorrido empieza con información clara.<br>Actividades y rutas con fuentes consultables.</p></div>
                <div class="text-sm"><p class="tc-footer-label">EXPLORA</p><a href="{{ route('catalogo.actividades.index') }}">Actividades</a><a href="{{ route('catalogo.rutas.index') }}">Rutas y circuitos</a></div>
                <div class="text-sm max-w-sm"><p class="tc-footer-label">ANTES DE VIAJAR</p><p class="text-teal-100/80">Confirma precios, acceso y disponibilidad con el responsable. Las reservas se realizan fuera de TourismCloud.</p></div>
            </div>
            <div class="tc-footer-bottom"><p>© {{ date('Y') }} TourismCloud AI · P3 Actividades + Rutas</p><p>Cartografía: © <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener noreferrer">OpenStreetMap contributors · ODbL ↗</a></p></div>
        </div>
    </footer>
    @stack('scripts')
</body>
</html>
