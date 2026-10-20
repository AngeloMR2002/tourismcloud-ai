<?php

namespace Tests\Feature;

use App\Modules\Actividades\Models\Actividad;
use App\Modules\Actividades\Services\ImportadorDemoActividadesRutas;
use App\Modules\Atractivos\Models\Atractivo;
use App\Modules\Destinos\Models\Destino;
use App\Modules\Itinerarios\Models\Itinerario;
use App\Modules\Rutas\Models\Ruta;
use App\Modules\Rutas\Models\RutaParada;
use App\Modules\Rutas\Services\RutaService;
use App\Modules\Usuarios\Models\Usuario;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ActividadesRutasTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $operador;

    private Usuario $otro;

    private Destino $destino;

    private Destino $ajeno;

    private Atractivo $primero;

    private Atractivo $segundo;

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
        Storage::fake('public');
        $this->operador = Usuario::create(UserFactory::new()->raw(['rol' => 'operador_turistico']));
        $this->otro = Usuario::create(UserFactory::new()->raw(['rol' => 'operador_turistico']));
        $this->destino = Destino::create(['nombre' => 'Cusco de prueba', 'pais' => 'Perú', 'operador_id' => $this->operador->id, 'estado' => 'activo']);
        $this->ajeno = Destino::create(['nombre' => 'Otro destino', 'pais' => 'Perú', 'operador_id' => $this->otro->id, 'estado' => 'activo']);
        $this->primero = $this->atractivo('Primera parada', 10);
        $this->segundo = $this->atractivo('Segunda parada', 15);
    }

    private function atractivo(string $nombre, int $costo = 0): Atractivo
    {
        return Atractivo::create(['nombre' => $nombre, 'destino_id' => $this->destino->id, 'costo_entrada' => $costo, 'estado' => 'activo']);
    }

    private function actividad(array $datos = []): Actividad
    {
        return Actividad::factory()->create(array_replace(['nombre' => 'Caminata cultural', 'destino_id' => $this->destino->id, 'costo' => 20, 'duracion_estimada_min' => 60, 'estado' => 'activo'], $datos));
    }

    private function datosRuta(array $datos = []): array
    {
        return array_replace(['nombre' => 'Ruta cultural', 'destino_id' => $this->destino->id, 'distancia_km' => 4.5, 'duracion_estimada_min' => 120, 'estado' => 'activo', 'paradas' => [$this->primero->id, $this->segundo->id]], $datos);
    }

    private function ruta(array $datos = []): Ruta
    {
        $datos = $this->datosRuta($datos);
        $paradas = $datos['paradas'];
        unset($datos['paradas']);
        $ruta = Ruta::factory()->create($datos);
        foreach ($paradas as $orden => $id) {
            DB::table('ruta_paradas')->insert(['ruta_id' => $ruta->id, 'atractivo_id' => $id, 'orden' => $orden + 1]);
        }

        return $ruta;
    }

    public function test_gestion_exige_autenticacion_y_rol_real(): void
    {
        foreach (['actividades', 'rutas'] as $modulo) {
            $this->get(route('operador.'.$modulo.'.index'))->assertRedirect(route('login'));
        }
        $turista = Usuario::create(UserFactory::new()->raw());
        $this->actingAs($turista);
        foreach (['actividades', 'rutas'] as $modulo) {
            $this->get(route('operador.'.$modulo.'.index'))->assertForbidden();
        }
        $this->operador->update(['estado' => 'inactivo']);
        $this->actingAs($this->operador)->get(route('operador.actividades.index'))->assertRedirect(route('login'));
    }

    public function test_dashboard_itinerarios_y_agenda_usan_el_usuario_autenticado(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
        $this->get(route('itinerarios.index'))->assertRedirect(route('login'));
        $this->get(route('agenda.index'))->assertRedirect(route('login'));

        $turista = Usuario::create(UserFactory::new()->raw(['rol' => 'turista']));
        $this->actingAs($turista);

        $this->get(route('dashboard'))->assertOk()->assertSee('Dashboard');
        $this->get(route('itinerarios.index'))->assertOk()->assertSee('Mis itinerarios');
        $this->get(route('agenda.index'))->assertOk()->assertSee('Agenda de itinerarios');

        $this->post(route('itinerarios.store'), [
            'destino_id' => $this->destino->id,
            'nombre' => 'Viaje de prueba',
            'descripcion' => 'Plan creado por el turista autenticado.',
            'presupuesto_total' => 250,
            'dias' => 3,
            'fecha_inicio' => '2026-12-02',
        ])->assertSessionHasNoErrors()->assertRedirect(route('itinerarios.index'));

        $itinerario = Itinerario::where('nombre', 'Viaje de prueba')->firstOrFail();
        $this->assertSame($turista->id, $itinerario->turista_id);
        $this->assertSame(3, DB::table('itinerario_dias')->where('itinerario_id', $itinerario->id)->count());
        $this->assertSame(['2026-12-02', '2026-12-03', '2026-12-04'], DB::table('itinerario_dias')->where('itinerario_id', $itinerario->id)->orderBy('numero_dia')->pluck('fecha')->map(fn ($fecha) => substr((string) $fecha, 0, 10))->all());
        $otroTurista = Usuario::create(UserFactory::new()->raw(['rol' => 'turista']));
        Itinerario::create([
            'turista_id' => $otroTurista->id,
            'destino_id' => $this->destino->id,
            'nombre' => 'Itinerario privado ajeno',
            'dias' => 1,
            'estado' => 'generado',
        ]);
        $this->get(route('dashboard'))->assertOk()->assertSee('Viaje de prueba')->assertDontSee('Itinerario privado ajeno');
        $this->get(route('itinerarios.show', $itinerario))->assertOk()->assertSee('Viaje de prueba')->assertSee('Día 3');
        $this->get(route('agenda.index'))->assertOk()->assertSee('Viaje de prueba');

        $this->actingAs($this->operador)->get(route('dashboard'))->assertForbidden();
        $this->actingAs($this->operador)->get(route('itinerarios.index'))->assertForbidden();
        $this->actingAs($this->operador)->get(route('agenda.index'))->assertForbidden();
        $this->actingAs($this->otro)->get(route('itinerarios.show', $itinerario))->assertForbidden();
        $this->actingAs($turista)->delete(route('itinerarios.destroy', $itinerario))->assertRedirect(route('itinerarios.index'));
        $this->assertDatabaseMissing('itinerarios', ['id' => $itinerario->id]);
    }

    public function test_actividad_crud_portada_y_eliminacion_logica(): void
    {
        $this->actingAs($this->operador);
        $this->post(route('operador.actividades.store'), ['destino_id' => $this->destino->id, 'nombre' => 'Taller andino', 'costo' => 0, 'duracion_estimada_min' => 90, 'latitud' => -13.5, 'longitud' => -71.9, 'estado' => 'activo', 'imagen_portada' => UploadedFile::fake()->image('portada.jpg')])->assertSessionHasNoErrors()->assertRedirect();
        $actividad = Actividad::where('nombre', 'Taller andino')->firstOrFail();
        Storage::disk('public')->assertExists($actividad->imagen_portada);
        $this->get(route('operador.actividades.edit', $actividad))->assertOk();
        $this->get(route('catalogo.actividades.show', $actividad))
            ->assertOk()
            ->assertSee('Taller andino')
            ->assertSee('actividad-public-map')
            ->assertSee('maps');
        $this->put(route('operador.actividades.update', $actividad), ['destino_id' => $this->destino->id, 'nombre' => 'Taller editado', 'costo' => 40, 'estado' => 'activo', 'quitar_portada' => 1])->assertSessionHasNoErrors()->assertRedirect();
        $this->assertNull($actividad->fresh()->imagen_portada);
        $this->patch(route('operador.actividades.toggle-estado', $actividad))->assertRedirect();
        $this->get(route('catalogo.actividades.show', $actividad))->assertNotFound();
        $this->delete(route('operador.actividades.destroy', $actividad))->assertRedirect();
        $this->assertSoftDeleted('actividades', ['id' => $actividad->id]);
    }

    public function test_propiedad_en_todas_las_mutaciones(): void
    {
        $actividad = $this->actividad(['destino_id' => $this->ajeno->id]);
        $ruta = $this->ruta(['destino_id' => $this->ajeno->id]);
        $this->actingAs($this->operador);
        foreach (['actividades' => $actividad, 'rutas' => $ruta] as $modulo => $modelo) {
            $this->get(route('operador.'.$modulo.'.edit', $modelo))->assertForbidden();
            $this->put(route('operador.'.$modulo.'.update', $modelo), [])->assertForbidden();
            $this->patch(route('operador.'.$modulo.'.toggle-estado', $modelo))->assertForbidden();
            $this->delete(route('operador.'.$modulo.'.destroy', $modelo))->assertForbidden();
        }
        $this->post(route('operador.actividades.store'), ['destino_id' => $this->ajeno->id, 'nombre' => 'Intento', 'costo' => 0, 'estado' => 'activo'])->assertForbidden();
        $paradasAjenas = [];
        foreach (['Ajena primera', 'Ajena segunda'] as $nombre) {
            $paradasAjenas[] = Atractivo::create(['nombre' => $nombre, 'destino_id' => $this->ajeno->id, 'costo_entrada' => 0, 'estado' => 'activo'])->id;
        }
        $this->post(route('operador.rutas.store'), $this->datosRuta(['destino_id' => $this->ajeno->id, 'paradas' => $paradasAjenas]))->assertForbidden();
        $this->get(route('operador.actividades.index'))->assertDontSee($actividad->nombre);
        $this->get(route('operador.rutas.index'))->assertDontSee($ruta->nombre);
    }

    public function test_filtros_y_publicacion_actividades(): void
    {
        $visible = $this->actividad(['nombre' => 'Actividad económica']);
        $this->actividad(['nombre' => 'Muy costosa', 'costo' => 200]);
        $this->actividad(['nombre' => 'Muy larga', 'duracion_estimada_min' => 400]);
        $this->actividad(['nombre' => 'Inactiva', 'estado' => 'inactivo']);
        $this->get(route('catalogo.actividades.index', ['destino_id' => $this->destino->id, 'costo_max' => 25, 'duracion_max' => 80, 'buscar' => 'Actividad']))->assertOk()->assertSee($visible->nombre)->assertDontSee('Muy costosa')->assertDontSee('Muy larga')->assertDontSee('Inactiva');
        $this->actingAs($this->operador)->get(route('operador.actividades.index', ['buscar' => 'Inactiva', 'estado' => 'inactivo']))->assertOk()->assertSee('Inactiva')->assertDontSee($visible->nombre);
        $this->destino->update(['estado' => 'inactivo']);
        $this->get(route('catalogo.actividades.show', $visible))->assertNotFound();
        $this->get(route('operador.actividades.edit', $visible))->assertOk();
    }

    public function test_campos_e_imagenes_invalidos_no_se_guardan(): void
    {
        $this->actingAs($this->operador);
        $this->post(route('operador.actividades.store'), ['destino_id' => $this->destino->id, 'nombre' => 'Inválida', 'costo' => -1, 'duracion_estimada_min' => 0, 'latitud' => 91, 'longitud' => 181, 'estado' => 'activo', 'imagen_portada' => UploadedFile::fake()->create('mal.pdf', 20, 'application/pdf')])->assertSessionHasErrors(['costo', 'duracion_estimada_min', 'latitud', 'longitud', 'imagen_portada']);
        $this->post(route('operador.rutas.store'), $this->datosRuta(['distancia_km' => -1, 'duracion_estimada_min' => 0, 'imagen_portada' => UploadedFile::fake()->image('grande.png')->size(5121)]))->assertSessionHasErrors(['distancia_km', 'duracion_estimada_min', 'imagen_portada']);
        $this->assertDatabaseCount('actividades', 0);
        $this->assertDatabaseCount('rutas', 0);
    }

    public function test_ruta_crud_paradas_ordenadas_portada_y_borrado(): void
    {
        $this->actingAs($this->operador)->post(route('operador.rutas.store'), $this->datosRuta(['imagen_portada' => UploadedFile::fake()->image('ruta.png')]))->assertSessionHasNoErrors()->assertRedirect();
        $ruta = Ruta::firstOrFail();
        Storage::disk('public')->assertExists($ruta->imagen_portada);
        $this->get(route('operador.rutas.edit', $ruta))->assertOk()->assertSee('Subir')->assertSee('Bajar');
        $this->get(route('catalogo.rutas.show', $ruta))
            ->assertOk()
            ->assertSee('25.00')
            ->assertSee('mapa-ruta-publica')
            ->assertSee(route('catalogo.atractivos.show', $this->primero), false);
        $this->put(route('operador.rutas.update', $ruta), $this->datosRuta(['nombre' => 'Ruta editada', 'paradas' => [$this->segundo->id, $this->primero->id], 'quitar_portada' => 1]))->assertSessionHasNoErrors()->assertRedirect();
        $this->assertSame([$this->segundo->id, $this->primero->id], DB::table('ruta_paradas')->where('ruta_id', $ruta->id)->orderBy('orden')->pluck('atractivo_id')->all());
        $this->assertNull($ruta->fresh()->imagen_portada);
        $this->patch(route('operador.rutas.toggle-estado', $ruta))->assertRedirect();
        $this->get(route('catalogo.rutas.show', $ruta))->assertNotFound();
        $this->delete(route('operador.rutas.destroy', $ruta))->assertRedirect();
        $this->assertDatabaseMissing('rutas', ['id' => $ruta->id]);
        $this->assertDatabaseMissing('ruta_paradas', ['ruta_id' => $ruta->id]);
    }

    public function test_paradas_repetidas_insuficientes_inactivas_ajenas_y_eliminadas(): void
    {
        $ajeno = Atractivo::create(['nombre' => 'Ajeno', 'destino_id' => $this->ajeno->id, 'estado' => 'activo', 'costo_entrada' => 0]);
        $inactivo = $this->atractivo('Inactivo');
        $inactivo->update(['estado' => 'inactivo']);
        $eliminado = $this->atractivo('Eliminado');
        $eliminado->delete();
        $this->actingAs($this->operador);
        foreach ([[$this->primero->id], [$this->primero->id, $this->primero->id], [$this->primero->id, $ajeno->id], [$this->primero->id, $inactivo->id], [$this->primero->id, $eliminado->id]] as $paradas) {
            $this->post(route('operador.rutas.store'), $this->datosRuta(['paradas' => $paradas]))->assertSessionHasErrors();
        }
        $this->assertDatabaseCount('rutas', 0);
        $this->assertDatabaseCount('ruta_paradas', 0);
        $ruta = $this->ruta();
        $this->put(route('operador.rutas.update', $ruta), $this->datosRuta(['paradas' => [$this->primero->id]]))->assertSessionHasErrors();
        $this->assertSame(2, DB::table('ruta_paradas')->where('ruta_id', $ruta->id)->count());
    }

    public function test_filtros_y_retiro_publico_de_rutas_sin_paradas_disponibles(): void
    {
        $ruta = $this->ruta();
        $this->ruta(['nombre' => 'Distante', 'distancia_km' => 200]);
        $this->ruta(['nombre' => 'Extensa', 'duracion_estimada_min' => 500]);
        $this->get(route('catalogo.rutas.index', ['destino_id' => $this->destino->id, 'distancia_max' => 10, 'duracion_max' => 200]))->assertOk()->assertSee('Ruta cultural')->assertDontSee('Distante')->assertDontSee('Extensa');
        $this->primero->update(['estado' => 'inactivo']);
        $this->get(route('catalogo.rutas.show', $ruta))->assertNotFound();
        $this->primero->update(['estado' => 'activo']);
        $this->primero->delete();
        $this->get(route('catalogo.rutas.show', $ruta))->assertNotFound();
        $this->primero->restore();
        $this->primero->forceDelete();
        $this->get(route('catalogo.rutas.show', $ruta))->assertNotFound();
        $this->get(route('catalogo.rutas.index'))->assertDontSee('Ruta cultural');
    }

    public function test_vistas_sin_imagen_y_destinos_inactivos(): void
    {
        $actividad = $this->actividad(['imagen_portada' => 'actividades/no-existe.jpg']);
        $ruta = $this->ruta(['imagen_portada' => 'rutas/no-existe.jpg']);
        $this->get(route('catalogo.actividades.show', $actividad))->assertOk()->assertDontSee('/storage/actividades/no-existe.jpg');
        $this->get(route('catalogo.rutas.show', $ruta))->assertOk()->assertDontSee('/storage/rutas/no-existe.jpg');
        $this->destino->update(['estado' => 'inactivo']);
        $this->actingAs($this->operador)->post(route('operador.actividades.store'), ['destino_id' => $this->destino->id, 'nombre' => 'No publicar', 'costo' => 0, 'estado' => 'activo'])->assertSessionHasErrors('destino_id');
        $this->post(route('operador.rutas.store'), $this->datosRuta())->assertSessionHasErrors('destino_id');
        $this->get(route('catalogo.rutas.show', $ruta))->assertNotFound();
    }

    private function mapeoDemo(): array
    {
        $this->destino->update(['nombre' => 'Cusco']);
        $mapeo = ['destinos' => ['cusco' => $this->destino->id], 'atractivos' => []];
        foreach (['saqsaywaman', 'qenqo', 'tambomachay', 'koricancha', 'catedral'] as $alias) {
            $mapeo['atractivos'][$alias] = $this->atractivo($alias)->id;
        }

        return $mapeo;
    }

    public function test_importacion_demo_es_idempotente_y_no_modifica_otros_modulos(): void
    {
        $mapeo = $this->mapeoDemo();
        $destinosAntes = DB::table('destinos')->orderBy('id')->get()->toJson();
        $atractivosAntes = DB::table('atractivos')->orderBy('id')->get()->toJson();
        $importador = app(ImportadorDemoActividadesRutas::class);
        $importador->import($mapeo);
        $this->assertDatabaseCount('actividades', 6);
        $this->assertDatabaseCount('rutas', 2);
        $primera = Actividad::firstOrFail();
        $primera->update(['descripcion' => 'Cambio manual que debe conservarse']);
        $ultima = Actividad::orderByDesc('id')->firstOrFail();
        $ultima->delete();
        $importador->import($mapeo);
        $this->assertDatabaseCount('actividades', 6);
        $this->assertDatabaseCount('rutas', 2);
        $this->assertSame('Cambio manual que debe conservarse', $primera->fresh()->descripcion);
        $this->assertSoftDeleted('actividades', ['id' => $ultima->id]);
        $this->assertSame($destinosAntes, DB::table('destinos')->orderBy('id')->get()->toJson());
        $this->assertSame($atractivosAntes, DB::table('atractivos')->orderBy('id')->get()->toJson());
        Storage::disk('public')->assertExists('actividades-rutas-demo/cusco-plaza.jpg');
        $this->get(route('catalogo.actividades.show', $primera))->assertOk()->assertSee('/storage/actividades-rutas-demo/', false);
    }

    public function test_importacion_invalida_cancela_todo_antes_de_escribir(): void
    {
        $mapeo = $this->mapeoDemo();
        $invalido = $mapeo;
        $invalido['atractivos']['catedral'] = 99999999;
        foreach ([$invalido, array_replace($mapeo, ['destinos' => ['cusco' => $this->ajeno->id]])] as $referencias) {
            $fallo = false;
            try {
                app(ImportadorDemoActividadesRutas::class)->import($referencias);
            } catch (\InvalidArgumentException|ValidationException|\RuntimeException $exception) {
                $fallo = true;
            }
            $this->assertTrue($fallo, 'Debe rechazar todas las referencias incorrectas.');
            $this->assertDatabaseCount('actividades', 0);
            $this->assertDatabaseCount('rutas', 0);
            $this->assertDatabaseCount('ruta_paradas', 0);
        }
    }

    public function test_portadas_webp_reemplazo_limpieza_y_imagen_compartida(): void
    {
        $this->actingAs($this->operador);
        foreach (['actividades', 'rutas'] as $modulo) {
            $datos = $modulo === 'rutas' ? $this->datosRuta() : ['nombre' => 'WebP de prueba', 'destino_id' => $this->destino->id, 'costo' => 0, 'estado' => 'activo'];
            $datos['imagen_portada'] = UploadedFile::fake()->image('imagen.webp')->size(5120);
            $this->post(route('operador.'.$modulo.'.store'), $datos)->assertSessionHasNoErrors()->assertRedirect();
            $registro = $modulo === 'rutas' ? Ruta::firstOrFail() : Actividad::firstOrFail();
            $anterior = $registro->imagen_portada;
            Storage::disk('public')->assertExists($anterior);
            $datos['imagen_portada'] = UploadedFile::fake()->image('nueva.jpg');
            $this->put(route('operador.'.$modulo.'.update', $registro), $datos)->assertSessionHasNoErrors()->assertRedirect();
            Storage::disk('public')->assertMissing($anterior);
            Storage::disk('public')->assertExists($registro->fresh()->imagen_portada);
            Storage::disk('public')->put('actividades-rutas-demo/compartida.jpg', 'imagen de prueba');
            $registro->update(['imagen_portada' => 'actividades-rutas-demo/compartida.jpg']);
            unset($datos['imagen_portada']);
            $datos['quitar_portada'] = 1;
            $this->put(route('operador.'.$modulo.'.update', $registro), $datos)->assertSessionHasNoErrors()->assertRedirect();
            Storage::disk('public')->assertExists('actividades-rutas-demo/compartida.jpg');
        }
    }

    public function test_filtros_malformados_y_svg_se_rechazan_sin_error_sql(): void
    {
        $this->getJson(route('catalogo.actividades.index', ['destino_id' => str_repeat('9', 60)]))->assertUnprocessable();
        $this->getJson(route('catalogo.rutas.index', ['destino_id' => ['inválido']]))->assertUnprocessable();
        $this->actingAs($this->operador);
        foreach (['actividades', 'rutas'] as $modulo) {
            $datos = $modulo === 'rutas' ? $this->datosRuta() : ['nombre' => 'SVG inválido', 'destino_id' => $this->destino->id, 'costo' => 0, 'estado' => 'activo'];
            $datos['imagen_portada'] = UploadedFile::fake()->createWithContent('imagen.svg', '<svg xmlns="http://www.w3.org/2000/svg"><rect width="10" height="10"/></svg>');
            $this->post(route('operador.'.$modulo.'.store'), $datos)->assertSessionHasErrors('imagen_portada');
        }
        $this->postJson(route('operador.rutas.store'), $this->datosRuta(['paradas' => [$this->primero->id, str_repeat('9', 60)]]))->assertUnprocessable();
        $this->assertDatabaseCount('rutas', 0);
    }

    public function test_guardado_de_ruta_revierte_paradas_y_portada_si_falla_la_transaccion(): void
    {
        $evento = 'eloquent.creating: '.RutaParada::class;
        $paradasInsertadas = 0;
        Event::listen($evento, function () use (&$paradasInsertadas): void {
            $paradasInsertadas++;
            if ($paradasInsertadas === 2) {
                throw new \RuntimeException('Fallo simulado en la segunda parada.');
            }
        });
        $fallo = false;
        $datos = $this->datosRuta();
        $paradas = $datos['paradas'];
        unset($datos['paradas']);
        try {
            app(RutaService::class)->crear($datos, $paradas, UploadedFile::fake()->image('transaccion.jpg'));
        } catch (\RuntimeException $exception) {
            $fallo = true;
            $this->assertSame('Fallo simulado en la segunda parada.', $exception->getMessage());
        } finally {
            Event::forget($evento);
        }
        $this->assertTrue($fallo);
        $this->assertDatabaseCount('rutas', 0);
        $this->assertDatabaseCount('ruta_paradas', 0);
        $this->assertSame([], Storage::disk('public')->allFiles('rutas/portadas'));
    }

    public function test_filtros_operador_y_rutas_legacy_repetidas_no_publicables(): void
    {
        $visible = $this->ruta(['nombre' => 'Recorrido seleccionado']);
        $this->ruta(['nombre' => 'Recorrido inactivo', 'estado' => 'inactivo']);
        $this->actingAs($this->operador)->get(route('operador.rutas.index', ['buscar' => 'seleccionado', 'estado' => 'activo', 'destino_id' => $this->destino->id]))->assertOk()->assertSee($visible->nombre)->assertDontSee('Recorrido inactivo');
        $repetida = $this->ruta(['nombre' => 'Ruta legacy duplicada', 'paradas' => [$this->primero->id, $this->primero->id]]);
        $this->get(route('catalogo.rutas.show', $repetida))->assertNotFound();
        $this->get(route('catalogo.rutas.index'))->assertDontSee('Ruta legacy duplicada');
        $this->destino->update(['estado' => 'inactivo']);
        $this->get(route('operador.rutas.edit', $visible))->assertOk();
        $this->put(route('operador.rutas.update', $visible), $this->datosRuta(['nombre' => 'Recorrido editado sin publicar']))->assertSessionHasNoErrors()->assertRedirect();
        $this->get(route('catalogo.rutas.show', $visible))->assertNotFound();
    }
}
