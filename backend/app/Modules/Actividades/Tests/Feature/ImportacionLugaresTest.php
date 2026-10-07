<?php

namespace App\Modules\Actividades\Tests\Feature;

use App\Modules\Actividades\Database\Factories\DestinoFactory;
use App\Modules\Actividades\Database\Factories\AtractivoFactory;
use App\Models\Atractivo;
use App\Models\Destino;
use App\Modules\Actividades\Services\ImportacionLugaresService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Tests\TestCase;

class ImportacionLugaresTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Http::preventStrayRequests();
    }

    private function respuestaOverpass(): array
    {
        return ['elements' => [
            ['type' => 'node', 'id' => 101, 'lat' => -9.12, 'lon' => -78.51,
                'tags' => ['name' => 'Lugar de prueba OSM', 'tourism' => 'attraction', 'website' => 'https://example.org/lugar', 'opening_hours' => 'Mo-Fr 08:00-18:00']],
            ['type' => 'way', 'id' => 202, 'center' => ['lat' => -9.13, 'lon' => -78.52],
                'tags' => ['name' => 'Playa de prueba', 'natural' => 'beach', 'website' => 'javascript:alert(1)']],
            ['type' => 'node', 'id' => 303, 'lat' => 999, 'lon' => 2, 'tags' => ['name' => 'Coordenadas inválidas']],
        ]];
    }

    public function test_importa_datos_reales_del_payload_como_pendientes_sin_precio_y_deduplica_con_cache(): void
    {
        Http::fake([config('tourism.overpass_url') => Http::response($this->respuestaOverpass())]);
        $destino = DestinoFactory::new()->create(['ciudad' => 'Nuevo Chimbote']);
        $service = app(ImportacionLugaresService::class);

        $primera = $service->importar($destino);
        $segunda = $service->importar($destino);

        $this->assertSame(['nuevos' => 2, 'actualizados' => 0, 'conservados' => 0], $primera);
        $this->assertSame(['nuevos' => 0, 'actualizados' => 2, 'conservados' => 0], $segunda);
        $this->assertDatabaseCount('atractivos', 2);
        $this->assertDatabaseHas('atractivos', ['origen_id' => 'osm/node/101', 'estado_verificacion' => 'pendiente', 'costo_entrada' => null,
            'fuente_url' => 'https://www.openstreetmap.org/node/101', 'horarios' => null]);
        $this->assertDatabaseHas('atractivos', ['origen_id' => 'osm/way/202', 'sitio_web_oficial' => null, 'latitud' => -9.13]);
        Http::assertSentCount(1);
        $this->assertDatabaseCount('actividades', 0);
    }

    public function test_importacion_no_sobrescribe_revision_manual_ni_restaura_eliminados(): void
    {
        Http::fake([config('tourism.overpass_url') => Http::response($this->respuestaOverpass())]);
        $destino = DestinoFactory::new()->create();
        $aprobado = AtractivoFactory::new()->create(['origen_id' => 'osm/node/101', 'destino_id' => $destino->id, 'nombre' => 'Nombre revisado']);
        $eliminado = AtractivoFactory::new()->create(['origen_id' => 'osm/way/202', 'destino_id' => $destino->id]);
        $eliminado->delete();

        $resultado = app(ImportacionLugaresService::class)->importar($destino);

        $this->assertSame(2, $resultado['conservados']);
        $this->assertDatabaseHas('atractivos', ['id' => $aprobado->id, 'nombre' => 'Nombre revisado', 'estado_verificacion' => 'aprobado']);
        $this->assertSoftDeleted($eliminado);
    }

    public function test_error_del_proveedor_no_importa_registros_parciales(): void
    {
        Http::fake([config('tourism.overpass_url') => Http::response([], 503)]);
        $destino = DestinoFactory::new()->create();

        try {
            app(ImportacionLugaresService::class)->importar($destino);
            $this->fail('Una respuesta fallida debe producir error.');
        } catch (RequestException) {
            $this->assertDatabaseCount('atractivos', 0);
        }
    }

    public function test_respuesta_incompleta_con_remark_se_rechaza(): void
    {
        Http::fake([config('tourism.overpass_url') => Http::response($this->respuestaOverpass() + ['remark' => 'timeout'])]);
        $destino = DestinoFactory::new()->create();

        try {
            app(ImportacionLugaresService::class)->importar($destino);
            $this->fail('Una respuesta incompleta no debe importarse.');
        } catch (RuntimeException) {
            $this->assertDatabaseCount('atractivos', 0);
        }
    }

    public function test_area_no_habilitada_no_hace_consultas_externas(): void
    {
        $destino = DestinoFactory::new()->create(['ciudad' => 'Ciudad no habilitada']);

        try {
            app(ImportacionLugaresService::class)->importar($destino);
            $this->fail('El área debe pertenecer a la lista permitida.');
        } catch (ValidationException) {
            Http::assertNothingSent();
            $this->assertDatabaseCount('atractivos', 0);
        }
    }
}
