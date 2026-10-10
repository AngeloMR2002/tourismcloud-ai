<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('preferencia_categoria', function (Blueprint $table) {
            $table->bigInteger('preferencia_id');
            $table->bigInteger('categoria_id');

            $table->primary(['preferencia_id', 'categoria_id']);

            $table->foreign('preferencia_id')
                ->references('id')->on('preferencias_turista')
                ->cascadeOnDelete();

            $table->foreign('categoria_id')
                ->references('id')->on('categorias_interes')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('preferencia_categoria');
    }
};
