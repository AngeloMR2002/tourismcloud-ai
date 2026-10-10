<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('organizacion_id')->nullable();
            $table->string('nombre', 150);
            $table->string('email', 150)->unique();
            $table->string('password', 255);
            $table->string('rol', 30);
            $table->string('telefono', 20)->nullable();
            $table->string('estado', 20)->default('activo');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->foreign('organizacion_id')
                ->references('id')->on('organizaciones')
                ->nullOnDelete();
        });

        DB::statement("ALTER TABLE usuarios ADD CONSTRAINT usuarios_rol_check CHECK (rol IN ('administrador','operador_turistico','proveedor','turista'))");
        DB::statement("ALTER TABLE usuarios ADD CONSTRAINT usuarios_estado_check CHECK (estado IN ('activo','inactivo'))");
        DB::statement("CREATE INDEX idx_usuarios_organizacion ON usuarios(organizacion_id)");
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
