<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resenas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('organizacion_id');
            $table->bigInteger('turista_id');
            $table->bigInteger('atractivo_id')->nullable();
            $table->bigInteger('establecimiento_id')->nullable();
            $table->smallInteger('calificacion');
            $table->text('comentario')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->foreign('organizacion_id')
                ->references('id')->on('organizaciones')
                ->cascadeOnDelete();

            $table->foreign('turista_id')
                ->references('id')->on('usuarios')
                ->cascadeOnDelete();

            $table->foreign('atractivo_id')
                ->references('id')->on('atractivos')
                ->cascadeOnDelete();

            $table->foreign('establecimiento_id')
                ->references('id')->on('establecimientos')
                ->cascadeOnDelete();
        });

        DB::statement("ALTER TABLE resenas ADD CONSTRAINT resenas_calificacion_check CHECK (calificacion BETWEEN 1 AND 5)");
        DB::statement("ALTER TABLE resenas ADD CONSTRAINT resenas_objetivo_check CHECK (atractivo_id IS NOT NULL OR establecimiento_id IS NOT NULL)");
        DB::statement("CREATE INDEX idx_resenas_atractivo ON resenas(atractivo_id)");
        DB::statement("CREATE INDEX idx_resenas_establecimiento ON resenas(establecimiento_id)");
    }

    public function down(): void
    {
        Schema::dropIfExists('resenas');
    }
};
