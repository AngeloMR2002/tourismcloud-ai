<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rutas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('destino_id');
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->decimal('distancia_km', 6, 2)->nullable();
            $table->integer('duracion_estimada_min')->nullable();
            $table->string('tipo_transporte', 20)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->foreign('destino_id')
                ->references('id')->on('destinos')
                ->cascadeOnDelete();
        });

        DB::statement("ALTER TABLE rutas ADD CONSTRAINT rutas_tipo_transporte_check CHECK (tipo_transporte IS NULL OR tipo_transporte IN ('caminata','vehiculo','bus','otro'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('rutas');
    }
};
