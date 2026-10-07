<?php

namespace App\Modules\Actividades\Providers;

use App\Models\User;
use App\Modules\Actividades\Console\Commands\ImportarLugares;
use App\Modules\Actividades\Console\Commands\CrearOperador;
use App\Modules\Actividades\Console\Commands\PrepararTurismo;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Support\ServiceProvider;

/**
 * P3: módulo Actividades. Responsable: Hernandez.
 * Rama del equipo: feature/actividades.
 */
class ActividadesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../Config/tourism.php', 'tourism');
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes.php');
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'actividades');
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');

        Gate::define('gestionar-catalogo', fn (User $user): bool => in_array($user->rol, ['administrador', 'operador'], true));
        Gate::define('revisar-catalogo', fn (User $user): bool => $user->rol === 'administrador');
        RateLimiter::for('login', fn (Request $request) => Limit::perMinute(5)
            ->by(Str::lower($request->string('email')->toString()).'|'.$request->ip()));

        if ($this->app->runningInConsole()) {
            $this->commands([ImportarLugares::class, CrearOperador::class, PrepararTurismo::class]);

            $this->callAfterResolving(Schedule::class, function (Schedule $schedule): void {
                $schedule->command('tourism:importar-lugares')
                    ->dailyAt('03:00')
                    ->timezone('America/Lima')
                    ->withoutOverlapping(10);
            });
        }
    }
}
