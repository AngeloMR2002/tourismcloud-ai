<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Los archivos son demasiado pesados - TourismCloud AI</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|plus-jakarta-sans:700,800" rel="stylesheet" />
    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Inter', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #FAF8FF;
            color: #1a2232;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            position: relative;
            overflow-x: hidden;
        }
        .glow-bg {
            position: absolute;
            width: 480px;
            height: 480px;
            border-radius: 50%;
            filter: blur(90px);
            opacity: 0.45;
            pointer-events: none;
            z-index: 0;
        }
        .glow-one {
            top: -100px;
            right: -100px;
            background: #c0e4e7;
        }
        .glow-two {
            bottom: -100px;
            left: -100px;
            background: #fbe5c4;
        }
        .card {
            position: relative;
            z-index: 1;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 1.25rem;
            box-shadow: 0 20px 25px -5px rgba(0, 98, 106, 0.06), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
            max-width: 32rem;
            width: 100%;
            padding: 2.5rem 2rem;
            text-align: center;
        }
        .icon-wrap {
            width: 4.5rem;
            height: 4.5rem;
            margin: 0 auto 1.5rem;
            background: #fdf6ec;
            border: 1px solid #f7cc93;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #85480C;
        }
        .icon-wrap svg {
            width: 2.5rem;
            height: 2.5rem;
        }
        .status-badge {
            display: inline-block;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            background: #e6f4f5;
            color: #00626A;
            margin-bottom: 1rem;
        }
        h1 {
            font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;
            font-size: 1.5rem;
            font-weight: 800;
            color: #1a2232;
            line-height: 1.25;
            margin-bottom: 1rem;
        }
        p {
            font-size: 0.9375rem;
            line-height: 1.6;
            color: #4b5563;
            margin-bottom: 2rem;
        }
        .btn-back {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            background-color: #00626A;
            color: #ffffff;
            font-weight: 600;
            font-size: 0.9375rem;
            padding: 0.75rem 1.75rem;
            border-radius: 0.75rem;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.15s ease-in-out;
            box-shadow: 0 4px 6px -1px rgba(0, 98, 106, 0.2);
        }
        .btn-back:hover {
            background-color: #08565D;
            transform: translateY(-1px);
            box-shadow: 0 6px 10px -1px rgba(0, 98, 106, 0.3);
        }
        .btn-back svg {
            width: 1.25rem;
            height: 1.25rem;
        }
    </style>
</head>
<body>
    <div class="glow-bg glow-one" aria-hidden="true"></div>
    <div class="glow-bg glow-two" aria-hidden="true"></div>

    <main class="card" role="main">
        <div class="icon-wrap" aria-hidden="true">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
            </svg>
        </div>

        <span class="status-badge">Error 413 · Carga excedida</span>

        <h1>Los archivos son demasiado pesados</h1>

        <p>El tamaño total de los archivos que intentaste subir supera el límite permitido (máximo 2 MB por imagen y 8 MB en total). Reduce el tamaño de las imágenes o sube menos archivos e inténtalo de nuevo.</p>

        <div>
            <a href="{{ url('/') }}"
               onclick="if (window.history.length > 1) { window.history.back(); return false; }"
               class="btn-back">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver al formulario
            </a>
        </div>
    </main>
</body>
</html>
