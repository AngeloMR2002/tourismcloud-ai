<?php

namespace Tests\Feature;

use App\Modules\Preferencias\Models\PreferenciaTurista;
use App\Modules\Usuarios\Models\Usuario;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PreferenciasAccesoTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $turista;

    private Usuario $otroTurista;

    public function refreshApplication(): void
    {
        parent::refreshApplication();
        if (config('database.default') !== 'pgsql' || ! str_ends_with((string) config('database.connections.pgsql.database'), '_test')) {
            throw new \RuntimeException('Estas pruebas requieren PostgreSQL y una base exclusiva cuyo nombre termine en _test.');
        }
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->turista = Usuario::create(UserFactory::new()->raw(['rol' => 'turista']));
        $this->otroTurista = Usuario::create(UserFactory::new()->raw(['rol' => 'turista']));
    }

    private function datosPreferencia(array $datos = []): array
    {
        return array_replace([
            'presupuesto_max' => 500,
            'dias_disponibles' => 3,
            'hora_inicio_preferida' => '08:00',
            'hora_fin_preferida' => '20:00',
        ], $datos);
    }

    public function test_un_turista_no_accede_a_las_preferencias_de_otro_por_autoservicio(): void
    {
        $preferenciaAjena = PreferenciaTurista::create($this->datosPreferencia(['turista_id' => $this->otroTurista->id]));

        $this->actingAs($this->turista);

        $this->get(route('preferencias.turista', $this->otroTurista->id))->assertForbidden();
        $this->get(route('preferencias.turista.edit', ['turistaId' => $this->otroTurista->id, 'preferenciaId' => $preferenciaAjena->id]))->assertForbidden();
        $this->put(route('preferencias.turista.update', ['turistaId' => $this->otroTurista->id, 'preferenciaId' => $preferenciaAjena->id]), $this->datosPreferencia())->assertForbidden();

        $this->assertSame(500.0, (float) $preferenciaAjena->fresh()->presupuesto_max);
    }

    public function test_store_ignora_un_turista_id_manipulado_y_usa_el_usuario_autenticado(): void
    {
        $this->actingAs($this->turista);

        $this->post(route('preferencias.store'), $this->datosPreferencia([
            'turista_id' => $this->otroTurista->id,
        ]))->assertSessionHasNoErrors()->assertRedirect(route('preferencias.turista', $this->turista->id));

        $this->assertDatabaseHas('preferencias_turista', ['turista_id' => $this->turista->id, 'presupuesto_max' => 500]);
        $this->assertDatabaseMissing('preferencias_turista', ['turista_id' => $this->otroTurista->id]);
    }

    public function test_las_rutas_administrativas_genericas_rechazan_a_un_turista(): void
    {
        $preferencia = PreferenciaTurista::create($this->datosPreferencia(['turista_id' => $this->otroTurista->id]));

        $this->actingAs($this->turista);

        $this->get(route('preferencias.index'))->assertForbidden();
        $this->get(route('preferencias.show', $preferencia))->assertForbidden();
        $this->get(route('preferencias.edit', $preferencia))->assertForbidden();
        $this->put(route('preferencias.update', $preferencia), $this->datosPreferencia(['turista_id' => $this->otroTurista->id]))->assertForbidden();
        $this->delete(route('preferencias.destroy', $preferencia))->assertForbidden();
        $this->get(route('preferencias.recientes'))->assertForbidden();

        $this->assertDatabaseHas('preferencias_turista', ['id' => $preferencia->id]);
    }

    public function test_un_administrador_si_gestiona_cualquier_preferencia(): void
    {
        $administrador = Usuario::create(UserFactory::new()->raw(['rol' => 'administrador']));
        $preferencia = PreferenciaTurista::create($this->datosPreferencia(['turista_id' => $this->otroTurista->id]));

        $this->actingAs($administrador);

        $this->get(route('preferencias.index'))->assertOk();
        $this->get(route('preferencias.show', $preferencia))->assertOk();
        $this->delete(route('preferencias.destroy', $preferencia))->assertRedirect(route('preferencias.index'));
        $this->assertDatabaseMissing('preferencias_turista', ['id' => $preferencia->id]);
    }
}
