<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Categoria;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $categorias = [
            ['nombre' => 'Procesadores', 'descripcion' => 'CPUs Intel y AMD', 'estado' => 1],
            ['nombre' => 'Memoria RAM', 'descripcion' => 'Módulos DDR4 y DDR5', 'estado' => 1],
            ['nombre' => 'Tarjetas gráficas', 'descripcion' => 'GPUs NVIDIA y AMD', 'estado' => 1],
            ['nombre' => 'Almacenamiento', 'descripcion' => 'SSD y HDD', 'estado' => 1],
            ['nombre' => 'Fuentes de poder', 'descripcion' => 'PSUs de distintas potencias', 'estado' => 1],
            ['nombre' => 'Gabinetes', 'descripcion' => 'Chasis ATX, mATX, ITX', 'estado' => 1],
            ['nombre' => 'Refrigeración', 'descripcion' => 'AIO y aire', 'estado' => 1],
            ['nombre' => 'Motherboards', 'descripcion' => 'Placas madre Intel y AMD', 'estado' => 1],
            ['nombre' => 'Periféricos', 'descripcion' => 'Teclados, mouse, audífonos', 'estado' => 1],
            ['nombre' => 'Monitores', 'descripcion' => 'Pantallas gaming y profesionales', 'estado' => 1],
        ];

        foreach ($categorias as $cat) {
            Categoria::create($cat);
        }
    }
}

