<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('destinos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('organizacion_id');
            $table->bigInteger('operador_id')->nullable();
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->string('pais', 100);
            $table->string('region', 100)->nullable();
            $table->string('ciudad', 100)->nullable();
            $table->decimal('latitud', 10, 7)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();
            $table->string('estado', 20)->default('activo');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->foreign('organizacion_id')
                ->references('id')->on('organizaciones')
                ->cascadeOnDelete();

            $table->foreign('operador_id')
                ->references('id')->on('usuarios')
                ->nullOnDelete();
        });

        DB::statement("ALTER TABLE destinos ADD CONSTRAINT destinos_estado_check CHECK (estado IN ('activo','inactivo'))");
        DB::statement("CREATE INDEX idx_destinos_organizacion ON destinos(organizacion_id)");
    }

    public function down(): void
    {
        Schema::dropIfExists('destinos');
    }
};
