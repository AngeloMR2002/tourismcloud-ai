<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('servicios_turisticos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('organizacion_id');
            $table->bigInteger('proveedor_id');
            $table->bigInteger('destino_id')->nullable();
            $table->string('tipo', 20);
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->string('modalidad_tarifa', 20);
            $table->decimal('precio', 10, 2);
            $table->string('estado', 20)->default('activo');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->foreign('organizacion_id')
                ->references('id')->on('organizaciones')
                ->cascadeOnDelete();

            $table->foreign('proveedor_id')
                ->references('id')->on('usuarios')
                ->restrictOnDelete();

            $table->foreign('destino_id')
                ->references('id')->on('destinos')
                ->nullOnDelete();
        });

        DB::statement("ALTER TABLE servicios_turisticos ADD CONSTRAINT servicios_turisticos_tipo_check CHECK (tipo IN ('transporte','guia','alquiler_equipo','seguro','asistencia','otro'))");
        DB::statement("ALTER TABLE servicios_turisticos ADD CONSTRAINT servicios_turisticos_modalidad_tarifa_check CHECK (modalidad_tarifa IN ('por_persona','por_grupo','por_dia','tarifa_unica'))");
        DB::statement("ALTER TABLE servicios_turisticos ADD CONSTRAINT servicios_turisticos_estado_check CHECK (estado IN ('activo','inactivo'))");
        DB::statement("CREATE INDEX idx_servicios_destino ON servicios_turisticos(destino_id)");
    }

    public function down(): void
    {
        Schema::dropIfExists('servicios_turisticos');
    }
};
