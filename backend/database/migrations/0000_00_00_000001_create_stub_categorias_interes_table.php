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
        Schema::create('categorias_interes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->unique()->notNull();
            // Sin timestamps — coincide con el schema SQL real.
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categorias_interes');
    }
};
