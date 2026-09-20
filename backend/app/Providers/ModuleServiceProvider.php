<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class ModuleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Registramos el namespace de vistas para cada módulo
        View::addNamespace('dashboard', base_path('app/Modules/Dashboard/Views'));
        View::addNamespace('agenda', base_path('app/Modules/Agenda/Views'));
        View::addNamespace('itinerarios', base_path('app/Modules/Itinerarios/Views'));
    }
}