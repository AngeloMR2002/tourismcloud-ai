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
        if (!Schema::hasTable('horarios')) {
            DB::statement("CREATE TABLE horarios (
            id BIGSERIAL PRIMARY KEY,
            establecimiento_id BIGINT,
            atractivo_id BIGINT,
            dia VARCHAR(15) NOT NULL,
            hora_inicio TIME NOT NULL,
            hora_fin TIME NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT fk_horarios_establecimiento FOREIGN KEY (establecimiento_id) REFERENCES establecimientos(id) ON DELETE CASCADE,
            CONSTRAINT fk_horarios_atractivo FOREIGN KEY (atractivo_id) REFERENCES atractivos(id) ON DELETE CASCADE,
            CONSTRAINT chk_horarios_entidad CHECK (num_nonnulls(establecimiento_id,atractivo_id)=1),
            CONSTRAINT chk_horarios_dia CHECK (dia IN ('lunes','martes','miercoles','jueves','viernes','sabado','domingo')),
            CONSTRAINT chk_horarios_horas CHECK (hora_fin > hora_inicio)
        );");
            DB::statement("CREATE INDEX idx_horarios_establecimiento ON horarios(establecimiento_id);");
            DB::statement("CREATE INDEX idx_horarios_atractivo ON horarios(atractivo_id);");
            DB::statement("CREATE INDEX idx_horarios_dia ON horarios(dia);");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horarios');
    }
};
