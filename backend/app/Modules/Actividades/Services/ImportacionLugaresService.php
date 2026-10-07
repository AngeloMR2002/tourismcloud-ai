<?php

namespace App\Modules\Actividades\Services;

use App\Models\Atractivo;
use App\Models\Destino;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class ImportacionLugaresService
{
    /** @return array{nuevos: int, actualizados: int, conservados: int} */
    public function importar(Destino $destino): array
    {
        if (! in_array($destino->ciudad, config('tourism.osm_areas'), true)) {
            throw ValidationException::withMessages(['destino_id' => 'La importación inicial está habilitada para Chimbote y Nuevo Chimbote.']);
        }

        return Cache::lock('osm-import-'.$destino->id, 90)->block(2, function () use ($destino): array {
            $payload = Cache::remember('osm-places-'.$destino->ciudad, config('tourism.import_cache_seconds'), function () use ($destino): array {
                $area = $destino->ciudad;
                $query = '[out:json][timeout:25];area["boundary"="administrative"]["name"="'.$area.'"]->.zona;'
                    .'(nwr(area.zona)["tourism"~"^(attraction|museum|viewpoint)$"]["name"];'
                    .'nwr(area.zona)["natural"="beach"]["name"];);out center;';
                $response = Http::asForm()->acceptJson()->withUserAgent('TourismCloud-Academic/1.0')
                    ->connectTimeout(5)->timeout(35)
                    ->post(config('tourism.overpass_url'), ['data' => $query])->throw();
                $payload = $response->json();
                if (! is_array($payload) || ! isset($payload['elements']) || ! is_array($payload['elements']) || isset($payload['remark'])) {
                    throw new RuntimeException('Overpass devolvió una respuesta incompleta. Intenta nuevamente más tarde.');
                }
                return $payload;
            });

            return DB::transaction(function () use ($destino, $payload): array {
                $counts = ['nuevos' => 0, 'actualizados' => 0, 'conservados' => 0];
                foreach (array_slice($payload['elements'], 0, 500) as $element) {
                    $tags = $element['tags'] ?? [];
                    $type = $element['type'] ?? null;
                    $id = $element['id'] ?? null;
                    $lat = $element['lat'] ?? ($element['center']['lat'] ?? null);
                    $lon = $element['lon'] ?? ($element['center']['lon'] ?? null);
                    if (! in_array($type, ['node', 'way', 'relation'], true) || ! is_numeric($id)
                        || ! is_string($tags['name'] ?? null) || ! is_numeric($lat) || ! is_numeric($lon)
                        || abs((float) $lat) > 90 || abs((float) $lon) > 180) {
                        continue;
                    }
                    $origin = 'osm/'.$type.'/'.$id;
                    $record = Atractivo::withTrashed()->firstOrNew(['origen_id' => $origin]);
                    if ($record->exists && ($record->trashed() || $record->estado_verificacion !== 'pendiente')) {
                        $counts['conservados']++;
                        continue;
                    }
                    $exists = $record->exists;
                    $website = $tags['website'] ?? ($tags['contact:website'] ?? null);
                    if (! is_string($website) || ! filter_var($website, FILTER_VALIDATE_URL)
                        || ! in_array(parse_url($website, PHP_URL_SCHEME), ['https', 'http'], true)) {
                        $website = null;
                    }
                    $record->fill([
                        'destino_id' => $destino->id,
                        'nombre' => Str::substr($tags['name'], 0, 150),
                        'descripcion' => 'Lugar registrado en OpenStreetMap. La actividad, accesos y servicios requieren revisión.',
                        'latitud' => $lat, 'longitud' => $lon, 'costo_entrada' => null,
                        'fuente_url' => 'https://www.openstreetmap.org/'.$type.'/'.$id,
                        'sitio_web_oficial' => $website,
                        'fecha_importacion' => now(), 'estado_verificacion' => 'pendiente',
                        'datos_origen' => Arr::only($tags, ['name', 'tourism', 'natural', 'website', 'contact:website', 'opening_hours']),
                        'estado' => 'activo',
                    ])->save();
                    $counts[$exists ? 'actualizados' : 'nuevos']++;
                }
                return $counts;
            });
        });
    }
}
