<?php

namespace Tests\Fixtures;

use App\Modules\Actividades\Services\ImportadorDemoActividadesRutas;
use App\Modules\Atractivos\Models\Atractivo;
use App\Modules\Destinos\Models\Destino;
use App\Modules\Usuarios\Models\Usuario;
use Database\Factories\UserFactory;
use Illuminate\Database\Seeder;

/**
 * Datos desechables para QA visual, exclusivamente en una base de pruebas.
 * No usar para integrar los datos de un compañero.
 */
class ActividadesRutasVisualSeeder extends Seeder
{
    public function run(): void
    {
        if (config('database.default') !== 'pgsql' || ! str_ends_with((string) config('database.connections.pgsql.database'), '_test')) {
            throw new \RuntimeException('El seeder visual solo admite una base PostgreSQL cuyo nombre termine en _test.');
        }

        $operador = Usuario::firstOrCreate(
            ['email' => 'operador.demo@example.test'],
            UserFactory::new()->raw(['email' => 'operador.demo@example.test', 'nombre' => 'Operador', 'apellido' => 'Pruebas', 'rol' => 'operador_turistico', 'password' => 'Demo-Actividades-2026!']),
        );
        $destino = Destino::firstOrCreate(
            ['nombre' => 'Cusco', 'operador_id' => $operador->id],
            ['pais' => 'Perú', 'region' => 'Cusco', 'ciudad' => 'Cusco', 'estado' => 'activo'],
        );
        $mapeo = ['destinos' => ['cusco' => $destino->id], 'atractivos' => []];
        foreach (['saqsaywaman' => 'Saqsaywaman', 'qenqo' => 'Qenqo', 'tambomachay' => 'Tambomachay', 'koricancha' => 'Koricancha', 'catedral' => 'Catedral de Cusco'] as $alias => $nombre) {
            $atractivo = Atractivo::firstOrCreate(
                ['nombre' => $nombre, 'destino_id' => $destino->id],
                ['descripcion' => 'Registro ficticio para pruebas visuales. No es una cotización.', 'costo_entrada' => 10, 'estado' => 'activo'],
            );
            $mapeo['atractivos'][$alias] = $atractivo->id;
        }
        app(ImportadorDemoActividadesRutas::class)->import($mapeo);
    }
}
