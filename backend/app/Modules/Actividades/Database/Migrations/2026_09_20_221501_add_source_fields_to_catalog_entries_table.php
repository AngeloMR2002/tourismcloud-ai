<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('actividades', function (Blueprint $table) {
            $table->decimal('precio_min', 10, 2)->nullable()->after('precio');
            $table->decimal('precio_max', 10, 2)->nullable()->after('precio_min');
            $table->enum('tipo_precio', ['fijo', 'estimado', 'variable'])->default('estimado')->after('precio_max');
            $table->string('sitio_web_oficial', 2048)->nullable()->after('imagen_portada');
            $table->string('url_reserva_oficial', 2048)->nullable()->after('sitio_web_oficial');
            $table->string('fuente_url', 2048)->nullable()->after('url_reserva_oficial');
            $table->timestamp('fecha_verificacion')->nullable()->after('fuente_url');
            $table->enum('estado_verificacion', ['pendiente', 'aprobado', 'desactualizado'])->default('pendiente')->after('fecha_verificacion');

            $table->index(['estado', 'estado_verificacion']);
            $table->index('tipo_precio');
        });

        Schema::table('rutas', function (Blueprint $table) {
            $table->decimal('costo_min', 10, 2)->nullable()->after('costo_estimado');
            $table->decimal('costo_max', 10, 2)->nullable()->after('costo_min');
            $table->enum('tipo_precio', ['fijo', 'estimado', 'variable'])->default('estimado')->after('costo_max');
            $table->string('sitio_web_oficial', 2048)->nullable()->after('imagen_portada');
            $table->string('url_reserva_oficial', 2048)->nullable()->after('sitio_web_oficial');
            $table->string('fuente_url', 2048)->nullable()->after('url_reserva_oficial');
            $table->timestamp('fecha_verificacion')->nullable()->after('fuente_url');
            $table->enum('estado_verificacion', ['pendiente', 'aprobado', 'desactualizado'])->default('pendiente')->after('fecha_verificacion');

            $table->index(['estado', 'estado_verificacion']);
            $table->index('tipo_precio');
        });
    }

    public function down(): void
    {
        Schema::table('actividades', function (Blueprint $table) {
            $table->dropIndex(['estado', 'estado_verificacion']);
            $table->dropIndex(['tipo_precio']);
            $table->dropColumn([
                'precio_min',
                'precio_max',
                'tipo_precio',
                'sitio_web_oficial',
                'url_reserva_oficial',
                'fuente_url',
                'fecha_verificacion',
                'estado_verificacion',
            ]);
        });

        Schema::table('rutas', function (Blueprint $table) {
            $table->dropIndex(['estado', 'estado_verificacion']);
            $table->dropIndex(['tipo_precio']);
            $table->dropColumn([
                'costo_min',
                'costo_max',
                'tipo_precio',
                'sitio_web_oficial',
                'url_reserva_oficial',
                'fuente_url',
                'fecha_verificacion',
                'estado_verificacion',
            ]);
        });
    }
};
