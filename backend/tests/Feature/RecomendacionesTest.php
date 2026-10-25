<?php

namespace Tests\Feature;

use App\Modules\Actividades\Models\Actividad;
use App\Modules\Atractivos\Models\Atractivo;
use App\Modules\Destinos\Models\Destino;
use App\Modules\Preferencias\Models\PreferenciaTurista;
use App\Modules\Usuarios\Models\Usuario;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RecomendacionesTest extends TestCase
{
    use RefreshDatabase;

    private Destino $destino;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->destino = Destino::create([
            'nombre' => 'Destino de prueba',
            'pais' => 'Perú',
            'estado' => 'activo',
        ]);
    }

    public function refreshApplication(): void
    {
        parent::refreshApplication();
        if (config('database.default') !== 'pgsql' || ! str_ends_with((string) config('database.connections.pgsql.database'), '_test')) {
            throw new \RuntimeException('Estas pruebas requieren PostgreSQL y una base exclusiva cuyo nombre termine en _test.');
        }
    }

    public function test_el_modulo_solo_permite_acceso_a_turistas_autenticados(): void
    {
        $this->get(route('recomendaciones.index'))->assertRedirect(route('login'));

        foreach (['administrador', 'operador_turistico', 'proveedor'] as $rol) {
            $usuario = Usuario::create(UserFactory::new()->raw(['rol' => $rol]));
            $this->actingAs($usuario)
                ->get(route('recomendaciones.index'))
                ->assertForbidden();
        }

        $turista = Usuario::create(UserFactory::new()->raw(['rol' => 'turista']));
        $this->actingAs($turista)
            ->get(route('recomendaciones.index'))
            ->assertOk()
            ->assertSee('Recomendaciones');
    }

    public function test_sin_preferencias_muestra_tres_atractivos_y_tres_actividades_activas(): void
    {
        $turista = Usuario::create(UserFactory::new()->raw(['rol' => 'turista']));

        foreach (range(1, 7) as $numero) {
            Atractivo::create([
                'destino_id' => $this->destino->id,
                'nombre' => sprintf('Atractivo %02d', $numero),
                'costo_entrada' => 10,
                'estado' => 'activo',
            ]);
            Actividad::factory()->create([
                'destino_id' => $this->destino->id,
                'nombre' => sprintf('Recomendación %02d', $numero),
                'estado' => 'activo',
            ]);
        }

        Actividad::factory()->create([
            'destino_id' => $this->destino->id,
            'nombre' => 'Actividad inactiva',
            'estado' => 'inactivo',
        ]);

        $this->actingAs($turista)
            ->get(route('recomendaciones.index'))
            ->assertOk()
            ->assertSee('Configurar preferencias')
            ->assertSee('Atractivos turísticos para ti')
            ->assertSee('Atractivo 01')
            ->assertSee('Atractivo 03')
            ->assertDontSee('Atractivo 04')
            ->assertSee('Recomendación 01')
            ->assertSee('Recomendación 03')
            ->assertDontSee('Recomendación 04')
            ->assertDontSee('Actividad inactiva');
    }

    public function test_prioriza_categorias_y_presupuesto_sin_tomar_en_cuenta_duracion(): void
    {
        $turista = Usuario::create(UserFactory::new()->raw(['rol' => 'turista']));
        $otroDestino = Destino::create([
            'nombre' => 'Otro destino',
            'pais' => 'Perú',
            'estado' => 'activo',
        ]);
        $categoriaId = DB::table('categorias_interes')->insertGetId([
            'nombre' => 'Naturaleza',
            'descripcion' => 'Actividades al aire libre.',
        ]);
        $preferencia = PreferenciaTurista::create([
            'turista_id' => $turista->id,
            'presupuesto_max' => 100,
            'dias_disponibles' => 1,
        ]);
        DB::table('preferencia_categoria')->insert([
            'preferencia_id' => $preferencia->id,
            'categoria_id' => $categoriaId,
        ]);

        $atractivo = Atractivo::create([
            'destino_id' => $this->destino->id,
            'nombre' => 'Reserva natural',
            'costo_entrada' => 10,
            'estado' => 'activo',
        ]);
        Atractivo::create([
            'destino_id' => $otroDestino->id,
            'nombre' => 'Atractivo fuera de presupuesto sin coincidencia',
            'costo_entrada' => 150,
            'estado' => 'activo',
        ]);
        DB::table('atractivo_categoria')->insert([
            'atractivo_id' => $atractivo->id,
            'categoria_id' => $categoriaId,
        ]);
        DB::table('actividad_categoria')->insert([
            'actividad_id' => Actividad::factory()->create([
                'destino_id' => $this->destino->id,
                'nombre' => 'Caminata en la reserva',
                'costo' => 50,
                'duracion_estimada_min' => 2880,
                'estado' => 'activo',
            ])->id,
            'categoria_id' => $categoriaId,
        ]);

        Actividad::factory()->create([
            'destino_id' => $otroDestino->id,
            'nombre' => 'Opción económica',
            'costo' => 20,
            'duracion_estimada_min' => 60,
            'estado' => 'activo',
        ]);
        Actividad::factory()->create([
            'destino_id' => $otroDestino->id,
            'nombre' => 'Actividad fuera de presupuesto sin coincidencia',
            'costo' => 150,
            'duracion_estimada_min' => 60,
            'estado' => 'activo',
        ]);

        $this->actingAs($turista)
            ->get(route('recomendaciones.index'))
            ->assertOk()
            ->assertSeeInOrder(['Atractivos turísticos para ti', 'Reserva natural', 'Actividades para ti', 'Caminata en la reserva', 'Opción económica'])
            ->assertSee('Interés: Naturaleza')
            ->assertSee('Dentro de tu presupuesto')
            ->assertDontSee('Atractivo fuera de presupuesto sin coincidencia')
            ->assertDontSee('Actividad fuera de presupuesto sin coincidencia')
            ->assertDontSee('Duración adecuada para tu viaje');
    }
}
