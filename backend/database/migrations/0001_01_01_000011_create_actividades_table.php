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
        if (!Schema::hasTable('actividades')) {
            DB::statement("CREATE TABLE actividades (
            id BIGSERIAL PRIMARY KEY,
            destino_id BIGINT NOT NULL,
            nombre VARCHAR(150) NOT NULL,
            descripcion TEXT,
            costo DECIMAL(10,2) NOT NULL DEFAULT 0,
            duracion_estimada_min INTEGER,
            latitud DECIMAL(10,7),
            longitud DECIMAL(10,7),
            estado VARCHAR(20) NOT NULL DEFAULT 'activo',
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            deleted_at TIMESTAMP NULL,
            CONSTRAINT fk_actividades_destino FOREIGN KEY (destino_id) REFERENCES destinos(id) ON DELETE CASCADE,
            CONSTRAINT chk_actividades_costo CHECK (costo >= 0),
            CONSTRAINT chk_actividades_duracion CHECK (duracion_estimada_min IS NULL OR duracion_estimada_min > 0),
            CONSTRAINT chk_actividades_latitud CHECK (latitud IS NULL OR latitud BETWEEN -90 AND 90),
            CONSTRAINT chk_actividades_longitud CHECK (longitud IS NULL OR longitud BETWEEN -180 AND 180),
            CONSTRAINT chk_actividades_estado CHECK (estado IN ('activo','inactivo'))
        );");
            DB::statement("CREATE INDEX idx_actividades_destino ON actividades(destino_id);");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actividades');
    }
};
