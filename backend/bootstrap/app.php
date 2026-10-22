<?php

use App\Http\Middleware\SetInterfaceLocale;
use App\Modules\Usuarios\Http\Middleware\RoleMiddleware;
use App\Modules\Usuarios\Models\Usuario;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Railway terminates HTTPS at its proxy. Trust forwarded headers so
        // Laravel generates HTTPS URLs for Vite assets and redirects.
        $middleware->trustProxies(at: '*');

        $middleware->web(append: [SetInterfaceLocale::class]);

        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);

        // Si un usuario ya logueado entra a /login, va a su panel
        $middleware->redirectUsersTo(function (Request $request) {
            /** @var Usuario|null $user */
            $user = $request->user();

            return $user ? $user->homeUrl() : '/';
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
