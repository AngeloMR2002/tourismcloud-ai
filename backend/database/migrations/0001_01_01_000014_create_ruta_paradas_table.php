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
        if (!Schema::hasTable('ruta_paradas')) {
            DB::statement("CREATE TABLE ruta_paradas (
            id BIGSERIAL PRIMARY KEY,
            ruta_id BIGINT NOT NULL,
            atractivo_id BIGINT NOT NULL,
            orden INTEGER NOT NULL,
            CONSTRAINT fk_ruta_paradas_ruta FOREIGN KEY (ruta_id) REFERENCES rutas(id) ON DELETE CASCADE,
            CONSTRAINT fk_ruta_paradas_atractivo FOREIGN KEY (atractivo_id) REFERENCES atractivos(id) ON DELETE CASCADE,
            CONSTRAINT chk_ruta_paradas_orden CHECK (orden > 0),
            CONSTRAINT uq_ruta_paradas_orden UNIQUE (ruta_id,orden)
        );");
            DB::statement("CREATE INDEX idx_ruta_paradas_atractivo ON ruta_paradas(atractivo_id);");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ruta_paradas');
    }
};
