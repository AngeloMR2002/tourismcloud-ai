<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('itinerarios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('turista_id');
            $table->bigInteger('preferencia_id')->nullable();
            $table->string('nombre', 150);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->decimal('presupuesto_total', 10, 2)->nullable();
            $table->integer('num_dias');
            $table->string('estado', 20)->default('borrador');
            $table->boolean('generado_por_ia')->default(false);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->foreign('turista_id')
                ->references('id')->on('usuarios')
                ->cascadeOnDelete();

            $table->foreign('preferencia_id')
                ->references('id')->on('preferencias_turista')
                ->nullOnDelete();
        });

        DB::statement("ALTER TABLE itinerarios ADD CONSTRAINT itinerarios_estado_check CHECK (estado IN ('borrador','confirmado','en_curso','completado','cancelado'))");
        DB::statement("ALTER TABLE itinerarios ADD CONSTRAINT itinerarios_fechas_check CHECK (fecha_fin >= fecha_inicio)");
        DB::statement("CREATE INDEX idx_itinerarios_turista ON itinerarios(turista_id)");
    }

    public function down(): void
    {
        Schema::dropIfExists('itinerarios');
    }
};
