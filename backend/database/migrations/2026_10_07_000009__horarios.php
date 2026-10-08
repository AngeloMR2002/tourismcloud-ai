<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('horarios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('establecimiento_id')->nullable();
            $table->bigInteger('atractivo_id')->nullable();
            $table->string('dia', 10);
            $table->time('hora_inicio');
            $table->time('hora_fin');

            $table->foreign('establecimiento_id')
                ->references('id')->on('establecimientos')
                ->cascadeOnDelete();

            $table->foreign('atractivo_id')
                ->references('id')->on('atractivos')
                ->cascadeOnDelete();
        });

        DB::statement("ALTER TABLE horarios ADD CONSTRAINT horarios_dia_check CHECK (dia IN ('lunes','martes','miercoles','jueves','viernes','sabado','domingo'))");
        DB::statement("ALTER TABLE horarios ADD CONSTRAINT horarios_un_solo_dueno CHECK (num_nonnulls(establecimiento_id, atractivo_id) = 1)");
        DB::statement("ALTER TABLE horarios ADD CONSTRAINT horarios_fin_mayor_inicio CHECK (hora_fin > hora_inicio)");
        DB::statement("CREATE INDEX idx_horarios_establecimiento ON horarios(establecimiento_id, dia)");
        DB::statement("CREATE INDEX idx_horarios_atractivo ON horarios(atractivo_id, dia)");
    }

    public function down(): void
    {
        Schema::dropIfExists('horarios');
    }
};
