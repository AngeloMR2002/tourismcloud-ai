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
        if (!Schema::hasTable('establecimientos')) {
            DB::statement("CREATE TABLE establecimientos (
            id BIGSERIAL PRIMARY KEY,
            destino_id BIGINT NOT NULL,
            proveedor_id BIGINT,
            tipo VARCHAR(50) NOT NULL,
            nombre VARCHAR(150) NOT NULL,
            descripcion TEXT,
            direccion VARCHAR(255),
            latitud DECIMAL(10,7),
            longitud DECIMAL(10,7),
            rango_precio VARCHAR(50),
            imagen_portada VARCHAR(500),
            estado VARCHAR(20) NOT NULL DEFAULT 'activo',
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            deleted_at TIMESTAMP NULL,
            CONSTRAINT fk_establecimientos_destino FOREIGN KEY (destino_id) REFERENCES destinos(id) ON DELETE CASCADE,
            CONSTRAINT fk_establecimientos_proveedor FOREIGN KEY (proveedor_id) REFERENCES usuarios(id) ON DELETE SET NULL,
            CONSTRAINT chk_establecimientos_latitud CHECK (latitud IS NULL OR latitud BETWEEN -90 AND 90),
            CONSTRAINT chk_establecimientos_longitud CHECK (longitud IS NULL OR longitud BETWEEN -180 AND 180),
            CONSTRAINT chk_establecimientos_estado CHECK (estado IN ('activo','inactivo'))
        );");
            DB::statement("CREATE INDEX idx_establecimientos_destino ON establecimientos(destino_id);");
            DB::statement("CREATE INDEX idx_establecimientos_proveedor ON establecimientos(proveedor_id);");
            DB::statement("CREATE INDEX idx_establecimientos_estado ON establecimientos(estado);");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('establecimientos');
    }
};
