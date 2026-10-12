<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('valoraciones', function (Blueprint $table) {
            $table->id();
            
            // Llave foránea estricta a usuarios
            $table->foreignId('usuario_id')
                ->constrained('usuarios')
                ->onDelete('cascade');

            // Relación polimórfica (valorable_type y valorable_id)
            $table->morphs('valorable');

            // Puntuación entre 1 y 5
            $table->unsignedTinyInteger('puntuacion');

            // Comentario opcional de la reseña
            $table->text('comentario')->nullable();

            // Verificación para prevención de Review Bombing (GPS, Itinerario o Reserva)
            $table->boolean('visita_verificada')->default(false);

            $table->timestamps();

            // Índice Único Compuesto Anti-Spam:
            // Un usuario solo puede registrar 1 valoración por lugar/entidad
            $table->unique(
                ['usuario_id', 'valorable_type', 'valorable_id'],
                'uq_valoraciones_usuario_valorable'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('valoraciones');
    }
};
