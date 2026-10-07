<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabla: actividades
     * Módulo: P3 - Actividades Turísticas y Tours.
     * Soporta restricciones de IA (duración, precio, horarios, dificultad, aforo).
     */
    public function up(): void
    {
        Schema::create('actividades', function (Blueprint $table) {
            $table->id();

            // Relaciones
            $table->unsignedBigInteger('destino_id');
            $table->unsignedBigInteger('atractivo_id')->nullable();
            $table->unsignedBigInteger('operador_id')->nullable();

            // Información descriptiva
            $table->string('nombre', 150);
            $table->text('descripcion');
            $table->string('categoria', 50)->default('cultural'); // aventura, cultural, gastronomica, ecoturismo, relax, deportiva

            // Restricciones operativas y de IA
            $table->integer('duracion_min')->default(60); // Duración estimada en minutos
            $table->decimal('precio', 10, 2)->default(0.00); // Costo por persona
            $table->enum('nivel_dificultad', ['baja', 'media', 'alta'])->default('baja');
            $table->integer('cupo_maximo')->default(15);
            $table->time('horario_inicio')->nullable();
            $table->time('horario_fin')->nullable();
            $table->json('dias_operacion')->nullable(); // ["lunes", "miercoles", "sabado", ...]

            // Detalles logísticos
            $table->string('punto_encuentro', 255)->nullable();
            $table->text('incluye')->nullable();
            $table->text('no_incluye')->nullable();
            $table->text('requisitos')->nullable();
            $table->string('imagen_portada', 255)->nullable();

            // Ubicación geográfica de referencia
            $table->decimal('latitud', 10, 7)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();

            // Estado y control
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->timestamps();
            $table->softDeletes();

            // Índices de búsqueda
            $table->index('destino_id');
            $table->index('atractivo_id');
            $table->index('operador_id');
            $table->index('categoria');
            $table->index('precio');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actividades');
    }
};
