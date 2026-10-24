<?php

namespace Tests\Feature;

use App\Modules\Actividades\Models\Actividad;
use App\Modules\Atractivos\Models\Atractivo;
use App\Modules\Destinos\Models\Destino;
use App\Modules\Rutas\Services\RutaService;
use App\Modules\Usuarios\Models\Usuario;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogoRangosTest extends TestCase
{
    use RefreshDatabase;

    public function refreshApplication(): void
    {
        parent::refreshApplication();
        if (config('database.default') !== 'pgsql' || ! str_ends_with((string) config('database.connections.pgsql.database'), '_test')) {
            throw new \RuntimeException('Se requiere una base PostgreSQL exclusiva _test.');
        }
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function usuario(array $attributes = []): Usuario
    {
        return Usuario::create(UserFactory::new()->raw($attributes));
    }

    private function destino(): Destino
    {
        return Destino::create(['nombre' => 'Destino de prueba', 'pais' => 'Perú', 'estado' => 'activo', 'operador_id' => $this->usuario(['rol' => 'operador_turistico'])->id]);
    }

    public function test_activity_and_attraction_ranges_include_exact_boundaries_and_exclude_outside(): void
    {
        $destino = $this->destino();
        foreach ([[0, 30, 'Fuera gratis'], [50, 60, 'Dentro inferior'], [100, 120, 'Dentro superior'], [101, 180, 'Fuera superior']] as [$price, $minutes, $name]) {
            Actividad::factory()->create(['destino_id' => $destino->id, 'nombre' => $name, 'costo' => $price, 'duracion_estimada_min' => $minutes]);
            Atractivo::create(['destino_id' => $destino->id, 'nombre' => $name, 'costo_entrada' => $price, 'duracion_estimada_min' => $minutes, 'estado' => 'activo']);
        }

        foreach (['actividades', 'atractivos'] as $catalog) {
            $this->get(route('catalogo.'.$catalog.'.index', ['costo_min' => 50, 'costo_max' => 100, 'duracion_min' => 60, 'duracion_max' => 120]))
                ->assertOk()->assertSee('Dentro inferior')->assertSee('Dentro superior')->assertDontSee('Fuera gratis')->assertDontSee('Fuera superior');
            $this->get(route('catalogo.'.$catalog.'.index', ['costo_max' => 0]))->assertOk()->assertSee('Fuera gratis')->assertDontSee('Dentro inferior');
        }
    }

    public function test_inverted_and_negative_ranges_are_rejected_by_all_three_catalogs(): void
    {
        foreach (['actividades', 'atractivos', 'rutas'] as $catalog) {
            $url = route('catalogo.'.$catalog.'.index');
            $this->getJson($url.'?costo_min=100&costo_max=20&duracion_min=180&duracion_max=60')
                ->assertUnprocessable()->assertJsonValidationErrors(['costo_max', 'duracion_max']);
            $this->getJson($url.'?costo_min=-1&duracion_min=-1')
                ->assertUnprocessable()->assertJsonValidationErrors(['costo_min', 'duracion_min']);
        }
    }

    public function test_route_filter_uses_sum_of_admission_costs_and_duration(): void
    {
        $destino = $this->destino();
        $first = Atractivo::create(['destino_id' => $destino->id, 'nombre' => 'Primero', 'costo_entrada' => 10.5, 'estado' => 'activo']);
        $second = Atractivo::create(['destino_id' => $destino->id, 'nombre' => 'Segundo', 'costo_entrada' => 20, 'estado' => 'activo']);
        app(RutaService::class)->crear(['destino_id' => $destino->id, 'nombre' => 'Recorrido exacto', 'duracion_estimada_min' => 120, 'estado' => 'activo'], [$first->id, $second->id]);

        $url = route('catalogo.rutas.index');
        $this->get($url.'?costo_min=30.5&costo_max=30.5&duracion_min=120&duracion_max=120')->assertOk()->assertSee('Recorrido exacto')->assertSee('S/ 30.50');
        $this->get($url.'?costo_max=30')->assertOk()->assertDontSee('Recorrido exacto');
        $this->get($url.'?duracion_min=121')->assertOk()->assertDontSee('Recorrido exacto');
    }

    public function test_cards_and_ranges_render_in_english_without_missing_translation_keys(): void
    {
        Actividad::factory()->create(['destino_id' => $this->destino()->id]);

        $this->withSession(['interface_locale' => 'en'])->get(route('catalogo.actividades.index'))->assertOk()
            ->assertSee('Price (S/)')->assertSee('Duration')->assertSee('No limit')->assertDontSee('favorites.');
    }
}
