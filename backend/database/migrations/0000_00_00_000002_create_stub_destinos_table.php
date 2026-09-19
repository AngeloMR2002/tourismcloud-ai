<?php

// ⚠️ STUB TEMPORAL — eliminar este archivo y su registro en la tabla
// migrations cuando la tabla real llegue via feature/destinos o feature/auth.
// Ver TODO en walkthrough.md.
// Comando de limpieza ANTES de borrar:
//   php artisan migrate:rollback --step=3

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('destinos', function (Blueprint $table) {
            $table->id();
            // organizacion_id y operador_id sin FK (otras tablas no existen en esta rama).
            $table->unsignedBigInteger('organizacion_id')->nullable();
            $table->unsignedBigInteger('operador_id')->nullable();
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->string('pais', 100)->default('');
            $table->string('region', 100)->nullable();
            $table->string('ciudad', 100)->nullable();
            $table->decimal('latitud', 10, 7)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('destinos');
    }
};
