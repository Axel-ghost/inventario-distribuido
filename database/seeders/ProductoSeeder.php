<?php

namespace Database\Seeders;

use App\Models\Producto;
use Illuminate\Database\Seeder;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        Producto::create([
            'codigo' => 'P001',
            'nombre' => 'Teclado inalámbrico',
            'categoria' => 'Periféricos',
            'precio_compra' => 250,
            'precio_venta' => 350,
            'existencia' => 7,
            'stock_minimo' => 10,
        ]);

        Producto::create([
            'codigo' => 'P002',
            'nombre' => 'Mouse óptico',
            'categoria' => 'Periféricos',
            'precio_compra' => 120,
            'precio_venta' => 180,
            'existencia' => 0,
            'stock_minimo' => 10,
        ]);

        Producto::create([
            'codigo' => 'P003',
            'nombre' => 'Monitor 24"',
            'categoria' => 'Monitores',
            'precio_compra' => 2100,
            'precio_venta' => 2800,
            'existencia' => 15,
            'stock_minimo' => 5,
        ]);
    }
}
