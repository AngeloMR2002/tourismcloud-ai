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
        if (!Schema::hasTable('establecimiento_categoria')) {
            DB::statement("CREATE TABLE establecimiento_categoria (
            establecimiento_id BIGINT NOT NULL,
            categoria_id BIGINT NOT NULL,
            PRIMARY KEY (establecimiento_id,categoria_id),
            CONSTRAINT fk_establecimiento_categoria_establecimiento FOREIGN KEY (establecimiento_id) REFERENCES establecimientos(id) ON DELETE CASCADE,
            CONSTRAINT fk_establecimiento_categoria_categoria FOREIGN KEY (categoria_id) REFERENCES categorias_interes(id) ON DELETE CASCADE
        );");
            DB::statement("CREATE INDEX idx_establecimiento_categoria_categoria ON establecimiento_categoria(categoria_id);");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('establecimiento_categoria');
    }
};
