<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('rol', 20)->default('turista');
        });

        Schema::table('atractivos', function (Blueprint $table) {
            $table->decimal('costo_entrada', 10, 2)->nullable()->default(null)->change();
            $table->string('origen_id', 100)->nullable()->unique();
            $table->string('fuente_url', 2048)->nullable();
            $table->string('sitio_web_oficial', 2048)->nullable();
            $table->timestamp('fecha_importacion')->nullable();
            $table->timestamp('fecha_verificacion')->nullable();
            $table->string('estado_verificacion', 20)->default('pendiente')->index();
            $table->json('datos_origen')->nullable();
        });

        foreach (['actividades', 'rutas'] as $name) {
            Schema::table($name, function (Blueprint $table) {
                $table->string('imagen_portada', 2048)->nullable()->change();
                $table->string('imagen_fuente_url', 2048)->nullable();
                $table->string('imagen_autor', 150)->nullable();
                $table->string('imagen_licencia', 100)->nullable();
                $table->string('precio_fuente_url', 2048)->nullable();
                $table->timestamp('precio_verificado_en')->nullable();
            });
        }

        Schema::table('actividades', function (Blueprint $table) {
            $table->decimal('precio', 10, 2)->nullable()->default(null)->change();
            $table->integer('duracion_min')->nullable()->default(null)->change();
            $table->integer('cupo_maximo')->nullable()->default(null)->change();
            $table->string('nivel_dificultad')->nullable()->default(null)->change();
            $table->string('tipo_registro', 20)->default('visita');
            $table->timestamp('evento_inicio')->nullable();
            $table->timestamp('evento_fin')->nullable();
            $table->index(['destino_id', 'estado', 'estado_verificacion', 'fecha_verificacion'], 'actividades_catalogo_idx');
        });

        Schema::table('rutas', function (Blueprint $table) {
            $table->decimal('costo_estimado', 10, 2)->nullable()->default(null)->change();
            $table->decimal('duracion_estimada_horas', 4, 1)->nullable()->default(null)->change();
            $table->decimal('distancia_km', 6, 2)->nullable()->default(null)->change();
            $table->string('nivel_dificultad')->nullable()->default(null)->change();
            $table->boolean('es_propuesta')->default(false);
            $table->index(['destino_id', 'estado', 'estado_verificacion', 'fecha_verificacion'], 'rutas_catalogo_idx');
        });

        Schema::table('ruta_puntos', function (Blueprint $table) {
            $table->integer('tiempo_estadia_min')->nullable()->default(null)->change();
            $table->integer('tiempo_traslado_min')->nullable()->default(null)->change();
            $table->decimal('distancia_desde_anterior_km', 6, 2)->nullable()->default(null)->change();
        });

        if (DB::getDriverName() === 'pgsql') {
            foreach (['users', 'password_reset_tokens', 'sessions', 'destinos', 'atractivos', 'actividades', 'rutas', 'ruta_puntos', 'cache', 'cache_locks', 'jobs', 'job_batches', 'failed_jobs'] as $table) {
                DB::statement('ALTER TABLE "'.$table.'" ENABLE ROW LEVEL SECURITY');
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('actividades', fn (Blueprint $table) => $table->dropIndex('actividades_catalogo_idx'));
        Schema::table('rutas', fn (Blueprint $table) => $table->dropIndex('rutas_catalogo_idx'));
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn('rol'));
        Schema::table('atractivos', fn (Blueprint $table) => $table->dropColumn([
            'origen_id', 'fuente_url', 'sitio_web_oficial', 'fecha_importacion',
            'fecha_verificacion', 'estado_verificacion', 'datos_origen',
        ]));
        foreach (['actividades', 'rutas'] as $name) {
            Schema::table($name, fn (Blueprint $table) => $table->dropColumn([
                'imagen_fuente_url', 'imagen_autor', 'imagen_licencia',
                'precio_fuente_url', 'precio_verificado_en',
            ]));
        }
        Schema::table('actividades', fn (Blueprint $table) => $table->dropColumn(['tipo_registro', 'evento_inicio', 'evento_fin']));
        Schema::table('rutas', fn (Blueprint $table) => $table->dropColumn('es_propuesta'));
    }
};
