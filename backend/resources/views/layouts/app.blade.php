<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TourismCloud - Agencia & Paquetes Turísticos</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen flex flex-col">

    <!-- Header / Navbar Superior -->
    <header class="bg-white/95 backdrop-blur-md border-b border-slate-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            
            <!-- Logo -->
            <a href="/dashboard" class="flex items-center gap-3">
                <div class="p-2 bg-purple-600 text-white rounded-xl shadow-md shadow-purple-200">
                    <i data-lucide="compass" class="w-6 h-6"></i>
                </div>
                <div>
                    <span class="font-extrabold text-xl tracking-tight text-slate-900 block leading-none">TourismCloud</span>
                    <span class="text-[10px] text-purple-600 font-semibold uppercase tracking-wider">Agencia & Experiencias</span>
                </div>
            </a>

            <!-- Menu de Navegación -->
            <nav class="hidden md:flex items-center gap-8 text-sm font-medium">
                <a href="/dashboard" class="transition-colors hover:text-purple-600 {{ request()->is('dashboard*') ? 'text-purple-600 font-semibold' : 'text-slate-600' }}">Inicio</a>
                <a href="/itinerarios" class="transition-colors hover:text-purple-600 flex items-center gap-1 {{ request()->is('itinerarios*') ? 'text-purple-600 font-semibold' : 'text-slate-600' }}">
                    Paquetes <i data-lucide="chevron-down" class="w-3.5 h-3.5"></i>
                </a>
                <a href="/agenda" class="transition-colors hover:text-purple-600 flex items-center gap-1 {{ request()->is('agenda*') ? 'text-purple-600 font-semibold' : 'text-slate-600' }}">
                    Agenda & Reservas <i data-lucide="chevron-down" class="w-3.5 h-3.5"></i>
                </a>
                <a href="#" class="text-slate-600 hover:text-purple-600 transition-colors">Promociones</a>
            </nav>

            <!-- Acciones / Perfil -->
            <div class="flex items-center gap-4 text-sm">
                <button class="hidden sm:flex items-center gap-2 text-slate-700 font-medium hover:text-purple-600 transition-colors">
                    <i data-lucide="user" class="w-4 h-4"></i> Mi Cuenta
                </button>
                <a href="/itinerarios" class="px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg shadow-md shadow-purple-200 transition-all flex items-center gap-2">
                    <i data-lucide="search" class="w-4 h-4"></i> Buscar viajes
                </a>
            </div>
        </div>
    </header>

    <!-- Dynamic Content -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer Simple -->
    <footer class="bg-slate-900 text-slate-400 py-8 text-center text-xs border-t border-slate-800">
        <p>© 2026 TourismCloud - Sistema Modular Turístico Base</p>
    </footer>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>