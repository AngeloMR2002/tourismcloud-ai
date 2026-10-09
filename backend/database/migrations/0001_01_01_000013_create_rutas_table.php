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
        if (!Schema::hasTable('rutas')) {
            DB::statement("CREATE TABLE rutas (
            id BIGSERIAL PRIMARY KEY,
            destino_id BIGINT NOT NULL,
            nombre VARCHAR(150) NOT NULL,
            descripcion TEXT,
            distancia_km DECIMAL(10,2),
            duracion_estimada_min INTEGER,
            estado VARCHAR(20) NOT NULL DEFAULT 'activo',
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT fk_rutas_destino FOREIGN KEY (destino_id) REFERENCES destinos(id) ON DELETE CASCADE,
            CONSTRAINT chk_rutas_distancia CHECK (distancia_km IS NULL OR distancia_km >= 0),
            CONSTRAINT chk_rutas_duracion CHECK (duracion_estimada_min IS NULL OR duracion_estimada_min > 0),
            CONSTRAINT chk_rutas_estado CHECK (estado IN ('activo','inactivo'))
        );");
            DB::statement("CREATE INDEX idx_rutas_destino ON rutas(destino_id);");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rutas');
    }
};
