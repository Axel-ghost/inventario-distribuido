<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            // SQLite no soporta modificar un enum directamente;
            // como el campo ya se guarda como texto por debajo,
            // no hace falta tocar nada aquí.
            return;
        }

        DB::statement("ALTER TABLE users MODIFY rol ENUM('administrador','vendedor','caja','almacen','cliente') NOT NULL DEFAULT 'vendedor'");
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE users MODIFY rol ENUM('administrador','vendedor','caja','almacen') NOT NULL DEFAULT 'vendedor'");
    }
};