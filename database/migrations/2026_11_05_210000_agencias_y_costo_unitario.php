<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agencias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('ciudad')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // Los datos que ya existen pasan a una agencia por defecto
        $agenciaId = DB::table('agencias')->insertGetId([
            'nombre' => 'Agencia Principal',
            'activo' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach (['users', 'productos', 'ventas', 'pedidos'] as $tabla) {
            Schema::table($tabla, function (Blueprint $table) {
                $table->foreignId('agencia_id')->nullable()->constrained('agencias');
            });

            DB::table($tabla)->update(['agencia_id' => $agenciaId]);
        }

        // Costo del producto al momento de la venta (para calcular ganancia real)
        Schema::table('detalle_ventas', function (Blueprint $table) {
            $table->decimal('costo_unitario', 10, 2)->default(0);
        });

        DB::statement('UPDATE detalle_ventas SET costo_unitario = COALESCE((SELECT precio_compra FROM productos WHERE productos.id = detalle_ventas.producto_id), 0)');
    }

    public function down(): void
    {
        Schema::table('detalle_ventas', function (Blueprint $table) {
            $table->dropColumn('costo_unitario');
        });

        foreach (['pedidos', 'ventas', 'productos', 'users'] as $tabla) {
            Schema::table($tabla, function (Blueprint $table) {
                $table->dropConstrainedForeignId('agencia_id');
            });
        }

        Schema::dropIfExists('agencias');
    }
};