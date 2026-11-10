<?php

namespace App\Models;

use App\Models\Concerns\PerteneceAAgencia;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use PerteneceAAgencia;

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
        'agencia_id',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];
}