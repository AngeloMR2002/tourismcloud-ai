<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('establecimientos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('destino_id');
            $table->bigInteger('proveedor_id');
            $table->string('tipo', 30);
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->string('direccion', 255)->nullable();
            $table->decimal('latitud', 10, 7)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();
            $table->string('rango_precio', 10)->nullable();
            $table->string('imagen_portada', 255)->nullable();
            $table->string('estado', 20)->default('activo');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('destino_id')
                ->references('id')->on('destinos')
                ->cascadeOnDelete();

            $table->foreign('proveedor_id')
                ->references('id')->on('usuarios')
                ->restrictOnDelete();
        });

        DB::statement("ALTER TABLE establecimientos ADD CONSTRAINT establecimientos_tipo_check CHECK (tipo IN ('hotel','restaurante','transporte','agencia','otro'))");
        DB::statement("ALTER TABLE establecimientos ADD CONSTRAINT establecimientos_rango_precio_check CHECK (rango_precio IS NULL OR rango_precio IN ('bajo','medio','alto','lujo'))");
        DB::statement("ALTER TABLE establecimientos ADD CONSTRAINT establecimientos_estado_check CHECK (estado IN ('activo','inactivo'))");
        DB::statement("CREATE INDEX idx_establecimientos_destino ON establecimientos(destino_id)");
    }

    public function down(): void
    {
        Schema::dropIfExists('establecimientos');
    }
};
