<?php

namespace App\Modules\Actividades\Tests\Feature;

use App\Modules\Actividades\Database\Factories\DestinoFactory;
use App\Modules\Actividades\Models\Actividad;
use App\Models\Destino;
use App\Modules\Rutas\Models\Ruta;
use App\Modules\Actividades\Services\ActividadService;
use App\Modules\Actividades\Database\Seeders\ActividadesRutasSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class CatalogoVerificadoTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->travelTo(Carbon::parse('2026-10-07 12:00:00'));
    }

    public function test_catalogo_oculta_registros_no_aprobados_sin_fuente_caducados_y_eventos_pasados(): void
    {
        $visible = Actividad::factory()->create(['nombre' => 'Visita revisada']);
        $pendiente = Actividad::factory()->create(['nombre' => 'Actividad pendiente', 'estado_verificacion' => 'pendiente']);
        $caducada = Actividad::factory()->create(['nombre' => 'Actividad caducada', 'fecha_verificacion' => now()->subDays(181)]);
        $sinFuente = Actividad::factory()->create(['nombre' => 'Sin respaldo', 'fuente_url' => null]);
        $evento = Actividad::factory()->create(['nombre' => 'Evento terminado', 'tipo_registro' => 'evento', 'evento_fin' => now()->subDay()]);
        $inactiva = Actividad::factory()->create(['nombre' => 'No publicada', 'estado' => 'inactivo']);

        $response = $this->get(route('catalogo.actividades.index'));

        $response->assertSee($visible->nombre)->assertDontSee($pendiente->nombre)->assertDontSee($caducada->nombre)
            ->assertDontSee($sinFuente->nombre)->assertDontSee($evento->nombre)->assertDontSee($inactiva->nombre);
        $this->get(route('catalogo.actividades.show', $pendiente))->assertNotFound();
    }

    public function test_valores_desconocidos_no_se_presentan_como_gratis_horarios_cupos_o_servicios(): void
    {
        $actividad = Actividad::factory()->create();

        $response = $this->get(route('catalogo.actividades.show', $actividad));

        $response->assertSee('Consultar precio')->assertSee('Por confirmar')->assertSee('Consultar fuente')
            ->assertDontSee('Acceso libre')->assertDontSee('Horario flexible')->assertDontSee('Todos los días')
            ->assertDontSee('Guía profesional')->assertDontSee('Máx. 15')->assertDontSee('Disponible');
    }

    public function test_presupuesto_cero_incluye_solo_acceso_libre_con_tarifa_reciente_y_fuente(): void
    {
        $gratis = Actividad::factory()->create(['precio' => 0, 'tipo_precio' => 'fijo', 'precio_fuente_url' => 'https://example.org/tarifa', 'precio_verificado_en' => now()]);
        Actividad::factory()->create();
        Actividad::factory()->create(['precio' => 0, 'tipo_precio' => 'fijo', 'precio_fuente_url' => 'https://example.org/tarifa', 'precio_verificado_en' => now()->subDays(8)]);
        Actividad::factory()->create(['precio' => 0, 'tipo_precio' => 'estimado', 'precio_fuente_url' => 'https://example.org/tarifa', 'precio_verificado_en' => now()]);

        $response = $this->getJson(route('api.v1.catalogo.actividades', ['precio_max' => 0]));

        $response->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $gratis->id)
            ->assertJsonPath('data.0.precio_pen', 0);
    }

    public function test_api_limita_resultados_y_no_expone_datos_del_operador_ni_precios_vencidos(): void
    {
        Actividad::factory()->create(['precio' => 10, 'tipo_precio' => 'fijo', 'precio_fuente_url' => 'https://example.org/tarifa', 'precio_verificado_en' => now()->subDays(8)]);
        Actividad::factory()->count(24)->create();

        $response = $this->getJson(route('api.v1.catalogo.actividades', ['orden' => 'precio_desc']));

        $response->assertJsonCount(20, 'data')->assertJsonPath('data.0.precio_pen', null)
            ->assertJsonPath('data.0.precio_estado', 'por_confirmar')->assertJsonMissingPath('data.0.operador')
            ->assertJsonMissingPath('data.0.operador_id')->assertJsonMissingPath('data.0.email')
            ->assertJsonPath('data.0.disponibilidad', 'consultar_con_responsable');
    }

    public function test_optimizador_excluye_duraciones_desconocidas_y_destinos_inactivos(): void
    {
        $destino = DestinoFactory::new()->create();
        $conTiempo = Actividad::factory()->create(['destino_id' => $destino->id, 'duracion_min' => 60]);
        Actividad::factory()->create(['destino_id' => $destino->id]);
        $service = app(ActividadService::class);

        $resultados = $service->obtenerParaOptimizadorIA($destino->id, tiempoMaxMin: 90);

        $this->assertSame([$conTiempo->id], $resultados->modelKeys());
        $destino->update(['estado' => 'inactivo']);
        $this->assertSame([], $service->obtenerParaOptimizadorIA($destino->id)->modelKeys());
    }

    public function test_semilla_real_es_idempotente_no_crea_usuarios_ni_tarifas_y_distingue_propuesta(): void
    {
        $this->seed(ActividadesRutasSeeder::class);
        $this->seed(ActividadesRutasSeeder::class);

        $this->assertDatabaseCount('actividades', 5);
        $this->assertDatabaseCount('atractivos', 5);
        $this->assertDatabaseCount('destinos', 2);
        $this->assertDatabaseCount('rutas', 1);
        $this->assertDatabaseCount('ruta_puntos', 2);
        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseMissing('actividades', ['fuente_url' => null]);
        $this->assertSame(0, Actividad::whereNotNull('precio')->count());
        $this->assertSame(3, Actividad::whereNotNull('imagen_fuente_url')->count());
        $ruta = Ruta::firstOrFail();
        $this->get(route('catalogo.rutas.show', $ruta))->assertSee('Propuesta TourismCloud')->assertSee('Por confirmar')->assertSee('Fuente de este lugar');
        $this->getJson(route('api.v1.catalogo.rutas'))->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.es_propuesta_tourismcloud', true)->assertJsonPath('data.0.costo_pen', null)
            ->assertJsonCount(2, 'data.0.paradas')->assertJsonPath('data.0.paradas.0.orden', 1);
    }

    public function test_fotografia_necesita_atribucion_completa_y_sin_ella_no_se_muestra(): void
    {
        $actividad = Actividad::factory()->create(['imagen_portada' => 'https://example.org/sin-licencia.jpg']);

        $this->get(route('catalogo.actividades.show', $actividad))
            ->assertSee('Sin fotografía verificada')->assertDontSee('https://example.org/sin-licencia.jpg');
    }
}
