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
        if (!Schema::hasTable('solicitudes_ia')) {
            DB::statement("CREATE TABLE solicitudes_ia (
            id BIGSERIAL PRIMARY KEY,
            usuario_id BIGINT NOT NULL,
            itinerario_id BIGINT,
            solicitud TEXT NOT NULL,
            respuesta TEXT,
            proveedor VARCHAR(50),
            modelo VARCHAR(100),
            tokens_entrada INTEGER,
            tokens_salida INTEGER,
            tiempo_respuesta_ms INTEGER,
            estado VARCHAR(30) NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT fk_solicitudes_ia_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            CONSTRAINT fk_solicitudes_ia_itinerario FOREIGN KEY (itinerario_id) REFERENCES itinerarios(id) ON DELETE SET NULL,
            CONSTRAINT chk_solicitudes_ia_estado CHECK (estado IN ('pendiente','procesando','completada','error')),
            CONSTRAINT chk_solicitudes_ia_tokens_entrada CHECK (tokens_entrada IS NULL OR tokens_entrada >= 0),
            CONSTRAINT chk_solicitudes_ia_tokens_salida CHECK (tokens_salida IS NULL OR tokens_salida >= 0),
            CONSTRAINT chk_solicitudes_ia_tiempo CHECK (tiempo_respuesta_ms IS NULL OR tiempo_respuesta_ms >= 0)
        );");
            DB::statement("CREATE INDEX idx_solicitudes_ia_usuario ON solicitudes_ia(usuario_id);");
            DB::statement("CREATE INDEX idx_solicitudes_ia_itinerario ON solicitudes_ia(itinerario_id);");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitudes_ia');
    }
};
