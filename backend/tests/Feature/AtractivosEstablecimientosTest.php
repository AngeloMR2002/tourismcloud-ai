<?php

namespace Tests\Feature;

use App\Modules\Atractivos\Models\Atractivo;
use App\Modules\Atractivos\Models\CategoriaInteres;
use App\Modules\Atractivos\Models\Imagen;
use App\Modules\Destinos\Models\Destino;
use App\Modules\Establecimientos\Models\Establecimiento;
use App\Modules\Usuarios\Models\Usuario;
use Database\Factories\UserFactory;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AtractivosEstablecimientosTest extends TestCase
{
    use RefreshDatabase;

    private FilesystemAdapter $storage;
    private Usuario $operador;
    private Usuario $otroOperador;
    private Usuario $proveedor;
    private Usuario $otroProveedor;
    private Usuario $turista;
    private Destino $destino;
    private Destino $destinoAjeno;
    private CategoriaInteres $categoriaAventura;
    private CategoriaInteres $categoriaCultura;

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
        $this->storage = Storage::fake('public');

        $this->operador = Usuario::create(UserFactory::new()->raw(['rol' => 'operador_turistico']));
        $this->otroOperador = Usuario::create(UserFactory::new()->raw(['rol' => 'operador_turistico']));
        $this->proveedor = Usuario::create(UserFactory::new()->raw(['rol' => 'proveedor']));
        $this->otroProveedor = Usuario::create(UserFactory::new()->raw(['rol' => 'proveedor']));
        $this->turista = Usuario::create(UserFactory::new()->raw(['rol' => 'turista']));

        $this->destino = Destino::create([
            'nombre'      => 'Cusco Histórico',
            'pais'        => 'Perú',
            'operador_id' => $this->operador->id,
            'estado'      => 'activo',
        ]);

        $this->destinoAjeno = Destino::create([
            'nombre'      => 'Arequipa Colonial',
            'pais'        => 'Perú',
            'operador_id' => $this->otroOperador->id,
            'estado'      => 'activo',
        ]);

        $this->categoriaAventura = CategoriaInteres::create([
            'nombre'      => 'Aventura y Trekking',
            'descripcion' => 'Actividades de montaña y aventura',
        ]);

        $this->categoriaCultura = CategoriaInteres::create([
            'nombre'      => 'Historia y Cultura',
            'descripcion' => 'Monumentos arqueológicos y museos',
        ]);
    }

    // =========================================================================
    // SECCIÓN 1: ATRACTIVOS
    // =========================================================================

    public function test_atractivos_rutas_operador_requieren_autenticacion_y_rol(): void
    {
        // Invitado es redirigido a login
        $this->get(route('operador.atractivos.index'))->assertRedirect(route('login'));
        $this->get(route('operador.atractivos.create'))->assertRedirect(route('login'));

        // Turista y proveedor reciben 403 Forbidden
        $this->actingAs($this->turista)->get(route('operador.atractivos.index'))->assertForbidden();
        $this->actingAs($this->proveedor)->get(route('operador.atractivos.index'))->assertForbidden();

        // Operador inactivo es redirigido a login por el middleware de autenticación/rol
        $this->operador->update(['estado' => 'inactivo']);
        $this->actingAs($this->operador)->get(route('operador.atractivos.index'))->assertRedirect(route('login'));
    }

    public function test_operador_autenticado_lista_solo_sus_atractivos(): void
    {
        $atractivoPropio = Atractivo::create([
            'destino_id'    => $this->destino->id,
            'nombre'        => 'Sacsayhuamán Propio',
            'costo_entrada' => 70.00,
            'estado'        => 'activo',
        ]);

        $atractivoAjeno = Atractivo::create([
            'destino_id'    => $this->destinoAjeno->id,
            'nombre'        => 'Cañón del Colca Ajeno',
            'costo_entrada' => 80.00,
            'estado'        => 'activo',
        ]);

        $this->actingAs($this->operador)
            ->get(route('operador.atractivos.index'))
            ->assertOk()
            ->assertSee('Sacsayhuamán Propio')
            ->assertDontSee('Cañón del Colca Ajeno');
    }

    public function test_operador_crea_atractivo_valido_con_categorias(): void
    {
        $this->actingAs($this->operador);

        $payload = [
            'destino_id'            => $this->destino->id,
            'nombre'                => 'Ollantaytambo Mágico',
            'descripcion'           => 'Fortaleza inca en el Valle Sagrado.',
            'costo_entrada'         => 45.50,
            'duracion_estimada_min' => 120,
            'latitud'               => -13.2580,
            'longitud'              => -72.2630,
            'estado'                => 'activo',
            'categorias'            => [$this->categoriaAventura->id, $this->categoriaCultura->id],
            'imagen_portada'        => UploadedFile::fake()->image('valle.jpg'),
            'horarios'              => [
                'lunes'  => ['abre' => '08:00', 'cierra' => '17:30'],
                'martes' => ['abre' => '08:00', 'cierra' => '17:30'],
            ],
        ];

        $this->post(route('operador.atractivos.store'), $payload)
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('operador.atractivos.index'));

        $atractivo = Atractivo::where('nombre', 'Ollantaytambo Mágico')->firstOrFail();
        $this->assertEquals($this->destino->id, $atractivo->destino_id);
        $this->assertEquals(45.50, (float) $atractivo->costo_entrada);
        $this->assertTrue($atractivo->categorias->contains($this->categoriaAventura));
        $this->assertTrue($atractivo->categorias->contains($this->categoriaCultura));
        $this->storage->assertExists($atractivo->imagen_portada);

        // Verificar que los horarios se guardaron en la tabla horarios
        $this->assertDatabaseHas('horarios', [
            'atractivo_id' => $atractivo->id,
            'dia'          => 'lunes',
            'hora_inicio'  => '08:00:00',
            'hora_fin'     => '17:30:00',
        ]);
    }

    public function test_atractivos_validaciones(): void
    {
        $this->actingAs($this->operador);

        $payloadInvalido = [
            'destino_id'            => $this->destino->id,
            'nombre'                => '',
            'costo_entrada'         => -10,
            'latitud'               => 200,
            'longitud'              => -200,
            'duracion_estimada_min' => 0,
            'estado'                => 'invalido',
            'imagen_portada'        => UploadedFile::fake()->create('documento.pdf', 100),
            'horarios'              => [
                'lunes' => ['abre' => '18:00', 'cierra' => '09:00'],
            ],
        ];

        $this->post(route('operador.atractivos.store'), $payloadInvalido)
            ->assertSessionHasErrors([
                'nombre',
                'descripcion',
                'costo_entrada',
                'latitud',
                'longitud',
                'duracion_estimada_min',
                'estado',
                'imagen_portada',
                'categorias',
                'horarios.lunes.cierra',
            ]);
    }

    public function test_atractivos_duracion_estimada_es_opcional_pero_demas_campos_son_obligatorios(): void
    {
        $this->actingAs($this->operador);

        // Sin duracion_estimada_min (debe crearse exitosamente porque es el único campo opcional)
        $payloadValidoSinDuracion = [
            'destino_id'     => $this->destino->id,
            'nombre'         => 'Atractivo Sin Tiempo Estimado',
            'descripcion'    => 'Descripción completa del atractivo.',
            'costo_entrada'  => 0,
            'latitud'        => -13.5150,
            'longitud'       => -71.9750,
            'estado'         => 'activo',
            'categorias'     => [$this->categoriaAventura->id],
            'imagen_portada' => UploadedFile::fake()->image('portada_ok.jpg'),
        ];

        $this->post(route('operador.atractivos.store'), $payloadValidoSinDuracion)
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('operador.atractivos.index'));

        $this->assertDatabaseHas('atractivos', [
            'nombre'                => 'Atractivo Sin Tiempo Estimado',
            'duracion_estimada_min' => null,
        ]);

        // Sin imagen de portada -> debe fallar
        $payloadSinPortada = $payloadValidoSinDuracion;
        unset($payloadSinPortada['imagen_portada']);
        $payloadSinPortada['nombre'] = 'Atractivo Sin Portada';
        $this->post(route('operador.atractivos.store'), $payloadSinPortada)
            ->assertSessionHasErrors(['imagen_portada']);

        // Sin categorías -> debe fallar
        $payloadSinCategorias = $payloadValidoSinDuracion;
        unset($payloadSinCategorias['categorias']);
        $payloadSinCategorias['nombre'] = 'Atractivo Sin Categorias';
        $this->post(route('operador.atractivos.store'), $payloadSinCategorias)
            ->assertSessionHasErrors(['categorias']);

        // Sin ubicación exacta (latitud/longitud) -> debe fallar
        $payloadSinMapa = $payloadValidoSinDuracion;
        unset($payloadSinMapa['latitud'], $payloadSinMapa['longitud']);
        $payloadSinMapa['nombre'] = 'Atractivo Sin Mapa';
        $this->post(route('operador.atractivos.store'), $payloadSinMapa)
            ->assertSessionHasErrors(['latitud', 'longitud']);

        // Sin descripción -> debe fallar
        $payloadSinDesc = $payloadValidoSinDuracion;
        unset($payloadSinDesc['descripcion']);
        $payloadSinDesc['nombre'] = 'Atractivo Sin Descripcion';
        $this->post(route('operador.atractivos.store'), $payloadSinDesc)
            ->assertSessionHasErrors(['descripcion']);
    }

    public function test_operador_a_recibe_403_en_operaciones_sobre_atractivo_del_operador_b(): void
    {
        $atractivoAjeno = Atractivo::create([
            'destino_id'    => $this->destinoAjeno->id,
            'nombre'        => 'Monasterio Santa Catalina',
            'costo_entrada' => 40.00,
            'estado'        => 'activo',
        ]);

        $this->actingAs($this->operador);

        // Intento de ver edición
        $this->get(route('operador.atractivos.edit', $atractivoAjeno))->assertForbidden();

        // Intento de actualizar
        $this->patch(route('operador.atractivos.update', $atractivoAjeno), [
            'destino_id'    => $this->destino->id,
            'nombre'        => 'Intento de Hack',
            'descripcion'   => 'Intento de hack descripción',
            'costo_entrada' => 10,
            'latitud'       => -13.5150,
            'longitud'      => -71.9750,
            'categorias'    => [$this->categoriaCultura->id],
            'estado'        => 'activo',
        ])->assertForbidden();

        // Intento de toggle estado
        $this->patch(route('operador.atractivos.toggle-estado', $atractivoAjeno))->assertForbidden();

        // Intento de eliminar
        $this->delete(route('operador.atractivos.destroy', $atractivoAjeno))->assertForbidden();

        // Intento de crear atractivo en destino que pertenece a otro operador
        $this->post(route('operador.atractivos.store'), [
            'destino_id'     => $this->destinoAjeno->id,
            'nombre'         => 'Nuevo en Destino Ajeno',
            'descripcion'    => 'Descripción válida',
            'costo_entrada'  => 0,
            'latitud'        => -13.5150,
            'longitud'       => -71.9750,
            'categorias'     => [$this->categoriaCultura->id],
            'imagen_portada' => UploadedFile::fake()->image('portada.jpg'),
            'estado'         => 'activo',
        ])->assertForbidden();
    }

    public function test_operador_puede_editar_cambiar_estado_y_eliminar_atractivo_propio(): void
    {
        $atractivo = Atractivo::create([
            'destino_id'    => $this->destino->id,
            'nombre'        => 'Qorikancha Original',
            'costo_entrada' => 15.00,
            'estado'        => 'activo',
        ]);

        $this->actingAs($this->operador);

        // Editar
        $this->get(route('operador.atractivos.edit', $atractivo))->assertOk();
        $this->patch(route('operador.atractivos.update', $atractivo), [
            'destino_id'    => $this->destino->id,
            'nombre'        => 'Templo Qorikancha Editado',
            'descripcion'   => 'Descripción editada del templo',
            'latitud'       => -13.5150,
            'longitud'      => -71.9750,
            'categorias'    => [$this->categoriaCultura->id],
            'costo_entrada' => 20.00,
            'estado'        => 'activo',
        ])->assertSessionHasNoErrors()->assertRedirect(route('operador.atractivos.index'));

        $this->assertSame('Templo Qorikancha Editado', $atractivo->fresh()->nombre);

        // Toggle estado
        $this->patch(route('operador.atractivos.toggle-estado', $atractivo))->assertRedirect();
        $this->assertSame('inactivo', $atractivo->fresh()->estado);

        // Soft delete
        $this->delete(route('operador.atractivos.destroy', $atractivo))->assertRedirect(route('operador.atractivos.index'));
        $this->assertSoftDeleted('atractivos', ['id' => $atractivo->id]);
    }

    public function test_parametro_prueba_en_url_no_cambia_identidad_en_atractivos(): void
    {
        $atractivoAjeno = Atractivo::create([
            'destino_id'    => $this->destinoAjeno->id,
            'nombre'        => 'Atractivo Secreto Operador B',
            'costo_entrada' => 50.00,
            'estado'        => 'activo',
        ]);

        $this->actingAs($this->operador);

        $paramTest = '_' . 'operador' . '_id' . '_test';

        // Intentar pasar el ID del otro operador en la query string
        $this->get(route('operador.atractivos.index', [$paramTest => $this->otroOperador->id]))
            ->assertOk()
            ->assertDontSee('Atractivo Secreto Operador B');

        $this->get(route('operador.atractivos.edit', $atractivoAjeno) . '?' . $paramTest . '=' . $this->otroOperador->id)
            ->assertForbidden();
    }

    public function test_catalogo_atractivos_es_accesible_publicamente(): void
    {
        $atractivoActivo = Atractivo::create([
            'destino_id'    => $this->destino->id,
            'nombre'        => 'Atractivo Activo Público',
            'costo_entrada' => 30.00,
            'estado'        => 'activo',
        ]);

        $atractivoInactivo = Atractivo::create([
            'destino_id'    => $this->destino->id,
            'nombre'        => 'Atractivo Inactivo Oculto',
            'costo_entrada' => 30.00,
            'estado'        => 'inactivo',
        ]);

        // Sin autenticación (invitado)
        $this->get(route('catalogo.atractivos.index'))
            ->assertOk()
            ->assertSee('Atractivo Activo Público')
            ->assertDontSee('Atractivo Inactivo Oculto');

        $this->get(route('catalogo.atractivos.show', $atractivoActivo))
            ->assertOk()
            ->assertSee('Atractivo Activo Público');

        $this->get(route('catalogo.atractivos.show', $atractivoInactivo))
            ->assertNotFound();
    }

    // =========================================================================
    // SECCIÓN 2: ESTABLECIMIENTOS
    // =========================================================================

    public function test_establecimientos_rutas_proveedor_requieren_autenticacion_y_rol(): void
    {
        // Invitado es redirigido a login
        $this->get(route('proveedor.establecimientos.index'))->assertRedirect(route('login'));
        $this->get(route('proveedor.establecimientos.create'))->assertRedirect(route('login'));

        // Turista y operador reciben 403 Forbidden
        $this->actingAs($this->turista)->get(route('proveedor.establecimientos.index'))->assertForbidden();
        $this->actingAs($this->operador)->get(route('proveedor.establecimientos.index'))->assertForbidden();

        // Proveedor inactivo es redirigido a login
        $this->proveedor->update(['estado' => 'inactivo']);
        $this->actingAs($this->proveedor)->get(route('proveedor.establecimientos.index'))->assertRedirect(route('login'));
    }

    public function test_proveedor_crea_establecimiento_con_proveedor_id_autenticado_y_categorias(): void
    {
        $this->actingAs($this->proveedor);

        $payload = [
            'destino_id'     => $this->destino->id,
            'proveedor_id'   => $this->otroProveedor->id, // Intento de inyectar proveedor_id ajeno desde el formulario
            'tipo'           => 'hotel',
            'nombre'         => 'Hotel San Blas Boutique',
            'descripcion'    => 'Cómodo hotel en barrio tradicional.',
            'direccion'      => 'Calle Tandapata 450',
            'latitud'        => -13.5150,
            'longitud'       => -71.9750,
            'rango_precio'   => 'Medio',
            'estado'         => 'activo',
            'categorias'     => [$this->categoriaCultura->id],
            'imagen_portada' => UploadedFile::fake()->image('portada_hotel.jpg'),
            'galeria'        => [UploadedFile::fake()->image('habitacion.jpg')],
            'horarios'       => [
                'lunes'   => ['abre' => '07:00', 'cierra' => '23:00'],
                'domingo' => ['abre' => '08:00', 'cierra' => '22:00'],
            ],
        ];

        $this->post(route('proveedor.establecimientos.store'), $payload)
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('proveedor.establecimientos.index'));

        $establecimiento = Establecimiento::where('nombre', 'Hotel San Blas Boutique')->firstOrFail();

        // Debe haberse asignado al usuario autenticado ($this->proveedor), NO al inyectado en el form
        $this->assertSame($this->proveedor->id, $establecimiento->proveedor_id);
        $this->assertTrue($establecimiento->categorias->contains($this->categoriaCultura));
        $this->storage->assertExists($establecimiento->imagen_portada);

        // Verificar horarios en base de datos
        $this->assertDatabaseHas('horarios', [
            'establecimiento_id' => $establecimiento->id,
            'dia'                => 'lunes',
            'hora_inicio'        => '07:00',
            'hora_fin'           => '23:00',
        ]);
    }

    public function test_establecimientos_validaciones(): void
    {
        $this->actingAs($this->proveedor);

        $payloadInvalido = [
            'destino_id'   => $this->destino->id,
            'tipo'         => 'espacial', // fuera de hotel/restaurante/transporte/agencia/otro
            'rango_precio' => 'SuperLujo', // fuera de Bajo/Medio/Alto/Lujoso
            'latitud'      => 200,
            'longitud'     => -200,
            'nombre'       => '',
            'estado'       => 'pendiente',
            'horarios'     => [
                'lunes' => ['abre' => '18:00', 'cierra' => '09:00'],
            ],
        ];

        $this->post(route('proveedor.establecimientos.store'), $payloadInvalido)
            ->assertSessionHasErrors([
                'tipo',
                'rango_precio',
                'latitud',
                'longitud',
                'nombre',
                'descripcion',
                'direccion',
                'imagen_portada',
                'categorias',
                'estado',
                'horarios.lunes.cierra',
            ]);
    }

    public function test_establecimientos_todos_los_campos_son_obligatorios(): void
    {
        $this->actingAs($this->proveedor);

        $payloadBase = [
            'destino_id'     => $this->destino->id,
            'tipo'           => 'restaurante',
            'nombre'         => 'Picantería El Fogón',
            'descripcion'    => 'Picantería típica con platos tradicionales.',
            'direccion'      => 'Av. Sol 500',
            'rango_precio'   => 'Medio',
            'latitud'        => -13.5150,
            'longitud'       => -71.9750,
            'estado'         => 'activo',
            'categorias'     => [$this->categoriaCultura->id],
            'imagen_portada' => UploadedFile::fake()->image('portada_fogon.jpg'),
        ];

        // Creación completa válida
        $this->post(route('proveedor.establecimientos.store'), $payloadBase)
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('proveedor.establecimientos.index'));

        // Sin descripción -> debe fallar
        $p = $payloadBase; unset($p['descripcion']);
        $p['nombre'] = 'Sin Descripcion';
        $this->post(route('proveedor.establecimientos.store'), $p)->assertSessionHasErrors(['descripcion']);

        // Sin dirección -> debe fallar
        $p = $payloadBase; unset($p['direccion']);
        $p['nombre'] = 'Sin Direccion';
        $this->post(route('proveedor.establecimientos.store'), $p)->assertSessionHasErrors(['direccion']);

        // Sin rango_precio -> debe fallar
        $p = $payloadBase; unset($p['rango_precio']);
        $p['nombre'] = 'Sin Rango Precio';
        $this->post(route('proveedor.establecimientos.store'), $p)->assertSessionHasErrors(['rango_precio']);

        // Sin mapa (latitud/longitud) -> debe fallar
        $p = $payloadBase; unset($p['latitud'], $p['longitud']);
        $p['nombre'] = 'Sin Mapa';
        $this->post(route('proveedor.establecimientos.store'), $p)->assertSessionHasErrors(['latitud', 'longitud']);

        // Sin categorías -> debe fallar
        $p = $payloadBase; unset($p['categorias']);
        $p['nombre'] = 'Sin Categorias';
        $this->post(route('proveedor.establecimientos.store'), $p)->assertSessionHasErrors(['categorias']);

        // Sin imagen de portada -> debe fallar
        $p = $payloadBase; unset($p['imagen_portada']);
        $p['nombre'] = 'Sin Portada';
        $this->post(route('proveedor.establecimientos.store'), $p)->assertSessionHasErrors(['imagen_portada']);
    }

    public function test_proveedor_a_recibe_403_sobre_establecimientos_del_proveedor_b(): void
    {
        $estAjeno = Establecimiento::create([
            'destino_id'   => $this->destino->id,
            'proveedor_id' => $this->otroProveedor->id,
            'tipo'         => 'restaurante',
            'nombre'       => 'Picantería Arequipeña Ajena',
            'rango_precio' => 'Medio',
            'estado'       => 'activo',
        ]);

        $this->actingAs($this->proveedor);

        $this->get(route('proveedor.establecimientos.edit', $estAjeno))->assertForbidden();

        $this->patch(route('proveedor.establecimientos.update', $estAjeno), [
            'destino_id'   => $this->destino->id,
            'tipo'         => 'restaurante',
            'nombre'       => 'Hack Restaurant',
            'descripcion'  => 'Descripción válida',
            'direccion'    => 'Calle Falsa 123',
            'rango_precio' => 'Medio',
            'latitud'      => -13.5150,
            'longitud'     => -71.9750,
            'categorias'   => [$this->categoriaCultura->id],
            'estado'       => 'activo',
        ])->assertForbidden();

        $this->patch(route('proveedor.establecimientos.toggle-estado', $estAjeno))->assertForbidden();
        $this->delete(route('proveedor.establecimientos.destroy', $estAjeno))->assertForbidden();
        $this->delete(route('proveedor.establecimientos.imagenes.destroy', [$estAjeno, 999]))->assertForbidden();
    }

    public function test_proveedor_puede_editar_cambiar_estado_y_eliminar_establecimiento_propio(): void
    {
        $est = Establecimiento::create([
            'destino_id'   => $this->destino->id,
            'proveedor_id' => $this->proveedor->id,
            'tipo'         => 'restaurante',
            'nombre'       => 'Chichería Tradicional',
            'rango_precio' => 'Bajo',
            'estado'       => 'activo',
        ]);

        $this->actingAs($this->proveedor);

        // Edit view
        $this->get(route('proveedor.establecimientos.edit', $est))->assertOk();

        // Update
        $this->patch(route('proveedor.establecimientos.update', $est), [
            'destino_id'   => $this->destino->id,
            'tipo'         => 'restaurante',
            'nombre'       => 'Chichería Tradicional Renovada',
            'descripcion'  => 'Descripción renovada',
            'direccion'    => 'Calle Tradición 123',
            'latitud'      => -13.5150,
            'longitud'     => -71.9750,
            'categorias'   => [$this->categoriaCultura->id],
            'rango_precio' => 'Medio',
            'estado'       => 'activo',
        ])->assertSessionHasNoErrors()->assertRedirect(route('proveedor.establecimientos.index'));

        $this->assertSame('Chichería Tradicional Renovada', $est->fresh()->nombre);
        $this->assertSame('Medio', $est->fresh()->rango_precio);

        // Toggle estado
        $this->patch(route('proveedor.establecimientos.toggle-estado', $est))->assertRedirect();
        $this->assertSame('inactivo', $est->fresh()->estado);

        // Soft delete
        $this->delete(route('proveedor.establecimientos.destroy', $est))
            ->assertRedirect(route('proveedor.establecimientos.index'));

        $this->assertSoftDeleted('establecimientos', ['id' => $est->id]);
    }

    public function test_parametro_prueba_no_suplanta_identidad_en_establecimientos(): void
    {
        $estAjeno = Establecimiento::create([
            'destino_id'   => $this->destino->id,
            'proveedor_id' => $this->otroProveedor->id,
            'tipo'         => 'hotel',
            'nombre'       => 'Resort Privado Proveedor B',
            'estado'       => 'activo',
        ]);

        $this->actingAs($this->proveedor);

        $paramTest = '_' . 'proveedor' . '_id' . '_test';

        // Listar intentando suplantar con query param
        $this->get(route('proveedor.establecimientos.index', [$paramTest => $this->otroProveedor->id]))
            ->assertOk()
            ->assertDontSee('Resort Privado Proveedor B');

        // Editar intentando suplantar con query param
        $this->get(route('proveedor.establecimientos.edit', $estAjeno) . '?' . $paramTest . '=' . $this->otroProveedor->id)
            ->assertForbidden();
    }

    public function test_catalogo_establecimientos_es_accesible_publicamente(): void
    {
        $estActivo = Establecimiento::create([
            'destino_id'   => $this->destino->id,
            'proveedor_id' => $this->proveedor->id,
            'tipo'         => 'hotel',
            'nombre'       => 'Hotel Público Activo',
            'estado'       => 'activo',
        ]);

        $estInactivo = Establecimiento::create([
            'destino_id'   => $this->destino->id,
            'proveedor_id' => $this->proveedor->id,
            'tipo'         => 'hotel',
            'nombre'       => 'Hotel Oculto Inactivo',
            'estado'       => 'inactivo',
        ]);

        // Sin autenticación (invitado)
        $this->get(route('catalogo.establecimientos.index'))
            ->assertOk()
            ->assertSee('Hotel Público Activo')
            ->assertDontSee('Hotel Oculto Inactivo');

        $this->get(route('catalogo.establecimientos.show', $estActivo))
            ->assertOk()
            ->assertSee('Hotel Público Activo');

        $this->get(route('catalogo.establecimientos.show', $estInactivo))
            ->assertNotFound();
    }

    public function test_atractivos_rechaza_imagen_portada_mayor_a_2mb(): void
    {
        $this->actingAs($this->operador);

        $payload = [
            'destino_id'     => $this->destino->id,
            'nombre'         => 'Atractivo Imagen Pesada',
            'descripcion'    => 'Descripción de prueba',
            'costo_entrada'  => 10.00,
            'latitud'        => -13.5150,
            'longitud'       => -71.9750,
            'categorias'     => [$this->categoriaCultura->id],
            'estado'         => 'activo',
            'imagen_portada' => UploadedFile::fake()->image('pesada.jpg')->size(2500),
        ];

        $this->post(route('operador.atractivos.store'), $payload)
            ->assertSessionHasErrors([
                'imagen_portada' => 'La imagen de portada no puede superar 2 MB.',
            ]);
    }

    public function test_atractivos_rechaza_imagen_portada_no_imagen(): void
    {
        $this->actingAs($this->operador);

        $payload = [
            'destino_id'     => $this->destino->id,
            'nombre'         => 'Atractivo Archivo PDF',
            'descripcion'    => 'Descripción de prueba',
            'costo_entrada'  => 10.00,
            'latitud'        => -13.5150,
            'longitud'       => -71.9750,
            'categorias'     => [$this->categoriaCultura->id],
            'estado'         => 'activo',
            'imagen_portada' => UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf'),
        ];

        $this->post(route('operador.atractivos.store'), $payload)
            ->assertSessionHasErrors(['imagen_portada']);
    }

    public function test_atractivos_rechaza_galeria_con_mas_de_5_imagenes(): void
    {
        $this->actingAs($this->operador);

        $payload = [
            'destino_id'     => $this->destino->id,
            'nombre'         => 'Atractivo Galeria Excedida',
            'descripcion'    => 'Descripción de prueba',
            'costo_entrada'  => 10.00,
            'latitud'        => -13.5150,
            'longitud'       => -71.9750,
            'categorias'     => [$this->categoriaCultura->id],
            'imagen_portada' => UploadedFile::fake()->image('portada.jpg'),
            'estado'         => 'activo',
            'galeria'        => [
                UploadedFile::fake()->image('1.jpg'),
                UploadedFile::fake()->image('2.jpg'),
                UploadedFile::fake()->image('3.jpg'),
                UploadedFile::fake()->image('4.jpg'),
                UploadedFile::fake()->image('5.jpg'),
                UploadedFile::fake()->image('6.jpg'),
            ],
        ];

        $this->post(route('operador.atractivos.store'), $payload)
            ->assertSessionHasErrors([
                'galeria' => 'Puedes subir máximo 5 imágenes en la galería.',
            ]);
    }

    public function test_establecimientos_rechaza_imagen_portada_mayor_a_2mb(): void
    {
        $this->actingAs($this->proveedor);

        $payload = [
            'destino_id'     => $this->destino->id,
            'tipo'           => 'hotel',
            'nombre'         => 'Hotel Imagen Pesada',
            'descripcion'    => 'Descripción de prueba',
            'direccion'      => 'Calle San Blas 100',
            'rango_precio'   => 'Medio',
            'latitud'        => -13.5150,
            'longitud'       => -71.9750,
            'categorias'     => [$this->categoriaCultura->id],
            'estado'         => 'activo',
            'imagen_portada' => UploadedFile::fake()->image('pesada.jpg')->size(2500),
        ];

        $this->post(route('proveedor.establecimientos.store'), $payload)
            ->assertSessionHasErrors([
                'imagen_portada' => 'La imagen de portada no puede superar 2 MB.',
            ]);
    }

    public function test_establecimientos_rechaza_imagen_portada_no_imagen(): void
    {
        $this->actingAs($this->proveedor);

        $payload = [
            'destino_id'     => $this->destino->id,
            'tipo'           => 'hotel',
            'nombre'         => 'Hotel Archivo PDF',
            'descripcion'    => 'Descripción de prueba',
            'direccion'      => 'Calle San Blas 100',
            'rango_precio'   => 'Medio',
            'latitud'        => -13.5150,
            'longitud'       => -71.9750,
            'categorias'     => [$this->categoriaCultura->id],
            'estado'         => 'activo',
            'imagen_portada' => UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf'),
        ];

        $this->post(route('proveedor.establecimientos.store'), $payload)
            ->assertSessionHasErrors(['imagen_portada']);
    }

    public function test_establecimientos_rechaza_galeria_con_mas_de_5_imagenes(): void
    {
        $this->actingAs($this->proveedor);

        $payload = [
            'destino_id'     => $this->destino->id,
            'tipo'           => 'hotel',
            'nombre'         => 'Hotel Galeria Excedida',
            'descripcion'    => 'Descripción de prueba',
            'direccion'      => 'Calle San Blas 100',
            'rango_precio'   => 'Medio',
            'latitud'        => -13.5150,
            'longitud'       => -71.9750,
            'categorias'     => [$this->categoriaCultura->id],
            'imagen_portada' => UploadedFile::fake()->image('portada.jpg'),
            'estado'         => 'activo',
            'galeria'        => [
                UploadedFile::fake()->image('1.jpg'),
                UploadedFile::fake()->image('2.jpg'),
                UploadedFile::fake()->image('3.jpg'),
                UploadedFile::fake()->image('4.jpg'),
                UploadedFile::fake()->image('5.jpg'),
                UploadedFile::fake()->image('6.jpg'),
            ],
        ];

        $this->post(route('proveedor.establecimientos.store'), $payload)
            ->assertSessionHasErrors([
                'galeria' => 'Puedes subir máximo 5 imágenes en la galería.',
            ]);
    }

    public function test_post_too_large_exception_muestra_vista_413_amigable(): void
    {
        Route::post('/test-post-too-large', function () {
            throw new PostTooLargeException();
        });

        $this->post('/test-post-too-large')
            ->assertStatus(413)
            ->assertSee('Los archivos son demasiado pesados');
    }
}
