<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('atractivos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('destino_id');
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->decimal('costo_entrada', 10, 2)->default(0);
            $table->integer('duracion_estimada_min')->nullable();
            $table->decimal('latitud', 10, 7)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();
            $table->string('imagen_portada', 255)->nullable();
            $table->string('estado', 20)->default('activo');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('destino_id')
                ->references('id')->on('destinos')
                ->cascadeOnDelete();
        });

        DB::statement("ALTER TABLE atractivos ADD CONSTRAINT atractivos_estado_check CHECK (estado IN ('activo','inactivo'))");
        DB::statement("CREATE INDEX idx_atractivos_destino ON atractivos(destino_id)");
    }

    public function down(): void
    {
        Schema::dropIfExists('atractivos');
    }
};
