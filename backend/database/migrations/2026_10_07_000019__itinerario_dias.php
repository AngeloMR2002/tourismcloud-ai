<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('itinerario_dias', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('itinerario_id');
            $table->integer('numero_dia');
            $table->date('fecha');

            $table->unique(['itinerario_id', 'numero_dia']);

            $table->foreign('itinerario_id')
                ->references('id')->on('itinerarios')
                ->cascadeOnDelete();
        });

        DB::statement("CREATE INDEX idx_itinerario_dias_itinerario ON itinerario_dias(itinerario_id)");
    }

    public function down(): void
    {
        Schema::dropIfExists('itinerario_dias');
    }
};
