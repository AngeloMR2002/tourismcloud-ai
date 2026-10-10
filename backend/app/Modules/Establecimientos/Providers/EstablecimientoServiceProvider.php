<?php

namespace App\Modules\Establecimientos\Providers;

use Illuminate\Support\ServiceProvider;

class EstablecimientoServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes.php');

        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'establecimientos');
    }
}