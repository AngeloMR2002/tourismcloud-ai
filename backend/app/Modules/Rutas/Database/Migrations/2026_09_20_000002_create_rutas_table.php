<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabla: rutas
     * Módulo: P3 - Rutas y Circuitos Turísticos.
     * Almacena circuitos temáticos y conexiones secuenciales entre atractivos.
     */
    public function up(): void
    {
        Schema::create('rutas', function (Blueprint $table) {
            $table->id();

            // Relaciones
            $table->unsignedBigInteger('destino_id');
            $table->unsignedBigInteger('operador_id')->nullable();

            // Información de la ruta
            $table->string('nombre', 150);
            $table->text('descripcion');
            $table->enum('tipo_ruta', ['circuito', 'lineal', 'tematica', 'senderismo'])->default('circuito');

            // Métricas y optimización para IA
            $table->decimal('duracion_estimada_horas', 4, 1)->default(2.0); // Horas estimadas
            $table->decimal('distancia_km', 6, 2)->default(0.00); // Kilómetros totales
            $table->enum('nivel_dificultad', ['facil', 'moderada', 'dificil', 'experto'])->default('facil');
            $table->enum('transporte_recomendado', ['a_pie', 'bicicleta', 'automovil', 'autobus', 'mixto'])->default('a_pie');
            $table->decimal('costo_estimado', 10, 2)->default(0.00);

            // Información complementaria
            $table->string('temporada_recomendada', 100)->nullable(); // Ej: "Mayo a Octubre"
            $table->text('recomendaciones')->nullable();
            $table->string('imagen_portada', 255)->nullable();

            // Estado y control
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('destino_id');
            $table->index('operador_id');
            $table->index('nivel_dificultad');
            $table->index('transporte_recomendado');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rutas');
    }
};
