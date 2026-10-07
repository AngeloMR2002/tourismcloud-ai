<?php

return [
    'verification_days' => 180,
    'price_days' => 7,
    'overpass_url' => env('OVERPASS_URL', 'https://overpass-api.de/api/interpreter'),
    'import_cache_seconds' => 86400,
    'osm_areas' => ['Chimbote', 'Nuevo Chimbote'],
];
