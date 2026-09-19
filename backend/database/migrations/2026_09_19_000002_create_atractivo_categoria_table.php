<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabla pivot: atractivo_categoria
     * Relaciona atractivos con categorías de interés (muchos a muchos).
     */
    public function up(): void
    {
        Schema::create('atractivo_categoria', function (Blueprint $table) {
            // FK pendiente hacia atractivos (misma rama, existe).
            $table->foreignId('atractivo_id')
                  ->constrained('atractivos')
                  ->cascadeOnDelete();

            // FK pendiente: se activará cuando feature/destinos (o el módulo de categorías)
            // defina la tabla categorias_interes en develop.
            // FK real: ->constrained('categorias_interes')->cascadeOnDelete()
            $table->unsignedBigInteger('categoria_id');

            $table->primary(['atractivo_id', 'categoria_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atractivo_categoria');
    }
};
