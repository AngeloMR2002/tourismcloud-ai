<?php

namespace App\Modules\Itinerarios\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ItinerarioServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::middleware('web')->group(__DIR__.'/../routes.php');
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'itinerarios');
    }
}
