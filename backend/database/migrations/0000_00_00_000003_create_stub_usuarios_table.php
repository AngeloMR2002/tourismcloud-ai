<?php

// ⚠️ STUB TEMPORAL — eliminar este archivo y su registro en la tabla
// migrations cuando la tabla real llegue via feature/destinos o feature/auth.
// Ver TODO en walkthrough.md.
// Comando de limpieza ANTES de borrar:
//   php artisan migrate:rollback --step=3
//
// NOTA: La tabla real del schema SQL se llama 'usuarios', no 'users'.
// La tabla 'users' de Laravel seguirá existiendo para el sistema de auth
// propio de Laravel; esta tabla stub imita la tabla de dominio del negocio.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            // organizacion_id sin FK (tabla no existe en esta rama).
            $table->unsignedBigInteger('organizacion_id')->nullable();
            $table->string('nombre', 150);
            $table->string('email', 150)->unique();
            $table->string('password', 255);
            $table->string('rol', 30)->default('turista');
            $table->string('telefono', 20)->nullable();
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
