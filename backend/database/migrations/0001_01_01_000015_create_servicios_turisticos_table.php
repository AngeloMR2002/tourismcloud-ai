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
        if (!Schema::hasTable('servicios_turisticos')) {
            DB::statement("CREATE TABLE servicios_turisticos (
            id BIGSERIAL PRIMARY KEY,
            destino_id BIGINT NOT NULL,
            nombre VARCHAR(150) NOT NULL,
            tipo VARCHAR(50) NOT NULL,
            descripcion TEXT,
            costo DECIMAL(10,2) NOT NULL DEFAULT 0,
            estado VARCHAR(20) NOT NULL DEFAULT 'activo',
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT fk_servicios_turisticos_destino FOREIGN KEY (destino_id) REFERENCES destinos(id) ON DELETE CASCADE,
            CONSTRAINT chk_servicios_turisticos_costo CHECK (costo >= 0),
            CONSTRAINT chk_servicios_turisticos_estado CHECK (estado IN ('activo','inactivo'))
        );");
            DB::statement("CREATE INDEX idx_servicios_turisticos_destino ON servicios_turisticos(destino_id);");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servicios_turisticos');
    }
};
