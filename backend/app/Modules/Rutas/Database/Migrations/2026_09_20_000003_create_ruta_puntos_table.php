<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabla: ruta_puntos
     * Módulo: P3 - Paradas e Hitos Secuenciales de una Ruta Turística.
     * Permite modelar el itinerario paso a paso de la ruta con tiempos y distancias.
     */
    public function up(): void
    {
        Schema::create('ruta_puntos', function (Blueprint $table) {
            $table->id();

            // Clave foránea hacia la ruta principal
            $table->foreignId('ruta_id')->constrained('rutas')->cascadeOnDelete();

            // Atractivo opcional vinculado (puede ser null si es un hito/mirador libre)
            $table->unsignedBigInteger('atractivo_id')->nullable();

            // Datos de la parada
            $table->string('nombre_parada', 150);
            $table->text('descripcion_parada')->nullable();
            $table->unsignedInteger('orden')->default(1); // 1, 2, 3...

            // Tiempos y distancias (esencial para el optimizador de itinerarios)
            $table->integer('tiempo_estadia_min')->default(30); // Tiempo sugerido en la parada
            $table->decimal('distancia_desde_anterior_km', 6, 2)->default(0.00); // Distancia desde la parada previa
            $table->integer('tiempo_traslado_min')->default(0); // Tiempo de viaje desde la parada previa
            $table->string('tipo_transporte_tramo', 50)->default('a_pie'); // a_pie, vehiculo, bicicleta

            // Coordenadas geográficas
            $table->decimal('latitud', 10, 7)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();

            $table->timestamps();

            // Índices para ordenamiento y consultas
            $table->index(['ruta_id', 'orden']);
            $table->index('atractivo_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ruta_puntos');
    }
};
