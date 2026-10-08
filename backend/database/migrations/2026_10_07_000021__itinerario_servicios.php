<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('itinerario_servicios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('itinerario_id');
            $table->bigInteger('servicio_id');
            $table->integer('cantidad')->default(1);
            $table->decimal('costo_aplicado', 10, 2);

            $table->foreign('itinerario_id')
                ->references('id')->on('itinerarios')
                ->cascadeOnDelete();

            $table->foreign('servicio_id')
                ->references('id')->on('servicios_turisticos')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('itinerario_servicios');
    }
};
