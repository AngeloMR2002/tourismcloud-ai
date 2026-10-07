<?php

// ⚠️ STUB DEFENSIVO TEMPORAL
// Permite que las migraciones de feature/actividades funcionen de forma 100%
// autónoma en local sin depender de que feature/destinos o feature/atractivos
// hayan sido mergeadas previamente.
// Si las tablas ya existen, esta migración no hace nada.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('destinos')) {
            Schema::create('destinos', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('organizacion_id')->nullable();
                $table->unsignedBigInteger('operador_id')->nullable();
                $table->string('nombre', 150);
                $table->text('descripcion')->nullable();
                $table->string('pais', 100)->default('Perú');
                $table->string('region', 100)->nullable();
                $table->string('ciudad', 100)->nullable();
                $table->decimal('latitud', 10, 7)->nullable();
                $table->decimal('longitud', 10, 7)->nullable();
                $table->enum('estado', ['activo', 'inactivo'])->default('activo');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('atractivos')) {
            Schema::create('atractivos', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('destino_id')->nullable();
                $table->string('nombre', 150);
                $table->text('descripcion')->nullable();
                $table->json('horarios')->nullable();
                $table->decimal('costo_entrada', 10, 2)->default(0);
                $table->integer('duracion_estimada_min')->nullable();
                $table->decimal('latitud', 10, 7)->nullable();
                $table->decimal('longitud', 10, 7)->nullable();
                $table->string('imagen_portada', 255)->nullable();
                $table->enum('estado', ['activo', 'inactivo'])->default('activo');
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        // En rollback no eliminamos agresivamente para preservar datos de otras ramas si existen
    }
};
