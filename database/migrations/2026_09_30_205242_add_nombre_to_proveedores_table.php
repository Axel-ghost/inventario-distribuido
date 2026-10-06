<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proveedores', function (Blueprint $table) {
            $table->string('nombre', 150)->after('id_proveedor');
        });
    }

    public function down(): void
    {
        Schema::table('proveedor', function (Blueprint $table) {
            $table->dropColumn('nombre');
        });
    }
};