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
        if (!Schema::hasTable('itinerario_servicios')) {
            DB::statement("CREATE TABLE itinerario_servicios (
            id BIGSERIAL PRIMARY KEY,
            itinerario_id BIGINT NOT NULL,
            servicio_id BIGINT NOT NULL,
            cantidad INTEGER NOT NULL DEFAULT 1,
            costo_estimado DECIMAL(10,2) NOT NULL DEFAULT 0,
            CONSTRAINT fk_itinerario_servicios_itinerario FOREIGN KEY (itinerario_id) REFERENCES itinerarios(id) ON DELETE CASCADE,
            CONSTRAINT fk_itinerario_servicios_servicio FOREIGN KEY (servicio_id) REFERENCES servicios_turisticos(id) ON DELETE CASCADE,
            CONSTRAINT chk_itinerario_servicios_cantidad CHECK (cantidad > 0),
            CONSTRAINT chk_itinerario_servicios_costo CHECK (costo_estimado >= 0)
        );");
            DB::statement("CREATE INDEX idx_itinerario_servicios_itinerario ON itinerario_servicios(itinerario_id);");
            DB::statement("CREATE INDEX idx_itinerario_servicios_servicio ON itinerario_servicios(servicio_id);");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itinerario_servicios');
    }
};
