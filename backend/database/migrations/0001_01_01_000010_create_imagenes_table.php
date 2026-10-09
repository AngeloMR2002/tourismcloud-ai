<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('imagenes')) {
            DB::statement("CREATE TABLE imagenes (
            id BIGSERIAL PRIMARY KEY,
            entidad_tipo VARCHAR(30) NOT NULL,
            entidad_id BIGINT NOT NULL,
            url VARCHAR(500) NOT NULL,
            orden INTEGER NOT NULL DEFAULT 0,
            alt_text VARCHAR(255),
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT chk_imagenes_entidad_tipo CHECK (entidad_tipo IN ('destino','atractivo','establecimiento')),
            CONSTRAINT chk_imagenes_orden CHECK (orden >= 0)
        );");
            DB::statement("CREATE INDEX idx_imagenes_entidad ON imagenes(entidad_tipo,entidad_id);");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imagenes');
    }
};
