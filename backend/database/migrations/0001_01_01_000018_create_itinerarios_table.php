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
        if (!Schema::hasTable('itinerarios')) {
            DB::statement("CREATE TABLE itinerarios (
            id BIGSERIAL PRIMARY KEY,
            turista_id BIGINT NOT NULL,
            destino_id BIGINT NOT NULL,
            preferencia_id BIGINT,
            nombre VARCHAR(150) NOT NULL,
            descripcion TEXT,
            presupuesto_total DECIMAL(10,2),
            dias INTEGER NOT NULL,
            estado VARCHAR(20) NOT NULL DEFAULT 'generado',
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT fk_itinerarios_turista FOREIGN KEY (turista_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            CONSTRAINT fk_itinerarios_destino FOREIGN KEY (destino_id) REFERENCES destinos(id) ON DELETE CASCADE,
            CONSTRAINT fk_itinerarios_preferencia FOREIGN KEY (preferencia_id) REFERENCES preferencias_turista(id) ON DELETE SET NULL,
            CONSTRAINT chk_itinerarios_presupuesto CHECK (presupuesto_total IS NULL OR presupuesto_total >= 0),
            CONSTRAINT chk_itinerarios_dias CHECK (dias > 0),
            CONSTRAINT chk_itinerarios_estado CHECK (estado IN ('generado','guardado','activo','finalizado','cancelado'))
        );");
            DB::statement("CREATE INDEX idx_itinerarios_turista ON itinerarios(turista_id);");
            DB::statement("CREATE INDEX idx_itinerarios_destino ON itinerarios(destino_id);");
            DB::statement("CREATE INDEX idx_itinerarios_preferencia ON itinerarios(preferencia_id);");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itinerarios');
    }
};
