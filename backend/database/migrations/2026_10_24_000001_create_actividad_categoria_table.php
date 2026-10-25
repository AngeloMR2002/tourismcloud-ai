<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('actividad_categoria', function (Blueprint $table): void {
            $table->foreignId('actividad_id')->constrained('actividades')->cascadeOnDelete();
            $table->foreignId('categoria_id')->constrained('categorias_interes')->cascadeOnDelete();
            $table->primary(['actividad_id', 'categoria_id']);
            $table->index('categoria_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actividad_categoria');
    }
};
