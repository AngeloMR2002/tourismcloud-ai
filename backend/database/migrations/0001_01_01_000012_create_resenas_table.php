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
        if (!Schema::hasTable('resenas')) {
            DB::statement("CREATE TABLE resenas (
            id BIGSERIAL PRIMARY KEY,
            usuario_id BIGINT NOT NULL,
            entidad_tipo VARCHAR(30) NOT NULL,
            entidad_id BIGINT NOT NULL,
            calificacion INTEGER NOT NULL,
            comentario TEXT,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT fk_resenas_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            CONSTRAINT chk_resenas_entidad_tipo CHECK (entidad_tipo IN ('destino','atractivo','establecimiento')),
            CONSTRAINT chk_resenas_calificacion CHECK (calificacion BETWEEN 1 AND 5)
        );");
            DB::statement("CREATE INDEX idx_resenas_usuario ON resenas(usuario_id);");
            DB::statement("CREATE INDEX idx_resenas_entidad ON resenas(entidad_tipo,entidad_id);");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resenas');
    }
};
