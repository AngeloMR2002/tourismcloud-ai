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
        if (!Schema::hasTable('preferencias_turista')) {
            DB::statement("CREATE TABLE preferencias_turista (
            id BIGSERIAL PRIMARY KEY,
            turista_id BIGINT NOT NULL UNIQUE,
            presupuesto_max DECIMAL(10,2),
            dias_disponibles INTEGER,
            hora_inicio_preferida TIME,
            hora_fin_preferida TIME,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT fk_preferencias_turista FOREIGN KEY (turista_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            CONSTRAINT chk_preferencias_presupuesto CHECK (presupuesto_max IS NULL OR presupuesto_max >= 0),
            CONSTRAINT chk_preferencias_dias CHECK (dias_disponibles IS NULL OR dias_disponibles > 0),
            CONSTRAINT chk_preferencias_horas CHECK (hora_inicio_preferida IS NULL OR hora_fin_preferida IS NULL OR hora_fin_preferida > hora_inicio_preferida)
        );");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preferencias_turista');
    }
};
