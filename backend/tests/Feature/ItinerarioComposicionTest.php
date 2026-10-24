<?php

namespace Tests\Feature;

use App\Modules\Atractivos\Models\Atractivo;
use App\Modules\Atractivos\Models\CategoriaInteres;
use App\Modules\Destinos\Models\Destino;
use App\Modules\Itinerarios\Models\Itinerario;
use App\Modules\Preferencias\Models\PreferenciaTurista;
use App\Modules\Usuarios\Models\Usuario;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItinerarioComposicionTest extends TestCase
{
    use RefreshDatabase;

    public function refreshApplication(): void
    {
        parent::refreshApplication();
        if (config('database.default') !== 'pgsql' || ! str_ends_with((string) config('database.connections.pgsql.database'), '_test')) {
            throw new \RuntimeException('Estas pruebas requieren PostgreSQL y una base exclusiva cuyo nombre termine en _test.');
        }
    }

    public function test_el_alta_de_itinerario_compone_visitas_por_categoria_horario_y_presupuesto_y_vincula_la_preferencia(): void
    {
        $this->withoutVite();

        $destino = Destino::create(['nombre' => 'Valle sagrado', 'pais' => 'Perú', 'estado' => 'activo']);
        $aventura = CategoriaInteres::create(['nombre' => 'Aventura']);

        $conCategoria = Atractivo::create([
            'destino_id' => $destino->id,
            'nombre' => 'Caminata al mirador',
            'costo_entrada' => 15,
            'duracion_estimada_min' => 45,
            'estado' => 'activo',
        ]);
        $conCategoria->categorias()->attach($aventura->id);

        Atractivo::create([
            'destino_id' => $destino->id,
            'nombre' => 'Museo sin interés declarado',
            'costo_entrada' => 5,
            'duracion_estimada_min' => 30,
            'estado' => 'activo',
        ]);

        $turista = Usuario::create(UserFactory::new()->raw(['rol' => 'turista']));
        $preferencia = PreferenciaTurista::create([
            'turista_id' => $turista->id,
            'presupuesto_max' => 100,
            'dias_disponibles' => 1,
            'hora_inicio_preferida' => '09:00',
            'hora_fin_preferida' => '12:00',
        ]);
        $preferencia->categorias()->attach($aventura->id);

        $this->actingAs($turista);
        $this->post(route('itinerarios.store'), [
            'destino_id' => $destino->id,
            'nombre' => 'Viaje compuesto',
            'dias' => 1,
        ])->assertSessionHasNoErrors()->assertRedirect(route('itinerarios.index'));

        $itinerario = Itinerario::where('nombre', 'Viaje compuesto')->firstOrFail();
        $this->assertSame($preferencia->id, $itinerario->preferencia_id);
        $this->assertSame(100.0, (float) $itinerario->presupuesto_total);

        $dia = $itinerario->diasItinerario()->firstOrFail();
        $items = $dia->items;

        $this->assertCount(1, $items, 'Solo el atractivo de la categoría declarada debe componerse.');
        $this->assertSame($conCategoria->id, $items->first()->entidad_id);
        $this->assertSame('09:00:00', $items->first()->hora_inicio);
        $this->assertSame('09:45:00', $items->first()->hora_fin);
        $this->assertSame(15.0, (float) $items->first()->costo_estimado);

        $this->get(route('itinerarios.show', $itinerario))
            ->assertOk()
            ->assertSee('Caminata al mirador')
            ->assertDontSee('Museo sin interés declarado');
    }
}
