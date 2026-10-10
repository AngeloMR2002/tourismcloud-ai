<?php

use App\Providers\AppServiceProvider;
use App\Modules\Atractivos\Providers\AtractivoServiceProvider;
use App\Modules\Establecimientos\Providers\EstablecimientoServiceProvider;
use App\Modules\Preferencias\Providers\PreferenciaServiceProvider;

return [
    AppServiceProvider::class,
    EstablecimientoServiceProvider::class,
    AtractivoServiceProvider::class,
    PreferenciaServiceProvider::class,
];