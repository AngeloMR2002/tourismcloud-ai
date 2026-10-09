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
        if (!Schema::hasTable('itinerario_dias')) {
            DB::statement("CREATE TABLE itinerario_dias (
            id BIGSERIAL PRIMARY KEY,
            itinerario_id BIGINT NOT NULL,
            numero_dia INTEGER NOT NULL,
            fecha DATE,
            CONSTRAINT fk_itinerario_dias_itinerario FOREIGN KEY (itinerario_id) REFERENCES itinerarios(id) ON DELETE CASCADE,
            CONSTRAINT chk_itinerario_dias_numero CHECK (numero_dia > 0),
            CONSTRAINT uq_itinerario_dias_numero UNIQUE (itinerario_id,numero_dia)
        );");
            DB::statement("CREATE INDEX idx_itinerario_dias_itinerario ON itinerario_dias(itinerario_id);");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itinerario_dias');
    }
};
