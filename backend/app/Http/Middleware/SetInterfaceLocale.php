<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetInterfaceLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('interface_locale')
            ?? $request->cookie('tc_locale', 'es');
        App::setLocale(in_array($locale, ['es', 'en'], true) ? $locale : 'es');

        return $next($request);
    }
}
