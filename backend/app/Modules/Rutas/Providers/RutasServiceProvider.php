<?php

namespace App\Modules\Rutas\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * P3: módulo Rutas. Responsable: Hernandez.
 */
class RutasServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes.php');
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'rutas');
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
    }
}
