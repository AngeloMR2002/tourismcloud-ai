<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ruta_paradas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('ruta_id');
            $table->bigInteger('atractivo_id');
            $table->integer('orden');
            $table->integer('tiempo_estimado_llegada_min')->nullable();

            $table->unique(['ruta_id', 'orden']);

            $table->foreign('ruta_id')
                ->references('id')->on('rutas')
                ->cascadeOnDelete();

            $table->foreign('atractivo_id')
                ->references('id')->on('atractivos')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ruta_paradas');
    }
};
