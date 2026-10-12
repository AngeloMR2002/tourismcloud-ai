<?php

use App\Providers\AppServiceProvider;
use App\Modules\Atractivos\Providers\AtractivoServiceProvider;
use App\Modules\Destinos\Providers\DestinoServiceProvider;
use App\Modules\Establecimientos\Providers\EstablecimientoServiceProvider;
use App\Modules\Preferencias\Providers\PreferenciaServiceProvider;
use App\Modules\Usuarios\Providers\UsuarioServiceProvider;
use App\Modules\Actividades\Providers\ActividadServiceProvider;
use App\Modules\Rutas\Providers\RutaServiceProvider;
use App\Modules\Dashboard\Providers\DashboardServiceProvider;
use App\Modules\Itinerarios\Providers\ItinerarioServiceProvider;
use App\Modules\Agenda\Providers\AgendaServiceProvider;

return [
    AppServiceProvider::class,
    UsuarioServiceProvider::class,
    DestinoServiceProvider::class,
    AtractivoServiceProvider::class,
    EstablecimientoServiceProvider::class,
    PreferenciaServiceProvider::class,
    ActividadServiceProvider::class,
    RutaServiceProvider::class,
    DashboardServiceProvider::class,
    ItinerarioServiceProvider::class,
    AgendaServiceProvider::class,
];
