<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('establecimiento_categoria', function (Blueprint $table) {
            $table->bigInteger('establecimiento_id');
            $table->bigInteger('categoria_id');

            $table->primary(['establecimiento_id', 'categoria_id']);

            $table->foreign('establecimiento_id')
                ->references('id')->on('establecimientos')
                ->cascadeOnDelete();

            $table->foreign('categoria_id')
                ->references('id')->on('categorias_interes')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('establecimiento_categoria');
    }
};
