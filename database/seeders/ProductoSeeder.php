<?php

namespace Database\Seeders;

use App\Models\Producto;
use Illuminate\Database\Seeder;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        $productos = [
            // Procesadores (id_categoria = 1)
            ['codigo'=>'CPU-001','nombre'=>'AMD Ryzen 5 5600','descripcion'=>'6 nucleos, 12 hilos, socket AM4','id_categoria'=>1,'precio_compra'=>1900,'precio_venta'=>2750,'existencia'=>9,'stock_minimo'=>3,'estado'=>true],
            ['codigo'=>'CPU-002','nombre'=>'Intel Core i5-13400F','descripcion'=>'10 nucleos, 16 hilos, LGA1700','id_categoria'=>1,'precio_compra'=>2600,'precio_venta'=>3700,'existencia'=>7,'stock_minimo'=>3,'estado'=>true],
            ['codigo'=>'CPU-003','nombre'=>'AMD Ryzen 7 7800X3D','descripcion'=>'8 nucleos, 16 hilos, AM5, 3D V-Cache','id_categoria'=>1,'precio_compra'=>6500,'precio_venta'=>9200,'existencia'=>4,'stock_minimo'=>2,'estado'=>true],

            // Memoria RAM (id_categoria = 2)
            ['codigo'=>'RAM-001','nombre'=>'Kingston Fury Beast 8GB DDR4 3200MHz','descripcion'=>'Memoria RAM DDR4 con disipador','id_categoria'=>2,'precio_compra'=>550,'precio_venta'=>850,'existencia'=>25,'stock_minimo'=>8,'estado'=>true],
            ['codigo'=>'RAM-002','nombre'=>'Corsair Vengeance RGB 16GB (2x8) DDR4 3600MHz','descripcion'=>'Kit dual channel RGB','id_categoria'=>2,'precio_compra'=>1450,'precio_venta'=>2150,'existencia'=>14,'stock_minimo'=>5,'estado'=>true],
            ['codigo'=>'RAM-003','nombre'=>'TeamGroup T-Force Delta 32GB (2x16) DDR5 6000MHz','descripcion'=>'Kit dual channel DDR5 con RGB','id_categoria'=>2,'precio_compra'=>3200,'precio_venta'=>4600,'existencia'=>7,'stock_minimo'=>3,'estado'=>true],

            // Tarjetas gráficas (id_categoria = 3)
            ['codigo'=>'GPU-001','nombre'=>'NVIDIA RTX 4060 8GB','descripcion'=>'Tarjeta grafica RTX 4060 GDDR6','id_categoria'=>3,'precio_compra'=>6500,'precio_venta'=>8900,'existencia'=>6,'stock_minimo'=>2,'estado'=>true],
            ['codigo'=>'GPU-002','nombre'=>'AMD Radeon RX 7600 8GB','descripcion'=>'Tarjeta grafica RX 7600 GDDR6','id_categoria'=>3,'precio_compra'=>5800,'precio_venta'=>8100,'existencia'=>5,'stock_minimo'=>2,'estado'=>true],
            ['codigo'=>'GPU-003','nombre'=>'NVIDIA RTX 4070 Ti Super 16GB','descripcion'=>'Tarjeta grafica high-end GDDR6X','id_categoria'=>3,'precio_compra'=>16500,'precio_venta'=>22000,'existencia'=>3,'stock_minimo'=>1,'estado'=>true],

            // Almacenamiento (id_categoria = 4)
            ['codigo'=>'ALM-001','nombre'=>'SSD Kingston NV2 1TB NVMe','descripcion'=>'SSD NVMe PCIe 4.0, lectura 3500 MB/s','id_categoria'=>4,'precio_compra'=>1100,'precio_venta'=>1650,'existencia'=>20,'stock_minimo'=>5,'estado'=>true],
            ['codigo'=>'ALM-002','nombre'=>'HDD Seagate Barracuda 2TB','descripcion'=>'Disco duro 7200 RPM, SATA III','id_categoria'=>4,'precio_compra'=>950,'precio_venta'=>1450,'existencia'=>15,'stock_minimo'=>5,'estado'=>true],
            ['codigo'=>'ALM-003','nombre'=>'USB Kingston DataTraveler 64GB','descripcion'=>'USB 3.2, color negro','id_categoria'=>4,'precio_compra'=>90,'precio_venta'=>160,'existencia'=>50,'stock_minimo'=>10,'estado'=>true],

            // Fuentes de poder (id_categoria = 5)
            ['codigo'=>'FUE-001','nombre'=>'Fuente Corsair CV650 650W 80+ Bronze','descripcion'=>'Fuente ATX 650W certificada','id_categoria'=>5,'precio_compra'=>850,'precio_venta'=>1350,'existencia'=>12,'stock_minimo'=>4,'estado'=>true],
            ['codigo'=>'FUE-002','nombre'=>'Fuente EVGA 600 BR 600W','descripcion'=>'Fuente ATX 600W 80+ Bronze','id_categoria'=>5,'precio_compra'=>780,'precio_venta'=>1200,'existencia'=>10,'stock_minimo'=>3,'estado'=>true],
            ['codigo'=>'FUE-003','nombre'=>'Fuente Thermaltake Smart 700W RGB','descripcion'=>'Fuente 700W con iluminacion RGB','id_categoria'=>5,'precio_compra'=>1050,'precio_venta'=>1600,'existencia'=>8,'stock_minimo'=>3,'estado'=>true],

            // Gabinetes (id_categoria = 6)
            ['codigo'=>'GAB-001','nombre'=>'Gabinete NZXT H510 Mid Tower','descripcion'=>'Mid Tower ATX, panel lateral de vidrio','id_categoria'=>6,'precio_compra'=>1400,'precio_venta'=>2100,'existencia'=>9,'stock_minimo'=>3,'estado'=>true],
            ['codigo'=>'GAB-002','nombre'=>'Gabinete Cooler Master MasterBox Q300L','descripcion'=>'Micro ATX, malla frontal','id_categoria'=>6,'precio_compra'=>950,'precio_venta'=>1450,'existencia'=>11,'stock_minimo'=>3,'estado'=>true],
            ['codigo'=>'GAB-003','nombre'=>'Gabinete Lian Li Lancool 216 RGB','descripcion'=>'Mid Tower ATX con 2 ventiladores ARGB','id_categoria'=>6,'precio_compra'=>1800,'precio_venta'=>2700,'existencia'=>6,'stock_minimo'=>2,'estado'=>true],

            // Refrigeración (id_categoria = 7)
            ['codigo'=>'REF-001','nombre'=>'Cooler Master Hyper 212 Black','descripcion'=>'Disipador CPU 120mm, 4 heatpipes','id_categoria'=>7,'precio_compra'=>650,'precio_venta'=>1000,'existencia'=>14,'stock_minimo'=>4,'estado'=>true],
            ['codigo'=>'REF-002','nombre'=>'NZXT Kraken 240 AIO','descripcion'=>'Refrigeracion liquida 240mm RGB','id_categoria'=>7,'precio_compra'=>2200,'precio_venta'=>3200,'existencia'=>8,'stock_minimo'=>3,'estado'=>true],
            ['codigo'=>'REF-003','nombre'=>'Corsair iCUE H150i Elite 360mm','descripcion'=>'Refrigeracion liquida 360mm RGB','id_categoria'=>7,'precio_compra'=>3800,'precio_venta'=>5400,'existencia'=>5,'stock_minimo'=>2,'estado'=>true],

            // Motherboards (id_categoria = 8)
            ['codigo'=>'MB-001','nombre'=>'ASUS Prime B550M-A AM4','descripcion'=>'Micro ATX, socket AM4, DDR4','id_categoria'=>8,'precio_compra'=>1500,'precio_venta'=>2200,'existencia'=>10,'stock_minimo'=>3,'estado'=>true],
            ['codigo'=>'MB-002','nombre'=>'MSI B760M Mortar WiFi LGA1700','descripcion'=>'Micro ATX, socket LGA1700, DDR5','id_categoria'=>8,'precio_compra'=>2600,'precio_venta'=>3700,'existencia'=>8,'stock_minimo'=>3,'estado'=>true],
            ['codigo'=>'MB-003','nombre'=>'Gigabyte X670 Aorus Elite AX AM5','descripcion'=>'ATX, socket AM5, DDR5, WiFi 6E','id_categoria'=>8,'precio_compra'=>5200,'precio_venta'=>7200,'existencia'=>4,'stock_minimo'=>2,'estado'=>true],

            // Periféricos (id_categoria = 9)
            ['codigo'=>'PER-001','nombre'=>'Mouse Logitech G502 Hero','descripcion'=>'Mouse gaming 16000 DPI RGB','id_categoria'=>9,'precio_compra'=>750,'precio_venta'=>1200,'existencia'=>18,'stock_minimo'=>5,'estado'=>true],
            ['codigo'=>'PER-002','nombre'=>'Teclado Redragon Kumara K552 RGB','descripcion'=>'Teclado mecanico TKL switches Red','id_categoria'=>9,'precio_compra'=>850,'precio_venta'=>1300,'existencia'=>15,'stock_minimo'=>4,'estado'=>true],
            ['codigo'=>'PER-003','nombre'=>'Audifonos HyperX Cloud II','descripcion'=>'Headset gaming 7.1 virtual','id_categoria'=>9,'precio_compra'=>1400,'precio_venta'=>2100,'existencia'=>12,'stock_minimo'=>4,'estado'=>true],

            // Monitores (id_categoria = 10)
            ['codigo'=>'MON-001','nombre'=>'Monitor LG 24 pulgadas IPS 75Hz Full HD','descripcion'=>'Monitor 24 pulgadas IPS, HDMI/VGA','id_categoria'=>10,'precio_compra'=>1900,'precio_venta'=>2750,'existencia'=>10,'stock_minimo'=>3,'estado'=>true],
            ['codigo'=>'MON-002','nombre'=>'Monitor Samsung Odyssey G3 27 pulgadas 165Hz','descripcion'=>'Monitor gaming 27 pulgadas Full HD 165Hz 1ms','id_categoria'=>10,'precio_compra'=>3200,'precio_venta'=>4500,'existencia'=>7,'stock_minimo'=>3,'estado'=>true],
            ['codigo'=>'MON-003','nombre'=>'Monitor ASUS TUF VG27AQ 27 pulgadas 2K 165Hz','descripcion'=>'Monitor QHD 165Hz IPS G-Sync compatible','id_categoria'=>10,'precio_compra'=>5500,'precio_venta'=>7600,'existencia'=>5,'stock_minimo'=>2,'estado'=>true],
        ];

        foreach ($productos as $p) {
            Producto::create($p);
        }

        $this->command->info('✅ ' . count($productos) . ' productos insertados.');
    }
}