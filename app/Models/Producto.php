<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $fillable = [
        'codigo',
        'nombre',
        'imagen_url',
        'descripcion',
        'categoria',
        'precio_compra',
        'precio_venta',
        'existencia',
        'stock_minimo',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];
}