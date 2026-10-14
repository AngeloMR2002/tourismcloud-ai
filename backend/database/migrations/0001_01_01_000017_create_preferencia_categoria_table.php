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
        if (!Schema::hasTable('preferencia_categoria')) {
            DB::statement("CREATE TABLE preferencia_categoria (
            preferencia_id BIGINT NOT NULL,
            categoria_id BIGINT NOT NULL,
            PRIMARY KEY (preferencia_id,categoria_id),
            CONSTRAINT fk_preferencia_categoria_preferencia FOREIGN KEY (preferencia_id) REFERENCES preferencias_turista(id) ON DELETE CASCADE,
            CONSTRAINT fk_preferencia_categoria_categoria FOREIGN KEY (categoria_id) REFERENCES categorias_interes(id) ON DELETE CASCADE
        );");
            DB::statement("CREATE INDEX idx_preferencia_categoria_categoria ON preferencia_categoria(categoria_id);");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preferencia_categoria');
    }
};
