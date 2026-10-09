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
        if (!Schema::hasTable('organizaciones')) {
            DB::statement("CREATE TABLE organizaciones (
            id BIGSERIAL PRIMARY KEY,
            nombre VARCHAR(150) NOT NULL,
            tipo VARCHAR(50) NOT NULL,
            descripcion TEXT,
            estado VARCHAR(20) NOT NULL DEFAULT 'activo',
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT chk_organizaciones_estado CHECK (estado IN ('activo','inactivo'))
        );");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizaciones');
    }
};
