<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('atractivo_categoria', function (Blueprint $table) {
            $table->bigInteger('atractivo_id');
            $table->bigInteger('categoria_id');

            $table->primary(['atractivo_id', 'categoria_id']);

            $table->foreign('atractivo_id')
                ->references('id')->on('atractivos')
                ->cascadeOnDelete();

            $table->foreign('categoria_id')
                ->references('id')->on('categorias_interes')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('atractivo_categoria');
    }
};
