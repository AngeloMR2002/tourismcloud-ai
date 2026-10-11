<?php

namespace Database\Seeders;

use App\Modules\Actividades\Services\ImportadorDemoActividadesRutas;
use Illuminate\Database\Seeder;

class ActividadesRutasDemoSeeder extends Seeder
{
    /**
     * Importa solo los datos demostrativos de Actividades y Rutas.
     *
     * No se registra en DatabaseSeeder para impedir que se ejecute por accidente.
     */
    public function run(ImportadorDemoActividadesRutas $importer): void
    {
        $mappingPath = (string) env(
            'ACTIVIDADES_RUTAS_DEMO_MAP',
            $importer->defaultLocalMappingPath(),
        );

        $this->command?->info('Importando el paquete demostrativo de Actividades y Rutas...');
        $this->command?->line("Mapa de aliases: {$mappingPath}");

        $mapping = $importer->loadMappingFromJson($mappingPath);
        $counts = $importer->import($mapping);

        $this->command?->table(
            ['Resultado', 'Cantidad'],
            collect($counts)
                ->map(static fn (int $count, string $key): array => [$key, $count])
                ->values()
                ->all(),
        );

        if ($counts['imagenes_fuente_ausente'] > 0 || $counts['imagenes_con_error'] > 0) {
            $this->command?->warn('Se importaron los datos, pero faltaron o fallaron algunas imágenes opcionales.');
        }
    }
}
