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
        if (!Schema::hasTable('destinos')) {
            DB::statement("CREATE TABLE destinos (
            id BIGSERIAL PRIMARY KEY,
            organizacion_id BIGINT,
            operador_id BIGINT,
            nombre VARCHAR(150) NOT NULL,
            descripcion TEXT,
            pais VARCHAR(100) NOT NULL,
            region VARCHAR(100),
            ciudad VARCHAR(100),
            latitud DECIMAL(10,7),
            longitud DECIMAL(10,7),
            estado VARCHAR(20) NOT NULL DEFAULT 'activo',
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT fk_destinos_organizacion FOREIGN KEY (organizacion_id) REFERENCES organizaciones(id) ON DELETE CASCADE,
            CONSTRAINT fk_destinos_operador FOREIGN KEY (operador_id) REFERENCES usuarios(id) ON DELETE SET NULL,
            CONSTRAINT chk_destinos_estado CHECK (estado IN ('activo','inactivo')),
            CONSTRAINT chk_destinos_latitud CHECK (latitud IS NULL OR latitud BETWEEN -90 AND 90),
            CONSTRAINT chk_destinos_longitud CHECK (longitud IS NULL OR longitud BETWEEN -180 AND 180)
        );");
            DB::statement("CREATE INDEX idx_destinos_organizacion ON destinos(organizacion_id);");
            DB::statement("CREATE INDEX idx_destinos_operador ON destinos(operador_id);");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('destinos');
    }
};
