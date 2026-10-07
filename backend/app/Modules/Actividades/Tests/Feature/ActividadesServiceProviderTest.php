<?php

namespace App\Modules\Actividades\Tests\Feature;

use App\Modules\Actividades\Database\Factories\DestinoFactory;
use App\Modules\Actividades\Database\Factories\AtractivoFactory;
use App\Models\Atractivo;
use App\Models\Destino;
use App\Models\User;
use App\Modules\Actividades\Console\Commands\ImportarLugares;
use App\Modules\Actividades\Http\Controllers\Api\V1\CatalogoActividadController as ApiCatalogoActividadController;
use App\Modules\Rutas\Http\Controllers\Api\V1\CatalogoRutaController as ApiCatalogoRutaController;
use App\Modules\Actividades\Http\Controllers\Catalogo\CatalogoActividadController;
use App\Modules\Rutas\Http\Controllers\Catalogo\CatalogoRutaController;
use App\Modules\Actividades\Http\Controllers\Operador\ImportacionController;
use App\Modules\Actividades\Http\Controllers\Operador\OperadorActividadController;
use App\Modules\Rutas\Http\Controllers\Operador\OperadorRutaController;
use App\Modules\Actividades\Http\Controllers\Operador\RevisionController;
use App\Modules\Actividades\Models\Actividad;
use App\Modules\Rutas\Models\Ruta;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ActividadesServiceProviderTest extends TestCase
{
    use LazilyRefreshDatabase;

    #[DataProvider('rutasP3')]
    public function test_conserva_urls_nombres_controladores_y_middleware_de_p3(string $method, string $uri, string $name, string $action, array $middleware): void
    {
        $route = Route::getRoutes()->match(Request::create($uri, $method));

        $this->assertSame($name, $route->getName());
        $this->assertSame($action, $route->getActionName());
        $this->assertSame($middleware, $route->gatherMiddleware());
    }

    public static function rutasP3(): array
    {
        return [
            'listado actividades' => ['GET', '/catalogo/actividades', 'catalogo.actividades.index', CatalogoActividadController::class.'@index', ['web']],
            'detalle actividad' => ['GET', '/catalogo/actividades/1', 'catalogo.actividades.show', CatalogoActividadController::class.'@show', ['web']],
            'listado rutas' => ['GET', '/catalogo/rutas', 'catalogo.rutas.index', CatalogoRutaController::class.'@index', ['web']],
            'detalle ruta' => ['GET', '/catalogo/rutas/1', 'catalogo.rutas.show', CatalogoRutaController::class.'@show', ['web']],
            'API actividades' => ['GET', '/api/v1/catalogo/actividades', 'api.v1.catalogo.actividades', ApiCatalogoActividadController::class.'@index', ['api', 'throttle:60,1']],
            'API rutas' => ['GET', '/api/v1/catalogo/rutas', 'api.v1.catalogo.rutas', ApiCatalogoRutaController::class.'@index', ['api', 'throttle:60,1']],
            'gestionar actividades' => ['POST', '/operador/actividades', 'operador.actividades.store', OperadorActividadController::class.'@store', ['web', 'auth', 'can:gestionar-catalogo']],
            'gestionar rutas' => ['POST', '/operador/rutas', 'operador.rutas.store', OperadorRutaController::class.'@store', ['web', 'auth', 'can:gestionar-catalogo']],
            'importar lugares' => ['POST', '/operador/importaciones', 'operador.importaciones.store', ImportacionController::class.'@store', ['web', 'auth', 'can:gestionar-catalogo', 'can:revisar-catalogo', 'throttle:2,1']],
            'aprobar datos' => ['POST', '/operador/revision/actividades/1/aprobar', 'operador.revision.aprobar', RevisionController::class.'@aprobar', ['web', 'auth', 'can:gestionar-catalogo', 'can:revisar-catalogo']],
        ];
    }

    public function test_las_factories_del_modulo_conservan_las_relaciones_con_destinos_y_atractivos(): void
    {
        $destino = DestinoFactory::new()->create();
        $atractivo = AtractivoFactory::new()->for($destino)->create();
        $actividad = Actividad::factory()->for($destino)->for($atractivo)->create();
        $ruta = Ruta::factory()->for($destino)->create();
        $punto = $ruta->puntos()->create(['atractivo_id' => $atractivo->id, 'nombre_parada' => 'Parada de prueba', 'orden' => 1]);

        $this->assertSame($actividad->id, $destino->actividades()->sole()->id);
        $this->assertSame($ruta->id, $destino->rutas()->sole()->id);
        $this->assertSame($actividad->id, $atractivo->actividades()->sole()->id);
        $this->assertSame($punto->id, $atractivo->rutaPuntos()->sole()->id);
        $this->assertSame($destino->id, $actividad->destino->id);
        $this->assertSame($atractivo->id, $actividad->atractivo->id);
        $this->assertSame($destino->id, $ruta->destino->id);
        $this->assertSame($ruta->id, $punto->ruta->id);
        $this->assertSame($atractivo->id, $punto->atractivo->id);
    }

    #[DataProvider('permisosP3')]
    public function test_los_permisos_de_p3_se_registran_segun_el_rol(string $rol, bool $gestionar, bool $revisar): void
    {
        $user = User::factory()->make(['rol' => $rol]);

        $this->assertSame($gestionar, Gate::forUser($user)->allows('gestionar-catalogo'));
        $this->assertSame($revisar, Gate::forUser($user)->allows('revisar-catalogo'));
    }

    public static function permisosP3(): array
    {
        return [
            'administrador' => ['administrador', true, true],
            'operador' => ['operador', true, false],
            'turista' => ['turista', false, false],
            'rol desconocido' => ['otro', false, false],
        ];
    }

    public function test_registra_el_comando_de_importacion_del_modulo(): void
    {
        $commands = $this->app->make(Kernel::class)->all();

        $this->assertInstanceOf(ImportarLugares::class, $commands['tourism:importar-lugares']);
    }

    public function test_programa_una_sola_importacion_diaria_con_hora_de_peru(): void
    {
        $events = collect($this->app->make(Schedule::class)->events())
            ->filter(fn ($event): bool => str_contains($event->command ?? '', 'tourism:importar-lugares'));

        $this->assertCount(1, $events);
        $this->assertSame('0 3 * * *', $events->sole()->expression);
        $this->assertSame('America/Lima', $events->sole()->timezone);
        $this->assertTrue($events->sole()->withoutOverlapping);
    }
}
