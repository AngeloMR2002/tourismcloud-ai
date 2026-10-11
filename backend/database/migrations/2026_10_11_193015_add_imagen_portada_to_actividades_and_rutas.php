<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('actividades', function (Blueprint $table): void {
            $table->string('imagen_portada', 500)->nullable();
        });
        Schema::table('rutas', function (Blueprint $table): void {
            $table->string('imagen_portada', 500)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('actividades', function (Blueprint $table): void {
            $table->dropColumn('imagen_portada');
        });
        Schema::table('rutas', function (Blueprint $table): void {
            $table->dropColumn('imagen_portada');
        });
    }
};
