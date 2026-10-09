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
        if (!Schema::hasTable('usuarios')) {
            DB::statement("CREATE TABLE usuarios (
            id BIGSERIAL PRIMARY KEY,
            organizacion_id BIGINT,
            nombre VARCHAR(100) NOT NULL,
            apellido VARCHAR(100) NOT NULL,
            email VARCHAR(150) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            rol VARCHAR(30) NOT NULL,
            estado VARCHAR(20) NOT NULL DEFAULT 'activo',
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT fk_usuarios_organizacion FOREIGN KEY (organizacion_id) REFERENCES organizaciones(id) ON DELETE SET NULL,
            CONSTRAINT chk_usuarios_rol CHECK (rol IN ('administrador','operador_turistico','proveedor','turista')),
            CONSTRAINT chk_usuarios_estado CHECK (estado IN ('activo','inactivo'))
        );");
            DB::statement("CREATE INDEX idx_usuarios_organizacion ON usuarios(organizacion_id);");
            DB::statement("CREATE INDEX idx_usuarios_rol ON usuarios(rol);");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
