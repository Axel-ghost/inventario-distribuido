<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            if (Schema::hasIndex('productos', 'productos_codigo_unique')) {
                $table->dropUnique('productos_codigo_unique');
            }

            $table->unique(['agencia_id', 'codigo']);
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropUnique(['agencia_id', 'codigo']);
            $table->unique('codigo');
        });
    }
};