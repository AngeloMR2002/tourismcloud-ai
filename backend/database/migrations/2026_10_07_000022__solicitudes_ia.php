<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitudes_ia', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('itinerario_id')->nullable();
            $table->bigInteger('turista_id');
            $table->text('prompt_usuario');
            $table->jsonb('restricciones_json');
            $table->jsonb('respuesta_ia_json')->nullable();
            $table->string('modelo_usado', 100)->nullable();
            $table->integer('tiempo_respuesta_ms')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('itinerario_id')
                ->references('id')->on('itinerarios')
                ->nullOnDelete();

            $table->foreign('turista_id')
                ->references('id')->on('usuarios')
                ->cascadeOnDelete();
        });

        DB::statement("CREATE INDEX idx_solicitudes_ia_turista ON solicitudes_ia(turista_id)");
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes_ia');
    }
};
