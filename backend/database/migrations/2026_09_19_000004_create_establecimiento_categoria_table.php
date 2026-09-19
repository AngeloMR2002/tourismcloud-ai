<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabla pivot: establecimiento_categoria
     * Relaciona establecimientos con categorías de interés (muchos a muchos).
     */
    public function up(): void
    {
        Schema::create('establecimiento_categoria', function (Blueprint $table) {
            // FK hacia establecimientos (misma rama, existe).
            $table->foreignId('establecimiento_id')
                  ->constrained('establecimientos')
                  ->cascadeOnDelete();

            // FK pendiente: se activará cuando feature/destinos (o el módulo de categorías)
            // defina la tabla categorias_interes en develop.
            // FK real: ->constrained('categorias_interes')->cascadeOnDelete()
            $table->unsignedBigInteger('categoria_id');

            $table->primary(['establecimiento_id', 'categoria_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('establecimiento_categoria');
    }
};
