<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categorias_interes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 100)->unique();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categorias_interes');
    }
};
