<!DOCTYPE html>

<html lang="es">

<head>
    <meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>
    @yield('title', 'TourismCloud AI')
</title>

@vite([
    'resources/css/app.css',
    'resources/js/app.js'
])

</head>

<body class="min-h-screen bg-slate-100 text-slate-800">

<div class="min-h-screen">

    {{-- Barra superior --}}
    <header class="border-b border-slate-200 bg-white">

        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-6">

            <a
                href="{{ url('/') }}"
                class="text-xl font-semibold tracking-tight text-slate-900"
            >
                TourismCloud AI
            </a>

            <div class="text-sm text-slate-500">
                Plataforma turística inteligente
            </div>

        </div>

    </header>


    <div class="mx-auto flex max-w-7xl">

        {{-- Menú lateral --}}
        <aside class="hidden min-h-[calc(100vh-4rem)] w-64 border-r border-slate-200 bg-white lg:block">

            <nav class="space-y-1 p-4">

                <a
                    href="{{ url('/') }}"
                    class="flex items-center rounded-lg px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-100 hover:text-slate-900"
                >
                    Inicio
                </a>

                <a
                    href="{{ route('preferencias.index') }}"
                    class="flex items-center rounded-lg px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-100 hover:text-slate-900"
                >
                    Preferencias
                </a>

                {{-- Futuros módulos --}}

                <a
                    href="#"
                    class="flex items-center rounded-lg px-4 py-2.5 text-sm font-medium text-slate-400"
                >
                    Recomendaciones
                </a>

                <a
                    href="#"
                    class="flex items-center rounded-lg px-4 py-2.5 text-sm font-medium text-slate-400"
                >
                    Itinerarios
                </a>

            </nav>

        </aside>


        {{-- Contenido principal --}}
        <main class="min-w-0 flex-1 px-4 py-6 sm:px-6 lg:px-8">

            {{-- Mensaje de éxito --}}
            @if (session('success'))

                <div
                    class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800"
                >
                    {{ session('success') }}
                </div>

            @endif


            {{-- Errores de validación --}}
            @if ($errors->any())

                <div
                    class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"
                >

                    <p class="mb-2 font-semibold">
                        Se encontraron errores:
                    </p>

                    <ul class="list-inside list-disc space-y-1">

                        @foreach ($errors->all() as $error)

                            <li>
                                {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            @endif


            {{-- Título de página --}}
            @hasSection('header')

                <div class="mb-6">
                    @yield('header')
                </div>

            @endif


            {{-- Contenido de cada módulo --}}
            @yield('content')

        </main>

    </div>


    {{-- Pie de página --}}
    <footer class="border-t border-slate-200 bg-white">

        <div class="mx-auto max-w-7xl px-6 py-4 text-center text-sm text-slate-500">

            TourismCloud AI

        </div>

    </footer>

</div>

</body>

</html>
