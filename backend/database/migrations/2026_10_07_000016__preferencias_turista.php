<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('preferencias_turista', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('turista_id');
            $table->decimal('presupuesto_min', 10, 2)->nullable();
            $table->decimal('presupuesto_max', 10, 2);
            $table->integer('dias_disponibles');
            $table->string('ritmo', 20)->default('moderado');
            $table->time('hora_inicio_dia')->nullable();
            $table->time('hora_fin_dia')->nullable();
            $table->boolean('vigente')->default(true);
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('turista_id')
                ->references('id')->on('usuarios')
                ->cascadeOnDelete();
        });

        DB::statement("ALTER TABLE preferencias_turista ADD CONSTRAINT preferencias_turista_ritmo_check CHECK (ritmo IN ('relajado','moderado','intenso'))");
        DB::statement("CREATE INDEX idx_preferencias_turista ON preferencias_turista(turista_id, vigente)");
    }

    public function down(): void
    {
        Schema::dropIfExists('preferencias_turista');
    }
};
