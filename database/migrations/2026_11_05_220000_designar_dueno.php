<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // El primer administrador registrado queda como dueño (sin agencia = ve todas)
        $dueno = DB::table('users')->where('rol', 'administrador')->orderBy('id')->first();

        if ($dueno) {
            DB::table('users')->where('id', $dueno->id)->update(['agencia_id' => null]);
        }
    }

    public function down(): void
    {
        //
    }
};