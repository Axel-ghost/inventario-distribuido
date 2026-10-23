<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Si estamos en SQLite, hacemos la conversión de columna
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('users', function (Blueprint $table) {
                $table->string('rol_temp')->nullable()->after('rol');
            });

            DB::statement('UPDATE users SET rol_temp = rol');

            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('rol');
            });

            Schema::table('users', function (Blueprint $table) {
                $table->renameColumn('rol_temp', 'rol');
            });

            DB::statement("UPDATE users SET rol = 'vendedor' WHERE rol IS NULL");
        }
        
        // Si estamos en PostgreSQL o MySQL, no hacemos nada aquí.
        // La columna 'rol' ya se creó como string en la migración base.
    }

    public function down(): void
    {
        // No se revierte de forma simple.
    }
};