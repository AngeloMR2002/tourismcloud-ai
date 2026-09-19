<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabla: imagenes
     * Galería polimórfica para destinos, atractivos y establecimientos.
     * No usa morphs() de Eloquent para respetar el esquema SQL original
     * con columna entidad_tipo (enum) + entidad_id.
     */
    public function up(): void
    {
        Schema::create('imagenes', function (Blueprint $table) {
            $table->id();

            // enum() genera VARCHAR + CHECK constraint en PostgreSQL automáticamente.
            $table->enum('entidad_tipo', ['destino', 'atractivo', 'establecimiento']);

            // ID de la entidad relacionada (sin FK polimórfica declarada a nivel BD).
            $table->unsignedBigInteger('entidad_id');

            $table->string('url', 255);
            $table->smallInteger('orden')->default(0);
            $table->string('alt_text', 200)->nullable();

            // Solo created_at, sin updated_at (las imágenes no se editan, se reemplazan).
            $table->timestamp('created_at')->useCurrent();

            // Índice compuesto para búsquedas polimórficas eficientes.
            $table->index(['entidad_tipo', 'entidad_id'], 'idx_imagenes_entidad');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imagenes');
    }
};
