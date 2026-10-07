<?php

use App\Modules\Actividades\Providers\ActividadesServiceProvider;
use App\Modules\Rutas\Providers\RutasServiceProvider;
use App\Providers\AppServiceProvider;

return [
    AppServiceProvider::class,
    ActividadesServiceProvider::class,
    RutasServiceProvider::class,
];
