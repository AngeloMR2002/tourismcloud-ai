<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizaciones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 150);
            $table->string('tipo', 30);
            $table->string('estado', 20)->default('activo');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });

        DB::statement("ALTER TABLE organizaciones ADD CONSTRAINT organizaciones_tipo_check CHECK (tipo IN ('agencia_turistica','operador_independiente'))");
        DB::statement("ALTER TABLE organizaciones ADD CONSTRAINT organizaciones_estado_check CHECK (estado IN ('activo','inactivo'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('organizaciones');
    }
};
