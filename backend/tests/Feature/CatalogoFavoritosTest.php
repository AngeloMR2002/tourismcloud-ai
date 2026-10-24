<?php

namespace Tests\Feature;

use App\Modules\Actividades\Models\Actividad;
use App\Modules\Atractivos\Models\Atractivo;
use App\Modules\Destinos\Models\Destino;
use App\Modules\Establecimientos\Models\Establecimiento;
use App\Modules\Rutas\Services\RutaService;
use App\Modules\Usuarios\Models\Usuario;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogoFavoritosTest extends TestCase
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

    public function test_guest_is_sent_to_login_without_saving_and_returns_after_registration(): void
    {
        $item = Actividad::factory()->create(['destino_id' => $this->destino()->id]);
        $url = route('favoritos.confirmar', ['actividad', $item->id]);

        $this->get($url)->assertRedirect(route('login'))->assertSessionHas('url.intended', $url);
        $this->post(route('favoritos.store', ['actividad', $item->id]))->assertRedirect(route('login'));
        $this->assertDatabaseCount('favoritos', 0);

        $this->post(route('register.post'), [
            'nombre' => 'Visitante', 'apellido' => 'Prueba', 'email' => 'visitante@example.test',
            'password' => 'Example-12345', 'password_confirmation' => 'Example-12345',
        ])->assertRedirect($url);
        $this->get($url)->assertOk()->assertSee(__('favorites.confirm_intro'));
        $this->assertDatabaseCount('favoritos', 0);
    }

    public function test_favorite_is_idempotent_private_and_can_be_removed(): void
    {
        $owner = $this->usuario();
        $other = $this->usuario();
        $item = Actividad::factory()->create(['destino_id' => $this->destino()->id, 'nombre' => 'Solo mi favorito']);
        $url = route('favoritos.store', ['actividad', $item->id]);

        $this->actingAs($owner)->postJson($url, ['usuario_id' => $other->id])->assertOk()->assertJson(['saved' => true]);
        $this->postJson($url)->assertOk();
        $this->assertDatabaseCount('favoritos', 1);
        $this->assertDatabaseHas('favoritos', ['usuario_id' => $owner->id, 'tipo' => 'actividad', 'recurso_id' => $item->id]);
        $this->get(route('favoritos.index'))->assertOk()->assertSee('Solo mi favorito')->assertSee('aria-pressed="true"', false);

        $this->actingAs($other)->get(route('favoritos.index'))->assertOk()->assertDontSee('Solo mi favorito');
        $this->deleteJson($url)->assertOk();
        $this->assertDatabaseCount('favoritos', 1);
        $this->actingAs($owner)->deleteJson($url)->assertOk()->assertJson(['saved' => false]);
        $this->assertDatabaseCount('favoritos', 0);
    }

    public function test_hidden_deleted_and_unknown_resources_cannot_be_saved(): void
    {
        $item = Actividad::factory()->create(['destino_id' => $this->destino()->id, 'estado' => 'inactivo']);
        $this->actingAs($this->usuario());

        $this->postJson(route('favoritos.store', ['actividad', $item->id]))->assertNotFound();
        $this->postJson(route('favoritos.store', ['usuarios', $item->id]))->assertNotFound();
        $item->delete();
        $this->postJson(route('favoritos.store', ['actividad', $item->id]))->assertNotFound();
        $this->assertDatabaseCount('favoritos', 0);
    }

    public function test_inactive_account_cannot_save(): void
    {
        $item = Actividad::factory()->create(['destino_id' => $this->destino()->id]);

        $this->actingAs($this->usuario(['estado' => 'inactivo']))
            ->postJson(route('favoritos.store', ['actividad', $item->id]))->assertForbidden();
        $this->assertDatabaseCount('favoritos', 0);
    }

    public function test_all_supported_types_are_saved_and_hidden_items_disappear(): void
    {
        $destino = $this->destino();
        $first = Atractivo::create(['destino_id' => $destino->id, 'nombre' => 'Mirador único', 'costo_entrada' => 10, 'estado' => 'activo']);
        $second = Atractivo::create(['destino_id' => $destino->id, 'nombre' => 'Segunda parada', 'costo_entrada' => 20, 'estado' => 'activo']);
        $place = Establecimiento::create(['destino_id' => $destino->id, 'proveedor_id' => $this->usuario(['rol' => 'proveedor'])->id, 'nombre' => 'Hotel único', 'tipo' => 'hotel', 'estado' => 'activo']);
        $route = app(RutaService::class)->crear(['destino_id' => $destino->id, 'nombre' => 'Ruta única', 'estado' => 'activo'], [$first->id, $second->id]);
        $this->actingAs($this->usuario());

        foreach (['atractivo' => $first, 'establecimiento' => $place, 'ruta' => $route] as $type => $item) {
            $this->postJson(route('favoritos.store', [$type, $item->id]))->assertOk();
            $this->get(route('favoritos.index', ['tipo' => $type]))->assertOk()->assertSee($item->nombre);
        }
        $first->update(['estado' => 'inactivo']);
        $this->get(route('favoritos.index'))->assertOk()->assertDontSee('Mirador único')->assertDontSee('Ruta única')->assertSee('Hotel único');
        $this->postJson(route('favoritos.store', ['ruta', $route->id]))->assertNotFound();
    }

    public function test_cards_show_real_reviews_only_on_supported_resources_and_escape_text(): void
    {
        $destino = $this->destino();
        $item = Atractivo::create(['destino_id' => $destino->id, 'nombre' => '<script>bad()</script>', 'costo_entrada' => 25, 'estado' => 'activo']);
        $item->valoraciones()->create(['usuario_id' => $this->usuario()->id, 'puntuacion' => 5, 'comentario' => 'Excelente']);
        $item->valoraciones()->create(['usuario_id' => $this->usuario()->id, 'puntuacion' => 3]);
        Actividad::factory()->create(['destino_id' => $destino->id]);

        $this->get(route('catalogo.atractivos.index'))->assertOk()
            ->assertSee('4.0')->assertSee('(2)')->assertSee('S/ 25.00')
            ->assertSee('&lt;script&gt;bad()&lt;/script&gt;', false)->assertDontSee('<script>bad()</script>', false)
            ->assertDontSee('tc-card-cta', false);
        $this->get(route('catalogo.actividades.index'))->assertOk()->assertDontSee('tc-discovery-card__rating', false);
        $this->get(route('inicio'))->assertOk()->assertSee('tc-discovery-card', false);
    }
}
