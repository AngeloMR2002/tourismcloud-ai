<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Los archivos son demasiado pesados | TourismCloud AI</title>
    <style>
        :root {
            --color-primary: #00626A;
            --color-primary-dark: #08565D;
            --color-primary-light: #E6F4F5;
            --color-surface: #FAF8FF;
            --color-card: #FFFFFF;
            --color-text: #1E293B;
            --color-text-muted: #64748B;
            --color-border: #E2E8F0;
            --font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font-family);
            background-color: var(--color-surface);
            color: var(--color-text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .error-card {
            background-color: var(--color-card);
            border: 1px solid var(--color-border);
            border-radius: 1rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.03);
            max-width: 32rem;
            width: 100%;
            padding: 2.5rem 2rem;
            text-align: center;
        }

        .icon-container {
            width: 4rem;
            height: 4rem;
            border-radius: 9999px;
            background-color: #FEF3C7;
            color: #D97706;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .badge-code {
            display: inline-block;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--color-primary);
            background-color: var(--color-primary-light);
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            margin-bottom: 1rem;
        }

        h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--color-text);
            margin-bottom: 1rem;
            line-height: 1.3;
        }

        p {
            font-size: 0.95rem;
            line-height: 1.6;
            color: var(--color-text-muted);
            margin-bottom: 2rem;
        }

        .actions {
            display: flex;
            justify-content: center;
            gap: 1rem;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            background-color: var(--color-primary);
            color: #FFFFFF;
            font-size: 0.95rem;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.15s ease, transform 0.15s ease;
        }

        .btn-back:hover {
            background-color: var(--color-primary-dark);
            transform: translateY(-1px);
        }

        .btn-back:active {
            transform: translateY(0);
        }

        .brand-footer {
            margin-top: 2rem;
            font-size: 0.8rem;
            color: #94A3B8;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="icon-container" aria-hidden="true">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="17 8 12 3 7 8"></polyline>
                <line x1="12" y1="3" x2="12" y2="15"></line>
            </svg>
        </div>
        <div class="badge-code">Error 413</div>
        <h1>Los archivos son demasiado pesados</h1>
        <p>El tamaño total de los archivos que intentaste subir supera el límite permitido (máximo 2 MB por imagen y 8 MB en total). Reduce el tamaño de las imágenes o sube menos archivos e inténtalo de nuevo.</p>
        <div class="actions">
            <button type="button" class="btn-back" onclick="if (window.history.length > 1) { window.history.back(); } else { window.location.href = '{{ url('/') }}'; }">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Volver al formulario
            </button>
        </div>
        <div class="brand-footer">
            TourismCloud AI
        </div>
    </div>
</body>
</html>
