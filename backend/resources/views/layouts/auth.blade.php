<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="tc-locale-endpoint" content="{{ route('interface.locale') }}">
    <meta name="tc-rate-endpoint" content="{{ route('exchange-rate.pen-usd') }}">
    <title>TourismCloud AI - {{ __('ui.authentication') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo_tourism.png') }}">
    
    <!-- Fuentes: Inter / Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700" rel="stylesheet" />
    
    <!-- Alpine.js (Para la interactividad del botón de ver contraseña) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="tc-auth-shell font-sans antialiased text-gray-900">
    <div class="tc-auth-shell__glow tc-auth-shell__glow--one" aria-hidden="true"></div>
    <div class="tc-auth-shell__glow tc-auth-shell__glow--two" aria-hidden="true"></div>
    @yield('content')

    <div class="tc-auth-utilities" role="group" aria-label="{{ __('ui.platform_preferences') }}">
        {{-- Dropdown Idioma --}}
        <div class="tc-dropdown-wrap" data-dropdown="language">
            <button type="button" class="tc-utility-control" data-dropdown-trigger aria-haspopup="menu" aria-expanded="false" title="{{ __('ui.select_language') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><circle cx="12" cy="12" r="8" stroke-width="1.7"/><path d="M4 12h16M12 4c2.1 2.2 3.2 4.9 3.2 8S14.1 17.8 12 20M12 4C9.9 6.2 8.8 8.9 8.8 12s1.1 5.8 3.2 8" stroke-width="1.7" stroke-linecap="round"/></svg>
                <span data-current-language>{{ app()->getLocale() === 'en' ? 'English' : 'Español' }}</span>
            </button>
            <div class="tc-dropdown-menu" role="menu" hidden>
                <button type="button" class="tc-dropdown-item" data-language-option="es" role="menuitem" aria-pressed="{{ app()->getLocale() === 'es' ? 'true' : 'false' }}">
                    <span class="tc-dropdown-mark">ES</span>
                    <span>Español</span>
                    <svg class="tc-dropdown-check" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                </button>
                <button type="button" class="tc-dropdown-item" data-language-option="en" role="menuitem" aria-pressed="{{ app()->getLocale() === 'en' ? 'true' : 'false' }}">
                    <span class="tc-dropdown-mark">EN</span>
                    <span>English</span>
                    <svg class="tc-dropdown-check" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                </button>
            </div>
        </div>

        {{-- Dropdown Moneda --}}
        <div class="tc-dropdown-wrap" data-dropdown="currency">
            <button type="button" class="tc-utility-control" data-dropdown-trigger aria-haspopup="menu" aria-expanded="false" title="{{ __('ui.select_currency') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><circle cx="12" cy="12" r="8" stroke-width="1.7"/><path d="M15.2 8.8c-.7-.6-1.8-.9-3-.9-1.7 0-2.9.8-2.9 2 0 3.1 5.8 1.1 5.8 4.1 0 1.3-1.2 2.1-3 2.1-1.3 0-2.5-.4-3.3-1.1M12 6.6v10.8" stroke-width="1.7" stroke-linecap="round"/></svg>
                <span data-current-currency>S/ PEN</span>
            </button>
            <div class="tc-dropdown-menu" role="menu" hidden>
                <button type="button" class="tc-dropdown-item" data-currency-option="PEN" role="menuitem" aria-pressed="true">
                    <span class="tc-dropdown-mark">S/</span>
                    <span>{{ __('ui.soles') }}</span>
                    <svg class="tc-dropdown-check" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                </button>
                <button type="button" class="tc-dropdown-item" data-currency-option="USD" role="menuitem" aria-pressed="false">
                    <span class="tc-dropdown-mark">$</span>
                    <span>{{ __('ui.dollars') }}</span>
                    <svg class="tc-dropdown-check" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                </button>
                <p data-tc-rate-status class="tc-dropdown-rate-status" role="status">{{ __('ui.rate_loading') }}</p>
            </div>
        </div>

        <button type="button" class="tc-utility-control" data-help-open aria-haspopup="dialog" aria-controls="help-dialog">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><circle cx="12" cy="12" r="8" stroke-width="1.7"/><path d="M9.8 9.5a2.4 2.4 0 1 1 4.4 1.4c-.8 1-2.2 1.4-2.2 3M12 16.8h.01" stroke-width="1.7" stroke-linecap="round"/></svg>
            <span>{{ __('ui.help') }}</span>
        </button>
    </div>

    @include('components.preference-dialogs')
</body>
</html>
