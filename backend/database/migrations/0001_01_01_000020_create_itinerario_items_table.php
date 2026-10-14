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
        if (!Schema::hasTable('itinerario_items')) {
            DB::statement("CREATE TABLE itinerario_items (
            id BIGSERIAL PRIMARY KEY,
            itinerario_dia_id BIGINT NOT NULL,
            entidad_tipo VARCHAR(30) NOT NULL,
            entidad_id BIGINT NOT NULL,
            hora_inicio TIME NOT NULL,
            hora_fin TIME NOT NULL,
            orden INTEGER NOT NULL,
            costo_estimado DECIMAL(10,2) NOT NULL DEFAULT 0,
            CONSTRAINT fk_itinerario_items_dia FOREIGN KEY (itinerario_dia_id) REFERENCES itinerario_dias(id) ON DELETE CASCADE,
            CONSTRAINT chk_itinerario_items_entidad_tipo CHECK (entidad_tipo IN ('atractivo','establecimiento','actividad')),
            CONSTRAINT chk_itinerario_items_horas CHECK (hora_fin > hora_inicio),
            CONSTRAINT chk_itinerario_items_orden CHECK (orden > 0),
            CONSTRAINT chk_itinerario_items_costo CHECK (costo_estimado >= 0)
        );");
            DB::statement("CREATE INDEX idx_itinerario_items_entidad ON itinerario_items(entidad_tipo,entidad_id);");
            DB::statement("CREATE INDEX idx_itinerario_items_dia ON itinerario_items(itinerario_dia_id);");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itinerario_items');
    }
};
