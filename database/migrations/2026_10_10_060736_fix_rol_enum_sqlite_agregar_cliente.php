<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            // SQLite guarda el enum como una restricción CHECK fija.
            // No se puede "editar" esa restricción directamente, así
            // que creamos una columna nueva sin esa limitación, copiamos
            // los datos, y sustituimos la columna vieja por la nueva.

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

            return;
        }

        DB::statement("ALTER TABLE users MODIFY rol ENUM('administrador','vendedor','caja','almacen','cliente') NOT NULL DEFAULT 'vendedor'");
    }

    public function down(): void
    {
        // No se revierte de forma simple; se deja tal cual.
    }
};