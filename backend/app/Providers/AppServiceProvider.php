<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Permite a Laravel buscar plantillas en los directorios Resources/views de cada módulo
        // para que llamadas como view('usuarios.auth.login') o view('destinos.index') funcionen directamente.
        if (is_dir(app_path('Modules'))) {
            foreach (glob(app_path('Modules/*/Resources/views')) as $moduleViews) {
                View::addLocation($moduleViews);
            }
        }
    }
}
