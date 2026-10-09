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
        if (!Schema::hasTable('atractivo_categoria')) {
            DB::statement("CREATE TABLE atractivo_categoria (
            atractivo_id BIGINT NOT NULL,
            categoria_id BIGINT NOT NULL,
            PRIMARY KEY (atractivo_id,categoria_id),
            CONSTRAINT fk_atractivo_categoria_atractivo FOREIGN KEY (atractivo_id) REFERENCES atractivos(id) ON DELETE CASCADE,
            CONSTRAINT fk_atractivo_categoria_categoria FOREIGN KEY (categoria_id) REFERENCES categorias_interes(id) ON DELETE CASCADE
        );");
            DB::statement("CREATE INDEX idx_atractivo_categoria_categoria ON atractivo_categoria(categoria_id);");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atractivo_categoria');
    }
};
