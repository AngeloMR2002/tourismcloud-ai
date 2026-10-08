<?php

use App\Providers\AppServiceProvider;
use App\Modules\Atractivos\Providers\AtractivoServiceProvider;
use App\Modules\Establecimientos\Providers\EstablecimientoServiceProvider;

return [
    AppServiceProvider::class,
    EstablecimientoServiceProvider::class,
    AtractivoServiceProvider::class,
];
