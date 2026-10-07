<?php

namespace App\Modules\Actividades\Console\Commands;

use App\Models\Destino;
use App\Modules\Actividades\Services\ImportacionLugaresService;
use Illuminate\Console\Command;
use Throwable;

class ImportarLugares extends Command
{
    protected $signature = 'tourism:importar-lugares {ciudad? : Chimbote o Nuevo Chimbote}';
    protected $description = 'Importar lugares de OpenStreetMap a la bandeja de revisión';

    public function handle(ImportacionLugaresService $service): int
    {
        $destinos = Destino::activos()->whereIn('ciudad', config('tourism.osm_areas'));
        if ($this->argument('ciudad')) {
            $destinos->where('ciudad', $this->argument('ciudad'));
        }
        $records = $destinos->get();
        if ($records->isEmpty()) {
            $this->error('No hay destinos compatibles. Ejecuta primero tourism:preparar.');
            return self::FAILURE;
        }
        foreach ($records as $destino) {
            try {
                $counts = $service->importar($destino);
                $this->info($destino->nombre.': '.json_encode($counts).'. Pendientes de revisión.');
            } catch (Throwable $error) {
                report($error);
                $this->error('No se pudo importar '.$destino->nombre.'. Se conservó el catálogo existente.');
                return self::FAILURE;
            }
        }
        return self::SUCCESS;
    }
}
