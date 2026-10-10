<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('itinerario_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('itinerario_dia_id');
            $table->bigInteger('atractivo_id')->nullable();
            $table->bigInteger('actividad_id')->nullable();
            $table->bigInteger('establecimiento_id')->nullable();
            $table->bigInteger('servicio_id')->nullable();
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fin')->nullable();
            $table->integer('orden');
            $table->decimal('costo_estimado', 10, 2)->default(0);
            $table->string('estado_item', 20)->default('sugerido');

            $table->foreign('itinerario_dia_id')
                ->references('id')->on('itinerario_dias')
                ->cascadeOnDelete();

            $table->foreign('atractivo_id')
                ->references('id')->on('atractivos')
                ->nullOnDelete();

            $table->foreign('actividad_id')
                ->references('id')->on('actividades')
                ->nullOnDelete();

            $table->foreign('establecimiento_id')
                ->references('id')->on('establecimientos')
                ->nullOnDelete();

            $table->foreign('servicio_id')
                ->references('id')->on('servicios_turisticos')
                ->nullOnDelete();
        });

        DB::statement("ALTER TABLE itinerario_items ADD CONSTRAINT itinerario_items_estado_check CHECK (estado_item IN ('sugerido','confirmado','descartado'))");
        DB::statement("ALTER TABLE itinerario_items ADD CONSTRAINT itinerario_items_recurso_check CHECK (atractivo_id IS NOT NULL OR actividad_id IS NOT NULL OR establecimiento_id IS NOT NULL OR servicio_id IS NOT NULL)");
        DB::statement("CREATE INDEX idx_itinerario_items_dia ON itinerario_items(itinerario_dia_id)");
    }

    public function down(): void
    {
        Schema::dropIfExists('itinerario_items');
    }
};
