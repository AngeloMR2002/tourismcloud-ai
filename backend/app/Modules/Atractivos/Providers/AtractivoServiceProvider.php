<?php

namespace App\Modules\Atractivos\Providers;

use Illuminate\Support\ServiceProvider;

class AtractivoServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes.php');

        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'atractivos');
    }
}