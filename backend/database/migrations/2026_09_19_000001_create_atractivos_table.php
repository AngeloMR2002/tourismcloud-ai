<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabla: atractivos
     * Módulo: Catálogo Turístico — gestionada por rol operador_turistico.
     */
    public function up(): void
    {
        Schema::create('atractivos', function (Blueprint $table) {
            $table->id();

            // FK pendiente: se activará cuando feature/destinos llegue a develop.
            // FK real: $table->foreignId('destino_id')->constrained('destinos')->cascadeOnDelete();
            $table->unsignedBigInteger('destino_id');

            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();

            // JSONB en PostgreSQL (json en SQLite/MySQL).
            // Estructura esperada: {"lunes": {"abre":"09:00","cierra":"18:00"}, "martes": null, ...}
            $table->json('horarios')->nullable();

            $table->decimal('costo_entrada', 10, 2)->default(0);
            $table->integer('duracion_estimada_min')->nullable();
            $table->decimal('latitud', 10, 7)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();
            $table->string('imagen_portada', 255)->nullable();

            // enum() genera VARCHAR + CHECK constraint en PostgreSQL automáticamente.
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');

            $table->timestamps();
            $table->softDeletes();

            // Índice para filtrar por destino (equivale a idx_atractivos_destino en el schema SQL).
            $table->index('destino_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atractivos');
    }
};
