<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            // foreignId -> hace el BIGINT y la FK automáticamente
            $table->foreignId('organizacion_id')->nullable()->constrained('organizaciones')->nullOnDelete();
            
            $table->string('nombre', 100);
            $table->string('apellido', 100);
            $table->string('email', 150)->unique();
            $table->string('google_id')->nullable();
            $table->timestamp('email_verified_at')->nullable(); // Campo útil de Laravel
            $table->string('password', 255)->nullable();
            $table->string('rol', 30);
            $table->string('estado', 20)->default('activo');
            
            $table->rememberToken(); // Campo requerido por Laravel Auth
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // En PostgreSQL los CHECK constraints se agregan después de crear la tabla a veces, 
        // pero Laravel permite hacerlo usando raw statements o simplemente confiando en 
        // la validación a nivel de aplicación (Request). Para mantener tu diseño estricto:
        DB::statement("ALTER TABLE usuarios ADD CONSTRAINT chk_usuarios_rol CHECK (rol IN ('administrador','operador_turistico','proveedor','turista'))");
        DB::statement("ALTER TABLE usuarios ADD CONSTRAINT chk_usuarios_estado CHECK (estado IN ('activo','inactivo'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('usuarios');
    }
};