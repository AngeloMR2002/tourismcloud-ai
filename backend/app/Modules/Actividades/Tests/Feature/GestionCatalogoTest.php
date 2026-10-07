<?php

namespace App\Modules\Actividades\Tests\Feature;

use App\Modules\Actividades\Database\Factories\DestinoFactory;
use App\Modules\Actividades\Database\Factories\AtractivoFactory;
use App\Modules\Actividades\Models\Actividad;
use App\Models\Atractivo;
use App\Models\Destino;
use App\Modules\Rutas\Models\Ruta;
use App\Models\User;
use App\Modules\Rutas\Services\RutaService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class GestionCatalogoTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->travelTo(Carbon::parse('2026-10-07 12:00:00'));
    }

    private function datosActividad(): array
    {
        return [
            'nombre' => 'Visita registrada', 'descripcion' => 'Actividad documentada para revisión.',
            'destino_id' => DestinoFactory::new()->create()->id, 'categoria' => 'cultural',
            'tipo_registro' => 'visita', 'tipo_precio' => 'variable',
            'fuente_url' => 'https://example.org/fuente', 'estado' => 'activo',
        ];
    }

    public function test_gestion_requiere_login_y_rol_y_revision_solo_administrador(): void
    {
        $this->get(route('operador.actividades.index'))->assertRedirectToRoute('login');
        $turista = User::factory()->create();
        $this->actingAs($turista)->get(route('operador.rutas.index'))->assertForbidden();
        $operador = User::factory()->create(['rol' => 'operador']);
        $this->actingAs($operador)->get(route('operador.revision.index'))->assertForbidden();
    }

    public function test_operador_no_puede_ver_editar_eliminar_ni_cambiar_registros_ajenos(): void
    {
        $operador = User::factory()->create(['rol' => 'operador']);
        $ajeno = User::factory()->create(['rol' => 'operador']);
        $actividad = Actividad::factory()->create(['operador_id' => $ajeno->id, 'nombre' => 'Registro ajeno']);
        $ruta = Ruta::factory()->create(['operador_id' => $ajeno->id, 'nombre' => 'Recorrido ajeno']);

        $this->actingAs($operador)->get(route('operador.actividades.index'))->assertDontSee('Registro ajeno');
        $this->get(route('operador.rutas.index'))->assertDontSee('Recorrido ajeno');
        $this->get(route('operador.actividades.edit', $actividad))->assertNotFound();
        $this->get(route('operador.rutas.edit', $ruta))->assertNotFound();
        $this->delete(route('operador.actividades.destroy', $actividad))->assertNotFound();
        $this->patch(route('operador.rutas.toggle-estado', $ruta))->assertNotFound();

        $this->assertNotSoftDeleted($actividad);
        $this->assertDatabaseHas('rutas', ['id' => $ruta->id, 'estado' => 'activo']);
    }

    public function test_creacion_no_permite_autoaprobar_ni_suplantar_operador_y_preserva_desconocidos(): void
    {
        $operador = User::factory()->create(['rol' => 'operador']);
        $datos = $this->datosActividad() + ['estado_verificacion' => 'aprobado', 'fecha_verificacion' => now(), 'operador_id' => 999];

        $response = $this->actingAs($operador)->post(route('operador.actividades.store'), $datos);

        $response->assertRedirectToRoute('operador.actividades.index');
        $this->assertDatabaseHas('actividades', ['nombre' => 'Visita registrada', 'operador_id' => $operador->id,
            'estado_verificacion' => 'pendiente', 'fecha_verificacion' => null, 'precio' => null,
            'duracion_min' => null, 'cupo_maximo' => null, 'nivel_dificultad' => null]);
    }

    public function test_precio_cero_tambien_necesita_fuente_y_fecha_y_foto_necesita_licencia(): void
    {
        $operador = User::factory()->create(['rol' => 'operador']);
        $datos = array_replace($this->datosActividad(), ['tipo_precio' => 'fijo', 'precio' => 0, 'imagen_portada' => 'https://example.org/foto.jpg']);

        $this->actingAs($operador)->post(route('operador.actividades.store'), $datos)
            ->assertSessionHasErrors(['precio_fuente_url', 'precio_verificado_en', 'imagen_fuente_url', 'imagen_autor', 'imagen_licencia']);

        $this->assertDatabaseCount('actividades', 0);
    }

    public function test_costo_desactualizado_y_atractivo_de_otro_destino_son_rechazados(): void
    {
        $operador = User::factory()->create(['rol' => 'operador']);
        $datos = array_replace($this->datosActividad(), ['tipo_precio' => 'fijo', 'precio' => 10,
            'precio_fuente_url' => 'https://example.org/precio', 'precio_verificado_en' => now()->subDays(8)->toDateString(),
            'atractivo_id' => AtractivoFactory::new()->create()->id]);

        $this->actingAs($operador)->post(route('operador.actividades.store'), $datos)
            ->assertSessionHasErrors(['precio_verificado_en', 'atractivo_id']);

        $this->assertDatabaseCount('actividades', 0);
    }

    public function test_editar_registro_aprobado_requiere_nueva_revision(): void
    {
        $operador = User::factory()->create(['rol' => 'operador']);
        $actividad = Actividad::factory()->create(['operador_id' => $operador->id]);
        $datos = array_replace($this->datosActividad(), ['destino_id' => $actividad->destino_id]);

        $this->actingAs($operador)->put(route('operador.actividades.update', $actividad), $datos)
            ->assertRedirectToRoute('operador.actividades.index');

        $this->assertDatabaseHas('actividades', ['id' => $actividad->id, 'estado_verificacion' => 'pendiente', 'fecha_verificacion' => null]);
    }

    public function test_administrador_aprueba_con_confirmacion_y_el_catalogo_publica(): void
    {
        $admin = User::factory()->create(['rol' => 'administrador']);
        $actividad = Actividad::factory()->create(['estado_verificacion' => 'pendiente', 'fecha_verificacion' => null]);

        $this->actingAs($admin)->post(route('operador.revision.aprobar', ['tipo' => 'actividades', 'id' => $actividad->id]), ['confirmado' => 1])->assertRedirect();

        $this->assertDatabaseHas('actividades', ['id' => $actividad->id, 'estado_verificacion' => 'aprobado']);
        $this->get(route('catalogo.actividades.show', $actividad))->assertSee($actividad->nombre);
    }

    public function test_constructor_de_ruta_guarda_orden_y_no_inventa_metricas(): void
    {
        $operador = User::factory()->create(['rol' => 'operador']);
        $datos = ['nombre' => 'Ruta propuesta', 'descripcion' => 'Visitas documentadas.', 'destino_id' => DestinoFactory::new()->create()->id,
            'tipo_ruta' => 'lineal', 'es_propuesta' => true, 'transporte_recomendado' => 'a_pie',
            'tipo_precio' => 'variable', 'fuente_url' => 'https://example.org/ruta', 'estado' => 'activo',
            'puntos' => [['nombre_parada' => 'Segunda elegida primero'], ['nombre_parada' => 'Primera elegida después']]];

        $this->actingAs($operador)->post(route('operador.rutas.store'), $datos)->assertRedirectToRoute('operador.rutas.index');

        $ruta = Ruta::firstOrFail();
        $this->assertDatabaseHas('rutas', ['id' => $ruta->id, 'distancia_km' => null, 'duracion_estimada_horas' => null, 'costo_estimado' => null, 'estado_verificacion' => 'pendiente']);
        $this->assertDatabaseHas('ruta_puntos', ['ruta_id' => $ruta->id, 'orden' => 1, 'nombre_parada' => 'Segunda elegida primero', 'tiempo_estadia_min' => null]);
        $this->assertDatabaseHas('ruta_puntos', ['ruta_id' => $ruta->id, 'orden' => 2, 'nombre_parada' => 'Primera elegida después', 'tiempo_traslado_min' => null]);
        $this->assertNull($ruta->tiempo_total_minutos);
    }

    public function test_ruta_suma_solo_metricas_completas_documentadas(): void
    {
        $ruta = app(RutaService::class)->crearConPuntos(Ruta::factory()->make()->getAttributes(), [
            ['nombre_parada' => 'Inicio', 'tiempo_estadia_min' => 30],
            ['nombre_parada' => 'Fin', 'tiempo_estadia_min' => 20, 'tiempo_traslado_min' => 10, 'distancia_desde_anterior_km' => 1.5],
        ]);

        $this->assertDatabaseHas('rutas', ['id' => $ruta->id, 'duracion_estimada_horas' => 1, 'distancia_km' => 1.5]);
    }

    public function test_formularios_y_paneles_renderizan_y_escapan_texto_del_constructor(): void
    {
        $admin = User::factory()->create(['rol' => 'administrador']);
        $ruta = Ruta::factory()->create(['operador_id' => $admin->id]);
        $ruta->puntos()->create(['nombre_parada' => '</script><script>alert(1)</script>', 'orden' => 1]);

        $this->actingAs($admin)->get(route('operador.actividades.create'))->assertSee('Fuente del lugar o actividad');
        $this->get(route('operador.rutas.create'))->assertSee('Paradas del recorrido');
        $this->get(route('operador.rutas.edit', $ruta))->assertDontSee('</script><script>alert(1)</script>', false);
        $this->get(route('operador.revision.index'))->assertSee('Revisión');
        $this->get(route('operador.importaciones.index'))->assertSee('Importar');
    }

    public function test_login_correcto_y_logout_invalidan_la_sesion(): void
    {
        $operador = User::factory()->create(['rol' => 'operador', 'password' => 'ClaveSoloParaPrueba123']);

        $this->post(route('login'), ['email' => $operador->email, 'password' => 'ClaveSoloParaPrueba123'])->assertRedirectToRoute('operador.actividades.index');
        $this->assertAuthenticatedAs($operador);
        $this->post(route('logout'))->assertRedirectToRoute('catalogo.actividades.index');
        $this->assertGuest();
    }
}
