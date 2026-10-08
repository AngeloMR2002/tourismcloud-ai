<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('actividades', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('destino_id');
            $table->bigInteger('atractivo_id')->nullable();
            $table->bigInteger('establecimiento_id')->nullable();
            $table->bigInteger('categoria_id')->nullable();
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->integer('duracion_min')->nullable();
            $table->decimal('costo', 10, 2)->default(0);
            $table->integer('cupo_maximo')->nullable();
            $table->string('estado', 20)->default('activo');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->foreign('destino_id')
                ->references('id')->on('destinos')
                ->cascadeOnDelete();

            $table->foreign('atractivo_id')
                ->references('id')->on('atractivos')
                ->nullOnDelete();

            $table->foreign('establecimiento_id')
                ->references('id')->on('establecimientos')
                ->nullOnDelete();

            $table->foreign('categoria_id')
                ->references('id')->on('categorias_interes')
                ->nullOnDelete();
        });

        DB::statement("ALTER TABLE actividades ADD CONSTRAINT actividades_estado_check CHECK (estado IN ('activo','inactivo'))");
        DB::statement("CREATE INDEX idx_actividades_destino ON actividades(destino_id)");
    }

    public function down(): void
    {
        Schema::dropIfExists('actividades');
    }
};
