<?php

namespace App\Modules\Destinos\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class DestinoServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Route::middleware('web')->group(__DIR__ . '/../routes.php');

        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'destinos');
    }
}
