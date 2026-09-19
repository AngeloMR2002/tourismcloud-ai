<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabla: establecimientos
     * Módulo: Catálogo Turístico — gestionada por rol proveedor.
     * El campo proveedor_id es NOT NULL (dueño explícito del registro).
     */
    public function up(): void
    {
        Schema::create('establecimientos', function (Blueprint $table) {
            $table->id();

            // FK pendiente: se activará cuando feature/destinos llegue a develop.
            // FK real: $table->foreignId('destino_id')->constrained('destinos')->cascadeOnDelete();
            $table->unsignedBigInteger('destino_id');

            // FK pendiente: se activará cuando feature/auth defina la tabla usuarios/users.
            // FK real: $table->foreignId('proveedor_id')->constrained('usuarios')->restrictOnDelete();
            // NOTA: ON DELETE RESTRICT (no se puede borrar el usuario si tiene establecimientos).
            $table->unsignedBigInteger('proveedor_id');

            // enum() genera VARCHAR + CHECK constraint en PostgreSQL automáticamente.
            $table->enum('tipo', ['hotel', 'restaurante', 'transporte', 'agencia', 'otro']);

            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->string('direccion', 255)->nullable();

            // JSONB en PostgreSQL (json en SQLite/MySQL).
            $table->json('horarios')->nullable();

            $table->decimal('latitud', 10, 7)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();

            // rango_precio: 4 niveles según el enunciado (ampliado de 3 en el schema original).
            $table->enum('rango_precio', ['bajo', 'medio', 'alto', 'lujo'])->nullable();

            $table->string('imagen_portada', 255)->nullable();

            // enum() genera VARCHAR + CHECK constraint en PostgreSQL automáticamente.
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');

            $table->timestamps();
            $table->softDeletes();

            // Índices (equivalen a los del schema SQL).
            $table->index('destino_id');
            $table->index('proveedor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('establecimientos');
    }
};
