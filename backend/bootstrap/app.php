<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            $modulesPath = app_path('Modules');
            if (is_dir($modulesPath)) {
                foreach (glob($modulesPath . '/*/routes.php') as $routeFile) {
                    Route::middleware('web')->group($routeFile);
                }
            }
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Modules\Usuarios\Http\Middleware\RoleMiddleware::class,
        ]);

        // Si un usuario ya logueado entra a /login, va a su panel
        $middleware->redirectUsersTo(function (Request $request) {
            /** @var \App\Modules\Usuarios\Models\Usuario|null $user */
            $user = $request->user();
            return $user ? $user->homeUrl() : '/';
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
