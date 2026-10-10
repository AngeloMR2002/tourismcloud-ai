<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('imagenes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('entidad_tipo', 20);
            $table->bigInteger('entidad_id');
            $table->string('url', 255);
            $table->smallInteger('orden')->default(0);
            $table->string('alt_text', 200)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        DB::statement("ALTER TABLE imagenes ADD CONSTRAINT imagenes_entidad_tipo_check CHECK (entidad_tipo IN ('destino','atractivo','establecimiento'))");
        DB::statement("CREATE INDEX idx_imagenes_entidad ON imagenes(entidad_tipo, entidad_id)");
    }

    public function down(): void
    {
        Schema::dropIfExists('imagenes');
    }
};
